<?php

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

 function cannot($ability, $object)
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