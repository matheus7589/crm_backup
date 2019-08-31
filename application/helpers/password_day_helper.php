<?php


function password_day()
{
    if (PAINEL == QUANTUM) {
        $now = \Carbon\Carbon::now();
        $day_manth = "$now->day";
        if($now->month < 10)
            $day_manth .= "0$now->month";
        else
            $day_manth .= $now->month;
        $day_manth += intval($now->year);
        return "$day_manth$now->year";
    }

	$string = date('d/m/Y');

	$string = substr($string, 3,2) . substr($string, 0,2) . substr($string, 6,4);
	 

    return strtoupper(substr(md5($string), 0, 8));


}
