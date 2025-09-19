<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['user', 'property.primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Optimizar estadísticas con una sola consulta
        $stats = DB::table('reservations')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
            ')
            ->first();

        return view('admin.reservations.index', compact('reservations', 'stats'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'property', 'property.images']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function approve(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'admin_notes' => request('admin_notes')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva aprobada exitosamente',
                'reservation' => [
                    'id' => $reservation->id,
                    'status' => $reservation->status,
                    'status_badge' => '<span class="badge bg-success">Aprobada</span>'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => 'rejected',
                'admin_notes' => request('admin_notes')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva rechazada exitosamente',
                'reservation' => [
                    'id' => $reservation->id,
                    'status' => $reservation->status,
                    'status_badge' => '<span class="badge bg-danger">Rechazada</span>'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->update([
            'status' => 'cancelled',
            'admin_notes' => request('admin_notes')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva cancelada exitosamente'
        ]);
    }

    public function updatePayment(Reservation $reservation)
    {
        $reservation->update([
            'payment_status' => request('payment_status'),
            'amount_paid' => request('amount_paid', 0),
            'paid_at' => request('payment_status') === 'paid' ? now() : null,
            'admin_notes' => request('admin_notes')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de pago actualizado exitosamente'
        ]);
    }

    public function getAvailability(Property $property)
    {
        $reservations = Reservation::where('property_id', $property->id)
            ->whereIn('status', ['approved', 'pending'])
            ->get(['start_date', 'end_date', 'status']);

        $availability = [];
        
        foreach ($reservations as $reservation) {
            $start = $reservation->start_date;
            $end = $reservation->end_date;
            
            $current = $start;
            while ($current < $end) {
                $availability[] = [
                    'date' => $current->format('Y-m-d'),
                    'status' => $reservation->status,
                    'class' => $reservation->status === 'approved' ? 'bg-danger' : 'bg-warning'
                ];
                $current = $current->addDay();
            }
        }

        return response()->json([
            'success' => true,
            'data' => $availability
        ]);
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $reservationIds = $request->input('reservation_ids', []);

        if (empty($reservationIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No se seleccionaron reservas'
            ]);
        }

        $reservations = Reservation::whereIn('id', $reservationIds)->get();

        foreach ($reservations as $reservation) {
            switch ($action) {
                case 'approve':
                    $reservation->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => Auth::id()
                    ]);
                    break;
                case 'reject':
                    $reservation->update([
                        'status' => 'rejected'
                    ]);
                    break;
                case 'cancel':
                    $reservation->update([
                        'status' => 'cancelled'
                    ]);
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Acción aplicada a ' . count($reservations) . ' reservas'
        ]);
    }
}
