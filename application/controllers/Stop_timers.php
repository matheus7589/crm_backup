<?php
/**
 * Created by PhpStorm.
 * User: dejai
 * Date: 19/09/2017
 * Time: 11:46
 */

use Carbon\Carbon;


class Stop_timers extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('timerstop_model');
        $this->load->helper("json_response");
    }


    public function stop_all_timers()
    {

        $jsonResponse = ["mensagem" => 'Ainda não está na hora'];
        $timers = $this->timerstop_model->all();

        // se não existe timer mata a função
        if (!$timers) json_response($jsonResponse);


        foreach ($timers as $timer) {

            if ($this->validate($timer)) {
                $this->timerstop_model->stop_all_timers();
                $jsonResponse['mensagem'] = "Tarefas paradas com sucesso";
            }
        }

        json_response($jsonResponse);
    }


    public function validate($timer)
    {
        if (!$timer->days && !is_array($timer->days)) return false;

        $time = Carbon::parse($timer->stop_timers);

        $now = Carbon::parse(Carbon::now("America/Sao_Paulo"));
        // se o timer não é igual retorna false;


        if (($now->hour != $time->hour) || ($now->minute != $time->minute)) return false;


        foreach ($timer->days as $day) {

            if ($day == $now->dayOfWeek+1) return true;
        }

        return false;
    }

}