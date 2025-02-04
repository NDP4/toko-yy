<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $redirectTo = '/customer/dashboard';

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate all fields including reCAPTCHA
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'ip' => request()->ip(),
                ]);

                Log::info('reCAPTCHA response:', [
                    'response' => $response->json(),
                    'secret_used' => config('services.recaptcha.secret_key')
                ]);

                if (!$response->json('success')) {
                    $fail('Mohon verifikasi bahwa anda bukan robot.');
                }
            }],
        ], [
            'g-recaptcha-response.required' => 'Mohon verifikasi bahwa anda bukan robot.'
        ]);

        // Attempt authentication with only email and password
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            if ($user && $user->isAdmin()) {
                return redirect('/panel'); // Redirect to Filament dashboard
            }

            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang anda masukan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
