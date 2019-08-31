<?php


function ciroute($name, $arguments = [])
{
    static $routes;
    static $regex = "/\(:([A-Za-z0-9_.-]*)\)/";


    if (!$routes) $routes = include(__DIR__ . "/../cache/ciroute_names.php");

    if (!isset($routes[$name])) {
        throw  new \Exception("Não existe o nome($name) dessa rota");
    }
    if (!is_array($arguments)) {
        throw  new \Exception("O segundo argumento tem que ser um array");
    }

    $count = 0;
    $route = preg_replace_callback($regex, function () use (&$count, $arguments) {

        return ($arguments[$count++]);

    }, $routes[$name]);


    return base_url($route);
}