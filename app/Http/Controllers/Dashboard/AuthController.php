<?php
namespace App\Http\Controllers\Dashboard;
use App\Services\Auth\{AuthService, GuardResolver};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller {
    public function __construct(
        protected AuthService $authService,
        protected GuardResolver $guardResolver
    ) {}

    public function showLoginForm(Request $request) {
        $authContext = $this->guardResolver->resolve($request);
        $guard = $authContext['guard'];
        $title = match ($authContext['base']) {
            'admin'  => 'Admin Login',
            'client' => 'Client Login',
            default  => 'Login',
        };
        return view('dashboard.auth.login', compact('title', 'guard'));
    }

    public function login(Request $request) {
        $authContext = $this->guardResolver->resolve($request);
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($authContext, $credentials);
        if ($result->success) {
            $request->session()->regenerate();
            return redirect()->route($authContext['guard'] . '.dashboard');
        }
        return back()->withErrors([
            'email' => $result->reason,
        ]);
    }

    public function logout(Request $request) {
        $authContext = $this->guardResolver->resolve($request);
        $this->authService->logout($authContext);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($authContext['guard'] . '.login');
    }
}
