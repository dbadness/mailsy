<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

// for SendinBlue
use \Sendinblue\Mailin as Mailin;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
            return response()->view('errors.404', [], 404);
        }

        // Custom error 500 view on production
        if (env('DEPLOYMENT_STATUS') == 'production') {
            if($e instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {

                // send dave an email
                $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
                $data = array( 
                    "to" => array("dave@mailsy.co"=>"David Baines"),
                    "from" => array('dave@mailsy.co','Mailsy'),
                    "subject" => '500 Error',
                    "html" => '<pre>'.$e.'</pre>'
                );
                
                $mailin->send_email($data);

                // show the custom error page
                return response()->view('errors.500', [], 500);
            }
        }
        else
        {
            return parent::render($request, $e);  
        }
    }
}
