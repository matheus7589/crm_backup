<?php init_painel_head(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Painel Desenvolvimento</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        /* 0000FF*/
        hr {
            width: 100%;
            max-width: 100%;
            background: <?= get_option("task_main_color")?>;
            height: 1px;
            border: 0;
            margin
        }

        h1, h2 {
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            background: <?= get_option("task_main_color")?>;
            text-align: center;
            padding: 5px 0;
            color: #fff;
        }

        h2 {
            font-size: 25px;
            color: <?= get_option("task_main_color")?>;
            padding-bottom: 5px;
            border-bottom: 1px solid<?= get_option("task_main_color")?>;
            margin: 10px 0;
        }

        h2 > span {
            color: #2d2d2d;
        }

        table > tbody > tr > td {
            font-size: medium;
            font-weight: bold;
        }

        .legenda {
            padding: 10px;
            position: fixed;
            bottom: 0;
            right: 0;
            /*font-size: 20px;*/
        }

        .btn {
            text-transform: uppercase;
            /*max-width: 70px;*/
        }

        .atendimento > tr > td > .label {
            padding: 15px 15px 15px 15px;
        }

        .atendimento-informacoes {
            font-size: 18px;
        }

        .line {
            width: 100%;
            height: 2px;
            margin: 2px 0 2px 0;
            background: <?= get_option("task_main_color")?>;
            float: left;
        }

        tr {
            width: 95%;
            display: inline-table;
            /*display: inline-table;*/
        }

        table {
            /*height: 600px;*/
            height: 80vh; /* 80% da view-height */
        }

        tbody {
            overflow-y: scroll;
            height: 85%; /*550px; */
            width: 95%;
            position: absolute;
            text-align: left;
        }

        /*#atend-interno > table {*/
            /*!*height: 300px;*!*/
            /*height: 30vh;*/
        /*}*/

        #reanalise > table {
            height: 25vh;
        }


        #atendimento-interno > tbody {
            height: 85%;
            overflow-y: scroll;
            overflow-x: hidden;
            width: 95%;
            position: absolute;
        }

        #atendimento-reanalise > tbody {
            height: 85%;
            overflow-y: scroll;
            overflow-x: hidden;
            width: 95%;
            position: absolute;
        }

        #suporte-pendente-table > tbody {
            height: 85%; /* 550px; */
            overflow-y: scroll;
            overflow-x: hidden;
            width: 95%;
            float: left;
            padding-bottom: 0;
        }

        #suporte-pendente-table > td {
            padding: 3px 15px;
            width: 60px;
            /*border: 1px solid black;*/
        }

        #suporte-pendente-table > :last-child {
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
            color: <?= get_option("task_main_color")?>;
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
            /*border-top: 1px solid

        <?= get_option("task_main_color")?>  ;*/
        }

        @keyframes fade {
            from {
                opacity: 1.0;
            }
            50% {
                opacity: 0.4;
            }
            to {
                opacity: 1.0;
            }
        }

        @-webkit-keyframes fade {
            from {
                opacity: 1.0;
            }
            50% {
                opacity: 0.4;
            }
            to {
                opacity: 1.0;
            }
        }

        .alert {
            animation: fade 1000ms infinite;
            -webkit-animation: fade 1000ms infinite;
            background-color: #f0ad4e;
        }

        .limit {
            animation: fade 1000ms infinite;
            -webkit-animation: fade 1000ms infinite;
            background-color: #d9534f;
        }
    </style>
</head>
<body>
<h1>Painel Desenvolvimento
    <!--    <div class="pull-left" style="font-size: 90%;">-->
    <!--        SENHA: <span id="senha_text"></span>-->
    <!--    </div>-->
    <!--    <div class="pull-right" style="font-size: 90%;">-->
    <!--        Média dos Atendimentos:-->
    <!--        <span class = "label--->
    <?php //echo ($featured > 3) ? 'success' : 'danger'; ?><!--" style="font-size: inherit; color: inherit">-->
    <!--            --><?php //echo number_format($featured, 2); ?>
    <!--        </span>-->
    <!--    </div>-->
