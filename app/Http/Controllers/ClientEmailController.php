<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Client;
use App\Models\Company;
use App\Models\EmailLog;
use Symfony\Component\Mime\Email as SymfonyEmail;

class ClientEmailController extends Controller
{
    /* =========================
     *  CONFIG SMTP (helpers)
     * ========================= */

    /**
     * Carga SMTP de companies.mail_settings (JSON) y lo aplica a runtime.
     * Si falta la encripción, infiere: 465 => ssl, 587 => tls, 25 => null.
     */
    private function applySmtpFromCompanyJson(): void
    {
        try { $company = Company::query()->first(); } catch (\Throwable $e) { $company = null; }
        if (!$company) return;

        $raw = $company->mail_settings ?? [];
        if (is_string($raw))      { $cfg = json_decode($raw, true) ?: []; }
        elseif (is_object($raw))  { $cfg = (array)$raw; }
        elseif (is_array($raw))   { $cfg = $raw; }
        else                      { $cfg = []; }

        $get = function(array $keys, $default=null) use ($cfg) {
            foreach ($keys as $k) {
                if (array_key_exists($k,$cfg) && $cfg[$k] !== null && $cfg[$k] !== '') return $cfg[$k];
            }
            return $default;
        };

        $host = $get(['host','smtp_host','mail_host','server']);
        $port = (int) $get(['port','smtp_port','mail_port'], 0);
        $user = $get(['username','user','smtp_user','mail_user']);
        $pass = $get(['password','pass','smtp_pass','mail_pass']);

        // encryption puede venir con otros nombres
        $enc  = $get(['encryption','enc','smtp_security','mail_security','secure']);
        $enc = $enc !== null ? strtolower((string)$enc) : null;
        if (in_array($enc, ['0','none','false','null',''], true)) $enc = null;

        // Inferencia por puerto si no vino explícito
        if ($enc === null) {
            if ($port === 465) $enc = 'ssl';
            elseif ($port === 587) $enc = 'tls';
            else $enc = null;
        }

        // FROM puede ser string u objeto {address,name}
        $fromAddr = null; $fromName = null;
        $fromRaw = $get(['from','from_address','from_email','email']);
        if (is_array($fromRaw)) {
            $fromAddr = $fromRaw['address'] ?? ($fromRaw['email'] ?? null);
            $fromName = $fromRaw['name'] ?? null;
        } else {
            $fromAddr = $fromRaw ?: null;
        }
        if (!$fromName) $fromName = $get(['from_name','name'], $company->name ?? 'WONN');

        if (!$host || !$port) {
            Log::warning('Mail settings missing host/port in companies.mail_settings');
            return;
        }

        // Si el FROM no es válido, cae a username o email de la empresa
        if (!$fromAddr || stripos((string)$fromAddr,'example.com') !== false) {
            if (!empty($user))      $fromAddr = $user;
            elseif (!empty($company->email)) $fromAddr = $company->email;
        }

        // Aplicar a runtime
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        if ($user !== null) Config::set('mail.mailers.smtp.username', $user);
        if ($pass !== null) Config::set('mail.mailers.smtp.password', $pass);
        Config::set('mail.mailers.smtp.encryption', $enc); // ssl|tls|null

        // Algunas instalaciones requieren auth_mode=login
        if ($get(['auth_mode','auth'], null)) {
            Config::set('mail.mailers.smtp.auth_mode', $get(['auth_mode','auth']));
        } else {
            // default sensato
            Config::set('mail.mailers.smtp.auth_mode', 'login');
        }

        if ($fromAddr) {
            Config::set('mail.from.address', $fromAddr);
            Config::set('mail.from.name', $fromName);
        }

        // SMTPS 465 con certificados autofirmados (opcional via .env)
        if (env('MAIL_ALLOW_SELF_SIGNED', false)) {
            Config::set('mail.mailers.smtp.stream', [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                ],
            ]);
        }

