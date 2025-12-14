<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\UserRegisteredMail;

class AdminUserController extends Controller
{

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return view('admin.admin_create_user');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,lecturer',
        ]);

        $plainPassword = $data['password']; // Store before hashing

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        // Send welcome email
        try {
            Mail::to($user->email)->send(new UserRegisteredMail($user, $plainPassword));
        } catch (\Exception $e) {
            // Log error but don't fail the registration
            Log::error('Failed to send registration email to ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.dashboard')->with('success', ucfirst($data['role']).' registered successfully!');
    }
}
