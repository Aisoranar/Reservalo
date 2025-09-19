<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\AvailabilityBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusChanged;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reservations = Auth::user()->reservations()
            ->with(['property.primaryImage'])
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $properties = Property::where('is_active', true)
            ->with('primaryImage')
            ->orderBy('name')
            ->get();

        // Cargar datos de reserva pendiente desde localStorage si existen
        $pendingReservation = null;
        if ($request->has('reservation_data')) {
            try {
                $pendingReservation = json_decode($request->reservation_data, true);
            } catch (\Exception $e) {
                // Ignorar error de JSON inválido
            }
        }

        return view('reservations.create', compact('properties', 'pendingReservation'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'guests' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $property = Property::findOrFail($request->property_id);

        // Verificar disponibilidad
        if (!$property->isAvailableForDates($request->start_date, $request->end_date)) {
            return back()->withErrors(['dates' => 'Las fechas seleccionadas no están disponibles'])->withInput();
        }

        // Calcular precio total
        $totalPrice = $property->calculatePriceForDates($request->start_date, $request->end_date);

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'guests' => $request->guests,
            'total_price' => $totalPrice,
            'special_requests' => $request->special_requests,
            'status' => 'pending'
        ]);

        // Notificar al administrador (aquí se implementaría la notificación por WhatsApp)
        $this->notifyAdmin($reservation);

        // Si es una solicitud AJAX, devolver JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Solicitud de reserva enviada exitosamente. Te notificaremos cuando sea aprobada.',
                'reservation' => $reservation
            ]);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Solicitud de reserva enviada exitosamente. Te notificaremos cuando sea aprobada.');
    }

    public function show(Reservation $reservation)
    {
        if (Auth::user()->isAdmin() || $reservation->user_id === Auth::id()) {
            $reservation->load(['property.images', 'user']);
            return view('reservations.show', compact('reservation'));
        }

        abort(403);
    }

    public function cancel(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$reservation->canBeCancelled()) {
            return back()->withErrors(['cancel' => 'No se puede cancelar esta reserva']);
        }

        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('reservations.index')
            ->with('success', 'Reserva cancelada exitosamente');
    }

    // Métodos del administrador
    public function adminIndex(Request $request)
    {
        $query = Reservation::with(['user', 'property.primaryImage']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $reservations = $query->latest()->paginate(20);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function approve(Reservation $reservation)
    {
        $reservation->update(['status' => 'approved']);

        // Notificar al usuario
        $this->notifyUser($reservation, 'approved');

        return back()->with('success', 'Reserva aprobada exitosamente');
    }

    public function reject(Request $request, Reservation $reservation)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $reservation->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Notificar al usuario
        $this->notifyUser($reservation, 'rejected');

        return back()->with('success', 'Reserva rechazada exitosamente');
    }

    public function blockDates(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'type' => 'required|in:maintenance,event,manual'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        AvailabilityBlock::create([
            'property_id' => $property->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'type' => $request->type
        ]);

        return back()->with('success', 'Fechas bloqueadas exitosamente');
    }

    private function notifyAdmin($reservation)
    {
        // Aquí se implementaría la notificación por WhatsApp al administrador
        // Por ahora solo log
        \Log::info("Nueva solicitud de reserva: {$reservation->id}");
    }

    private function notifyUser($reservation, $status)
    {
        try {
            Mail::to($reservation->user->email)
                ->send(new ReservationStatusChanged($reservation, $status));
        } catch (\Exception $e) {
            \Log::error("Error enviando email: " . $e->getMessage());
        }

        // Aquí se implementaría la notificación por WhatsApp al usuario
    }
}
