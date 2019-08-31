<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 27/12/2017
 * Time: 15:44
 */
if($vehicle->chave_detran != "" || $vehicle->chave_detran != null)
    echo "<style>".utf8_encode(file_get_contents("http://internet.detran.to.gov.br/Estilos/EstiloDetranNet.css"))."</style>";
init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!--Cabeçario-Informações-->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body no-padding-bottom">
                        <a href="<?php echo admin_url("fleet/vehicles"); ?>" class="btn btn-info"><i class="fa fa-angle-double-left fa-1x" aria-hidden="true"></i>&nbsp;&nbsp;Voltar</a>
                        <a class="btn btn-info" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            Distância
                        </a>
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body">
                                <hr>
                                <div class="staff_logged_time">
                                    <div class="col-md-3">
                                        <?php echo render_date_input('date_from', 'Data de Início', _d('')); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_date_input('date_to', 'Data de Fim', _d('')); ?>
                                    </div>
                                    <div class="col-md-3" style="text-align: center; padding: 27px">
                                        <button class="btn btn-success" onclick="filter()"
                                                style="margin: auto; vertical-align: middle"><?php echo _l('Filtrar'); ?></button>
                                    </div>
                                    <div class="col-md-3ths col-sm-3 col-xs-12 total-column">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                                <h3 class="text-muted _total" id="distanciaperiodo">
                                                    Selecione a data
                                                </h3>
                                                <span class="staff_logged_time_text text-success">Distância no Período</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">

                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-heading">
                        <div class="staff_logged_time">
                            <div class="col-md-3ths col-sm-3 col-xs-12 total-column">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h3 class="text-muted _total">
                                            <?php echo $vehicle->kmatual." Km"; ?>
                                        </h3>
                                        <span class="staff_logged_time_text text-success">Km Atual</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3ths col-sm-3 col-xs-12 total-column">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h3 class="text-muted _total">
                                            <?php
                                                $total = 0;
                                                foreach ($outputs as $output)
                                                {
                                                    $total += $output['km_final']-$output['km_inicial'];
                                                }
                                                if($vehicle->inuse == 0)
                                                    echo $total." Km";
                                                else
                                                    echo "<h6>Veículo em uso. Finalizar Para mostrar resumo.</h6>";
                                            ?>
                                        </h3>
                                        <span class="staff_logged_time_text text-info">Distancia Total</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3ths col-sm-3 col-xs-12 total-column">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h3 class="text-muted _total">
                                            <?php
                                                $total = 0;
                                                $now = \Carbon\Carbon::now();
                                                foreach ($outputs as $output)
                                                {
                                                    $dt = \Carbon\Carbon::parse($output['data']);
                                                    if($dt->year == $now->year && $dt->month == $now->month)
                                                        $total += $output['km_final']-$output['km_inicial'];
                                                }
                                                if($vehicle->inuse == 0)
                                                    echo $total." Km";
                                                else
                                                    echo "<h6>Veículo em uso. Finalizar Para mostrar resumo.</h6>";
                                            ?>
                                        </h3>
                                        <span class="staff_logged_time_text text-success">Distancia Neste Mês</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3ths col-sm-3 col-xs-12 total-column">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h3 class="text-muted _total">
                                             Km/L
                                        </h3>
                                        <span class="staff_logged_time_text text-info">Desempenho</span>
                                    </div>
                                </div>
                            </div>
