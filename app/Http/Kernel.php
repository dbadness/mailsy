<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
/*
use \Sendinblue\Mailin as Mailin;
use App\User;
use App\Email;
use App\Message;
*/

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        date_default_timezone_get('EST');
        $schedule->call(function () {

            $to = 'andrew@lucolo.com';
            $subject = 'Cron Job Running';
            $body = 'You better go catch it!';

            Utils::sendEmail($to,$subject,$body);

        })->everyFiveMinutes();
    }

}
