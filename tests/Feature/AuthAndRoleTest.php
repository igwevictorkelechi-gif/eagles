<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    private function school(string $slug = 'alpha'): School
    {
        return School::create(['name' => ucfirst($slug), 'slug' => $slug]);
    }

    private function user(string $role, ?string $schoolId, string $email): User
    {
        return User::create([
            'school_id' => $schoolId,
            'role' => $role,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'password' => 'Password123!',
            'email_verified' => true,
        ]);
    }

    public function test_marketing_pages_are_public(): void
    {
        $this->get('/')->assertOk();
        $this->get('/features')->assertOk();
        $this->get('/faq')->assertOk();
        $this->get('/pricing')->assertOk();
    }

    public function test_guests_are_redirected_from_the_app(): void
    {
        $this->get('/app')->assertRedirect('/login');
        $this->get('/platform')->assertRedirect('/login');
    }

    public function test_a_school_admin_can_log_in_and_reach_the_dashboard(): void
    {
        $school = $this->school();
        $this->user('school_admin', $school->id, 'admin@alpha.test');

        $this->post('/login', ['email' => 'admin@alpha.test', 'password' => 'Password123!'])
            ->assertRedirect('/app');

        $this->get('/app')->assertOk();
    }

    public function test_login_fails_with_a_wrong_password(): void
    {
        $school = $this->school();
        $this->user('school_admin', $school->id, 'admin@alpha.test');

        $this->post('/login', ['email' => 'admin@alpha.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_super_admin_cannot_reach_a_school_area(): void
    {
        $this->actingAs($this->user('super_admin', null, 'super@sas.test'))
            ->get('/app')->assertForbidden();
    }

    public function test_a_school_admin_cannot_reach_the_platform_area(): void
    {
        $school = $this->school();
        $this->actingAs($this->user('school_admin', $school->id, 'admin@alpha.test'))
            ->get('/platform')->assertForbidden();
    }

    public function test_a_teacher_cannot_reach_admin_only_areas(): void
    {
        $school = $this->school();
        $this->actingAs($this->user('teacher', $school->id, 'teacher@alpha.test'))
            ->get('/app/fees')->assertForbidden();
    }

    public function test_registration_creates_a_school_with_a_trial(): void
    {
        $this->post('/start', [
            'school_name' => 'Bright Stars',
            'admin_first_name' => 'Tunde',
            'admin_last_name' => 'Bakare',
            'email' => 'tunde@bright.test',
            'password' => 'Password123!',
        ])->assertRedirect('/app');

        $this->assertDatabaseHas('schools', ['name' => 'Bright Stars']);
        $this->assertDatabaseHas('users', ['email' => 'tunde@bright.test', 'role' => 'school_admin']);
    }
}
