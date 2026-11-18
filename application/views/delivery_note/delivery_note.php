<!DOCTYPE html>
<html>

<head>
<!-- FORM CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
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
 
 
 <?php $this->load->view('sidebar');?>
 
 <?php
 $CI =& get_instance();
    ensure_deliverynote_customer_trn_no();
    if(!isset($deliverynote_id)){
      $customer_id  = $deliverynote_date = $deliverynote_status = $warehouse_id =
      $reference_no  = $salesman_id = 
      $other_charges_input          = $other_charges_tax_id = $store_id =
      $discount_type  = $deliverynote_note = $customer_trn_no = '';
      $deliverynote_date=show_date(date("d-m-Y"));
      $expire_date='';
      $discount_input = 0;
      $discount_input = ($discount_input==0) ? '' : $discount_input;
    }
    else{
      $q2 = $this->db->query("select * from db_deliverynote where id=$deliverynote_id");
      $customer_id=$q2->row()->customer_id;
      $deliverynote_date=show_date($q2->row()->deliverynote_date);
      $expire_date=(!empty($q2->row()->expire_date)) ? show_date($q2->row()->expire_date) : '';
      $deliverynote_status=$q2->row()->deliverynote_status;
      $warehouse_id=$q2->row()->warehouse_id;
      $reference_no=$q2->row()->reference_no;
      $discount_input=$q2->row()->discount_to_all_input;
      $discount_type=$q2->row()->discount_to_all_type;
      $other_charges_input=$q2->row()->other_charges_input;
      $other_charges_tax_id=$q2->row()->other_charges_tax_id;
      $deliverynote_note=$q2->row()->deliverynote_note;
      $store_id=$q2->row()->store_id;
      $salesman_id=$q2->row()->salesman_id;
      $customer_trn_no = !empty($q2->row()->customer_trn_no) ? $q2->row()->customer_trn_no : get_customer_trn_no($customer_id);

      $items_count = $this->db->query("select count(*) as items_count from db_deliverynoteitems where deliverynote_id=$deliverynote_id")->row()->items_count;
    }
    if(!isset($customer_trn_no)){
      $customer_trn_no = '';
    }
    $branch_tax_store_id = !empty($store_id) ? $store_id : get_current_store_id();
    $branch_tax_enabled = is_branch_tax_enabled($branch_tax_store_id);
    ?>
    <?php if(!$branch_tax_enabled): ?>
    <style>#deliverynote_table .branch-tax-col{display:none !important;}</style>
    <?php endif; ?>

 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- **********************MODALS***************** -->
    
    <?php $this->load->view('modals/modal_customer');?>
    <?php $this->load->view('modals/modal_item');?>
    <?php $this->load->view('modals/modal_item_or_service');?>
    <?php /*$this->load->view('modals/modal_service');*/ ?>

    <!-- **********************MODALS END***************** -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
         <h1>
            <?=$page_title;?>
            <small>Add/Update Delivery Note</small>
         </h1>
         <ol class="breadcrumb">
            <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo $base_url; ?>deliverynote"><?= $this->lang->line('deliverynote_list'); ?></a></li>
            <li><a href="<?php echo $base_url; ?>Delivery_note/add">New Delivery Note</a></li>
            <li class="active"><?=$page_title;?></li>
         </ol>
      </section>

    <!-- Main content -->
     <section class="content">
               <div class="row">
                <!-- ********** ALERT MESSAGE START******* -->
               <?php $this->load->view('comman/code_flashdata');?>
               <?php $this->load->view('modals/modal_sales_item');?>
               <!-- ********** ALERT MESSAGE END******* -->
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary " >
                        <!-- style="background: #68deac;" -->
                        
                        <!-- form start -->
                         <!-- OK START -->
                        <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'deliverynote-form', 'enctype'=>'multipart/form-data', 'method'=>'POST'));?>
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
                               if(warehouse_module() && warehouse_count()>1) {$this->load->view('warehouse/warehouse_code',array('show_warehouse_select_box'=>true,'warehouse_id'=>$warehouse_id,'div_length'=>'col-sm-3','show_select_option'=>false)); }else{
                                echo "<input type='hidden' name='warehouse_id' id='warehouse_id' value='".get_store_warehouse_id()."'>";
                               }
                              ?>
                              <!-- Warehouse Code end -->
                              
                              <div class="form-group">
                                 <label for="customer_id" class="col-sm-2 control-label"><?= $this->lang->line('customer_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group">
                                       <select class="form-control select2" id="customer_id" name="customer_id"  style="width: 100%;">
                                          <?= get_customers_select_list($customer_id,get_current_store_id()); ?>
                                       </select>
                                       <span class="input-group-addon pointer" data-toggle="modal" data-target="#customer-modal" title="New Customer?"><i class="fa fa-user-plus text-primary fa-lg"></i></span>
                                    </div>
                                    <span id="customer_id_msg" style="display:none" class="text-danger"></span>
                                    <lable><?= $this->lang->line('previous_due'); ?> :<label class="customer_previous_due text-red" style="font-size: 18px;">0.00</label></lable>
                                 </div>
                                 <label for="deliverynote_date" class="col-sm-2 control-label"><?= $this->lang->line('deliverynote_date'); ?> <label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right datepicker"  id="deliverynote_date" name="deliverynote_date" readonly onkeyup="shift_cursor(event,'deliverynote_status')" value="<?= $deliverynote_date;?>" style="background-color:#fff;cursor:pointer;">
                                    </div>
                                    <span id="deliverynote_date_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group hide">
                                 <label for="deliverynote_status" class="col-sm-2 control-label"><?= $this->lang->line('status'); ?> <label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                       <select class="form-control select2" id="deliverynote_status" name="deliverynote_status"  style="width: 100%;" onkeyup="shift_cursor(event,'mobile')">
                                          <?php 
                                              // $received_select = ($deliverynote_status=='Final') ? 'selected' : ''; 
                                               $pending_select = ($deliverynote_status=='Delivery Note') ? 'selected' : '';
                                          ?>
                                         <!--    <option <?= $received_select; ?> value="Final">Final</option> -->
                                            <option <?= $pending_select; ?> value="Delivery Note">Delivery Note</option>
                                       </select>
                                    <span id="deliverynote_status_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group ">
                                 <input type="hidden" id="expire_date" name="expire_date" value="<?= $expire_date;?>">
                                 <label for="reference_no" class="col-sm-2 control-label"><?= (!empty($this->lang->line('reference'))) ? $this->lang->line('reference') : 'Reference'; ?> </label>
                                 <div class="col-sm-3">
                                    <input type="text" value="<?php echo  $reference_no; ?>" class="form-control " id="reference_no" name="reference_no" placeholder="" >
                   <span id="reference_no_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 <?php if($branch_tax_enabled): ?>
                                 <?php $show_customer_trn = (trim((string)$customer_trn_no) !== ''); ?>
                                 <label for="customer_trn_no" id="customer_trn_no_label" class="col-sm-2 control-label" <?= $show_customer_trn ? '' : 'style="display:none;"'; ?>>Customer TRN No.</label>
                                 <div class="col-sm-3" id="customer_trn_no_wrap" <?= $show_customer_trn ? '' : 'style="display:none;"'; ?>>
                                    <input type="text" value="<?= htmlspecialchars($customer_trn_no, ENT_QUOTES); ?>" class="form-control" id="customer_trn_no" name="customer_trn_no">
                                    <span id="customer_trn_no_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                 <?php else: ?>
                                 <input type="hidden" name="customer_trn_no" id="customer_trn_no" value="">
                                 <?php endif; ?>
                              </div>
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
                                                 <input type="text" class="form-control " placeholder="Item name/Barcode/Itemcode" autofocus id="item_search">
                                                 <span class="input-group-addon pointer text-green show_item_service" title="Click to Add New Item or Service"><i class="fa fa-plus"></i></span>
                                              </div>
                                        </div>
                                      </div>
                                      <div class="box-body">
                                        <div class="table-responsive" style="width: 100%">
                                        <table class="table table-hover table-bordered" style="width:100%" id="deliverynote_table">
                                             <thead class="custom_thead">
                                                <tr class="bg-primary" >
                                                    <th rowspan='2' style="width:10%"><?= get_item_barcode_label(); ?></th>
                                                    <th rowspan='2' style="width:15%"><?= (get_item_barcode_label_key() === 'barcode_no') ? $this->lang->line('item_name') : 'Part Name'; ?></th>
                                                    <th rowspan='2' style="width:10%;min-width: 180px;"><?= $this->lang->line('quantity'); ?></th>
                                                    <th rowspan='2' style="width:10%"><?= $this->lang->line('unit_price'); ?></th>
                                                    <th rowspan='2' style="width:10%"><?= $this->lang->line('discount'); ?></th>
                                                    <th rowspan='2' class="branch-tax-col" style="width:10%"><?= (!empty($this->lang->line('vat_amount'))) ? $this->lang->line('vat_amount') : 'Vat Amount'; ?></th>
                                                    <th rowspan='2' class="branch-tax-col" style="width:5%"><?= (!empty($this->lang->line('vat_percentage'))) ? $this->lang->line('vat_percentage') : 'Vat %'; ?></th>
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
                                          <label for="deliverynote_note" class="col-sm-4 control-label"><?= $this->lang->line('note'); ?></label>    
                                          <div class="col-sm-8">
                                             <textarea class="form-control text-left" id='deliverynote_note' name="deliverynote_note"><?= $deliverynote_note; ?></textarea>
                                            <span id="deliverynote_note_msg" style="display:none" class="text-danger"></span>
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
                                                <th class="text-right" style="font-size: 17px;"><?= $this->lang->line('round_off'); ?>
                                                <i class="hover-q " data-container="body" data-toggle="popover" data-placement="top" data-content="Go to Site Settings-> Site -> Disable the Round Off(Checkbox)." data-html="true" data-trigger="hover" data-original-title="" title="Do you wants to Disable Round Off ?">
                                                      <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                                    </i>
                                                  
                                                </th>
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

                              

                              

                              <!-- SMS Sender while saving -->
                                <?php 
                                   //Change Return
                                    $send_sms_checkbox='disabled';
                                    if($CI->is_sms_enabled()){
                                      if(!isset($deliverynote_id)){
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
                                        <input type="checkbox" <?=$send_sms_checkbox;?> class="form-control" id="send_sms" name="send_sms" > <label for="deliverynote_discount" class=" control-label"><?= $this->lang->line('send_sms_to_customer'); ?>
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
                                if(isset($deliverynote_id)){
                                  $btn_id='update';
                                  $btn_name="Update";
                                  echo '<input type="hidden" name="deliverynote_id" id="deliverynote_id" value="'.$deliverynote_id.'"/>';
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
  
 <?php $this->load->view('footer');?>
<!-- SOUND CODE -->
<?php $this->load->view('comman/code_js_sound');?>
<!-- GENERAL CODE -->
<?php $this->load->view('comman/code_js');?>

<script src="<?php echo $theme_link; ?>js/modals.js"></script>
<script src="<?php echo $theme_link; ?>js/modals/modal_item.js"></script>
 <script>var STORE_TAX_ENABLED = <?= $branch_tax_enabled ? 'true' : 'false'; ?>;</script>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<script type="text/javascript">
  var walk_in_customer_name ='<?= get_walk_in_customer_name();?>'
</script>
<script src="<?php echo $theme_link; ?>js/deliverynote/deliverynote.js"></script>  
      <script>
        $("#customer_id").on("change",function(){
          set_previous_due();
          set_customer_trn_no();
        });
        function set_previous_due(){
          $(".customer_previous_due").html($('option:selected', "#customer_id").attr('data-previous_due'));
        }
        function customer_trn_present() {
           return (($("#customer_trn_no").val() || '').toString().trim() !== '');
        }
        function set_customer_trn_no() {
           if (!STORE_TAX_ENABLED || !$("#customer_trn_no_wrap").length) {
              if (typeof final_total === 'function') { final_total(); }
              return;
           }
           var trn = ($('option:selected', "#customer_id").attr('data-tax_number') || '').toString().trim();
           $("#customer_trn_no").val(trn);
           if (trn !== '') {
              $("#customer_trn_no_label, #customer_trn_no_wrap").show();
           } else {
              $("#customer_trn_no_label, #customer_trn_no_wrap").hide();
           }
           if (typeof final_total === 'function') { final_total(); }
        }
        $(document).on('keyup change blur', '#customer_trn_no', function() {
           if (typeof final_total === 'function') { final_total(); }
        });
        set_customer_trn_no();

        var base_url=$("#base_url").val();
        $("#store_id").on("change",function(){
          var store_id=$("#store_id").val();
          $.post(base_url+"Delivery_note/get_customers_select_list",{store_id:store_id},function(result){
              $("#customer_id").html('').append(result).select2();
              $("#deliverynote_table > tbody").empty();
              set_customer_trn_no();
              calculate_tax();
          });
          $.post(base_url+"Delivery_note/get_tax_select_list",{store_id:store_id},function(result){
              $("#other_charges_tax_id").html('').append(result).select2();
              calculate_tax();
          });
        });

        /*Warehouse*/
        $("#warehouse_id").on("change",function(){
          var warehouse_id=$(this).val();
          $("#deliverynote_table > tbody").empty();
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
           var deliverynote_price=parseFloat($("#td_data_"+i+"_10").val());
           $("#td_data_"+i+"_4").val(deliverynote_price);
           /*Discounr*/
           var discount_amt=$("#td_data_"+i+"_8").val();
               discount_amt   =(isNaN(parseFloat(discount_amt)))    ? 0 : parseFloat(discount_amt);

           var amt=parseFloat(qty) * deliverynote_price;//Taxable

           var total_amt=amt-discount_amt;
           total_amt = (tax_type=='Inclusive') ? total_amt : parseFloat(total_amt) + parseFloat(tax_amount);
           
           //Set Unit cost
           $("#td_data_"+i+"_9").val('').val(to_Fixed(total_amt));
        
           final_total();
         }

        
         /* ---------- CALCULATE GST END -------------*/

        
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
             $("#round_off_amt").html(to_Fixed(subtotal_diff));
             $("#total_amt").html(to_Fixed(subtotal_round));
             $("#hidden_total_amt").val(to_Fixed(subtotal_round));
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
             $("#round_off_amt").html('0.00');
             $("#total_amt").html('0.00');
             $("#hidden_total_amt").val('0.00');
             $("#discount_to_all_amt").html('0.00');
             $("#hidden_discount_to_all_amt").val('0.00');
             $("#other_charges_amt").html('0.00');
           }
         }
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

    $(document).ready(function() {
        set_previous_due()
    });

    //Sale Items Modal Operations Start
    function show_deliverynote_item_modal(row_id){

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
      //get the deliverynote price of the item
      var tax_type = $("#tr_tax_type_"+row_id).val();
      var tax = $("#tr_tax_value_"+row_id).val(); //%
      // Branch TRN empty => do not process tax
      if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
         tax = 0;
      }
      var qty=$("#td_data_"+row_id+"_3").val();
          qty = (isNaN(qty)) ? 0 :qty;
      var deliverynote_price = parseFloat($("#td_data_"+row_id+"_10").val());
          deliverynote_price = (isNaN(deliverynote_price)) ? 0 :deliverynote_price;
          deliverynote_price = deliverynote_price * qty;

      /*Discount*/
      var item_discount_type = $("#item_discount_type_"+row_id).val();
      var item_discount_input = parseFloat($("#item_discount_input_"+row_id).val());
          item_discount_input = (isNaN(item_discount_input)) ? 0 :item_discount_input;

      //Calculate discount      
      var discount_amt=(item_discount_type=='Percentage') ? ((deliverynote_price) * item_discount_input)/100 : (item_discount_input*qty);
      deliverynote_price-=parseFloat(discount_amt);

      var tax_amount = (tax_type=='Inclusive') ? calculate_inclusive(deliverynote_price,tax) : calculate_exclusive(deliverynote_price,tax);
      if (typeof STORE_TAX_ENABLED !== 'undefined' && !STORE_TAX_ENABLED) {
         tax_amount = 0;
      }
      
      $("#td_data_"+row_id+"_8").val(to_Fixed(discount_amt));

      $("#td_data_"+row_id+"_11").val(to_Fixed(tax_amount));
    }
    //Sale Items Modal Operations End
      </script>


      <!-- UPDATE OPERATIONS -->
      <script type="text/javascript">
         <?php if(isset($deliverynote_id)){ ?> 
             $("#store_id").attr('readonly',true);
             $(document).ready(function(){
                var base_url='<?= base_url();?>';
                var deliverynote_id='<?= $deliverynote_id;?>';
                $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                $.post(base_url+"Delivery_note/return_deliverynote_list/"+deliverynote_id,{},function(result){
                  //alert(result);
                  $('#deliverynote_table tbody').append(result);
                  $("#hidden_rowcount").val(parseInt(<?=$items_count;?>)+1);
                  success.currentTime = 0;
                  success.play();
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
