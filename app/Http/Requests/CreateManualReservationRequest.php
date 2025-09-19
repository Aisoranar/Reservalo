<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property;
use App\Models\GlobalPricing;
use App\Models\User;
use Carbon\Carbon;

class CreateManualReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('create_reservations');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'client_type' => 'required|in:registered,guest',
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'guests' => 'required|integer|min:1|max:20',
            'pricing_method' => 'required|in:global,manual',
            'total_price' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
            'send_email' => 'nullable|boolean'
        ];

        // Validación para precio global
        if ($this->pricing_method === 'global') {
            $rules['global_pricing_id'] = 'required|exists:global_pricings,id';
        }

        // Validaciones específicas según el tipo de cliente
        if ($this->client_type === 'registered') {
            $rules['user_id'] = 'required|exists:users,id';
        } else {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255|unique:users,email';
            $rules['guest_phone'] = 'nullable|string|max:20';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_type.required' => 'Debe seleccionar un tipo de cliente.',
            'client_type.in' => 'El tipo de cliente seleccionado no es válido.',
            'property_id.required' => 'Debe seleccionar una propiedad.',
            'property_id.exists' => 'La propiedad seleccionada no existe.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
            'guests.required' => 'El número de huéspedes es obligatorio.',
            'guests.integer' => 'El número de huéspedes debe ser un número entero.',
            'guests.min' => 'Debe haber al menos 1 huésped.',
            'guests.max' => 'No puede haber más de 20 huéspedes.',
            'pricing_method.required' => 'Debe seleccionar un método de precio.',
            'pricing_method.in' => 'El método de precio seleccionado no es válido.',
            'total_price.required' => 'El precio total es obligatorio.',
            'total_price.numeric' => 'El precio total debe ser un número.',
            'total_price.min' => 'El precio total no puede ser negativo.',
            'status.required' => 'Debe seleccionar un estado para la reserva.',
            'status.in' => 'El estado seleccionado no es válido.',
            'global_pricing_id.required' => 'Debe seleccionar un precio global.',
            'global_pricing_id.exists' => 'El precio global seleccionado no existe.',
            'user_id.required' => 'Debe seleccionar un cliente registrado.',
            'user_id.exists' => 'El cliente seleccionado no existe.',
            'guest_name.required' => 'El nombre del huésped es obligatorio.',
            'guest_name.max' => 'El nombre del huésped no puede exceder 255 caracteres.',
            'guest_email.required' => 'El email del huésped es obligatorio.',
            'guest_email.email' => 'El email del huésped debe ser válido.',
            'guest_email.unique' => 'Ya existe un usuario con este email.',
            'guest_phone.max' => 'El teléfono no puede exceder 20 caracteres.',
            'special_requests.max' => 'Las solicitudes especiales no pueden exceder 1000 caracteres.',
            'admin_notes.max' => 'Las notas del administrador no pueden exceder 1000 caracteres.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validatePropertyAvailability($validator);
            $this->validateGlobalPricing($validator);
            $this->validateDateRange($validator);
        });
    }

    /**
     * Validar disponibilidad de la propiedad
     */
    private function validatePropertyAvailability($validator): void
    {
        if (!$this->has('property_id') || !$this->has('start_date') || !$this->has('end_date')) {
            return;
        }

        $property = Property::find($this->property_id);
        if (!$property) {
            return;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        // Verificar si hay reservas conflictivas
        $conflictingReservations = \App\Models\Reservation::where('property_id', $this->property_id)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'deleted')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        if ($conflictingReservations) {
            $validator->errors()->add('property_id', 'La propiedad no está disponible para las fechas seleccionadas.');
        }
    }

    /**
     * Validar precio global
     */
    private function validateGlobalPricing($validator): void
    {
        if ($this->pricing_method !== 'global' || !$this->has('global_pricing_id')) {
            return;
        }

        $globalPricing = GlobalPricing::find($this->global_pricing_id);
        if (!$globalPricing) {
            $validator->errors()->add('global_pricing_id', 'El precio global seleccionado no existe.');
            return;
        }

        if (!$globalPricing->is_active) {
            $validator->errors()->add('global_pricing_id', 'El precio global seleccionado está inactivo.');
        }
    }

    /**
     * Validar rango de fechas
     */
    private function validateDateRange($validator): void
    {
        if (!$this->has('start_date') || !$this->has('end_date')) {
            return;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        // Validar que no sea más de 1 año en el futuro
        if ($startDate->gt(now()->addYear())) {
            $validator->errors()->add('start_date', 'La fecha de inicio no puede ser más de 1 año en el futuro.');
        }

        // Validar que no sea más de 30 días de duración
        if ($startDate->diffInDays($endDate) > 30) {
            $validator->errors()->add('end_date', 'La reserva no puede durar más de 30 días.');
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'client_type' => 'tipo de cliente',
            'property_id' => 'propiedad',
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'guests' => 'huéspedes',
            'pricing_method' => 'método de precio',
            'total_price' => 'precio total',
            'special_requests' => 'solicitudes especiales',
            'admin_notes' => 'notas del administrador',
            'status' => 'estado',
            'send_email' => 'enviar correo',
            'global_pricing_id' => 'precio global',
            'user_id' => 'cliente',
            'guest_name' => 'nombre del huésped',
            'guest_email' => 'email del huésped',
            'guest_phone' => 'teléfono del huésped',
        ];
    }
}