<!--                            <div class="col-md-5ths col-sm-6 col-xs-12 total-column">-->
<!--                                <div class="panel_s">-->
<!--                                    <div class="panel-body">-->
<!--                                        <h3 class="text-muted _total">-->
<!--                                            600 Km-->
<!--                                        </h3>-->
<!--                                        <span class="staff_logged_time_text text-success">Nesta Semana</span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!--Fim-Cabeçario-Informações-->
            <!--Informações-Veiculo-Resumida-->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            #<?php echo $vehicle->vehicleid." ".$vehicle->descricao; ?><small> - <?php echo isset($outputs[0]['datetime_final'])?"Ultima atividade: ".\Carbon\Carbon::parse($outputs[0]['datetime_final'])->diffForHumans() : "Nenhum uso registrado.";?></small>
                        </h4>
                    </div>
                </div>
            </div>
            <!--Fim-Informações-Veiculo-Resumida-->
            <!--Configuração-Veiculo-->
            <div class="col-md-5" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
                                    Definições
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_detran" aria-controls="tab_detran" role="tab" data-toggle="tab">
                                    Detran
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
                                <?php echo form_open(admin_url('fleet/vehicles/update_vehicle'),array("id"=>"form-new-vehicles"))?>
                                <div class="col-md-6">
                                    <input type="hidden" name="vehicleid" value="<?php echo $vehicle->vehicleid;?>">
                                    <?php echo render_input('vehicleid','Código do Veículo',$vehicle->vehicleid,'',array("disabled"=>"true"));?>
                                    <?php echo render_input('finalplaca','Final Placa <span class="bold text-danger">*</span>',$vehicle->finalplaca);?>
                                    <?php echo render_input('medelo','Modelo <span class="bold text-danger">*</span>',$vehicle->medelo);?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('placa','Placa <span class="bold text-danger">*</span>',$vehicle->placa); ?>
                                    <?php echo render_select('marca',array(array("tipo"=>"AGRALE"),
                                        array("tipo"=>"ALFA ROMEO"),
                                        array("tipo"=>"AM GENERAL"),
                                        array("tipo"=>"ASIA"),
                                        array("tipo"=>"ASTON MARTIN"),
                                        array("tipo"=>"AUDI"),
                                        array("tipo"=>"BENTLEY"),
                                        array("tipo"=>"BMW"),
                                        array("tipo"=>"CHANA"),
                                        array("tipo"=>"CHERY"),
                                        array("tipo"=>"CHEVROLET"),
                                        array("tipo"=>"CHRYSLER"),
                                        array("tipo"=>"CITROEN"),
                                        array("tipo"=>"CROSS LANDER"),
                                        array("tipo"=>"DAEWOO"),
                                        array("tipo"=>"DAIHATSU"),
                                        array("tipo"=>"DODGE"),
                                        array("tipo"=>"DS"),
                                        array("tipo"=>"EFFA HAFEI"),
                                        array("tipo"=>"FERRARI"),
                                        array("tipo"=>"FIAT"),
                                        array("tipo"=>"FORD"),
                                        array("tipo"=>"GEELY"),
                                        array("tipo"=>"HAFEI"),
                                        array("tipo"=>"HONDA"),
                                        array("tipo"=>"HYUNDAI"),
                                        array("tipo"=>"IVECO"),
                                        array("tipo"=>"JAC"),
                                        array("tipo"=>"JAGUAR"),
                                        array("tipo"=>"JEEP"),
                                        array("tipo"=>"JINBEI"),
                                        array("tipo"=>"JPX"),
                                        array("tipo"=>"KIA"),
                                        array("tipo"=>"LADA"),
                                        array("tipo"=>"LAMBORGHINI"),
                                        array("tipo"=>"LAND ROVER"),
                                        array("tipo"=>"LEXUS"),
                                        array("tipo"=>"LIFAN"),
                                        array("tipo"=>"LOTUS"),
                                        array("tipo"=>"MAHINDRA"),
                                        array("tipo"=>"MASERATI"),
                                        array("tipo"=>"MAZDA"),
                                        array("tipo"=>"MERCEDES"),
                                        array("tipo"=>"MINI"),
                                        array("tipo"=>"MITSUBISHI"),
                                        array("tipo"=>"NISSAN"),
                                        array("tipo"=>"PAGANI"),
                                        array("tipo"=>"PEUGEOT"),
                                        array("tipo"=>"PORSCHE"),
                                        array("tipo"=>"RAM"),
                                        array("tipo"=>"RELY"),
                                        array("tipo"=>"RENAULT"),
                                        array("tipo"=>"ROLLS ROYCE"),
                                        array("tipo"=>"SANTANA"),
                                        array("tipo"=>"SEAT"),
                                        array("tipo"=>"SHINERAY"),
                                        array("tipo"=>"SMART"),
                                        array("tipo"=>"SPYKER"),
                                        array("tipo"=>"SSANGYONG"),
                                        array("tipo"=>"SUBARU"),
                                        array("tipo"=>"SUZUKI"),
                                        array("tipo"=>"TAC"),
                                        array("tipo"=>"TOYOTA"),
                                        array("tipo"=>"TROLLER"),
                                        array("tipo"=>"VOLKSWAGEN"),
                                        array("tipo"=>"VOLVO")),array("tipo","tipo"),'Marca <span class="bold text-danger">*</span>',$vehicle->marca); ?>
                                    <?php echo render_select('tipo',array(
                                    array("tipo"=>"Automóvel"),
                                    array("tipo"=>"Bicicleta"),
                                    array("tipo"=>"Bonde"),
                                    array("tipo"=>"Caminhonete"),
                                    array("tipo"=>"Caminhão"),
                                    array("tipo"=>"Camioneta"),
                                    array("tipo"=>"Carroça"),
                                    array("tipo"=>"Carro de mão"),
                                    array("tipo"=>"Charrete"),
                                    array("tipo"=>"Ciclomotor"),
                                    array("tipo"=>"Microônibus"),
                                    array("tipo"=>"Motocicleta"),
                                    array("tipo"=>"Motoneta"),
                                    array("tipo"=>"Motoneta"),
                                    array("tipo"=>"Quadricíclo"),
                                    array("tipo"=>"Reboque"),
                                    array("tipo"=>"Trator"),
                                    array("tipo"=>"Triciclo"),
                                    array("tipo"=>"Ônibus")),array("tipo","tipo"),'Tipo de Veículo <span class="bold text-danger">*</span>',$vehicle->tipo); ?>
                                </div>
                                <div class="col-md-12">
                                    <?php echo render_textarea('descricao','Descrição <span class="bold text-danger">*</span>',$vehicle->descricao);?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('codinternemp','Cód Interno Empresa',$vehicle->codinternemp) ; ?>
                                    <?php echo render_input('kmatual','Km Atual <span class="bold text-danger">*</span>',$vehicle->kmatual) ; ?>
                                    <?php echo render_input('chassi','Chassi',$vehicle->chassi) ; ?>
                                    <?php echo render_input('eixos','Eixos',$vehicle->eixos) ; ?>
                                    <?php echo render_select('proprietario',$staff,array("staffid","name"),'Proprietário/Arredatário <span class="bold text-danger">*</span>',$vehicle->proprietario); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $anos = array();for($i = Carbon\Carbon::now()->year; $i > 1970 ; $i--){array_push($anos,array("ano"=>$i));}?>
                                    <?php echo render_select('ano',$anos,array("ano","ano"),'Ano  <span class="bold text-danger">*</span>',$vehicle->ano,array("required"=>"true")); ?>
