<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 15/03/2018
 * Time: 16:57
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">

                        <h3 style="float: left;">Relatório Tipo de Atendimentos</h3> <br>
                        <div style="float: right; font-size: x-large">Total Atendimentos: <span id="atendimentos" style="padding:3px;" class="label-primary">
                                <?php echo number_format($atendimentos->total); ?></span></div>
                        <br>

                        <hr>
                        <div class="col-md-3">
                            <?php echo render_date_input('date_from', 'Data de Início', _d($date_from)); ?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_date_input('date_to', 'Data Final', _d($date_to)); ?>
                        </div>
                        <div class="col-md-3" style="text-align: center; padding: 27px">
                            <button class="btn btn-success" onclick="filter()" style="margin: auto; vertical-align: middle">Filtrar</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        echo render_datatable(array(
                            'Tipo de Serviço',
                            'Número de Atendimentos',
                            'Percentual',
                            'Detalhes'
                        ),'attendance-type');
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-attendance-type', admin_url + 'reports/attendance_type_report');
    });

    function filter() {
        if(($("#date_from").val() != "" && $("#date_from").val() != null) && ($("#date_to").val() != "" && $("#date_to").val() != null)
        || ($("#date_from").val() == "" || $("#date_from").val() == null) && ($("#date_to").val() == "" || $("#date_to").val() == null)) {
            var table = $('.table-attendance-type').DataTable();
            table.destroy();
            var serverParams = {};
            serverParams['date_from'] = '[name="date_from"]';
            serverParams['date_to'] = '[name="date_to"]';
            serverParams['attend'] = '[name="staff"]';
            initDataTable('.table-attendance-type', window.location.href, [], [], serverParams, [], []);

            var data = {};
            data.from = $('[name="date_from"]').val();
            data.to = $('[name="date_to"]').val();
            $.get(admin_url + "reports/get_atendimentos/", data).done(function (response ) {
                response = JSON.parse(response);
                // console.log(response);
                $('#atendimentos').text(response.atendimentos);
            }, "json");
        }
    }

    function detalhesServicos(name, total) {

        let data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.name = name;
        data.total = total;

        if (name) {
            $.confirm({
                title: '',
                columnClass: 'col-md-12',
                content: function () {
                    let self = this;
                    // self.setContent('Checking callback flow');
                    return $.ajax({
                        url: 'get_services_percentage',
                        dataType: 'html',
                        data: data,
                        method: 'get'
                    }).done(function (response) {
                        self.setContent(response);
                    }).fail(function () {
                        self.setContent('Falha :(');
                    });
                },
                onContentReady: function () {
                    initDataTableOffline("#services", [1, 'desc']);
                }
            });
        } else {
            $.alert({
                title: 'Atenção',
                content: 'Não é possível listar os tipos de serviços pois não foram selecionados tipos de serviço nesta modalidade.',
                color: 'yellow',
            });
        }
    }

    function detalhesSecondServices(name, serviceName, total, serviceid) {
        let data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.name = name;
        data.service_name = serviceName;
        data.total = total;
        data.serviceid = serviceid;

        $.confirm({
            title: '',
            columnClass: 'col-md-offset-1 col-md-10',
            content: function(){
                let self = this;
                return $.ajax({
                    url: 'get_second_service_percentage',
                    dataType: 'html',
                    data: data,
                    method: 'get'
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function(){
                    self.setContent('Falha :(');
                });
            },
            onContentReady: function(){
                initDataTableOffline("#servicesnv2", [1, 'desc']);
            }
        });
    }

    function detalhesTickets(name, total){
        let data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.name = name;
        data.total = total;

        $.confirm({
            title: '',
            columnClass: 'col-md-12',
            content: function(){
                let self = this;
                return $.ajax({
                    url: 'get_tickets_from_services',
                    dataType: 'html',
                    data: data,
                    method: 'get'
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function(){
                    self.setContent('Falha :(');
                });
            },
            onContentReady: function(){
                initDataTableOffline("#tickets-from-service", [0, 'desc']);
            }
        });
    }

    function detalhesTicketsFirstServices(name, serviceid, total, serviceName) {
        let data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.name = name;
        data.total = total;
        data.serviceid = serviceid;
        data.service_name = serviceName;

        $.confirm({
            title: '',
            columnClass: 'col-md-offset-2 col-md-8',
            content: function(){
                let self = this;
                return $.ajax({
                    url: 'get_tickets_from_servicesnv1',
                    dataType: 'html',
                    data: data,
                    method: 'get'
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function(){
                    self.setContent('Falha :(');
                });
            },
            onContentReady: function(){
                initDataTableOffline("#tickets-from-servicenv2", [0, 'desc']);
            }
        });
    }

    function detalhesTicketsSecondServices(name, serviceid, servicenv2, total, serviceName) {
        let data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.name = name;
        data.total = total;
        data.serviceid = serviceid;
        data.servicenv2 = servicenv2;
        data.service_name = serviceName;

        $.confirm({
            title: '',
            columnClass: 'col-md-offset-2 col-md-8',
            content: function(){
                let self = this;
                return $.ajax({
                    url: 'get_tickets_from_servicesnv2',
                    dataType: 'html',
                    data: data,
                    method: 'get'
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function(){
                    self.setContent('Falha :(');
                });
            },
            onContentReady: function(){
                initDataTableOffline("#tickets-from-servicenv2", [0, 'desc']);
            }
        });
    }

</script>