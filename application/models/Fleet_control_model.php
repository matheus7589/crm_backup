<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 27/12/2017
 * Time: 13:18
 */

class Fleet_control_model extends CRM_Model
{
    public function add_vehicle($data)
    {
        $data['venclicenci'] = to_sql_date($data['venclicenci']);
        $data['datainicicontr'] = to_sql_date($data['datainicicontr']);
        $data['datafimcontr'] = to_sql_date($data['datafimcontr']);
        $data['vehicleid'] = NULL;
        $data['active'] = 1;

        $this->db->insert('tblfleetvehicles',$data);
        if ($this->db->affected_rows() > 0) {
            logActivity("Veículo cadastrado. [ID: ".$this->db->insert_id()."]");
            return true;
        }
        else
            return false;
    }

    public function delete_vehicle($vehicleid)
    {
        $this->db->where('vehicleid', $vehicleid)->delete('tblfleetout');
        $this->db->where('vehicleid', $vehicleid)->delete('tblfleetvehicles');
        if ($this->db->affected_rows()>0) {
            logActivity("Veículo excluido. [ID: ".$vehicleid."]");
            return true;
        }
        else
            return false;
    }

    public function get_vehicles($id = '')
    {
        if(is_numeric($id))
        {
            $result = $this->db->where("vehicleid",$id)->get("tblfleetvehicles")->row();
            if($result != null)
                return $result;
            else
                return false;
        }
        else
            return false;
    }

    public function fleet_out($data)
    {
        if(is_numeric($data['outid']))
        {
            $data['state'] = 0;
            $data1['inuse'] = 0;
            $data1['kmatual'] = $data['km_final'];
            $id = $data['outid'];
            $vhid = $data['vhid'];
            unset($data['vhid']);
            unset($data['outid']);
            if($data['km_final'] != "" && $data['datetime_final'] != "")
            {
                if(isset($data['data']))
                    $data['data'] = to_sql_date($data['data'], false);
                if(isset($data['datetime_inicial']))
                    $data['datetime_inicial'] = to_sql_date($data['datetime_inicial'], true);
                if(isset($data['datetime_final']))
                    $data['datetime_final'] = to_sql_date($data['datetime_final'], true);
                $this->db->where("idsaida",$id)->update('tblfleetout', $data);
                $this->db->where("vehicleid",$vhid)->update('tblfleetvehicles', $data1);
            }
            if($this->db->affected_rows()>0) {
                logActivity("Saída frota Finalizada. [ID: ".$id."]");
                return true;
            }
            else
                return false;
        }
        else
        {
            unset($data['outid']);
            unset($data['vhid']);
            $data['state'] = 1;
            $data1['inuse'] = 1;
            $vid = $data['vehicleid'];
            $data['data'] = to_sql_date($data['data'], true);
            if($data['km_final'] == "")
                $data['km_final'] = NULL;
            if($data['datetime_final'] == "")
                $data['datetime_final'] = NULL;
            if($data['km_final'] != "" && $data['datetime_final'] != "")
            {
                $data['datetime_final'] = to_sql_date($data['datetime_final'], true);
                $data['state'] = 0;
                $data1['inuse'] = 0;
                $data1['kmatual'] = $data['km_final'];
            }
            $data['datetime_inicial'] = to_sql_date($data['datetime_inicial'], true);
            $this->db->where("vehicleid",$vid)->update('tblfleetvehicles', $data1);
            $this->db->insert('tblfleetout', $data);
            if($this->db->affected_rows()>0) {
                logActivity("Nova Saída frota registrada. [ID: ".$this->db->insert_id()."]");
                return true;
            }
            else
                return false;
        }
    }

    public function add_supply($data)
    {
        $data['data'] = to_sql_date($data['data']);
        if($data['precoporlitro'] == "")
            $data['precoporlitro'] = $data['valortotal']/$data['litro'];
        $this->db->insert('tblfleetsupply', $data);
        if($this->db->affected_rows()>0) {
            logActivity("Novo abastecimento. [ID: ".$this->db->insert_id()."]");
            return true;
        }
        else
            return false;
    }
}