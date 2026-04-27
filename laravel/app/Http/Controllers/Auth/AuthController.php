<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $this->checkPassword($user, $credentials['password'])) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended($this->redirectTo($user));
    }

    protected function checkPassword(User $user, string $password): bool
    {
        $storedPassword = $user->password;

        if ($this->passwordLooksHashed($storedPassword)) {
            $isValid = password_verify($password, $storedPassword);
        } else {
            $isValid = hash_equals($storedPassword, $password);
        }

        if ($isValid && ! $this->passwordLooksHashed($storedPassword)) {
            $user->password = Hash::make($password);
            $user->save();
        }

        return $isValid;
    }

    protected function passwordLooksHashed(string $value): bool
    {
        return password_get_info($value)['algo'] !== 0;
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        // Send email verification
        $user->sendEmailVerificationNotification();

        // Login user
        Auth::login($user);

        // Redirect to email verification notice
        return redirect()->route('verification.notice');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Show the email verification notice
     */
    public function showVerifyEmail(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectTo($request->user()))
            : view('auth.verify-email');
    }

    /**
     * Verify the user's email
     */
    public function verifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo($request->user()))->with('message', 'Email sudah terverifikasi.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect($this->redirectTo($request->user()))->with('message', 'Email berhasil diverifikasi!');
    }

    /**
     * Resend the email verification notification
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('message', 'Email sudah terverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Link verifikasi telah dikirim ke email Anda.');
    }

    protected function redirectTo(User $user): string
    {
        return $user->role === 'admin' ? '/admin' : '/dashboard';
    }
}