</h1>
<div class="container-fluid">
    <div class="row <?php if (is_partner() || (isset($_SESSION['partner_filter']) && $_SESSION['partner_filter'] != '')) echo 'hide'; ?>">
        <div class="col-md-12">
            <span class="atendentes-disponiveis-title">DESENVOLVEDORES DISPONÍVES:</span>
            <ul id="atendentes-disponiveis">
            </ul>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">

            <h2>Em Espera (<span id="nao_iniciado_quantidade"></span>)</h2>

            <table class="table table-responsive table-scrollable" id="nao_iniciado">
                <thead>
                <tr>
                    <th class="col-md-2">
                        PRIORIDADE:
                    </th>
                    <th class="col-md-5">
                        TICKET:
                    </th>

                    <th class="col-md-5">
                        TEMPO EM ESPERA
                    </th>
                </tr>
                </thead>
                <tbody id="nao_iniciado_interno">

                </tbody>
            </table>


            <!--            <div class='line'></div>-->

            <!--            <table class="table table-responsive">-->
            <!--                <tbody id="em_espera_externo">-->
            <!---->
            <!--                </tbody>-->
            <!--            </table>-->
        </div>

        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12" id="atend-interno">
                    <h2>Em Produção (<span id="em_progresso_quantidade"></span>)</h2>

                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th class="col-md-1">
                                PRIORIDADE:
                            </th>
                            <th>
                                TICKET:
                            </th>
                            <th class="col-md-4">
                                EM ATENDIMENTO
                            </th>
                        </tr>
                        </thead>
                        <tbody id="atendimento-interno" class='atendimento panel-group'>
                        </tbody>
                    </table>
                </div>


<!--                <div class="col-md-12" id="reanalise" style="padding-top: 4em">-->
<!--                    <h2>Reanálise (<span id="atendimento_reanalise_quantidade"></span>)</h2>-->
<!---->
<!--                    <table class="table table-responsive">-->
<!--                        <thead>-->
<!--                        <tr>-->
<!--                            <th class="col-md-1">-->
<!--                                PRIORIDADE:-->
<!--                            </th>-->
<!--                            <th>-->
<!--                                TICKET:-->
<!--                            </th>-->
<!--                            <th class="col-md-4">-->
<!--                                EM REANÁLISE-->
<!--                            </th>-->
<!--                        </tr>-->
<!--                        </thead>-->
<!--                        <tbody id="atendimento-reanalise" class="atendimento">-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
            </div>
        </div>

        <div class="col-md-4">
            <h2>Em Pausa (<span id="em_teste_quantidade"></span>)</h2>
            <table class="table table-responsive" id="suporte-pendente-table">
                <thead>
                <tr>
                    <th class="col-md-3">
                        TICKET:
                    </th>
                    <th class="col-md-2">
                        EM PAUSA
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

</div>

<audio id="soundpanel">
    <source src="../../definite.ogg" type="audio/ogg"/>
</audio>
<audio id="soundpanel1">
    <source src="../../alert2.ogg" type="audio/ogg"/>
