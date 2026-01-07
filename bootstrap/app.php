<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions,Middleware};
use Illuminate\Support\Facades\{Route, File};
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            $excludedFiles = ['console.php',];
            collect(File::allFiles(base_path('routes')))
                ->filter(fn($file) => $file->getExtension() === 'php')
                ->reject(fn($file) => in_array($file->getFilename(), $excludedFiles))
                ->each(fn($file) => Route::middleware('web')->group($file->getPathname()));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            $segments = $request->segments();
            $guards = get_guard();
            $guard = collect($segments)->first(fn($segment) => in_array($segment, $guards));
            return match ($guard) {
                'admin'  => route('admin.login'),
                'client' => route('client.login'),
                default  => route('client.login'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();