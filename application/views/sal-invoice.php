<!DOCTYPE html>
<html>
<head>
<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>
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
      .sales-footer-left { display: grid; grid-template-columns: 60% 40%; align-content: start; }
      .sales-footer-left > * { grid-column: 1 / -1; min-width: 0; }
      .sales-footer-left > .sales-footer-summary { grid-column: 1; grid-row: 1; }
      .sales-footer-left > .sales-footer-advance { grid-column: 2; grid-row: 1; }
      .sales-footer-summary > div:first-child { width: 32%; }
      .sales-footer-summary > div:nth-child(2) { width: 68%; }
      .sales-footer-summary .form-group { margin-left: 0; margin-right: 0; }
      .sales-footer-summary .input-group { max-width: 190px !important; }
      .sales-footer-advance .checkbox-inline { white-space: nowrap; }
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
      @media (max-width: 767px) {
         .sales-page-panel {
            padding-left: 10px;
            padding-right: 10px;
         }
      }
  table.table-bordered>thead>tr>th { text-align: center; }
  .table>tbody>tr>td,
  .table>tbody>tr>th,
  .table>tfoot>tr>td,
  .table>tfoot>tr>th,
  .table>thead>tr>td,
  .table>thead>tr>th {
    padding-left: 2px;
    padding-right: 2px;
  }
  .view-readonly .form-control[readonly],
  .view-readonly .form-control[disabled],
  .view-readonly select[disabled],
  .view-readonly textarea[readonly] {
    background-color: #eee !important;
    cursor: not-allowed;
    opacity: 1;
  }
  .view-readonly .select2-container--default.select2-container--disabled .select2-selection--single {
    background-color: #eee;
    cursor: not-allowed;
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

  <?php include"sidebar.php"; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= $this->lang->line('invoice'); ?>
        <small>View Sales</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo $base_url; ?>sales"><?= $this->lang->line('sales_list'); ?></a></li>
        <li><a href="<?php echo $base_url; ?>sales/add"><?= $this->lang->line('new_sales'); ?></a></li>
        <li class="active"><?= $this->lang->line('invoice'); ?></li>
      </ol>
    </section>

    <?php
    $CI =& get_instance();

    ensure_sales_customer_trn_no();
    ensure_sales_task18_fields();
    ensure_sales_task27_fields();
    $q3=$this->db->query("SELECT b.coupon_id,b.coupon_amt, b.due_date,b.quotation_id,b.store_id,a.customer_name,a.mobile,a.phone,a.gstin,a.tax_number,a.email,a.shippingaddress_id,a.id,
                           a.opening_balance,a.country_id,a.state_id,a.city,
                           a.postcode,a.address,b.address AS invoice_address,b.sales_date,b.created_time,b.reference_no,
                           b.sales_code,b.init_code,b.count_id,b.sales_status,b.sales_note,b.invoice_terms,
                           b.warehouse_id,b.salesman_id,b.payment_terms,b.customer_trn_no,b.whatsapp_no,
                           b.invoice_type,b.payment_type,b.card_type,b.customer_mobile,b.customer_email,
                           coalesce(b.grand_total,0) as grand_total,
                           coalesce(b.subtotal,0) as subtotal,
                           coalesce(b.paid_amount,0) as paid_amount,
                           coalesce(b.other_charges_input,0) as other_charges_input,
                           other_charges_tax_id,
                           coalesce(b.other_charges_amt,0) as other_charges_amt,
                           discount_to_all_input,
                           b.discount_to_all_type,
                           coalesce(b.tot_discount_to_all_amt,0) as tot_discount_to_all_amt,
                           coalesce(b.round_off,0) as round_off,
                           b.payment_status,b.pos

                           FROM db_customers a,
                           db_sales b 
                           WHERE 
                           a.`id`=b.`customer_id` AND 
                           b.`id`='$sales_id' AND b.store_id=".get_current_store_id());

    $res3=$q3->row();
    if($res3->store_id!=get_current_store_id()){
      $CI->show_access_denied_page();exit();
    }
    $customer_id=$res3->id;
    $customer_name=$res3->customer_name;
    $invoice_type = !empty($res3->invoice_type) ? $res3->invoice_type : 'Cash';
    $payment_type = !empty($res3->payment_type) ? $res3->payment_type : '';
    if (empty($payment_type)) {
       $q_pay_view = $this->db->query("select payment_type from db_salespayments where sales_id=$sales_id order by id asc limit 1");
       if ($q_pay_view->num_rows() > 0 && !empty($q_pay_view->row()->payment_type)) {
          $payment_type = $q_pay_view->row()->payment_type;
       }
    }
    if (empty($payment_type)) {
       $payment_type = ($invoice_type === 'Credit') ? 'Credit' : (($invoice_type === 'Bank') ? 'Bank Transfer' : 'Cash');
    }
    if ($invoice_type === 'Credit') {
       $payment_type = '';
    } elseif ($invoice_type === 'Cash') {
       $payment_type = 'Cash';
    }
    $card_type = !empty($res3->card_type) ? $res3->card_type : '';
    $customer_mobile = !empty($res3->customer_mobile) ? $res3->customer_mobile : $res3->mobile;
    $customer_email = !empty($res3->customer_email) ? $res3->customer_email : $res3->email;
    $whatsapp_no = !empty($res3->whatsapp_no) ? $res3->whatsapp_no : $customer_mobile;
    $customer_tax_number=$res3->tax_number;
    $invoice_trn = '';
    if (!empty($res3->customer_trn_no)) {
      $invoice_trn = trim((string)$res3->customer_trn_no);
    } elseif (!empty($customer_tax_number)) {
      $invoice_trn = trim((string)$customer_tax_number);
    }
    $show_tax = is_branch_tax_enabled($res3->store_id);
    // TASK-35: keep the Customer TRN field visible when the branch TRN is set.
    $show_customer_trn = ($invoice_trn !== '' || $show_tax);
    $barcode_label = get_item_barcode_label();
    $item_name_label = (get_item_barcode_label_key() === 'barcode_no') ? $this->lang->line('item_name') : 'Part Name';
    $warehouse_id = $res3->warehouse_id;
    $warehouse_name = !empty($warehouse_id) ? get_warehouse_name($warehouse_id) : '';
    $sales_date_ui = show_date($res3->sales_date);
    $due_date_ui = (!empty($res3->due_date)) ? show_date($res3->due_date) : '';
    $reference_no = $res3->reference_no;
    $sales_code = $res3->sales_code;
    $init_code = !empty($res3->init_code) ? $res3->init_code : '';
    $count_id = !empty($res3->count_id) ? $res3->count_id : '';
    $quotation_id = $res3->quotation_id;
    $pos = $res3->pos;

    $discount_to_all_input = $res3->discount_to_all_input;
    $discount_to_all_type_raw = $res3->discount_to_all_type;
    $tot_discount_to_all_amt = (float)$res3->tot_discount_to_all_amt;
    $coupon_amt = (float)$res3->coupon_amt;
    $other_charges_input = (float)$res3->other_charges_input;
    $other_charges_amt = (float)$res3->other_charges_amt;
    $paid_amount = (float)$res3->paid_amount;
    $grand_total_db = (float)$res3->grand_total;

    // Due days (same as add page)
    $due_days = null;
    if (!empty($res3->due_date) && !empty($res3->sales_date)) {
      $inv_ts = strtotime($res3->sales_date);
      $due_ts = strtotime($res3->due_date);
      if ($inv_ts !== false && $due_ts !== false) {
        $due_days = (int) floor(($due_ts - $inv_ts) / 86400);
        if ($due_days < 0) { $due_days = 0; }
      }
    }

    // Previous due for customer (same label as add)
    $customer_previous_due = 0;
    $q_due = $this->db->query("SELECT COALESCE(SUM(grand_total-paid_amount),0) AS due_amt FROM db_sales WHERE customer_id=".(int)$customer_id." AND id<>".(int)$sales_id." AND store_id=".(int)$res3->store_id);
    if ($q_due->num_rows() > 0) {
      $customer_previous_due = (float)$q_due->row()->due_amt + (float)$res3->opening_balance;
    } else {
      $customer_previous_due = (float)$res3->opening_balance;
    }

    $default_vat_pct = '';
    if ($show_tax) {
      $tax_defaults = get_store_item_tax_defaults($res3->store_id);
      if (!empty($tax_defaults->default_tax_id)) {
        $default_tax_row = $this->db->select('tax')->where('id', (int)$tax_defaults->default_tax_id)->get('db_tax')->row();
        if (!empty($default_tax_row) && isset($default_tax_row->tax)) {
          $default_vat_pct = rtrim(rtrim(number_format((float)$default_tax_row->tax, 2, '.', ''), '0'), '.');
        }
      }
    }

    // Load items (add-page style columns)
    $q_items = $this->db->query("SELECT a.description, c.item_name, c.item_image, c.item_code, c.custom_barcode, c.stock, c.service_bit, c.price AS cost_price,
                                  a.item_id, a.sales_qty, a.tax_type, a.price_per_unit, b.tax, b.tax_name, a.tax_amt,
                                  a.discount_input, a.discount_amt, a.unit_total_cost, a.total_cost
                                  FROM db_salesitems AS a
                                  LEFT JOIN db_tax AS b ON b.id=a.tax_id
                                  LEFT JOIN db_items AS c ON c.id=a.item_id
                                  WHERE a.sales_id='$sales_id'");

    $tot_qty = 0;
    $pretax_subtotal = 0;
    $amount_sum = 0;
    $line_discount_sum = 0;
    $vat_sum = 0;
    $summary_vat_pct = null;
    $summary_vat_mixed = false;
    $item_rows_html = '';
    $i = 0;

    foreach ($q_items->result() as $res2) {
      $i++;
      $barcode_val = (!empty($res2->custom_barcode)) ? $res2->custom_barcode : $res2->item_code;
      $sales_qty = (float)$res2->sales_qty;
      $sales_price = (float)$res2->price_per_unit;
      $line_tax = $show_tax ? (float)$res2->tax_amt : 0;
      $line_discount = (float)$res2->discount_amt;
      // Inclusive prices already contain VAT; only exclusive prices add VAT.
      $line_amount = ($sales_qty * $sales_price) - $line_discount;
      if ($show_tax && strcasecmp(trim((string)$res2->tax_type), 'Inclusive') !== 0) {
        $line_amount += $line_tax;
      }
      if (!$show_tax) {
        $line_amount = ($sales_qty * $sales_price) - $line_discount;
      }

      $stock_show = (float)$res2->stock;
      if (function_exists('total_available_qty_items_of_warehouse') && !empty($warehouse_id) && empty($res2->service_bit)) {
        $stock_show = total_available_qty_items_of_warehouse($warehouse_id, null, $res2->item_id);
      }

      $tax_pct = (float)$res2->tax;
      $tax_label = !empty($res2->tax_name) ? trim(str_ireplace('vat', '', $res2->tax_name)) : '';
      if ($tax_label === '') {
        $tax_label = $tax_pct.'%';
      }

      if ($show_tax) {
        if ($summary_vat_pct === null) {
          $summary_vat_pct = $tax_pct;
        } elseif ((float)$summary_vat_pct !== $tax_pct) {
          $summary_vat_mixed = true;
        }
      }

      $tot_qty += $sales_qty;
      $pretax_subtotal += ($sales_qty * $sales_price) - $line_discount;
      $vat_sum += $line_tax;
      $amount_sum += $line_amount;
      $line_discount_sum += $line_discount;

      $item_rows_html .= "<tr>";
      $preview_url = !empty($res2->item_image) ? base_url($res2->item_image) : base_url('theme/images/no_image.png');
      $item_rows_html .= "<td class='text-center' style='vertical-align:middle;'><img class='sales-item-image' src='".html_escape($preview_url)."' data-full-image='".html_escape($preview_url)."' data-fallback-image='".base_url('theme/images/no_image.png')."' alt='".html_escape($res2->item_name)."' tabindex='0' loading='lazy' style='width:40px;height:40px;object-fit:contain;cursor:zoom-in;' onerror=\"this.onerror=null;this.src='".base_url('theme/images/no_image.png')."';\"></td>";
      $item_rows_html .= "<td><span class='form-control text-center' style='height:auto;background:#eee;'>".htmlspecialchars($barcode_val)."</span></td>";
      $item_rows_html .= "<td><label class='form-control' style='height:auto;margin:0;'>".htmlspecialchars($res2->item_name);
      if (!empty($res2->description)) {
        $item_rows_html .= "<br><i>[".nl2br(htmlspecialchars($res2->description))."]</i>";
      }
      $item_rows_html .= "</label></td>";
      $item_rows_html .= "<td class='text-center'><span class='form-control text-center' style='height:auto;background:#eee;'>".format_qty($stock_show)."</span></td>";
      $item_rows_html .= "<td><input type='text' class='form-control no-padding text-center' value='".format_qty($sales_qty)."' readonly></td>";
      $item_rows_html .= "<td><input type='text' class='form-control text-right no-padding text-center' value='".store_number_format($sales_price,0)."' readonly></td>";
      $item_rows_html .= "<td><input type='text' class='form-control text-right no-padding text-center' value='".store_number_format($line_discount,0)."' readonly></td>";
      $item_rows_html .= "<td class='branch-tax-col'><input type='text' class='form-control text-right no-padding text-center' value='".store_number_format($line_tax,0)."' readonly></td>";
      $item_rows_html .= "<td class='text-center branch-tax-col'><label class='form-control text-center' style='width:100%;padding-left:0;padding-right:0;margin:0;'>".htmlspecialchars($tax_label)."</label></td>";
      $item_rows_html .= "<td><input type='text' class='form-control text-right no-padding text-center' style='border-color:#f39c12;' value='".store_number_format($line_amount,0)."' readonly></td>";
      $item_rows_html .= "<td class='text-center' style='white-space:nowrap;'><input type='hidden' id='tr_item_id_".$i."' value='".(int)$res2->item_id."'><button type='button' class='btn btn-info btn-flat sales-price-history-trigger' title='Price History' aria-label='Price History' onclick='show_item_price_history_popup(".$i.")'><i class='fa fa-history' aria-hidden='true'></i></button></td>";
      $item_rows_html .= "</tr>";
    }

    // Summary like add final_total()
    // A fixed invoice discount is distributed into the item discount column on add/edit.
    $invoice_discount = ($discount_to_all_type_raw === 'in_percentage')
      ? (($amount_sum - $vat_sum + $other_charges_input - $coupon_amt) * (float)$discount_to_all_input / 100)
      : (($line_discount_sum > 0) ? 0 : (float)$discount_to_all_input);
    $summary_discount = $line_discount_sum + $invoice_discount;
    // Row amounts are already net of row discounts: subtract only the invoice-level part here
    $taxable = $amount_sum - $invoice_discount - $vat_sum + $other_charges_amt - $coupon_amt;
    if ($taxable < 0) { $taxable = 0; }
    $display_subtotal = $amount_sum + $line_discount_sum; // subtotal before discount (same as add/edit)
    $display_vat = $show_tax ? $vat_sum : 0;
    // Same as add page final_total(): Net = taxable + VAT (when branch tax on)
    $display_net = $show_tax ? ($taxable + $display_vat) : $taxable;

    $summary_vat_label = 'VAT Amount';
    if ($show_tax) {
      if (!$summary_vat_mixed && $summary_vat_pct !== null && $summary_vat_pct !== '') {
        $summary_vat_label = 'VAT Amount ('.(float)$summary_vat_pct.'%)';
      } elseif ($default_vat_pct !== '') {
        $summary_vat_label = 'VAT Amount ('.$default_vat_pct.'%)';
      }
    }

    $discount_type_label = ($discount_to_all_type_raw === 'in_percentage') ? 'Per%' : 'Fixed';
    $pdf_popup_enabled = is_pdf_part_number_popup_enabled();
    ?>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <?php include"comman/code_flashdata.php"; ?>
        </div>
      </div>

      <div class="row view-readonly">
        <div class="col-md-12">
          <div class="box box-primary sales-page-panel">
            <div class="box-body form-horizontal">

              <?php /* TASK-31: header — 3 fields per row (same as add/edit) */ ?>
              <div class="form-group">
                <div class="col-sm-4">
                  <label>Branch Name<label class="text-danger">*</label></label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($warehouse_name); ?>" readonly>
                </div>
                <div class="col-sm-4">
                  <label><?= $this->lang->line('invoice_no'); ?><label class="text-danger">*</label></label>
                  <div class="row" style="margin:0;">
                    <div class="col-xs-7" style="padding-left:0;padding-right:2px;">
                      <input type="text" class="form-control no-padding" style="font-size:18px;" value="<?= htmlspecialchars($init_code); ?>" readonly>
                    </div>
                    <div class="col-xs-5" style="padding-left:2px;padding-right:0;">
                      <input type="text" class="form-control text-center" style="font-size:18px;" value="<?= htmlspecialchars($count_id); ?>" readonly>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <label><?= $this->lang->line('invoice_date'); ?> <label class="text-danger">*</label></label>
                  <div class="input-group date">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control" value="<?= $sales_date_ui; ?>" readonly>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4">
                  <label><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label></label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($customer_name); ?>" readonly>
                  <span class="customer-previous-due-wrap"><lable><?= $this->lang->line('previous_due'); ?> :<label class="customer_previous_due text-red" style="font-size: 18px;"><?= store_number_format($customer_previous_due); ?></label></lable></span>

                </div>
                <div class="col-sm-4">
                  <label>Due Days</label>
                  <input type="text" class="form-control" value="<?= ($invoice_type === 'Credit' && $due_days !== null) ? (int)$due_days : ''; ?>" readonly>
                </div>
                <div class="col-sm-4">
                  <label><?= $this->lang->line('due_date'); ?></label>
                  <div class="input-group date">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control" value="<?= $invoice_type === 'Credit' ? $due_date_ui : ''; ?>" readonly>
                  </div>
                </div>

              </div>

              <div class="form-group">
                <div class="col-sm-4">
                  <label>Invoice Type</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($invoice_type); ?>" readonly>
                </div>
                <div class="col-sm-4">
                  <label><?= $this->lang->line('payment_type'); ?></label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($payment_type); ?>" readonly>
                </div>
                <div class="col-sm-4">
                  <label>Card Type</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($card_type); ?>" readonly>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4">
                  <label>Mobile No</label>
                  <input type="text" class="form-control" id="customer_mobile" value="<?= htmlspecialchars((string)$customer_mobile, ENT_QUOTES); ?>" readonly>
                </div>
                <div class="col-sm-4">
                  <label for="whatsapp_no">WhatsApp No</label>
                  <div class="whatsapp-number-control">                <input type="text" class="form-control" id="whatsapp_no" value="<?= htmlspecialchars((string)$whatsapp_no, ENT_QUOTES); ?>" readonly>
                    <span class="input-group-btn"><button type="button" class="btn btn-default" id="copy-whatsapp-number" title="Copy Mobile No to WhatsApp No" aria-label="Copy Mobile No to WhatsApp No"><i class="fa fa-copy" aria-hidden="true"></i></button></span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <label>Email Address</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars((string)$customer_email); ?>" readonly>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-4">
                  <div id="customer_trn_no_label" style="<?= $show_customer_trn ? '' : 'display:none;' ?>">
                    <label >Customer TRN</label>
                  </div>
                  <div id="customer_trn_no_wrap" style="<?= $show_customer_trn ? '' : 'display:none;' ?>">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($invoice_trn, ENT_QUOTES); ?>" readonly>
                  </div>
</div>
                <div class="col-sm-4">
                  <label>Address</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars((string)($res3->invoice_address !== null ? $res3->invoice_address : $res3->address), ENT_QUOTES); ?>" readonly>
                </div>
                <div class="col-sm-4">
                  <label>Salesperson</label>
                  <?php $salesperson = !empty($res3->salesman_id) ? $this->db->select('salesman_name')->where('id', (int)$res3->salesman_id)->get('db_salesman')->row() : null; ?>
                  <select class="form-control" disabled><option><?= htmlspecialchars($salesperson ? $salesperson->salesman_name : '-Select-', ENT_QUOTES); ?></option></select>
                </div>
</div><div class="form-group">
<div class="col-sm-4">
                  <label>Remark</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($reference_no); ?>" readonly>
                </div>

              </div>

            </div>
            <!-- /.box-body header -->

            <div class="row">
              <div class="col-md-12">
                <div class="col-md-12">
                  <div class="box">
                    <div class="box-info">
                      <div class="box-body">
                        <div class="table-responsive" style="width: 100%">
                          <table class="table table-hover table-bordered" style="width:100%" id="sales_table">
                            <thead class="custom_thead">
                              <tr class="bg-primary">
                                <th style="width:5%">Image</th>
                                <th style="width:10%"><?= $barcode_label; ?></th>
                                <th style="width:15%"><?= $item_name_label; ?></th>
                                <th style="width:8%"><?= (!empty($this->lang->line('stock'))) ? $this->lang->line('stock') : 'Stock'; ?></th>
                                <th style="width:10%;min-width: 180px;"><?= $this->lang->line('quantity'); ?></th>
                                <th style="width:10%">Sale Price</th>
                                <th style="width:10%"><?= $this->lang->line('discount'); ?></th>
                                <th class="branch-tax-col" style="width:10%"><?= (!empty($this->lang->line('vat_amount'))) ? $this->lang->line('vat_amount') : 'VAT Amount'; ?></th>
                                <th class="branch-tax-col" style="width:5%"><?= (!empty($this->lang->line('vat_percentage'))) ? $this->lang->line('vat_percentage') : 'VAT %'; ?></th>
                                <th style="width:7.5%">Amount</th>
                                <th style="width:7.5%"><?= $this->lang->line('action'); ?></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?= $item_rows_html; ?>
                              <?php if ($i === 0) { ?>
                              <tr><td colspan="11" class="text-center text-bold">No Items Found!!</td></tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <?php
              // TASK-21 footer (read-only) — match add/edit layout
              $cust_advance = 0;
              $q_adv = $this->db->select('tot_advance')->where('id', (int)$customer_id)->get('db_customers')->row();
              if (!empty($q_adv) && isset($q_adv->tot_advance)) {
                $cust_advance = (float)$q_adv->tot_advance;
              }
              $view_advance_row = $this->db->select('COALESCE(SUM(advance_adjusted),0) AS amount')->where('sales_id', (int)$sales_id)->get('db_salespayments')->row();
              $view_advance = (float)$view_advance_row->amount;
              $view_invoice_dues = (float)$grand_total_db - (float)$paid_amount;
              $customer_previous_due -= (float)get_paid_cob($customer_id);
              $view_paid_amount = (float)$paid_amount;
              $view_cash_paid = max(0, $view_paid_amount - $view_advance);
              $view_balance_due = max(0, (float)$display_net - $view_cash_paid);
              $view_refund = 0;
              if ($view_cash_paid > (float)$display_net) {
                $view_refund = $view_cash_paid - (float)$display_net;
              }
              $view_payment_note = '';
              $q_pay_note = $this->db->query("select payment_note from db_salespayments where sales_id=".(int)$sales_id." order by id asc limit 1");
              if ($q_pay_note->num_rows() > 0) {
                $view_payment_note = (string)$q_pay_note->row()->payment_note;
              }
              ?>

              <div class="col-md-6 sales-footer-left">
                <div class="row sales-footer-summary" style="margin-bottom:8px;">
                  <div class="col-sm-4">
                    <label class="control-label" style="margin-right:6px;"><?= $this->lang->line('quantity'); ?></label>
                    <label class="control-label total_quantity text-success" style="font-size: 15pt;"><?= format_qty($tot_qty); ?></label>
                  </div>
                  <div class="col-sm-8">
                    <label class="control-label" style="margin-right:6px;"><?= $this->lang->line('discount_on_all'); ?></label>
                    <div class="input-group" style="max-width:280px;display:inline-table;vertical-align:middle;">
                      <input type="text" class="form-control text-right" value="<?= store_number_format($discount_to_all_input, 0); ?>" readonly>
                      <span class="input-group-addon" style="min-width:90px;"><?= $discount_type_label; ?></span>
                    </div>
                  </div>
                </div>

                <div class="row sales-footer-advance" style="margin-bottom:8px;">
                  <div class="col-sm-12">
                    <label style="margin-right:18px;">
                      <?= $this->lang->line('advance'); ?> : <label style="font-weight:bold;"><?= store_number_format($cust_advance); ?></label>
                    </label>
                    <label class="checkbox-inline" style="padding-left:0;margin-left:0;">
                      <input type="checkbox" disabled <?= $view_advance > 0 ? 'checked' : ''; ?>> <?= $this->lang->line('adjust_advance_payment'); ?>
                    </label>
                    <div style="margin-top:8px;">
                      <label>Advance Amount</label>
                      <input type="text" class="form-control text-right" value="<?= store_number_format($view_advance); ?>" readonly>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-4">
                    <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                      <div class="box-body" style="padding:10px 0;">
                        <label>Paid Amount</label>
                        <input type="text" class="form-control text-right" value="<?= store_number_format(max(0, $view_paid_amount - $view_advance)); ?>" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                      <div class="box-body" style="padding:10px 0;">
                        <label>Balance Dues</label>
                        <input type="text" class="form-control text-right" value="<?= store_number_format($view_balance_due); ?>" readonly style="background-color:#eee;color:#555;">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                      <div class="box-body" style="padding:10px 0;">
                        <label>Refund Amount</label>
                        <input type="text" class="form-control text-right" value="<?= store_number_format($view_refund); ?>" readonly style="background-color:#eee;color:#555;">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="box box-solid" style="margin-bottom:8px;background:transparent;box-shadow:none;border:0;">
                  <div class="box-body" style="padding:10px 0;">
                    <label><?= $this->lang->line('note'); ?></label>
                    <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($view_payment_note); ?></textarea>
                  </div>
                </div>
              </div>

              <div class="col-md-3 sales-footer-dues"><table class="table" style="margin-bottom:0;"><tr><th class="text-right" style="border:0;">Advance Amount</th><th class="text-right" style="border:0;"><?= store_number_format($view_advance); ?></th></tr>
                  <tr><th class="text-right" style="border:0;">Invoice Dues</th><th class="text-right" style="border:0;"><?= store_number_format($view_invoice_dues); ?></th></tr>
                  <tr><th class="text-right" style="border:0;">Previous Dues</th><th class="text-right" style="border:0;"><?= store_number_format($customer_previous_due); ?></th></tr>
                  <tr><th class="text-right" style="border:0;">Current Dues</th><th class="text-right" style="border:0;"><?= store_number_format($view_invoice_dues + $customer_previous_due); ?></th></tr></table></div>
<div class="col-md-3 sales-footer-right">
                <table class="table" style="margin-bottom:0;">
                  <tr>
                    <th class="text-right" style="font-size: 17px;border:0;"><?= $this->lang->line('subtotal'); ?></th>
                    <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                      <h4 style="margin:4px 0;"><b><?= store_number_format($display_subtotal); ?></b></h4>
                    </th>
                  </tr>
                  <tr>
                    <th class="text-right" style="font-size: 17px;border:0;">Discount Amount</th>
                    <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                      <h4 style="margin:4px 0;"><b><?= store_number_format($summary_discount); ?></b></h4>
                    </th>
                  </tr>
                  <?php if ($show_tax) { ?>
                  <tr class="summary-trn-row">
                    <th class="text-right" style="font-size: 17px;border:0;">Taxable Amount</th>
                    <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                      <h4 style="margin:4px 0;"><b><?= store_number_format($taxable); ?></b></h4>
                    </th>
                  </tr>
                  <tr class="summary-trn-row">
                    <th class="text-right" style="font-size: 17px;border:0;"><?= $summary_vat_label; ?></th>
                    <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                      <h4 style="margin:4px 0;"><b><?= store_number_format($display_vat); ?></b></h4>
                    </th>
                  </tr>
                  <?php } ?>
                  <tr>
                    <th class="text-right" style="font-size: 17px;border:0;">Net Amount</th>
                    <th class="text-right" style="padding-left:10%;font-size: 17px;border:0;">
                      <h4 style="margin:4px 0;"><b><?= store_total_format($display_net); ?></b></h4>
                    </th>
                  </tr>
                                  </table>
              </div>
              <div class="clearfix"></div>

              <div class="col-xs-12" style="margin-top:10px;margin-bottom:15px;">
                <div class="col-xs-6">
                  <?php if($CI->permissions('sales_edit')) { ?>
                  <?php $str2= ($pos==1)? 'pos/edit/':'sales/update/'; ?>
                  <a href="<?php echo $base_url; ?><?=$str2;?><?php echo  $sales_id ?>" class="btn btn-success">
                    <i class="fa fa-edit"></i> Edit
                  </a>
                  <?php } ?>

                  <a href="<?php echo $base_url; ?>pos/print_invoice_pos/<?php echo  $sales_id ?>" target="_blank" class="btn btn-info">
                    <i class="fa fa-file-text"></i> POS Invoice
                  </a>

                  <?php if($CI->permissions('sales_return_add')) { ?>
                  <a href="<?php echo $base_url; ?>sales_return/add/<?php echo  $sales_id ?>" class="btn btn-danger">
                    <i class="fa fa-undo"></i> Sales Return
                  </a>
                  <?php } ?>
                </div>
                <div class="col-xs-6 text-right">
                  <?php if($pdf_popup_enabled){ ?>
                  <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#pdf-part-number-modal">
                    <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php } else { ?>
                  <a href="<?php echo $base_url; ?>pdf/sales/<?php echo $sales_id; ?>" target="_blank" class="btn btn-primary">
                    <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <?php } ?>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
  </div>
  <!-- /.content-wrapper -->
  <?php include"footer.php"; ?>

  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php if($pdf_popup_enabled){ ?>
<div class="modal fade" id="pdf-part-number-modal" tabindex="-1" role="dialog" aria-labelledby="pdfPartNumberModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="pdfPartNumberModalLabel">PDF Options</h4>
      </div>
      <div class="modal-body">
        <div class="form-group" style="margin-bottom:0;">
          <div class="radio">
            <label>
              <input type="radio" name="pdf_part_number_option" id="pdf_without_part_number" value="0" checked>
              Without <?= get_item_barcode_label(); ?>
            </label>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="pdf_part_number_option" id="pdf_with_part_number" value="1">
              With <?= get_item_barcode_label(); ?>
            </label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="generate-pdf-btn">
          <i class="fa fa-file-pdf-o"></i> Generate PDF
        </button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include"comman/code_js_sound.php"; ?>
<?php include"comman/code_js.php"; ?>

<?php if(!$show_tax){ ?>
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
<?php } ?>

<script>
$(".sales-list-active-li").addClass("active");
<?php if($pdf_popup_enabled){ ?>
$('#pdf-part-number-modal').on('show.bs.modal', function () {
  $('#pdf_without_part_number').iCheck('check');
});

$('#generate-pdf-btn').on('click', function () {
  var withPartNumber = $('input[name="pdf_part_number_option"]:checked').val();
  var pdfUrl = '<?php echo $base_url; ?>pdf/sales/<?php echo $sales_id; ?>';
  if (withPartNumber === '1') {
    pdfUrl += '?with_part_number=1';
  }
  $('#pdf-part-number-modal').modal('hide');
  window.open(pdfUrl, '_blank');
});
<?php } ?>
<?php if (!empty($_GET['auto_pdf'])) { ?>
$(function () {
  <?php if ($pdf_popup_enabled) { ?>
  $('#pdf-part-number-modal').modal('show');
  <?php } else { ?>
  window.open('<?php echo $base_url; ?>pdf/sales/<?php echo $sales_id; ?>', '_blank');
  <?php } ?>
});
<?php } ?>
</script>
<?php include "modals/modal_sales_price_history.php"; ?>
<input type="hidden" id="customer_id" value="<?= (int)$customer_id; ?>">
<input type="hidden" id="sales_id" value="<?= (int)$sales_id; ?>">
<script src="<?= $theme_link; ?>js/sales-price-history.js?v=<?= filemtime(FCPATH.'theme/js/sales-price-history.js'); ?>"></script>
<script src="<?= $theme_link; ?>js/sales-whatsapp-copy.js?v=<?= filemtime(FCPATH.'theme/js/sales-whatsapp-copy.js'); ?>"></script>
<script src="<?= $theme_link; ?>js/sales-image-preview.js?v=<?= filemtime(FCPATH.'theme/js/sales-image-preview.js'); ?>"></script>
</body>
</html>