</audio>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<?php init_tail(); ?>
<script>

    // Enable pusher logging - don't include this in production
    var sounds = {};
    var tocar = false;
    sounds.new = document.getElementById("soundpanel");
    sounds.t2 = document.getElementById("soundpanel1");
    sounds.t3 = document.getElementById("soundpanel3");

    var ticket_waiting_limit_sound = <?php echo get_option("task_waiting_limit_sound"); ?>;
    var ticket_waiting_alert_sound = <?php echo get_option("task_waiting_alert_sound"); ?>;
    var ticket_waiting_limit_sound_type = <?php echo get_option("task_waiting_limit_sound_type"); ?>;
    var ticket_waiting_alert_sound_type = <?php echo get_option("task_waiting_alert_sound_type"); ?>;
    var pertubacao_notify = <?php echo get_option("pertubacao_notify"); ?>;

    var tick = [];
    var tick_ = [];
    Pusher.logToConsole = true;
    var pusher = new Pusher('<?php echo get_option('pusher_app_key');?>', {
        cluster: '<?php echo get_option('pusher_cluster');?>',
        encrypted: true
    });

    var channel = pusher.subscribe('painel');
    channel.bind('atualizar', function (data) {
        getData();
        console.log("atualizando pelo pusher");
    });

    function getData() {
        axios.get('<?= ciroute("painel.desenvolvimento.dados")?>')
            .then(function (response) {
                quantidade(response.data);
                limparTela();
                tocar = false;

                senha(response.data.senha);

                response.data.em_espera.forEach(function (data) {
                    if (data.limit == true)
                        tocar_(data.ticketid, "limit");
                    else if (data.alerta == true)
                        tocar_(data.ticketid, "alert");

                    nao_iniciado(data, 'nao_iniciado_interno');
                    sound(data.ticketid, "new");
                });

                response.data.atendimento_interno.forEach(function (data) {
                    em_progresso(data, "atendimento-interno");
                });
                // response.data.atendimento_reanalise.forEach(function (data) {
                //     em_progresso(data, "atendimento-reanalise");
                // });
                response.data.atendentes_disponiveis.forEach(function (data) {
                    atendentes_disponiveis(data);
                });
                response.data.suporte_pendente.forEach(function (data) {
                    em_teste(data, "suporte-pendente");
                });
                if (pertubacao_notify == 1)
                    tocar = true;

                // $('.tarefa').addClass("in");

                //Parte do Collapse
                //when a group is shown, save it as the active accordion group
                $("#nao_iniciado").on('shown.bs.collapse', function () {
                    var active = $("#nao_iniciado .in").attr('id');
                    $.cookie('activeOptionsGroupNaoIniciado', active);
                    // alert(active);
                });
                $("#nao_iniciado").on('hidden.bs.collapse', function () {
                    $.removeCookie('activeOptionsGroupNaoIniciado');
                });
                var last_int = $.cookie('activeOptionsGroupNaoIniciado');
                if (last_int != null) {
                    //remove default collapse settings
                    $("#nao_iniciado .panel-collapse").removeClass('in');
                    //show the account_last visible group
                    $("#" + last_int).addClass("in");
                }

                /** Gambiras */

                //when a group is shown, save it as the active accordion group
                $("#suporte-pendente").on('shown.bs.collapse', function () {
                    var active = $("#suporte-pendente .in").attr('id');
                    $.cookie('activeOptionsPendente', active);
                    // alert(active);
                });
                $("#suporte-pendente").on('hidden.bs.collapse', function () {
                    $.removeCookie('activeOptionsPendente');
                });
                var last_pend = $.cookie('activeOptionsPendente');
                if (last_pend != null) {
                    //remove default collapse settings
                    $("#suporte-pendente .panel-collapse").removeClass('in');
                    //show the account_last visible group
                    $("#" + last_pend).addClass("in");
                }

                /** Gambiras */

                //when a group is shown, save it as the active accordion group
                // $("#atendimento-reanalise").on('shown.bs.collapse', function () {
                //     var active = $("#atendimento-reanalise .in").attr('id');
                //     $.cookie('activeOptionsReanalise', active);
                //     // alert(active);
                // });
                // $("#atendimento-reanalise").on('hidden.bs.collapse', function () {
                //     $.removeCookie('activeOptionsReanalise');
                // });
                // var last = $.cookie('activeOptionsReanalise');
                // if (last != null) {
                //     //remove default collapse settings
                //     $("#atendimento-reanalise .panel-collapse").removeClass('in');
                //     //show the account_last visible group
                //     $("#" + last).addClass("in");
                // }

                //Parte do Collapse

            });
    }

    setInterval(function () {
        getData();
    }, <?php echo get_option("painel_task_refresh_time"); ?>000);
    setInterval(function () {
        if (tocar == true)
            sounds.t2.play();
    }, 120000);

    function tocar_(ticketid, tipo) {
        if (tipo == "limit" && ticket_waiting_limit_sound == 1) {
            if (ticket_waiting_limit_sound_type == 1) {
                sound(ticketid, "limit");
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
        else if (tipo == "alert" && ticket_waiting_alert_sound == 1) {
            if (ticket_waiting_alert_sound_type == 1) {
                sound(ticketid, "alert");
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

    function sound(ticketid, type) {
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
        document.getElementById("nao_iniciado_interno").innerHTML = "";
        // document.getElementById("atendimento-reanalise").innerHTML = "";
        document.getElementById("atendimento-interno").innerHTML = "";
        document.getElementById("atendentes-disponiveis").innerHTML = "";
        document.getElementById("suporte-pendente").innerHTML = "";
    }

    function senha(senha) {
        document.getElementById("senha_text").innerHTML = senha;
    }

    function nao_iniciado(data, element) {
        var html = "<tr";
        if (data.limit == true) {
            html += " class='limit'";
        }
        else if (data.alerta == true)
            html += " class='alert'";

        html += "><td class='label-td'>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'>";
        html += "<div class='col-md-12' ><a target='_blank' href='" + admin_url + 'tickets/ticket/' + data.ticketid + "'>" + data.company + "</a>" +
            "<span class='pull-right' style='min-width: 15vh; text-align: right'>" + data.date_espera + "</span>" +
            "</div>";
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>Espera Total: </b>" ;
        html += "<span class='pull-right'>" + data.date + "</span></div>";
        html += "</div></td>";
        // html += "<td style='min-width: 200px; min-height: 50px; max-width: 200px'><a target='_blank' href='" + admin_url + 'tickets/ticket/' + data.ticketid + "' >" + data.company + "</a></td>";
        // html += "<td class='date' style='min-width: 130px;'>" + data.date + "</td>";
        html += "</tr>";

        html += '<tr>';
        html += '<td colspan="5">';
        html += '<div id="tarefa-' + data.ticketid + '" class="panel-collapse collapse ';
        if(element === 'atendimento-interno')
            html += 'in';
        html += '" style="height: 0px;" aria-expanded="false">';
        html += '<div class="panel-body">';
        html += '<div class="row">';


        // console.log(data);
        if(data.developers !== undefined) {
            data.developers.forEach(function (element, index, array) {

                console.log(element);
                html += '<div class="col-md-12 mtop10">';
                html += '<div class="col-md-2">';
                html += '<a style="max-width: 80px;" href="' + admin_url + 'tasks/view/' + element.taskid + '" target="_blank" class="btn btn-warning">#' + element.taskid + '</a>';
                html += '</div>';
                html += '<div class="col-md-8">';
                html += '<a href="' + admin_url + 'tasks/view/' + element.taskid + '" target="_blank" style="max-height: 20px; overflow: hidden; float: left;">' + element.nome + '</a>';
                html += '</div>';
                html += '<div class="col-md-2">';
                html += '<a id="image-profile-' + element.taskid + '" target="_blank" href="' + admin_url + 'profile/' + element.assigneeid + '">';
                html += element.profile_pic;
                html += '</a>';
                html += '</div>';
                html += '</div>';


            });
        }


        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';

        var espera = document.getElementById(element);
        espera.innerHTML += html;
    }

    function em_progresso(data, idElement) {

        var html = "<tr";
        var label_time = '';
        if(idElement === 'atendimento-interno')
            label_time = 'Produção';
        else
            label_time = 'Reanálise';

        if (data.limit_att == true) {
            html += " class='limit' style='color:white;'";
        }
        else if (data.alerta_att == true)
            html += " class='alert' style='color:white;'";
        html += "><td>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'>";
        html += "<div class='col-md-12' style='min-width: 25vh;' ><a target='_blank' href='" + admin_url + 'tickets/ticket/' + data.ticketid + "'>" + data.company + "</a>" +
            "<span class='pull-right' style='min-width: 15vh; text-align: right'><span class='mbot5 mright5 badge badge-pill badge-info'> Em " + label_time + " </span>" + data.date_espera + "</span>" +
            "</div>"
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>Analista: </b>" + data.firstname.toUpperCase();
        html += "<span class='pull-right'><span class='mbot5 mright5 badge badge-pill badge-info'> Total </span>" + data.time + "</span></div>";
        html += "</div></td>";
        html += "</tr>";

        // console.log(data.developers[0].assigneeid);
        html += '<tr>';
        html += '<td colspan="5">';
        html += '<div id="tarefa-' + data.ticketid + '" class="panel-collapse collapse ';
        if(idElement === 'atendimento-interno')
            html += 'in';
        html += '" style="height: 0px;" aria-expanded="false">';
        html += '<div class="panel-body">';
        html += '<div class="row">';


        // console.log(data);
        if(data.developers !== undefined) {
            data.developers.forEach(function (element, index, array) {

                console.log(element);
                html += '<div class="col-md-12 mtop10">';
                html += '<div class="col-md-2">';
                html += '<a style="max-width: 80px;" href="' + admin_url + 'tasks/view/' + element.taskid + '" target="_blank" class="btn btn-warning">#' + element.taskid + '</a>';
                html += '</div>';
                html += '<div class="col-md-8">';
                html += '<a href="' + admin_url + 'tasks/view/' + element.taskid + '" target="_blank" style="max-height: 20px; overflow: hidden; float: left;">' + element.nome + '</a>';
                html += '</div>';
                html += '<div class="col-md-2">';
                html += '<a id="image-profile-' + element.taskid + '" target="_blank" href="' + admin_url + 'profile/' + element.assigneeid + '">';
                html += element.profile_pic;
                html += '</a>';
                html += '</div>';
                html += '</div>';


            });
        }


        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';


        var atendimento = document.getElementById(idElement);
        atendimento.innerHTML += html;
    }

    function em_teste(data, idElement) {
        var html = "<tr>";
        html += "<td>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'>";
        html += "<div class='col-md-12'><a target='_blank' href='" + admin_url + 'tickets/ticket/' + data.ticketid + "'>" + data.company + "</a>" +
            "<span class='pull-right' style='min-width: 15vh; text-align: right'><span class='mbot5 mright5 badge badge-pill badge-info'> Em Teste </span>" + data.date_espera + "</span>" +
            "</div>"
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>Analista: </b>" + data.firstname.toUpperCase();
        html += "<span class='pull-right' style='min-width: 15vh; text-align: right'><span class='mbot5 mright5 badge badge-pill badge-info'> Total </span>" + data.time + "</span></div>";
        html += "</div></td>";
        html += "</tr>";


        html += '<tr>';
        html += '<td colspan="5">';
        html += '<div id="tarefa-' + data.ticketid + '" class="panel-collapse collapse" style="height: 0px;" aria-expanded="false">';
        html += '<div class="panel-body">';
        html += '<div class="row">';


        if(data.developers !== undefined) {
            data.developers.forEach(function (element, index, array) {

                console.log(element);
                html += '<div class="col-md-12 mtop10">';
                html += '<div class="col-md-2">';
                html += '<a style="max-width: 80px;" href="' + admin_url + 'tasks/view/' + element.taskid + '" target="_blank" class="btn btn-warning">#' + element.taskid + '</a>';
                html += '</div>';
                html += '<div class="col-md-8">';
                html += '<a href="' + admin_url + 'tasks/view/' + element.taskid + '" target="_blank" style="max-height: 20px; overflow: hidden; float: left;">' + element.nome + '</a>';
                html += '</div>';
                html += '<div class="col-md-2">';
                html += '<a id="image-profile-' + element.taskid + '" target="_blank" href="' + admin_url + 'profile/' + element.assigneeid + '">';
                html += element.profile_pic;
                html += '</a>';
                html += '</div>';
                html += '</div>';

            });
        }


        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';


        var atendimento = document.getElementById(idElement);
        atendimento.innerHTML += html;
    }

    function atendentes_disponiveis(data) {
        var html = "<li>" + data.firstname + "</li><li>|</li>";
        var atendentes_disponiveis = document.getElementById("atendentes-disponiveis");
        atendentes_disponiveis.innerHTML += html;
    }

    function quantidade(data) {
        //length
        var em_espera = 0;
        //em_espera += data.em_espera.hasOwnProperty(0) ? data.em_espera[0].length : 0;
        //em_espera += data.em_espera.hasOwnProperty(1) ? data.em_espera[1].length : 0;
        em_espera += data.em_espera.length ? data.em_espera.length : 0;
        document.getElementById("nao_iniciado_quantidade")
            .innerHTML = em_espera;
        document.getElementById("em_progresso_quantidade")
            .innerHTML = data.atendimento_interno.length;
        // document.getElementById("atendimento_reanalise_quantidade")
        //     .innerHTML = data.atendimento_reanalise.length;
        document.getElementById("em_teste_quantidade")
            .innerHTML = data.suporte_pendente.length;
        //document.getElementById("atendentes_disponiveis_quantidade")
        //  .innerHTML = data.atendentes_disponiveis.length;
    }

    function create_tags(value, priority, subject) {
        var label = [
            "danger",
            "warning",
            "success",
            "primary",
            "info",
            "secondary"
        ];
        label = label[priority - 1];
        return '<button style="min-width: 8vh; max-width: 8vh" data-toggle="collapse" data-parent="#atendimento-interno" href="#tarefa-' + value + '" class="btn btn-' + label + ' center-block">\n' +
            '  <span data-toggle="tooltip" title="' + subject + '" data-placement="right">' + value + '<span>\n' +
            '</button>';
        // return "<button id='ticket-" + value + "' style='width: 60px; min-width: 60px' data-placement='right'" +
        //         "data-toggle='collapse tooltip' data-parent='#options'" +
        //     " data-title='"+ subject + "' href='#tarefa-" + value + "' class=\"btn btn-" + label + " center-block collapsed\">" + value + "</button>";
    }

    getData();

</script>

</body>
</html>