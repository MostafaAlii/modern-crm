<?php

namespace App\Services\Auth;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
class GuardResolver {
    public function availableGuards(): array {
        return get_guard();
    }

    public function resolveFromSegments(array $segments): array {
        $guards = $this->availableGuards();
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        $segments = array_filter($segments, fn($seg) => !in_array($seg, $supportedLocales));
        $segments = array_values($segments);
        $context = 'web';
        $baseGuard = $guards[0] ?? 'web';
        $finalGuard = $baseGuard;
        if (!empty($segments)) {
            if ($segments[0] === 'api') {
                $context = 'api';
                $baseGuard = $segments[1] ?? $guards[0] ?? 'web';
            } else {
                $baseGuard = $segments[0];
            }
            if (!in_array($baseGuard, $guards, true)) {
                $baseGuard = $guards[0] ?? 'web';
            }
        }
        $finalGuard = $context === 'api' ? $baseGuard . '_api' : $baseGuard;
        return [
            'base'    => $baseGuard,
            'context' => $context,
            'guard'   => $finalGuard,
        ];
    }

    /**
     * Resolve from Request
     */
    public function resolve(Request $request): array
    {
        return $this->resolveFromSegments($request->segments());
    }
}
