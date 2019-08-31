<h4 class="customer-profile-group-heading"><?php echo _l('client_add_edit_profile'); ?></h4>
<div class="row">
    <?php echo form_open($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?><?php echo form_open($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
    <div class="additional"></div>
    <div class="col-md-12">
        <ul class="nav nav-tabs profile-tabs row customer-profile-tabs" role="tablist">
            <li role="presentation" class="<?php if (!$this->input->get('tab')) {
                echo 'active';
            }; ?>">
                <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                    <?php echo _l('customer_profile_details'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                    <?php echo _l('billing_shipping'); ?>
                </a>
            </li>
            <?php do_action('after_customer_billing_and_shipping_tab', isset($client) ? $client : false); ?>
            <?php if (isset($client)) { ?>
                <li role="presentation<?php if ($this->input->get('tab') && $this->input->get('tab') == 'contacts') {
                    echo ' active';
                }; ?>">
                    <a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">
                        <?php echo _l('customer_contacts'); ?>
                    </a>
                </li>
                <li role="presentation" class="<?php if(is_partner()) echo "hide"; ?>">
                    <a href="#customer_admins" aria-controls=customer_admins" role="tab" data-toggle="tab">
                        <?php echo _l('customer_admins'); ?>
                    </a>
                </li>
                <?php do_action('after_customer_admins_tab', $client); ?>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <?php do_action('after_custom_profile_tab_content', isset($client) ? $client : false); ?>
            <div role="tabpanel" class="tab-pane<?php if (!$this->input->get('tab')) {
                echo ' active';
            }; ?>" id="contact_info">
                <div class="row">
                    <?php if(is_partner())echo "<fieldset disabled>";?>
                    <div class="col-md-12<?php if (isset($client) && (!is_empty_customer_company($client->userid) && total_rows('tblcontacts', array('userid' => $client->userid, 'is_primary' => 1)) > 0)) {
                        echo '';
                    } else {
                        echo ' hide';
                    } ?>" id="client-show-primary-contact-wrapper">
                        <div class="checkbox checkbox-info mbot20 no-mtop">
                            <input type="checkbox"
                                   name="show_primary_contact"<?php if (isset($client) && $client->show_primary_contact == 1) {
                                echo ' checked';
                            } ?> value="1" id="show_primary_contact">
                            <label for="show_primary_contact"><?php echo _l('show_primary_contact', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('payments')); ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php $value = (isset($client) ? $client->cnpj_or_cpf : ''); ?>
                        <?php echo render_input('cnpj_or_cpf', 'CPF/CNPJ: ', $value, 'text', array('maxlength' => 30, 'minlength' => 11)); ?>
                        <?php $value = (isset($client) ? $client->cod_empresa : ''); ?>
                        <?php echo render_input('cod_empresa', 'Código da Empresa: ', $value, 'number'); ?>
                        <?php
                        $value = (isset($client) ? $client->company : ''); ?>
                        <?php $attrs = (isset($client) ? array() : array('autofocus' => true)); ?>
                        <?php $label_attrs = array(
                            'class' => 'text-danger',

                        ); ?>
                        <?php echo render_input('company', 'Fantasia', $value, 'text', $attrs, array(), '', '', $label_attrs); ?>
                        <?php $value = (isset($client) ? $client->phonenumber : ''); ?>
                        <?php echo render_input('phonenumber', 'Telefone Fixo', $value); ?>
                        <?php $value = (isset($client) ? $client->zip : ''); ?>
                        <?php echo render_input('zip', 'client_postal_code', $value); ?>
                        <?php $value = (isset($client) ? $client->city : ''); ?>
                        <?php echo render_input('city', 'client_city', $value); ?>

                        <?php $countries = get_all_countries();
                        $customer_default_country = get_option('customer_default_country');
                        $selected = (isset($client) ? $client->country : $customer_default_country);
                        echo render_select('country', $countries, array('country_id', array('short_name')), 'clients_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                        ?>
                        <?php
                        if (get_option('company_requires_vat_number_field') == 1) {
                            $value = (isset($client) ? $client->vat : '');
                            echo render_input('vat', 'client_vat_number', $value);
                        }
                        ?>

                        <?php if (get_option('disable_language') == 0) { ?>
                            <div class="form-group">
                                <label for="default_language"
                                       class="control-label"><?php echo _l('localization_default_language'); ?>
                                </label>
                                <select name="default_language" id="default_language" class="form-control selectpicker"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""><?php echo _l('system_default_string'); ?></option>
                                    <?php foreach (list_folders(APPPATH . 'language') as $language) {
                                        $selected = '';
                                        if (isset($client)) {
                                            if ($client->default_language == $language) {
                                                $selected = 'selected';
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>

                        <?php


                        $selected = array();

                        // Model Certificate
                        if (isset($client->model_certificate) && $client->model_certificate) {
                            foreach ($model_certificates as $model_certificate) {
                                if ($model_certificate["id"] == $client->model_certificate) {
                                    $selected[] = $model_certificate["id"];
                                }
                            }
                        }

                        echo render_select('model_certificate', $model_certificates, ['id', 'name'], 'Modelo de Certificado', $selected);


                        // tblissuingcertificate
                        $issue = '';
                        if (isset($client->issuing_certificate) && $client->issuing_certificate != null) {
                            foreach ($issuing_certificates as $certificate) {
                                if ($certificate["id"] == $client->issuing_certificate) {
                                    $issue = $certificate["id"];
                                }
                            }
                        }

                        echo render_select('issuing_certificate', $issuing_certificates, ['id', 'name'], 'Emissor certificado', $issue);

                        ?>

                        <?php $value = (isset($client) ? _d($client->validate_certificate) : _d('')); ?>
                        <?php echo render_date_input('validate_certificate', 'Certificado Validade: ', $value); ?>

                        <?php

                        // Partner
                        if (isset($client->partner_id) ) {
                            foreach ($partners as $partner) {
                                if ($partner["partner_id"] == $client->partner_id) {
                                    $selecionado = $partner["partner_id"];
                                }
                            }
                        }
                        else
                        {
                            $selecionado = get_staff_partner_id();
                        }
                        echo render_select('partner_id', $partners, array('partner_id', 'firstname'), 'Parceiro', $selecionado, array(), array(), '', '', false);

                        ?>


                    </div>
                    <div class="col-md-6">
                        <?php $value = (isset($client) ? $client->social_reason : ''); ?>
                        <?php echo render_input('social_reason', 'Razão Social ', $value, 'text', array('maxlength' => 255)); ?>
                        <?php $value = (isset($client) ? $client->telefone : ''); ?>
                        <?php echo render_input('telefone', 'Telefone', $value); ?>
                        <?php $value = (isset($client) ? $client->cellphone : ''); ?>
                        <?php echo render_input('cellphone', 'Celular: ', $value, 'text'); ?>
                        <?php $value = (isset($client) ? $client->address : ''); ?>
                        <?php echo render_input('address', 'client_address', $value); ?>

                        <?php $value = (isset($client) ? $client->state : ''); ?>
                        <?php echo render_input('state', 'client_state', $value); ?>

                        <?php
                        $selected = array();
                        if (isset($customer_groups)) {
                            foreach ($customer_groups as $group) {
                                array_push($selected, $group['groupid']);
                            }
                        }
                        echo render_select('groups_in[]', $groups, array('id', 'name'), 'customer_groups', $selected, array('multiple' => true), array(), '', '', false);
                        ?>

                        <?php $value = (isset($client) ? $client->website : ''); ?>
                        <?php echo render_input('website', 'client_website', $value); ?>

                        <?php if (!isset($client)) { ?>
                            <i class="fa fa-question-circle pull-left" data-toggle="tooltip"
                               data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                        <?php }
                        $s_attrs = array('data-none-selected-text' => _l('system_default_string'));
                        $selected = '';
                        if (isset($client) && client_have_transactions($client->userid)) {
                            $s_attrs['disabled'] = true;
                        }
                        foreach ($currencies as $currency) {
                            if (isset($client)) {
                                if ($currency['id'] == $client->default_currency) {
                                    $selected = $currency['id'];
                                }
                            }
                        }
                        // Do not remove the currency field from the customer profile!
                        echo render_select('default_currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $s_attrs); ?>

                        <a href="#" tabindex="-1" class="pull-left mright5"
                           onclick="fetch_lat_long_from_google_cprofile(); return false;" data-toggle="tooltip"
                           data-title="<?php echo _l('fetch_from_google') . ' - ' . _l('customer_fetch_lat_lng_usage'); ?>"><i
                                    id="gmaps-search-icon" class="fa fa-google" aria-hidden="true"></i></a>
                        <?php $value = (isset($client) ? $client->latitude : ''); ?>
                        <?php echo render_input('latitude', 'customer_latitude', $value); ?>
                        <?php $value = (isset($client) ? $client->longitude : ''); ?>
                        <?php echo render_input('longitude', 'customer_longitude', $value); ?>
                        <?php echo render_select('priority_client', $priorities, array('priorityid', 'name'), 'Prioridade', isset($client) ? $client->priority_client : '') ?>
                        <?php if(PAINEL ==  QUANTUM){ ?>
                            <div class="form-group">
                                <label for="rtl_support_admin" class="control-label clearfix">Backup Configurado</label>
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" id="y_opt_1_settings_rtl_support_admin" name="flag_backup" value="1" <?php if(isset($client) && $client->flag_backup == 1){echo 'checked="true"';}?>>
                                    <label for="y_opt_1_settings_rtl_support_admin">Sim</label>
                                </div>
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" id="y_opt_2_settings_rtl_support_admin" name="flag_backup" value="0" <?php if((isset($client) && $client->flag_backup == 0) || !isset($client)){echo 'checked="true"';}?>>
                                    <label for="y_opt_2_settings_rtl_support_admin">Não</label>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                    <?php if(is_partner())echo "</fieldset>";?>
                    <div class="col-md-12">

                        <?php
                        $value = $client->observation ?? '';
                        echo render_textarea("observation", "Observação do Cliente", str_replace("</br>","\r\n",$value));
                        ?>
                    </div>


                </div>
            </div>
            <?php if (isset($client)) { ?>
                <div role="tabpanel"
                     class="tab-pane<?php if ($this->input->get('tab') && $this->input->get('tab') == 'contacts') {
                         echo ' active';
                     }; ?>" id="contacts">
                    <?php if (has_permission('customers', '', 'create') || is_customer_admin($client->userid)) {
                        $disable_new_contacts = false;
                        if (is_empty_customer_company($client->userid) && total_rows('tblcontacts', array('userid' => $client->userid)) == 1) {
                            $disable_new_contacts = true;
                        }
                        ?>
                        <div class="inline-block"<?php if ($disable_new_contacts) { ?> data-toggle="tooltip" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>"<?php } ?>>
                            <a href="#" onclick="contact(<?php echo $client->userid; ?>); return false;"
                               class="btn btn-info mbot25<?php if ($disable_new_contacts) {
                                   echo ' disabled';
                               } ?>"><?php echo _l('new_contact'); ?></a>
                            <a href="#" onclick="connect_contact(<?php echo $client->userid; ?>); return false;"
                               class="btn btn-info mbot25<?php if ($disable_new_contacts) {
                                   echo ' disabled';
                               } ?>"><?php echo _l('Adicionar Contato Existente'); ?></a>
                        </div>
                    <?php } ?>
                    <?php
                    $table_data = array(
                        array(
                            'name' => _l('client_firstname'),
                        ),
                        array(
                            'name' => _l('client_lastname'),
                        ),
                        array(
                            'name' => _l('client_email'),
                        ),
                        array(
                            'name' => _l('contact_position'),
                        ),
                        array(
                            'name' => _l('client_phonenumber'),
                        ),
                        array(
                            'name' => _l('contact_active'),
                        ),
                        array(
                            'name' => _l('clients_list_last_login')
                        ));
                    $custom_fields = get_custom_fields('contacts', array('show_on_table' => 1));
                    foreach ($custom_fields as $field) {
                        array_push($table_data, $field['name']);
                    }
                    array_push($table_data, _l('options'));
                    echo render_datatable($table_data, 'contacts'); ?>
                </div>
                <div role="tabpanel" class="tab-pane <?php if(is_partner()) echo "hide"; ?>" id="customer_admins" >
                    <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
                        <a href="#" data-toggle="modal" data-target="#customer_admins_assign"
                           class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
                    <?php } ?>
                    <table class="table dt-table">
                        <thead>
                        <tr>
                            <th><?php echo _l('staff_member'); ?></th>
                            <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                            <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
                                <th><?php echo _l('options'); ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($customer_admins as $c_admin) { ?>
                            <tr>
                                <td><a href="<?php echo admin_url('profile/' . $c_admin['staff_id']); ?>">
                                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                                            'staff-profile-image-small',
                                            'mright5'
                                        ));
                                        echo get_staff_full_name($c_admin['staff_id']); ?></a>
                                </td>
                                <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                                <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
                                    <td>
                                        <a href="<?php echo admin_url('clients/delete_customer_admin/' . $client->userid . '/' . $c_admin['staff_id']); ?>"
                                           class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
            <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#"
                                                                                            class="pull-right billing-same-as-customer">
                                        <small class="text-info font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small>
                                    </a></h4>
                                <hr/>
                                <?php if(is_partner())echo "<fieldset disabled>";?>
                                <?php $value = (isset($client) ? $client->billing_street : ''); ?>
                                <?php echo render_input('billing_street', 'billing_street', $value); ?>
                                <?php $value = (isset($client) ? $client->billing_city : ''); ?>
                                <?php echo render_input('billing_city', 'billing_city', $value); ?>
                                <?php $value = (isset($client) ? $client->billing_state : ''); ?>
                                <?php echo render_input('billing_state', 'billing_state', $value); ?>
                                <?php $value = (isset($client) ? $client->billing_zip : ''); ?>
                                <?php echo render_input('billing_zip', 'billing_zip', $value); ?>
                                <?php $selected = (isset($client) ? $client->billing_country : ''); ?>
                                <?php echo render_select('billing_country', $countries, array('country_id', array('short_name')), 'billing_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>
                                <?php if(is_partner())echo "</fieldset>";?>
                            </div>
                            <div class="col-md-6">
                                <h4 class="no-mtop">
                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                       data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                                    <?php echo _l('shipping_address'); ?> <a href="#"
                                                                             class="pull-right customer-copy-billing-address">
                                        <small class="text-info font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small>
                                    </a>
                                </h4>
                                <hr/>
                                <?php if(is_partner())echo "<fieldset disabled>";?>
                                <?php $value = (isset($client) ? $client->shipping_street : ''); ?>
                                <?php echo render_input('shipping_street', 'shipping_street', $value); ?>
                                <?php $value = (isset($client) ? $client->shipping_city : ''); ?>
                                <?php echo render_input('shipping_city', 'shipping_city', $value); ?>
                                <?php $value = (isset($client) ? $client->shipping_state : ''); ?>
                                <?php echo render_input('shipping_state', 'shipping_state', $value); ?>
                                <?php $value = (isset($client) ? $client->shipping_zip : ''); ?>
                                <?php echo render_input('shipping_zip', 'shipping_zip', $value); ?>
                                <?php $selected = (isset($client) ? $client->shipping_country : $customer_default_country); ?>
                                <?php echo render_select('shipping_country', $countries, array('country_id', array('short_name')), 'shipping_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>
                            </div>
                            <?php if (isset($client) &&
                                (total_rows('tblinvoices', array('clientid' => $client->userid)) > 0 || total_rows('tblestimates', array('clientid' => $client->userid)) > 0)) { ?>
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <div class="checkbox checkbox-default">
                                            <input type="checkbox" name="update_all_other_transactions"
                                                   id="update_all_other_transactions">
                                            <label for="update_all_other_transactions">
                                                <?php echo _l('customer_update_address_info_on_invoices'); ?><br/>
                                            </label>
                                        </div>
                                        <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(is_partner())echo "</fieldset>";?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<div id="contact_data"></div>
<?php if (isset($client)) { ?>
    <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
        <div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <?php echo form_open(admin_url('clients/assign_admins/' . $client->userid)); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $selected = array();
                        foreach ($customer_admins as $c_admin) {
                            array_push($selected, $c_admin['staff_id']);
                        }
                        echo render_select('customer_admins[]', $staff, array('staffid', array('firstname', 'lastname')), '', $selected, array('multiple' => true), array(), '', '', false); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo _l('close'); ?></button>
                        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <!-- /.modal-content -->
                <?php echo form_close(); ?>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    <?php } ?>
<?php } ?>
<div id="connect_contact_modal"></div>
