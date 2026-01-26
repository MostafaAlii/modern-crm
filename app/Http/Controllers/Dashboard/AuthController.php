<?php
namespace App\Http\Controllers\Dashboard;
use App\Services\Auth\{AuthService, GuardResolver};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponseTrait;
class AuthController extends Controller {
    use ApiResponseTrait;
    public function __construct(
        protected AuthService $authService,
        protected GuardResolver $guardResolver,
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
        if (!$result->success) {
            if ($authContext['context'] === 'web') {
                return back()->withErrors(['email' => $result->reason]);
            } else {
                return $this->errorResponse(
                    message: $result->reason,
                    status: 401
                );
            }
        }
        if ($authContext['context'] === 'web') {
            $request->session()->regenerate();
            return redirect()->route($authContext['guard'] . '.dashboard');
        }
        /*if ($authContext['context'] === 'api') {
            return $this->successResponse(
                data: [
                    'user'  => $result->user,
                    'token' => $result->token,
                ],
                message: 'Login successful',
                meta: [
                    'expires_at' => $result->expires_at,
                ]
            );
        }*/
        if ($authContext['context'] === 'api') {
            $responseData = [
                'user'  => $result->user,
                'token' => $result->token,
            ];
            if ($result->refresh_token) {
                $responseData['refresh_token'] = $result->refresh_token;
            }
            return $this->successResponse(
                data: $responseData,
                message: 'Login successful',
                meta: [
                    'expires_at' => $result->expires_at,
                ]
            );
        }
    }

    public function logout(Request $request) {
        $authContext = $this->guardResolver->resolve($request);
        $this->authService->logout($authContext);
        if ($authContext['context'] === 'web') {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route($authContext['guard'] . '.login');
        }
        // API response
        return $this->successResponse(
            message: 'Logged out successfully'
        );
    }
}