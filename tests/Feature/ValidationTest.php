<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Validation Logic Tests
 * 
 * Purpose: Confirm code rejects invalid inputs BEFORE database insertion
 * Quality Attribute: INTEGRITY (Data Accuracy)
 * 
 * These tests verify that the system validates all user inputs and
 * prevents invalid data from being stored in the database.
 */
class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | REGISTRATION VALIDATION - REJECT INVALID INPUTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Empty name is rejected before database insertion
     */
    public function test_registration_rejects_empty_name()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => '',  // Invalid: empty
            'email' => 'test@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('name');
        
        // Assert NO user was inserted into database
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);
    }

    /**
     * Test: Empty email is rejected before database insertion
     */
    public function test_registration_rejects_empty_email()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => '',  // Invalid: empty
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('email');
        
        // Assert NO user was inserted into database
        $this->assertDatabaseMissing('users', ['name' => 'Test User']);
    }

    /**
     * Test: Invalid email format is rejected before database insertion
     */
    public function test_registration_rejects_invalid_email_format()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'not-a-valid-email',  // Invalid: bad format
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('email');
        
        // Assert NO user was inserted into database
        $this->assertDatabaseMissing('users', ['name' => 'Test User']);
    }

    /**
     * Test: Duplicate email is rejected before database insertion
     */
    public function test_registration_rejects_duplicate_email()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create existing user
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'New User',
            'email' => 'existing@gmail.com',  // Invalid: duplicate
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('email');
        
        // Assert duplicate was NOT inserted (still only 2 users)
        $this->assertEquals(2, User::count());
    }

    /**
     * Test: Password less than 8 characters is rejected before database insertion
     */
    public function test_registration_rejects_short_password()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => '123',  // Invalid: too short
            'password_confirmation' => '123',
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('password');
        
        // Assert NO user was inserted into database
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);
    }

    /**
     * Test: Password mismatch is rejected before database insertion
     */
    public function test_registration_rejects_password_mismatch()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',  // Invalid: doesn't match
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('password');
        
        // Assert NO user was inserted into database
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);
    }

    /**
     * Test: Invalid role is rejected before database insertion
     */
    public function test_registration_rejects_invalid_role()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/store-user', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'superuser',  // Invalid: not student or lecturer
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('role');
        
        // Assert NO user was inserted into database
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE UPDATE VALIDATION - REJECT INVALID INPUTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Profile update rejects empty name before database update
     */
    public function test_profile_update_rejects_empty_name()
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => '',  // Invalid: empty
            'email' => 'user@test.com',
            'current_password' => 'password123',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('name');
        
        // Assert database was NOT updated
        $this->assertDatabaseHas('users', [
            'email' => 'user@test.com',
            'name' => 'Original Name',  // Still original
        ]);
    }

    /**
     * Test: Profile update rejects invalid email format before database update
     */
    public function test_profile_update_rejects_invalid_email()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'original@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'invalid-email',  // Invalid: bad format
            'current_password' => 'password123',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('email');
        
        // Assert database was NOT updated
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'original@test.com',  // Still original
        ]);
    }

    /**
     * Test: Profile update rejects wrong current password before database update
     */
    public function test_profile_update_rejects_wrong_current_password()
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'New Name',
            'email' => 'user@test.com',
            'current_password' => 'wrongpassword',  // Invalid: wrong password
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('current_password');
        
        // Assert database was NOT updated
        $this->assertDatabaseHas('users', [
            'email' => 'user@test.com',
            'name' => 'Original Name',  // Still original
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN VALIDATION - REJECT INVALID INPUTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Login rejects empty email
     */
    public function test_login_rejects_empty_email()
    {
        $response = $this->post('/login', [
            'email' => '',  // Invalid: empty
            'password' => 'password123',
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Login rejects empty password
     */
    public function test_login_rejects_empty_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => '',  // Invalid: empty
            'role' => 'student',
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Login rejects invalid role
     */
    public function test_login_rejects_invalid_role()
    {
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'password123',
            'role' => 'superadmin',  // Invalid: not a valid role
        ]);

        // Assert validation error returned
        $response->assertSessionHasErrors('role');
    }
}
