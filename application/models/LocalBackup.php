<?php
/**
 * Created by PhpStorm.
 * User: Dejair Sisconeto
 * Date: 25/10/2017
 * Time: 11:21
 */

class LocalBackup extends CRM_Model
{


    public function all()
    {

        return $this->db->select(["id", "nome"])->from("tbllocalbackup")->get()->result_array();
    }

}