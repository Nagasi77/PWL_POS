<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\Helper; 

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('ribuan', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });
    }

    public function register()
    {
        //
    }
}