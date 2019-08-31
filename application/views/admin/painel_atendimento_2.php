<?php init_painel_head(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Painel de Atedimento</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        /* 0000FF*/
        hr {
            width: 100%;
            max-width: 100%;
            background: <?= get_option("ticket_main_color")?>;
            height: 1px;
            border: 0;
            margin
        }
        h1, h2 {
            text-transform: uppercase;
        }
        h1 {
            margin: 0;
            background: <?= get_option("ticket_main_color")?>;
            text-align: center;
            padding: 5px 0;
            color: #fff;
        }
        h2 {
            font-size: 25px;
            color: <?= get_option("ticket_main_color")?>;
            padding-bottom: 5px;
            border-bottom: 1px solid <?= get_option("ticket_main_color")?>;
            margin: 10px 0;
        }
        h2 > span {
            color: #2d2d2d;
        }
        table > tbody > tr > td {
            font-size: medium;
            font-weight: bold;
        }

        tr {
            width: 95%;
            display: inline-table;
            /*display: inline-table;*/
        }

        table {
            /*height: 600px;*/
            height: 80vh; /* 80% da view-width */
        }

        tbody {
            overflow-y: scroll;
            height: 85%; /*550px; */
            width: 95%;
            /*position: absolute;*/
            text-align: left;
        }


        .fixed_header tbody{
            display: block;
            overflow: auto;
            height: 75vh;
            width: 95%;
        }

        .fixed_header thead tr{
            display:block;
        }

        .fixed_header_atend tbody{
            display: block;
            overflow: auto;
            height: 30vh;
            width: 95%;
        }

        .fixed_header_atend thead tr{
            display:block;
        }

        /*#atend-interno > table {*/
            /*!*height: 300px;*!*/
            /*height: 30vh;*/
        /*}*/

        #reanalise > table {
            height: 25vh;
        }


        /*#atendimento-interno > tbody {*/
            /*height: 85%;*/
            /*overflow-y: scroll;*/
            /*overflow-x: hidden;*/
            /*width: 95%;*/
            /*position: absolute;*/
        /*}*/

        /*#atendimento-externo > tbody {*/
            /*height: 85%;*/
            /*overflow-y: scroll;*/
            /*overflow-x: hidden;*/
            /*width: 95%;*/
            /*position: absolute;*/
        /*}*/


        #suporte-pendente-table > td {
            padding: 3px 15px;
            width: 60px;
            /*border: 1px solid black;*/
        }

        .legenda {
            padding: 10px;
            position: fixed;
            bottom: 0;
            right: 0;
            /*font-size: 20px;*/
        }
        .label {
            text-transform: uppercase;
        }

        .atendimento > tr > td > .label {
            padding: 15px 15px 15px 15px;
        }
        .atendimento-informacoes {
            font-size: 15px;
        }
        .line {
            width: 100%;
            height: 2px;
            margin: 2px 0 2px 0;
            background: <?= get_option("ticket_main_color")?>;
            float: left;
        }
        #suporte-pendente-table > :last-child{
            margin-bottom: 3em;
        }
        #atendentes-disponiveis {
            list-style: none;
            padding: 0;
        }
        #atendentes-disponiveis > li {
            display: inline-block;
            float: left;
            padding: 5px;
            font-weight: bold;
        }
        .atendentes-disponiveis-title {
            display: inline-block;
            float: left;
            padding: 5px;
            font-weight: bold;
            font-size: 20px;
            color: <?= get_option("ticket_main_color")?>;
        }

        .max-td {
            min-width: 80px;
            max-width: 80px;
        }

        .td-tags {
            width: 80px;
        }

        .senha {
            position: fixed;
            bottom: 0;
            left: 0;
            padding: 10px 30px 10px 10px;
            font-size: 25px;
            font-weight: bold;
        }
        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            /*border-top: 1px solid <?= get_option("ticket_main_color")?>;*/
        }

        @keyframes fade {
            from { opacity: 1.0; }
            50% { opacity: 0.4; }
            to { opacity: 1.0; }
        }

        @-webkit-keyframes fade {
            from { opacity: 1.0; }
            50% { opacity: 0.4; }
            to { opacity: 1.0; }
        }

        .alert {
            animation:fade 1000ms infinite;
            -webkit-animation:fade 1000ms infinite;
            background-color: #f0ad4e;
        }
        .limit {
            animation:fade 1000ms infinite;
            -webkit-animation:fade 1000ms infinite;
            background-color: #d9534f;
        }
    </style>
