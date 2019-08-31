$(function() {

    // Add predefined reply click
    $('#insert_predefined_reply').on('change', function(e) {
        e.preventDefault();
        var selectpicker = $(this);
        var id = selectpicker.val();

        if (id != '') {
            $.get(admin_url + 'tickets/get_predefined_reply_ajax/' + id, function(response) {
                tinymce.activeEditor.execCommand('mceInsertContent', false, response.message);
                selectpicker.selectpicker('val', '');
            }, 'json');
        }
    });

    $('.block-sender').on('click', function() {
        var sender = $(this).data('sender');
        if (sender == '') {
            alert('No Sender Found');
            return false;
        }
        $.post(admin_url + 'tickets/block_sender', {
            sender: sender
        }).done(function() {
            window.location.reload();
        });
    });

    // Admin ticket note add
    $('.add_note_ticket').on('click', function(e) {
        e.preventDefault();
        var note_description = $('textarea[name="note_description"]').val();
        var ticketid = $('input[name="ticketid"]').val();
        if (note_description == '') {
            return;
        }
        $.post(admin_url + 'misc/add_note/' + ticketid + '/ticket', {
            description: note_description
        }).done(function() {
            window.location.reload();
        });
    });

    // Update ticket settings from settings tab
    $('.save_changes_settings_single_ticket').on('click', function(e) {
        // habilita_priority_select();
        e.preventDefault();

        if ($('#scheduled_date_div').is(':visible')){
            if ($('#scheduled_date').val() == '' || $('#scheduled_date').val() == null) {
                if ($('#scheduled_date_div').find('span.help-block').length <= 0)
                    $("#scheduled_date_div").append("<span class='help-block' style='color: red;'>É obrigatório informar uma data!</span>");
                return false;
            }
        }

        var data = {};
        data = $('#settings *').serialize();
        data += '&ticketid=' + $('input[name="ticketid"]').val();
        $.post(admin_url + 'tickets/update_single_ticket_settings', data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if (typeof(response.department_reassigned) !== 'undefined') {
                    window.location.href = admin_url + 'tickets/';
                } else {
                    window.location.reload();
                }
            }
        });
    });

    $('select[name="status_top"]').on('change', function() {
            var status = $(this).val();
            var ticketid = $('input[name="ticketid"]').val();
            $.get(admin_url + 'tickets/change_status_ajax/' + ticketid + '/' + status, function(response) {
                alert_float(response.alert, response.message);
            }, 'json');
    });
    $('select[name="status"]').on('change', function() {
        var status = $(this).val();
        var ticketidv = $('input[name="ticketid"]').val();
        $.post(admin_url + 'tickets/change_status_ajax/' + ticketidv + '/' + status, {tipo: "verifica"}, function (response) {
            alert_float(response.alert, response.message);
        }, 'json');
    });

    // $('body.ticket select[name=service]').on('change', function(){
	// 	var id = $(this).val();
	// 	console.log("teste");
	// });

    ticket_contact_change_data();

  $('button[name="add_contato"]').click(function () {
      $("#add_contato_modal").modal();
  });

    $('body.ticket select[name="clientid"]').on('change', function () {
       // contato.empty().selectpicker('refresh');
        setTimeout(function() {
            var contato = $('body.ticket select[name="contactid"]');
            var clonedSelect = contato.empty('').clone();
            contato.selectpicker('destroy').remove();
            contato = clonedSelect;
            $('#select_contact').append(clonedSelect);
            init_ajax_search('contact', contato, {tickets_contacts: true});
            ticket_contact_change_data();
        }, 1000);
    });

    var already_id = 0;

