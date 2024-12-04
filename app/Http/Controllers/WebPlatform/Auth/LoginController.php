<?php

namespace App\Http\Controllers\WebPlatform\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Get the path the user should be redirected to after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return route('iclub.user.page');
        } elseif ($user->hasRole('club_manager')) {
            return route('iclub.event.page');
        } elseif ($user->hasRole('user')) {
            return route('iclub.event.page');
        }

        return '/iclub/dashboard'; // Default fallback
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            /** @var \App\Models\User */
            $user = auth()->user();

            // Redirect based on the user's role
            if ($user->hasRole('admin')) {
                return redirect()->route('iclub.user.page');
            } elseif ($user->hasRole('club_manager')) {
                return redirect()->route('iclub.event.page');
            } elseif ($user->hasRole('user')) {
                return redirect()->route('iclub.event.page');
            }
        }

        return view('webplatform.auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Handle a failed login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => ['Incorrect username or password. Type the correct username and password, and try again.'],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('username', 'password');
    }
}
