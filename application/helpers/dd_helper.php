<?php


function dd($value)
{
    header_remove();
    echo "<pre>";

    var_dump($value);
    echo "<hr></pre>";
    die();

}