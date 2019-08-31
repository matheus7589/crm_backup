<?php
/**
 * Created by PhpStorm.
 * User: dejair
 * Date: 10/10/17
 * Time: 09:02
 */

class Issuing_certificate_model extends CRM_Model
{

    public function all()
    {
        return $this->db
            ->select("*")
            ->order_by("name ASC")
            ->get("tblissuingcertificate")
            ->result_array();

    }

}