<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns      = array(
    'firstname',
    'partner_cnpj',
    'active',
    'partner_id'
);
$sIndexColumn  = "partner_id";
$sTable        = 'tblpartner';
$join          = array();
$i             = 0;

$where = "";

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow)
{
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == 'firstname')
        {
            if(has_permission('partner','','edit'))
                $_data = '<a href="partner/alter/' . $aRow['partner_id'].'">'.$aRow['firstname'].'</a>';
            else
                $_data = $aRow['firstname'];
        }
        else if ($aColumns[$i] == 'partner_cnpj')
        {
            $_data = $aRow['partner_cnpj'];
        }
        else if ($aColumns[$i] == 'active')
        {
            $checked = '';
            if ($aRow['active'] == 1) {
                $checked = 'checked';
            }
            $disabled = '';
            if(!has_permission('partner','','edit'))
                $disabled = 'disabled';

            $_data = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="'.admin_url().'partner/change_partner_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$aRow['partner_id'].'" data-id="'.$aRow['partner_id'].'" '.$checked.' '.$disabled.'>
                <label class="onoffswitch-label" for="c_'.$aRow['partner_id'].'"></label>
            </div>';

            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        }
        else if ($aColumns[$i] == 'partner_id')
        {
            $_data = icon_btn('partner/' . $aRow['partner_id'], 'pencil-square-o');
            if(has_permission('partner','','delete'))
            {
                $_data .= icon_btn('', 'remove', 'btn-danger', array(
                    'onclick' => 'delete_staff_member(' . $aRow['partner_id'] . '); return false;',
                ));
            }
            if(has_permission('partner','','edit'))
            {
                $_data .= "<div class='pull-right'>" . icon_btn('partner/alter/' . $aRow['partner_id'], 'sign-in') . "</div>";
            }
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
