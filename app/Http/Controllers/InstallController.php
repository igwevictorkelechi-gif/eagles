<?php

namespace App\Http\Controllers;

use App\Support\DotEnv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PDO;

class InstallController extends Controller
{
    public const LOCK = 'installed';

    public static function isInstalled(): bool
    {
        return file_exists(storage_path(self::LOCK));
    }

    private function guard()
    {
        if (self::isInstalled()) {
            return redirect('/')->with('ok', 'SAS is already installed.');
        }
        return null;
    }

    // Step 1 — requirements
    public function requirements()
    {
        if ($r = $this->guard()) return $r;

        $checks = [
            ['PHP >= 8.2', version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['PDO MySQL extension', extension_loaded('pdo_mysql')],
            ['Mbstring extension', extension_loaded('mbstring')],
            ['OpenSSL extension', extension_loaded('openssl')],
            ['storage/ is writable', is_writable(storage_path())],
            ['bootstrap/cache/ is writable', is_writable(base_path('bootstrap/cache'))],
            ['.env is writable', is_writable(DotEnv::path()) || is_writable(base_path())],
        ];
        $ok = collect($checks)->every(fn ($c) => $c[1]);

        return view('install.requirements', compact('checks', 'ok'));
    }

    // Step 2 — database
    public function database()
    {
        if ($r = $this->guard()) return $r;
        return view('install.database', ['env' => DotEnv::read()]);
    }

    public function saveDatabase(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $data = $request->validate([
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'string'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
            'app_url' => ['nullable', 'string'],
        ]);

        // Test the connection live before writing anything.
        try {
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_database']}";
            new PDO($dsn, $data['db_username'], $data['db_password'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'db_host' => 'Could not connect: ' . $e->getMessage(),
            ]);
        }

        $env = DotEnv::read();
        DotEnv::set([
            'APP_URL' => $data['app_url'] ?: ($env['APP_URL'] ?? $request->getSchemeAndHttpHost()),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
        ]);

        // Generate an app key if the shipped .env didn't have one.
        if (empty($env['APP_KEY'])) {
            DotEnv::set(['APP_KEY' => 'base64:' . base64_encode(random_bytes(32))]);
        }

        return redirect()->route('install.mail');
    }

    // Step 3 — SMTP (optional)
    public function mail()
    {
        if ($r = $this->guard()) return $r;
        return view('install.mail', ['env' => DotEnv::read()]);
    }

    public function saveMail(Request $request)
    {
        if ($r = $this->guard()) return $r;

        if ($request->input('action') === 'skip') {
            return redirect()->route('install.run');
        }

        $data = $request->validate([
            'mail_host' => ['required', 'string'],
            'mail_port' => ['required', 'string'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email'],
            'mail_from_name' => ['nullable', 'string'],
        ]);

        DotEnv::set([
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $data['mail_host'],
            'MAIL_PORT' => $data['mail_port'],
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => ($data['mail_encryption'] ?? 'tls') === 'none' ? '' : ($data['mail_encryption'] ?? 'tls'),
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? 'no-reply@example.com',
            'MAIL_FROM_NAME' => $data['mail_from_name'] ?? 'SAS',
        ]);

        // Optional live test email.
        if ($request->filled('test_to')) {
            try {
                $this->applyRuntimeConfig();
                Mail::raw('SAS SMTP test — your mail settings work. 🎉', function ($m) use ($request) {
                    $m->to($request->input('test_to'))->subject('SAS SMTP test');
                });
                return back()->with('ok', 'Test email sent to ' . $request->input('test_to') . '. Check the inbox, then continue.')->withInput();
            } catch (\Throwable $e) {
                return back()->withInput()->withErrors(['mail_host' => 'Saved, but the test failed: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('install.run');
    }

    // Step 4 — build the database
    public function run()
    {
        if ($r = $this->guard()) return $r;

        $this->applyRuntimeConfig();

        try {
            DB::connection()->getPdo();
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $e) {
            return view('install.run', ['error' => $e->getMessage()]);
        }

        // Lock the installer.
        @file_put_contents(storage_path(self::LOCK), 'Installed at ' . now()->toDateTimeString() . "\n");

        return redirect()->route('install.finished');
    }

    public function finished()
    {
        return view('install.finished');
    }

    // Apply the freshly-written .env DB/mail values to the running process,
    // since the current request booted before they were saved.
    private function applyRuntimeConfig(): void
    {
        $env = DotEnv::read();

        Config::set('database.connections.mysql.host', $env['DB_HOST'] ?? '127.0.0.1');
        Config::set('database.connections.mysql.port', $env['DB_PORT'] ?? '3306');
        Config::set('database.connections.mysql.database', $env['DB_DATABASE'] ?? '');
        Config::set('database.connections.mysql.username', $env['DB_USERNAME'] ?? '');
        Config::set('database.connections.mysql.password', $env['DB_PASSWORD'] ?? '');
        Config::set('database.default', 'mysql');
        DB::purge('mysql');

        if (! empty($env['MAIL_HOST'])) {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $env['MAIL_HOST']);
            Config::set('mail.mailers.smtp.port', $env['MAIL_PORT'] ?? '587');
            Config::set('mail.mailers.smtp.username', $env['MAIL_USERNAME'] ?? null);
            Config::set('mail.mailers.smtp.password', $env['MAIL_PASSWORD'] ?? null);
            Config::set('mail.mailers.smtp.encryption', ($env['MAIL_ENCRYPTION'] ?? '') ?: null);
            Config::set('mail.from.address', $env['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.com');
            Config::set('mail.from.name', $env['MAIL_FROM_NAME'] ?? 'SAS');
        }
    }
}
