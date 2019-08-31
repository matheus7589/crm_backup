<?php
/**
 * Included in application/views/admin/clients/client.php
 */
?>
<script>
//--------------Inicio----------------
//-----Validação dos campos = CEP-----

//Cep Cliente
$('#zip').focusout(function()
{
  var cep = $(this);
  $.getJSON("https://viacep.com.br/ws/"+cep[0].value+"/json/", function(result)
  {
    document.getElementById('address').value = result["logradouro"];
    //document.getElementById('bairro').value="...";
    document.getElementById('city').value = result["localidade"];
    document.getElementById('state').value = result["uf"];
    //document.getElementById('ibge').value="...";
  });
});

//CEP da endereço da fatura
$('#billing_zip').focusout(function()
{
  var cep = $(this);
  $.getJSON("https://viacep.com.br/ws/"+cep[0].value+"/json/", function(result)
  {
    document.getElementById('billing_street').value = result["logradouro"];
    //document.getElementById('bairro').value="...";
    document.getElementById('billing_city').value = result["localidade"];
    document.getElementById('billing_state').value = result["uf"];
    //document.getElementById('ibge').value="...";
  });
});

//CEP da endereço de entrega
$('#shipping_zip').focusout(function()
{
  var cep = $(this);
  $.getJSON("https://viacep.com.br/ws/"+cep[0].value+"/json/", function(result)
  {
    document.getElementById('shipping_street').value = result["logradouro"];
    //document.getElementById('bairro').value="...";
    document.getElementById('shipping_city').value = result["localidade"];
    document.getElementById('shipping_state').value = result["uf"];
    //document.getElementById('ibge').value="...";
  });
});/**/

//-----Validação dos campos = CEP-----
//---------------FIM------------------

//--------------Inicio----------------
//--------Validação dos campos--------


function verifica_cpf_cnpj ( valor )
{

    // Garante que o valor é uma string
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');

    // Verifica CPF
    if ( valor.length === 11 ) {
        return 'CPF';
    }

    // Verifica CNPJ
    else if ( valor.length === 14 ) {
        return 'CNPJ';
    }

    // Não retorna nada
    else {
        return false;
    }

} // verifica_cpf_cnpj

/*
 calc_digitos_posicoes

 Multiplica dígitos vezes posições

 @param string digitos Os digitos desejados
 @param string posicoes A posição que vai iniciar a regressão
 @param string soma_digitos A soma das multiplicações entre posições e dígitos
 @return string Os dígitos enviados concatenados com o último dígito
*/
function calc_digitos_posicoes( digitos, posicoes = 10, soma_digitos = 0 ) {

    // Garante que o valor é uma string
    digitos = digitos.toString();

    // Faz a soma dos dígitos com a posição
    // Ex. para 10 posições:
    //   0    2    5    4    6    2    8    8   4
    // x10   x9   x8   x7   x6   x5   x4   x3  x2
    //   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
    for ( var i = 0; i < digitos.length; i++  ) {
        // Preenche a soma com o dígito vezes a posição
        soma_digitos = soma_digitos + ( digitos[i] * posicoes );

        // Subtrai 1 da posição
        posicoes--;

        // Parte específica para CNPJ
        // Ex.: 5-4-3-2-9-8-7-6-5-4-3-2
        if ( posicoes < 2 ) {
            // Retorno a posição para 9
            posicoes = 9;
        }
    }

    // Captura o resto da divisão entre soma_digitos dividido por 11
    // Ex.: 196 % 11 = 9
    soma_digitos = soma_digitos % 11;

    // Verifica se soma_digitos é menor que 2
    if ( soma_digitos < 2 ) {
        // soma_digitos agora será zero
        soma_digitos = 0;
    } else {
        // Se for maior que 2, o resultado é 11 menos soma_digitos
        // Ex.: 11 - 9 = 2
        // Nosso dígito procurado é 2
        soma_digitos = 11 - soma_digitos;
    }

    // Concatena mais um dígito aos primeiro nove dígitos
    // Ex.: 025462884 + 2 = 0254628842
    var cpf = digitos + soma_digitos;

    // Retorna
    return cpf;

} // calc_digitos_posicoes

/*
 Valida CPF

 Valida se for CPF

 @param  string cpf O CPF com ou sem pontos e traço
 @return bool True para CPF correto - False para CPF incorreto
*/
function valida_cpf(valor)
{

    // Garante que o valor é uma string
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');


    // Captura os 9 primeiros dígitos do CPF
    // Ex.: 02546288423 = 025462884
    var digitos = valor.substr(0, 9);

    // Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
    var novo_cpf = calc_digitos_posicoes( digitos );

    // Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
    var novo_cpf = calc_digitos_posicoes( novo_cpf, 11 );

    // Verifica se o novo CPF gerado é idêntico ao CPF enviado
    if ( novo_cpf === valor ) {
        // CPF válido
        return true;
    } else {
        // CPF inválido
        return false;
    }

} // valida_cpf

