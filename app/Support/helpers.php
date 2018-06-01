<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;

function flash($message, $type = 'danger')
{
    Session::flash('flash-message', $message);
    Session::flash('flash-type', $type);
}

function __($id, $parameters = [], $domain = 'messages', $locale = null)
{
    return trans($id, $parameters, $domain, $locale);
}

function slug($value='')
{
    return Illuminate\Support\Str::slug($value);
}

 function cannot($ability, $object = null)
 {
     return \Gate::denies($ability, $object);
 }

function can($ability, $object)
{
    return \Gate::allows($ability, $object);
}

function roundup($number, $precision)
{
    $multiplier = pow(10, $precision);
    $convert = $number * $multiplier;
    return ceil($convert) / $multiplier;
}

function csv_quote($str)
{
    return '"' . str_replace('"', '""', preg_replace('/\s+/', ' ', $str)) . '"';
}

function check_syntax($equation)
{
    $content = '<?php ' . $equation;
    $file = tempnam('/tmp/', 'eval_');
    file_put_contents($file, $content);
    $result = exec('php -l ' . $file);
    if (strpos(strtolower($result), 'no syntax errors')) {
        return true;
    }

    return false;
}

function optional($object)
{
    return new \App\Support\Optional($object);
}

if (! function_exists('report')) {
    /**
     * Report an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    function report($exception)
    {
        if ($exception instanceof Throwable &&
            ! $exception instanceof Exception) {
            $exception = new FatalThrowableError($exception);
        }
        app(ExceptionHandler::class)->report($exception);
    }
}

if (! function_exists('rescue')) {
    /**
     * Catch a potential exception and return a default value.
     *
     * @param  callable  $callback
     * @param  mixed  $rescue
     * @return mixed
     */
    function rescue(callable $callback, $rescue = null)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            report($e);
            return value($rescue);
        }
    }
}

function translate_method($method)
{
    static $methods = [
        'POST' => 'created', 'PATCH' => 'updated', 'DELETE' => 'deleted'
    ];

    return $methods[$method] ?? 'updated';
}

/**
 * Removes unwanted characters from string. It is used for Excel added extra chars
 *
 * @param $code
 * @return string
 */
function code_trim($code)
{
    return preg_replace('/[^0-9a-z\.\-_]/i', '', $code);
}