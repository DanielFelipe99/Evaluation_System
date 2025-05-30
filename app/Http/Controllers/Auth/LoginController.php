<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        Log::info('Redirección según rol', ['user_id' => $user->id, 'role' => $user->getRoleNames()->first()]);
        return redirect()->route('dashboard');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->route('login')
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'auth.failed',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();  // Cerrar sesión
        $request->session()->invalidate();  // Invalidar la sesión
        $request->session()->regenerateToken();  // Regenerar el token CSRF

        // Redirigir siempre al login
        return redirect()->route('login');  // Redirige a la página de login
    }
}
