<?php


/**
 * @param array $array
 */
function json_response(Array $array)
{

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($array);
    exit();

}