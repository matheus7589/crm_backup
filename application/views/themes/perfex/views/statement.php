<div class="panel_s">
   <div class="panel-body">
      <h4 class="customer-profile-group-heading"><?php echo _l('customer_statement'); ?></h4>
      <div class="row">
         <div class="col-md-4">
            <div class="form-group">
               <select class="selectpicker" name="range" id="range" data-width="100%" onchange="render_customer_statement();">
                  <option value='<?php echo $period_today; ?>'
                  <?php if($period_selected == $period_today){echo ' selected';} ?>>
                     <?php echo _l('today'); ?>
                  </option>
                  <option value='<?php echo $period_this_week; ?>'
                  <?php if($period_selected == $period_this_week){echo ' selected';} ?>>
                     <?php echo _l('this_week'); ?>
                  </option>
                  <option value='<?php echo $period_this_month; ?>'
                  <?php if($period_selected == $period_this_month){echo ' selected';} ?>>
                      <?php echo _l('this_month'); ?>
                  </option>
                  <option value='<?php echo $period_last_month; ?>'
                  <?php if($period_selected == $period_last_month){echo ' selected';} ?>>
                      <?php echo _l('last_month'); ?>
                  </option>
                  <option value='<?php echo $period_this_year; ?>'
                  <?php if($period_selected == $period_this_year){echo ' selected';} ?>>
                      <?php echo _l('this_year'); ?>
                  </option>
                  <option value='<?php echo $period_last_year; ?>'
                  <?php if($period_selected == $period_last_year){echo ' selected';} ?>>
                      <?php echo _l('last_year'); ?></option>
                  <option value="period"<?php if($custom_period){echo ' selected';} ?>><?php echo _l('period_datepicker'); ?></option>
               </select>
            </div>
            <div class="row mtop15">
               <div class="col-md-12 period<?php if(!$custom_period){echo ' hide';} ?>">
                  <?php echo render_date_input('period-from','',($custom_period ? $from : ''),array('onchange'=>'render_customer_statement();')); ?>
               </div>
               <div class="col-md-12 period<?php if(!$custom_period){echo ' hide';} ?>">
                  <?php echo render_date_input('period-to','',($custom_period ? $to : ''),array('onchange'=>'render_customer_statement();')); ?>
               </div>
            </div>
         </div>
         <div class="col-md-8">
            <div class="text-right _buttons pull-right">
               <a href="<?php echo site_url('clients/statement_pdf?from='.urlencode($from).'&to='.urlencode($to).'&print=true'); ?>" id="statement_print" target="_blank" class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom">
               <i class="fa fa-print"></i>
               </a>
               <a href="<?php echo site_url('clients/statement_pdf?from='.urlencode($from).'&to='.urlencode($to)); ?>" id="statement_pdf"  class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom">
               <i class="fa fa-file-pdf-o"></i>
               </a>
            </div>
         </div>
         <div class="col-md-12 mtop15">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                        <address class="text-right">
                           <span class="bold"><?php echo get_option('invoice_company_name'); ?></span><br>
                           <?php echo get_option('invoice_company_address'); ?><br>
                           <?php echo get_option('invoice_company_city'); ?>, <?php echo get_option('company_state'); ?> <?php echo get_option('invoice_company_postal_code'); ?><br>
                           <?php echo get_option('invoice_company_country_code'); ?><br>
                           <?php if(get_option('invoice_company_phonenumber') != ''){ ?>
                           <?php echo get_option('invoice_company_phonenumber'); ?><br />
                           <?php } ?>
                           <?php if(get_option('company_vat') != ''){ ?>
                           <?php echo _l('company_vat_number').': '. get_option('company_vat'); ?><br />
                           <?php } ?>
                        </address>
                     </div>
                     <div class="col-md-12">
                        <hr />
                     </div>
                     <div class="col-md-7">
                        <address>
                           <p><?php echo _l('statement_bill_to'); ?>:</p>
                           <span class="bold">
                           <?php
                              echo $client->company; ?></span><br>
                           <?php echo $client->billing_street; ?><br>
                           <?php
                              if(!empty($client->billing_city)){
                                  echo $client->billing_city;
                              }
                              if(!empty($client->billing_state)){
                                  echo ', '.$client->billing_state;
                              }
                              $billing_country = get_country_short_name($client->billing_country);
                              if(!empty($billing_country)){
                                  echo '<br />'.$billing_country;
                              }
                              if(!empty($client->billing_zip)){
                                  echo ', '.$client->billing_zip;
                              }
                              if(!empty($client->vat)){
                                  echo '<br /><b>'._l('invoice_vat') .'</b>: '. $client->vat;
                              }
                              ?>
                        </address>
                     </div>
                     <div class="col-md-5">
                        <div class="text-right">
                           <h4 class="no-margin bold"><?php echo _l('account_summary'); ?></h4>
                           <p class="text-muted"><?php echo _l('statement_from_to',array($from,$to)); ?></p>
                           <hr />
                           <table class="table statement-account-summary">
                              <tbody>
                                 <tr>
                                    <td class="text-left"><?php echo _l('statement_beginning_balance'); ?>:</td>
                                    <td><?php echo format_money($statement['beginning_balance'],$statement['currency']->symbol); ?></td>
                                 </tr>
                                 <tr>
                                    <td class="text-left"><?php echo _l('invoiced_amount'); ?>:</td>
                                    <td><?php echo format_money($statement['invoiced_amount'],$statement['currency']->symbol); ?></td>
                                 </tr>
                                 <tr>
                                    <td class="text-left"><?php echo _l('amount_paid'); ?>:</td>
                                    <td><?php echo format_money($statement['amount_paid'],$statement['currency']->symbol); ?></td>
                                 </tr>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td class="text-left"><b><?php echo _l('balance_due'); ?></b>:</td>
                                    <td><?php echo format_money($statement['balance_due'],$statement['currency']->symbol); ?></td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="text-center bold">
                           <p class="mbot20"><?php echo _l('customer_statement_info',array($from,$to)); ?></p>
                        </div>
                        <div class="table-responsive">
                           <table class="table table-bordered table-striped">
                              <thead>
                                 <tr>
                                    <th><b><?php echo _l('statement_heading_date'); ?></b></th>
                                    <th><b><?php echo _l('statement_heading_details'); ?></b></th>
                                    <th class="text-right"><b><?php echo _l('statement_heading_amount'); ?></b></th>
                                    <th class="text-right"><b><?php echo _l('statement_heading_payments'); ?></b></b></th>
                                    <th class="text-right"><b><?php echo _l('statement_heading_balance'); ?></b></b></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td><?php echo $from; ?></td>
                                    <td><?php echo _l('statement_beginning_balance'); ?></td>
                                    <td class="text-right"><?php echo _format_number($statement['beginning_balance']); ?></td>
                                    <td></td>
                                    <td class="text-right"><?php echo _format_number($statement['beginning_balance']); ?></td>
                                 </tr>
                                 <?php
                                    $tmpBeginningBalance = $statement['beginning_balance'];
                                    foreach($statement['result'] as $data){ ?>
                                 <tr>
                                    <td><?php echo _d($data['date']); ?></td>
                                    <td>
                                       <?php
                                          if(isset($data['invoice_id'])) {
                                             echo _l('statement_invoice_details',array('<a href="'.site_url('viewinvoice/'.$data['invoice_id']).'/'.$data['hash'].'" target="_blank">'.format_invoice_number($data['invoice_id']).'</a>',_d($data['duedate'])));
                                          } else if(isset($data['payment_id'])){
                                          echo _l('statement_payment_details',array('#'.$data['payment_id'],format_invoice_number($data['payment_invoice_id'])));

                                          }
                                          ?>
                                    </td>
                                    <td class="text-right">
                                       <?php
                                          if(isset($data['invoice_id'])) {
                                            echo _format_number($data['invoice_amount']);
                                          }
                                          ?>
                                    </td>
                                    <td class="text-right">
                                       <?php
                                          if(isset($data['payment_id'])) {
                                            echo _format_number($data['payment_total']);
                                          }
                                          ?>
                                    </td>
                                    <td class="text-right">
                                       <?php
                                          if(isset($data['invoice_id'])) {
                                            $tmpBeginningBalance = ($tmpBeginningBalance + $data['invoice_amount']);
                                          } else if(isset($data['payment_id'])){
                                            $tmpBeginningBalance = ($tmpBeginningBalance - $data['payment_total']);
                                          }
                                          echo _format_number($tmpBeginningBalance);
                                          ?>
                                    </td>
                                 </tr>
                                 <?php } ?>
                              </tbody>
                              <tfoot class="statement_tfoot">
                                 <tr>
                                    <td colspan="3" class="text-right">
                                       <b><?php echo _l('balance_due'); ?></b>
                                    </td>
                                    <td class="text-right" colspan="2">
                                       <b><?php echo format_money($statement['balance_due'],$statement['currency']->symbol); ?></b>
                                    </td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
