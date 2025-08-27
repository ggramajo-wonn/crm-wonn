<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use Symfony\Component\Mime\Email as SymfonyEmail;

class EmailServerController extends Controller
{
    public function index()
    {
        $company  = Company::first();
        $settings = [];
        if ($company && !empty($company->mail_settings)) {
            $settings = is_array($company->mail_settings) ? $company->mail_settings : (json_decode($company->mail_settings, true) ?: []);
        }
        return view('config.email.index', compact('company', 'settings'));
    }

    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'to'      => 'required|email',
            'subject' => 'nullable|string|max:150',
            'message' => 'nullable|string|max:5000',
        ]);

        $company  = Company::first();
        $settings = [];
        if ($company && !empty($company->mail_settings)) {
            $settings = is_array($company->mail_settings) ? $company->mail_settings : (json_decode($company->mail_settings, true) ?: []);
        }

        $host = $settings['host'] ?? null;
        $port = (int)($settings['port'] ?? 587);
        $security = strtoupper($settings['security'] ?? 'TLS');
        $encryption = $security === 'SSL' ? 'ssl' : ($security === 'TLS' ? 'tls' : null);
        if ($port === 465) $encryption = 'ssl';
        if ($port === 587 && $encryption === null) $encryption = 'tls';

        $fromAddr = $settings['username'] ?? null; // forzar remitente = usuario
        $fromName = $settings['from_name'] ?? ($company->name ?? 'Sistema');

        if (!filter_var($fromAddr, FILTER_VALIDATE_EMAIL)) {
            return back()->with('err', 'Configuración inválida: el remitente (From address) no es un email válido.');
        }
        if (!$host || empty($settings['username']) || empty($settings['password'])) {
            return back()->with('err', 'Configuración SMTP incompleta: faltan host/usuario/clave.');
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.from.address', $fromAddr);
        Config::set('mail.from.name', $fromName);
        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $host,
            'port'       => $port,
            'encryption' => $encryption,
            'username'   => $settings['username'],
            'password'   => $settings['password'],
            'timeout'    => 10,
            'auth_mode'  => null,
        ]);

        $subject = $validated['subject'] ?: 'Prueba WONN';
        $html    = $validated['message'] ?: '<p>Este es un correo de <strong>prueba</strong> enviado desde la configuración SMTP.</p>';
        $textAlt = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        if ($textAlt === '') { $textAlt = ' '; }

        try {
            Mail::html($html, function ($m) use ($validated, $subject, $fromAddr, $fromName, $textAlt) {
                $m->from($fromAddr, $fromName)
                  ->replyTo($fromAddr, $fromName)
                  ->to($validated['to'])
                  ->subject($subject)
                  ->bcc($fromAddr);
                $sym = $m->getSymfonyMessage();
                if ($sym instanceof SymfonyEmail) {
                    $sym->text($textAlt);
                    $sym->getHeaders()->addTextHeader('X-Mailer', 'WONN Mailer');
                    $sym->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:' . $fromAddr . '>');
                    $sym->returnPath($fromAddr);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('err', 'Error al enviar: ' . $e->getMessage());
        }

        return back()->with('ok', 'Correo de prueba enviado a ' . $validated['to']);
    }
}
