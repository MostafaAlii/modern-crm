<?php
namespace App\Services\Auth;
use Illuminate\Http\Request;
class GuardResolver {
    public function availableGuards(): array {
        return get_guard();
    }

    public function resolveFromSegments(array $segments): string {
        $guards = $this->availableGuards();
        foreach ($segments as $segment) {
            if (in_array($segment, $guards, true)) {
                return $segment;
            }
        }
        return $guards[0] ?? 'web';
    }

    public function resolve(Request $request): string {
        return $this->resolveFromSegments(
            $request->segments()
        );
    }
}