/*
 valida_cnpj

 Valida se for um CNPJ

 @param string cnpj
 @return bool true para CNPJ correto
*/
function valida_cnpj (valor)
{

    // Garante que o valor é uma string
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');


    // O valor original
    var cnpj_original = valor;

    // Captura os primeiros 12 números do CNPJ
    var primeiros_numeros_cnpj = valor.substr( 0, 12 );

    // Faz o primeiro cálculo
    var primeiro_calculo = calc_digitos_posicoes( primeiros_numeros_cnpj, 5 );

    // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
    var segundo_calculo = calc_digitos_posicoes( primeiro_calculo, 6 );

    // Concatena o segundo dígito ao CNPJ
    var cnpj = segundo_calculo;

    // Verifica se o CNPJ gerado é idêntico ao enviado
    if ( cnpj === cnpj_original ) {
        return true;
    }

    // Retorna falso por padrão
    return false;

} // valida_cnpj

function valida_cpf_cnpj (valor)
{

    // Verifica se é CPF ou CNPJ
    var valida = verifica_cpf_cnpj(valor);

    // Garante que o valor é uma string
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');

    // Valida CPF
    if (valida === 'CPF')
    {
        // Retorna true para cpf válido
        var validac = {tipo: "CPF", valido: valida_cpf(valor)};
        $('input[name="cnpj_or_cpf"]').mask("999.999.999-99");
        return validac;
    }

    // Valida CNPJ
    else if (valida === 'CNPJ')
    {
        // Retorna true para CNPJ válido
        //return valida_cnpj(valor);

        var validac = {tipo: "CNPJ", valido: valida_cnpj(valor)};
        $('input[name="cnpj_or_cpf"]').mask("99.999.999/9999-99");
        return validac;
    }

    // Não retorna nada
    else
    {
      var validac = {tipo: "Dado", valido: valida_cnpj(valor)};
        return validac;
    }

} // valida_cpf_cnpj

/*
 formata_cpf_cnpj

 Formata um CPF ou CNPJ

 @access public
 @return string CPF ou CNPJ formatado
*/
function formata_cpf_cnpj(valor)
{

    // O valor formatado
    var formatado = false;

    // Verifica se é CPF ou CNPJ
    var valida = verifica_cpf_cnpj(valor);

    // Garante que o valor é uma string
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');


    // Valida CPF
    if ( valida === 'CPF' ) {

        // Verifica se o CPF é válido
        if ( valida_cpf( valor ) ) {

            // Formata o CPF ###.###.###-##
            formatado  = valor.substr( 0, 3 ) + '.';
            formatado += valor.substr( 3, 3 ) + '.';
            formatado += valor.substr( 6, 3 ) + '-';
            formatado += valor.substr( 9, 2 ) + '';

        }

    }

    // Valida CNPJ
    else if ( valida === 'CNPJ' ) {

        // Verifica se o CNPJ é válido
        if ( valida_cnpj( valor ) ) {

            // Formata o CNPJ ##.###.###/####-##
            formatado  = valor.substr( 0,  2 ) + '.';
            formatado += valor.substr( 2,  3 ) + '.';
            formatado += valor.substr( 5,  3 ) + '/';
            formatado += valor.substr( 8,  4 ) + '-';
            formatado += valor.substr( 12, 14 ) + '';

        }

    }

    // Retorna o valor
    return formatado;

} // formata_cpf_cnpj

//--------------------------

var cnpj_cpf = $('#cnpj_or_cpf').val();
if(cnpj_cpf != null) {
    valida_cpf_cnpj(cnpj_cpf);
}


$('#cnpj_or_cpf').focusout(function()
{

  // O input que estamos utilizando
  var input = $(this);

  // O CPF ou CNPJ
  var cpf_cnpj = input.val();

  // Valida
  var valida = valida_cpf_cnpj(cpf_cnpj);
  console.log(valida);

  // Testa a validação
  if(!valida.valido)
  {
    input[0].value = "";
    input[0].placeholder = valida.tipo+" inválido.";
    $('input[name="cnpj_or_cpf"]').unmask();
    input.focus();
  }
});

//--------Validação dos campos--------
//--------------FIM----------------