        Log::info('Mail config in use (company.json)', [
            'host' => $host,
            'port' => $port,
            'enc'  => $enc,
            'from' => $fromAddr,
            'user' => $user ? preg_replace('/:.+@/','@',$user) : null,
        ]);
    }

    private function resolveDefaultFrom(): ?string
    {
        try { $company = Company::query()->first(); } catch (\Throwable $e) { $company = null; }
        if (!$company) return config('mail.from.address');

        $raw = $company->mail_settings ?? [];
        $cfg = is_string($raw) ? (json_decode($raw, true) ?: []) : (array)$raw;
        $from = $cfg['from'] ?? ($cfg['from_address'] ?? ($cfg['from_email'] ?? ($cfg['email'] ?? null)));
        if (is_array($from)) $from = $from['address'] ?? ($from['email'] ?? null);
        if (!$from || stripos((string)$from,'example.com')!==false) {
            $from = $cfg['username'] ?? ($cfg['smtp_user'] ?? ($company->email ?? config('mail.from.address')));
        }
        return $from;
    }

    /* =========================
     *         VISTAS
     * ========================= */

    public function index(Request $request)
    {
        $perPage      = $request->input('per_page', 25);
        $defaultFrom  = $this->resolveDefaultFrom();

        $q = EmailLog::query()
            ->with(['client:id,name'])
            ->latest('id');

        if ($term = trim((string)$request->input('q',''))) {
            $q->where(function($w) use ($term) {
                $w->where('to','like',"%{$term}%")
                  ->orWhere('subject','like',"%{$term}%")
                  ->orWhereHas('client', function($cw) use ($term) {
                      $cw->where('name','like',"%{$term}%");
                  });
                if (ctype_digit($term)) $w->orWhere('client_id',(int)$term);
            });
        }

        if ($from = $request->input('fromDate')) $q->whereDate('created_at','>=',$from);
        if ($to   = $request->input('toDate'))   $q->whereDate('created_at','<=',$to);

        $logs = $perPage === 'all' ? $q->get() : $q->paginate((int)($perPage ?: 25))->withQueryString();
        return view('clientes.emails.index', compact('logs','defaultFrom'));
    }

    public function create(Request $request)
    {
        $clients = Client::query()
            ->select('id','name','email','dni','localidad','saldo')
            ->orderBy('name')->get();
        return view('clientes.emails.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'audience'      => 'required|in:all,name,by_name,localidad,router',
            'client_ids'    => 'nullable|array',
            'client_ids.*'  => 'integer',
            'subject'       => 'required|string|max:255',
            'body'          => 'required|string',
        ]);

        $this->applySmtpFromCompanyJson();
        if (($data['audience'] ?? null) === 'by_name') $data['audience'] = 'name';

        $recipients = $this->resolveRecipients($data);

        $fromAddr = config('mail.from.address');
        $fromName = config('mail.from.name', 'WONN');

        $sent = 0; $fails = []; $hasErrorCol = false;
        try { $hasErrorCol = DB::getSchemaBuilder()->hasColumn('email_logs','error'); } catch (\Throwable $e) {}

        foreach ($recipients as $rcpt) {
            $html = $this->renderBody($data['body'], $rcpt);
            $text = strip_tags(str_replace(['<br>','<br/>','<br />'], PHP_EOL, $html));

            $status = 'sent'; $errMsg = null;

            try {
                Mail::send([], [], function ($message) use ($rcpt, $data, $html, $text, $fromAddr, $fromName) {
                    $message->to($rcpt->email, $rcpt->name)
                            ->subject($data['subject'])
                            ->html($html)->text($text);

                    if ($fromAddr) $message->from($fromAddr, $fromName);

                    $sym = $message->getSymfonyMessage();
                    if ($sym instanceof SymfonyEmail && $fromAddr) {
                        $sym->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:' . $fromAddr . '>');
                        $sym->returnPath($fromAddr);
                        $sym->sender($fromAddr);
                    }
                });
                $sent++;
            } catch (\Throwable $e) {
                $status = 'failed'; $errMsg = $e->getMessage();
                Log::error('Email send failed', ['to'=>$rcpt->email, 'err'=>$errMsg]);
                $fails[] = $rcpt->email . ': ' . $errMsg;
            }

            $payload = [
                'client_id' => $rcpt->id,
                'to'        => $rcpt->email,
                'subject'   => $data['subject'],
                'body'      => $data['body'],
                'status'    => $status,
            ];
            if ($hasErrorCol && $errMsg) $payload['error'] = mb_substr($errMsg, 0, 1000);
            try { EmailLog::create($payload); } catch (\Throwable $e) {}
        }

        $msg = 'Se enviaron '.$sent.' de '.$recipients->count().' emails.';
        if ($fails) {
            return redirect()->route('clientes.emails.index')->with(['ok'=>$msg, 'error'=>'Falló: '.implode(' | ', array_slice($fails,0,3))]);
        }
        return redirect()->route('clientes.emails.index')->with('ok', $msg);
    }

    /* =========================
     *        AUXILIARES
     * ========================= */

    private function renderBody(string $template, $client): string
    {
        $map = [
            '{name}'      => $client->name ?? '',
            '{dni}'       => $client->dni ?? '',
            '{localidad}' => $client->localidad ?? '',
            '{email}'     => $client->email ?? '',
            '{saldo}'     => $client->saldo ?? '',
        ];
        return nl2br(strtr($template, $map));
    }

    private function resolveRecipients(array $data)
    {
        $q = Client::query()->select('id','name','email','dni','localidad','saldo')
                            ->whereNotNull('email')->where('email','<>','');

        switch ($data['audience']) {
            case 'all':
                break;
            case 'name':
                if (!empty($data['client_ids']) && is_array($data['client_ids'])) {
                    $q->whereIn('id', $data['client_ids']);
                } elseif (!empty($data['name'])) {
                    $q->where('name','like','%'.$data['name'].'%');
                } else {
                    $q->whereRaw('1=0');
                }
                break;
            case 'localidad':
                if (!empty($data['localidad'])) $q->where('localidad','like','%'.$data['localidad'].'%');
                else $q->whereRaw('1=0');
                break;
            case 'router':
                $q->whereRaw('1=0');
                break;
        }

        return $q->get();
    }
}
