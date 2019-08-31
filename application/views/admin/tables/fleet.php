<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 29/01/2018
 * Time: 14:57
 */

defined('BASEPATH') or exit('No direct script access allowed');
if($this->_instance->input->get('type') == 'report') {
    $aColumns = array(
        'idsaida',
        'data',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11'
    );
    $sIndexColumn = "idsaida";
    $sTable = 'tblfleetout';
    $join = array('INNER JOIN tblstaff ON tblstaff.staffid = tblfleetout.staffid', 'INNER JOIN tblfleetvehicles ON tblfleetvehicles.vehicleid = tblfleetout.vehicleid');
    $i = 0;
    $additionalSelect = array(
        'data',
        'motivo',
        'km_inicial',
        'km_final',
        'datetime_inicial',
        'datetime_final',
        'obs',
        'state',
        'rel_id',
        'rel_type',
        'local',
        'tblfleetvehicles.descricao as vehicle',
        'firstname',
        'lastname',
    );

    $where = array('AND state = 0');
    if ($this->_instance->input->get('staff'))
        array_push($where, ' AND tblfleetout.staffid = ' . $this->_instance->input->get('staff'));
    if ($this->_instance->input->get('vehicle'))
        array_push($where, ' AND tblfleetout.vehicleid = ' . $this->_instance->input->get('vehicle'));
    if ($this->_instance->input->get('rel_type'))
        array_push($where, ' AND tblfleetout.rel_type = "' . $this->_instance->input->get('rel_type') . '"');
    if ($this->_instance->input->get('rel_id'))
        array_push($where, ' AND tblfleetout.rel_id = "' . $this->_instance->input->get('rel_id') . '"');
    if ($this->_instance->input->get('date')) {
        if ($this->_instance->input->get('date_to') == "false")
            $date_to = Carbon\Carbon::now()->toDateString();
        else
            $date_to = to_sql_date($this->_instance->input->get('date_to'));
        if ($this->_instance->input->get('date_from') == "false")
            $date_from = '0000-00-00';
        else
            $date_from = to_sql_date($this->_instance->input->get('date_from'));

        array_push($where, ' AND data BETWEEN "' . $date_from . '" AND "' . $date_to . '"');
    }

    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect, '', '', 'ORDER BY tblfleetout.idsaida DESC');

    $output = $result['output'];
    $rResult = $result['rResult'];

    foreach ($rResult as $aRow) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = "";
            if ($aColumns[$i] == 'idsaida') {
                $_data = $aRow['idsaida'];
            } else if ($aColumns[$i] == 'data') {
                $_data = \Carbon\Carbon::parse($aRow['data'])->format('d/m/Y');
            } else if ($aColumns[$i] == '1') {
                $_data = $aRow['firstname'] . " " . $aRow['lastname'];
            } else if ($aColumns[$i] == '2') {
                if (!empty($aRow['rel_type']))
                    if(PAINEL == INORTE && $aRow['rel_type'] == 'vendas'){
                        //nada kek. Isso resolve o problema, pois quando e relacionado a vendas, nao existe relacao
                        $_data = "Sem registro*";
                    }else {
                        $value = get_relation_values(get_relation_data($aRow['rel_type'], $aRow['rel_id']), $aRow['rel_type'])["link"];
//                        $_data = "<a target='_blank' href='" . $value . "'>#" . $aRow['rel_id'] . "</a>";
                        if(!empty($value)){
                            $_data = "<a target='_blank' href='" . $value . "'>#" . $aRow['rel_id'] . "</a>";
                        }else{
                            $_data = "Sem registro*";
                        }
                    }
                else
                    $_data = "Sem registro*";
            } else if ($aColumns[$i] == '3') {
                $_data = '<div style="max-width: 200px;">';
                $_data .= $aRow['motivo'];
                $_data .= '</div>';
            } else if ($aColumns[$i] == '4') {
                if(!empty($aRow['local']))
                    $_data = $aRow['local'];
                else
                    $_data = "Não possui registro*";
            } else if ($aColumns[$i] == '5') {
                $_data = '<div style="max-width: 200px;">';
                $_data .= $aRow['vehicle'];
                $_data .= '</div>';

            } else if ($aColumns[$i] == '6') {
                $_data = $aRow['km_inicial'];
            } else if ($aColumns[$i] == '7') {
                $_data = $aRow['km_final'];
            } else if ($aColumns[$i] == '8') {
                if (\Carbon\Carbon::parse($aRow['datetime_inicial'])->day == \Carbon\Carbon::parse($aRow['datetime_final'])->day) {
                    $_data .= \Carbon\Carbon::parse($aRow['datetime_inicial'])->toTimeString();
                } else {
                    $_data .= \Carbon\Carbon::parse($aRow['datetime_inicial'])->format('d/m/Y H:i:s');
                }
            } else if ($aColumns[$i] == '9') {
                if (\Carbon\Carbon::parse($aRow['datetime_inicial'])->day == \Carbon\Carbon::parse($aRow['datetime_final'])->day) {
                    $_data .= \Carbon\Carbon::parse($aRow['datetime_final'])->toTimeString();
                } else {
                    $_data .= \Carbon\Carbon::parse($aRow['datetime_final'])->format('d/m/Y H:i:s');
                }
            } else if ($aColumns[$i] == '10') {
                $_data = $aRow['km_final'] - $aRow['km_inicial'] . " Km ";
            } else if ($aColumns[$i] == '11') {
                $uhourin = \Carbon\Carbon::parse($aRow['datetime_inicial'])->timestamp;
                $uhouren = \Carbon\Carbon::parse($aRow['datetime_final'])->timestamp;
                $time = $uhouren - $uhourin;
                $now = \Carbon\Carbon::now()->toDateString();
                $now = \Carbon\Carbon::createFromFormat('Y-m-d', $now)->timestamp;
                $tempo = \Carbon\Carbon::createFromTimeStampUTC($time + $now)->diffForHumans(null, true);
                $_data = $tempo;
            }

            $row[] = $_data;
        }
        $output['aaData'][] = $row;
    }
}
else if($this->_instance->input->get('type') == 'manager')
{
    $aColumns = array(
        'idsaida',
        'motivo',
        'descricao',
        'firstname',
        '1',
        '2',
        'data',
        '3'
    );
    $sIndexColumn = "idsaida";
    $sTable = 'tblfleetout';
    $join = array('INNER JOIN tblstaff ON tblstaff.staffid = tblfleetout.staffid', 'INNER JOIN tblfleetvehicles ON tblfleetvehicles.vehicleid = tblfleetout.vehicleid');
    $i = 0;
    $additionalSelect = array(
        'km_inicial',
        'km_final',
        'datetime_inicial',
        'datetime_final',
        'local',
        'lastname'
    );

    $where = array('AND state = 0');

    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect, '', '', 'ORDER BY tblfleetout.idsaida DESC');

    $output = $result['output'];
    $rResult = $result['rResult'];
    foreach ($rResult as $aRow) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            if ($aColumns[$i] == 'idsaida') {
                $_data = $aRow['idsaida'];
            }
            else if ($aColumns[$i] == 'motivo') {
                $_data = $aRow['motivo'];
            }
            else if ($aColumns[$i] == 'descricao') {
                $_data = $aRow['descricao'];
            }
            else if ($aColumns[$i] == 'firstname') {
                $_data = $aRow['firstname']." ".$aRow['lastname'];
            }
            else if ($aColumns[$i] == '1') {
                $_data = ($aRow['km_final'] - $aRow['km_inicial']) . " Km";
            }
            else if ($aColumns[$i] == '2') {
                $uhourin = \Carbon\Carbon::parse($aRow['datetime_inicial'])->timestamp;
                $uhouren = \Carbon\Carbon::parse($aRow['datetime_final'])->timestamp;
                $time = $uhouren - $uhourin;
                $now = \Carbon\Carbon::now()->toDateString();
                $now = \Carbon\Carbon::createFromFormat('Y-m-d', $now)->timestamp;
//                $tempo = \Carbon\Carbon::createFromTimeStampUTC($time + $now)->diffForHumans(null, true);
                $_data = \Carbon\Carbon::createFromTimeStampUTC($time + $now)->diffForHumans(null, true);
            }
            else if ($aColumns[$i] == 'data') {
                $_data = \Carbon\Carbon::parse($aRow['data'])->format('d/m/Y');
            }
            else if ($aColumns[$i] == '3') {
                $_data = '<a onclick="see_out('.$aRow['idsaida'].'); return false;" class="btn btn-default btn-icon">
                    <i class="fa fa-arrow-right"></i>
                </a>';
                if(has_permission('fleet','','edit'))
                    $_data .= '<a href="'.admin_url("fleet/get_out/".$aRow['idsaida']).'" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>';
                if(has_permission('fleet','','delete'))
                    $_data .= '<a onclick="delete_out('.$aRow['idsaida'].'); return false;" class="btn btn-danger btn-icon"><i class="fa fa-trash"></i></a>';
            }
            $row[] = $_data;
        }
        $output['aaData'][] = $row;
    }
}