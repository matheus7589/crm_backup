<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 28/12/2017
 * Time: 08:45
 */

class Charts extends Admin_controller
{
    public function index()
    {
        $this->load->view("admin/reports/charts");
    }

    public function get_data()
    {
        if($this->input->post()) {
            $data = $this->input->post();
            if (in_array($data["type"], array("tickets-mes","tickets-dia","tickets-hora")))
            {
                if ($data["type"] == "tickets-mes") {
                    $name = "Número de atendimentos por mes";
                    if(PAINEL == INORTE) {
                        $start = Carbon\Carbon::today();
                        $last = $start->copy()->startOfYear();
                        $diff = $start->diffInDays($last);
                        $sql = "SELECT DISTINCT EXTRACT(MONTH FROM date) as data, MAX(UNIX_TIMESTAMP(LAST_DAY(date))) as timestamp, COUNT(ticketid) as num FROM tbltickets
                                join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value <> ''
                                WHERE status in (3, 5) AND DATE_SUB(CURDATE(),INTERVAL " . $diff . " DAY) <= date GROUP BY data ORDER BY data";
                    }else{
                        $sql = "SELECT DISTINCT EXTRACT(MONTH FROM date) as data, MAX(UNIX_TIMESTAMP(LAST_DAY(date))) as timestamp, COUNT(ticketid) as num FROM tbltickets WHERE DATE_SUB(CURDATE(),INTERVAL 365 DAY) <= date GROUP BY data ORDER BY data";
                    }
                    $pformat = "{point.x:%m/%Y}";
                    $hformat = "<b>{point.y:.0f} Atendimentos</b><br>";
                }
                if ($data["type"] == "tickets-dia") {
                    $name = "Número de atendimentos por dia";
                    $sql = "SELECT DISTINCT DATE(date) as data, MAX(UNIX_TIMESTAMP(DATE(date))) as timestamp, COUNT(ticketid) as num FROM tbltickets WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= date GROUP BY data ORDER BY data";
                    $hformat = "<b>{point.x:%e/%m %A}</b><br>";
                    $pformat = "{point.y:.0f} Atendimentos";
                }
                if ($data["type"] == "tickets-hora") {
                    $name = "Número de atendimentos por hora";
                    //Pode ser que der problema por causa do fuso horário
                    $sql = "SELECT DISTINCT HOUR(date) as hour, MAX(UNIX_TIMESTAMP(concat('2015-12-12 ',HOUR(date)-3,':00'))) as timestamp, COUNT(ticketid) as numm, ROUND(((SELECT COUNT(ticketid) FROM tbltickets WHERE HOUR(date) = hour)/(SELECT COUNT(ticketid) FROM tbltickets))*100,2) as num FROM `tbltickets` GROUP BY hour ORDER BY hour";
                    $hformat = "<b>{point.x:%H:00}</b><br>";
                    $pformat = "{point.y:.2f}% dos atendimentos";
                    $subtitle = "";
                    $title = "% de atendimentos por horário";
                }
                $atendimentos = $this->db->query($sql)->result_array();
                $atte = array();
                $semifinal = array();
                $media = 0;
                foreach ($atendimentos as $att)
                {
                    array_push($atte, array(intval($att['timestamp'])*1000, floatval($att['num'])));
                    $media += intval($att['num']);
                }
                if (count($atendimentos) <= 0) {
                    $media = $media;
                } else {
                    $media = $media / count($atendimentos);
                }
                array_push($semifinal, array("name" => $name,"data" => $atte));
                $final = array(
                    "dados" => $semifinal,
                    "type"=>$type??"spline",
                    "tooltips"=>array(
                        "headerFormat"=>$hformat,
                        "pointFormat"=>$pformat),
                    "title"=>$title??"Número de atendimentos",
                    "subtitle"=>$subtitle??"Média de ".round($media)." atendimentos",
                    "yAxisT"=>"Atendimentos",
                    "xAxis"=>$xaxis??array(
                        "type"=>"datetime",
                        "dateTimeLabelFormats"=>array(
                            "month" => "%e. %b","year"=>"%b"
                        ),
                        "title" => array(
                            "text"=>"Data"
                        )
                    )
                );
                echo json_encode($final);
            }
            if ($data["type"] == "tickets-status") {
                $statuses = $this->db->query("SELECT DISTINCT status FROM tbltickets")->result_array();
                $semifinal = array();
                $hformat = "<b>{point.x:%e/%m %A}</b><br>";
                $pformat = "{point.y:.0f} Atendimentos";
                foreach ($statuses as $status)
                {
                    $atendimentos = $this->db->query("SELECT DISTINCT DATE(datetime) as data, MAX(UNIX_TIMESTAMP(DATE(datetime))) as timestamp,(SELECT COUNT(ticketid) FROM tblticketstimestatus WHERE DATE(datetime) = data AND statusid = ".$status['status'].") as num FROM tblticketstimestatus WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= datetime AND statusid = ".$status['status'] . " GROUP BY data")->result_array();
                    $atte = array();
                    foreach ($atendimentos as $att) {
                        array_push($atte, array(intval($att['timestamp'])*1000, intval($att['num'])));
                    }
                    if($status["status"] == 3)
                        $bool = true;
                    else
                        $bool = false;
                    array_push($semifinal, array("name" => ticket_status_translate($status["status"]),"data" => $atte,"selected"=>"false","visible" => $bool));
                }
                $final = array(
                    "dados" => $semifinal,
                    "type"=>$type??"spline",
                    "tooltips"=>array(
                        "headerFormat"=>$hformat,
                        "pointFormat"=>$pformat
                    ),
                    "title"=>"Número de atendimentos",
                    "subtitle"=>"Atendimento dos ultimos 30 dias",
                    "yAxisT"=>"Atendimentos",
                    "xAxis"=>$xaxis??array(
                        "type"=>"datetime",
                        "dateTimeLabelFormats"=>array(
                            "month" => "%e. %b","year"=>"%b"
                        ),
                        "title" => array(
                            "text"=>"Data"
                        )
                    ));
                echo json_encode($final);
            }
            if ($data["type"] == "tickets-service") {
                $limit = "";
                if($data["all"] == "resumed")
                    $limit = "LIMIT 0,10";
                $services = $this->db->query("SELECT serviceid, name, (SELECT COUNT(ticketid) FROM tbltickets WHERE service = serviceid) as num FROM `tblservices` WHERE name != 'ND' AND (SELECT COUNT(ticketid) FROM tbltickets WHERE service = serviceid) > 0 ORDER BY num DESC ".$limit)->result_array();
                $series = array();
                $subseriesp = array();
                foreach ($services as $service)
                {
                    $subseries = array();
                    $services2 = $this->db->query("SELECT name,(SELECT COUNT(ticketid) FROM tbltickets WHERE servicenv2 = secondServiceid) as num FROM tblsecondservice WHERE (SELECT COUNT(ticketid) FROM tbltickets WHERE servicenv2 = secondServiceid) > 0 AND serviceid = ".$service["serviceid"]." ORDER BY num DESC")->result_array();
                    foreach ($services2 as $service2)
                    {
                        array_push($subseries,array($service2["name"],intval($service2["num"])));
                    }
                array_push($series,array("name"=>$service["name"],"y"=>intval($service["num"]),"drilldown"=>$service["serviceid"]));
                array_push($subseriesp,array("name"=>$service["name"],"id"=>$service["serviceid"],"data"=>$subseries));
                }
                echo json_encode(array("data"=>$series,"subitems"=>$subseriesp));
            }
        }
    }
}