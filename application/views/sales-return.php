<!DOCTYPE html>
<html>

<head>
<!-- FORM CSS CODE -->
<?php include"comman/code_css.php"; ?>
<!-- </copy> --> 
<style type="text/css">
   table.table-bordered > thead > tr > th {
   /* border:1px solid black;*/
   text-align: center;
   }
   .table > tbody > tr > td, 
   .table > tbody > tr > th, 
   .table > tfoot > tr > td, 
   .table > tfoot > tr > th, 
   .table > thead > tr > td, 
   .table > thead > tr > th 
   {
   padding-left: 2px;
   padding-right: 2px;  

   }
</style> 
</head>


<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
 
 
 <?php include"sidebar.php"; ?>
 
 <?php
    $sales_code=$customer_name='';
    ensure_sales_return_customer_trn_no();
    ensure_sales_customer_trn_no();
    $customer_trn_no = '';
    if($oper=='return_against_sales'){

          $return_id='';
          $q2 = $this->db->query("select * from db_sales where id=$sales_id");
          $customer_id=$q2->row()->customer_id;
          $salesman_id=$q2->row()->salesman_id;
          $return_date=show_date(date("d-m-Y"));
          $sales_code=$q2->row()->sales_code;
          $return_status=$q2->row()->sales_status;
          $warehouse_id=$q2->row()->warehouse_id;
          $reference_no='';
          $due_date='';
          $discount_input=store_number_format($q2->row()->discount_to_all_input,0);
          $discount_type=$q2->row()->discount_to_all_type;
          $other_charges_input=store_number_format($q2->row()->other_charges_input,0);
          $other_charges_tax_id=$q2->row()->other_charges_tax_id;
          $store_id=$q2->row()->store_id;
          $return_note='';
          $customer_trn_no = !empty($q2->row()->customer_trn_no) ? $q2->row()->customer_trn_no : get_customer_trn_no($customer_id);

          $coupon_id=$q2->row()->coupon_id;
          $coupon_code = (!empty($coupon_id)) ? get_customer_coupon_details($coupon_id)->code : '';

          $items_count = $this->db->query("select count(*) as items_count from db_salesitems where sales_id=$sales_id")->row()->items_count;
    }
    if($oper=='edit_existing_return'){
          $q2 = $this->db->query("select * from db_salesreturn where id=$return_id");
          $sales_id=$q2->row()->sales_id;
          $customer_id=$q2->row()->customer_id;
          $salesman_id=$q2->row()->salesman_id;
          $return_date=show_date(date("d-m-Y"));
          $return_status=$q2->row()->return_status;
          $return_code=$q2->row()->return_code;
          $warehouse_id=$q2->row()->warehouse_id;
          $reference_no=$q2->row()->reference_no;
          $due_date=(!empty($q2->row()->due_date)) ? show_date($q2->row()->due_date) : '';
          $discount_input=store_number_format($q2->row()->discount_to_all_input,0);
          $discount_type=$q2->row()->discount_to_all_type;
          $other_charges_input=store_number_format($q2->row()->other_charges_input,0);
          $other_charges_tax_id=$q2->row()->other_charges_tax_id;
          $return_note=$q2->row()->return_note;
          $store_id=$q2->row()->store_id;
          $customer_trn_no = !empty($q2->row()->customer_trn_no) ? $q2->row()->customer_trn_no : get_customer_trn_no($customer_id);

          $items_count = $this->db->query("select count(*) as items_count from db_salesitemsreturn where return_id=$return_id")->row()->items_count;
          $sales_code = (!empty($sales_id)) ? $this->db->query("select * from db_sales where id=$sales_id")->row()->sales_code : '';

          $coupon_id=$q2->row()->coupon_id;
          $coupon_code = (!empty($coupon_id)) ? get_customer_coupon_details($coupon_id)->code : '';

    }
    if($oper=='create_new_return'){
          $customer_id  = $return_date = $return_status = $warehouse_id = $salesman_id = 
          $reference_no = $due_date =
          $other_charges_input          = $other_charges_tax_id =
          $discount_input = $discount_type  = $return_note= $store_id='';
          $return_date=show_date(date("d-m-Y"));
          $coupon_code='';
          $customer_trn_no = '';
    }

    if(!empty($customer_id)){
      $customer_name=$this->db->select('customer_name')->where('id',$customer_id)->get('db_customers')->row()->customer_name;
      if($customer_trn_no==='' || $customer_trn_no===null){
        $customer_trn_no = get_customer_trn_no($customer_id);
      }
    }

    $branch_tax_store_id = (!empty($store_id)) ? $store_id : get_current_store_id();
    $branch_tax_enabled = is_branch_tax_enabled($branch_tax_store_id);
    ?>
    <?php if(!$branch_tax_enabled): ?>
    <style>#sales_table .branch-tax-col{display:none !important;}</style>
    <?php endif; ?>

 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- **********************MODALS***************** -->
    <?php include"modals/modal_customer.php"; ?>
    <?php include"modals/modal_sales_item.php"; ?>
    <?php include"modals/modal_item.php"; ?>
    <?php include"modals/modal_item_or_service.php"; ?>
   <?php /*include"modals/modal_service.php";*/ ?>
    <!-- **********************MODALS END***************** -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
         <h1>
            <?=$page_title;?>
            <small><?=$subtitle;?></small>
         </h1>
         <ol class="breadcrumb">
            <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo $base_url; ?>sales_return"><?= $this->lang->line('sales_return_list'); ?></a></li>
            <li><a href="<?php echo $base_url; ?>sales_return/create"><?= $this->lang->line('new_sales'); ?></a></li>
            <li class="active"><?=$page_title;?></li>
         </ol>
      </section>

    <!-- Main content -->
     <section class="content">
               <div class="row">
                <!-- ********** ALERT MESSAGE START******* -->
               <?php include"comman/code_flashdata.php"; ?>
               <!-- ********** ALERT MESSAGE END******* -->
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary " >
                        <!-- style="background: #68deac;" -->
                        
                        <!-- form start -->
                         <!-- OK START -->
                        <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'sales-form', 'enctype'=>'multipart/form-data', 'method'=>'POST'));?>
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <input type="hidden" value='1' id="hidden_rowcount" name="hidden_rowcount">
                           <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">

                          
                           <div class="box-body">
                              <!-- Store Code -->
                              <?php 
                              /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>$store_id,'div_length'=>'col-sm-3')); }else{*/
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              /*}*/
                              ?>
                              <!-- Store Code end -->
                              <!-- Warehouse Code -->
                              <?php 
                               if(warehouse_module() && warehouse_count()>1) {$this->load->view('warehouse/warehouse_code',array('show_warehouse_select_box'=>true,'warehouse_id'=>$warehouse_id,'div_length'=>'col-sm-3','show_select_option'=>false,'label'=>'Branch Name')); }else{
                                echo "<input type='hidden' name='warehouse_id' id='warehouse_id' value='".get_store_warehouse_id()."'>";
                               }
                              ?>
                              <!-- Warehouse Code end -->
                              
                              <div class="form-group">
                              <?php if(!empty($sales_code)) { ?>
                                 <label for="" class="col-sm-2 control-label"><?= $this->lang->line('invoice_no'); ?><label class="text-danger">*</label> </label>
                                  <label class="col-sm-3 control-label" style="text-align: left;"><?= $sales_code;?></label>
                                <?php } ?>
                                <?php if(!empty($customer_name)) { ?>
                                 <label for="" class="col-sm-2 control-label"><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label> </label>
                                  <label class="col-sm-3 control-label" style="text-align: left;"><?= $customer_name;?></label>
                                  <input type="hidden" name="customer_id" id='customer_id' value="<?=$customer_id;?>">
                                <?php } ?>
                              </div>

                              <div class="form-group">
                                <?php if(!empty($return_code)) { ?>
                                 <label for="" class="col-sm-2 control-label"><?= $this->lang->line('invoice'); ?><label class="text-danger">*</label> </label>
                                  <label class="col-sm-3 control-label" style="text-align: left;">#<?= $return_code;?></label>
                                <?php } ?>

                                <?php if(empty($customer_id)) {?>
                                 <label for="customer_id" class="col-sm-2 control-label"><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group">
                                       <select class="form-control select2" id="customer_id" name="customer_id"  style="width: 100%;" onkeyup="shift_cursor(event,'mobile')">
                                          <?= get_customers_select_list($customer_id,get_current_store_id()); ?>
                                       </select>
                                      <span class="input-group-addon pointer" data-toggle="modal" data-target="#customer-modal" title="New customer?"><i class="fa fa-user-plus text-primary fa-lg"></i></span>
                                    </div>
                                    <span id="customer_id_msg" style="display:none" class="text-danger"></span>
                                 </div>
                               <?php } ?>

                              

                                 <label for="return_date" class="col-sm-2 control-label"><?= $this->lang->line('invoice_date'); ?> <label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right datepicker"  id="return_date" name="return_date" readonly onkeyup="shift_cursor(event,'return_status')" value="<?= $return_date;?>" style="background-color:#fff;cursor:pointer;">
                                    </div>
                                    <span id="return_date_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                              
                              <div class="form-group">
                                 <label for="return_status" class="col-sm-2 control-label"><?= $this->lang->line('status'); ?> <label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                       <select class="form-control select2" id="return_status" name="return_status"  style="width: 100%;" onkeyup="shift_cursor(event,'mobile')">
                                          <!-- <option value="">-Select-</option> -->
                                          <?php 
                                               $return_select = ($return_status=='Return') ? 'selected' : ''; 
                                               $cancel_select = ($return_status=='Cancel') ? 'selected' : ''; 
                                          ?>
                                            <option <?= $return_select; ?> value="Return">Return</option>
                                            <option <?= $cancel_select; ?> value="Cancel">Cancel</option>
                                       </select>
                                    <span id="return_status_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                  <label for="reference_no" class="col-sm-2 control-label">Remark</label>
                                 <div class="col-sm-3">
                                    <input type="text" value="<?php echo  $reference_no; ?>" class="form-control " id="reference_no" name="reference_no" placeholder="" >
                  <span id="reference_no_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 
                              </div>
                              <div class="form-group">
                                 <label for="due_date" class="col-sm-2 control-label"><?= $this->lang->line('due_date'); ?></label>
                                 <div class="col-sm-3">
                                    <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right due-datepicker" id="due_date" name="due_date" value="<?= isset($due_date) ? $due_date : ''; ?>" readonly style="background-color:#fff;cursor:pointer;">
                                    </div>
                                    <span id="due_date_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <?php if($branch_tax_enabled): ?>
                              <?php $show_customer_trn = (trim((string)$customer_trn_no) !== ''); ?>
                              <div class="form-group" id="customer_trn_no_group" <?= $show_customer_trn ? '' : 'style="display:none;"'; ?>>
                                 <label for="customer_trn_no" class="col-sm-2 control-label">Customer TRN No.</label>
                                 <div class="col-sm-3">
                                    <input type="text" value="<?= htmlspecialchars($customer_trn_no, ENT_QUOTES); ?>" class="form-control" id="customer_trn_no_display" readonly disabled style="background-color:#eee;cursor:not-allowed;">
                                    <input type="hidden" name="customer_trn_no" value="<?= htmlspecialchars($customer_trn_no, ENT_QUOTES); ?>" id="customer_trn_no">
                                    <span id="customer_trn_no_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <?php else: ?>
                              <input type="hidden" name="customer_trn_no" id="customer_trn_no" value="">
                              <?php endif; ?>
                              
                              <input type="hidden" id="salesman_id" name="salesman_id" value="<?php echo $salesman_id; ?>">
                              
                           </div>
                           <!-- /.box-body -->
                           
                           <div class="row">
                              <div class="col-md-12">
                                <div class="col-md-12">
                                  <div class="box">
                                    <div class="box-info">
                                      <div class="box-header">
                                        <div class="col-md-8 col-md-offset-2 d-flex justify-content" >
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
                                                <tr class="bg-primary" >
                                                   <th rowspan='2' style="width:10%"><?= get_item_barcode_label(); ?></th>
                                                   <th rowspan='2' style="width:15%"><?= (get_item_barcode_label_key() === 'barcode_no') ? $this->lang->line('item_name') : 'Part Name'; ?></th>
                                                   <th rowspan='2' style="width:8%"><?= (!empty($this->lang->line('stock'))) ? $this->lang->line('stock') : 'Stock'; ?></th>
                                                   <th rowspan='2' style="width:18.5%;min-width: 180px;"><?= $this->lang->line('quantity'); ?></th>
                                                   <th rowspan='2' style="width:10%">Sale Price</th> 
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('discount'); ?></th>
                                                   <th rowspan='2' class="branch-tax-col" style="width:5%"><?= (!empty($this->lang->line('vat_percentage'))) ? $this->lang->line('vat_percentage') : 'Vat %'; ?></th>
                                                   <th rowspan='2' class="branch-tax-col" style="width:10%"><?= (!empty($this->lang->line('vat_amount'))) ? $this->lang->line('vat_amount') : 'Vat Amount'; ?></th>
                                                   <th rowspan='2' style="width:7.5%">Amount</th>
                                                   <th rowspan='2' style="width:3%"><?= $this->lang->line('action'); ?></th>
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
                              
                              
                              <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="" class="col-sm-4 control-label"><?= $this->lang->line('quantity'); ?></label>    
                                          <div class="col-sm-4">
                                             <label class="control-label total_quantity text-success" style="font-size: 15pt;">0</label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row" style="display:none;">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="other_charges_input" class="col-sm-4 control-label"><?= $this->lang->line('other_charges'); ?></label>    
                                          <div class="col-sm-4">
                                             <input type="text" class="form-control text-right only_currency" id="other_charges_input" name="other_charges_input" onkeyup="final_total();" value="<?php echo  $other_charges_input; ?>">
                                          </div>
                                          <div class="col-sm-4">
                                             <select class="form-control " id="other_charges_tax_id" name="other_charges_tax_id" onchange="final_total();" style="width: 100%;">
                                                 <?= get_tax_select_list($other_charges_tax_id,get_current_store_id());?>
                                             </select>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row" style="display:none;">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="other_charges_input" class="col-sm-4 control-label"><?= $this->lang->line('discountCouponCode'); ?></label>    
                                          <div class="col-sm-8">
                                             <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="<?=$coupon_code; ?>">

                                             <label class="control-label pull-left"><?= $this->lang->line('couponType'); ?>:<span class="coupon_type">---</span></label>
                                             <label class="control-label pull-right"><?= $this->lang->line('couponValue'); ?>:<span class="coupon_value">0.00</span></label>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-12 div1 hide">
                                       <div class="form-group">    
                                          <div class="col-sm-8 col-sm-offset-4">
                                             <div class="alert text-left msg_color">
                                              <strong id="coupon_code_msg">
                                              </strong>
                                            </div>
                                          </div>
                                       </div>
                                    </div>

                                 </div>

                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="discount_to_all_input" class="col-sm-4 control-label"><?= $this->lang->line('discount_on_all'); ?></label>    
                                          <div class="col-sm-4">
                                             <input type="text" class="form-control  text-right only_currency" id="discount_to_all_input" name="discount_to_all_input" onkeyup="enable_or_disable_item_discount();" value="<?php echo  $discount_input; ?>">
                                          </div>
                                          <div class="col-sm-4">
                                             <select class="form-control" onchange="final_total();" id='discount_to_all_type' name="discount_to_all_type">
                                                <option value='in_percentage'>Per%</option>
                                                <option value='in_fixed'>Fixed</option>
                                             </select>
                                          </div>
                                          <!-- Dynamicaly select Supplier name -->
                                          <script type="text/javascript">
                                             <?php if($discount_type!=''){ ?>
                                                 document.getElementById('discount_to_all_type').value='<?php echo  $discount_type; ?>';
                                             <?php }?>
                                          </script>
                                          <!-- Dynamicaly select Supplier name end-->
                                       </div>
                                    </div>
                                 </div>
                                <div class="row" style="display:none;">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="return_note" class="col-sm-4 control-label">Comments</label>
                                          <div class="col-sm-8">
                                             <textarea class="form-control text-left" id='return_note' name="return_note"><?= $return_note; ?></textarea>
                                            <span id="return_note_msg" style="display:none" class="text-danger"></span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 
                              </div>
                              

                              <div class="col-md-6">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                           
                                          <table  class="col-md-9">
                                             <tr>
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('subtotal'); ?></th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="subtotal_amt" name="subtotal_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr style="display:none;">
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('other_charges'); ?></th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="other_charges_amt" name="other_charges_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr style="display:none;">
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('couponDiscount'); ?></th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="coupon_discount_amt" name="coupon_discount_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr>
                                                <th class="text-right" style="font-size: 17px;">Discount</th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="discount_to_all_amt" name="discount_to_all_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr class="summary-trn-row"<?= $branch_tax_enabled ? '' : ' style="display:none;"' ?>>
                                                <th class="text-right" style="font-size: 17px;"><?= (!empty($this->lang->line('taxable'))) ? $this->lang->line('taxable') : 'Taxable'; ?></th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="taxable_amt" name="taxable_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr class="summary-trn-row"<?= $branch_tax_enabled ? '' : ' style="display:none;"' ?>>
                                                <th class="text-right" style="font-size: 17px;" id="summary_vat_label">VAT</th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="summary_vat_amt" name="summary_vat_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr style="display:none;">
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('round_off'); ?></th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="round_off_amt" name="tot_round_off_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr>
                                                <th class="text-right" style="font-size: 17px;">Net Amount</th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4><b id="total_amt" name="total_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                          </table>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-xs-12 " style="display:none;">
                                 <div class="col-sm-12">
                                       <div class="box-body ">
                                        <div class="col-md-12">
                                          <table class="table table-hover table-bordered" style="width:100%" id="payments_table"><h4 class="box-title text-info"><?= $this->lang->line('previous_payments_information'); ?> : </h4>
                                             <thead>
                                                <tr class="bg-gray " >
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
                                                  if(!empty($return_id)){
                                                    $q3 = $this->db->query("select * from db_salespaymentsreturn where return_id=$return_id");
                                                    if($q3->num_rows()>0){
                                                      $i=1;
                                                      $total_paid = 0;
                                                      foreach ($q3->result() as $res3) {
                                                        echo "<tr class='text-center text-bold' id='payment_row_".$res3->id."'>";
                                                        echo "<td>".$i."</td>";
                                                        echo "<td>".show_date($res3->payment_date)."</td>";
                                                        echo "<td>".$res3->payment_type."</td>";
                                                        echo "<td>".$res3->payment_note."</td>";
                                                        echo "<td class='text-right' id='paid_amt_$i'>".store_number_format($res3->payment)."</td>";
                                                        echo '<td><i class="fa fa-trash text-red pointer" onclick="delete_payment('.$res3->id.')"> Delete</i></td>';
                                                        echo "</tr>";
                                                        $total_paid +=$res3->payment;
                                                        $i++;
                                                      }
                                                      echo "<tr class='text-right text-bold'><td colspan='4' >Total</td><td data-rowcount='$i' id='paid_amt_tot'>".store_number_format($total_paid)."</td><td></td></tr>";
                                                    }
                                                    else{
                                                      echo "<tr><td colspan='6' class='text-center text-bold'>No Previous Payments Found!!</td></tr>";
                                                    }

                                                  }
                                                  else{
                                                    echo "<tr><td colspan='6' class='text-center text-bold'>Payments Pending!!</td></tr>";
                                                  }
                                                ?>
                                             </tbody>
                                          </table>
                                        </div>
                                       </div>
                                       <!-- /.box-body -->
                                    </div>
                                 <!-- /.box -->
                              </div>

                              <div class="col-xs-12 ">
                                 <div class="col-sm-12">
                                       <div class="box-body ">

                                          <div class="col-md-12 payments_div payments_div_">
                                            <h4 class="box-title text-info"><?= $this->lang->line('subtotal'); ?> : </h4>
                                          <div class="box box-solid bg-gray">
                                            <div class="box-body">
                                              <div class="row">
                                         
                                                <div class="col-md-3">
                                                  <div class="">
                                                  <label for="amount">Paid Amount</label>
                                                    <input type="text" class="form-control text-right paid_amt only_currency" id="amount" name="amount" placeholder="" >
                                                      <span id="amount_msg" style="display:none" class="text-danger"></span>
                                                </div>
                                               </div>
                                                <div class="col-md-3">
                                                  <div class="">
                                                    <label for="payment_type"><?= $this->lang->line('payment_type'); ?></label>
                                                    <select class="form-control select2" id='payment_type' name="payment_type">
                                                       <?php
                                                       $default_payment_type = 'CASH';
                                                       if (!empty($return_id) && (int)$return_id > 0) {
                                                           $q_pay = $this->db->select('payment_type')->where('return_id', (int)$return_id)->order_by('id', 'asc')->limit(1)->get('db_salespaymentsreturn');
                                                           if ($q_pay->num_rows() > 0 && !empty($q_pay->row()->payment_type)) {
                                                               $default_payment_type = $q_pay->row()->payment_type;
                                                           }
                                                       }
                                                       $q1 = $this->db->query("select * from db_paymenttypes where status=1 and store_id=" . get_current_store_id());
                                                       if ($q1->num_rows() > 0) {
                                                          echo "<option value=''>-Select-</option>";
                                                          foreach ($q1->result() as $res1) {
                                                             $selected = (strcasecmp($res1->payment_type, $default_payment_type) == 0) ? 'selected' : '';
                                                             echo "<option value='" . $res1->payment_type . "' $selected>" . $res1->payment_type . "</option>";
                                                          }
                                                       } else {
                                                          echo "<option value=''>None</option>";
                                                       }
                                                       ?>
                                                     </select>
                                                    <span id="payment_type_msg" style="display:none" class="text-danger"></span>
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="account_id"><?= $this->lang->line('account'); ?></label>
                                                    <select class="form-control select2" id='account_id' name="account_id">
                                                       <?php
                                                       $default_account_id = '';
                                                       if (!empty($return_id) && (int)$return_id > 0) {
                                                           $q_pay_acc = $this->db->select('account_id')->where('return_id', (int)$return_id)->order_by('id', 'asc')->limit(1)->get('db_salespaymentsreturn');
                                                           if ($q_pay_acc->num_rows() > 0 && !empty($q_pay_acc->row()->account_id)) {
                                                               $default_account_id = $q_pay_acc->row()->account_id;
                                                           }
                                                       }
                                                       if (empty($default_account_id)) {
                                                           $q_acc = $this->db->query("select id from ac_accounts where status=1 and store_id=" . get_current_store_id() . " and account_name LIKE '%Cash in Hand%' limit 1");
                                                           if ($q_acc->num_rows() > 0) {
                                                               $default_account_id = $q_acc->row()->id;
                                                           }
                                                       }
                                                       echo '<option value="">-None-</option>';
                                                       echo get_accounts_select_list($default_account_id);
                                                       ?>
                                                     </select>
                                                    <span id="account_id_msg" style="display:none" class="text-danger"></span>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="">
                                                    <label for="refund_amount">Refund Amount</label>
                                                    <input type="text" class="form-control text-right" id="refund_amount" name="refund_amount" value="0.00" readonly disabled style="background-color:#ffcccc;cursor:not-allowed;">
                                                    <span id="refund_amount_msg" style="display:none" class="text-danger"></span>
                                                  </div>
                                                </div>
                                            <div class="clearfix"></div>
                                        </div>  
                                        <div class="row">
                                               <div class="col-md-12">
                                                  <div class="">
                                                    <label for="payment_note"><?= (!empty($this->lang->line('note'))) ? $this->lang->line('note') : 'Note'; ?></label>
                                                    <textarea type="text" class="form-control" id="payment_note" name="payment_note" placeholder="" ></textarea>
                                                    <span id="payment_note_msg" style="display:none" class="text-danger"></span>
                                                  </div>
                                               </div>
                                                
                                            <div class="clearfix"></div>
                                        </div>   
                                        </div>
                                        </div>
                                        </div><!-- col-md-12 -->
                                       </div>
                                       <!-- /.box-body -->
                                    </div>
                                 <!-- /.box -->
                              </div>

                              <!-- SMS Sender while saving -->
                                <?php 
                                   //Change Return
                                    $send_sms_checkbox='disabled';
                                    if($CI->is_sms_enabled()){
                                      if(!isset($sales_id)){
                                        $send_sms_checkbox='checked';  
                                      }else{
                                        $send_sms_checkbox='';
                                      }
                                    }

                              ?>
                             
                              <div class="col-xs-12 " style="display:none;">
                                 <div class="col-sm-12">
                                       <div class="box-body ">
                                          <div class="col-md-12">
                                            <div class="checkbox icheck">
                                      <label>
                                        <input type="checkbox" <?=$send_sms_checkbox;?> class="form-control" id="send_sms" name="send_sms" > <label for="sales_discount" class=" control-label"><?= $this->lang->line('send_sms_to_customer'); ?>
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
                           <div class="box-footer col-sm-12">
                              <center>
                                <?php
                                 if($oper=='return_against_sales'){
                                   $btn_id='save';
                                   $btn_name="Save";
                                   $btn_print_id='save_and_print';
                                   $btn_print_name="Save And Print";
                                   echo '<input type="hidden" name="sales_id" id="sales_id" value="'.$sales_id.'"/>';
                                 }
                                 if($oper=='edit_existing_return'){
                                   $btn_id='update';
                                   $btn_name="Update";
                                   $btn_print_id='update_and_print';
                                   $btn_print_name="Update And Print";
                                   echo '<input type="hidden" name="return_id" id="return_id" value="'.$return_id.'"/>';
                                   echo '<input type="hidden" name="sales_id" id="sales_id" value="'.$sales_id.'"/>';
                                 }
                                 if($oper=='create_new_return'){
                                   $btn_id='save';
                                   $btn_name="Save";
                                   $btn_print_id='save_and_print';
                                   $btn_print_name="Save And Print";
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
  
 <?php include"footer.php"; ?>
<!-- SOUND CODE -->
<?php include"comman/code_js_sound.php"; ?>
<!-- GENERAL CODE -->
<?php include"comman/code_js.php"; ?>

<script src="<?php echo $theme_link; ?>js/modals.js"></script>
<script src="<?php echo $theme_link; ?>js/modals/modal_item.js"></script>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

      <script>
        var pdf_part_number_popup_enabled = <?= is_pdf_part_number_popup_enabled() ? 'true' : 'false'; ?>;
      </script>
      <script src="<?php echo $theme_link; ?>js/sales-return.js"></script>  
      <script>
        var base_url=$("#base_url").val();
        var STORE_TAX_ENABLED = <?= $branch_tax_enabled ? 'true' : 'false'; ?>;
      </script>
      <script>
        function customer_trn_present(){
          return (($("#customer_trn_no").val() || '').toString().trim() !== '');
        }
        function set_customer_trn_no(){
          if (!$("#customer_trn_no").length) {
            if (typeof final_total === 'function') { final_total(); }
            return;
          }
          var trn = ($('option:selected', "#customer_id").attr('data-tax_number') || '').toString().trim();
          $("#customer_trn_no_display").val(trn);
          $("#customer_trn_no").val(trn);
          if ($("#customer_trn_no_group").length) {
            if (trn !== '') {
              $("#customer_trn_no_group").show();
            } else {
              $("#customer_trn_no_group").hide();
            }
          }
          if (typeof final_total === 'function') { final_total(); }
        }
        $(document).on("change", "#customer_id", function(){
          set_customer_trn_no();
        });
        set_customer_trn_no();

        $("#store_id").on("change",function(){
          var store_id=$(this).val();
          $.post(base_url+"sales/get_customers_select_list",{store_id:store_id},function(result){
              $("#customer_id").html('').append(result).select2();
              $("#sales_table > tbody").empty();
              set_customer_trn_no();
              final_total();
          });
          $.post(base_url+"sales/get_tax_select_list",{store_id:store_id},function(result){
              $("#other_charges_tax_id").html('').append(result).select2();
              final_total();
          });
        });

        /*Warehouse*/
        $("#warehouse_id").on("change",function(){
          var warehouse_id=$(this).val();
          $("#sales_table > tbody").empty();
          final_total();
        });
        /*Warehouse end*/
        
         $(".close_btn").on("click",function(){
           if(confirm('Are you sure you want to navigate away from this page?')){
               window.location='<?php echo $base_url; ?>dashboard';
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
             // Due date must be after today (destroy first — code_js.php already inits datepickers)
             if ($('#due_date').data('datepicker')) {
                $('#due_date').datepicker('remove');
             }
             $('#due_date').datepicker({
               autoclose: true,
               format: 'dd-mm-yyyy',
               todayHighlight: true,
               startDate: '+1d'
             });
          
        /*if($("#warehouse_id").val()==''){
          $("#item_search").attr({
            disabled: true,
          });
          toastr["warning"]("Please Select Warehouse!!");
          failed.currentTime = 0; 
          failed.play();
         
        }*/
         
         /* ---------- CALCULATE TAX -------------*/
        

         function calculate_tax(i){ //i=Row
            set_tax_value(i);

           //Find the Tax type and Tax amount
           var tax_type = $("#tr_tax_type_"+i).val();
           var tax_amount = $("#td_data_"+i+"_11").val();
           if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
              tax_amount = 0;
              $("#td_data_"+i+"_11").val(to_Fixed(0));
              tax_type = 'Exclusive';
           }

           var qty=$("#td_data_"+i+"_3").val();
           var sales_price=parseFloat($("#td_data_"+i+"_10").val());
           $("#td_data_"+i+"_4").val(sales_price);
           /*Discounr*/
           var discount_amt=$("#td_data_"+i+"_8").val();
               discount_amt   =(isNaN(parseFloat(discount_amt)))    ? 0 : parseFloat(discount_amt);

           var amt=parseFloat(qty) * sales_price;//Taxable

           var total_amt=amt-discount_amt;
           total_amt = (tax_type=='Inclusive') ? total_amt : parseFloat(total_amt) + parseFloat(tax_amount);
           
           //Set Unit cost
           $("#td_data_"+i+"_9").val('').val(to_Fixed(total_amt));
        
           final_total();
         }
        
         /* ---------- CALCULATE GST END -------------*/

         /*Calculate Coupon Discount Amount*/
         const discount_coupon_tot = function(subtotal) {
             var coupon_value=parseFloat($(".coupon_value").text());
                 coupon_value = isNaN(coupon_value) ? 0 : coupon_value;

             var coupon_type=$(".coupon_type").text();

             var discount_amt =0;
             if(coupon_type!='' && coupon_value>0){
                 if(coupon_type=='Percentage'){
                     discount_amt=(subtotal*coupon_value)/100;
                 }
                 else{//Fixed
                     discount_amt=coupon_value;
                 }
             }
             return discount_amt;
         }

        
         /* ---------- Final Description of amount ------------*/
         function final_total(){
           var rowcount=$("#hidden_rowcount").val();
           var pretax_subtotal=0;
           var vat_sum=0;
           var total_quantity=0;
           var vat_pct=null;
           var mixed_vat=false;

           var other_charges_per_amt=parseFloat(0);
           var other_charges_total_amt=0;
           var other_charges_input=0;
           if($("#other_charges_input").val()!=null && $("#other_charges_input").val()!=''){
              var other_charges_tax_id =$('option:selected', '#other_charges_tax_id').attr('data-tax');
             other_charges_input=parseFloat($("#other_charges_input").val()) || 0;
             if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
               other_charges_tax_id = 0;
             }
             if(other_charges_tax_id>0){
               other_charges_per_amt=(other_charges_tax_id * other_charges_input)/100;
             }
             other_charges_total_amt=parseFloat(other_charges_per_amt)+parseFloat(other_charges_input);
           }

           for(var i=1;i<=rowcount;i++){
             if(document.getElementById("td_data_"+i+"_3")){
               if($("#td_data_"+i+"_3").val()!=null && $("#td_data_"+i+"_3").val()!=''){
                    var line_amt=parseFloat($("#td_data_"+i+"_9").val());
                    line_amt=isNaN(line_amt)?0:line_amt;
                    var line_vat=parseFloat($("#td_data_"+i+"_11").val());
                    line_vat=isNaN(line_vat)?0:line_vat;
                    if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
                      line_vat = 0;
                    }
                    vat_sum += line_vat;
                    pretax_subtotal += (line_amt - line_vat);
                    total_quantity +=parseFloat($("#td_data_"+i+"_3").val()) || 0;

                    var row_tax=parseFloat($("#tr_tax_value_"+i).val());
                    if(!isNaN(row_tax)){
                      if(vat_pct===null){ vat_pct=row_tax; }
                      else if(parseFloat(vat_pct)!==parseFloat(row_tax)){ mixed_vat=true; }
                    }
                }
             }
           }

           vat_sum += other_charges_per_amt;
           $(".total_quantity").html(format_qty(total_quantity));

           if(pretax_subtotal!=0 || vat_sum!=0 || other_charges_input!=0){
             var taxable=pretax_subtotal + other_charges_input;
             var coupon_amt = discount_coupon_tot(taxable);
             taxable-=coupon_amt;

             var discount_input=parseFloat($("#discount_to_all_input").val());
             discount_input = isNaN(discount_input) ? 0 : discount_input;
             var discount=0;
             if(discount_input>0){
                 var discount_type=$("#discount_to_all_type").val();
                 if(discount_type=='in_fixed'){
                   taxable-=discount_input;
                   discount=discount_input;
                 }
                 else if(discount_type=='in_percentage'){
                     discount=(taxable*discount_input)/100;
                    taxable-=discount;
                 }
             }
             discount=parseFloat(discount) || 0;
             if(taxable<0){ taxable=0; }

             // Tax/VAT by branch TRN (settings), not customer TRN
             var has_trn = (typeof STORE_TAX_ENABLED !== 'undefined' && STORE_TAX_ENABLED);
             if(has_trn){
               $(".summary-trn-row").show();
               $("#taxable_amt").html(to_Fixed(taxable));
               $("#summary_vat_amt").html(to_Fixed(vat_sum));
               if(!mixed_vat && vat_pct!==null && !isNaN(vat_pct)){
                 $("#summary_vat_label").text('VAT ' + parseFloat(vat_pct) + '%');
               } else {
                 $("#summary_vat_label").text('VAT');
               }
             } else {
               $(".summary-trn-row").hide();
               $("#taxable_amt").html('0.00');
               $("#summary_vat_amt").html('0.00');
             }

             var net_before_round = has_trn ? (taxable + vat_sum) : taxable;
             var subtotal_round=round_off(net_before_round);
             var subtotal_diff=subtotal_round-net_before_round;

             $("#subtotal_amt").html(to_Fixed(pretax_subtotal));
             $("#other_charges_amt").html(to_Fixed(other_charges_total_amt));
             $("#discount_to_all_amt").html(to_Fixed(discount));
             $("#hidden_discount_to_all_amt").val(to_Fixed(discount));
             $("#coupon_discount_amt").html(to_Fixed(coupon_amt));
             $("#round_off_amt").html(to_Fixed(subtotal_diff));
             $("#total_amt").html(to_Fixed(subtotal_round));
             $("#hidden_total_amt").val(to_Fixed(subtotal_round));
             update_refund_amount();
           }
           else{
             var has_trn = (typeof STORE_TAX_ENABLED !== 'undefined' && STORE_TAX_ENABLED);
             if(has_trn){
               $(".summary-trn-row").show();
               $("#summary_vat_label").text('VAT');
             } else {
               $(".summary-trn-row").hide();
             }
             $("#subtotal_amt").html('0.00');
             $("#taxable_amt").html('0.00');
             $("#summary_vat_amt").html('0.00');
             $("#discount_to_all_amt").html('0.00');
             $("#hidden_discount_to_all_amt").val('0.00');
             $("#other_charges_amt").html('0.00');
             $("#coupon_discount_amt").html('0.00');
             $("#round_off_amt").html('0.00');
             $("#total_amt").html('0.00');
             $("#hidden_total_amt").val('0.00');
             update_refund_amount();
           }
         }

         function update_refund_amount(){
           if(!$("#refund_amount").length){ return; }
           var paid=parseFloat($("#amount").val());
           paid=isNaN(paid)?0:paid;
           var net=parseFloat($("#total_amt").text());
           net=isNaN(net)?0:net;
           var refund=paid>net?(paid-net):0;
           $("#refund_amount").val(to_Fixed(refund));
         }
         $(document).on('keyup change blur', '#amount', function(){
           update_refund_amount();
         });
         /* ---------- Final Description of amount end ------------*/
          
         function removerow(id){//id=Rowid
           
         $("#row_"+id).remove();
         final_total();
         failed.currentTime = 0;
        failed.play();
         }
               
     

    function enable_or_disable_item_discount(){
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

      var rowcount=$("#hidden_rowcount").val();
      for(k=1;k<=rowcount;k++){
       if(document.getElementById("tr_item_id_"+k)){
         calculate_tax(k);
       }//if end
     }//for end

      //final_total();
    }


    //Sale Items Modal Operations Start
    function show_sales_item_modal(row_id){
      $('#sales_item').modal('toggle');
      $("#popup_tax_id").select2();

      //Find the item details
      var item_name = $("#td_data_"+row_id+"_1").html();
      var tax_type = $("#tr_tax_type_"+row_id).val();
      var tax_id = $("#tr_tax_id_"+row_id).val();
      var description = $("#description_"+row_id).val();

      /*Discount*/
      var item_discount_input = $("#item_discount_input_"+row_id).val();
      var item_discount_type = $("#item_discount_type_"+row_id).val();

      //Set to Popup
      $("#item_discount_input").val(item_discount_input);
      $("#item_discount_type").val(item_discount_type).select2();

      $("#popup_item_name").html(item_name);
      $("#popup_tax_type").val(tax_type).select2();
      $("#popup_tax_id").val(tax_id).select2();
      $("#popup_description").val(description);
      $("#popup_row_id").val(row_id);
    }

    function set_info(){
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
      $("#item_discount_input_"+row_id).val(item_discount_input);
      $("#item_discount_type_"+row_id).val(item_discount_type);

      $("#tr_tax_type_"+row_id).val(tax_type);
      $("#tr_tax_id_"+row_id).val(tax_id);
      $("#tr_tax_value_"+row_id).val(tax);//%
      $("#description_"+row_id).val(description);
      var display_tax_name = tax_name ? tax_name.replace(/vat/gi, '').trim() : '';
      if(!display_tax_name && !isNaN(tax)){
         display_tax_name = tax + '%';
      }
      $("#td_data_"+row_id+"_12").html(display_tax_name);
      
      calculate_tax(row_id);
      $('#sales_item').modal('toggle');
    }
    function set_tax_value(row_id){
      //get the sales price of the item
      var tax_type = $("#tr_tax_type_"+row_id).val();
      var tax = $("#tr_tax_value_"+row_id).val(); //%
      if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
        tax = 0;
      }
      var qty=$("#td_data_"+row_id+"_3").val();
          qty = (isNaN(qty)) ? 0 :qty;
      var sales_price = parseFloat($("#td_data_"+row_id+"_10").val());
          sales_price = (isNaN(sales_price)) ? 0 :sales_price;
          sales_price = sales_price * qty;

      /*Discount*/
      var item_discount_type = $("#item_discount_type_"+row_id).val();
      var item_discount_input = parseFloat($("#item_discount_input_"+row_id).val());
          item_discount_input = (isNaN(item_discount_input)) ? 0 :item_discount_input;

      //Calculate discount      
      var discount_amt=(item_discount_type=='Percentage') ? ((sales_price) * item_discount_input)/100 : (item_discount_input*qty);
      sales_price-=parseFloat(discount_amt);

      var tax_amount = (tax_type=='Inclusive') ? calculate_inclusive(sales_price,tax) : calculate_exclusive(sales_price,tax);
      if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
        tax_amount = 0;
      }
      
      $("#td_data_"+row_id+"_8").val(to_Fixed(discount_amt));

      $("#td_data_"+row_id+"_11").val(to_Fixed(tax_amount));

    }
    //Sale Items Modal Operations End

      </script>


      <!-- Return against sales Entry -->
      <script type="text/javascript">
        <?php if($oper=='return_against_sales') { ?>
          $(document).ready(function(){
                var base_url='<?= base_url();?>';
                var sales_id='<?= $sales_id;?>';
                $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                $.post(base_url+"sales_return/sales_list/"+sales_id,{},function(result){
                  //alert(result);
                  $('#sales_table tbody').append(result);
                  $("#hidden_rowcount").val(parseInt(<?=$items_count;?>)+1);
                  success.currentTime = 0;
                  success.play();
                  get_coupon_details();
                  enable_or_disable_item_discount();
                  $(".overlay").remove();
              }); 
             });
        <?php } ?>
      </script>
      <!-- EDIT OPERATIONS -->
      <script type="text/javascript">
         <?php if($oper=='edit_existing_return') { ?> 
             $(document).ready(function(){
                $("#store_id").attr('readonly',true);
                var base_url='<?= base_url();?>';
                var return_id='<?= $return_id;?>';
                $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                $.post(base_url+"sales_return/return_sales_list/"+return_id,{},function(result){
                  //alert(result);
                  $('#sales_table tbody').append(result);
                  $("#hidden_rowcount").val(parseInt(<?=$items_count;?>)+1);
                  success.currentTime = 0;
                  success.play();
                  get_coupon_details();
                  enable_or_disable_item_discount();
                  $(".overlay").remove();
              }); 
             });
         <?php }?>
      </script>
      <!-- UPDATE OPERATIONS end-->

      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>
