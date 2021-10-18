<?php

namespace Jiannius\Atom\Traits;

use Carbon\Carbon;
use Inertia\Inertia;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Jiannius\Atom\Requests\AuthResetPasswordRequest;

trait AuthManager
{
    /**
     * Show login page
     * 
     * @return \Illuminate\Http\Response
     */
    public function showLogin()
    {
        return Inertia::render('auth/login');
    }

    /**
     * Show register page
     * 
     * @return \Illuminate\Http\Response
     */
    public function showRegister()
    {
        return Inertia::render('auth/register', [
            'referral' => request()->ref,
        ]);
    }

    public function showRegisterCompleted()
    {
        return Inertia::render('auth/register-completed');
    }

    /**
     * Show forgot password page
     * 
     * @return \Illuminate\Http\Response
     */
    public function showForgotPassword()
    {
        return Inertia::render('auth/forgot-password');
    }

    /**
     * Show reset password page
     * 
     * @return \Illuminate\Http\Response
     */
    public function showResetPassword()
    {
        return Inertia::render('auth/reset-password', [
            'token' => request()->token,
            'email' => request()->email,
        ]);
    }

    /**
     * Login
     * 
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (env('APP_ENV') === 'local') {
            if ($user = User::where('email', request()->input('email'))->first()) {
                Auth::login($user);
            }
            else {
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);    
            }
        }
        else {
            $this->ensureIsNotRateLimited();
    
            if (! Auth::attempt(request()->only('email', 'password'), request()->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());
    
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }
    
            RateLimiter::clear($this->throttleKey());
        }

        request()->session()->regenerate();
        
        return $this->authenticated(Auth::user()) ?? redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Logout
     * 
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user();

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->loggedOut($user) ?? redirect()->route('login');
    }

    /**
     * Register
     * 
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $user = $this->createUser(request());

        if ($user && $user->id) {
            if ($user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
            }

            Auth::login($user);
            Cookie::expire('_ref');

            return $this->registered($user) ?? redirect()->route('register.completed');
        }
    }

    /**
     * Resend email verification
     * 
     * @return \Illuminate\Http\Response
     */
    public function resendEmailVerification()
    {
        if (request()->user() instanceof MustVerifyEmail && !request()->user()->hasVerifiedEmail()) {
            request()->user()->sendEmailVerificationNotification();

            return $this->verificationLinkSent(request()->user()) 
                ?? back()->with('flash', __('auth.verification-sent'));
        }
    }

    /**
     * Verify email
     * 
     * @return \Illuminate\Http\Response
    */
    public function verifyEmail()
    {
        if (request()->user() instanceof MustVerifyEmail && !request()->user()->hasVerifiedEmail()) {
            request()->user()->markEmailAsVerified();

            return $this->emailVerified(request()->user())
                ?? back()->with('flash', __('auth.verified') . '::success');
        }

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Send password reset link
     * 
     * @return \Illuminate\Http\Response
     */
    public function sendPasswordResetLink()
    {
        $status = Password::sendResetLink(request()->only('email'));

        return $status === Password::RESET_LINK_SENT
                ? redirect()->route('login')->with(['flash' => __($status)])
                : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Reset password
     * 
     * @param AuthResetPasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(AuthResetPasswordRequest $request)
    {
        $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'email_verified_at' => Carbon::now(),
                ])->setRememberToken(Str::random(60));
    
                $user->saveQuietly();
            }
        );
    
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('flash', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower(request()->input('email')).'|'.request()->ip();
    }
}