<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Profile Feature Tests
 * Tests for user profile update functionality
 * 
 * Quality Attribute: SECURITY & DATA INTEGRITY
 * - Ensures current password verification before updates
 * - Validates email uniqueness
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | PROFILE UPDATE TESTS WITH CURRENT PASSWORD
    |--------------------------------------------------------------------------
    */

    /**
     * Test: User can update profile with correct current password
     */
    public function test_user_can_update_profile_with_correct_current_password()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Update profile with correct current password
        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Updated Name',
            'email' => 'testuser@test.com',
            'current_password' => 'password123',
        ]);

        // Assert no validation errors for current_password
        $response->assertSessionDoesntHaveErrors('current_password');

        // Refresh user from database
        $user->refresh();

        // Assert name was updated
        $this->assertEquals('Updated Name', $user->name);
    }

    /**
     * Test: Profile update rejected with wrong current password
     */
    public function test_profile_update_rejected_with_wrong_current_password()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Try to update profile with wrong current password
        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Updated Name',
            'email' => 'testuser@test.com',
            'current_password' => 'wrongpassword',
        ]);

        // Assert validation error for current_password
        $response->assertSessionHasErrors('current_password');

        // Refresh user from database
        $user->refresh();

        // Assert name was NOT updated
        $this->assertEquals('Test User', $user->name);
    }

    /**
     * Test: User can change password with correct current password
     */
    public function test_user_can_change_password()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Change password
        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Refresh user from database
        $user->refresh();

        // Assert new password works
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /*
    |--------------------------------------------------------------------------
    | EMAIL UNIQUENESS TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Email change rejected if email already exists
     */
    public function test_email_change_rejected_if_already_exists()
    {
        // Create two users
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Try to change user1's email to user2's email
        $response = $this->actingAs($user1)->post('/profile', [
            'name' => 'User One',
            'email' => 'user2@test.com',
            'current_password' => 'password123',
        ]);

        // Assert validation error for email
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: User can update email to unique value
     */
    public function test_user_can_update_email_to_unique_value()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Change email to a new unique email
        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'newemail@test.com',
            'current_password' => 'password123',
        ]);

        // Refresh user from database
        $user->refresh();

        // Assert email was updated
        $this->assertEquals('newemail@test.com', $user->email);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION TESTS
    |--------------------------------------------------------------------------
    */

    /**
     * Test: Profile update requires name field
     */
    public function test_profile_update_requires_name()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => '',
            'email' => 'testuser@test.com',
            'current_password' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test: Profile update requires email field
     */
    public function test_profile_update_requires_email()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => '',
            'current_password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Profile update requires current_password field
     */
    public function test_profile_update_requires_current_password()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    /**
     * Test: New password must be confirmed
     */
    public function test_new_password_must_be_confirmed()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'current_password' => 'password123',
            'password' => 'newpassword123',
            // Missing password_confirmation
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: New password minimum length (8 characters)
     */
    public function test_new_password_minimum_length()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'current_password' => 'password123',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Unauthenticated user cannot access profile page
     */
    public function test_unauthenticated_user_cannot_access_profile()
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    /**
     * Test: Email must be valid format
     */
    public function test_email_must_be_valid_format()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'current_password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
