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
        $reservations = Reservation::with(['user', 'property'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Reservation::count(),
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved' => Reservation::where('status', 'approved')->count(),
            'rejected' => Reservation::where('status', 'rejected')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
        ];

        return view('admin.reservations.index', compact('reservations', 'stats'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'property', 'property.images']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function approve(Reservation $reservation)
    {
        $reservation->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'admin_notes' => request('admin_notes')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva aprobada exitosamente'
        ]);
    }

    public function reject(Reservation $reservation)
    {
        $reservation->update([
            'status' => 'rejected',
            'admin_notes' => request('admin_notes')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva rechazada exitosamente'
        ]);
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
