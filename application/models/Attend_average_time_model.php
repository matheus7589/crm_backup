<?php

use App\Enums\TicketStatusEnum;

/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 08/11/2017
 * Time: 08:45
 */


class Attend_average_time_model extends CRM_Model
{

    public function get_technicians($staffid = '')
    {
        if(!empty($staffid))
            $this->db->where('staffid', $staffid);

        return $this->db
            ->where(array(
                'active' => 1,
                'role!=' => 5,
                'admin!=' => 1
            ))
            ->get('tblstaff')->result_array();

    }

    public function get_tickets($staffid = '', $from, $to, $ticketid = '', $where = array())
    {
        if(!empty($ticketid)) {
            $this->db->where('ticketid', $ticketid);
        }

        if(!empty($staffid)) {
            $this->db->where('staffid', $staffid);
        }

        return $this->db
            ->distinct()
//            ->where(" datetime > '" .  $from . "' AND datetime < '" . $to . "' ")
            ->where($where)
//            ->group_by('ticketid')
            ->order_by('datetime ASC')
            ->get('tblticketstimestatus')->result_array();
    }

    public function get_list_tickets($where = array()){ // faz uma lista dos ticket q passaram pelo status informado
        $this->db->select('DISTINCT(ticketid)');
        $this->db->where($where);
        $this->db->group_by('ticketid, id');
        return $this->db->get('tblticketstimestatus')->result_array();
    }

    /**
     * @param string $from
     * @param string $to
     * @param null $status
     * @return array
     */
    public function get_average_await_time($from = '', $to = '', $status = null){

        if ($from != '' && $to != ''){
            $where = " tbltickets.date BETWEEN '" . to_sql_date($from, true) . "' AND '" . to_sql_date($to, true) . "' " ;
        }else{
            $where = " tbltickets.date BETWEEN '" . \Carbon\Carbon::today()->subDays(30)->format('Y-m-d 00:00:00.000000') . "' AND
             '" . \Carbon\Carbon::today()->format('Y-m-d 23:59:59.000000') . "' ";
        }

        $timestatus = $this->db
            ->select('tblticketstimestatus.*, tbltickets.ticketid, tbltickets.date')
            ->join('tbltickets', 'tbltickets.ticketid = tblticketstimestatus.ticketid', 'INNER')
            ->where('tbltickets.status', TicketStatusEnum::CLOSED)
            ->where($where)
            ->group_by('tblticketstimestatus.ticketid, id')
            ->order_by('tblticketstimestatus.ticketid ASC, tblticketstimestatus.datetime ASC')
            ->get('tblticketstimestatus')->result_array();


        $result = array(
            'ticketid' => 0,
            'data1' => 0,
            'diff_data' => 0,
            'status' => 0,
            'cont' => 0,
            'cont_tickets' => array(),
            'tempo_medio' => 0,
            'bad_tickets' => '',
            'bad_times' => '',
        );
        $diff = 0;
        $diffAux = 0;
        $bad_tickets = '';
        $bad_times = '';
        $continue_same_status = 0;
        $diff_data_continue_same_status = 0;
        $statusDate = null;

        $timestatusAux = array();

        // agrupa por ticketid
        foreach ($timestatus as $key => $item) {
            $timestatusAux[$item['ticketid']][$key] = $item;
        }

        ksort($timestatusAux, SORT_NUMERIC);
        // agrupa por ticketid



        foreach ($timestatusAux as $ticketid => $ticket) {
            $statusDate = null;
            $diffAux = 0;
            foreach ($ticket as $time) {

                if ($status == TicketStatusEnum::OPEN) {

//                    if ($time['statusid'] == TicketStatusEnum::OPEN) {
                    if (count($ticket) > 1) {
                        $auxArray = $ticket;
                        $auxArray = array_values($auxArray);
                        $statusDate = $auxArray[0]['datetime'];


                        if ($statusDate != null and $statusDate !== $time['datetime']) {
                            $diff = Carbon\Carbon::parse($time['datetime'])->diffFiltered(Carbon\CarbonInterval::minutes(), function (Carbon\Carbon $date) {
                                return !$date->isSunday();
                            }, Carbon\Carbon::parse($statusDate));

                            $diffAux += $diff;
                            break;
                        }
                    }

                } else {
                    if ($time['statusid'] == $status) {
                        if ($continue_same_status < 1) {
                            $statusDate = $time['datetime'];
                        }
                        $continue_same_status++;
                    } else {

                        if ($statusDate != null) {
//                            var_dump($statusDate, $time['datetime']);
                            $diff = \Carbon\Carbon::parse($time['datetime'])->diffFiltered(\Carbon\CarbonInterval::minutes(), function (\Carbon\Carbon $date) {
                                return !$date->isSunday();
                            }, \Carbon\Carbon::parse($statusDate));

                            $diffAux += $diff;

                            $statusDate = null;
                        }

                        $continue_same_status = 0;
                    }
                }

            }

            if ($diffAux > 0) {
                $result['diff_data'] += $diff;
                $result['cont_tickets'][] = $ticketid;

                if($diffAux > 30){
                    $bad_tickets .= $ticketid . ', ';
                    $bad_times .= $diffAux . ', ';
                }

            }

        }

        $result['bad_tickets'] = $bad_tickets;
        $result['bad_times'] = $bad_times;
        $result['cont_tickets'] = array_unique($result['cont_tickets']);

        return $result;

    }

    public function get_next_timestatus($ticketid, $date){
//        print_r($date);
        return $this->db
                ->where('ticketid', $ticketid)
                ->where("datetime > '" . $date . "' ")
                ->get('tblticketstimestatus')->first_row();
    }

}