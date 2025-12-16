<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Registration Feature Tests
 * Tests for admin user registration functionality (individual and bulk)
 * 
 * Quality Attribute: DATA INTEGRITY & SECURITY
 * - Ensures proper validation of user data
 * - Verifies passwords are securely hashed
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL REGISTRATION TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Admin can register a new student successfully
     */
    public function test_admin_can_register_student_successfully()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Register a new student (using real email domain for DNS validation)
        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'New Student',
            'email' => 'newstudent@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert student is created in database
        $this->assertDatabaseHas('users', [
            'name' => 'New Student',
            'email' => 'newstudent@gmail.com',
            'role' => 'student',
        ]);
    }

    /**
     * Test: Admin can register a new lecturer successfully
     */
    public function test_admin_can_register_lecturer_successfully()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Register a new lecturer (using real email domain for DNS validation)
        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'New Lecturer',
            'email' => 'newlecturer@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'lecturer',
        ]);

        // Assert lecturer is created in database
        $this->assertDatabaseHas('users', [
            'name' => 'New Lecturer',
            'email' => 'newlecturer@gmail.com',
            'role' => 'lecturer',
        ]);
    }

    /**
     * Test: Registration rejects duplicate emails
     */
    public function test_it_rejects_duplicate_emails()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create an existing user
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Try to register with duplicate email
        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'New User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert validation error for email
        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION TESTS - REQUIRED FIELDS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Registration requires name field
     */
    public function test_registration_requires_name()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => '',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test: Registration requires email field
     */
    public function test_registration_requires_email()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Registration requires password field
     */
    public function test_registration_requires_password()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Registration requires role field
     */
    public function test_registration_requires_role()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => '',
        ]);

        $response->assertSessionHasErrors('role');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION TESTS - FORMAT & CONSTRAINTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Email must be valid format
     */
    public function test_registration_requires_valid_email()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Password must be at least 8 characters
     */
    public function test_password_must_be_minimum_8_characters()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Password confirmation must match
     */
    public function test_password_confirmation_must_match()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Role must be valid (student or lecturer)
     */
    public function test_role_must_be_valid()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
        ]);

        $response->assertSessionHasErrors('role');
    }

    /*
    |--------------------------------------------------------------------------
    | SECURITY TESTS - PASSWORD HASHING
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Passwords are stored as hashed strings (NOT plain text)
     */
    public function test_passwords_are_stored_as_hashed_strings()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Register a new user (using real email domain for DNS validation)
        $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'hashtest@gmail.com',
            'password' => 'plainpassword123',
            'password_confirmation' => 'plainpassword123',
            'role' => 'student',
        ]);

        // Get the created user
        $user = User::where('email', 'hashtest@gmail.com')->first();

        // Assert user was created
        $this->assertNotNull($user);

        // Assert password is NOT plain text
        $this->assertNotEquals('plainpassword123', $user->password);

        // Assert password is hashed (starts with $2y$ for Bcrypt)
        $this->assertStringStartsWith('$2y$', $user->password);

        // Assert the hash verifies correctly
        $this->assertTrue(Hash::check('plainpassword123', $user->password));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESS CONTROL TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Non-admin cannot access registration page
     */
    public function test_non_admin_cannot_access_registration_page()
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($student)->get('/admin/create-user');

        $response->assertStatus(403);
    }

    /**
     * Test: Non-admin cannot register users
     */
    public function test_non_admin_cannot_register_users()
    {
        $lecturer = User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'lecturer',
        ]);

        $response = $this->actingAs($lecturer)->post('/admin/store-user', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | BULK REGISTRATION TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Bulk registration requires excel file
     */
    public function test_bulk_registration_requires_excel_file()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/bulk-register', [
            // No file uploaded
        ]);

        $response->assertSessionHasErrors('excel');
    }

    /**
     * Test: Non-admin cannot access bulk registration
     */
    public function test_non_admin_cannot_access_bulk_registration()
    {
        $lecturer = User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'lecturer',
        ]);

        $response = $this->actingAs($lecturer)->post('/admin/bulk-register', [
            'excel' => UploadedFile::fake()->create('users.xlsx', 100),
        ]);

        $response->assertStatus(403);
    }
}
