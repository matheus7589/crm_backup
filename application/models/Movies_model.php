<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 14/02/2018
 * Time: 15:34
 */

class Movies_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_categories()
    {
        return $this->db->get("tblmoviescategory")->result_array();
    }

    public function get_subcategories($id = '',$all = false)
    {
        if(is_numeric($id))
            return $this->db->select("*,(SELECT name FROM tblmoviescategory WHERE categoryid = categoryfk) as category")->where("categoryfk",$id)->get("tblmoviessubcategory")->result_array();
        else if($all)
            return $this->db->select("*,(SELECT name FROM tblmoviescategory WHERE categoryid = categoryfk) as category")->get("tblmoviessubcategory")->result_array();
        return false;
    }

    public function get_movies()
    {
        return $this->db->select("*,(SELECT subcategory FROM tblmoviessubcategory WHERE idsubcategory = idsubcategoryfk) as subcategory, (SELECT name FROM tblmoviescategory WHERE categoryid = (SELECT categoryfk FROM tblmoviessubcategory WHERE idsubcategory = idsubcategoryfk)) as category")->where("status",1)->order_by("date","DESC")->get("tblmovies")->result_array();
    }

    public function get_movie($id)
    {
        if(is_numeric($id))
        {
            return $this->db->select("*,(SELECT categoryid FROM tblmoviescategory WHERE categoryid = (SELECT categoryfk FROM tblmoviessubcategory WHERE idsubcategory = idsubcategoryfk)) as idcategory")->where("idmovie",$id)->get("tblmovies")->row();
        }
    }
}