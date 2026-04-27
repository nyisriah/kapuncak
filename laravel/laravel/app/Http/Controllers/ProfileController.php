<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page
     */
    public function show()
    {
        $user = auth()->user();
        $bookings = $user->bookings()
            ->with('villa')
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'bookings'));
    }

    /**
     * Show the edit profile form
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                unlink(storage_path('app/public/' . $user->avatar));
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Update user
        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'current_password' => 'Password saat ini salah',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Get user's avatar URL
     */
    public function getAvatarUrl()
    {
        $user = auth()->user();
        
        if ($user->avatar) {
            return asset('storage/' . $user->avatar);
        }

        // Default avatar using UI Avatars service
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1F7A63&color=fff';
    }
}
