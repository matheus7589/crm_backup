<?php

class Staff_online_model extends CRM_Model
{
    private $table = "tblstaffonline";
    const ONLINE = 1;
    const OFFLINE = 0;


    public function online($staff_id)
    {

        $this->offline($staff_id);

        $this->load->library('user_agent');

        $this->db->set('session_id', session_id());
        $this->db->set('is_online', self::ONLINE);
        $this->db->set('staffid', $staff_id);
        $this->db->set("browser", $this->agent->browser());
        $this->db->set("ip_address", $this->input->ip_address());
        $this->db->set("date_login", \Carbon\Carbon::now()->format("Y-m-d H:i:s"));

        return $this->db->insert($this->table);

    }

    public function offline($staffId)
    {

        $this->deadSessions($staffId);

        $this->db->set('is_online', self::OFFLINE);
        $this->db->set("date_logoff", \Carbon\Carbon::now()->format("Y-m-d H:i:s"));
        $this->db->where("is_online", self::ONLINE);
        $this->db->where("staffid", $staffId);

        return $this->db->update($this->table);

    }


    public function set_all_offline($staffId)
    {
        $this->deadSessions($staffId);

        $this->db->set('is_online', self::OFFLINE);
        $this->db->set("date_logoff", \Carbon\Carbon::now()->format("Y-m-d H:i:s"));
        $this->db->where("is_online", self::ONLINE);
        $this->db->where("staffid", $staffId);


        return $this->db->update($this->table);
    }


    public function deadSessions($staffId)
    {

        $sessions = $this->db->select("session_id")
            ->from($this->table)->where("is_online = ", self::ONLINE)
            ->where("staffid =", $staffId)->get()->result_array();

        if (!$sessions)
            return null;


        foreach ($sessions as $session) {
            if (isset($session->session_id))

                $this->db->where("id =", $session->session_id)->delete("tblsessions");

        }
    }

}