<!--                                    --><?php //echo render_select('categoria',array(),array(),'Categoria'); ?>
                                    <?php echo render_input('renavan','Renavam',$vehicle->renavan); ?>
                                    <?php echo render_input('cor','Cor',$vehicle->cor); ?>
                                    <?php echo render_date_input('venclicenci','Venc Licenciamento',$vehicle->venclicenci); ?>
                                </div>
                                <div class="col-md-12">
                                    <?php echo render_input('alenado','Alienado(Banco)',$vehicle->alenado); ?>
                                    <?php echo render_input('locallicenci','Local Licenciamento',$vehicle->locallicenci); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('valorveic','Valor Veículo',$vehicle->valorveic); ?>
                                    <?php echo render_date_input('datainicicontr','Data Inicio Contrato <span class="bold text-danger">*</span>',$vehicle->datainicicontr); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('numcontrato','Número Contrato',$vehicle->numcontrato); ?>
                                    <?php echo render_date_input('datafimcontr','Data Fim Contrato <span class="bold text-danger">*</span>',$vehicle->datafimcontr); ?>
                                </div>
                                <div class="col-md-12">
                                    <?php echo render_textarea('observacao','Observação',$vehicle->observacao); ?>
                                </div>
                                <button type="submit" class="btn btn-success pull-right">Atualizar</button>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_detran">
                                <div class="col-md-12">
                                    <?php echo render_input('chave_detran','Chave Site Detran',$vehicle->chave_detran); ?>
                                    <?php echo render_input('cpfcnpjveiculo','CPF/CNPJ do veículo',$vehicle->cpfcnpjveiculo); ?>
                                </div>
                                <button type="submit" class="btn btn-success pull-right">Atualizar</button>
                                <?php echo form_close();?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 small-table-right-col">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <?php if(count($outputs) > 0){ ?>
                                <div class="table-responsive">
                                    <table class="table dt-table table-striped">
                                        <thead>
                                        <th>#</th>
                                        <th>Motivo</th>
                                        <th>Colaborador</th>
                                        <th>Distancia</th>
                                        <th>Tempo</th>
                                        <th>Data</th>
<!--                                        <th>Opções</th>-->
                                        </thead>
                                        <tbody>
                                        <?php foreach($outputs as $fleetout){ ?>
                                            <tr>
                                                <td>
                                                    <?php echo $fleetout["idsaida"]; ?>
                                                </td>
                                                <td>
                                                    <?php echo $fleetout["motivo"]; ?>
                                                </td>
                                                <td>
                                                    <?php echo get_staff_full_name($fleetout['staffid']); ?>
                                                </td>
                                                <td>
                                                    <?php echo $fleetout["km_final"]-$fleetout['km_inicial']; ?> Km
                                                </td>
                                                <td>
                                                    <?php
                                                    if($fleetout['datetime_final'] != NULL) {
                                                        $uhourin = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fleetout['datetime_inicial'])->timestamp;
                                                        $uhouren = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fleetout['datetime_final'])->timestamp;
                                                        $time = $uhouren - $uhourin;
                                                        $now = \Carbon\Carbon::now()->toDateString();
                                                        $now = \Carbon\Carbon::createFromFormat('Y-m-d', $now)->timestamp;
                                                        echo \Carbon\Carbon::createFromTimeStampUTC($time + $now)->diffForHumans(null, true);
                                                        //                                                        echo \Carbon\Carbon::createFromTimeStampUTC($time)->diffForHumans(null, true);
                                                    }
                                                    else
                                                        echo "Em uso";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php $date = date_create($fleetout["data"]); echo date_format($date, 'd/m/Y'); ?>
                                                </td>
