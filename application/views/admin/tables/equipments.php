<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 05/02/2018
 * Time: 09:59
 */

    defined('BASEPATH') or exit('No direct script access allowed');
    $type = $this->_instance->input->get('type');
    if($type == 'in') {
        $aColumns = array(
            'description',
            'tipo',
            '1'
        );
//        SELECT description FROM `tblequipments_in` UNION SELECT descricao FROM tblpatrimony_bens
        $sIndexColumn = "equipinid";
        $sTable = 'tblequipments_in';
        $join = array();
        $additionalSelect = array();
        $where = array("AND flag_out = 0 AND status = 1");// UNION SELECT descricao FROM tblpatrimony_bens");

        array_push($additionalSelect, $sIndexColumn);

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == 'description') {
                    $_data = "#" . $aRow[$sIndexColumn] . " - " . $aRow['description'];
                } else if ($aColumns[$i] == 'motivo') {
                    $_data = $aRow['motivo'];
                } else if ($aColumns[$i] == 'tipo') {
                    $color = "";
                    $name = $aRow['tipo'];
                    if ($aRow['tipo'] == 'Patrimônio'){
                        $color = "#84c529";
                    } else if ($aRow['tipo'] == 'emprestimo') {
                        $color = "#ff6f00";
                        $name = "Emprestimo";
                    } else if ($aRow['tipo'] == 'locacao') {
                        $color = "#03a9f4";
                        $name = "Locação";
                    } else if ($aRow['tipo'] == 'manutencao') {
                        $color = "#777";
                        $name = "Manutenção";
                    }

                    $_data = '<span class="label inline-block" style="border:1px solid ' . $color . '; color:' . $color . ';">' . $name . '<i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" data-original-title="" title="" style="color: ' . $color . ';border: 1px dashed ' . $color . ';"></i></span>';
                } else if ($aColumns[$i] == '1') {
                    $_data = "";
                    if(has_permission('equipments','','edit'))
                        $_data = '<a onclick="in_to_return('.$aRow[$sIndexColumn].'); return false;" class="btn btn-default btn-icon"><i class="fa fa-arrow-left"></i></a>';
                }

                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
    }
    else if($type == 'out') {
        $aColumns = array(
            'description',
            '2',
            'tipo',
            '1'
        );
        $sIndexColumn = "equipoutid";
        $sTable = 'tblequipments_mov';
        $join = array();
        $additionalSelect = array('equipmentid','(SELECT tipo FROM tblequipments_in WHERE equipinid = equipmentid) as tipo_orig');
        $where = array();
        array_push($where,"AND status = 1");
//        array_push($where,"AND flag_in_or_out = 1");//0 = Entrou | 1 = Saiu

        array_push($additionalSelect, $sIndexColumn);

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = array();
            $color = "";
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == 'description') {
                    $_data = "#" . $aRow['equipmentid'] . " - " . $aRow['description'];
                } else if ($aColumns[$i] == 'motivo') {
                    $_data = $aRow['motivo'];
                } else if ($aColumns[$i] == '2') {
                    $name = $aRow['tipo_orig'];
                    $color = "#84c529";
                    if ($aRow['tipo_orig'] == 'emprestimo') {
                        $color = "#ff6f00";
                        $name = "Emprestimo";
                    } else if ($aRow['tipo_orig'] == 'manutencao') {
                        $color = "#777";
                        $name = "Manutenção";
                    }
                    $_data = '<span class="label inline-block" style="border:1px solid ' . $color . '; color:' . $color . ';">' . $name . '<i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" data-original-title="" title="" style="color: ' . $color . ';border: 1px dashed ' . $color . ';"></i></span>';
                } else if ($aColumns[$i] == 'tipo') {
                    $name = $aRow['tipo'];
                    if ($aRow['tipo'] == 'emprestimo') {
                        $color = "#ff6f00";
                        $name = "Emprestimo";
                    } else if ($aRow['tipo'] == 'manutencao') {
                        $color = "#777";
                        $name = "Manutenção";
                    }
                    $_data = '<span class="label inline-block" style="border:1px solid ' . $color . '; color:' . $color . ';">' . $name . '<i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" data-original-title="" title="" style="color: ' . $color . ';border: 1px dashed ' . $color . ';"></i></span>';
                } else if ($aColumns[$i] == '1') {
                    $_data = "";
                    if(has_permission('equipments','','view'))
                        $_data = '<a onclick="out_to_in('.$aRow['equipoutid'].','.$aRow['equipmentid'].'); return false;" class="btn btn-default btn-icon"><i class="fa fa-arrow-left"></i></a>';
                }

                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
    }
    else if($type == 'patrimony') {
        $aColumns = array(
            'id',
            'descricao',
            'id_category',
            '3',
            '1'
        );
        $sIndexColumn = "id";
        $sTable = 'tblpatrimony_bens';
        $join = array("INNER JOIN tblequipments_in ON patrimonyid = id");
        $additionalSelect = array("status","(SELECT name FROM tblpatrimony_categories WHERE id = id_category) as categor");
        $where = array();

        array_push($additionalSelect, $sIndexColumn);

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == 'id') {
                    $_data = $aRow['id'];
                }
                else if ($aColumns[$i] == 'descricao') {
                    $_data = $aRow['descricao'];
                }
                else if ($aColumns[$i] == 'id_category') {
                    $_data = $aRow['categor'];
                }
                else if ($aColumns[$i] == '1')
                {
//                    $_data = '<a onclick="add_to_output_modal('.$aRow['id'].'); return false;" class="btn btn-default btn-icon"><i class="fa fa-arrow-right"></i></a>';
                    $_data = '';
                }
                else if ($aColumns[$i] == '3') {
                    $_data = '<div class="onoffswitch">';
                    $_data .= '<input type="checkbox" data-switch-url="'.admin_url('equipmens/patrimony').'" name="onoffswitch" class="onoffswitch-checkbox" id="v_'.$aRow['id'].'" data-id="'.$aRow['id'].'"';
                    if($aRow['status'] == 1)
                        $_data .= ' checked="true"';
                    if(!has_permission('equipments','','edit'))
                        $_data .= ' disabled="true"';
                    $_data .= '><label class="onoffswitch-label" for="v_'.$aRow['id'].'"></label>';
                    $_data .= '</div>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
    }