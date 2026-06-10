<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        // ─────────────────────────────────────────
        //  Forzar entorno de testing en $_ENV y $_SERVER
        //  (necesario porque Docker setea DB_DATABASE como
        //   variable de entorno real, y EnvConstAdapter /
        //   ServerConstAdapter tienen prioridad sobre PutenvAdapter)
        // ─────────────────────────────────────────
        $_ENV['APP_ENV']       = 'testing';
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE']   = ':memory:';
        $_ENV['MAIL_MAILER']   = 'array';

        $_SERVER['APP_ENV']       = $_ENV['APP_ENV'];
        $_SERVER['DB_CONNECTION'] = $_ENV['DB_CONNECTION'];
        $_SERVER['DB_DATABASE']   = $_ENV['DB_DATABASE'];
        $_SERVER['MAIL_MAILER']   = $_ENV['MAIL_MAILER'];

        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
