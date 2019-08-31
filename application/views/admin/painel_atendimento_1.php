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
            font-size: 25px;
            font-weight: bold;
        }

        .legenda {
            padding: 10px;
            position: fixed;
            bottom: 0;
            right: 0;
            font-size: 20px;
        }

        .label {
            text-transform: uppercase;
        }

        .atendimento > tr > td > .label {
            padding: 20px 0 20px 0;
        }

        .atendimento-informacoes {
            font-size: 20px;
        }

        .line {
            width: 100%;
            height: 2px;
            margin: 2px 0 2px 0;
            background: <?= get_option("ticket_main_color")?>;
            float: left;
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
            color: <?= get_option("ticket_main_color")?>;
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
            border-top: 1px solid <?= get_option("ticket_main_color")?>;
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

<div class="container-fluid">


    <div class="row">
        <div class="col-md-4">
            <h2>Em espera (<span id="em_espera_quantidade"></span>)</h2>
            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th class="text-left">
                        PRIORIDADE:
                    </th>
                    <th class="text-center">
                        CLIENTE:
                    </th>

                    <th class="text-right">
                        TEMPO EM ESPERA
                    </th>
                </tr>
                </thead>
            </table>
            <table class="table table-responsive">
                <tbody id="em_espera">
                </tbody>

            </table>
        </div>

        <div class="col-md-6">
            <h2>Em atendimento Interno (<span id="atendimento_interno_quantidade"></span>)</h2>

            <table class="table table-responsive">
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
                <tbody id="atendimento-interno" class='atendimento'>
                </tbody>
            </table>


            <h2>Em atendimento Externo (<span id="atendimento_externo_quantidade"></span>)</h2>

            <table class="table table-responsive">
                <tbody id="atendimento-externo">
                </tbody>
            </table>


        </div>
        <div class="col-md-2">
            <h2>Disponiveis (<span id="atendentes_disponiveis_quantidade"></span>)</h2>
            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th>
                        ANALISTA:
                    </th>

                </tr>
                </thead>
                <tbody id="atendentes-disponiveis">

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="senha">
    SENHA: <span id="senha_text"></span>
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
    Pusher.logToConsole = true;
    var pusher = new Pusher('<?php echo get_option('pusher_app_key');?>', {
        cluster: '<?php echo get_option('pusher_cluster');?>',
        encrypted: true
    });

    var channel = pusher.subscribe('painel');
    channel.bind('atualizar', function(data) {
        getData();
        console.log("atualizando pelo pusher");
    });

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

    function getData() {
        axios.get('<?= ciroute("painel.atendimento.dados")?>')
            .then(function (response) {
                limparTela();
                tocar = false;
                quantidade(response.data);
                senha(response.data.senha);
                response.data.em_espera.forEach(function (data) {
                    if(data.limit == true)
                        tocar_(data.ticketid,"limit");
                    else if(data.alerta == true)
                        tocar_(data.ticketid,"alert");

                    em_espera(data);
                    sound(data.ticketid,"new");
                });

                response.data.atendimento_interno.forEach(function (data) {
                    atendimento(data, "atendimento-interno");
                });

                response.data.atendimento_externo.forEach(function (data) {
                    atendimento(data, "atendimento-externo");
                });

                response.data.atendentes_disponiveis.forEach(function (data) {
                    atendentes_disponiveis(data);
                });
                if(pertubacao_notify == 1)
                    tocar = true;
            });
    }


    /// funcoes que criam o HTML
    function limparTela() {
        document.getElementById("em_espera").innerHTML = "";
        document.getElementById("atendimento-externo").innerHTML = "";
        document.getElementById("atendimento-interno").innerHTML = "";
        document.getElementById("atendentes-disponiveis").innerHTML = "";
    }

    function senha(senha) {
        document.getElementById("senha_text").innerHTML = senha;
    }

    function em_espera(data) {
        var html = "<tr";
        if(data.limit == true) {
            html += " class='limit'";
        }
        else if(data.alerta == true)
            html += " class='alert'";
        html += "><td class='label-td'>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><a target='_blank' href='<?php echo base_url("/admin/tickets/ticket/")?>"+data.ticketid+"'";
        if(data.limit == true || data.alerta == true)
            html += " style='color:white;'";
        html += ">" + data.company + "</a></td>";
        html += "<td class='date";
        if(data.limit == true)
            html += "' style='color:white;";
        else if(data.alerta == true)
            html += "' style='color:white;";
        html +="'>" + data.date_espera + "</td>";
        html += "</tr>";
        var espera = document.getElementById("em_espera");
        espera.innerHTML += html;
    }

    function atendimento(data, idElement) {
        var html = "<tr";
        if(data.limit_att == true) {
            html += " class='limit' style='color:white;'";
        }
        else if(data.alerta_att == true)
            html += " class='alert' style='color:white;'";
        html += "><td>" + create_tags(data.ticketid, data.priority, data.subject) + "</td>";
        html += "<td><div class='row'>";
        html += "<div class='col-md-12'>" + data.company + "</div>"
        html += "<div class='line'></div>";
        html += "<div class='col-md-12 atendimento-informacoes'> <b>ANALISTA: </b>" + data.firstname;
        html += "<span class='pull-right'> <b>TEMPO:</b> " + data.time + "</span></div>";
        html += "</div></td>";
        html += "</tr>";
        var atendimento = document.getElementById(idElement);
        atendimento.innerHTML += html;
    }

    function atendentes_disponiveis(data) {

        document.getElementById("atendentes-disponiveis").innerHTML += "<tr><td>" + data.firstname + "</td></tr>";

    }

    function quantidade(data) {

        document.getElementById("em_espera_quantidade")
            .innerHTML = data.em_espera.length;
        document.getElementById("atendimento_interno_quantidade")
            .innerHTML = data.atendimento_interno.length;
        document.getElementById("atendimento_externo_quantidade")
            .innerHTML = data.atendimento_externo.length;
        document.getElementById("atendentes_disponiveis_quantidade")
            .innerHTML = data.atendentes_disponiveis.length;
    }

    function create_tags(value, priority, subject) {
        var label = [
            "danger",
            "warning",
            "success",
            "primary",
            "info",
            "default",
        ];
        label = label[priority - 1];
        return "<div data-toggle='tooltip' data-placement='bottom'" +
            " data-title='"+ subject + "' class=\"label label-" + label + " center-block\" >" + value + "</div>";
    }
    getData();
</script>

</body>
</html>