<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 05/12/2017
 * Time: 13:05
 */

class Partner_model extends CRM_Model
{
    public function get($id)
    {
        if(is_numeric($id))
            $_id = $id;
        else
            $_id = get_staff_partner_id();

        $where = "WHERE partner_id = ".$_id;
        return $this->db
            ->query("SELECT * FROM tblpartner ".$where)
            ->result_array();
    }
    public function update($data, $id)
    {
        $this->db->where('partner_id',$id);
        $this->db->update('tblpartner', $data);
        if($this->db->affected_rows() > 0) {
            logActivity("Parceiro atualizado. [IdParceiro: ".$id."]");
            return true;
        }

        return false;
    }
    public function add($datap)
    {
        $this->db->query("INSERT INTO tblpartner(partner_cnpj, active, email, firstname, lastname, phonenumber, cidade, estado, datecreated) VALUES ('".$datap['partner_cnpj']."',1,'".$datap['email']."','".$datap['fantasia']."','".$datap['socialr']."','".$datap['phonenumber']."', '".$datap['cidade']."', '".$datap['estado']."','".date("Y-m-d H:i:s")."')");
        if($this->db->affected_rows() > 0) {
            logActivity("Parceiro atualizado. [IdParceiro: " . $this->db->insert_id() . "]");
            return true;
        }
        return false;
    }
}