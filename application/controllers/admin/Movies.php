<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 14/02/2018
 * Time: 14:18
 */

class Movies extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model("movies_model");
    }

    public function index()
    {
        if($this->input->post())
        {
            if(has_permission_video('movies','create')) {
                if($this->input->post("is-url"))
                {
                    $data = $this->input->post();
                    $data['filename'] = $data['video'];
                    $deps = $data['departments'];
                    $data['departments'] = "";
                    $j = 0;
                    foreach ($deps as $department) {
                        if($j > 0)
                            $data['departments'] .= ",";
                        $data['departments'] .= $department;
                        $j++;
                    }
                    /**Inicio Verificação de permições*/
                    $data['perm_clients'] = 0;
                    $data['perm_staff'] = 0;
                    $data['perm_public'] = 0;
                    if (isset($data['perm-public'])) {
                        $data['perm_public'] = 1;
                        $data['perm_key'] = NULL;
                    } else {
                        if (!isset($data['perm-key']))
                            $data['perm_key'] = NULL;
                        unset($data['perm-key']);
                        if (isset($data['perm-staff']))
                            $data['perm_staff'] = 1;
                        if (isset($data['perm-clients']))
                            $data['perm_clients'] = 1;
                    }
                    /**Fim Verificação de permições*/
                    $data['is_url'] = 1;
                    $data['date'] = \Carbon\Carbon::now()->toDateTimeString();
                    unset($data['video']);
                    unset($data['tags']);
                    unset($data['is-url']);
                    unset($data['category']);
                    unset($data['perm-key']);
                    unset($data['perm-staff']);
                    unset($data['perm-clients']);
                    unset($data['perm-public']);

                    $this->db->insert("tblmovies", $data);
                    if (is_numeric($this->db->insert_id()))
                        set_alert("success", "Vídeo inserido com sucesso!");
                    else
                        set_alert("danger","Falha ao inserir Vídeo");
                    header("location:".admin_url("movies"));
                    die();
                }
                $config['upload_path'] = './uploads/movies';
                $config['allowed_types'] = 'mp4';
                $config['file_name'] = md5($_FILES['video']['name']);
                $config['overwrite'] = true;
                $arquivo = $this->db->where("filename", $config['file_name'] . ".mp4")->where("status", 1)->get("tblmovies")->row("idmovie");
                if ($arquivo != NULL) {
                    set_alert("warning", "Arquivo já existe [ID: " . $arquivo . "]");
                    header("location:" . admin_url("movies"));
                    return false;
                }
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('video')) {
                    $data = $this->input->post();
                    $deps = $data['departments'];
                    $data['departments'] = "";
                    $j = 0;
                    foreach ($deps as $department) {
                        if($j > 0)
                            $data['departments'] .= ",";
                        $data['departments'] .= $department;
                        $j++;
                    }
                    /**Inicio Verificação de permições*/
                    $data['perm_clients'] = 0;
                    $data['perm_staff'] = 0;
                    $data['perm_public'] = 0;
                    if (isset($data['perm-public'])) {
                        $data['perm_public'] = 1;
                        $data['perm_key'] = NULL;
                    } else {
                        if (!isset($data['perm-key']))
                            $data['perm_key'] = NULL;
                        unset($data['perm-key']);
                        if (isset($data['perm-staff']))
                            $data['perm_staff'] = 1;
                        if (isset($data['perm-clients']))
                            $data['perm_clients'] = 1;
                    }
                    /**Fim Verificação de permições*/

                    $data['date'] = \Carbon\Carbon::now()->toDateTimeString();

                    unset($data['tags']);
                    unset($data['is-url']);
                    unset($data['category']);
                    unset($data['perm-key']);
                    unset($data['perm-staff']);
                    unset($data['perm-clients']);
                    unset($data['perm-public']);

                    $data['filename'] = $this->upload->data("file_name");
                    $this->db->insert("tblmovies", $data);
                    if (is_numeric($this->db->insert_id()))
                        set_alert("success", "Vídeo inserido com sucesso!");
                } else {
                    set_alert("danger", $this->upload->display_errors());
                }
            }
            else
                access_denied('movies');
            header("location:".admin_url("movies"));
        }
        unset($data);
        $data['categories'] = $this->movies_model->get_categories();
        $data['movies'] = $this->movies_model->get_movies();
        $data['admin'] = true;
        $data['departments'] = $this->db->select('departmentid,name')->get('tbldepartments')->result_array();
        $this->load->view("admin/movies/manager",$data);
    }

    public function subcategory()
    {
        $id = $this->input->get("categoryid");
        if(is_numeric($id))
        {
            $subs = $this->movies_model->get_subcategories($id);
            $options = "";
            foreach ($subs as $sub)
            {
                $options .= "<option value='".$sub["idsubcategory"]."'>".$sub["subcategory"]."</option>";
            }
            echo $options;
        }
    }

    public function get_media($id,$key = "")
    {
        render_player($id,$key,true);
    }
    public function edit($id)
    {
        if($this->input->post()) {
            if(has_permission_video('movies','edit')) {
                $data = $this->input->post();
                $deps = $data['departments'];
                $data['departments'] = "";
                $j = 0;
                foreach ($deps as $department) {
                    if($j > 0)
                        $data['departments'] .= ",";
                    $data['departments'] .= $department;
                    $j++;
                }
                /**Inicio Verificação de permições*/
                $data['perm_clients'] = 0;
                $data['perm_staff'] = 0;
                $data['perm_public'] = 0;
                if (isset($data['perm-public'])) {
                    $data['perm_public'] = 1;
                    $data['perm_key'] = NULL;
                } else {
                    if (!isset($data['perm-key']))
                        $data['perm_key'] = NULL;
                    unset($data['perm-key']);
                    if (isset($data['perm-staff']))
                        $data['perm_staff'] = 1;
                    if (isset($data['perm-clients']))
                        $data['perm_clients'] = 1;
                }
                /**Fim Verificação de permições*/

                //            $data['date'] = \Carbon\Carbon::now()->toDateTimeString();

                unset($data['tags']);
                unset($data['category']);
                unset($data['perm-key']);
                unset($data['perm-staff']);
                unset($data['perm-clients']);
                unset($data['perm-public']);

                $this->db->where("idmovie", $id)->update("tblmovies", $data);
                if ($this->db->affected_rows() > 0)
                    set_alert("success", "Vídeo atualizado com sucesso!");
                header("location:" . admin_url("movies/".$id));
            }
            else
                access_denied('movies');
        }
        $data['movie'] = $this->movies_model->get_movie($id);
        $data['id'] = $id;
        $data['categories'] = $this->movies_model->get_categories();
        $data['subcategories'] = $this->movies_model->get_subcategories($data['movie']->idcategory);
        $data['admin'] = true;
        $data['departments'] = $this->db->select('departmentid,name')->get('tbldepartments')->result_array();
        $this->load->view("admin/movies/single",$data);
    }
    public function delete()
    {
        if(has_permission_video('movies','delete')) {
            $id = $this->input->post('idvideo');
            $media = $this->movies_model->get_movie($id);
            $data['status'] = 0;
            set_alert("danger", "Erro ao deletar Vídeo");
            if (unlink(str_replace("\\", '/', FCPATH . "uploads/movies/" . $media->filename))) {
                $this->db->where("idmovie", $id)->update("tblmovies", $data);
                if ($this->db->affected_rows() > 0)
                    set_alert("success", "Vídeo deletado com Sucesso!");
            }
        }
        else
            access_denied('movies');
        header("location:".admin_url("movies"));
    }
    public function categories()
    {
        if($this->input->post())
        {
            set_alert("danger", "Erro ao cadastrar categoría");
            $data = $this->input->post();
            $type = $data['type'] ;
            unset($data['type'] );
            if($type == "add_categorie")
            {
                $this->db->insert("tblmoviescategory",$data);
                if (is_numeric($this->db->insert_id()))
                    set_alert("success", "Categoría cadastrada com sucesso!");
            }
            else if($type == "add_subcategorie")
            {
                $this->db->insert("tblmoviessubcategory",$data);
                if (is_numeric($this->db->insert_id()))
                    set_alert("success", "Categoría cadastrada com sucesso!");
            }
            header("location:".admin_url("movies/categories"));
        }
        else if($this->input->get())
        {
            set_alert("danger", "Erro ao deletar categoría");
            $data = $this->input->get();
            $type = $data['type'] ;
            unset($data['type'] );
            if($type == "delete_cate")
            {
                $this->db->where("categoryid",$data['id'])->delete("tblmoviescategory");
                if ($this->db->affected_rows() > 0)
                    set_alert("success", "Categoría deletada com sucesso!");
            }
            else if($type == "delete_subcate")
            {
                $this->db->where("idsubcategory",$data['id'])->delete("tblmoviessubcategory");
                if ($this->db->affected_rows() > 0)
                    set_alert("success", "Sub-Categoría deletada com sucesso!");
            }
            header("location:".admin_url("movies/categories"));
        }

        $data['categories'] = $this->movies_model->get_categories();
        $data['sub_categories'] = $this->movies_model->get_subcategories('',true);

        $this->load->view("admin/movies/categories",$data);
    }
//    public function add_categorie()
//    {
//        echo "oi";
//        $data['name'] = $this->input->post("name");
//        $this->db->insert("tblmoviescategory",$data);
//        set_alert("danger", "Erro ao cadastrar categoría");
//        if (is_numeric($this->db->insert_id()))
//            set_alert("success", "Categoría cadastrada com sucesso!");
//        header("location:".admin_url("movies/categories"));
//    }
}