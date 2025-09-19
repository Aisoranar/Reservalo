<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationNotification;

class EmailTemplateController extends Controller
{
    /**
     * Listar plantillas de correo
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('display_name', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $templates = $query->orderBy('type')->orderBy('display_name')->paginate(15);
        
        return view('superadmin.email-templates.index', compact('templates'));
    }

    /**
     * Mostrar formulario para crear plantilla
     */
    public function create()
    {
        $types = [
            'reservation' => 'Reservas',
            'notification' => 'Notificaciones',
            'system' => 'Sistema',
            'marketing' => 'Marketing'
        ];

        return view('superadmin.email-templates.create', compact('types'));
    }

    /**
     * Guardar nueva plantilla
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name',
            'display_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'body_text' => 'nullable|string',
            'type' => 'required|string|in:reservation,notification,system,marketing',
            'description' => 'nullable|string',
            'variables' => 'nullable|array'
        ]);

        $template = EmailTemplate::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'subject' => $request->subject,
            'body' => $request->body,
            'body_text' => $request->body_text,
            'type' => $request->type,
            'description' => $request->description,
            'variables' => $request->variables ?? []
        ]);

        return redirect()->route('superadmin.email-templates.index')
            ->with('success', 'Plantilla creada correctamente');
    }

    /**
     * Mostrar plantilla específica
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return view('superadmin.email-templates.show', compact('emailTemplate'));
    }

    /**
     * Mostrar formulario para editar plantilla
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        $types = [
            'reservation' => 'Reservas',
            'notification' => 'Notificaciones',
            'system' => 'Sistema',
            'marketing' => 'Marketing'
        ];

        return view('superadmin.email-templates.edit', compact('emailTemplate', 'types'));
    }

    /**
     * Actualizar plantilla
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'display_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'body_text' => 'nullable|string',
            'type' => 'required|string|in:reservation,notification,system,marketing',
            'description' => 'nullable|string',
            'variables' => 'nullable|array'
        ]);

        $emailTemplate->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'subject' => $request->subject,
            'body' => $request->body,
            'body_text' => $request->body_text,
            'type' => $request->type,
            'description' => $request->description,
            'variables' => $request->variables ?? []
        ]);

        return redirect()->route('superadmin.email-templates.index')
            ->with('success', 'Plantilla actualizada correctamente');
    }

    /**
     * Eliminar plantilla
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('superadmin.email-templates.index')
            ->with('success', 'Plantilla eliminada correctamente');
    }

    /**
     * Activar/desactivar plantilla
     */
    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update([
            'is_active' => !$emailTemplate->is_active
        ]);

        $status = $emailTemplate->is_active ? 'activada' : 'desactivada';

        return response()->json([
            'success' => true,
            'message' => "Plantilla {$status} correctamente"
        ]);
    }

    /**
     * Previsualizar plantilla
     */
    public function preview(EmailTemplate $emailTemplate, Request $request)
    {
        $data = $request->get('data', []);
        
        // Datos de ejemplo si no se proporcionan
        if (empty($data)) {
            $data = [
                'user_name' => 'Juan Pérez',
                'property_title' => 'Casa de Playa en Cartagena',
                'property_location' => 'Cartagena, Bolívar',
                'check_in_date' => '15/12/2024',
                'check_in_time' => '15:00',
                'check_out_date' => '20/12/2024',
                'check_out_time' => '11:00',
                'guests' => '4',
                'nights' => '5',
                'total_amount' => '1,500,000',
                'admin_notes' => 'Bienvenido, esperamos que disfrutes tu estadía.',
                'rejection_reason' => 'La propiedad no está disponible en las fechas solicitadas.'
            ];
        }

        $processed = $emailTemplate->process($data);

        return response()->json([
            'success' => true,
            'subject' => $processed['subject'],
            'body' => $processed['body'],
            'body_text' => $processed['body_text']
        ]);
    }

    /**
     * Enviar correo de prueba
     */
    public function sendTest(EmailTemplate $emailTemplate, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'data' => 'nullable|array'
        ]);

        $data = $request->get('data', []);
        $processed = $emailTemplate->process($data);

        try {
            Mail::raw($processed['body_text'] ?: strip_tags($processed['body']), function($message) use ($processed, $request) {
                $message->to($request->email)
                        ->subject($processed['subject']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Correo de prueba enviado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear plantillas por defecto
     */
    public function createDefaults()
    {
        EmailTemplate::createDefaultTemplates();

        return redirect()->route('superadmin.email-templates.index')
            ->with('success', 'Plantillas por defecto creadas correctamente');
    }
}