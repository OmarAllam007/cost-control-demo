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

function roundup($number, $precision)
{
    $multiplier = pow(10, $precision);
    $convert = $number * $multiplier;
    return ceil($convert) / $multiplier;
}