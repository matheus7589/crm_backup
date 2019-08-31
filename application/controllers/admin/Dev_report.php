<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 13/07/2018
 * Time: 15:14
 */

class Dev_report extends Admin_controller
{

    const DEV_ESPERA = 26;
    const DEV_ATENDIDO = 32;
    const DEV_PRODUCAO = 33;
    const DEV_TESTE = 24;
    const DEV_ANALISE = 35;
    const DEV_REANALISE = 34;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dev_report_model');
    }

    public function show(){

        if (!is_admin()) {
            access_denied('Reports');
        }

        $statuses = $this->db
            ->select('ticketstatusid')
            ->get('tblticketstatus')->result_array();



//        $data['status_media'] = array(
        $status_media = array(
            0 => array(
                'status_from' => $this::DEV_ANALISE,
                'status_to' => $this->get_list_statuses($statuses, $this::DEV_ANALISE),
                'name' => 'Tempo Médio de Solicitações em DEV - ANALISE',
                'time' => '',
                'options' => ''
            ),
            1 => array(
                'status_from' => $this::DEV_ESPERA,
                'status_to' => $this->get_list_statuses($statuses, $this::DEV_ESPERA),
                'name' => 'Tempo Médio de Solicitações em DEV - ESPERA',
                'time' => '',
                'options' => ''
            ),
            2 => array(
                'status_from' => $this::DEV_PRODUCAO,
                'status_to' => $this->get_list_statuses($statuses, $this::DEV_PRODUCAO),
                'name' => 'Tempo Médio de Solicitações em DEV - Produção',
                'time' => '',
                'options' => ''
            ),
            3 => array(
                'status_from' => $this::DEV_REANALISE,
                'status_to' => $this->get_list_statuses($statuses, $this::DEV_REANALISE),
                'name' => 'Tempo Médio de Solicitações em DEV - Reanalise',
                'time' => '',
                'options' => ''
            ),
            4 => array(
                'status_from' => $this::DEV_TESTE,
                'status_to' => $this->get_list_statuses($statuses, $this::DEV_TESTE),
                'name' => 'Tempo Médio de Solicitações em DEV - Teste',
                'time' => '',
                'options' => ''
            ),
            5 => array(
                'status_from' => 'primeiro_dev',
                'status_to' => [$this::DEV_ATENDIDO], //TEMPO MEDIO DE SOLICITAÇÕES EM DESENVOLVIMENTO
                'name' => 'Tempo Médio de Solicitações em Desenvolvimento',
                'time' => '',
                'options' => ''
            )
        );

        if ($this->input->post('from') && $this->input->post('to')) {
            $from = $this->input->post('from') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('from'))
                ->format('Y-m-d 00:00:00.000000') : '';

            $to = $this->input->post('to') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('to'))
                ->format('Y-m-d 23:59:59.000000') : '';

        } else {
            $from = \Carbon\Carbon::today()->subDays(30)->format('Y-m-d 00:00:00.000000');
            $to = \Carbon\Carbon::today()->format('Y-m-d 23:59:59.000000');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name',
                'time',
                'options',
            );
            $output = array(
                'draw' => 1,
                'iTotalRecords' => 6,
                'iTotalDisplayRecords' => 6,
                'aaData' => Array
                    (
                    )
            );

            $output = $output;
            $rResult = $status_media;

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= $aRow['name'];
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }
                    if ($aColumns[$i] == "time") {
                        $tempo = segundosParaLeituraHumana($this->dev_report_model->media_espera($from, $to, $aRow['status_from'], $aRow['status_to'])['total_minutos'] * 60);
                        $_data = '<p class = "label label-default" style="font-size: medium">';
                        $_data .= !empty($tempo) ? $tempo : 'Registro indisponível';
                        $_data .= '</p>';
                    }
                    if ($aColumns[$i] == "options") {
                        $status_to = str_replace('"', '\'', json_encode($aRow['status_to']));
                        $_data = '<div class="center">';
                        $_data .= '<button class="btn btn-success" name="status'.$aRow['status_from'].'" value="'.$aRow['status_from'].'" onclick="open_table(\'' . $aRow['status_from'] . '\', ' . $status_to . ', \'' . $aRow['name'] . '\'); return false;" style="margin: auto; vertical-align: middle">+</button>';
                        $_data .= '</div>';
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
//
        $data['from'] = !empty($this->input->post('from')) ? $this->input->post('from') : \Carbon\Carbon::today()->subDays(30)->format('Y-m-d');
        $data['to'] = !empty($this->input->post('to')) ? $this->input->post('to') : \Carbon\Carbon::today()->format('Y-m-d');

//        $data['espera_to_concluido'] = segundosParaLeituraHumana($this->dev_report_model->media_espera($from, $to, $this::DEV_ESPERA, [$this::DEV_ATENDIDO])['total_minutos'] * 60);
//        $data['espera_to_producao'] = segundosParaLeituraHumana($this->dev_report_model->media_espera($from, $to, $this::DEV_ESPERA, [$this::DEV_PRODUCAO])['total_minutos'] * 60);
//        $data['em_producao'] = segundosParaLeituraHumana($this->dev_report_model->media_espera($from, $to, $this::DEV_PRODUCAO, $aux_producao)['total_minutos'] * 60);
//        $data['em_teste'] = segundosParaLeituraHumana($this->dev_report_model->media_espera($from, $to, $this::DEV_TESTE, $aux_teste)['total_minutos'] * 60);
//
//        $data['aux_producao'] = json_encode($aux_producao);
//        $data['aux_teste'] = json_encode($aux_teste);


        $data['status_dev'] = $this->db
            ->like('name', 'DEV')
            ->get('tblticketstatus')->result_array();

        $this->load->view('admin/dev_report/view', $data);
    }


    public function open_table(){
        $status_from = $this->input->post('status_from');
        if(is_array($this->input->post('status_to')))
            $status_to = $this->input->post('status_to');
        else
            $status_to = array($this->input->post('status_to'));
        $from = !empty($this->input->post('from')) ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('from'))
            ->format('Y-m-d 00:00:00.000000') : \Carbon\Carbon::today()->subDays(30)->format('Y-m-d 00:00:00.000000');
        $to = !empty($this->input->post('to')) ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('to'))
            ->format('Y-m-d 23:59:59.000000') : \Carbon\Carbon::today()->format('Y-m-d 23:59:59.000000');
        $where = array(
            "tblticketstimestatus.datetime >=" => $from,
            "tblticketstimestatus.datetime <=" => $to,
            "tblticketstimestatus.statusid" => $this::DEV_ATENDIDO
        );

        $this->load->model('tickets_model');
        $tickets = $this->tickets_model->get_tickets_by_staffid('', $where);
        $tabela = '';
        $tabela .= "<div class='row'><div class='panel-body'><table class='table' id='table-resps'><thead>
                        <tr><th><b>#Ticket</b></th><th><b>Atendente</b></th><th><b>Tempo em Atendimento</b></th></tr></thead><tbody>";

        foreach ( $tickets as $resp)
        {
            $tempo = $this->dev_report_model->media_espera($from, $to, $status_from, $status_to, $resp['ticketid'], false);
            if(isset($tempo[0]))
                $tempo = $tempo[0];
//            var_dump($tempo);

            if($tempo['total_minutos'] == 0)
                continue;

            $tabela .= '<tr>
                    <td>
                        <div class="col-md-3">
                            <a target="_blank" href="'.APP_BASE_URL.'admin/tickets/ticket/'.$resp["ticketid"].'"> #' . $resp['ticketid'] . '</a>
                        </div>    
                    </td>
                    <td>
                        <div align="left" style="font-size: 13px;">
                            <div class="col-md-3">
                                <p>
                                    <a target="_blank" href="'.APP_BASE_URL.'admin/profile/'.$resp["staffid"].'">'. $resp['firstname'] .'</a>
                                </p>
                                <p class="text-muted">
                                    Colaborador
                                    <br>
                                    
                                </p>
                            </div>
                        </div>
                    </td>
                    <td data-order="' . $tempo['total_minutos'] . '">
                        <div class="col-md-9" align="left" style="font-size: 13px;">
                            <div class="clearfix"></div>
                            <div class="tc-content">
                                
                            </div>
                            <br>
                            <p>'.
                                segundosParaLeituraHumana($tempo['total_minutos'] * 60)
                            .'</p>
                            
                        </div>
                        
                    </td>
                </tr>';
//            $tabela .= '<tr><td colspan="2"><span class="pull-left" style="font-size: 12px;">Postado '.date("d/m/Y h:i:s",strtotime($resp["date"])).'</span></td></tr>';
//            $tabela .= '<tr class="hide"></tr><td colspan="3"><span class="pull-left" style="font-size: 12px;">Postado '.date("d/m/Y h:i:s",strtotime($resp["date"])).'</span></td><tr><tr class="hide"></tr></tr>';
        }
        $tabela .= "</tbody></table></div></div>";
        echo $tabela;
    }


    public function get_list_statuses($statuses, $no_status){

        $resultado = [];
        foreach ($statuses as $status){
            if($status['ticketstatusid'] != $no_status)
                array_push($resultado, $status['ticketstatusid']);
        }

        return $resultado;
    }



}