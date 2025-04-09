<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for older MySQL versions
        Schema::defaultStringLength(191);
        
        // Keep the existing password reset URL customization
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        
        // Force HTTPS for all URLs in production
        if (config('app.env') === 'production' || request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }
        
        // Add a custom Blade directive for the logo
        Blade::directive('logo', function () {
            return "<?php echo '<img src=\"' . url('/logo') . '?v=' . time() . '\" alt=\"Massar Logo\" onerror=\"this.onerror=null; this.src=\'' . url('/inline-logo') . '\'\">'; ?>";
        });
    }
}

