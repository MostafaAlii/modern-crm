<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Auth\TokenService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Concerns\ApiResponseTrait;
class TokenController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TokenService $tokenService
    ) {
        // يمكنك إضافة middleware هنا إذا احتجت
        // $this->middleware('auth:admin_api,client_api')->only(['refresh', 'logoutAll', 'listDevices']);
    }

    /**
     * Refresh access token using refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        try {
            $result = $this->tokenService->refreshAccessToken(
                $request->input('refresh_token')
            );

            return $this->successResponse([
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => 'bearer',
                'expires_at' => $result['expires_at']->toIso8601String(),
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'type' => get_class($result['user']) === 'App\Models\Admin' ? 'admin' : 'client',
                ],
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                401
            );
        }
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAllDevices(Request $request): JsonResponse
    {
        try {
            // الحصول على المستخدم الحالي
            $user = JWTAuth::user();

            if (!$user) {
                return $this->errorResponse('User not authenticated', 401);
            }

            // سحب كل الـ tokens
            $count = $this->tokenService->revokeAllUserTokens(
                $user,
                $user->id,
                'user_logout_all'
            );

            // إبطال الـ access token الحالي
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->successResponse([
                'revoked_tokens' => $count,
            ], 'Logged out from all devices successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to logout from all devices',
                500
            );
        }
    }

    /**
     * List all active devices/sessions
     */
    public function listDevices(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $sessions = $this->tokenService->getUserActiveSessions($user);

            // الحصول على الـ token الحالي لتمييز الجلسة الحالية
            $currentToken = JWTAuth::getToken();
            $currentTokenId = null;

            if ($currentToken) {
                try {
                    $payload = JWTAuth::getPayload($currentToken);
                    $currentTokenId = $payload->get('jti');
                } catch (\Exception $e) {
                    // تجاهل الخطأ
                }
            }

            return $this->successResponse([
                'sessions' => $sessions,
                'current_token_id' => $currentTokenId,
                'total_active_sessions' => count($sessions),
            ], 'Active sessions retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve sessions',
                500
            );
        }
    }

    /**
     * Revoke a specific session/device
     */
    public function revokeSession(Request $request): JsonResponse
    {
        $request->validate([
            'token_id' => 'required|string',
        ]);

        try {
            $user = JWTAuth::user();

            if (!$user) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $tokenId = $request->input('token_id');

            // التحقق إذا كان الـ token يخص المستخدم الحالي
            $token = $user->refreshTokens()
                ->where('token_id', $tokenId)
                ->first();

            if (!$token) {
                return $this->errorResponse('Token not found', 404);
            }

            // سحب الـ token
            $success = $this->tokenService->revokeRefreshToken(
                $tokenId,
                $user->id,
                'user_revoked_session'
            );

            if (!$success) {
                return $this->errorResponse('Failed to revoke session', 500);
            }

            // إذا كان الـ token المسحوب هو الـ token الحالي، إبطال الـ access token أيضاً
            if ($token->access_token_id) {
                try {
                    // البحث عن الـ access token وسحبه
                    // يمكنك إضافة blacklist هنا إذا أردت
                } catch (\Exception $e) {
                    // تجاهل الخطأ
                }
            }

            return $this->successResponse([
                'token_id' => $tokenId,
            ], 'Session revoked successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to revoke session',
                500
            );
        }
    }

    /**
     * Get token statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $stats = $this->tokenService->getStatistics($user);

            return $this->successResponse([
                'statistics' => $stats,
            ], 'Token statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve statistics',
                500
            );
        }
    }
}