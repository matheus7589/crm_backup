<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 08/01/2018
 * Time: 09:59
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Activation_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function connect(){
        $mysqli = new mysqli(get_host(), get_username(), get_pass(), get_dbname());
        if($mysqli->connect_error){
            echo "Falha ao conectar ao banco de dados:  " . $mysqli->connect_errno . " -> " . $mysqli->connect_error;
        }

//        echo $mysqli->host_info;

        return $mysqli;
    }

    public function close($mysqli){
        $mysqli->close();
    }

    public function activate($data){

        $this->load->model('emails_model');

        $key = '';
        $acesso = '';
        $date = str_replace('/', '', $data['date']);
        $date = substr($date, 0, 4) . $date[6] . $date[7];
        $cnpj = $this->db
            ->select('cnpj_or_cpf')
            ->where('userid', $data['userid'])
            ->get('tblclients')->row();

        if(count($cnpj) == 0){
            echo json_encode(array(
                'sucesso' => false,
                'message' => 'Cliente não encontrado no servidor local!'
            ));
            return false;
        }

        $mysqli = $this->connect();

        $query = "SELECT cliId FROM cliente WHERE cliCpfCgc=" . $cnpj->cnpj_or_cpf;
        if(!$mysqli->query($query)){
            printf("\nErro: " . $mysqli->error . "\n");
            $this->close($mysqli);
        }else {
            $resultado = $mysqli->query($query);
            if(count($resultado) == 0){
                echo json_encode(array(
                    'sucesso' => false,
                    'message' => 'Cliente não encontrado no servidor remoto'
                ));
                $this->close($mysqli);
                return false;
            }
            $row = $resultado->fetch_assoc();
            $acesso = codifica($data['date'], false);
            $key = cifra($date, $cnpj);

//            echo $row['cliId'];
            $_sql = "UPDATE config SET cofAcesso='" . $acesso . "', cofChave='" . $key . "' ,cofIdentific='" . codifica($cnpj->cnpj_or_cpf, false) . "')";

//            if(!$mysqli->query($_sql)){
//                printf("\n erro: " . $mysqli->error . "\n");
//            }else{
//
//                $email = $this->db
//                    ->select('email')
//                    ->where('userid', $data['userid'])
//                    ->where('is_primary', 1)
//                    ->get('tblcontacts')->row();
//
//                $this->emails_model->send_simple_email($email->email, 'Ativação de usuário', 'Sua conta está sendo ativada!');
//
//                echo json_encode(array(
//                    'sucesso' => true,
//                    'message' => 'Cliente ativado com sucesso',
//                ));
//
//                $this->close($mysqli);
//                return true;
//
//            }

//             echo json_encode(array(
//                 'sucesso' => true,
//                 'idUser' => $row['cliId'],
//                 'date' => $data['date'],
//                 'codificado' => codifica($data['date'], false),
//                 'decodificado' => decodifica(codifica($data['date'], false)),
//                 'message' => 'Cliente ativado com sucesso!',
//                 'cnpj' => $cnpj->cnpj_or_cpf,
//             ), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }

        $this->close($mysqli);

    }

    public function gerar_lum($data){

        $cnpj = $this->db
            ->select('cnpj_or_cpf')
            ->where('userid', $data['userid'])
            ->get('tblclients')->row();

        if(count($cnpj) == 0){
            echo json_encode(array(
                'sucesso' => false,
                'message' => 'Cliente não encontrado no servidor local!',
            ));
            return false;
        }


        $mysqli = $this->connect();


        $query = "SELECT cliId FROM cliente WHERE cliCpfCgc=" . $cnpj->cnpj_or_cpf;
        if(!$mysqli->query($query)){
            printf("\nErro: " . $mysqli->error . "\n");
            $this->close($mysqli);
        }else {
            $resultado = $mysqli->query($query);
            if (count($resultado) == 0) {
                echo json_encode(array(
                    'sucesso' => false,
                    'message' => 'Cliente não encontrado no servidor remoto',
                ));
                $this->close($mysqli);
                return false;
            }
            $row = $resultado->fetch_assoc();

        }

        $sql = "SELECT cliNome, cliFantasia, clicpfCgc, cliDiaVencLum, cliEmail FROM cliente WHERE cliId=" . $row['cliId'];
        if(!$mysqli->query($sql)){
            printf("\n Erro: " . $mysqli->error . "\n");
        }else{
            $resultado = $mysqli->query($sql);
            $row = $resultado->fetch_assoc();
            echo json_encode(array(
               'sucesso' => true,
               'message' => 'It worked!',
            ));
//            var_dump($row);
        }

        /**
         * Gerar Chave e retornar ela(a chave é gerada com base na funcao CifraDll())
         */
        $key = '';

        /**
         * Esse slq faz o update da lum do usuario
         */
//        $_sql_update_lum = "UPDATE cliente SET cliLUM='" . $key . "' WHERE cliId='" . cliId . "' ";
//
//        if(!$mysqli->query($_sql_update_lum)){
//            printf("\n Erro: " . $mysqli->error . "\n");
//        }else{
//            echo json_encode(array(
//                'sucesso' => true,
//                'message' => 'Lum Atualizada com sucesso',
//            ));
//            return true;
//        }
        

        $this->close($mysqli);
        return false;

    }


    public function get_codigo_ativacao($data)
    {

        $cnpj = $this->db
            ->select('cnpj_or_cpf')
            ->where('userid', $data['userid'])
            ->get('tblclients')->row();

        if (count($cnpj) == 0) {
            echo json_encode(array(
                'sucesso' => false,
                'message' => 'Cliente não encontrado no servidor local!'
            ));
            return false;
        }

        $mysqli = $this->connect();

        $query = "SELECT cliId FROM cliente WHERE cliCpfCgc=" . $cnpj->cnpj_or_cpf;
        if (!$mysqli->query($query)) {
            printf("\nErro: " . $mysqli->error . "\n");
            $this->close($mysqli);
        } else {
            $resultado = $mysqli->query($query);
            if (count($resultado) == 0) {
                echo json_encode(array(
                    'sucesso' => false,
                    'message' => 'Cliente não encontrado no servidor remoto'
                ));
                $this->close($mysqli);
                return false;
            }

            $row = $resultado->fetch_assoc();
            $cliId = $row['cliId'];

            $sql = "SELECT cliCodAtivacao FROM cliente WHERE cliId='" . $cliId . "'";

            if (!$mysqli->query($sql)) {
                printf("\n Erro: \n" . $mysqli->error);
            }

            $resultado = $mysqli->query($sql);
            $row = $resultado->fetch_assoc();
            return $row;
        }
    }




}