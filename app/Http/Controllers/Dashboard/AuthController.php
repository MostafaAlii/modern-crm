<?php
namespace App\Http\Controllers\Dashboard;
use App\Services\Auth\{AuthService,AdminAuthStrategy,ClientAuthStrategy, GuardResolver};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class AuthController extends Controller {
    public function __construct(
        protected AuthService $authService,
        protected GuardResolver $guardResolver
    ) {}

    public function showLoginForm(Request $request) {
        $guard = $this->guardResolver->resolve($request);
        $title = match ($guard) {
            'admin'  => 'Admin Login',
            'client' => 'Client Login',
            default  => 'Login',
        };
        return view('dashboard.auth.login', compact('title', 'guard'));
    }

    public function login(Request $request) {
        $guard = $this->guardResolver->resolve($request);
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($guard, $credentials);
        if ($result->success) {
            $request->session()->regenerate();
            return redirect()->route($guard . '.dashboard');
        }
        return back()->withErrors([
            'email' => $result->reason,
        ]);
    }

    public function logout(Request $request) {
        $guard = $this->guardResolver->resolve($request);
        $this->authService->logout($guard);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($guard . '.login');
    }
}
