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

    /*
    protected function schedule(Schedule $schedule)
    {
        date_default_timezone_get('EST');
        $schedule->call(function () {

            $totalUsers = User::all()->count();

            $body = 'Total Users: '.$totalUsers.'<br>';
            $body .= 'New Users Yesterday: '.$yesterdaysUsers.'<br>';
            $body .= 'New Users in the Past Week: '.$yesterdaysUsers.'<br>';
            $body .= 'New Users This Month: '.$monthlyUsers.'<br>';
            $body .= 'New Users Last Month: '.$yesterdaysUsers.'<br>';
            $body .= 'Total Messages: '.$totalMessages.'<br>';
            $body .= 'Total Templates: '.$totalTemplates.'<br>';
            $body .= 'Messages per User: '.$perUserMessages.'<br>';
            $body .= 'Messages per Template:: '.$perTemplateMessages.'<br>';
            // send a user report every day
            $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
            $data = array( 
                "to" => array("dave@mailsy.co"=>"David Baines"),
                "from" => array('dave@mailsy.co','Mailsy'),
                "subject" => 'User Report '.date('m/D/Y'),
                "html" => $body
            );
         
            $mailin->send_email($data);
        })->dailyAt('9:00');
    }
    */
}
