<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento3
 * Date: 17/11/2018
 * Time: 09:16
 */

class Attendance_type_model extends CRM_Model
{

    public function get_services()
    {
        return $this->db->get('tblservices')->result();
    }

    public function get_second_services($serviceid)
    {
        return $this->db
            ->where('serviceid', $serviceid)
            ->get('tblsecondservice')
            ->result();
    }

    public function get_first_service_name($id)
    {
        return $this->db
            ->where('serviceid', $id)
            ->get('tblservices')
            ->result()[0]->name;
    }

    public function get_second_service_name($id)
    {
        return $this->db
            ->where('secondServiceid', $id)
            ->get('tblsecondservice')
            ->result()[0]->name;
    }

    public function getAtendimentos($date_from, $date_to)
    {
        $atendimentos = $this->db->query("SELECT count(*) as total, MAX(tblcustomfieldsvalues.id) as customfieldsid
        from tbltickets left join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid
        and  tblcustomfieldsvalues.value <> '' and tblcustomfieldsvalues.fieldid = 17 WHERE tbltickets.status in (3, 5)" .
        ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
        )->row();
//        $atendimentos = $this->db->query("SELECT count(*) as total, MAX(tblcustomfieldsvalues.id) as customfieldsid from tblcustomfieldsvalues " .
//            " join tbltickets on tbltickets.ticketid = tblcustomfieldsvalues.relid and tblcustomfieldsvalues.value <> '' " .
//            " and tbltickets.status in (3, 5) " .
//            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
//            . " WHERE tblcustomfieldsvalues.fieldid = 17")->row();

        return $atendimentos;
    }

    public function getSecondServiceAttends($name, $date_from, $date_to, $serviceid)
    {
        $atendimentos = $this->db->query("SELECT tbltickets.servicenv2 as servicenv2, count(tbltickets.servicenv2) as total, count(*) as full_total, MAX(tblcustomfieldsvalues.id) as customfieldsid from tblcustomfieldsvalues " .
            " join tbltickets on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value = '" . $name . "' AND tbltickets.service <> 0 " .
            " and tbltickets.status in (3, 5) " .
            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
            . " WHERE tbltickets.service = " . $serviceid . " GROUP BY tbltickets.servicenv2 ")->result();

        return $atendimentos;
    }

    public function getTicketsFromServices($name, $date_from, $date_to)
    {
        if (empty($name)) {
            $tickets = $this->db->query("SELECT *, tbltickets.userid as ticketuserid from  tbltickets" .
            " LEFT JOIN tblticketstatus ON tbltickets.status = tblticketstatus.ticketstatusid 
             LEFT JOIN tblcontacts ON tbltickets.contactid = tblcontacts.id 
             LEFT JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid"
                . " WHERE tbltickets.status in (3, 5) " .
                " AND tbltickets.ticketid not in (SELECT tblcustomfieldsvalues.relid from tblcustomfieldsvalues WHERE tblcustomfieldsvalues.value <> '' AND tblcustomfieldsvalues.fieldid = 17 ) " .
                ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
                . " AND tbltickets.service <> 0 GROUP BY tbltickets.ticketid ")->result();
        } else {
            $tickets = $this->db->query("SELECT *, tbltickets.userid as ticketuserid, MAX(tblcustomfieldsvalues.id) as customfieldsid from  tbltickets" .
                " join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value = '" . $name . "' AND tbltickets.service <> 0 " .
                "LEFT JOIN tblticketstatus ON tbltickets.status = tblticketstatus.ticketstatusid 
             LEFT JOIN tblcontacts ON tbltickets.contactid = tblcontacts.id 
             LEFT JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid"
                . " WHERE tblcustomfieldsvalues.fieldid = 17 AND tblcustomfieldsvalues.value = '" . $name . "' " .
                " and tbltickets.status in (3, 5) " .
                ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
                . " AND tbltickets.service <> 0 GROUP BY tbltickets.ticketid ")->result();
        }

        return $tickets;
    }

    public function getTicketsFromServicesnv1($name, $date_from, $date_to, $serviceid)
    {
        $tickets = $this->db->query("SELECT *, tbltickets.userid as ticketuserid, MAX(tblcustomfieldsvalues.id) as customfieldsid from  tbltickets" .
            " join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value = '" . $name . "' AND tbltickets.service <> 0 " .
            "LEFT JOIN tblticketstatus ON tbltickets.status = tblticketstatus.ticketstatusid 
             LEFT JOIN tblcontacts ON tbltickets.contactid = tblcontacts.id 
             LEFT JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid"
            . " WHERE tblcustomfieldsvalues.fieldid = 17 AND tblcustomfieldsvalues.value = '" . $name . "' " .
            " and tbltickets.status in (3, 5) " .
            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
            . " AND tbltickets.service = " . $serviceid . " GROUP BY tbltickets.ticketid ")->result();

        return $tickets;
    }

    public function getTicketsFromServicesnv2($name, $date_from, $date_to, $serviceid, $servicenv2)
    {
        $tickets = $this->db->query("SELECT *, tbltickets.userid as ticketuserid, MAX(tblcustomfieldsvalues.id) as customfieldsid from  tbltickets" .
            " join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value = '" . $name . "' AND tbltickets.service <> 0 " .
            "LEFT JOIN tblticketstatus ON tbltickets.status = tblticketstatus.ticketstatusid 
             LEFT JOIN tblcontacts ON tbltickets.contactid = tblcontacts.id 
             LEFT JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid"
            . " WHERE tblcustomfieldsvalues.fieldid = 17 AND tblcustomfieldsvalues.value = '" . $name . "' " .
            " and tbltickets.status in (3, 5) " .
            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
            . " AND tbltickets.service = " . $serviceid . " AND tbltickets.servicenv2 = " . $servicenv2 . "  GROUP BY tbltickets.ticketid ")->result();

        return $tickets;
    }



}