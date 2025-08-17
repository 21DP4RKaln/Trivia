<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\TermsOfService;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function showLoginForm(Request $request): View
    {
        // Check if request is from mobile device
        if ($this->isMobileDevice($request)) {
            return view('auth.mobile-auth', ['isLogin' => true]);
        }
        
        return view('auth.auth', ['isLogin' => true]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function showRegisterForm(Request $request): View
    {
        // Check if request is from mobile device
        if ($this->isMobileDevice($request)) {
            return view('auth.mobile-auth', ['isLogin' => false]);
        }
        
        return view('auth.auth', ['isLogin' => false]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Name is required.',
            'email.unique' => 'This email address is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'terms.accepted' => 'You must accept the terms of service.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome! Your account has been created successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('trivia.index');
    }

    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Get user's best session (highest score)
        $bestSession = $user->getBestSession();
        
        // Get user's fastest session
        $fastestSession = $user->getFastestSession();
        
        // Get recent games with pagination (10 per page)
        $recentGames = $user->gameSessions()
            ->where('completed', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('auth.dashboard', compact('user', 'bestSession', 'fastestSession', 'recentGames'));
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function showTermsOfService(): View
    {
        // Clear any cached data to ensure we get the latest terms
        \Illuminate\Support\Facades\Cache::forget('terms_of_service_active');
        
        $termsData = TermsOfService::getCurrentContent();
        
        return view('auth.terms-of-service', compact('termsData'));
    }

    public function checkTermsUpdates(): \Illuminate\Http\JsonResponse
    {
        try {
            $currentTerms = TermsOfService::getActive();
            
            if (!$currentTerms) {
                return response()->json([
                    'hasUpdates' => false,
                    'lastUpdated' => null
                ]);
            }

            $lastUpdated = $currentTerms->updated_at->format('F j, Y');
            $termsUpdatedAt = \Illuminate\Support\Facades\Cache::get('terms_updated_at');
            
            // Check if there was a recent update (within last hour)
            $hasRecentUpdate = $termsUpdatedAt && (now()->timestamp - $termsUpdatedAt) < 3600;
            
            return response()->json([
                'hasUpdates' => $hasRecentUpdate,
                'lastUpdated' => $lastUpdated,
                'version' => $currentTerms->version,
                'timestamp' => $currentTerms->updated_at->timestamp,
                'updateTime' => $termsUpdatedAt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'hasUpdates' => false,
                'error' => 'Failed to check for updates'
            ], 500);
        }
    }

    /**
     * Detect if the request is from a mobile device
     */
    private function isMobileDevice(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');
        
        // Check for mobile patterns in user agent
        $mobilePatterns = [
            '/Mobile/',
            '/Android/',
            '/iPhone/',
            '/iPad/',
            '/iPod/',
            '/BlackBerry/',
            '/Windows Phone/',
            '/webOS/',
            '/Opera Mini/',
            '/Opera Mobi/'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }
        
        // Check for small screen size via viewport width (if available)
        if ($request->hasHeader('Sec-CH-Viewport-Width')) {
            $viewportWidth = intval($request->header('Sec-CH-Viewport-Width'));
            if ($viewportWidth > 0 && $viewportWidth <= 768) {
                return true;
            }
        }
        
        // Check for touch capability
        if ($request->hasHeader('Sec-CH-UA-Mobile') && $request->header('Sec-CH-UA-Mobile') === '?1') {
            return true;
        }
        
        return false;
    }
}
