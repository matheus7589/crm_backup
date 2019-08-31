<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/10/2017
 * Time: 10:48
 */

class Load_report_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_staff(){
        return $this->db
            ->where('active = 1 and role = 5 and is_not_staff != 1')
            ->get('tblstaff')->result_array();
    }

    public function get_tasks(){
        $result = $this->db
        ->join('tblstafftasks', 'tblstafftaskassignees.taskid = tblstafftasks.id', 'INNER')
        ->where('status = 1')
        ->get('tblstafftaskassignees')->result_array();

        usort($result, array($this, "compare_priority"));
        return $result;
    }

    public function get_user_tasks_current($id)
    {
        $this->db->where('id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . $id . ')');
        $this->db->where('status =', 1);
        $this->db->order_by('duedate', 'asc');

        return $this->db->get('tblstafftasks')->result_array();
    }

    public function get_stafftaskassignees($taskid){
        return $this->db
            ->where('taskid', $taskid)
            ->get('tblstafftaskassignees')->result_array();
    }

    function compare_priority($a, $b) {
        return $a['priority'] < $b['priority'];
    }

}