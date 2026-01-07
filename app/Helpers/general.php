<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('admin_guard')) {
    function admin_guard()
    {
        return auth('admin');
    }
}


if (!function_exists('check_guard')) {
    function check_guard()
    {
        $guards = ['admin', 'client'];
        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                return $guard;
            }
        }
        return null;
    }
}

if (!function_exists('get_guard')) {
    function get_guard(): array {
        return collect(config('auth.guards'))
            ->keys()
            ->reject(fn($guard) => $guard === 'web')
            ->values()
            ->toArray();
        //return array_keys(config('auth.guards'));
    }
}


if (!function_exists('get_user_data')) {
    function get_user_data()
    {
        $guards = ['admin', 'client'];
        foreach ($guards as $guard) {
            if (auth($guard)->check())
                return auth($guard)->user();
        }
        return null;
    }
}

if (!function_exists('guard_dashboard_route')) {
    function guard_dashboard_route()
    {
        return match (check_guard()) {
            'admin' => route('admin.dashboard'),
            default => url('/'),
        };
    }
}
if (!function_exists('is_active')) {
    /**
     * Check if current route is active
     *
     * @param string|array $routes
     * @param string $output
     * @return string
     */
    function is_active($routes, $output = "active")
    {
        if (is_array($routes)) {
            return in_array(Route::currentRouteName(), $routes) ? $output : '';
        }
        return Route::currentRouteName() === $routes ? $output : '';
    }
}

if (!function_exists('is_open')) {
    /**
     * Check if dropdown should be open
     *
     * @param array $routes
     * @param string $output
     * @return string
     */
    function is_open($routes, $output = "nav-provoke")
    {
        return in_array(Route::currentRouteName(), $routes) ? $output : '';
    }
}

if (!function_exists('is_tree_open')) {
    /**
     * Check if dropdown (including nested) should be open
     *
     * @param array $routes
     * @param string $output
     * @return string
     */
    function is_tree_open($routes, $output = "nav-provoke")
    {
        $current = Route::currentRouteName();

        foreach ($routes as $route) {
            // لو array جوا array (nested)
            if (is_array($route) && in_array($current, $route)) {
                return $output;
            }

            if ($current === $route) {
                return $output;
            }
        }
        return '';
    }


    if (!function_exists('chevron_direction')) {
        function chevron_direction()
        {
            return app()->getLocale() === 'ar' ? 'chevron-left' : 'chevron-right';
        }
    }
}