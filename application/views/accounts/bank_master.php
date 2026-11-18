<!DOCTYPE html>
<html>
   <head>
      <!-- TABLES CSS CODE -->
      <?php $this->load->view('comman/code_css.php');?>
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php $this->load->view('sidebar');?>
         <?php
            if(!isset($account_number)){
                 $account_number = $branch_name = $iban_number = $bank_details = $opening_balance = $store_id = "";
            }
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?=$page_title;?>
                  <small><?= (isset($q_id)) ? 'Update Bank' : 'Add Bank'; ?></small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo $base_url; ?>bank_master"><?= (!empty($this->lang->line('bank_master_list'))) ? $this->lang->line('bank_master_list') : 'Bank Master List'; ?></a></li>
                  <li class="active"><?=$page_title;?></li>
               </ol>
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        <div class="box-header with-border">
                           <h3 class="box-title">Please Enter Valid Data</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="bank-master-form" onkeypress="return event.keyCode != 13;">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <!-- Store Code -->
                              <?php
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              ?>
                              <!-- Store Code end -->

                              <div class="form-group">
                                 <label for="account_number" class="col-sm-2 control-label"><?= (!empty($this->lang->line('account_number'))) ? $this->lang->line('account_number') : 'Account Number'; ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="account_number" name="account_number" placeholder="" onkeyup="shift_cursor(event,'branch_name')" value="<?php print $account_number; ?>" autofocus >
                                    <span id="account_number_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                              <div class="form-group">
                                 <label for="branch_name" class="col-sm-2 control-label"><?= (!empty($this->lang->line('branch_name'))) ? $this->lang->line('branch_name') : 'Branch Name'; ?></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="branch_name" name="branch_name" placeholder="" onkeyup="shift_cursor(event,'iban_number')" value="<?php print $branch_name; ?>" >
                                    <span id="branch_name_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                              <div class="form-group">
                                 <label for="iban_number" class="col-sm-2 control-label"><?= (!empty($this->lang->line('iban_number'))) ? $this->lang->line('iban_number') : 'IBAN Number'; ?></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="iban_number" name="iban_number" placeholder="" onkeyup="shift_cursor(event,'opening_balance')" value="<?php print $iban_number; ?>" >
                                    <span id="iban_number_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                              <div class="form-group">
                                 <label for="opening_balance" class="col-sm-2 control-label"><?= (!empty($this->lang->line('opening_balance'))) ? $this->lang->line('opening_balance') : 'Opening Balance'; ?></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm only_currency text-right" id="opening_balance" name="opening_balance" placeholder="" onkeyup="shift_cursor(event,'bank_details')" value="<?php print $opening_balance; ?>" >
                                    <span id="opening_balance_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                              <div class="form-group">
                                 <label for="bank_details" class="col-sm-2 control-label"><?= (!empty($this->lang->line('bank_details'))) ? $this->lang->line('bank_details') : 'Bank Details'; ?></label>
                                 <div class="col-sm-4">
                                    <textarea class="form-control" id="bank_details" name="bank_details" placeholder=""><?php print $bank_details; ?></textarea>
                                    <span id="bank_details_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>

                           </div>
                           <!-- /.box-footer -->
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <?php
                                    if(isset($q_id)){
                                         $btn_name="Update";
                                         $btn_id="update";
                                        ?>
                                 <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id;?>"/>
                                 <?php
                                    }
                                    else{
                                        $btn_name="Save";
                                        $btn_id="save";
                                    }
                                 ?>
                                 <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="<?php echo $btn_id;?>" class=" btn btn-block btn-success" title="Save Data"><?php echo $btn_name;?></button>
                                 </div>
                                 <div class="col-sm-3">
                                    <a href="<?=base_url('bank_master');?>">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go to List">Close</button>
                                    </a>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                        </form>
                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
               </div>
               <!-- /.row -->
            </section>
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <?php $this->load->view('footer.php');?>
         <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
         <div class="control-sidebar-bg"></div>
      </div>
      <!-- ./wrapper -->
      <!-- SOUND CODE -->
      <?php $this->load->view('comman/code_js_sound.php');?>
      <!-- TABLES CODE -->
      <?php $this->load->view('comman/code_js.php');?>
      <script src="<?php echo $theme_link; ?>js/bank_master.js"></script>
      <script type="text/javascript">
        <?php if(isset($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
      </script>
      <!-- Make sidebar menu highlighter/selector -->
      <script>$(".bank_master_list-active-li, .bank_master-active-li").addClass("active");</script>
   </body>
</html>