function ticket_contact_change_data() {
    // Select ticket user id
    $('body.ticket select[name="contactid"]').on('change', function(){
        var contactid = $(this).val();
        var projectAjax = $('select[name="project_id"]');
        var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
        var projectsWrapper = $('.projects-wrapper');
        projectAjax.selectpicker('destroy').remove();
        projectAjax = clonedProjectsAjaxSearchSelect;
        $('#project_ajax_search_wrapper').append(clonedProjectsAjaxSearchSelect);
        init_ajax_search('project', projectAjax, {
            customer_id: function() {
                return $('input[name="userid"]').val();
            }
        });
        if (contactid != '') {
            //limpa os campos caso ja tenha sido pesquisado algum contato anteriormente
            limpa();
            ///////////////////////////////////////////////////////////////////////////
            $.post(admin_url + 'tickets/ticket_change_data/', {
                contact_id: contactid,
            }).done(function(response) {

                response = JSON.parse(response);
                //console.log(response.custom_data.value);
                $('input[name="contact_now"]').val(response.contact_data.firstname + ' ' + response.contact_data.lastname);
                $('input[name="email_contact_now"]').val(response.contact_data.email);
                $('input[name="userid"]').val(response.contact_data.userid);

                $('input[name="to"]').val(response.primary_contact.firstname + ' ' + response.primary_contact.lastname);
                $('input[name="emailPrimary"]').val(response.primary_contact.email);
                $('input[name="phonePrimary"]').val(response.primary_contact.phonenumber);

                if(response.client_data.telefone == "")
                    $('input[name="phone_contact_now"]').val(response.contact_data.phonenumber);
                else
                    $('input[name="phone_contact_now"]').val(response.client_data.telefone);
                $(".observation-content").html(response.client_data.observation);
                if(PAINEL == QUANTUM) {
                    if (response.client_data.flag_backup == 1)
                        $("#backup").html('<span class="label inline-block pull-right" style="border:1px solid #84c529; color:#84c529">Backup configurado</span>');
                    else
                        $("#backup").html('<span class="label inline-block pull-right" style="border:1px solid #f11327; color:#f11327">Backup não configurado</span>');
                }
                $('input[name="fantasia"]').val(response.client_data.company);
                $('input[name="telefoneEmpresa"]').val(response.client_data.phonenumber);
                $('input[name="city"]').val(response.client_data.city);
                $('input[name="revenda"]').val(response.client_partner);
                $('input[name="address"]').val(response.client_data.address);
                $('input[name="celEmpresa"]').val(response.client_data.cellphone);
                $("#priority").val(response.client_data.priority_client).change();


                if(PAINEL == INORTE){
                    $('#name_soli').html(response.solicitantes);
                    $('#name_soli').selectpicker('refresh');
                }

                if(already_id != parseInt(response.client_data.userid)) {
                    if (response.client_data.observation != null) {
                        var modal = $("#observation").modal();
                        modal.find('.modal-body').html(response.client_data.observation);
                        already_id = parseInt(response.client_data.userid);
                    }
                }

                if(response.custom_revenda != null){
                    if(PAINEL == INORTE)
                        $('input[name="revenda"]').val(response.custom_revenda);
                    else
                        $('input[name="revenda"]').val(response.custom_revenda.value);
                }
                if(response.ultimo_atendimento != null){
                    $('input[name="ultimo_atendimento"]').val(response.ultimo_atendimento);
                    if(response.validation != null){
                        if(parseInt(response.ultimo_atendimento) >= response.validation){
                            //mostra o modal
                            $("#danger").modal();
                            $("#priority").val('3').change();
                        }
                    }

                }else{
                    $('input[name="ultimo_atendimento"]').val("Sem atendimentos anteriores");
                }
                if(PAINEL == INORTE)
                    $('label[for="telefone"]').text("Telefone " + response.contact_data.lastname);

                if (response.customer_has_projects) {
                    projectsWrapper.removeClass('hide');
                } else {
                    projectsWrapper.addClass('hide');
                }
            });
        } else {
            limpa();
            projectsWrapper.addClass('hide');
        }
    });
}


    // + button for adding more attachments
    // var ticketAttachmentKey = 1;
    // $('.add_more_attachments').on('click', function() {
    //     if ($(this).hasClass('disabled')) {
    //         return false;
    //     }
    //     var total_attachments = $('.attachments input[name*="attachments"]').length;
    //     if (total_attachments >= app_maximum_allowed_ticket_attachments) {
    //         return false;
    //     }
    //     var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
    //     newattachment.find('input').removeAttr('aria-describedby');
    //     newattachment.find('input').removeAttr('aria-invalid');
    //     newattachment.find('input').attr('name','attachments['+ticketAttachmentKey+']').val('');
    //     newattachment.find('p[id*="error"]').remove();
    //     newattachment.find('i').removeClass('fa-plus').addClass('fa-minus');
    //     newattachment.find('button').removeClass('add_more_attachments').addClass('remove_attachment').removeClass('btn-success').addClass('btn-danger');
    //     ticketAttachmentKey++;
    // });
    // Remove attachment
    $('body').on('click', '.remove_attachment', function() {
        $(this).parents('.attachment').remove();
    });
});

function desabilita_priority_select(adm) {
    if(!adm) {
        $('#priority').prop('disabled', true);
        $('#priority').selectpicker('refresh');
    }
}


function limpa(){
    $('input[name="to"]').val('');
    $('input[name="email"]').val('');
    $('input[name="userid"]').val('');
    $('input[name="contactid"]').val('');
    $('input[name="telefone"]').val('');
    $('input[name="fantasia"]').val('');
    $('input[name="telEmpresa"]').val('');
    $('input[name="celEmpresa"]').val('');
    $('input[name="revenda"]').val('');
    $('input[name="ultimo_atendimento"]').val('');
    $('input[name="city"]').val('');
    $('input[name="address"]').val('');
}

// Insert ticket knowledge base link modal
function insert_ticket_knowledgebase_link(e) {
    var id = $(e).val();
    if (id != '') {
        $.get(admin_url + 'knowledge_base/get_article_by_id_ajax/' + id, function(response) {
            var textarea = $('textarea[name="message"]');
            tinymce.activeEditor.execCommand('mceInsertContent', false, '<a href="'+site_url + 'knowledge_base/' + response.slug+'">'+response.subject+'</a>');
            $(e).selectpicker('val', '');
        }, 'json');
    }
}

