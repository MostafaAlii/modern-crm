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
                ->each(function ($file) {
                    if ($file->getFilename() === 'api.php') {
                        Route::prefix('api')->middleware('api')->group($file->getPathname());
                    } else {
                        Route::middleware('web')->group($file->getPathname());
                    }
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            $resolver = app(\App\Services\Auth\GuardResolver::class);
            $guard = $resolver->resolveFromSegments(
                $request->segments()
            );
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