<!--                                                <td>-->
<!--                                                    <a onclick="see_out(--><?php //echo $fleetout['idsaida']; ?><!--)" class="btn btn-default btn-icon">-->
<!--                                                        <i class="fa fa-arrow-right"></i>-->
<!--                                                    </a>-->
<!--                                                    <a onclick="alert('abrir resumo - '+--><?php //echo $fleetout['idsaida']; ?><!--)" class="btn btn-info btn-icon">-->
<!--                                                        <i class="fa fa-newspaper-o"></i>-->
<!--                                                    </a>-->
<!--                                                </td>-->
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <p class="no-margin">Nenhuma saída registrada.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <?php if(count($supplys) > 0){ ?>
                                <div class="table-responsive">
                                    <table class="table dt-table table-striped">
                                        <thead>
                                        <th>#</th>
                                        <th>Posto</th>
                                        <th>Preço/Litro</th>
                                        <th>Valor Total</th>
                                        <th>Data</th>
                                        <!--                                        <th>Opções</th>-->
                                        </thead>
                                        <tbody>
                                        <?php foreach($supplys as $supply){ ?>
                                            <tr>
                                                <td>
                                                    <?php echo $supply["supplyid"]; ?>
                                                </td>
                                                <td>
                                                    <?php echo $supply["posto"]; ?>
                                                </td>
                                                <td>
                                                    R$ <?php echo $supply["precoporlitro"]?>
                                                </td>
                                                <td>
                                                    R$ <?php echo $supply["valortotal"]?>
                                                </td>
                                                <td>
                                                    <?php $date = date_create($supply["data"]); echo date_format($date, 'd/m/Y'); ?>
                                                </td>
<!--                                                <td>-->
<!--                                                    <a onclick="see_out(--><?php //echo $fleetout['idsaida']; ?><!--)" class="btn btn-default btn-icon">-->
<!--                                                        <i class="fa fa-arrow-right"></i>-->
<!--                                                    </a>-->
<!--                                                    <a onclick="alert('abrir resumo - '+--><?php //echo $fleetout['idsaida']; ?><!--)" class="btn btn-info btn-icon">-->
<!--                                                        <i class="fa fa-newspaper-o"></i>-->
<!--                                                    </a>-->
<!--                                                </td>-->
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <p class="no-margin">Nenhum abastecimento registrado.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <?php
                            if($vehicle->chave_detran != "" || $vehicle->chave_detran != null) {
//                                $postdata = http_build_query(array('hdChave' => $vehicle->chave_detran, 'txtDocPrincipal' => $vehicle->cpfcnpjveiculo, 'oculto' => 'C', 'btOk' => 'Consultar'));
//                                $opts = array('http' => array('method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
//                                $context = stream_context_create($opts);
//                                $result = file_get_contents('http://internet.detran.to.gov.br/VerificaVeiculo.asp?Chave=' . $vehicle->chave_detran, false, $context);
//                                $result = utf8_encode($result);
//                                $result = str_replace("../../../Imagens/", "http://internet.detran.to.gov.br/Imagens/", $result);
//                                $result = str_replace("../../../SharedASP/", "http://internet.detran.to.gov.br/SharedASP/", $result);

                                echo '<iframe src="http://internet.detran.to.gov.br/VerificaVeiculo.asp?Chave='.$vehicle->chave_detran.'" style="margin: 0px; width: 100%; heigth: 100%;" height="30%" width="100%"></iframe>'; //Solução para captcha
                            }
                            else
                                echo "Cadastrar chave detran.";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail();?>
<script>
    function filter() {
        var comando = "";
        if(($("#date_from").val() != "" && $("#date_from").val() != null) || ($("#date_to").val() != "" && $("#date_to").val() != null)) {
            comando += "?date=true";
            if ($("#date_from").val() != "" && $("#date_from").val() != null)
                comando += "&date_from=" + $("#date_from").val();
            else
                comando += "&date_from=false";
            if ($("#date_to").val() != "" && $("#date_to").val() != null)
                comando += "&date_to=" + $("#date_to").val();
            else
                comando += "&date_to=false";

            $.get(window.location.href + comando, function (data) {
                data = JSON.parse(data);
                if(data != null) {
                    $("#distanciaperiodo").html(data+" Km");
                }
                else {
                    $("#distanciaperiodo").html("Sem Registros");
                }
            });
            $('.table-media').DataTable().ajax.reload();
        }
    }
</script>