</head>
<body>
<h1>Painel de Atendimento
    <div class="pull-left" style="font-size: 90%;">
        SENHA: <span id="senha_text"></span>
    </div>
    <div class="pull-right" style="font-size: 90%;">
        Média dos Atendimentos:
        <span class = "label-<?php echo ($featured > 3) ? 'success' : 'danger'; ?>" style="font-size: inherit; color: inherit">
            <?php echo number_format($featured, 2); ?>
        </span>
    </div>
</h1>
<div class="container-fluid">
    <div class="row <?php if(is_partner() || (isset($_SESSION['partner_filter']) && $_SESSION['partner_filter'] != '')) echo 'hide'; ?>" >
        <div class="col-md-12">
            <span class="atendentes-disponiveis-title">ANALISTAS DISPONÍVES:</span>
            <ul id="atendentes-disponiveis">
            </ul>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">

            <h2>Em espera (<span id="em_espera_quantidade"></span>)</h2>

                <table class="table fixed_header">
                    <thead>
                    <tr>
                        <th class="col-md-2">
                            PRIORIDADE:
                        </th>
                        <th class="col-md-6">
                            CLIENTE:
                        </th>

<!--                        <th class="text-right col-md-4">-->
<!--                            TEMPO EM ESPERA-->
<!--                        </th>-->
                    </tr>
                    </thead>

                    <tbody id="em_espera_interno" class="atendimento">

                    </tbody>
                </table>

<!--            <div class='line'></div>-->
<!---->
<!--            <table class="table table-responsive">-->
<!--                <tbody id="em_espera_externo">-->
<!---->
<!--                </tbody>-->
<!--            </table>-->
        </div>

        <div class="col-md-4">
<!--            <div class="row">-->
                <div class="col-md-12" id="atend-interno">
                    <h2>Em atendimento (<span id="atendimento_interno_quantidade"></span>)</h2>

                    <table class="table fixed_header">
                        <thead>
                        <tr>
                            <th class="col-md-1">
                                TICKET:
                            </th>
                            <th>
                                CLIENTE:
                            </th>
                        </tr>
                        </thead>
                        <tbody id="atendimento-interno" class='atendimento panel-group'>
                        </tbody>
                    </table>
                </div>

<!--                <div class="col-md-12" id="reanalise" >-->
<!--                    <h2>Em atendimento Externo (<span id="atendimento_externo_quantidade"></span>)</h2>-->
<!---->
<!--                    <table class="table fixed_header_atend">-->
<!--                        <tbody id="atendimento-externo" class="atendimento">-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
        </div>

        <div class="col-md-4">
            <h2>Suporte Implantação (<span id="suporte_pendente_quantidade"></span>)</h2>
            <table class="table fixed_header" id="suporte-pendente-table">
                <thead>
                <tr>
                    <th class="col-md-1">
                        TICKET:
                    </th>
                </tr>
                </thead>
                <tbody id="suporte-pendente" class='atendimento panel-group'>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="senha hide">
    SENHA: <span id="senha_text">12345678</span>
</div>
<div class="legenda">

    <span class="label label-danger">Urgente</span>
    <span class="label label-warning">Alto</span>
    <span class="label label-success">Médio</span>
    <span class="label label-primary">Baixo</span>
    <span class="label label-info">Atualização</span>
    <span class="label label-default">Agendado</span>
    <span class="label label-pending">Pendente</span>

</div>

<audio id="soundpanel">
    <source src="../../definite.ogg" type="audio/ogg" />
</audio>
<audio id="soundpanel1">
    <source src="../../alert2.ogg" type="audio/ogg" />
