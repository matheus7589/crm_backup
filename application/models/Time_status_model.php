<?php

class Time_status_model extends CRM_Model
{

    private $table = "tblticketstimestatus";

    public function __construct()
    {
        parent::__construct();
    }

    public function add($ticketid, $status, $assigned){
        $data['datetime'] = date('Y-m-d H:i:s');
        $data['statusid'] = $status;
        $data['ticketid'] = $ticketid;
        $data['staffid'] = $assigned;
        $insert_id = $this->db->insert($this->table, $data);
        if($insert_id){
            return true;
        }
        return false;
    }

}