Dropzone.options.clientAttachmentsUpload = false;
var customer_id = $('input[name="userid"]').val();
if ($('#client-attachments-upload').length > 0) {
    new Dropzone('#client-attachments-upload', {
        paramName: "file",

        dictDefaultMessage: appLang.drop_files_here_to_upload,
        dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
        dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
        dictCancelUpload: appLang.cancel_upload,
        dictRemoveFile: appLang.remove_file,
        dictMaxFilesExceeded: appLang.you_can_not_upload_any_more_files,
        maxFilesize: (max_php_ini_upload_size_bytes / (1024*1024)).toFixed(0),
        addRemoveLinks: true,
        acceptedFiles: app_allowed_files,

        accept: function(file, done) {
            done();
        },
        error: function(file, response) {
            alert_float('danger', response);
        },
        success: function(file, response) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                window.location.reload();
            }
        }
    });
}
$(function() {
    // Save button not hidden if passed from url ?tab= we need to re-click again
    if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }

    $('a[href="#contacts"],a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').addClass('hide');
    });

    $('.profile-tabs a').not('a[href="#contacts"],a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').removeClass('hide');
    });

    $("input[name='tasks_related_to[]']").on('change', function() {
        var tasks_related_values = []
        $('#tasks_related_filter :checkbox:checked').each(function(i) {
            tasks_related_values[i] = $(this).val();
        });
        $('input[name="tasks_related_to"]').val(tasks_related_values.join());
        $('.table-rel-tasks').DataTable().ajax.reload();
    });

    var contact_id = get_url_param('contactid');
    if (contact_id) {
        contact(customer_id, contact_id);
        $('a[href="#contacts"]').click();
    }

    $('body').on('change', '.onoffswitch input.customer_file', function(event, state) {
        var invoker = $(this);
        var checked_visibility = invoker.prop('checked');
        var share_file_modal = $('#customer_file_share_file_with');
        setTimeout(function() {
            $('input[name="file_id"]').val(invoker.attr('data-id'));
            if (checked_visibility && share_file_modal.attr('data-total-contacts') > 1) {
                share_file_modal.modal('show');
            } else {
                do_share_file_contacts();
            }
        }, 200);
    });
    // If user clicked save and add new contact
    var new_contact = get_url_param('new_contact');
    if (new_contact) {
        contact(customer_id);
        $('a[href="#contacts"]').click();
    }
    $('.customer-form-submiter').on('click', function() {
        var form = $('.client-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });

    if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
        document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
            success: function(files) {
                $.post(admin_url + 'clients/add_external_attachment', {
                    files: files,
                    clientid: customer_id,
                    external: 'dropbox'
                }).done(function() {
                    window.location.reload();
                });
            },
            linkType: "preview",
            extensions: app_allowed_files.split(','),
        }));
    }

    /* Custome profile tickets table */
    var ticketsNotSortable = $('.table-tickets-single').find('th').length - 1;
    _table_api = initDataTable('.table-tickets-single', admin_url + 'tickets/index/false/' + customer_id, [ticketsNotSortable], [ticketsNotSortable], 'undefined', [$('table thead .ticket_created_column').index(), 'DESC'])
    if (_table_api) {
        _table_api.column(5).visible(false, false).columns.adjust();
    }
    /* Custome profile contacts table */
    var contractsNotSortable = $('.table-contracts-single-client').find('th').length - 1;
    _table_api = initDataTable('.table-contracts-single-client', admin_url + 'contracts/index/' + customer_id, [contractsNotSortable], [contractsNotSortable], 'undefined', [3, 'DESC']);

    /* Custome profile contacts table */
    var contactsNotSortable = $('.table-contacts').find('th').length - 1;
    initDataTable('.table-contacts', admin_url + 'clients/contacts/' + customer_id, [contactsNotSortable], [contactsNotSortable]);

    /* Custome profile invoices table */
    initDataTable('.table-invoices-single-client',
        admin_url + 'invoices/list_invoices/false/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'DESC'],
            [0, 'DESC']
        ]);

    /* Custome profile Estimates table */
    initDataTable('.table-estimates-single-client',
        admin_url + 'estimates/list_estimates/false/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'DESC'],
            [0, 'DESC']
        ]);

    /* Custome profile payments table */
    initDataTable('.table-payments-single-client',
        admin_url + 'payments/list_payments/' + customer_id, [7], [7],
        'undefined', [6, 'DESC']);

    /* Custome profile reminders table */
    initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + customer_id + '/' + 'customer', [4], [4], undefined, [1, 'ASC']);

    /* Custome profile expenses table */
    initDataTable('.table-expenses-single-client',
        admin_url + 'expenses/list_expenses/false/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [5, 'DESC']);


    /* Custome profile proposals table */
    initDataTable('.table-proposals-client-profile',
        admin_url + 'proposals/proposal_relations/' + customer_id + '/customer',
        'undefined',
        'undefined',
        'undefined', [6, 'DESC']);

    /* Custome profile projects table */
    var notSortableProjects = $('.table-projects-single-client').find('th').length - 1;
    initDataTable('.table-projects-single-client', admin_url + 'projects/index/' + customer_id, [notSortableProjects], [notSortableProjects], 'undefined', <?php echo do_action('projects_table_default_order',json_encode(array(5,'ASC'))); ?>);

    if (app_company_is_required == 1) {
        _validate_form($('.client-form'), {
            company: 'required',
        });
    }

    $('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('input[name="billing_street"]').val($('input[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('input[name="shipping_street"]').val($('input[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
    });

    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.client-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });

});

