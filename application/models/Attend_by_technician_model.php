<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 07/11/2017
 * Time: 10:29
 */

class Attend_by_technician_model extends CRM_Model
{

    public function get_ticket_status(){
        return $this->db
            ->get('tblticketstatus')->result_array();
    }

    public function get_tickets($staffid, $from, $to, $status)
    {
        $where_array = array(
            'staffid' => $staffid,
            'datetime >=' => $from,
            'datetime <=' => $to,
        );
        if($status != ''){
            $where_array['statusid'] = $status;
        }
        return $this->db
            ->where($where_array)
            ->order_by('ticketid ASC, datetime ASC')
            ->get('tblticketstimestatus')->result_array();
    }

}