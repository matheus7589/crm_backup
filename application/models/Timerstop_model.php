<?php

use Carbon\Carbon;

class Timerstop_model extends CRM_Model
{

    private $table = "tbsettimerstop";

    public function stop_all_timers()
    {
        // para todos os cronometros muda o estatus
        // das tarefas
        $timers = $this->get_all_tasks_timers();
        $note = 'Cronometro parado automaticamente';

        $this->db->trans_start();

        foreach ($timers as $timer) {
            $this->stop_task($timer->task_id);
            $this->stop_timers($timer->id, $note);
        }

        $tasks = $this->get_task_in_progress();
        foreach ($tasks as $task) {
            $this->stop_task($task->id);
        }

        return $this->db->trans_complete();
    }


    public function all()
    {

        $timers = $this->db->get($this->table)->result();
        $newTimers = [];
        if ($timers) {
            foreach ($timers as $key => $timer) {
                $timer->days = json_decode($timer->days, true);
                $newTimers[$key] = $timer;
            }
        }

        return $newTimers;
    }


    public function insert()
    {
        $this->lastset = Carbon::now("America/Sao_Paulo");

        $data["staffid"] = $this->staffid;
        $data["stop_timers"] = $this->stop_timers;
        $data["status"] = true;
        $data["days"] = $this->days;
        $data["data_insert"] = Carbon::now();


        return $this->db->insert($this->table, $data);
    }


    public function delete()
    {

        return $this->db->delete($this->table, [
            "settimerstopid" => $this->settimerstopid
        ]);
    }


    public function stop_task($task_id)
    {
        // colocar todas as tarefas em progresso para não iniciada
        $this->db->where('id', $task_id);

        return $this->db->update('tblstafftasks', ['status' => 1]);
    }

    public function stop_timers($timer_id, $note)
    {

        $this->db->where('id', $timer_id);
        $this->db->update('tbltaskstimers', array(
            'end_time' => time(),
            'note' => ($note != '' ? $note : null)
        ));

    }


    public function get_all_tasks_timers()
    {
        return $this->db->where(['end_time' => null])->get('tbltaskstimers')->result();
    }

    public function get_task_in_progress()
    {
        return $this->db->where(["status" => 4])->get('tblstafftasks')->result();
    }


    public function set_settimerstopid($settimerstopid)
    {

        $this->settimerstopid = $settimerstopid;
        return $this;
    }

    public function set_days(Array $days)
    {
        if (is_array($days)) {
            $this->days = json_encode($days);
        }
        return $this;
    }

    public function get_days()
    {
        if (!$this->days) {

            return [];
        }
        return json_decode($this->days);
    }

    public function set_stoptimers($stop_timers)
    {
        $this->stop_timers = $stop_timers;
        return $this;
    }

    public function set_staffid($staffid)
    {
        $this->staffid = $staffid;
        return $this;
    }


}

