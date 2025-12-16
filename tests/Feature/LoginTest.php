<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Login Feature Tests
 * Tests for user authentication functionality
 * 
 * Quality Attribute: CONFIDENTIALITY (Security)
 * - Ensures only authenticated users can access the system
 * - Verifies role-based access control
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Successful login with correct credentials
     */
    public function test_user_can_login_with_correct_credentials()
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Attempt login (note: this app requires role in login)
        $response = $this->post('/login', [
            'email' => 'testuser@test.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        // Assert user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Failed login with wrong password
     */
    public function test_user_cannot_login_with_wrong_password()
    {
        // Create a test user
        User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('correctpassword'),
            'role' => 'student',
        ]);

        // Attempt login with wrong password
        $response = $this->post('/login', [
            'email' => 'testuser@test.com',
            'password' => 'wrongpassword123',
            'role' => 'student',
        ]);

        // Assert user is NOT authenticated
        $this->assertGuest();
    }

    /**
     * Test: Failed login with non-existent email
     */
    public function test_user_cannot_login_with_nonexistent_email()
    {
        // Attempt login with email that doesn't exist
        $response = $this->post('/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        // Assert user is NOT authenticated
        $this->assertGuest();
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE-BASED REDIRECT TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Student is redirected to student dashboard after login
     */
    public function test_student_redirected_to_student_dashboard()
    {
        // Create a student user
        $user = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Attempt login
        $response = $this->post('/login', [
            'email' => 'student@test.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        // Assert redirect to student dashboard
        $response->assertRedirect('/dashboard/student');
    }

    /**
     * Test: Lecturer is redirected to lecturer dashboard after login
     */
    public function test_lecturer_redirected_to_lecturer_dashboard()
    {
        // Create a lecturer user
        $user = User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'lecturer',
        ]);

        // Attempt login
        $response = $this->post('/login', [
            'email' => 'lecturer@test.com',
            'password' => 'password123',
            'role' => 'lecturer',
        ]);

        // Assert redirect to lecturer dashboard
        $response->assertRedirect('/dashboard/lecturer');
    }

    /**
     * Test: Admin is redirected to admin dashboard after login
     */
    public function test_admin_redirected_to_admin_dashboard()
    {
        // Create an admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Attempt login
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        // Assert redirect to admin dashboard
        $response->assertRedirect('/dashboard/admin');
    }

    /*
    |--------------------------------------------------------------------------
    | UNAUTHORIZED ACCESS TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Student CANNOT access lecturer dashboard (403 Forbidden)
     */
    public function test_student_cannot_access_lecturer_dashboard()
    {
        // Create a student user
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Login as student and try to access lecturer dashboard
        $response = $this->actingAs($student)->get('/dashboard/lecturer');

        // Assert 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Test: Student CANNOT access admin dashboard (403 Forbidden)
     */
    public function test_student_cannot_access_admin_dashboard()
    {
        // Create a student user
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Login as student and try to access admin dashboard
        $response = $this->actingAs($student)->get('/dashboard/admin');

        // Assert 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Test: Lecturer CANNOT access admin dashboard (403 Forbidden)
     */
    public function test_lecturer_cannot_access_admin_dashboard()
    {
        // Create a lecturer user
        $lecturer = User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'lecturer',
        ]);

        // Login as lecturer and try to access admin dashboard
        $response = $this->actingAs($lecturer)->get('/dashboard/admin');

        // Assert 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Test: Unauthenticated user cannot access dashboard
     */
    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        // Try to access student dashboard without login
        $response = $this->get('/dashboard/student');

        // Assert redirect to login page
        $response->assertRedirect('/login');
    }

    /**
     * Test: User can logout successfully
     */
    public function test_user_can_logout()
    {
        // Create and login a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $this->actingAs($user);

        // Logout
        $response = $this->get('/logout');

        // Assert user is logged out
        $this->assertGuest();
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Login requires email field
     */
    public function test_login_requires_email()
    {
        $response = $this->post('/login', [
            'password' => 'password123',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Login requires password field
     */
    public function test_login_requires_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Login requires role field
     */
    public function test_login_requires_role()
    {
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('role');
    }
}
