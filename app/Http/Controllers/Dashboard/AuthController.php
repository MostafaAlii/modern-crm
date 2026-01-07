<?php
namespace App\Http\Controllers\Dashboard;
use App\Services\Auth\{AuthService,AdminAuthStrategy,ClientAuthStrategy};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
    protected ?AuthService $authService;
    public function __construct() {
        $this->authService = null;
    }

    public function showLoginForm(Request $request)
    {
        // نجيب الجزء اللي فيه guard
        // ممكن يكون segment 1 لو مفيش لغة، أو segment 2 لو فيه لغة
        $firstSegment = $request->segment(1);
        $secondSegment = $request->segment(2);

        // array بالـ guards المتاحة
        $guards = get_guard();

        if (in_array($firstSegment, $guards)) {
            $guard = $firstSegment;
        } elseif (in_array($secondSegment, $guards)) {
            $guard = $secondSegment;
        } else {
            $guard = 'client'; // default guard لو محددش
        }

        // title حسب الـ guard
        $title = match ($guard) {
            'admin' => 'Admin Login',
            'client' => 'Client Login',
            default => 'Login'
        };
        return view('dashboard.auth.login', compact('title', 'guard'));
    }


    /**
     * Admin Login
     */
    public function loginAdmin(Request $request) {
        $this->authService = new AuthService(new AdminAuthStrategy());
        $credentials = $request->only(['email', 'password']);
        if ($this->authService->login($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    /**
     * Client Login
     */
    public function loginClient(Request $request) {
        $this->authService = new AuthService(new ClientAuthStrategy());
        $credentials = $request->only(['email', 'password']);
        if ($this->authService->login($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('client.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    /**
     * Admin Logout
     */
    public function logoutAdmin() {
        $this->authService = new AuthService(new AdminAuthStrategy());
        $this->authService->logout();
        return redirect()->route('admin.login');
    }

    /**
     * Client Logout
     */
    public function logoutClient() {
        $this->authService = new AuthService(new ClientAuthStrategy());
        $this->authService->logout();
        return redirect()->route('client.login');
    }
}