<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 18/10/2017
 * Time: 08:41
 */

class Attendance_report_model extends CRM_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_attends(){
        $result = $this->db
            ->where('')
            ->order_by('count(userid)')
            ->group_by('userid')
            ->get('tbltickets')->result_array();
    }

    public function get_client_from_contact($idcontact){
        return $this->db
//            ->select('clientid')
            ->where('id', $idcontact)
            ->limit('')
            ->get('tblclientscontacts')->row();
    }
}