function delete_contact_profile_image(contact_id) {
    $.get(admin_url + 'clients/delete_contact_profile_image/' + contact_id, function() {
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-remove-img').addClass('hide');
        $('body').find('#contact-img').attr('src', '<?php echo base_url('assets/images/user-placeholder.jpg'); ?>');
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

function contact(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    $.post(admin_url + 'clients/contact/' + client_id + '/' + contact_id).done(function(response) {
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

function delete_contact(clientid, contactid) {
    $.get(admin_url + 'clients/delete_contact/' + clientid + '/' + contactid).done(function (response) {
        window.location.reload();
    });
}

function connect_contact(client_id) {
    $.get(admin_url + 'clients/connect_contact/' + client_id).done(function (response) {
        $('#connect_contact_modal').html(response);
        $('#connect_contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="contact"]').focus();
            }
        });
        init_ajax_search('contact', '#contactid.ajax-search', {tickets_contacts: true});
    })
}

function save_connect_contact(client_id, contact_id) {
    $.post(admin_url + 'clients/save_connect_contact/' + client_id + '/' + contact_id).done(function (response) {
        response = JSON.parse(response);
        if(response.message == 'success'){
            alert_float('success', 'Contato relacionado com sucesso!');
            $('.table-contacts').DataTable().ajax.reload(null,false);
        }else{
            alert_float('warning', 'Não foi possível relacionar o cliente')
        }
    })
}

function update_all_proposal_emails_linked_to_contact(contact_id) {
    var data = {};
    data.update = true;
    data.original_email = $('body').find('#contact_update_proposals_emails').data('original-email');
    $.post(admin_url + 'clients/update_all_proposal_emails_linked_to_customer/' + contact_id, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        $('#contact').modal('hide');
    });
}

function do_share_file_contacts(edit_contacts, file_id) {
    var contacts_shared_ids = $('select[name="share_contacts_id[]"]');
    if (typeof(edit_contacts) == 'undefined' && typeof(file_id) == 'undefined') {
        var contacts_shared_ids_selected = $('select[name="share_contacts_id[]"]').val();
    } else {
        var _temp = edit_contacts.toString().split(',');
        for (var cshare_id in _temp) {
            contacts_shared_ids.find('option[value="' + _temp[cshare_id] + '"]').attr('selected', true);
        }
        contacts_shared_ids.selectpicker('refresh');
        $('input[name="file_id"]').val(file_id);
        $('#customer_file_share_file_with').modal('show');
        return;
    }
    var file_id = $('input[name="file_id"]').val();
    $.post(admin_url + 'clients/update_file_share_visibility', {
        file_id: file_id,
        share_contacts_id: contacts_shared_ids_selected,
        customer_id: $('input[name="userid"]').val()
    }).done(function() {
        window.location.reload();
    });
}

function fetch_lat_long_from_google_cprofile() {
    var data = {};
    data.address = $('input[name="address"]').val();
    data.city = $('input[name="city"]').val();
    data.country = $('select[name="country"] option:selected').text();
    $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
    $.post(admin_url + 'misc/fetch_address_info_gmaps', data).done(function(data) {
        data = JSON.parse(data);
        $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
        if (data.response.status == 'OK') {
            $('input[name="latitude"]').val(data.lat);
            $('input[name="longitude"]').val(data.lng);
        } else {
            if (data.response.status == 'ZERO_RESULTS') {
                alert_float('warning', "<?php echo _l('g_search_address_not_found'); ?>")
            } else {
                alert_float('danger', data.response.status);
            }
        }
    });
}
</script>
