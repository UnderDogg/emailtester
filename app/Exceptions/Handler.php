<?php

namespace App\Exceptions;

// controller
use App\Http\Controllers\Common\PhpMailController;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. php mailer.
     *
     * @return void
     */
    // public function __construct(PhpMailController $PhpMailController)
    // {
    //     $this->PhpMailController = $PhpMailController;
    // }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
            return $this->renderExceptionWithWhoops($e);
        return parent::render($request, $e);
    }

    /**
     * function to generate oops error page.
     *
     * @param \Exception $e
     *
     * @return \Illuminate\Http\Response
     */
    protected function renderExceptionWithWhoops(Exception $e)
    {
        // new instance of whoops class to display customized error page
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

        return new \Illuminate\Http\Response(
                $whoops->handleException($e), $e->getStatusCode(), $e->getHeaders()
        );
    }
}
