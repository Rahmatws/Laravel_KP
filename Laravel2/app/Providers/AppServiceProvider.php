<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

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
        try {
            if (config('database.default') === 'mysql') {
                $db = config('database.connections.mysql.database');
                $host = config('database.connections.mysql.host');
                $port = (string) config('database.connections.mysql.port');
                $user = config('database.connections.mysql.username');
                $pass = (string) config('database.connections.mysql.password', '');
                $dsn = "mysql:host={$host};port={$port}";
                $pdo = new \PDO($dsn, $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
            if (!Schema::hasTable('users') || !Schema::hasTable('settings')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
        }
    }
}
