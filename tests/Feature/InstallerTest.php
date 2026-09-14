<?php

namespace Tests\Feature;

use App\Support\DotEnv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_uninstalled_app_redirects_everything_to_the_wizard(): void
    {
        $this->markNotInstalled();

        $this->get('/')->assertRedirect(route('install.requirements'));
        $this->get('/login')->assertRedirect(route('install.requirements'));
    }

    public function test_the_wizard_pages_render_when_not_installed(): void
    {
        $this->markNotInstalled();

        $this->get('/install')->assertOk()->assertSee('Requirements');
        $this->get('/install/database')->assertOk()->assertSee('Database connection');
        $this->get('/install/mail')->assertOk()->assertSee('SMTP');
    }

    public function test_a_bad_database_connection_is_rejected_before_saving(): void
    {
        $this->markNotInstalled();

        $this->post('/install/database', [
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_database' => 'nope',
            'db_username' => 'nope',
            'db_password' => 'nope',
        ])->assertSessionHasErrors('db_host');
    }

    public function test_once_installed_the_wizard_is_hidden_and_the_site_loads(): void
    {
        // setUp() already created the lock, so the app is "installed".
        $this->get('/install')->assertRedirect('/');
        $this->get('/')->assertOk()->assertSee('Run your entire school');
    }

    public function test_dotenv_writer_updates_and_adds_keys(): void
    {
        $tmp = base_path('.env');
        $backup = file_exists($tmp) ? file_get_contents($tmp) : null;
        file_put_contents($tmp, "APP_NAME=SAS\nDB_HOST=old\n");

        try {
            DotEnv::set(['DB_HOST' => 'localhost', 'DB_PASSWORD' => 'p@ss word']);
            $env = DotEnv::read();
            $this->assertSame('localhost', $env['DB_HOST']);
            $this->assertSame('p@ss word', $env['DB_PASSWORD']);
            $this->assertSame('SAS', $env['APP_NAME']);
        } finally {
            if ($backup !== null) file_put_contents($tmp, $backup);
        }
    }
}
