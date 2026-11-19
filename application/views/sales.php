<!DOCTYPE html>
<html>

<head>
   <!-- FORM CSS CODE -->
   <?php include "comman/code_css.php"; ?>
   <!-- </copy> -->
   <!-- bootstrap wysihtml5 - text editor -->
   <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
   <style type="text/css">
      #sales_table tbody .form-control {
         background-color: #fff !important;
         color: #000;
         opacity: 1;
      }
      #sales_table tbody .form-control a,
      #sales_table tbody .form-control .text-blue {
         color: #000 !important;
      }
      .whatsapp-number-control { display: flex; width: 100%; align-items: stretch; }
      .whatsapp-number-control > .form-control { flex: 1 1 0; width: 100%; min-width: 0; float: none; border-radius: 3px; }
      .whatsapp-number-control > .input-group-btn { display: flex; flex: 0 0 auto; width: auto; }
      .whatsapp-number-control > .input-group-btn > .btn { margin-left: -1px; height: 34px; }
      .sales-price-history-trigger { min-width: 32px; height: 34px; color: #fff; font-size: 18px; font-weight: bold; line-height: 18px; }
      .sales-item-search-table { max-height: 320px; overflow-y: auto; overflow-x: hidden; padding: 0; background: #fff; }
      ul.sales-item-search-table.ui-menu > li { width: 100%; box-sizing: border-box; padding: 0; margin: 0; }
      ul.sales-item-search-table.ui-menu > .sales-search-header,
      ul.sales-item-search-table.ui-menu > li.ui-menu-item > a.sales-search-row {
         display: grid;
         grid-template-columns: 20% 44% 18% 18%;
         width: 100%;
         box-sizing: border-box;
         padding: 0;
         margin: 0;
         border: 0;
         border-radius: 0;
      }
      .sales-item-search-table .sales-search-header { position: sticky; top: 0; z-index: 1; background: #d9edf7; color: #000; font-weight: bold; }
      ul.sales-item-search-table.ui-menu > li.ui-menu-item > a.sales-search-row { background: #fff; color: #000; }
      ul.sales-item-search-table.ui-menu > li.ui-menu-item > a.sales-search-row.ui-state-focus,
      ul.sales-item-search-table.ui-menu > li.ui-menu-item > a.sales-search-row.ui-state-active,
      ul.sales-item-search-table.ui-menu > li.ui-menu-item > a.sales-search-row:hover { background: #eaf6fc; color: #000; }
      .sales-item-search-table span { padding: 8px 10px; border-right: 1px solid #c9dfe9; border-bottom: 1px solid #c9dfe9; min-width: 0; overflow-wrap: anywhere; }
      .sales-item-search-table span:nth-child(3),
      .sales-item-search-table span:nth-child(4) { text-align: right; }
      .sales-item-search-table .sales-search-header > span { text-align: center; }
      .sales-item-search-table .sales-search-empty { grid-column: 1 / -1; }
      .sales-footer-left { display: grid; grid-template-columns: 60% 40%; align-content: start; }
      .sales-footer-left > * { grid-column: 1 / -1; min-width: 0; }
      .sales-footer-left > .sales-footer-summary { grid-column: 1; grid-row: 1; }
      .sales-footer-left > .sales-footer-advance { grid-column: 2; grid-row: 1; }
      .sales-footer-summary > div:first-child { width: 32%; }
      .sales-footer-summary > div:nth-child(2) { width: 68%; }
      .sales-footer-summary .form-group { margin-left: 0; margin-right: 0; }
      .sales-footer-summary .input-group { max-width: 190px !important; }
      .sales-footer-advance .checkbox-inline { white-space: nowrap; }
      .sales-footer-advance > .col-sm-12 { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
      .sales-footer-advance > .col-sm-12 > label { margin: 0 !important; }
      .sales-advance-input { flex: 1 0 100px; max-width: 140px; }
      #advance_amount { background-color: #fff; color: #555; }
      /* Remark sits under Customer TRN when TRN is shown; otherwise it joins the Address/Salesperson row */
      @media (min-width: 768px) { #reference_no_col.remark-below-trn { clear: left; margin-top: 15px; } }
      #advance_amount:disabled { background-color: #eee; color: #555; cursor: not-allowed; opacity: 1; }
      .sales-footer-left .sales-send-options { background: transparent; padding: 0; margin: 0; display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
      .sales-send-options > label { color: #000; font-weight: 600; margin-bottom: 0; }
      .sales-note-heading { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 6px; }
      @media (max-width: 991px) {
         .sales-footer-left { display: block; }
         .sales-footer-summary > div:first-child,
         .sales-footer-summary > div:nth-child(2) { width: auto; }
      }
      /* Keep the sales form, items and totals clear of both panel edges. */
      .sales-page-panel {
         padding-left: 15px;
         padding-right: 15px;
      }
      .sales-form-actions {
         clear: both;
         display: flow-root;
         margin-left: -15px;
         margin-right: -15px;
         padding: 10px 15px;
      }
      @media (max-width: 767px) {
         .sales-page-panel {
            padding-left: 10px;
            padding-right: 10px;
         }
         .sales-form-actions {
            margin-left: -10px;
            margin-right: -10px;
         }
      }
      table.table-bordered>thead>tr>th {
         /* border:1px solid black;*/
         text-align: center;
      }

      .table>tbody>tr>td,
      .table>tbody>tr>th,
      .table>tfoot>tr>td,
      .table>tfoot>tr>th,
      .table>thead>tr>td,
      .table>thead>tr>th {
         padding-left: 2px;
         padding-right: 2px;

      }
         .sales-footer-dues th { border: 0; padding: 10px 6px; }
      .sales-footer-dues th:first-child { text-align: left; }
      .sales-footer-dues th:last-child { text-align: right; white-space: nowrap; }
      @media (min-width: 992px) {
         .sales-footer-left { grid-template-columns: 16% 43% 41%; }
         .sales-footer-left > .sales-footer-summary { display: contents; }
         .sales-footer-summary > div:first-child { grid-column: 1; grid-row: 1; width: auto; padding: 0 4px; }
         .sales-footer-summary > div:nth-child(2) { grid-column: 3; grid-row: 1; width: auto; padding: 0 4px; }
         .sales-footer-left > .sales-footer-advance { grid-column: 2; grid-row: 1; margin: 0 0 8px; }
         .sales-footer-advance > .col-sm-12 { padding: 0 4px; gap: 4px; }
         .sales-footer-summary, .sales-footer-advance { font-size: 11px; }
         .sales-footer-summary .input-group { max-width: 125px !important; }
         .sales-footer-summary .input-group-addon { width: 65px !important; min-width: 60px !important; }
         .sales-advance-input { flex-basis: 65px; max-width: 85px; }
         .sales-footer-summary .form-control, .sales-footer-advance .form-control { height: 30px; padding: 4px; font-size: 11px; }
      }
</style>
</head>


<body class="hold-transition skin-blue sidebar-mini">
   <div class="wrapper">


      <?php include "sidebar.php"; ?>

      <?php
      if (isset($sales_id)) {
         ensure_sales_task27_fields();
         $q2 = $this->db->query("select * from db_sales where id=$sales_id");
         $customer_id = $q2->row()->customer_id;
         $salesman_id = $q2->row()->salesman_id;
         $sales_date = show_date($q2->row()->sales_date);
         $due_date = (!empty($q2->row()->due_date)) ? show_date($q2->row()->due_date) : '';
         $sales_status = $q2->row()->sales_status;
         $warehouse_id = $q2->row()->warehouse_id;
         $reference_no = $q2->row()->reference_no;
         $discount_input = store_number_format($q2->row()->discount_to_all_input, 0);
         $discount_type = $q2->row()->discount_to_all_type;
         $other_charges_input = store_number_format($q2->row()->other_charges_input, 0);
         $other_charges_tax_id = $q2->row()->other_charges_tax_id;
         $sales_note = $q2->row()->sales_note;
         $store_id = $q2->row()->store_id;

         $init_code = $q2->row()->init_code;
         $count_id = $q2->row()->count_id;

         $coupon_id = $q2->row()->coupon_id;
         $coupon_code = (!empty($coupon_id)) ? get_customer_coupon_details($coupon_id)->code : '';
         $invoice_terms = $q2->row()->invoice_terms;
         $payment_terms = isset($q2->row()->payment_terms) ? $q2->row()->payment_terms : '';
         ensure_sales_customer_trn_no();
         ensure_sales_task18_fields();
         ensure_sales_task24_fields();
         ensure_sales_task27_fields();
         $customer_trn_no = isset($q2->row()->customer_trn_no) ? $q2->row()->customer_trn_no : '';
         $invoice_type = (isset($q2->row()->invoice_type) && !empty($q2->row()->invoice_type)) ? $q2->row()->invoice_type : 'Cash';
         $card_type = isset($q2->row()->card_type) ? $q2->row()->card_type : '';
         $payment_type = isset($q2->row()->payment_type) ? $q2->row()->payment_type : '';
         $customer_mobile = isset($q2->row()->customer_mobile) ? $q2->row()->customer_mobile : '';
         $customer_email = isset($q2->row()->customer_email) ? $q2->row()->customer_email : '';
         $address = isset($q2->row()->address) ? $q2->row()->address : '';
         $whatsapp_no = isset($q2->row()->whatsapp_no) ? $q2->row()->whatsapp_no : '';
         if (empty($customer_mobile) && !empty($customer_id)) {
            $cust_row = $this->db->select('mobile, email')->where('id', $customer_id)->get('db_customers')->row();
            if (!empty($cust_row)) {
               $customer_mobile = !empty($cust_row->mobile) ? $cust_row->mobile : '';
               if (empty($customer_email)) {
                  $customer_email = !empty($cust_row->email) ? $cust_row->email : '';
               }
            }
         }
         if (empty($whatsapp_no) && !empty($customer_mobile)) {
            $whatsapp_no = $customer_mobile;
         }


         $items_count = $this->db->query("select count(*) as items_count from db_salesitems where sales_id=$sales_id")->row()->items_count;
      } else if (isset($quotation_id) && !empty($quotation_id)) {
         $q2 = $this->db->query("select * from db_quotation where id=$quotation_id");
         $customer_id = $q2->row()->customer_id;
         $salesman_id = '';
         $sales_date = show_date($q2->row()->quotation_date);
         $due_date = '';
         $sales_status = '';
         $warehouse_id = $q2->row()->warehouse_id;
         $reference_no = $q2->row()->reference_no;
         $discount_input = store_number_format($q2->row()->discount_to_all_input, 0);
         $discount_type = $q2->row()->discount_to_all_type;
         $other_charges_input = store_number_format($q2->row()->other_charges_input, 0);
         $other_charges_tax_id = $q2->row()->other_charges_tax_id;
         $sales_note = $q2->row()->quotation_note;
         $store_id = $q2->row()->store_id;

         //$sales_code = get_init_code('sales');
         $init_code = get_only_init_code('sales');
         $count_id = get_last_count_id('db_sales');

         $items_count = $this->db->query("select count(*) as items_count from db_quotationitems where quotation_id=$quotation_id")->row()->items_count;
         $coupon_code = '';

         $store_details = get_store_details($store_id);
         $invoice_terms = $store_details->invoice_terms;
         $payment_terms = '';
         $customer_trn_no = !empty($q2->row()->customer_trn_no) ? $q2->row()->customer_trn_no : get_customer_trn_no($customer_id);
         $address = '';
         $whatsapp_no = '';
      } else if (isset($deliverynote_id) && !empty($deliverynote_id)) {
         $q2 = $this->db->query("select * from db_deliverynote where id=$deliverynote_id");
         $customer_id = $q2->row()->customer_id;
         $salesman_id = '';
         $sales_date = show_date($q2->row()->deliverynote_date);
         $due_date = '';
         $sales_status = '';
         $warehouse_id = $q2->row()->warehouse_id;
         $reference_no = $q2->row()->reference_no;
         $discount_input = store_number_format($q2->row()->discount_to_all_input, 0);
         $discount_type = $q2->row()->discount_to_all_type;
         $other_charges_input = store_number_format($q2->row()->other_charges_input, 0);
         $other_charges_tax_id = $q2->row()->other_charges_tax_id;
         $sales_note = $q2->row()->deliverynote_note;
         $store_id = $q2->row()->store_id;

         $init_code = get_only_init_code('sales');
         $count_id = get_last_count_id('db_sales');

         $items_count = $this->db->query("select count(*) as items_count from db_deliverynoteitems where deliverynote_id=$deliverynote_id")->row()->items_count;
         $coupon_code = '';

         $store_details = get_store_details($store_id);
         $invoice_terms = $store_details->invoice_terms;
         $payment_terms = '';
         $customer_trn_no = '';
         $address = '';
         $whatsapp_no = '';
      } else {
         $customer_id  = $sales_date = $sales_status = $warehouse_id = $due_date = $salesman_id =
            $reference_no  = $coupon_code =
            $other_charges_input          = $other_charges_tax_id = $store_id =
            $discount_type  = $sales_note = $payment_terms = $customer_trn_no = '';
         $sales_date = show_date(date("d-m-Y"));
         $discount_input = $this->db->select("sales_discount")->get('db_store')->row()->sales_discount;
         $discount_input = ($discount_input == 0) ? 0 : $discount_input;

         $init_code = get_only_init_code('sales');
         $count_id = get_last_count_id('db_sales');

         $store_details = get_store_details();
         $invoice_terms = $store_details->invoice_terms;
         $whatsapp_no = '';
         ensure_sales_task27_fields();
      }
      if(!isset($customer_trn_no)){
         $customer_trn_no = '';
      }
      if(!isset($invoice_type) || empty($invoice_type)){
         $invoice_type = 'Cash';
      }
      if(!isset($card_type)){
         $card_type = '';
      }
      if(!isset($payment_type) || empty($payment_type)){
         $payment_type = 'Cash';
      }
      if(!isset($customer_mobile)){
         $customer_mobile = '';
      }
      if(!isset($customer_email)){
         $customer_email = '';
      }
      if(!isset($address)){
         $address = '';
      }
      if(!isset($whatsapp_no)){
         $whatsapp_no = '';
      }
      $default_payment_type = $payment_type;
      $default_account_id = '';
      if (isset($sales_id)) {
          $q_pay = $this->db->query("select payment_type, account_id from db_salespayments where sales_id=$sales_id order by id asc limit 1");
          if ($q_pay->num_rows() > 0) {
              if (!empty($q_pay->row()->payment_type) && (empty($default_payment_type) || $default_payment_type === 'Cash')) {
                  $default_payment_type = $q_pay->row()->payment_type;
              }
              if (!empty($q_pay->row()->account_id)) {
                  $default_account_id = $q_pay->row()->account_id;
              }
          }
      }
      if (empty($default_account_id)) {
          $q_acc = $this->db->query("select id from ac_accounts where status=1 and store_id=" . get_current_store_id() . " and account_name LIKE '%Cash in Hand%' limit 1");
          if ($q_acc->num_rows() > 0) {
              $default_account_id = $q_acc->row()->id;
          }
      }
      $branch_tax_store_id = !empty($store_id) ? $store_id : get_current_store_id();
      $branch_tax_enabled = is_branch_tax_enabled($branch_tax_store_id);
      // Default VAT % for summary label on page load (e.g. VAT (5%))
      $default_vat_pct = '';
      if ($branch_tax_enabled) {
         $tax_defaults = get_store_item_tax_defaults($branch_tax_store_id);
         if (!empty($tax_defaults->default_tax_id)) {
            $default_tax_row = $this->db->select('tax')->where('id', (int)$tax_defaults->default_tax_id)->get('db_tax')->row();
            if (!empty($default_tax_row) && isset($default_tax_row->tax)) {
               $default_vat_pct = rtrim(rtrim(number_format((float)$default_tax_row->tax, 2, '.', ''), '0'), '.');
            }
         }
      }
      // TASK-36: render Due Days immediately on edit/view, before JS runs.
      $due_days_initial = '';
      if (!empty($sales_date) && !empty($due_date)) {
         $invoice_date_obj = DateTime::createFromFormat('d-m-Y', $sales_date);
         $due_date_obj = DateTime::createFromFormat('d-m-Y', $due_date);
         if ($invoice_date_obj && $due_date_obj) {
            $due_days_initial = max(0, (int)$invoice_date_obj->diff($due_date_obj)->format('%r%a'));
         }
      }

      ?>



      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
         <!-- **********************MODALS***************** -->
         <?php include "modals/modal_customer.php"; ?>
         <?php include "modals/modal_item.php"; ?>
         <?php include "modals/modal_item_or_service.php"; ?>
         <?php /*include"modals/modal_service.php";*/ ?>
         <!-- **********************MODALS END***************** -->
         <!-- Content Header (Page header) -->
         <section class="content-header">
            <h1>
               <?= $page_title; ?>
               <small>Add/Update Sales</small>
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
               <li><a href="<?php echo $base_url; ?>sales"><?= $this->lang->line('sales_list'); ?></a></li>
               <li><a href="<?php echo $base_url; ?>sales/add"><?= $this->lang->line('new_sales'); ?></a></li>
               <li class="active"><?= $page_title; ?></li>
            </ol>
         </section>

         <!-- Main content -->
         <section class="content">
            <div class="row">
               <!-- ********** ALERT MESSAGE START******* -->
               <?php include "comman/code_flashdata.php"; ?>
               <?php include "modals/modal_sales_item.php"; ?>

               <!-- TASK-17: Cost Price popup (kept for compatibility) -->
               <div class="modal fade" id="item-cost-price-modal" tabindex="-1">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header header-custom">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                           </button>
                           <h4 class="modal-title text-center">Cost Price</h4>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="row invoice-info">
                                    <div class="col-md-12">
                                       <div class="col-sm-12 invoice-col">
                                          <div><b><?= get_item_barcode_label(); ?> : </b> <span id="cost_popup_item_barcode"></span></div>
                                          <div><b><?= $this->lang->line('item_name'); ?> : </b> <span id="cost_popup_item_name"></span></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <div class="col-md-12">
                                    <div class="box box-solid bg-gray">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="item_cost_price_popup_value">Cost Price</label>
                                                   <input type="text" class="form-control" id="item_cost_price_popup_value" value="0.00" readonly style="background-color:#fff;">
                                                </div>
                                             </div>
                                             <div class="clearfix"></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- TASK-26: Price History popup -->
               <?php include "modals/modal_sales_price_history.php"; ?>
               <!-- ********** ALERT MESSAGE END******* -->
               <!-- right column -->
               <div class="col-md-12">
                  <!-- Horizontal Form -->
                  <div class="box box-primary sales-page-panel">
                     <!-- style="background: #68deac;" -->

                     <!-- form start -->
                     <!-- OK START -->
                     <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'sales-form', 'enctype' => 'multipart/form-data', 'method' => 'POST')); ?>
                     <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                     <input type="hidden" value='1' id="hidden_rowcount" name="hidden_rowcount">
                     <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">
                     <input type="hidden" value='Final' id="sales_status" name="sales_status">
                     <input type="hidden" id="allow_negative_stock" value="<?= is_negative_stock_allowed() ? '1' : '0'; ?>">

                     <?php if (isset($quotation_id)) { ?>
                        <input type="hidden" id="quotation_id" name="quotation_id" value="<?php echo $quotation_id;; ?>">
                     <?php } ?>
                     <?php if (isset($deliverynote_id)) { ?>
                        <input type="hidden" id="deliverynote_id" name="deliverynote_id" value="<?php echo $deliverynote_id;; ?>">
                     <?php } ?>

                     <div class="box-body">
                        <!-- Store Code -->
                        <?php
                        /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>$store_id,'div_length'=>'col-sm-3')); }else{*/
                        echo "<input type='hidden' name='store_id' id='store_id' value='" . get_current_store_id() . "'>";
                        /*}*/
                        ?>
                        <!-- Store Code end -->
                        <!-- Warehouse Code -->

                        <!-- Warehouse Code end -->
                        <?php /* TASK-31: header — 3 fields per row */ ?>
                        <?php
                        // TASK-35: show Customer TRN when the branch has a TRN
                        // configured, even if this customer does not have one yet.
                        $trn_store_id = !empty($store_id) ? $store_id : get_current_store_id();
                        $show_customer_trn = (trim((string)$customer_trn_no) !== '' || is_branch_tax_enabled($trn_store_id));
                        ?>
                        <div class="form-group">
                           <div class="col-sm-4">
                              <label for="warehouse_id">Branch Name<label class="text-danger">*</label></label>
                              <select class="form-control select2 " id="warehouse_id" name="warehouse_id" style="width:100%;">
                                 <?= get_warehouse_select_list($warehouse_id, get_current_store_id()); ?>
                              </select>
                              <span id="warehouse_id_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="col-sm-4">
                              <label for="init_code"><?= $this->lang->line('invoice_no'); ?><label class="text-danger">*</label></label>
                              <div class="row" style="margin:0;">
                                 <div class="col-xs-7" style="padding-left:0;padding-right:2px;">
                                    <input type="text" value="<?= $init_code; ?>" class="form-control no-padding" style="font-size:18px;background-color:#eee;cursor:not-allowed;" id="init_code" name="init_code" placeholder="" readonly>
                                    <span id="init_code_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 <div class="col-xs-5" style="padding-left:2px;padding-right:0;">
                                    <input type="text" style="font-size:18px;background-color:#eee;cursor:not-allowed;" value="<?php echo  $count_id; ?>" class="form-control no_special_char text-center" id="count_id" name="count_id" placeholder="" readonly>
                                    <span id="count_id_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <label for="sales_date"><?= $this->lang->line('invoice_date'); ?> <label class="text-danger">*</label></label>
                              <div class="input-group date">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" class="form-control pull-right datepicker" id="sales_date" name="sales_date" readonly onkeyup="shift_cursor(event,'sales_status')" value="<?= $sales_date; ?>" style="background-color:#fff;cursor:pointer;">
                              </div>
                              <span id="sales_date_msg" style="display:none" class="text-danger"></span>
                           </div>
                        </div>

                        <div class="form-group">
                           <div class="col-sm-4">
                              <label for="customer_id"><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label></label>
                              <div class="input-group">
                                 <select class="form-control select2" id="customer_id" name="customer_id" style="width: 100%;">
                                    <?= get_customers_select_list($customer_id, get_current_store_id()); ?>
                                 </select>
                                 <span class="input-group-addon pointer" data-toggle="modal" data-target="#customer-modal" title="New Customer?"><i class="fa fa-user-plus text-primary fa-lg"></i></span>
                              </div>
                              <span id="customer_id_msg" style="display:none" class="text-danger"></span>
                              <span class="customer-previous-due-wrap"><lable><?= $this->lang->line('previous_due'); ?> :<label class="customer_previous_due text-red" style="font-size: 18px;">0.00</label></lable></span>

                           </div>
                           <div class="col-sm-4">
                              <label for="due_days">Due Days</label>
                              <select class="form-control select2" id="due_days" name="due_days" style="width:100%;">
                                 <option value="">-Select-</option>
                                 <?php for ($day = 1; $day <= 99; $day++) { ?>
                                 <option value="<?= $day; ?>" <?= ((int)$due_days_initial === $day) ? 'selected' : ''; ?>><?= $day; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-sm-4">
                              <label for="due_date"><?= $this->lang->line('due_date'); ?></label>
                              <div class="input-group date">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" class="form-control" id="due_date" name="due_date" value="<?= $due_date; ?>" readonly style="background-color:#eee;">
                              </div>
                              <span id="due_date_msg" style="display:none" class="text-danger"></span>
                           </div>
                        </div>

                        <div class="form-group">
                           <div class="col-sm-4">
                              <label for="invoice_type">Invoice Type</label>
                              <select class="form-control select2" id="invoice_type" name="invoice_type" style="width: 100%;">
                                 <option value="Cash" <?= (strcasecmp($invoice_type, 'Cash') == 0 || empty($invoice_type)) ? 'selected' : ''; ?>>Cash</option>
                                 <option value="Bank" <?= (strcasecmp($invoice_type, 'Bank') == 0) ? 'selected' : ''; ?>>Bank</option>
                                 <option value="Credit" <?= (strcasecmp($invoice_type, 'Credit') == 0) ? 'selected' : ''; ?>>Credit</option>
                              </select>
                              <span id="invoice_type_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="col-sm-4">
                              <label for="payment_type"><?= $this->lang->line('payment_type'); ?></label>
                              <select class="form-control select2" id='payment_type' name="payment_type" style="width: 100%;">
                                 <?php
                                 // Credit is controlled by Invoice Type, not Payment Type.
                                 $sale_payment_types = array('Cash', 'Card', 'Bank Transfer', 'Cheque', 'Wallet');
                                 echo "<option value=''>-Select-</option>";
                                 foreach ($sale_payment_types as $ptype) {
                                    $selected = (strcasecmp($ptype, $default_payment_type) == 0) ? 'selected' : '';
                                    if ($selected === '' && strcasecmp($default_payment_type, 'CASH') == 0 && strcasecmp($ptype, 'Cash') == 0) {
                                       $selected = 'selected';
                                    }
                                    if ($selected === '' && strcasecmp($default_payment_type, 'CREDIT') == 0 && strcasecmp($ptype, 'Credit') == 0) {
                                       $selected = 'selected';
                                    }
                                    echo "<option value='" . htmlspecialchars($ptype, ENT_QUOTES) . "' $selected>" . htmlspecialchars($ptype) . "</option>";
                                 }
                                 ?>
                              </select>
                              <span id="payment_type_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="col-sm-4">
                              <label for="card_type">Card Type</label>
                              <select class="form-control select2" id="card_type" name="card_type" style="width: 100%;">
                                 <option value="">-Select-</option>
                                 <option value="Visa" <?= (strcasecmp($card_type, 'Visa') == 0) ? 'selected' : ''; ?>>Visa</option>
                                 <option value="MasterCard" <?= (strcasecmp($card_type, 'MasterCard') == 0) ? 'selected' : ''; ?>>MasterCard</option>
                                 <option value="AMEX" <?= (strcasecmp($card_type, 'AMEX') == 0) ? 'selected' : ''; ?>>AMEX</option>
                              </select>
                              <span id="card_type_msg" style="display:none" class="text-danger"></span>
                           </div>
                        </div>

                        <div class="form-group">
                           <div class="col-sm-4">
                              <label for="customer_mobile">Mobile No</label>
                              <input type="text" value="<?= htmlspecialchars((string)$customer_mobile, ENT_QUOTES); ?>" class="form-control" id="customer_mobile" name="customer_mobile">
                              <span id="customer_mobile_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="col-sm-4">
                              <label for="whatsapp_no">WhatsApp No</label>
                              <div class="whatsapp-number-control">                             <input type="text" value="<?= htmlspecialchars((string)$whatsapp_no, ENT_QUOTES); ?>" class="form-control" id="whatsapp_no" name="whatsapp_no">
                                 <span class="input-group-btn"><button type="button" class="btn btn-default" id="copy-whatsapp-number" title="Copy Mobile No to WhatsApp No" aria-label="Copy Mobile No to WhatsApp No"><i class="fa fa-copy" aria-hidden="true"></i></button></span>
                              </div>
                              <span id="whatsapp_no_msg" style="display:none" class="text-danger"></span>
                           </div>
                           <div class="col-sm-4">
                              <label for="customer_email">Email Address</label>
                              <input type="text" value="<?= htmlspecialchars((string)$customer_email, ENT_QUOTES); ?>" class="form-control" id="customer_email" name="customer_email">
                              <span id="customer_email_msg" style="display:none" class="text-danger"></span>
                           </div>
                        </div>

                        <div class="form-group">
                           <div class="col-sm-4" id="customer_trn_no_col" style="<?= $show_customer_trn ? '' : 'display:none;' ?>">
                              <div id="customer_trn_no_label" style="<?= $show_customer_trn ? '' : 'display:none;' ?>">
                                 <label for="customer_trn_no" >Customer TRN</label>
                              </div>
                              <div id="customer_trn_no_wrap" style="<?= $show_customer_trn ? '' : 'display:none;' ?>">
                                 <input type="text" value="<?= htmlspecialchars($customer_trn_no, ENT_QUOTES); ?>" class="form-control" id="customer_trn_no" name="customer_trn_no">
                                 <span id="customer_trn_no_msg" style="display:none" class="text-danger"></span>
                              </div>
</div>
                           <div class="col-sm-4">
                              <label for="invoice_address">Address</label>
                              <input type="text" class="form-control" id="invoice_address" name="address" value="<?= htmlspecialchars((string)$address, ENT_QUOTES); ?>">
                           </div>
                           <div class="col-sm-4">
                              <label for="salesman_id">Salesperson</label>
                              <select class="form-control select2" id="salesman_id" name="salesman_id" style="width:100%;">
                                 <option value="">-Select-</option>
                                 <?= get_salesmans_select_list($salesman_id, $store_id); ?>
                              </select>
                           </div>
<div class="col-sm-4<?= $show_customer_trn ? ' remark-below-trn' : '' ?>" id="reference_no_col">
                              <label for="reference_no">Remark</label>
                              <input type="text" value="<?php echo  $reference_no; ?>" class="form-control " id="reference_no" name="reference_no" placeholder="">
                              <span id="reference_no_msg" style="display:none" class="text-danger"></span>
                           </div>

                        </div>


                        <input type="hidden" name="payment_terms" id="payment_terms" value="<?= htmlspecialchars((string)$payment_terms, ENT_QUOTES); ?>">

                     </div>
                     <!-- /.box-body -->

                     <div class="row">
                        <div class="col-md-12">
                           <div class="col-md-12">
                              <div class="box">
                                 <div class="box-info">
                                    <div class="box-header">
                                       <div class="col-md-8 col-md-offset-2 d-flex justify-content">
                                          <div class="input-group">
                                             <span class="input-group-addon" title="Select Items"><i class="fa fa-barcode"></i></span>
                                             <input type="text" class="form-control " placeholder="Search" autofocus id="item_search">


                                             <span class="input-group-addon pointer text-green show_item_service" title="Click to Add New Item or Service"><i class="fa fa-plus"></i></span>




                                          </div>
                                       </div>
                                    </div>
                                    <div class="box-body">
                                       <div class="table-responsive" style="width: 100%">
                                          <table class="table table-hover table-bordered" style="width:100%" id="sales_table">
                                             <thead class="custom_thead">
                                                <tr class="bg-primary">
                                                   <th rowspan='2' style="width:5%">Image</th>
                                                   <th rowspan='2' style="width:10%"><?= get_item_barcode_label(); ?></th>
                                                   <th rowspan='2' style="width:15%"><?= (get_item_barcode_label_key() === 'barcode_no') ? $this->lang->line('item_name') : 'Part Name'; ?></th>
                                                   <th rowspan='2' style="width:8%"><?= (!empty($this->lang->line('stock'))) ? $this->lang->line('stock') : 'Stock'; ?></th>
                                                   <th rowspan='2' style="width:10%;min-width: 180px;"><?= $this->lang->line('quantity'); ?></th>
                                                   <th rowspan='2' style="width:10%">Sale Price</th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('discount'); ?></th>
                                                   <th rowspan='2' class="branch-tax-col" style="width:10%"><?= (!empty($this->lang->line('vat_amount'))) ? $this->lang->line('vat_amount') : 'VAT Amount'; ?></th>
                                                   <th rowspan='2' class="branch-tax-col" style="width:5%"><?= (!empty($this->lang->line('vat_percentage'))) ? $this->lang->line('vat_percentage') : 'VAT %'; ?></th>
                                                   <th rowspan='2' style="width:7.5%">Amount</th>
                                                   <th rowspan='2' style="width:7.5%"><?= $this->lang->line('action'); ?></th>
                                                </tr>
                                             </thead>
                                             <tbody>

                                             </tbody>
                                          </table>
                                       </div>
                                    </div>
                                 </div>
                              </div>


                           </div>
                        </div>

                        <?php /* TASK-21: Footer — left (qty/discount/advance/paid/refund/note) | right (totals) */ ?>
                        <div class="col-md-6 sales-footer-left">
                           <!-- Hidden fields kept for JS/calc -->
                           <div style="display:none;">
                              <input type="text" class="form-control text-right only_currency" id="other_charges_input" name="other_charges_input" onkeyup="final_total();" value="<?php echo  $other_charges_input; ?>">
                              <select class="form-control" id="other_charges_tax_id" name="other_charges_tax_id" onchange="final_total();">
                                 <?= get_tax_select_list($other_charges_tax_id, get_current_store_id()); ?>
                              </select>
                              <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="<?= $coupon_code; ?>">
                              <span class="coupon_type">---</span>
                              <span class="coupon_value">0.00</span>
                              <strong id="coupon_code_msg"></strong>
                              <textarea class="form-control text-left" id='sales_note' name="sales_note"><?= $sales_note; ?></textarea>
                           </div>
                           <?php /* Invoice Terms - hidden (TASK-8) */ ?>
                           <textarea id="invoice_terms" name="invoice_terms" style="display:none;"><?= $invoice_terms; ?></textarea>

                           <div class="row sales-footer-summary" style="margin-bottom:8px;">
                              <div class="col-sm-4">
                                 <label class="control-label" style="margin-right:6px;"><?= $this->lang->line('quantity'); ?></label>
                                 <label class="control-label total_quantity text-success" style="font-size: 15pt;">0</label>
                              </div>
                              <div class="col-sm-8">
                                 <div class="form-group" style="margin-bottom:0;">
                                    <label for="discount_to_all_input" class="control-label" style="margin-right:6px;"><?= $this->lang->line('discount_on_all'); ?></label>
                                    <div class="input-group" style="max-width:280px;display:inline-table;vertical-align:middle;">
                                       <input type="text" class="form-control text-right only_currency" id="discount_to_all_input" name="discount_to_all_input" onkeyup="enable_or_disable_item_discount();" value="<?= store_number_format($discount_input, 0); ?>">
                                       <span class="input-group-addon" style="padding:0;border:0;width:110px;">
                                          <select class="form-control" onchange="final_total();" id='discount_to_all_type' name="discount_to_all_type" style="border-radius:0 3px 3px 0;">
                                             <?php /* TASK-11: default Fixed, then Per% */ ?>
                                             <option value='in_fixed' <?= (empty($discount_type) || $discount_type === 'in_fixed') ? 'selected' : ''; ?>>Fixed</option>
                                             <option value='in_percentage' <?= ($discount_type === 'in_percentage') ? 'selected' : ''; ?>>Per%</option>
                                          </select>
                                       </span>
                                    </div>
                                    <script type="text/javascript">
                                       <?php if ($discount_type != '') { ?>
                                          document.getElementById('discount_to_all_type').value = '<?php echo  $discount_type; ?>';
                                       <?php } else { ?>
                                          document.getElementById('discount_to_all_type').value = 'in_fixed';
                                       <?php } ?>
                                    </script>
                                 </div>
                              </div>
                           </div>

                                                      <?php
                           $saved_advance = 0; $saved_paid = 0; $saved_invoice_due = 0;
                           if (!empty($sales_id)) {
                              $saved_payments = $this->db->select('COALESCE(SUM(advance_adjusted),0) AS advance, COALESCE(SUM(payment),0) AS paid')->where('sales_id', (int)$sales_id)->get('db_salespayments')->row();
                              $saved_advance = (float)$saved_payments->advance;
                              $saved_paid = (float)$saved_payments->paid;
                              $saved_sale = $this->db->select('grand_total')->where('id', (int)$sales_id)->get('db_sales')->row();
                              $saved_invoice_due = (float)$saved_sale->grand_total - $saved_paid;
                           }
                           ?>
                           <input type="hidden" name="advance_controls" value="1">
                           <input type="hidden" id="saved_advance" value="<?= $saved_advance; ?>">
                           <input type="hidden" id="saved_nonadvance_paid" value="<?= $saved_paid - $saved_advance; ?>">
                           <input type="hidden" id="saved_invoice_due" value="<?= $saved_invoice_due; ?>">
                           <input type="hidden" id="saved_customer_id" value="<?= !empty($sales_id) ? (int)$customer_id : 0; ?>">
<div class="row payments_div payments_div_ sales-footer-advance" style="margin-bottom:8px;">
                              <div class="col-sm-12">
                                 <label style="margin-right:18px;">
                                    <?= $this->lang->line('advance'); ?> : <label class='customer_tot_advance' style="font-weight:bold;">0.00</label>
                                 </label>
                                 <label class="checkbox-inline" style="padding-left:0;margin-left:0;">
                                    <input type="checkbox" id="allow_tot_advance" name="allow_tot_advance" <?= $saved_advance > 0 ? 'checked' : ''; ?>> <?= $this->lang->line('adjust_advance_payment'); ?>
                                 </label>
                                 <div class="sales-advance-input">
                                    <label for="advance_amount" class="sr-only">Advance Amount</label>
                                    <input type="text" id="advance_amount" name="advance_amount" class="form-control text-right only_currency" value="<?= store_number_format($saved_advance, false); ?>" <?= $saved_advance > 0 ? '' : 'disabled'; ?>>
                                 </div>
                              </div>
                           </div>

                           <input type="hidden" name="account_id" id="account_id" value="<?= $default_account_id; ?>">
                           <div class="row">
                              <div class="col-sm-4">
                                 <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                                    <div class="box-body" style="padding:10px 0;">
                                       <label for="amount">Paid Amount</label>
                                       <input type="text" class="form-control text-right paid_amt only_currency" id="amount" name="amount" placeholder="">
                                       <span id="amount_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                                    <div class="box-body" style="padding:10px 0;">
                                       <label for="balance_due_amount">Balance Dues</label>
                                       <input type="text" class="form-control text-right" id="balance_due_amount" name="balance_due_amount" value="0.00" readonly disabled style="background-color:#eee;color:#555;cursor:not-allowed;">
                                       <span id="balance_due_amount_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                                    <div class="box-body" style="padding:10px 0;">
                                       <label for="refund_amount">Refund Amount</label>
                                       <input type="text" class="form-control text-right" id="refund_amount" name="refund_amount" value="0.00" readonly disabled style="background-color:#eee;color:#555;cursor:not-allowed;">
                                       <span id="refund_amount_msg" style="display:none" class="text-danger"></span>
                                    </div>
                                 </div>
                              </div>
                           </div>

                           <div class="row cheque_div" style="display: none;margin-bottom:8px;">
                              <div class="col-sm-8">
                                 <label for="cheque_number"><?= $this->lang->line('cheque_number'); ?></label>
                                 <input type="text" class="form-control" id="cheque_number" name="cheque_number" placeholder="">
                                 <span id="cheque_number_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="col-sm-4">
                                 <label for="cheque_period"><?= $this->lang->line('cheque_period_days'); ?></label>
                                 <input type="text" class="form-control only_currency" id="cheque_period" name="cheque_period" placeholder="">
                                 <span id="cheque_period_msg" style="display:none" class="text-danger"></span>
                              </div>
                           </div>

                           <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                              <div class="box-body" style="padding:10px 0;">
                                 <div class="sales-note-heading"><label for="payment_note"><?= $this->lang->line('note'); ?></label>
                     <div class="sales-send-options">
                        <label class="checkbox-inline" style="margin-right:20px;">
                           <input type="checkbox" id="send_invoice_whatsapp" name="send_invoice_whatsapp" value="1"> Send WhatsApp
                        </label>
                        <label class="checkbox-inline">
                           <input type="checkbox" id="send_invoice_email" name="send_invoice_email" value="1"> Send Email
                        </label>
                     </div>
</div>
                                 <textarea type="text" class="form-control" id="payment_note" name="payment_note" placeholder="" rows="2"></textarea>
                                 <span id="payment_note_msg" style="display:none" class="text-danger"></span>
                              </div>
                           </div>
                                             <?php /* TASK-27: Send WhatsApp / Send Email */ ?>


</div>

                        <div class="col-md-3 sales-footer-dues"><table class="table" style="margin-bottom:0;"><tr><th class="text-right" style="border:0;">Advance Amount</th><th class="text-right" style="border:0;" id="summary_advance_amount">0.00</th></tr>
                              <tr><th class="text-right" style="border:0;">Invoice Dues</th><th class="text-right" style="border:0;" id="invoice_dues">0.00</th></tr>
                              <tr><th class="text-right" style="border:0;">Previous Dues</th><th class="text-right" style="border:0;" id="previous_dues">0.00</th></tr>
                              <tr><th class="text-right" style="border:0;">Current Dues</th><th class="text-right" style="border:0;" id="current_dues">0.00</th></tr></table></div>
<div class="col-md-3 sales-footer-right">
                           <table class="table" style="margin-bottom:0;">
                              <tr>
                                 <th class="text-right" style="font-size: 17px;border:0;"><?= $this->lang->line('subtotal'); ?></th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="subtotal_amt" name="subtotal_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr style="display:none;">
                                 <th class="text-right" style="font-size: 17px;border:0;"><?= $this->lang->line('other_charges'); ?></th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="other_charges_amt" name="other_charges_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr style="display:none;">
                                 <th class="text-right" style="font-size: 17px;border:0;"><?= $this->lang->line('couponDiscount'); ?></th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="coupon_discount_amt" name="coupon_discount_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr>
                                 <th class="text-right" style="font-size: 17px;border:0;">Discount Amount</th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="discount_to_all_amt" name="discount_to_all_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr class="summary-trn-row"<?= $branch_tax_enabled ? '' : ' style="display:none;"' ?>>
                                 <th class="text-right" style="font-size: 17px;border:0;">Taxable Amount</th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="taxable_amt" name="taxable_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr class="summary-trn-row"<?= $branch_tax_enabled ? '' : ' style="display:none;"' ?>>
                                 <th class="text-right" style="font-size: 17px;border:0;" id="summary_vat_label"><?= ($default_vat_pct !== '') ? ('VAT Amount ('.$default_vat_pct.'%)') : 'VAT Amount'; ?></th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="summary_vat_amt" name="summary_vat_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr style="display:none;">
                                 <th class="text-right" style="font-size: 17px;border:0;"><?= $this->lang->line('round_off'); ?></th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="round_off_amt" name="tot_round_off_amt">0.00</b></h4>
                                 </th>
                              </tr>
                              <tr>
                                 <th class="text-right" style="font-size: 17px;border:0;">Net Amount</th>
                                 <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                                    <h4 style="margin:4px 0;"><b id="total_amt" name="total_amt">0.00</b></h4>
                                 </th>
                              </tr>
                                                         </table>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-xs-12 " style="display:none;">
                           <div class="col-sm-12">
                              <div class="box-body ">
                                 <div class="col-md-12">
                                    <table class="table table-hover table-bordered" style="width:100%" id="payments_table">
                                       <h4 class="box-title text-info"><?= $this->lang->line('previous_payments_information'); ?> : </h4>
                                       <thead>
                                          <tr class="bg-gray ">
                                             <th>#</th>
                                             <th><?= $this->lang->line('date'); ?></th>
                                             <th><?= $this->lang->line('payment_type'); ?></th>
                                             <th><?= $this->lang->line('payment_note'); ?></th>
                                             <th><?= $this->lang->line('payment'); ?></th>
                                             <th><?= $this->lang->line('action'); ?></th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <?php
                                          if (isset($sales_id)) {
                                             $q3 = $this->db->query("select * from db_salespayments where sales_id=$sales_id");
                                             if ($q3->num_rows() > 0) {
                                                $i = 1;
                                                $total_paid = 0;
                                                foreach ($q3->result() as $res3) {
                                                   echo "<tr class='text-center text-bold' id='payment_row_" . $res3->id . "'>";
                                                   echo "<td>" . $i . "</td>";
                                                   echo "<td>" . show_date($res3->payment_date) . "</td>";
                                                   echo "<td>" . $res3->payment_type . "</td>";
                                                   echo "<td>" . $res3->payment_note . "</td>";
                                                   echo "<td class='text-right' id='paid_amt_$i'>" . store_number_format($res3->payment) . "</td>";
                                                   echo '<td><i class="fa fa-trash text-red pointer" onclick="delete_payment(' . $res3->id . ')"> Delete</i></td>';
                                                   echo "</tr>";
                                                   $total_paid += $res3->payment;
                                                   $i++;
                                                }
                                                echo "<tr class='text-right text-bold'><td colspan='4' >Total</td><td data-rowcount='$i' id='paid_amt_tot'>" . store_number_format($total_paid) . "</td><td></td></tr>";
                                             } else {
                                                echo "<tr><td colspan='6' class='text-center text-bold'>No Previous Payments Found!!</td></tr>";
                                             }
                                          } else {
                                             echo "<tr><td colspan='6' class='text-center text-bold'>Payments Pending!!</td></tr>";
                                          }
                                          ?>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <!-- SMS Sender while saving -->
                        <?php
                        //Change Return
                        $send_sms_checkbox = 'disabled';
                        if ($CI->is_sms_enabled()) {
                           if (!isset($sales_id)) {
                              $send_sms_checkbox = 'checked';
                           } else {
                              $send_sms_checkbox = '';
                           }
                        }

                        ?>

                        <div class="col-xs-12 " style="display:none;">
                           <div class="col-sm-12">
                              <div class="box-body ">
                                 <div class="col-md-12">
                                    <div class="checkbox icheck">
                                       <label>
                                          <input type="checkbox" <?= $send_sms_checkbox; ?> class="form-control" id="send_sms" name="send_sms"> <label for="sales_discount" class=" control-label"><?= $this->lang->line('send_sms_to_customer'); ?>
                                             <i class="hover-q " data-container="body" data-toggle="popover" data-placement="top" data-content="If checkbox is Disabled! You need to enable it from SMS -> SMS API <br><b>Note:<i>Walk-in Customer will not receive SMS!</i></b>" data-html="true" data-trigger="hover" data-original-title="" title="Do you wants to send SMS ?">
                                                <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                             </i>
                                          </label>
                                       </label>
                                    </div>
                                 </div><!-- col-md-12 -->
                              </div>
                              <!-- /.box-body -->
                           </div>
                           <!-- /.box -->
                        </div>
                     </div>

                     <!-- /.box-body -->
                     <div class="box-footer sales-form-actions">
                        <center>
                           <?php
                           if (isset($sales_id)) {
                              $btn_id = 'update';
                              $btn_name = "Update";
                              $btn_print_id = 'update_and_print';
                              $btn_print_name = 'Update And Print';
                              echo '<input type="hidden" name="sales_id" id="sales_id" value="' . $sales_id . '"/>';
                           } else {
                              $btn_id = 'save';
                              $btn_name = "Save";
                              $btn_print_id = 'save_and_print';
                              $btn_print_name = 'Save And Print';
                           }

                           ?>
                           <div class="col-md-2 col-md-offset-3 col-xs-4">
                              <button type="button" id="<?php echo $btn_id; ?>" class="btn btn-block btn-success payments_modal" title="Save Data" style="font-size: 16px; font-weight: bold; height: 44px;"><?php echo $btn_name; ?></button>
                           </div>
                           <div class="col-md-2 col-xs-4">
                              <button type="button" id="<?php echo $btn_print_id; ?>" class="btn btn-block btn-info payments_modal" title="Save And Print Data" style="font-size: 16px; font-weight: bold; height: 44px;"><?php echo $btn_print_name; ?></button>
                           </div>
                           <div class="col-md-2 col-xs-4">
                              <a href="<?= base_url() ?>dashboard">
                                 <button type="button" class="btn btn-block btn-warning" title="Go Dashboard" style="font-size: 16px; font-weight: bold; height: 44px;">Close</button>
                              </a>
                           </div>
                        </center>
                     </div>


                     <?= form_close(); ?>
                     <!-- OK END -->
                  </div>
               </div>
               <!-- /.box-footer -->

            </div>
            <!-- /.box -->
         </section>
         <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      <?php include "footer.php"; ?>
      <!-- SOUND CODE -->
      <?php include "comman/code_js_sound.php"; ?>
      <!-- GENERAL CODE -->
      <?php include "comman/code_js.php"; ?>

      <script src="<?php echo $theme_link; ?>js/modals.js?v=<?= filemtime(FCPATH.'theme/js/modals.js'); ?>"></script>
      <script src="<?php echo $theme_link; ?>js/modals/modal_item.js"></script>
      <script>
         var STORE_TAX_ENABLED = <?= $branch_tax_enabled ? 'true' : 'false'; ?>;
         var ITEM_BARCODE_IS_PART_NO = <?= (get_item_barcode_label_key() === 'part_no') ? 'true' : 'false'; ?>;
         var DEFAULT_VAT_PCT = <?= ($default_vat_pct !== '') ? json_encode((string)$default_vat_pct) : 'null'; ?>;
      </script>
      <?php if(!$branch_tax_enabled): ?>
      <style>#sales_table .branch-tax-col{display:none !important;}      .sales-footer-dues th { border: 0; padding: 10px 6px; }
      .sales-footer-dues th:first-child { text-align: left; }
      .sales-footer-dues th:last-child { text-align: right; white-space: nowrap; }
      @media (min-width: 992px) {
         .sales-footer-left { grid-template-columns: 16% 43% 41%; }
         .sales-footer-left > .sales-footer-summary { display: contents; }
         .sales-footer-summary > div:first-child { grid-column: 1; grid-row: 1; width: auto; padding: 0 4px; }
         .sales-footer-summary > div:nth-child(2) { grid-column: 3; grid-row: 1; width: auto; padding: 0 4px; }
         .sales-footer-left > .sales-footer-advance { grid-column: 2; grid-row: 1; margin: 0 0 8px; }
         .sales-footer-advance > .col-sm-12 { padding: 0 4px; gap: 4px; }
         .sales-footer-summary, .sales-footer-advance { font-size: 11px; }
         .sales-footer-summary .input-group { max-width: 125px !important; }
         .sales-footer-summary .input-group-addon { width: 65px !important; min-width: 60px !important; }
         .sales-advance-input { flex-basis: 65px; max-width: 85px; }
         .sales-footer-summary .form-control, .sales-footer-advance .form-control { height: 30px; padding: 4px; font-size: 11px; }
      }
</style>
      <?php endif; ?>
      <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
   </div>
   <!-- ./wrapper -->
   <script type="text/javascript">
      var walk_in_customer_name = '<?= get_walk_in_customer_name(); ?>'
   </script>
   <!-- Bootstrap WYSIHTML5 -->
   <script src="<?php echo $theme_link; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

   <script>
      var pdf_part_number_popup_enabled = <?= is_pdf_part_number_popup_enabled() ? 'true' : 'false'; ?>;
   </script>
   <script src="<?php echo $theme_link; ?>js/sales.js?v=<?= filemtime(FCPATH.'theme/js/sales.js'); ?>"></script>
   <script>
      var syncing_payment_invoice = false;

      function show_cheque_details() {
         var payment_type = $("#payment_type").val();
         if (payment_type && payment_type.toUpperCase() == '<?= strtoupper(cheque_name()) ?>') {
            $(".cheque_div").show();
         } else {
            $(".cheque_div").hide();
            $("#cheque_period,#cheque_number").val('');
         }
      }

      function update_payment_options() {
         var invoice = $("#invoice_type").val();
         var selected = $("#payment_type").val();
         var allowed = invoice === 'Cash' ? ['Cash'] : (invoice === 'Bank' ? ['Card', 'Cheque', 'Bank Transfer', 'Wallet'] : []);
         var payment = $("#payment_type").empty();
         if (invoice !== 'Cash') payment.append(new Option('-Select-', ''));
         allowed.forEach(function(type) { payment.append(new Option(type, type)); });
         payment.val(allowed.indexOf(selected) >= 0 ? selected : (invoice === 'Cash' ? 'Cash' : ''));
         payment.prop('disabled', invoice === 'Credit').trigger('change.select2');
         update_payment_details();
      }
      function update_payment_details() {
         var card = $("#payment_type").val() === 'Card';
         if (!card) $("#card_type").val('').trigger('change.select2');
         $("#card_type").prop('disabled', !card).trigger('change.select2');
         show_cheque_details();
      }
      $("#invoice_type").on('change', update_payment_options);
      $("#payment_type").on('change', update_payment_details);
      update_payment_options();
      // Initial cheque visibility on page load
      show_cheque_details();


      $("#customer_id").on("change", function() {
         set_previous_due();
         set_customer_trn_no(true);
         set_customer_contact_info(true);
      });

      function set_customer_contact_info(from_customer_change) {
         var selected = $('option:selected', "#customer_id");
         var cust_mobile = (selected.attr('data-mobile') || '').toString().trim();
         var cust_email = (selected.attr('data-email') || '').toString().trim();
         var cust_address = (selected.attr('data-address') || '').toString().trim();
         if (from_customer_change) {
            $("#customer_mobile").val(cust_mobile);
            $("#customer_email").val(cust_email);
            $("#invoice_address").val(cust_address);
            // TASK-27: default WhatsApp No from customer mobile
            $("#whatsapp_no").val(cust_mobile);
         } else {
            if (!$("#customer_mobile").val()) {
               $("#customer_mobile").val(cust_mobile);
            }
            if (!$("#customer_email").val()) {
               $("#customer_email").val(cust_email);
            }
            if (!$("#invoice_address").val()) {
               $("#invoice_address").val(cust_address);
            }
            if (!$("#whatsapp_no").val()) {
               $("#whatsapp_no").val(cust_mobile);
            }
         }
      }

      function set_previous_due() {
         var selected_customer = $('option:selected', "#customer_id");
         // Hide Previous Due for cash customer CU-2026-0001 (and walk-in delete_bit=1)
         var customer_code = (selected_customer.attr('data-customer_code') || '').toString();
         var hide_previous_due = (customer_code === 'CU-2026-0001' || String(selected_customer.attr('data-delete_bit')) === '1');
         $(".customer-previous-due-wrap").toggle(!hide_previous_due);
         $(".customer_previous_due").html(selected_customer.attr('data-previous_due'));
         $(".customer_tot_advance").html(selected_customer.attr('data-tot_advance'));
      }

      function customer_trn_present() {
         return (($("#customer_trn_no").val() || '').toString().trim() !== '');
      }

      var branch_trn_enabled = <?= is_branch_tax_enabled($trn_store_id) ? 'true' : 'false'; ?>;
      var customer_trn_synced_once = false;
      function set_customer_trn_no(from_customer_change) {
         if (!$("#customer_trn_no_wrap").length) {
            if (typeof final_total === 'function') {
               final_total();
            }
            return;
         }
         var customer_trn = ($('option:selected', "#customer_id").attr('data-tax_number') || '').toString().trim();
         var current_trn = ($("#customer_trn_no").val() || '').toString().trim();
         // On customer change: use customer TRN. On first load: keep saved invoice TRN if present.
         if (from_customer_change || !customer_trn_synced_once) {
            if (customer_trn !== '') {
               $("#customer_trn_no").val(customer_trn);
               current_trn = customer_trn;
            } else if (from_customer_change) {
               $("#customer_trn_no").val('');
               current_trn = '';
            }
            customer_trn_synced_once = true;
         }
         // If TRN is not null then show (same as previous task requirement)
         if (current_trn !== '' || branch_trn_enabled) {
            $("#customer_trn_no_col, #customer_trn_no_label, #customer_trn_no_wrap").show(); $("#reference_no_col").addClass("remark-below-trn");
         } else {
            $("#customer_trn_no_col, #customer_trn_no_label, #customer_trn_no_wrap").hide(); $("#reference_no_col").removeClass("remark-below-trn");
         }
         if (typeof final_total === 'function') {
            final_total();
         }
      }
      $(document).on('keyup change', '#customer_trn_no', function() {
         var trn = ($(this).val() || '').toString().trim();
         if (trn !== '' || branch_trn_enabled) {
            $("#customer_trn_no_col, #customer_trn_no_label, #customer_trn_no_wrap").show(); $("#reference_no_col").addClass("remark-below-trn");
         }
         if (typeof final_total === 'function') {
            final_total();
         }
      });
      $(document).on('blur', '#customer_trn_no', function() {
         var trn = ($(this).val() || '').toString().trim();
         if (trn !== '' || branch_trn_enabled) {
            $("#customer_trn_no_col, #customer_trn_no_label, #customer_trn_no_wrap").show(); $("#reference_no_col").addClass("remark-below-trn");
         } else {
            $("#customer_trn_no_col, #customer_trn_no_label, #customer_trn_no_wrap").hide(); $("#reference_no_col").removeClass("remark-below-trn");
         }
         if (typeof final_total === 'function') {
            final_total();
         }
      });
      set_customer_trn_no(false);

      // Due Days display (same as PDF): Due Date - Invoice Date
      function parse_ui_date(val) {
         val = (val || '').toString().trim();
         if (!val) {
            return null;
         }
         var parts = val.split('-');
         if (parts.length !== 3) {
            return null;
         }
         var d = new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
         d.setHours(0, 0, 0, 0);
         return isNaN(d.getTime()) ? null : d;
      }
      var dueInvoiceType = $("#invoice_type").val();
      function update_due_days_display() {
         var credit = $("#invoice_type").val() === 'Credit';
         $("#due_days").prop('disabled', !credit);
         var invoiceType = $('#invoice_type').val();
         if (invoiceType !== dueInvoiceType || !credit) {
            $('#due_days').val('').trigger('change.select2');
         }
         dueInvoiceType = invoiceType;
         $('#due_date').prop('disabled', !credit);

         var inv = parse_ui_date($("#sales_date").val());
         var days = parseInt($("#due_days").val(), 10);
         if (!credit || !inv || !(days >= 1 && days <= 99)) {
            $("#due_date, #payment_terms").val('');
            return;
         }
         inv.setDate(inv.getDate() + days);
         var due = ('0' + inv.getDate()).slice(-2) + '-' + ('0' + (inv.getMonth() + 1)).slice(-2) + '-' + inv.getFullYear();
         $("#due_date").val(due);
         $("#payment_terms").val(days + ' days');
      }
      $("#due_days, #invoice_type, #sales_date").on('change changeDate', update_due_days_display);
      update_due_days_display();
      var base_url = $("#base_url").val();
      $("#store_id").on("change", function() {
         var store_id = $("#store_id").val();
         $.post(base_url + "sales/get_salesmans_select_list", {store_id: store_id}, function(result) {
            $("#salesman_id").html('<option value="">-Select-</option>' + result).val('').trigger('change');
         });
         $.post(base_url + "sales/get_customers_select_list", {
            store_id: store_id
         }, function(result) {
            $("#customer_id").html('').append(result).select2();
            $("#sales_table > tbody").empty();
            calculate_tax();
         });
         $.post(base_url + "sales/get_tax_select_list", {
            store_id: store_id
         }, function(result) {
            $("#other_charges_tax_id").html('').append(result).select2();
            calculate_tax();
         });
      });

      /*Warehouse*/
      $("#warehouse_id").on("change", function() {
         var warehouse_id = $(this).val();
         $("#sales_table > tbody").empty();
         final_total();
      });
      /*Warehouse end*/

      $(".close_btn").on("click", function() {
         if (confirm('Are you sure you want to navigate away from this page?')) {
            window.location = '<?php echo $base_url; ?>dashboard';
         }
      });
      //Initialize Select2 Elements
      $(".select2").select2();
      //Date picker
      $('.datepicker').datepicker({
         autoclose: true,
         format: 'dd-mm-yyyy',
         todayHighlight: true
      });
      // Invoice date: do not allow less than current date (destroy first — code_js.php already inits)
      if ($('#sales_date').data('datepicker')) {
         $('#sales_date').datepicker('remove');
      }
      $('#sales_date').datepicker({
         autoclose: true,
         format: 'dd-mm-yyyy',
         todayHighlight: true,
         startDate: '0d'
      });
      // If loaded invoice date is in the past, reset to today
      (function() {
         var sd = ($("#sales_date").val() || '').toString().trim().split('-');
         if (sd.length === 3) {
            var invDt = new Date(parseInt(sd[2], 10), parseInt(sd[1], 10) - 1, parseInt(sd[0], 10));
            var today0 = new Date();
            today0.setHours(0, 0, 0, 0);
            invDt.setHours(0, 0, 0, 0);
            if (!isNaN(invDt.getTime()) && invDt.getTime() < today0.getTime()) {
               var dd = ('0' + today0.getDate()).slice(-2);
               var mm = ('0' + (today0.getMonth() + 1)).slice(-2);
               var yyyy = today0.getFullYear();
               $("#sales_date").datepicker('update', dd + '-' + mm + '-' + yyyy);
            }
         }
      })();
      // Due date is calculated from the selected payment terms (destroy first — code_js.php already inits datepickers)
      if ($('#due_date').data('datepicker')) {
         $('#due_date').datepicker('remove');
      }
      update_due_days_display();
      /*if($("#warehouse_id").val()==''){
        $("#item_search").attr({
          disabled: true,
        });
        toastr["warning"]("Please Select Warehouse!!");
        failed.currentTime = 0; 
        failed.play();
       
      }*/

      /* ---------- CALCULATE TAX -------------*/


      /* Editable Discount column (no Click-to-Change popup) — treat entered value as line discount amount */
      function set_item_discount_from_amt(row_id) {
         var qty = parseFloat($("#td_data_" + row_id + "_3").val());
         qty = (isNaN(qty) || qty <= 0) ? 0 : qty;
         var disc_amt = parseFloat($("#td_data_" + row_id + "_8").val());
         disc_amt = (isNaN(disc_amt)) ? 0 : disc_amt;
         $("#item_discount_type_" + row_id).val('Fixed');
         // Fixed formula in set_tax_value: discount_amt = item_discount_input * qty
         var per_unit = (qty > 0) ? (disc_amt / qty) : disc_amt;
         $("#item_discount_input_" + row_id).val(per_unit);
         calculate_tax(row_id);
      }

      function calculate_tax(i) { //i=Row
         set_tax_value(i);

         //Find the Tax type and Tax amount
         var tax_type = $("#tr_tax_type_" + i).val();
         var tax_amount = $("#td_data_" + i + "_11").val();
         if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
            tax_amount = 0;
            $("#td_data_" + i + "_11").val(to_Fixed(0));
            tax_type = 'Exclusive';
         }

         var qty = $("#td_data_" + i + "_3").val();
         var sales_price = parseFloat($("#td_data_" + i + "_10").val());
         $("#td_data_" + i + "_4").val(sales_price);
         /*Discounr*/
         var discount_amt = $("#td_data_" + i + "_8").val();
         discount_amt = (isNaN(parseFloat(discount_amt))) ? 0 : parseFloat(discount_amt);

         var amt = parseFloat(qty) * sales_price; //Taxable

         var total_amt = amt - discount_amt;
         total_amt = (String(tax_type).trim().toLowerCase() === 'inclusive') ? total_amt : parseFloat(total_amt) + parseFloat(tax_amount);

         //Set Unit cost
         $("#td_data_" + i + "_9").val('').val(to_Fixed(total_amt));

         final_total();
      }


      /* ---------- CALCULATE GST END -------------*/

      /*Calculate Coupon Discount Amount*/
      const discount_coupon_tot = function(subtotal) {
         var coupon_value = parseFloat($(".coupon_value").text());
         coupon_value = isNaN(coupon_value) ? 0 : coupon_value;

         var coupon_type = $(".coupon_type").text();

         var discount_amt = 0;
         if (coupon_type != '' && coupon_value > 0) {
            if (coupon_type == 'Percentage') {
               discount_amt = (subtotal * coupon_value) / 100;
            } else { //Fixed
               discount_amt = coupon_value;
            }
         }
         return discount_amt;
      }

      /* ---------- Final Description of amount ------------*/
      var fixed_invoice_discount_lock = false;

      function apply_fixed_invoice_discount_to_rows() {
         if (fixed_invoice_discount_lock) {
            return false;
         }
         fixed_invoice_discount_lock = true;

         try {
         var discount_input = parseFloat($("#discount_to_all_input").val());
         discount_input = isNaN(discount_input) ? 0 : discount_input;
         var discount_type = $("#discount_to_all_type").val();
         if (discount_type !== 'in_fixed' || discount_input <= 0) {
            return false;
         }

         var rowcount = $("#hidden_rowcount").val();
         var rows = [];
         var rows_total = 0;

         for (var i = 1; i <= rowcount; i++) {
            if (!document.getElementById("td_data_" + i + "_3")) {
               continue;
            }
            var qty = parseFloat($("#td_data_" + i + "_3").val());
            qty = isNaN(qty) ? 0 : qty;
            if (qty <= 0) {
               continue;
            }
            var line_total = parseFloat($("#td_data_" + i + "_9").val());
            line_total = isNaN(line_total) ? 0 : line_total;
            var line_vat = parseFloat($("#td_data_" + i + "_11").val());
            line_vat = isNaN(line_vat) ? 0 : line_vat;
            if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
               line_vat = 0;
            }
            var line_subtotal = Math.max(0, line_total - line_vat);
            if (line_subtotal > 0) {
               rows.push({
                  row_id: i,
                  qty: qty,
                  subtotal: line_subtotal
               });
               rows_total += line_subtotal;
            }
         }

         if (rows.length === 0 || rows_total <= 0) {
            return false;
         }

         var distributed_total = 0;
         for (var idx = 0; idx < rows.length; idx++) {
            var row = rows[idx];
            var row_discount = (idx === rows.length - 1)
               ? Math.max(0, discount_input - distributed_total)
               : (discount_input * row.subtotal) / rows_total;
            row_discount = parseFloat(row_discount.toFixed(2));
            if (row_discount < 0) {
               row_discount = 0;
            }
            var unit_discount = (row.qty > 0) ? (row_discount / row.qty) : 0;
            $("#item_discount_type_" + row.row_id).val('Fixed');
            $("#item_discount_input_" + row.row_id).val(to_Fixed(unit_discount));
            $("#td_data_" + row.row_id + "_8").val(to_Fixed(row_discount));
            distributed_total += row_discount;
            calculate_tax(row.row_id);
         }

         return true;
         } finally {
            fixed_invoice_discount_lock = false;
         }
      }

      function final_total() {
         var rowcount = $("#hidden_rowcount").val();
         var pretax_subtotal = 0;
         var amount_sum = 0;
         var line_discount_sum = 0;
         var vat_sum = 0;
         var total_quantity = 0;
         var vat_pct = null;
         var mixed_vat = false;

         var other_charges_per_amt = parseFloat(0);
         var other_charges_total_amt = 0;
         var other_charges_input = 0;
         if ($("#other_charges_input").val() != null && $("#other_charges_input").val() != '') {
            var other_charges_tax_id = $('option:selected', '#other_charges_tax_id').attr('data-tax');
            other_charges_input = parseFloat($("#other_charges_input").val()) || 0;
            if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
               other_charges_tax_id = 0;
            }
            if (other_charges_tax_id > 0) {
               other_charges_per_amt = (other_charges_tax_id * other_charges_input) / 100;
            }
            other_charges_total_amt = parseFloat(other_charges_per_amt) + parseFloat(other_charges_input);
         }

         for (var i = 1; i <= rowcount; i++) {
            if (document.getElementById("td_data_" + i + "_3")) {
               if ($("#td_data_" + i + "_3").val() != null && $("#td_data_" + i + "_3").val() != '') {
                  var line_amt = parseFloat($("#td_data_" + i + "_9").val());
                  line_amt = isNaN(line_amt) ? 0 : line_amt;
                  var line_vat = parseFloat($("#td_data_" + i + "_11").val());
                  line_vat = isNaN(line_vat) ? 0 : line_vat;
                  if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
                     line_vat = 0;
                  }
                  vat_sum += line_vat;
                  pretax_subtotal += (line_amt - line_vat);
                  amount_sum += line_amt;
                  line_discount_sum += parseFloat($("#td_data_" + i + "_8").val()) || 0;
                  total_quantity += parseFloat($("#td_data_" + i + "_3").val()) || 0;

                  var row_tax = parseFloat($("#tr_tax_value_" + i).val());
                  if (!isNaN(row_tax)) {
                     if (vat_pct === null) {
                        vat_pct = row_tax;
                     } else if (parseFloat(vat_pct) !== parseFloat(row_tax)) {
                        mixed_vat = true;
                     }
                  }
               }
            }
         }

         var discount_input = parseFloat($("#discount_to_all_input").val());
         discount_input = isNaN(discount_input) ? 0 : discount_input;
         var discount_type = $("#discount_to_all_type").val();
         var did_apply_fixed_distribution = false;
         if (discount_input > 0 && discount_type == 'in_fixed') {
            did_apply_fixed_distribution = apply_fixed_invoice_discount_to_rows();
         }

         // Recalculate subtotal after the distributed fixed discount is synced to each row.
         pretax_subtotal = 0;
         amount_sum = 0;
         line_discount_sum = 0;
         vat_sum = 0;
         total_quantity = 0;
         vat_pct = null;
         mixed_vat = false;
         for (var i = 1; i <= rowcount; i++) {
            if (document.getElementById("td_data_" + i + "_3")) {
               if ($("#td_data_" + i + "_3").val() != null && $("#td_data_" + i + "_3").val() != '') {
                  var line_amt = parseFloat($("#td_data_" + i + "_9").val());
                  line_amt = isNaN(line_amt) ? 0 : line_amt;
                  var line_vat = parseFloat($("#td_data_" + i + "_11").val());
                  line_vat = isNaN(line_vat) ? 0 : line_vat;
                  if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
                     line_vat = 0;
                  }
                  vat_sum += line_vat;
                  pretax_subtotal += (line_amt - line_vat);
                  amount_sum += line_amt;
                  line_discount_sum += parseFloat($("#td_data_" + i + "_8").val()) || 0;
                  total_quantity += parseFloat($("#td_data_" + i + "_3").val()) || 0;

                  var row_tax = parseFloat($("#tr_tax_value_" + i).val());
                  if (!isNaN(row_tax)) {
                     if (vat_pct === null) {
                        vat_pct = row_tax;
                     } else if (parseFloat(vat_pct) !== parseFloat(row_tax)) {
                        mixed_vat = true;
                     }
                  }
               }
            }
         }

         vat_sum += other_charges_per_amt;
         $(".total_quantity").html(format_qty(total_quantity));

         if (pretax_subtotal != 0 || vat_sum != 0 || other_charges_input != 0) {
            var taxable = pretax_subtotal + other_charges_input;

            var coupon_amt = discount_coupon_tot(taxable);
            taxable -= coupon_amt;

            var discount = 0;
            if (discount_input > 0) {
               if (discount_type == 'in_fixed') {
                  discount = discount_input;
                  if (did_apply_fixed_distribution) {
                     taxable = pretax_subtotal + other_charges_input;
                     taxable -= coupon_amt;
                  } else {
                     taxable -= discount_input;
                  }
               } else if (discount_type == 'in_percentage') {
                  discount = (taxable * discount_input) / 100;
                  taxable -= discount;
               }
            }
            discount = parseFloat(discount) || 0;
            // Fixed invoice discounts are already distributed into the row discounts.
            discount = line_discount_sum + (did_apply_fixed_distribution ? 0 : discount);
            taxable = amount_sum - discount - vat_sum + other_charges_total_amt - coupon_amt;
            if (taxable < 0) {
               taxable = 0;
            }

            var has_trn = (typeof STORE_TAX_ENABLED !== 'undefined' && STORE_TAX_ENABLED);
            if (has_trn) {
               $(".summary-trn-row").show();
               $("#taxable_amt").html(to_Fixed(taxable));
               $("#summary_vat_amt").html(to_Fixed(vat_sum));
               if (!mixed_vat && vat_pct !== null && !isNaN(vat_pct)) {
                  $("#summary_vat_label").text('VAT Amount (' + parseFloat(vat_pct) + '%)');
               } else if (typeof DEFAULT_VAT_PCT !== 'undefined' && DEFAULT_VAT_PCT !== null && DEFAULT_VAT_PCT !== '') {
                  $("#summary_vat_label").text('VAT Amount (' + DEFAULT_VAT_PCT + '%)');
               } else {
                  $("#summary_vat_label").text('VAT Amount');
               }
            } else {
               $(".summary-trn-row").hide();
               $("#taxable_amt").html('0.00');
               $("#summary_vat_amt").html('0.00');
            }

            var net_before_round = has_trn ? (taxable + vat_sum) : taxable;
            var subtotal_round = round_off(net_before_round);
            var subtotal_diff = subtotal_round - net_before_round;

            $("#subtotal_amt").html(to_Fixed(amount_sum));
            $("#other_charges_amt").html(to_Fixed(other_charges_total_amt));
            $("#coupon_discount_amt").html(to_Fixed(coupon_amt));
            $("#discount_to_all_amt").html(to_Fixed(discount));
            $("#hidden_discount_to_all_amt").val(to_Fixed(discount));
            $("#round_off_amt").html(to_Fixed(subtotal_diff));
            $("#total_amt").html(to_Fixed(subtotal_round));
            $("#hidden_total_amt").val(to_Fixed(subtotal_round));
            update_refund_amount();
         } else {
            var has_trn = (typeof STORE_TAX_ENABLED !== 'undefined' && STORE_TAX_ENABLED);
            if (has_trn) {
               $(".summary-trn-row").show();
               if (typeof DEFAULT_VAT_PCT !== 'undefined' && DEFAULT_VAT_PCT !== null && DEFAULT_VAT_PCT !== '') {
                  $("#summary_vat_label").text('VAT Amount (' + DEFAULT_VAT_PCT + '%)');
               } else {
                  $("#summary_vat_label").text('VAT Amount');
               }
            } else {
               $(".summary-trn-row").hide();
            }
            $("#subtotal_amt").html('0.00');
            $("#taxable_amt").html('0.00');
            $("#summary_vat_amt").html('0.00');
            $("#round_off_amt").html('0.00');
            $("#total_amt").html('0.00');
            $("#hidden_total_amt").val('0.00');
            $("#discount_to_all_amt").html('0.00');
            $("#hidden_discount_to_all_amt").val('0.00');
            $("#other_charges_amt").html('0.00');
            $("#coupon_discount_amt").html('0.00');
            update_refund_amount();
         }
      }

      // Cash / walk-in customer (CU-2026-0001): lock 'Adjust Advance Payment' and show all dues as 0
      var CASH_CUSTOMER_CODE = 'CU-2026-0001';
      function is_cash_customer_selected() {
         return (($('option:selected', '#customer_id').attr('data-customer_code') || '').toString().trim() === CASH_CUSTOMER_CODE);
      }
      function apply_cash_customer_rules() {
         var cash = is_cash_customer_selected();
         var $cb = $('#allow_tot_advance');
         if (!$cb.length) { return cash; }
         if (cash) {
            $cb.prop('checked', false);
            $('#advance_amount').prop('disabled', true).val('0.00');
         }
         $cb.prop('disabled', cash);
         if ($.fn.iCheck) { $cb.iCheck('update'); }
         return cash;
      }

      function update_refund_amount() {
         var cashCustomer = apply_cash_customer_rules();
         if (!$("#refund_amount").length) {
            return;
         }
         var paid = parseFloat($("#amount").val());
         paid = isNaN(paid) ? 0 : paid;
         var net = parseFloat($("#total_amt").text());
         net = isNaN(net) ? 0 : net;
         var advance = $('#allow_tot_advance').prop('checked') ? (parseFloat($('#advance_amount').val()) || 0) : 0;
         var savedPaid = parseFloat($('#saved_nonadvance_paid').val()) || 0;
         var totalPaid = paid + advance + savedPaid;
         var previous = parseFloat(($('option:selected', '#customer_id').attr('data-previous_due') || '0').replace(/,/g, '')) || 0;
         if (String($('#customer_id').val()) === String($('#saved_customer_id').val()) && String($('option:selected', '#customer_id').attr('data-delete_bit')) !== '1') {
            previous -= parseFloat($('#saved_invoice_due').val()) || 0;
         }
         var invoiceDues = net - totalPaid;
         $('#summary_advance_amount').text(to_Fixed(cashCustomer ? 0 : advance));
         $('#invoice_dues').text(to_Fixed(cashCustomer ? 0 : invoiceDues));
         $('#previous_dues').text(to_Fixed(cashCustomer ? 0 : previous));
         $('#current_dues').text(to_Fixed(cashCustomer ? 0 : (invoiceDues + previous)));
         var refund = Math.max(0, paid + savedPaid - net);
         var balance_due = Math.max(0, net - paid - savedPaid);
         $("#refund_amount").val(to_Fixed(refund));
         if ($("#balance_due_amount").length) {
            $("#balance_due_amount").val(to_Fixed(balance_due));
         }
      }
      $(document).on('input keyup change blur', '#amount, #advance_amount', function() {
         update_refund_amount();
      });
      $('#allow_tot_advance').on('change ifChanged', function() {
         $('#advance_amount').prop('disabled', !this.checked);
         if (!this.checked) $('#advance_amount').val('0.00');
         update_refund_amount();
      });
      $('#customer_id').on('change', update_refund_amount);
      /* ---------- Final Description of amount end ------------*/

      function removerow(id) { //id=Rowid

         $("#row_" + id).remove();
         final_total();
         failed.currentTime = 0;
         failed.play();
      }



      function enable_or_disable_item_discount() {
         /*var discount_input=parseFloat($("#discount_to_all_input").val());
         discount_input = isNaN(discount_input) ? 0 : discount_input;
         if(discount_input>0){
           $(".item_discount").attr({
             'readonly': true,
             'style': 'border-color:red;cursor:no-drop',
           });
         }
         else{
           $(".item_discount").attr({
             'readonly': false,
             'style': '',
           });
         }*/

         var rowcount = $("#hidden_rowcount").val();
         for (k = 1; k <= rowcount; k++) {
            if (document.getElementById("tr_item_id_" + k)) {
               calculate_tax(k);
            } //if end
         } //for end

         //final_total();
      }

      $(document).ready(function() {
         set_previous_due();
         update_refund_amount();
      });

      // TASK-17: Cost Price popup (kept)
      function show_item_cost_price_popup(row_id) {
         var cost_price = $("#tr_item_cost_price_" + row_id).val();
         if (cost_price === undefined || cost_price === null || cost_price === '') {
            cost_price = '0.00';
         }
         var item_name = $("#td_data_" + row_id + "_1").text();
         var item_barcode = $("#td_" + row_id + "_barcode").text();
         $("#cost_popup_item_name").text($.trim(item_name));
         $("#cost_popup_item_barcode").text($.trim(item_barcode));
         $("#item_cost_price_popup_value").val(cost_price);
         $("#item-cost-price-modal").modal('show');
      }

      // TASK-26: Amount ... -> Price History popup
      //Sale Items Modal Operations Start
      function show_sales_item_modal(row_id) {
         $('#sales_item').modal('toggle');
         $("#popup_tax_id").select2();

         //Find the item details
         var item_name = $("#td_data_" + row_id + "_1").html();
         var tax_type = $("#tr_tax_type_" + row_id).val();
         var tax_id = $("#tr_tax_id_" + row_id).val();
         var description = $("#description_" + row_id).val();

         /*Discount*/
         var item_discount_input = $("#item_discount_input_" + row_id).val();
         var item_discount_type = $("#item_discount_type_" + row_id).val();

         //Set to Popup
         $("#item_discount_input").val(item_discount_input);
         $("#item_discount_type").val(item_discount_type).select2();

         $("#popup_item_name").html(item_name);
         $("#popup_tax_type").val(tax_type).select2();
         $("#popup_tax_id").val(tax_id).select2();
         $("#popup_description").val(description);
         $("#popup_row_id").val(row_id);
      }

      function set_info() {
         var row_id = $("#popup_row_id").val();
         var tax_type = $("#popup_tax_type").val();
         var tax_id = $("#popup_tax_id").val();
         var description = $("#popup_description").val();
         var tax_name = ($('option:selected', "#popup_tax_id").attr('data-tax-value'));
         var tax = parseFloat($('option:selected', "#popup_tax_id").attr('data-tax'));

         /*Discounr*/
         var item_discount_input = $("#item_discount_input").val();
         var item_discount_type = $("#item_discount_type").val();

         //Set it into row 
         $("#item_discount_input_" + row_id).val(item_discount_input);
         $("#item_discount_type_" + row_id).val(item_discount_type);

         $("#tr_tax_type_" + row_id).val(tax_type);
         $("#tr_tax_id_" + row_id).val(tax_id);
         $("#tr_tax_value_" + row_id).val(tax); //%
         $("#description_" + row_id).val(description);
         var display_tax_name = tax_name ? tax_name.replace(/vat/gi, '').trim() : '';
         if(!display_tax_name && !isNaN(tax)){
            display_tax_name = tax + '%';
         }
         $("#td_data_" + row_id + "_12").html(display_tax_name);

         calculate_tax(row_id);
         $('#sales_item').modal('toggle');
      }

      function set_tax_value(row_id) {
         //get the sales price of the item
         var tax_type = $("#tr_tax_type_" + row_id).val();
         var tax = $("#tr_tax_value_" + row_id).val(); //%
         // Branch TRN empty => do not process tax
         if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
            tax = 0;
         }
         var qty = $("#td_data_" + row_id + "_3").val();
         qty = (isNaN(qty)) ? 0 : qty;
         var sales_price = parseFloat($("#td_data_" + row_id + "_10").val());
         sales_price = (isNaN(sales_price)) ? 0 : sales_price;
         sales_price = sales_price * qty;

         /*Discount*/
         var item_discount_type = $("#item_discount_type_" + row_id).val();
         var item_discount_input = parseFloat($("#item_discount_input_" + row_id).val());
         item_discount_input = (isNaN(item_discount_input)) ? 0 : item_discount_input;

         //Calculate discount      
         var discount_amt = (item_discount_type == 'Percentage') ? ((sales_price) * item_discount_input) / 100 : (item_discount_input * qty);
         sales_price -= parseFloat(discount_amt);

         var tax_amount = (String(tax_type).trim().toLowerCase() === 'inclusive') ? calculate_inclusive(sales_price, tax) : calculate_exclusive(sales_price, tax);
         if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
            tax_amount = 0;
         }

         $("#td_data_" + row_id + "_8").val(to_Fixed(discount_amt));

         $("#td_data_" + row_id + "_11").val(to_Fixed(tax_amount));
      }
      //Sale Items Modal Operations End
   </script>


   <!-- UPDATE OPERATIONS -->
   <script type="text/javascript">
      <?php if (isset($sales_id) || isset($quotation_id) || isset($deliverynote_id) || isset($order_id)) { ?>
         $(document).ready(function() {
            var base_url = '<?= base_url(); ?>';
            var path = '';
            var id = '';
            <?php if (isset($sales_id) && !empty($sales_id)) { ?>
               var id = '<?= $sales_id; ?>';
               var path = 'return_sales_list';
            <?php } ?>

            <?php if (isset($quotation_id) && !empty($quotation_id)) { ?>
               var id = '<?= $quotation_id; ?>';
               var path = "return_quotation_list";
            <?php } elseif (isset($deliverynote_id) && !empty($deliverynote_id)) { ?>
               var id = '<?= $deliverynote_id; ?>';
               var path = "return_deliverynote_list";
            <?php } ?>

            $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            $.post(base_url + "sales/" + path + "/" + id, {}, function(result) {
               //  alert(result);
               $('#sales_table tbody').append(result);
               $("#hidden_rowcount").val(parseInt(<?= $items_count; ?>) + 1);
               success.currentTime = 0;
               success.play();
               get_coupon_details();
               enable_or_disable_item_discount();
               $(".overlay").remove();
            });
         });
      <?php } ?>
   </script>
   <script>
      $(function() {
         //bootstrap WYSIHTML5 - text editor
         //$("#invoice_terms").wysihtml5()
      })
   </script>
   <!-- UPDATE OPERATIONS end-->

   <!-- Make sidebar menu hughlighter/selector -->
   <script>
      $(".<?php echo basename(__FILE__, '.php'); ?>-active-li").addClass("active");
   </script>
<script src="<?= $theme_link; ?>js/sales-whatsapp-copy.js?v=<?= filemtime(FCPATH.'theme/js/sales-whatsapp-copy.js'); ?>"></script>
<script src="<?= $theme_link; ?>js/sales-price-history.js?v=<?= filemtime(FCPATH.'theme/js/sales-price-history.js'); ?>"></script>
<script src="<?= $theme_link; ?>js/form-field-navigation.js?v=<?= filemtime(FCPATH.'theme/js/form-field-navigation.js'); ?>"></script>
<script src="<?= $theme_link; ?>js/sales-image-preview.js?v=<?= filemtime(FCPATH.'theme/js/sales-image-preview.js'); ?>"></script>
</body>

</html>
