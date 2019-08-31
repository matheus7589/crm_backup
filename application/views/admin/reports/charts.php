<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 28/12/2017
 * Time: 08:51
 */
init_head();?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons" id="accordion">
                        <button id="ticketc" href="#tickets" data-toggle="collapse" data-parent="#accordion" class="btn btn-info collapsed">Tickets</button></h4>
                        <div class="collapse" id="tickets">
                            <hr>
                            <a id="t1" href="#" onclick="chart_line('tickets-dia'); return false;" class="btn btn-info">Atendimentos por dia</a>
                            <a id="t2" href="#" onclick="chart_line('tickets-mes'); return false;" class="btn btn-info">Atendimentos por mes</a>
                            <a id="t3" href="#" onclick="chart_line('tickets-hora'); return false;" class="btn btn-info">Atendimentos por hora</a>
                            <a id="t4" href="#" onclick="chart_line('tickets-status'); return false;" class="btn btn-info">Atendimentos por status</a>
<!--                            <a id="t5" href="#" onclick="" class="btn btn-info">Atendimentos por serviços</a>-->
                            <a id="t5" href="#service" data-toggle="collapse" data-parent="#tickets" class="btn btn-info collapsed">Atendimentos por serviços</a>
                            <div class="collapse" id="service">
                                <hr>
                                <a id="t5" href="#" onclick="chart_column('tickets-service','resumed'); return false;" class="btn btn-info">Principais</a>
                                <a id="t5" href="#" onclick="chart_column('tickets-service','all'); return false;" class="btn btn-info">Todos</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable(array(
                                    array(
                                        'name' => 'Empresa',
                                        'th_attrs' => array('class' => 'col-md-5', 'style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => ' ',
                                        'th_attrs' => array('class' => 'col-md-6', 'style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Opções',
                                        'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                ), 'attend', array('info')
                            );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail();?>
<script>
    Highcharts.setOptions({
        lang: {
            months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            weekdays: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
            shortMonths: ["Jan", "Fev", "Mar", "Abr", "Maio", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
            drillUpText: "Voltar para {series.name}",
            printChart: "Imprimir Gráfico",
        }
    });
    loadbar("container");
    chart_line("tickets-dia");
    function chart_line(tipo)
    {
        $('#service').collapse("hide");
        loadbar("container");
        var data = {type:tipo};
        $.post(admin_url + "utilities/chart/get", data, function (response) {
            response = JSON.parse(response);
            series = response;
            console.log(series);
            Highcharts.chart('container', {
                rangeSelector: {
                    verticalAlign: 'top',
                    x: 0,
                    y: 0
                },
                chart: {
                    type: series.type
                },
                title: {
                    text: series.title
                },
                subtitle: {
                    text: series.subtitle
                },
                xAxis: series.xAxis,
                yAxis: {
                    title: {
                        text: series.yAxisT
                    },
                    min: 0
                },

                tooltip: series.tooltips,

                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },

                series: series.dados
            });
        });
    }
    function chart_column(tipo,all) {
        var data = {type:tipo,all:all};
        if(tipo != "tickets-service")
            $('#service').collapse("hide");
        $.post(admin_url + "utilities/chart/get", data, function (response) {
            response = JSON.parse(response);
            Highcharts.chart('container', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Atendimentos por Serviços'
                },
                subtitle: {
                    text: 'Clique nas colunas para ver as os serviços nível 2.'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Total de atendimentos'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.0f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
                },

                series: [{
                    name: 'Serviços',
                    colorByPoint: true,
                    data: response.data
                }],
                drilldown: {
                    series: response.subitems
                }
            });
        });
    }

    $(function () {
        initDataTable('.table-attend', admin_url+'utilities/attendance_report', [], [], '', '', []);
    });

</script>