</audio>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<?php init_tail(); ?>
<script>
    // Enable pusher logging - don't include this in production
    const ATENDIMENTO_INTERNO = 2;
    const ATENDIMENTO_EXTERNO = 27;
    var sounds = {};
    var tocar = false;
    sounds.new = document.getElementById("soundpanel");
    sounds.t2 = document.getElementById("soundpanel1");
    sounds.t3 = document.getElementById("soundpanel3");

    var ticket_waiting_limit_sound = <?php echo get_option("ticket_waiting_limit_sound"); ?>;
    var ticket_waiting_alert_sound = <?php echo get_option("ticket_waiting_alert_sound"); ?>;
    var ticket_waiting_limit_sound_type = <?php echo get_option("ticket_waiting_limit_sound_type"); ?>;
    var ticket_waiting_alert_sound_type = <?php echo get_option("ticket_waiting_alert_sound_type"); ?>;
    var pertubacao_notify = <?php echo get_option("pertubacao_notify"); ?>;

    var tick = [];
    var tick_ = [];
    // Pusher.logToConsole = true;
    var pusher = new Pusher('<?php echo get_option('pusher_app_key');?>', {
        cluster: '<?php echo get_option('pusher_cluster');?>',
        encrypted: true
    });

    var channel = pusher.subscribe('painel');
    channel.bind('atualizar', function(data) {
        getData();
        // console.log("atualizando pelo pusher");
    });

    function getData() {
        axios.get('<?= ciroute("painel.atendimento.dados")?>')
            .then(function (response) {
                quantidade(response.data);
                limparTela();
                tocar = false;
                // console.log(response.data.em_espera);

                senha(response.data.senha);

               response.data.em_espera.forEach(function (data) {
                   if(data.limit == true)
                       tocar_(data.ticketid,"limit");
                   else if(data.alerta == true)
                       tocar_(data.ticketid,"alert");

                   em_espera(data, 'em_espera_interno');
                   sound(data.ticketid,"new");
                });

                response.data.atendimento_interno.forEach(function (data) {
                    atendimento(data, "atendimento-interno");
                });
                // response.data.atendimento_externo.forEach(function (data) {
                //     atendimento(data, "atendimento-externo");
                // });
                response.data.atendentes_disponiveis.forEach(function (data) {
                    atendentes_disponiveis(data);
                });
                response.data.suporte_pendente.forEach(function (data){
                    suporte_pendente(data, "suporte-pendente");
                });
                if(pertubacao_notify == 1)
                    tocar = true;
            });
    }
    setInterval(function () {
        getData();
    }, <?php echo get_option("painel_refresh_time"); ?>000);
    setInterval(function () {
        if(tocar == true)
            sounds.t2.play();
    }, 120000);

    function tocar_(ticketid,tipo) {
        if(tipo == "limit" && ticket_waiting_limit_sound == 1) {
            if (ticket_waiting_limit_sound_type == 1) {
                sound(ticketid,"limit");
                tocar = false;
            }
            else if (ticket_waiting_limit_sound_type == 2) {
                tocar = true;
            }
            else if (ticket_waiting_limit_sound_type == 3) {
                // sounds.t3.loop = true;
                sounds.new.play();
                tocar = false;
            }
        }
        else if(tipo == "alert" && ticket_waiting_alert_sound == 1) {
            if (ticket_waiting_alert_sound_type == 1) {
                sound(ticketid,"alert");
                tocar = false;
            }
            else if (ticket_waiting_alert_sound_type == 2) {
                tocar = true;
            }
            else if (ticket_waiting_alert_sound_type == 3) {
                // sounds.t3.loop = true;
                sounds.new.play();
                tocar = false;
            }
        }
    }

    function sound(ticketid,type) {
        if (type == "new") {
            if (jQuery.inArray(ticketid, tick) == -1) {
                tick.push(ticketid);
                sounds.new.play();
            }
        }
        else if (type == "limit") {
            if (jQuery.inArray(ticketid, tick_) == -1) {
                tick_.push(ticketid);
                sounds.t2.play();
            }
        }
        else if (type == "alert") {
            if (jQuery.inArray(ticketid, tick_) == -1) {
                tick_.push(ticketid);
                sounds.t2.play();
            }
        }
    }

    /// funcoes que criam o HTML
    function limparTela() {
        // document.getElementById("em_espera_externo").innerHTML = "";
        document.getElementById("em_espera_interno").innerHTML = "";
        // document.getElementById("atendimento-externo").innerHTML = "";
        document.getElementById("atendimento-interno").innerHTML = "";
        document.getElementById("atendentes-disponiveis").innerHTML = "";
        document.getElementById("suporte-pendente").innerHTML = "";
    }
    function senha(senha) {
        document.getElementById("senha_text").innerHTML = senha;
    }
    function em_espera(data, element) {
        let analista = 'Tempo Total: ';

        if (!!data.assigned.firstname){
            analista = 'Analista: ' + data.assigned.firstname;
        }

        var html = "<tr";
        if(data.limit == true) {
            html += " class='limit'";
        }
        else if(data.alerta == true)
            html += " class='alert'";
        html += "><td class='td-tags'>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'><div class='col-md-12'>";
        if(data.is_externo == 1){
            html += "<a target='_blank' href='" + admin_url + "tickets/ticket/" + data.ticketid + "'>@" + data.company + "</a>";
        }else{
            html += "<a target='_blank' href='" + admin_url + "tickets/ticket/" + data.ticketid + "'>" + data.company + "</a>";
        }
        html += "<span class='pull-right' style='min-width: 15vh; text-align: right'>" + data.date_espera + "</span></div>";
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>" + analista + "</b>" ;
        html += "<span class='pull-right'>" + data.date + "</span></div>";
        html += "<div class='col-md-12 atendimento-informacoes'>";
        html += "Última resposta: " + data.lastreply;
        html += "</div>";
        html += "</div>";
        html += "</td>";
        html += "</tr>";
        var espera = document.getElementById(element);
        espera.innerHTML += html;
    }

    function atendimento(data, idElement) {
        let tipoAttend = 'Interno';
        if (data.status == ATENDIMENTO_EXTERNO)
            tipoAttend = 'Externo';

        var html = "<tr";
        if(data.limit_att == true) {
            html += " class='limit' style='color:white;'";
        }
        else if(data.alerta_att == true)
            html += " class='alert' style='color:white;'";
        html += "><td class='td-tags'>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'>";
        if(data.is_externo == 1){
            html += "<div class='col-md-9' ><a target='_blank' href='" + admin_url + "tickets/ticket/" + data.ticketid + "'>@" + data.company + "</a></div>"
        }else{
            html += "<div class='col-md-9' ><a target='_blank' href='" + admin_url + "tickets/ticket/" + data.ticketid + "'>" + data.company + "</a></div>"
        }
        html += "<div class='col-md-3 text-right'><span class='badge badge-info'>" + tipoAttend + "</span></div>";
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>Analista: </b>" + data.firstname.toUpperCase();
        html += "<span class='pull-right'>" + data.time + "</span></div>";
        html += "<div class='col-md-12 atendimento-informacoes'>";
        html += "Última resposta: " + data.lastreply;
        html += "</div>";
        html += "</div></td>";
        html += "</tr>";
        var atendimento = document.getElementById(idElement);
        atendimento.innerHTML += html;
    }
    function suporte_pendente(data, idElement) {
        var html = "<tr>";
        html += "<td class='td-tags'>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'>";
        html += "<div class='col-md-12'><a target='_blank' href='" + admin_url + "tickets/ticket/" + data.ticketid + "'>" + data.company + "</a></div>"
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>Analista: </b>" + data.firstname.toUpperCase();
        html += "<span class='pull-right'>" + data.time + "</span></div>";
        html += "<div class='col-md-12 atendimento-informacoes'>";
        html += "Última resposta: " + data.lastreplyAux;
        html += "</div>";
        html += "</div></td>";
        html += "</tr>";
        var atendimento = document.getElementById(idElement);
        atendimento.innerHTML += html;
    }
    function atendentes_disponiveis(data) {
        var  html = "<li>" + data.firstname + "</li><li>|</li>";
        var atendentes_disponiveis = document.getElementById("atendentes-disponiveis");
        atendentes_disponiveis.innerHTML += html;
    }
    function quantidade(data) {
        //length
        var em_espera = 0;
        //em_espera += data.em_espera.hasOwnProperty(0) ? data.em_espera[0].length : 0;
        //em_espera + data.em_espera.hasOwnProperty(1) ? data.em_espera[1].length : 0;
        if (!!data) {
            em_espera += data.em_espera.length ? data.em_espera.length : 0;
            document.getElementById("em_espera_quantidade")
                .innerHTML = em_espera;
            document.getElementById("atendimento_interno_quantidade")
                .innerHTML = data.atendimento_interno.length;
            // document.getElementById("atendimento_externo_quantidade")
            //     .innerHTML = data.atendimento_externo.length;
            document.getElementById("suporte_pendente_quantidade")
                .innerHTML = data.suporte_pendente.length;
            //document.getElementById("atendentes_disponiveis_quantidade")
            //  .innerHTML = data.atendentes_disponiveis.length;
        }
    }
    function create_tags(value, priority, subject) {
        let label = [
            "danger",
            "warning",
            "success",
            "primary",
            "info",
            "default",
            "pending",
        ];
        label = label[priority - 1];
        return "<div data-toggle='tooltip' data-placement='right'" +
            " data-title='"+ subject + "' class='label label-" + label + " center-block max-td'>" + value + "</div>";
    }

    getData();
</script>

</body>
</html>