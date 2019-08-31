<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 23/04/2018
 * Time: 09:55
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Retaguarda_crm extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
    }

    public function client_retaguarda($cnpj = ''){
        $type = array(
            'table' => 'clients',
            'controller' => 'empresas',
        );
        $contatos = null;
        if($this->input->post()) {
//            $key = get_key_retaguarda($this->input->post('cnpj_or_cpf')); /**  Chave de validacao */
//            if (strtoupper($key) == strtoupper($this->input->post('key'))) {
            if (true) {

                /** Dados da empresa  */ // Pega os dados pelo cnpj
                $client = $this->clients_model->get('', array(
//                    'tblclients.active' => $this->input->post('active'),
                    'cod_empresa' => $this->input->post('cod_empresa'),
//                    'cnpj_or_cpf' => $this->input->post('cnpj_or_cpf'),
                ));

                $data = $this->input->post(null, false); /**  Dados do POST  */
                $data['company'] = $data['cod_empresa'] . ' ' . $data['company']; // altera o nome da company
                $segmento = $data['segmento'];
                unset($data['segmento']);
                if(get_partner_by_cnpj($data['partner_id'])) {
                    $data['partner_id'] = get_partner_by_cnpj($data['partner_id'])->partner_id; //Encontra o parceiro pelo CNPJ
                }else{
                    echo json_encode(array(
                        'status' => false,
                        'message' => 'Parceiro não encontrado',
                    ));
                    die;
                }

                $envia_gestor = $data['enviaGestor'];

                unset($data['key']);
                unset($data['enviaGestor']);

                if(isset($data['contatos'])){
                    $contatos = json_decode($data['contatos']); /** Pega os contatos enviados pelo Retaguarda */
                    unset($data['contatos']);
                }

                if ($client == null) { // Novo Cliente
                    $type['metodo'] = 'add';
//                    if ($this->input->post('cnpj_or_cpf') !== '99751941172') { TODO Verificar essa condicao
//                        if ($this->clients_model->check_cnpj($this->input->post('cnpj_or_cpf'))) {
//                            echo json_encode(array(
//                                'status' => false,
//                                'message' => 'CNPJ já existe',
//                            ));
//                            die;
//                        }
//                    }
                    $id = $this->clients_model->add($data);

                    if(count($contatos) > 0){ /** verifica se tem algum contato do retaguarda para fazer edicao/adicao/delecao */
//                        $this->db->trans_start();
                        foreach ($contatos as $key => $value){ /** itera entre os contatos enviados pelo Retaguarda */
                            $contato = array(
                                'firstname' => $value->ccoNome,
                                'lastname' => '',
                                'email' => $value->ccoEmail,
                                'cpf' => $value->ccoCpf,
                                'donotsendwelcomeemail' => true,
                                'phonenumber' => $value->ccoFone,
                                'password' => 123,
                                'active' => 1,
                                'permissions' => array('1', '5'),
                            );

                            /**
                             * Insere no CRM
                             */
                            $this->clients_model->add_contact($contato, $id);

                            /**
                             * Insere na ApiGestor
                             */
                            if($value->ccoGestor === 'True' && $envia_gestor == 'True') {
                                $retorno = $this->sendContactToApiGestor($contato, $data['social_reason'], $data['cod_empresa'], $type['metodo']); /** Funcao auxiliar para adicioar apenas o contato */
                                if($retorno['success'] == false){
                                    echo json_encode(array(
                                        'status' => false,
                                        'message' => $retorno['message'],
                                    ));
                                    die();
                                }
                            }
                        }
//                        $this->db->trans_complete();
                    }


                    if ($id && ($envia_gestor == 'True')) {
                        $data['prop'] = null;
                        $api = $this->sendToApiGestor($data, $type); /** envia o client pro Gestor */
                        if($api->message == 'success'){
                            echo json_encode(array(
                                'status' => true,
                                'message' => 'Novo Cliente Adicionado',
                            ));
                        } else {
                           $this->clients_model->delete($id);
                            echo json_encode(array(
                                'status' => false,
                                'message' => 'Falha ao adicionar na API',
                            ));
                        }
                    } else {
                        if($id){
                            echo json_encode(array(
                                'status' => true,
                                'message' => 'Cliente Adicionado com Sucesso!'
                            ));
                        }else {
                            echo json_encode(array(
                                'status' => false,
                                'message' => 'Falha ao Adicionar Cliente'
                            ));
                        }
                    }
                } else { // Update Cliente
                    $type['metodo'] = 'edit';
                    $client = $this->clients_model->get_userid_by_cod_empresa($this->input->post('cnpj_or_cpf'), $this->input->post('cod_empresa')); /** retorna o client de acordo com o CNPJ informado */
                    if(!empty($client->observation))
                        $data['observation'] = $client->observation;

                    if(!empty($client->validate_certificate))
                        $data['validate_certificate'] = _d($client->validate_certificate);

                    $success = $this->clients_model->update($data, $client->userid, false); /** realiza o Update no CRM */
                    unset($data['active']);
                    $data['prop'] = '';

                    $update_contacts = $this->clients_model->get_contacts_by_where(['clientid' => $client->userid]); /** retorna a lista de contatos existente no CRM que serao atualizados/deletados */

                    if(count($contatos) > 0){ /** novamente, verifica se o numero de contatos enviados do Retaguarda e maior que 0 */
                        $selected_ids = [];
                        $add_contatos = [];
                        $all_ids = [];

//                        $this->db->trans_start();
                        foreach ($contatos as $key => $value) { /** itera pelos contatos enviados do Retaguarda */

                            $aux_contact_update = 0;
                            $contato = array(
                                'firstname' => $value->ccoNome,
                                'lastname' => ' ',
                                'email' => $value->ccoEmail,
                                'cpf' => $value->ccoCpf,
                                'phonenumber' => $value->ccoFone,
                                'password' => '',
                                'active' => 1,
                                'userid' => $client->userid,
                                'permissions' => array('1', '5'),
                            );

                            if(count($update_contacts) > 0) {
                                foreach ($update_contacts as $chave => $valor) {
                                    /** itera pelos contatos existentes no CRM */
                                    $temp_contato = $this->clients_model->get_contact($valor['contactid']);
                                    /** retorna um contato do CRM pelo Id */
                                    if ($temp_contato) {
                                        if ($value->ccoCpf == $temp_contato->cpf) {
                                            /** se o contato enviado pelo retaguarda tem o mesmo cpf do contato no CRM */

                                            /**
                                             * Faz o update no CRM
                                             */
                                            $this->clients_model->update_contact($contato, $valor['contactid']);

                                            /**
                                             * Faz o update/deleta no APIGestor
                                             */
                                            if ($value->ccoGestor === 'True' && $envia_gestor == 'True') {
                                                /** se esta setado o envio para o gestor */
                                                $contato['emailOld'] = $temp_contato->email;
                                                /** emailOld é um campo auxiliar utilizado na api para identificar o usuario a ser editado(ja que
                                                 * na api não existe cpf para comparar)
                                                 */
                                                $retorno = $this->sendContactToApiGestor($contato, $client->social_reason, $client->cod_empresa, $type['metodo']);
                                                if ($retorno['success'] == false) {
                                                    $aux_contact_update++;
                                                    /** Gambira pra adicionar caso não encontre ao tentar editar */
                                                }
                                                unset($contato['emailOld']);
                                            } else {
//                                                $type['metodo'] = 'delete';
//                                                $contato['emailOld'] = $temp_contato->email;
//                                                /** campo auxiliar para a Api */
//                                                $retorno = $this->sendContactToApiGestor($contato, $client->social_reason, $client->cod_empresa, $type['metodo']);
//                                                if ($retorno['success'] == false) {
//                                                    echo json_encode(array(
//                                                        'status' => false,
//                                                        'message' => $retorno['message'],
//                                                    ));
//                                                    die();
//                                                }
//                                                $type['metodo'] = 'edit';
//                                                unset($contato['emailOld']);
                                            }
                                            array_push($selected_ids, $valor['contactid']);
                                            /** armazena os ids que tem o cpf semelhante */
                                        } else {
                                            $aux_contact_update++;
                                        }
                                    }
                                    array_push($all_ids, $valor['contactid']);
                                    /** armazena todos os ids */

                                }
                            }

//                            if(count($add_contatos) > 0){ /** Caso tenha um novo contato */
//
//                                foreach ($add_contatos as $add_contato) {
//                                    $contato = $add_contato;
                                    $contato['donotsendwelcomeemail'] = true;
                                    /** variavel de controle de email */
                                    $contato['password'] = 123;
                                    /**
                                     * Adiciona no CRM
                                     */

                                    if ($this->clients_model->check_cpf($contato['cpf']) == false) {
                                        /** checa se o cpf ja existe no CRM para evitar contatos duplicados */
                                        $this->clients_model->add_contact($contato, $client->userid);
                                    }
                                    /**
                                     * Adiciona no ApiGestor
                                     */
                                    if ($value->ccoGestor === 'True' && $envia_gestor == 'True') {
                                        $type['metodo'] = 'add';
                                        $retorno = $this->sendContactToApiGestor($contato, $client->social_reason, $client->cod_empresa, $type['metodo']);
                                        if ($retorno['success'] == false) {
                                            echo json_encode(array(
                                                'status' => false,
                                                'message' => $retorno['message'],
                                            ));
                                            die();
                                        }
                                        $type['metodo'] = 'edit';
                                    }
//                                }
//                            }

                        }

                        if(count($update_contacts) >= count($contatos)) { /** Se o CRM tem mais contatos do que os q foram enviados, ele deleta os contatos extras do CRM */
                            $all_ids = array_unique($all_ids); /** retira valores duplicados do array */
                            $not_selected = array_diff($all_ids, $selected_ids); /** faz a diferença entre os dois arrays - $all_ids que armazena todos os ids que estão no CRM
                             * e o $selected_ids que armazena os ids  com ccoGestor marcados como True
                             */

//                            foreach ($not_selected as $id_not_selected) { /** Passa por todos os ids não constam no retaguarda e deleta-os */
//                                $contact_not_selected = $this->clients_model->get_contact($id_not_selected);
//
//                                if($contact_not_selected) {
//
//                                    /**
//                                     * Deletando do CRM
//                                     */
//                                    $this->clients_model->delete_contact($id_not_selected);
//
//                                    /**
//                                     * Deletando do ApiGestor
//                                     */
//                                    $contato = array(
//                                        'firstname' => $contact_not_selected->firstname,
//                                        'lastname' => ' ',
//                                        'email' => $contact_not_selected->email,
//                                        'cpf' => $contact_not_selected->cpf,
//                                        'phonenumber' => $contact_not_selected->phonenumber,
//                                        'password' => 123,
//                                        'active' => 1,
//                                        'userid' => $client->userid,
//                                    );
//
//                                    $type['metodo'] = 'delete';
//                                    $contato['emailOld'] = $contact_not_selected->email;
//                                    $retorno = $this->sendContactToApiGestor($contato, $client->social_reason, $client->cod_empresa, $type['metodo']);
//                                    if ($retorno['success'] == false) {
//                                        echo json_encode(array(
//                                            'status' => false,
//                                            'message' => $retorno['message'],
//                                        ));
//                                        die();
//                                    }
//                                    $type['metodo'] = 'edit';
//                                    unset($contato['emailOld']);
//                                }
//                            }
                        }


//                        $this->db->trans_complete();
                    }else{
//                        foreach ($update_contacts as $aux_contacts){
//                            $no_contact = $this->clients_model->get_contact($aux_contacts['contactid']);
//
//                            if($no_contact) {
//                                /**
//                                 * Deletando do CRM
//                                 */
//                                $this->clients_model->delete_contact($aux_contacts['contactid']);
//
//                                /**
//                                 * Deletando do ApiGestor
//                                 */
//                                $contato = array(
//                                    'firstname' => $no_contact->firstname,
//                                    'lastname' => ' ',
//                                    'email' => $no_contact->email,
//                                    'cpf' => $no_contact->cpf,
//                                    'phonenumber' => $no_contact->phonenumber,
//                                    'password' => 123,
//                                    'active' => 1,
//                                    'userid' => $client->userid,
//                                );
//
//                                $type['metodo'] = 'delete';
//                                $contato['emailOld'] = $no_contact->email;
//                                $retorno = $this->sendContactToApiGestor($contato, $client->social_reason, $client->cod_empresa, $type['metodo']);
//                                if ($retorno['success'] == false) {
//                                    echo json_encode(array(
//                                        'status' => false,
//                                        'message' => $retorno['message'],
//                                    ));
//                                    die();
//                                }
//                                $type['metodo'] = 'edit';
//                                unset($contato['emailOld']);
//                            }
//                        }
                    }

                    $contact = $this->clients_model->get_contact($client->primary_contact); /** pega o contato para edicao */
                    $data['codOld'] = $client->cod_empresa;
                    $data['prop'] = ' ';
//                    if ($success == true && $envia_gestor == 'True') {
                    if ($envia_gestor == 'True') {
                        if($contact){
                            $data['prop'] = $contact->firstname ?? ' ';
                        }
                        $api = $this->sendToApiGestor($data, $type);
                        if($api->message == 'success'){
                            echo json_encode(array(
                                'status' => true,
                                'message' => 'Cliente Atualizado',
                            ));
                        } else {
                            //Alterei aqui
                            $type['metodo'] = 'add';
                            $api = $this->sendToApiGestor($data, $type);

                            if($api->message == 'success') {
                                echo json_encode(array(
                                    'status' => true,
                                    'message' => 'Cliente Inserido no Update',
                                ));
                            }else {
                                echo json_encode(array(
                                    'status' => false,
                                    'message' => 'não está inserido no gestor por isso n edita'
//                                'message' => 'Falha ao Atualizar API',
                                ));
                            }
                            //Alterei aqui
                        }

                    } else{
                        echo json_encode(array(
                            'status' => true,
                            'message' => 'Não houve update no CRM'
                        ));
                    }
//                    else {
//                        if(isset($data['senha']) and $data['senha'] != '' && $envia_gestor == 'True'){
////                            $contact = $this->clients_model->get_contact($client->primary_contact);
//                            if($contact){
//                                $data['prop'] = $contact->firstname ?? ' ';
//                            }
//
//                            $query_client = array (
//                                'cod_empresa' => $data['cod_empresa'],
//                                'company' => $data['company'],
//                                'prop' => $data['prop'] ?? ' ',
//                                'senha' => $data['senha'] ?? 123,
//                                'codOld' => $data['codOld'],
//                            );
//
//                            $api = $this->sendToApiGestor($query_client, $type);
//                            if($api->message == 'success'){
//                                echo json_encode(array(
//                                    'status' => true,
//                                    'message' => 'Cliente Atualizado na Api',
//                                ));
//                            } else {
//                                echo json_encode(array(
//                                    'status' => false,
//                                    'message' => $api->message,
////                                    'message' => 'Falha ao Atualizar Cliente na Api',
//                                ));
//                            }
//                        }else{
//                            echo json_encode(array(
//                                'status' => false,
//                                'message' => 'Cliente não Encontrado/Modificado'
//                            ));
//                        }
//
//                    }
                }
            }else{
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Chaves Incompatíveis',
                ));
                die;
            }
        }
    }

    public function contact_retaguarda()
    {
        if ($this->input->post()) {
            $type = array(
                'table' => 'contacts',
                'controller' => 'usuario',
            );
            $key = get_key_retaguarda($this->input->post('cnpj'));
            if (strtoupper($key) == strtoupper($this->input->post('key'))) {
                /** Dados da empresa  */ // Pega os dados pelo cnpj
                $clientid = $this->clients_model->get('', array(
//                    'tblclients.active' => 1,
                    'cnpj_or_cpf' => $this->input->post('cnpj'),
                ))[0];
//                echo ($clientid['userid']); die();
                /** Dados do Contato  */ // pega o contato caso ele exista
                $contactid = $this->clients_model->get_contact_related_clientcontact('', array(
                    'tblclientscontacts.clientid' => $clientid['userid'],
                    'tblcontacts.cpf' => $this->input->post('cpf'),
                ));
                if($contactid == null){ /** ultima verificacao que busca por email ao inves do cpf */
                    $contactid = $this->clients_model->get_contact_related_clientcontact('', array(
                        'tblclientscontacts.clientid' => $clientid['userid'],
                        'tblcontacts.email' => $this->input->post('email'),
                    ));
                }
                /** ------------------ */
                /** tretinha para caso a empresa nao esteja atualizada com o codEmpresa */
                if(empty($clientid['cod_empresa'])){
                    $this->db->where('userid', $clientid['userid']);
                    $this->db->update('tblclients', array(
                        'cod_empresa' => explode(" ", $clientid['company'])[0],
                    ));
                }

                $data = $this->input->post();
                $nomeEmpresa = $data['nomeEmpresa'];
                unset($data['nomeEmpresa']);
                $data['donotsendwelcomeemail'] = true;
                $data['active'] = 1;
                unset($data['key']);
                if ($contactid == null) {
                    $type['metodo'] = 'add';
                    unset($data['cnpj']);
                    $data['permissions'] = array('1', '5');
                    $id = $this->clients_model->add_contact($data, $clientid['userid']);
//                    $message = '';
//                    $success = false;
                    if ($id) {
//                        $retorno = $this->sendContactToApiGestor($data, $nomeEmpresa, $clientid['cod_empresa'], $type['metodo']);
                        $message = 'Inserido com sucesso';
                        $success = true;
                    }
                    echo json_encode(array(
                        'status' => $success,
                        'message' => $message,
                        'has_primary_contact' => (total_rows('tblcontacts', array('userid' => $clientid['userid'], 'is_primary' => 1)) > 0 ? true : false)
                    ));
                    die;
                } else {
                    $type['metodo'] = 'edit';
                    unset($data['cnpj']);
                    $data['userid'] = $contactid->clientid;
                    $data['permissions'] = array('1', '5');
                    unset($data['send_set_password_email']);
                    unset($data['donotsendwelcomeemail']);
                    $original_contact = $this->clients_model->get_contact($contactid->id);
                    $success = $this->clients_model->update_contact($data, $contactid->id);
                    $data['emailOld'] = $contactid->email;
                    $data['cod_empresa'] = $clientid['cod_empresa'];
                    $data['nomeEmpresa'] = $nomeEmpresa;
                    $message = '';
                    $proposal_warning = false;
                    $original_email = '';
                    $updated = false;
                    if (is_array($success)) {
                        if (isset($success['set_password_email_sent'])) {
                            $message = _l('set_password_email_sent_to_client');
                        } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                            $updated = true;
                            $message = _l('set_password_email_sent_to_client_and_profile_updated');
                        }
                    } else {
                        if ($success == true) {
                            $updated = true;
                            $message = _l('updated_successfully', _l('contact'));
                        }
                    }
                    if (handle_contact_profile_image_upload($contactid) && !$updated) {
                        $message = _l('updated_successfully', _l('contact'));
                        $success = true;
                    }
                    if ($updated == true) {
                        $contact = $this->clients_model->get_contact($contactid->id);
                        if (total_rows('tblproposals', array(
                                'rel_type' => 'customer',
                                'rel_id' => $contact->userid,
                                'email' => $original_contact->email
                            )) > 0 && ($original_contact->email != $contact->email)) {
                            $proposal_warning = true;
                            $original_email = $original_contact->email;
                        }
                    }
//                    $retorno = $this->sendContactToApiGestor($data, $nomeEmpresa, $clientid['cod_empresa'], $type['metodo']);
//                    if ($retorno['success'] == true) {
//                        $success = true;
//                        $message = _l('updated_successfully', _l('contact'));
//                    }else{
//                        $data['donotsendwelcomeemail'] = true;
//                        $type['metodo'] = 'add';
//                        $retorno = $this->sendContactToApiGestor($data, $nomeEmpresa, $clientid['cod_empresa'], $type['metodo']);
//                        if ($retorno['success'] == true) {
//                            $success = true;
//                            $message = _l('updated_successfully', _l('contact'));
//                        }
//                        $type['metodo'] = 'edit';
//                    }
                    echo json_encode(array(
                        'status' => $success,
                        'proposal_warning' => $proposal_warning,
                        'message' => $message,
                        'original_email' => $original_email,
                        'has_primary_contact' => (total_rows('tblcontacts', array('userid' => $clientid['userid'], 'is_primary' => 1)) > 0 ? true : false)
                    ));
                    die;
                }
            }else{
                echo json_encode(array(
                    'status' => false,
                ));
                die;
            }
        }
    }

    public function delete_contact_retaguarda(){
        if ($this->input->post()) {

            $key = get_key_retaguarda($this->input->post('cnpj'));
            if (strtoupper($key) == strtoupper($this->input->post('key'))) {
                /** Dados da empresa  */ // Pega os dados pelo cnpj
                $clientid = $this->clients_model->get('', array(
//                    'tblclients.active' => 1,
                    'cnpj_or_cpf' => $this->input->post('cnpj'),
                ))[0];
                /** Dados do Contato  */ // pega o contato caso ele exista
                $contactid = $this->clients_model->get_contact_related_clientcontact('', array(
                    'tblclientscontacts.clientid' => $clientid['userid'],
                    'tblcontacts.cpf' => $this->input->post('cpf'),
                ));

                if($contactid == null){
                    echo json_encode(array(
                        'status' => true,
                        'message' => 'Contato não encontrado',
                    ));
                    die;
                }

                $this->clients_model->delete_contact($contactid->id);
                echo json_encode(array(
                    'status' => true,
                    'message' => 'Contato deletado!',
                ));
                die;

            }else{
                echo json_encode(array(
                    'status' => false,
                    'message' => 'error',
                ));
                die;
            }
        }
    }

    public function sendContactToApiGestor($data, $nomeEmpresa, $codEmpresa, $metodo){

        $success = false;
        $message = 'error';
        $type = array(
            'table' => 'contacts',
            'controller' => 'usuario',
            'metodo' => $metodo,
        );

        $data['nomeEmpresa'] = $nomeEmpresa;
        $data['cod_empresa'] = $codEmpresa;
        $api = $this->sendToApiGestor($data, $type);
        if((isset($api->message) && $api->message == 'success') && $type['metodo'] != 'edit'){
            $success = true;
            $type['table'] = 'usuarioempresa';
            $type['controller'] = 'usuarioempresa';
            $data['nomeEmpresa'] = $nomeEmpresa;
            $data['user_id'] = $api->usuario->Id;

            $usuarioEmpresa = $this->sendToApiGestor($data, $type);
            if($usuarioEmpresa->message == 'success'){
                $message = 'Cliente adicionado e relacionado com a empresa';
            }else{
                $message = 'Cliente adicionado, porém, não foi relacionado à empresa';
            }
        }else if((isset($api->message) && $api->message == 'success') || (isset($api->message) && $api->message == 'nao_encontrado')) {
            $success = true;
            $message = 'Editado com sucesso';
        }else{
            $message = 'Falha ao adicionar na API';
        }
        return array(
            'success' => $success,
            'message' => $message,
        );
    }


    public function sendToApiGestor($data, $type){
        $metodo = $type['metodo'] . '/';
        if($type['table'] == 'clients'){
            $query = array (
                'codEmpresa' => $data['cod_empresa'],
                'nomeEmpresa' => $data['company'],
                'prop' => $data['prop'] ?? '',
                'monitorando' => 0,
                'gerar_eventos' => 0,
                'senha' => $data['senha'] ?? 123,
            );
            if($type['metodo'] == 'edit') {
                $metodo .= $data['codOld'];
                $query['retaguardaEdit'] = true;
            }
        } else if($type['table'] == 'contacts'){ //otherwise is contact
            $query = array (
                'user_email' => $data['email'] ?? '',
                'nome' => trim($data['firstname']) . ' ' . trim($data['lastname']),
                'password' => $data['password'] ?? 123,
                'codEmpresa' => $data['cod_empresa'] ?? null,
                'nomeEmpresa' => $data['nomeEmpresa'] ?? null,
            );
            if($type['metodo'] == 'edit'){
                $metodo .= $data['emailOld'];
                $query['retaguardaEdit'] = true;
                $query['emailOld'] = $data['emailOld'];
            }
            else if($type['metodo'] == 'delete'){
                $query['retaguardaDelete'] = true;
                $query['emailOld'] = $data['emailOld'];
            }
        } else { //otherwise is usuarioEmpresa
            $query = array(
                'codEmpresa' => $data['cod_empresa'],
                'nome' => $data['nomeEmpresa'] ?? null,
                'user_id' => $data['user_id'],
            );
        }

        // Build the query
        $content = http_build_query ($query);



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/api-gestor/' . $type['controller'] . '/' . $metodo . '.json?token=' . TOKEN);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        $result = curl_exec($ch);

//        print_r($metodo);
        return json_decode($result);
    }

    public function set_versao(){
        if ($this->input->post()) {
            $message = 'error';
            $status = false;
            if($this->input->post('cnpj') && $this->input->post('versao') && $this->input->post('executavel')){
                $cnpj = $this->input->post('cnpj');
                $versao = $this->input->post('versao');
                $executavel = $this->input->post('executavel');

                $where = array(
                    'tblclients.cnpj_or_cpf' => $cnpj
                );

                $client = $this->clients_model->get('', $where);

                if($client){

                    $this->db->where('userid', $client[0]['userid']);
                    $this->db->where('executavel', $executavel);
                    $tblVersao = $this->db->get('tblversoes')->row();

                    if($tblVersao){
                        $this->db->where('id', $tblVersao->id);
                        $this->db->update('tblversoes', array(
                            'versao' => $versao,
                        ));

                        if ($this->db->affected_rows() > 0) {
                            $message = 'Atualizado com sucesso';
                            $status = true;
                        }

                    }else{
                        $id = $this->db->insert('tblversoes', array(
                            'executavel' => $executavel,
                            'versao' => $versao,
                            'userid' => $client[0]['userid'],
                        ));
                        if($id){
                            $message = 'Inserido com sucesso';
                            $status = true;
                        }
                    }

                }else{
                    $message = 'Cliente não encontrado';
                    $status = false;
                }
            }else{
                $message = 'Parâmetros inválidos';
                $status = false;
            }

            echo json_encode(array(
                'status' => $status,
                'message' => $message,
            ));
            die;
        }
    }

    public  function atualiza($cnpj){
        if(empty($cnpj)){
            echo json_encode(array(
                'status' => false,
                'message' => 'Parâmetro inválido',
            ));
            die;
        }
        $this->db->where('cnpj_or_cpf', $cnpj);
        $client = $this->db->get('tblclients')->row();
        if($client){
//            echo json_encode(array(
//                'atualiza' => boolval($client->atualiza),
////                'message' => 'Atualização Ativa',
//            ));
            echo boolval($client->atualiza);
            die;
        }

//        echo json_encode(array(
//            'atualiza' => false,
////            'message' => 'Não é possível atualizar',
//        ));
        echo false;
        die;
    }

    public function after_atualiza($cnpj){
        if(empty($cnpj)){
            echo json_encode(array(
                'status' => false,
                'message' => 'Parâmetro inválido',
            ));
            die;
        }

 //       $message = 'error';
  //      $status = false;
//
 //       $this->db->where('cnpj_or_cpf', $cnpj);
        $success = $this->db->update('tblclients', array(
            'atualiza' => false,
        ));
        if ($this->db->affected_rows() > 0) {
//            $message = 'Atualizado com sucesso';
            $status = true;
        }

        echo json_encode(array(
            'status' => $status,
            'message' => $message,
        ));
        die;
    }

}
