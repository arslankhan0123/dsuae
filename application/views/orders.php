<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>
<style type="text/css">
   table.table-bordered > thead > tr > th {
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
     ensure_order_supplier_trn_no();
     if(!isset($order_id)){
       $supplier_id  = $pur_date = $purchase_status = $warehouse_id =
       $reference_no  =
       $other_charges_input          = $other_charges_tax_id =
       $discount_input = $discount_type  = $purchase_note=$store_id=$supplier_trn_no='';
       $pur_date=show_date(date("d-m-Y"));
     }
     else{
       $q2 = $this->db->query("select * from db_orders where id=$order_id");
       $supplier_id=$q2->row()->supplier_id;
       $warehouse_id=$q2->row()->warehouse_id;
       $pur_date=show_date($q2->row()->order_date);
       $purchase_status=$q2->row()->order_status;
       $reference_no=$q2->row()->reference_no;
       $discount_input=store_number_format($q2->row()->discount_to_all_input,2);
       $discount_type=$q2->row()->discount_to_all_type;
       $other_charges_input=store_number_format($q2->row()->other_charges_input,0);
       $other_charges_tax_id=$q2->row()->other_charges_tax_id;
       $purchase_note=$q2->row()->order_note;
       $store_id=$q2->row()->store_id;
       $supplier_trn_no = !empty($q2->row()->supplier_trn_no) ? $q2->row()->supplier_trn_no : get_supplier_trn_no($supplier_id);

       $items_count = $this->db->query("select count(*) as items_count from db_orderitems where order_id=$order_id")->row()->items_count;
     }
     if(!isset($supplier_trn_no)){
       $supplier_trn_no = '';
     }
     $branch_tax_store_id = !empty($store_id) ? $store_id : get_current_store_id();
     $branch_tax_enabled = is_branch_tax_enabled($branch_tax_store_id);
     ?>
     <?php if(!$branch_tax_enabled): ?>
     <style>#orders_table .branch-tax-col{display:none !important;}</style>
     <?php endif; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- **********************MODALS***************** -->
    <?php include"modals/modal_supplier.php"; ?>
    <?php include"modals/modal_order_item.php"; ?>
    <?php include"modals/modal_item.php"; ?>
    <?php include"modals/modal_item_or_service.php"; ?>
    <!-- **********************MODALS END***************** -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
         <h1>
            <?=$page_title;?>
            <small>Add/Update Order</small>
         </h1>
         <ol class="breadcrumb">
            <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo $base_url; ?>orders">Order List</a></li>
            <li><a href="<?php echo $base_url; ?>orders/add">New Order</a></li>
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
                        
                        <!-- form start -->
                        <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'orders-form', 'enctype'=>'multipart/form-data', 'method'=>'POST'));?>
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <input type="hidden" value='1' id="hidden_rowcount" name="hidden_rowcount">
                           <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">
                           <input type="hidden" value='Received' id="purchase_status" name="purchase_status">

                           <div class="box-body">
                              <!-- Store Code -->
                              <?php 
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              ?>
                              <!-- Store Code end -->
                              <!-- Warehouse Code -->
                              <?php 
                               if(warehouse_module() && warehouse_count()>1) {$this->load->view('warehouse/warehouse_code',array('show_warehouse_select_box'=>true,'warehouse_id'=>$warehouse_id,'div_length'=>'col-sm-3','show_select_option'=>false)); }else{
                                echo "<input type='hidden' name='warehouse_id' id='warehouse_id' value='".get_store_warehouse_id()."'>";
                               }
                              ?>
                              <!-- Warehouse Code end -->

                              <div class="form-group">
                                 <label for="supplier_id" class="col-sm-2 control-label"><?= $this->lang->line('supplier_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group">
                                       <select class="form-control select2" id="supplier_id" name="supplier_id"  style="width: 100%;" >
                                          <?= get_suppliers_select_list($supplier_id,get_current_store_id(),$show_select=true); ?>
                                       </select>
                                       <span class="input-group-addon pointer" data-toggle="modal" data-target="#supplier-modal" title="New Supplier?"><i class="fa fa-user-plus text-primary fa-lg"></i></span>
                                    </div>
                                    <span id="supplier_id_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 <label for="pur_date" class="col-sm-2 control-label">Order Date <label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right datepicker"  id="pur_date" name="pur_date" readonly onkeyup="shift_cursor(event,'purchase_status')" value="<?= $pur_date;?>">
                                    </div>
                                    <span id="pur_date_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                  <label for="reference_no" class="col-sm-2 control-label"><?= $this->lang->line('reference_no'); ?> </label>
                                 <div class="col-sm-3">
                                    <input type="text" value="<?php echo  $reference_no; ?>" class="form-control " id="reference_no" name="reference_no" placeholder="" >
                  <span id="reference_no_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 <?php if($branch_tax_enabled): ?>
                                 <?php $show_supplier_trn = (trim((string)$supplier_trn_no) !== ''); ?>
                                 <label for="supplier_trn_no" id="supplier_trn_no_label" class="col-sm-2 control-label" <?= $show_supplier_trn ? '' : 'style="display:none;"'; ?>>Supplier TRN No.</label>
                                 <div class="col-sm-3" id="supplier_trn_no_wrap" <?= $show_supplier_trn ? '' : 'style="display:none;"'; ?>>
                                    <input type="text" value="<?= htmlspecialchars($supplier_trn_no, ENT_QUOTES); ?>" class="form-control" id="supplier_trn_no" name="supplier_trn_no">
                                    <span id="supplier_trn_no_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 <?php else: ?>
                                 <input type="hidden" name="supplier_trn_no" id="supplier_trn_no" value="">
                                 <?php endif; ?>
                              </div>
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
                                                 <input type="text" class="form-control " placeholder="Item name/Barcode/Itemcode" autofocus id="item_search">
                                                 <span class="input-group-addon pointer text-green show_item_service" title="Click to Add New Item or Service"><i class="fa fa-plus"></i></span>
                                              </div>
                                        </div>
                                      </div>
                                      <div class="box-body">
                                        <div class="table-responsive" style="width: 100%">
                                        <table class="table table-hover table-bordered" style="width:100%" id="orders_table">
                                             <thead class="custom_thead">
                                                <tr class="bg-primary" >
                                                   <th rowspan='2' style="width:10%"><?= get_item_barcode_label(); ?></th>
                                                   <th rowspan='2' style="width:15%"><?= $this->lang->line('item_name'); ?></th>
                                                   <th rowspan='2' style="width:15%;min-width: 180px;"><?= $this->lang->line('quantity'); ?></th>
                                                   <th rowspan='2' style="width:10%">Order Price(<?= $CI->currency(); ?>)</th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('discount'); ?>(<?= $CI->currency(); ?>)</th>
                                                   <th rowspan='2' class="branch-tax-col" style="width:7.5%"><?= (!empty($this->lang->line('vat_amount'))) ? $this->lang->line('vat_amount') : 'Vat Amount'; ?></th>
                                                   <th rowspan='2' style="width:7.5%"><?= $this->lang->line('unit_cost'); ?></th>
                                                   <th rowspan='2' style="width:7.5%"><?= $this->lang->line('total_amount'); ?></th>
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
                              
                              <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="" class="col-sm-4 control-label"><?= $this->lang->line('total_quantities'); ?></label>    
                                          <div class="col-sm-4">
                                             <label class="control-label total_quantity text-success" style="font-size: 15pt;">0</label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
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
                                          <script type="text/javascript">
                                             <?php if($discount_type!=''){ ?>
                                                 document.getElementById('discount_to_all_type').value='<?php echo  $discount_type; ?>';
                                             <?php }?>
                                          </script>
                                       </div>
                                    </div>
                                 </div>
                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="purchase_note" class="col-sm-4 control-label"><?= $this->lang->line('note'); ?></label>    
                                          <div class="col-sm-8">
                                             <textarea class="form-control text-left" id='purchase_note' name="purchase_note"><?=$purchase_note;?></textarea>
                                             <span id="purchase_note_msg" style="display:none" class="text-danger"></span>
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
                                                   <h4>
                                                    <b id="subtotal_amt" name="subtotal_amt">0.00</b>
                                                   </h4>
                                                </th>
                                             </tr>
                                             <tr style="display:none;">
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('other_charges'); ?></th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4>
                                                    <b id="other_charges_amt" name="other_charges_amt">0.00</b>
                                                  </h4>
                                                </th>
                                             </tr>
                                             <tr>
                                                <th class="text-right" style="font-size: 17px;">Discount</th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4>
                                                    <b id="discount_to_all_amt" name="discount_to_all_amt">0.00</b></h4>
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
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('round_off'); ?>
                                                    <i class="hover-q " data-container="body" data-toggle="popover" data-placement="top" data-content="Go to Site Settings-> Site -> Disable the Round Off(Checkbox)." data-html="true" data-trigger="hover" data-original-title="" title="Do you wants to Disable Round Off ?">
                                                      <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                                    </i>
                                                </th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4>
                                                    <b id="round_off_amt" name="tot_round_off_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                             <tr>
                                                <th class="text-right" style="font-size: 17px;">Net Amount</th>
                                                <th class="text-right" style="padding-left:10%;font-size: 17px;">
                                                   <h4>
                                                    <b id="total_amt" name="total_amt">0.00</b></h4>
                                                </th>
                                             </tr>
                                          </table>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-xs-12 ">
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
                                                  if(isset($order_id)){
                                                    $q3 = $this->db->query("select * from db_orderpayments where order_id=$order_id");
                                                    if($q3->num_rows()>0){
                                                      $i=1;
                                                      $total_paid = 0;
                                                      foreach ($q3->result() as $res3) {
                                                        echo "<tr class='text-center text-bold' id='payment_row_".$res3->id."'>";
                                                        echo "<td>".$i."</td>";
                                                        echo "<td>".show_date($res3->payment_date)."</td>";
                                                        echo "<td>".$res3->payment_type."</td>";
                                                        echo "<td>".$res3->payment_note."</td>";
                                                        echo "<td class='text-right' id='paid_amt_$i'>".
                                                         $CI->currency($res3->payment)."</td>";
                                                        echo '<td><i class="fa fa-trash text-red pointer" onclick="delete_payment('.$res3->id.')"> Delete</i></td>';
                                                        echo "</tr>";
                                                        $total_paid +=$res3->payment;
                                                        $i++;
                                                      }
                                                      echo "<tr class='text-right text-bold'><td colspan='4' >Total</td><td data-rowcount='$i' id='paid_amt_tot'>".
                                                      $CI->currency(number_format($total_paid,2,'.',''))."</td><td></td></tr>";
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
                                    </div>
                              </div>

                              <div class="col-xs-12 ">
                                 <div class="col-sm-12">
                                       <div class="box-body ">
                                          <div class="col-md-12 payments_div payments_div_">
                                            <h4 class="box-title text-info"><?= $this->lang->line('make_payment'); ?> : </h4>
                                          <div class="box box-solid bg-gray">
                                            <div class="box-body">
                                              <div class="row">
                                                <div class="col-md-4">
                                                  <div class="">
                                                   <label for="amount"><?= $this->lang->line('amount'); ?></label>
                                                    <input type="text" class="form-control text-right paid_amt only_currency" id="amount" name="amount" placeholder="" >
                                                    <span id="amount_msg" style="display:none" class="text-danger"></span>
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="">
                                                    <label for="payment_type"><?= $this->lang->line('payment_type'); ?></label>
                                                    <select class="form-control select2" id='payment_type' name="payment_type">
                                                      <?php
                                                        $q1=$this->db->query("select * from db_paymenttypes where status=1 and store_id=".get_current_store_id());
                                                         if($q1->num_rows()>0){
                                                            echo "<option value=''>-Select-</option>";
                                                             foreach($q1->result() as $res1){
                                                             echo "<option value='".$res1->payment_type."'>".$res1->payment_type ."</option>";
                                                           }
                                                         }
                                                         else{
                                                            echo "<option>None</option>";
                                                         }
                                                        ?>
                                                    </select>
                                                    <span id="payment_type_msg" style="display:none" class="text-danger"></span>
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="account_id"><?= $this->lang->line('account'); ?></label>
                                                    <select class="form-control select2" id='account_id' name="account_id">
                                                      <?php
                                                        echo '<option value="">-None-</option>'; 
                                                        echo get_accounts_select_list();
                                                        ?>
                                                    </select>
                                                    <span id="account_id_msg" style="display:none" class="text-danger"></span>
                                                </div>
                                            <div class="clearfix"></div>
                                          </div>  
                                          <div class="row">
                                               <div class="col-md-12">
                                                  <div class="">
                                                    <label for="payment_note"><?= $this->lang->line('payment_note'); ?></label>
                                                    <textarea type="text" class="form-control" id="payment_note" name="payment_note" placeholder="" ></textarea>
                                                    <span id="payment_note_msg" style="display:none" class="text-danger"></span>
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

                           </div>
                           
                           <div class="box-footer col-sm-12">
                              <center>
                                 <?php
                                 if(isset($order_id)){
                                   $btn_id='update';
                                   $btn_name="Update";
                                   echo '<input type="hidden" name="order_id" id="order_id" value="'.$order_id.'"/>';
                                 }
                                 else{
                                   $btn_id='save';
                                   $btn_name="Save";
                                 }
                                 ?>
                                 <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="<?php echo $btn_id;?>" class="btn btn-block btn-success payments_modal" title="Save Data"><?php echo $btn_name;?></button>
                                 </div>
                                 <div class="col-sm-3"><a href="<?= base_url()?>dashboard">
                                    <button type="button" class="btn btn-block btn-warning" title="Go Dashboard">Close</button>
                                  </a>
                                </div>
                              </center>
                           </div>

                           <?= form_close(); ?>
                     </div>
                  </div>
               </div>
             </section>
  </div>
  
  <?php include"footer.php"; ?>
<!-- SOUND CODE -->
<?php include"comman/code_js_sound.php"; ?>
<!-- GENERAL CODE -->
<?php include"comman/code_js.php"; ?>

 <script src="<?php echo $theme_link; ?>js/modals.js"></script>
 <script src="<?php echo $theme_link; ?>js/modals/modal_item.js"></script>
  <div class="control-sidebar-bg"></div>
</div>

<script>var STORE_TAX_ENABLED = <?= $branch_tax_enabled ? 'true' : 'false'; ?>;</script>
<script src="<?php echo $theme_link; ?>js/orders.js"></script>  

<script>
        var base_url=$("#base_url").val();
        $("#store_id").on("change",function(){
          var store_id=$(this).val();
          $.post(base_url+"orders/get_suppliers_select_list",{store_id:store_id},function(result){
              $("#supplier_id").html('').append(result).select2();
              $("#orders_table > tbody").empty();
              set_supplier_trn_no();
              final_total();
          });
          $.post(base_url+"sales/get_tax_select_list",{store_id:store_id},function(result){
              $("#other_charges_tax_id").html('').append(result).select2();
              final_total();
          });
        });

        $("#warehouse_id").on("change",function(){
          var warehouse_id=$(this).val();
          $("#orders_table > tbody").empty();
          final_total();
        });

        $("#supplier_id").on("change", function() {
           set_supplier_trn_no();
        });

        function supplier_trn_present() {
           return (($("#supplier_trn_no").val() || '').toString().trim() !== '');
        }

        function set_supplier_trn_no() {
           if (!STORE_TAX_ENABLED || !$("#supplier_trn_no_wrap").length) {
              if (typeof final_total === 'function') {
                 final_total();
              }
              return;
           }
           var trn = ($('option:selected', "#supplier_id").attr('data-tax_number') || '').toString().trim();
           $("#supplier_trn_no").val(trn);
           if (trn !== '') {
              $("#supplier_trn_no_label, #supplier_trn_no_wrap").show();
           } else {
              $("#supplier_trn_no_label, #supplier_trn_no_wrap").hide();
           }
           if (typeof final_total === 'function') {
              final_total();
           }
        }
        $(document).on('keyup change blur', '#supplier_trn_no', function() {
           if (typeof final_total === 'function') {
              final_total();
           }
        });
        set_supplier_trn_no();

         $(".close_btn").on("click",function(){
            if(confirm('Are you sure you want to navigate away from this page?')){
                window.location='<?php echo $base_url; ?>dashboard';
             }
         });
         $(".select2").select2();
         $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayHighlight: true
         });
          
         function calculate_tax(i){ //i=Row
           set_tax_value(i);

           var tax_type = $("#tr_tax_type_"+i).val();
           var tax_amount = get_float_type_data("#td_data_"+i+"_5");
           if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
              tax_amount = 0;
              $("#td_data_"+i+"_5").val(to_Fixed(0));
              tax_type = 'Exclusive';
           }
           var qty=get_float_type_data("#td_data_"+i+"_3")
           var purchase_price=get_float_type_data("#td_data_"+i+"_4");
           var discount =get_float_type_data("#td_data_"+i+"_8");
           
           var amt=qty * purchase_price;//Taxable
           var total_amt=amt-discount;

           total_amt = (tax_type=='Inclusive') ? total_amt : total_amt + tax_amount;
           
           $("#td_data_"+i+"_10").val('').val(to_Fixed(total_amt/qty));
           $("#td_data_"+i+"_9").val('').val(to_Fixed(total_amt));
           final_total();
         }
         
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
                    var line_vat=parseFloat($("#td_data_"+i+"_5").val());
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
             $("#round_off_amt").html(to_Fixed(subtotal_diff));
             $("#total_amt").html(to_Fixed(subtotal_round));
             $("#hidden_total_amt").val(to_Fixed(subtotal_round));
             if ($("#amount").length) {
               $("#amount").val(to_Fixed(subtotal_round));
             }
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
             $("#round_off_amt").html('0.00');
             $("#total_amt").html('0.00');
             $("#hidden_total_amt").val('0.00');
             if ($("#amount").length) {
               $("#amount").val('0.00');
             }
           }
         }
          
         function removerow(id){
             $("#row_"+id).remove();
             final_total();
             failed.currentTime = 0;
             failed.play();
         }
               
    function enable_or_disable_item_discount(){
      var rowcount=$("#hidden_rowcount").val();
      for(k=1;k<=rowcount;k++){
       if(document.getElementById("tr_item_id_"+k)){
         calculate_tax(k);
       }
     }
    }

    function show_purchase_item_modal(row_id){
      $('#purchase_item').modal('toggle');
      $("#popup_tax_id").select2();

      var item_name = $("#td_data_"+row_id+"_1").html();
      var tax_type = $("#tr_tax_type_"+row_id).val();
      var tax_id = $("#tr_tax_id_"+row_id).val();
      var description = $("#description_"+row_id).val();

      var item_discount_input = $("#item_discount_input_"+row_id).val();
      var item_discount_type = $("#item_discount_type_"+row_id).val();

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

      var item_discount_input = $("#item_discount_input").val();
      var item_discount_type = $("#item_discount_type").val();

      $("#item_discount_input_"+row_id).val(item_discount_input);
      $("#item_discount_type_"+row_id).val(item_discount_type);

      $("#tr_tax_type_"+row_id).val(tax_type);
      $("#tr_tax_id_"+row_id).val(tax_id);
      $("#tr_tax_value_"+row_id).val(tax);
      $("#description_"+row_id).val(description);
      $("#td_data_"+row_id+"_15").html(tax_name);
      
      calculate_tax(row_id);
      $('#purchase_item').modal('toggle');
    }
    
    function set_tax_value(row_id){
      var tax_type = $("#tr_tax_type_"+row_id).val();
      var tax = $("#tr_tax_value_"+row_id).val();
      if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
         tax = 0;
      }
      var qty=$("#td_data_"+row_id+"_3").val();
          qty = (isNaN(qty)) ? 0 :qty;
      var purchase_price = parseFloat($("#td_data_"+row_id+"_4").val());
          purchase_price = (isNaN(purchase_price)) ? 0 :purchase_price;
          purchase_price = purchase_price * qty;

      var item_discount_type = $("#item_discount_type_"+row_id).val();
      var item_discount_input = parseFloat($("#item_discount_input_"+row_id).val());
          item_discount_input = (isNaN(item_discount_input)) ? 0 :item_discount_input;

      var discount_amt=(item_discount_type=='Percentage') ? ((purchase_price) * item_discount_input)/100 : (item_discount_input*qty);
      purchase_price-=parseFloat(discount_amt);

      var tax_amount = (tax_type=='Inclusive') ? calculate_inclusive(purchase_price,tax) : calculate_exclusive(purchase_price,tax);
      if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
         tax_amount = 0;
      }
      
      $("#td_data_"+row_id+"_8").val(to_Fixed(discount_amt));

      $("#td_data_"+row_id+"_5").val(to_Fixed(tax_amount));
    }
</script>

<?php if (isset($order_id)) { ?>
  <script type="text/javascript">
    $(document).ready(function() {
      var base_url = $("#base_url").val();
      var order_id = $("#order_id").val();
      $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      $.post(base_url + "orders/return_purchase_list/" + order_id, {}, function(result) {
        $('#orders_table tbody').append(result);
        var rowcount = $('#orders_table tbody tr').length + 1;
        $("#hidden_rowcount").val(rowcount);
        enable_or_disable_item_discount();
        $(".overlay").remove();
      });
    });
  </script>
<?php } ?>

<!-- Make sidebar menu hughlighter/selector -->
<script>$(".orders-active-li").addClass("active");</script>

</body>
</html>