function tickets_bulk_action(event) {
    var r = confirm(appLang.confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        var mass_delete = $('#mass_delete').prop('checked');
        var ids = [];
        var data = {};
        if (mass_delete == false || typeof(mass_delete) == 'undefined') {
            data.status = $('#move_to_status_tickets_bulk').val();
            data.department = $('#move_to_department_tickets_bulk').val();
            data.priority = $('#move_to_priority_tickets_bulk').val();
            data.service = $('#move_to_service_tickets_bulk').val();
            data.tags = $('#tags_bulk').tagit('assignedTags');
            if (data.status == '' && data.department == '' && data.priority == '' && data.service == '' && data.tags == '') {
                return;
            }
        } else {
            data.mass_delete = true;
        }
        var rows = $('.table-tickets').find('tbody tr');
        $.each(rows, function() {

            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'tickets/bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 50);
    }
}

function contact(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    if(!$('#clientid').valid()){
        return;
    }
    $.post(admin_url + 'tickets/add_contact/' + client_id + '/' + contact_id).done(function(response) {

        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
        validate_contact_form();
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}

function validate_contact_form() {
    _validate_form('#contact-form', {
        firstname: 'required',
        password: {
            required: {
                depends: function(element) {
                    var sent_set_password = $('input[name="send_set_password_email"]');
                    if ($('#contact input[name="contactid"]').val() == '' && sent_set_password.prop('checked') == false) {
                        return true;
                    }
                }
            }
        },
        // email: {
        //     required: true,
        //     email: true,
        //     remote: {
        //         url: admin_url + "misc/contact_email_exists",
        //         type: 'post',
        //         data: {
        //             email: function() {
        //                 return $('#contact input[name="email"]').val();
        //             },
        //             userid: function() {
        //                 return $('body').find('input[name="contactid"]').val();
        //             }
        //         }
        //     }
        // }
    }, contactFormHandler);
}

function contactFormHandler(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);
    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response){
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        if ($.fn.DataTable.isDataTable('.table-contacts')) {
            $('.table-contacts').DataTable().ajax.reload(null,false);
        }
        if (response.proposal_warning && response.proposal_warning != false) {
            $('body').find('#contact_proposal_warning').removeClass('hide');
            $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
            $('#contact').animate({
                scrollTop: 0
            }, 800);
        } else {
            $('#contact').modal('hide');
        }
        if(response.has_primary_contact == true){
            $('#client-show-primary-contact-wrapper').removeClass('hide');
        }
    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}


function ticketCheckDialog(data){
    $.alert({
        columnClass: 'col-md-10 col-md-offset-1',
        title: 'Atenção!',
        // escapeKey: true,
        backgroundDismiss: true,
        type: 'red',
        typeAnimated: true,
        content: function(){
            var self = this;
            self.setContent('<h5>Os seguintes tickets já foram abertos para este cliente, requisitando este mesmo serviço:</h5>');
            return $.ajax({
                url: admin_url + 'tickets/get_checked_services_data/?service1=' + data.service1 + '&service2=' +
                    data.service2 + '&userid=' + data.userid + '&ticketid=' + data.ticketid,
                dataType: 'html',
                method: 'get'
            }).done(function (response) {
                self.setContentAppend(response);
            }).fail(function(){
                // self.setContentAppend('<div>Fail!</div>');
            }).always(function(){
                // self.setContentAppend('<div>Always!</div>');
            });
        },
        onContentReady: function(){
            initDataTableOffline('#tb1');
        }
    });

}

$("#servicenv2, #service, #clientid").on('change', function () {
    var data = {};
    data.service1 = $("#service").val();
    data.service2 = $("#servicenv2").val();
    data.userid = $("#clientid").val();
    data.ticketid = $('input[name=ticketid]').val();

    if(data.ticketid === undefined) {
        data.ticketid = '';
    }

    if((data.service1 !== '' && data.service2 !== '' && data.userid !== '') && (data.service1 !== undefined &&
        data.service2 !== undefined && data.userid !== undefined) && (data.service1 !== null &&
        data.service2 !== null && data.userid !== null) && (data.service1 !== '153' && data.service2 !== '113')) {

        $.get(admin_url + "tickets/check_services/", data).done(function (response) {
            response = JSON.parse(response);
            if (response.message === 'true') {
                ticketCheckDialog(data);
            }
        });
        setLocalStorage('messageAfterLoad', 'true', 5);
    }
});

function openresp(ticketid)
{
    var tr = $('#tr_' + ticketid);
    var row = $('#tb1').DataTable().row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        var data = {};
        data.ticketid = ticketid;

        $.get(admin_url + "tickets/sub_table/", data).done(function(response)
        {
            // console.log(response);
            row.child(response).show();
            initDataTableOffline('#tb2', 'not-order');
            tr.addClass('shown');
        });


    }
}
