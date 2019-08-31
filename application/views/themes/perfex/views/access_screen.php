<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento3
 * Date: 09/03/2019
 * Time: 10:43
 */ ?>

<style>

    .button {
        margin-right: 10px;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        font-weight: 500;
        border: none;
        border-radius: 45px;
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease 0s;
        cursor: pointer;
        outline: none;
        padding: 18px 36px!important;
    }

    .button:hover {
        box-shadow: 0px 10px 15px rgba(40, 184, 218, 0.66);
        transform: translateY(-9px);
    }

</style>

<div class="row">
    <div style="font-size: 25px" class="alert alert-warning alert-dismissible col-md-11" role="alert" id="myAlert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
        <strong style="font-size: 25px">Atenção!</strong> Solicitações de atendimento in loco, somente através de
        agendamento por telefone.
    </div>
    <div class="col-md-12">

        <div class="panel_s" style="margin-top: 15px;">

            <a class="btn btn-primary btn-lg button" href="<?php echo site_url('clients/open_ticket'); ?>">Abrir Novo Ticket</a>
            <a class="btn btn-primary btn-lg button" href="<?php echo site_url('clients/tickets'); ?>">Visualizar Tickets</a>
            <a class="btn btn-primary btn-lg button" href="<?php echo site_url('knowledge_base'); ?>">Base de Conhecimento</a>
            <a class="btn btn-primary btn-lg button" href="<?php echo site_url('movies'); ?>">Acessar Vídeos</a>

        </div>

    </div>

</div>
