<!DOCTYPE html>
<html>
   
   <head>
  <!-- TABLES CSS CODE -->
  <?php $this->load->view('comman/code_css.php');?>
  <!-- </copy> -->  
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  </head>

   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php $this->load->view('sidebar');?>
          <?php
              if(!isset($q_id)){
                
                $store_name=$logo=$currency_id=$currency_placement=$timezone=
                $date_format=$time_format=
                $round_off='';
                $allow_negative_stock=0;
                $pdf_part_number_popup=1;
                $mobile=$phone=$email=$country=$state=$city=
                $postcode=$address=$address_arabic=$gst_no=$vat_no=
                $store_website=$location_url=$whatsapp_number=$pan_no=$cr_no=$bank_details=$store_logo=$qr_image='';
                $store_name_arabic='';
                $bank_name=$account_name=$account_number=$iban_number=$routing_number='';
                $department_init=$subcategory_init='';
                $number_to_words=$number_to_words_pos='Yes';

                $decimals=2;
                $qty_decimals=2;
                $invoice_terms = '';
                $invoice_declaration = 'We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct';
                
               
                
              }
              if(!isset($invoice_declaration)){
                $invoice_declaration = '';
              }
              if(!isset($cr_no)){
                $cr_no = '';
              }
              if(!isset($store_name_arabic)){ $store_name_arabic = ''; }
              if(!isset($store_last_name_arabic)){ $store_last_name_arabic = ''; }
              if(!isset($address_arabic)){ $address_arabic = ''; }
              if(!isset($bank_name)){ $bank_name = ''; }
              if(!isset($account_name)){ $account_name = ''; }
              if(!isset($account_number)){ $account_number = ''; }
              if(!isset($iban_number)){ $iban_number = ''; }
              if(!isset($routing_number)){ $routing_number = ''; }
              if(!isset($location_url)){ $location_url = ''; }
              if(!isset($whatsapp_number)){ $whatsapp_number = ''; }
              if(!isset($show_website_qr)){ $show_website_qr = 1; }
              if(!isset($show_location_qr)){ $show_location_qr = 1; }
              if(!isset($show_whatsapp_qr)){ $show_whatsapp_qr = 1; }
              if(!isset($show_invoice_qr)){ $show_invoice_qr = 1; }
              if(!isset($show_zatca_qr)){ $show_zatca_qr = 1; }
              if(empty($branch_first_name) && empty($branch_last_name)){
                $branch_name_parts = preg_split('/\s+/u', trim((string)$store_name), 3);
                $branch_first_name = implode(' ', array_slice($branch_name_parts, 0, 2));
                $branch_last_name = isset($branch_name_parts[2]) ? $branch_name_parts[2] : '';
              }
          ?>

         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?= $page_title; ?>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li class="active"><?= $page_title; ?></li>
               </ol>
            </section>
            
            <!-- Main content -->
  <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'store-form', 'enctype'=>'multipart/form-data', 'method'=>'POST'));?>
            <section class="content">
               <div class="row">
                  <!-- ********** ALERT MESSAGE START******* -->
                <?php $this->load->view('comman/code_flashdata');?>
                  <!-- ********** ALERT MESSAGE END******* -->

                  <div class="col-md-12">
                     <!-- Custom Tabs -->
                     <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                           <li class="active"><a href="#tab_4" id='tab_4_btn' data-toggle="tab">Branch</a></li>
                           <li><a href="#tab_1" id='tab_1_btn' data-toggle="tab"><?= $this->lang->line('system'); ?></a></li>
                           <?php if(!is_user()){?>
                           <li><a href="#tab_2" id='tab_2_btn' data-toggle="tab"><?= $this->lang->line('sales'); ?></a></li>
                           <li><a href="#tab_3" id='tab_3_btn' data-toggle="tab"><?= $this->lang->line('prefixes'); ?></a></li>
                         <?php }?>
                           
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="tab_4">
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                    <!-- form start -->
                                       <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                   <label for="store_code" class="col-sm-4 control-label">Branch Code<label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="store_code" name="store_code" readonly=""  placeholder="" onkeyup="shift_cursor(event,'mobile')" value="<?php print $store_code; ?>" >
                                                      <span id="store_code_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="branch_first_name" class="col-sm-4 control-label">Branch First Name<label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="branch_first_name" name="branch_first_name" placeholder="" onkeyup="shift_cursor(event,'branch_last_name')" value="<?= html_escape($branch_first_name); ?>" >
                                                      <span id="branch_first_name_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="branch_last_name" class="col-sm-4 control-label">Branch Last Name<label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="branch_last_name" name="branch_last_name" placeholder="" onkeyup="shift_cursor(event,'store_name_arabic')" value="<?= html_escape($branch_last_name); ?>" >
                                                      <span id="branch_last_name_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="store_name_arabic" class="col-sm-4 control-label">Store Name (Arabic)</label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" dir="rtl" id="store_name_arabic" name="store_name_arabic" placeholder="اسم الشركة بالعربي" onkeyup="shift_cursor(event,'store_last_name_arabic')" value="<?= html_escape($store_name_arabic); ?>" >
                                                      <span id="store_name_arabic_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="store_last_name_arabic" class="col-sm-4 control-label">Last Name (Arabic)</label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" dir="rtl" id="store_last_name_arabic" name="store_last_name_arabic" placeholder="" onkeyup="shift_cursor(event,'cr_no')" value="<?= html_escape(html_entity_decode((string)$store_last_name_arabic, ENT_QUOTES, 'UTF-8')); ?>">
                                                      <span id="store_last_name_arabic_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                 <div class="form-group">
                                                    <label for="cr_no" class="col-sm-4 control-label">CR No.</label>
                                                    <div class="col-sm-8">
                                                       <input type="text" class="form-control" id="cr_no" name="cr_no" placeholder="" onkeyup="shift_cursor(event,'mobile')" value="<?= html_escape($cr_no); ?>" >
                                                       <span id="cr_no_msg" style="display:none" class="text-danger"></span>
                                                    </div>
                                                 </div>
                                                <div class="form-group">
                                                   <label for="mobile" class="col-sm-4 control-label"><?= $this->lang->line('mobile'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control multiple_mobile_numbers" id="mobile" name="mobile" placeholder="+971500000000,+971511111111" title="Separate multiple mobile numbers with a comma" value="<?php print $mobile; ?>" onkeyup="shift_cursor(event,'email')" >
                                                      <span id="mobile_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="email" class="col-sm-4 control-label"><?= $this->lang->line('email'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="email" name="email" placeholder="" value="<?php print $email; ?>" onkeyup="shift_cursor(event,'phone')">
                                                      <span id="email_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="phone" class="col-sm-4 control-label"><?= $this->lang->line('phone'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_char_no_space" id="phone" name="phone" placeholder="" value="<?php print $phone; ?>" onkeyup="shift_cursor(event,'vat_no')" >
                                                      <span id="phone_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                <?php if(vat_number()){ ?>
                                                <div class="form-group">
                                                   <label for="vat_no" class="col-sm-4 control-label"><?= $this->lang->line('vat_number'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="vat_no" name="vat_no" placeholder="" value="<?php print $vat_no; ?>" onkeyup="shift_cursor(event,'store_website')">
                                                      <span id="vat_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php } ?>
                                                <div class="form-group">
                                                   <label for="store_website" class="col-sm-4 control-label">Website</label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="store_website" name="store_website" placeholder="" value="<?php print $store_website; ?>" onkeyup="shift_cursor(event,'location_url')">
                                                      <span id="website_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="location_url" class="col-sm-4 control-label">Location URL</label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="location_url" name="location_url" placeholder="https://www.google.com/maps/place/..." value="<?php print html_escape($location_url); ?>" onkeyup="shift_cursor(event,'whatsapp_number')">
                                                      <span class="help-block" style="margin-bottom:0;">Google Maps place/share URL — used for Location QR on invoice PDF</span>
                                                      <span id="location_url_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="whatsapp_number" class="col-sm-4 control-label">WhatsApp Number</label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" placeholder="971555164774" value="<?php print html_escape($whatsapp_number); ?>" onkeyup="shift_cursor(event,'country')">
                                                      <span class="help-block" style="margin-bottom:0;">Country code + number (digits only preferred). QR generates on Save.</span>
                                                      <span id="whatsapp_number_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php 
                                                if(!empty($qr_image) && file_exists(FCPATH.$qr_image)){
                                                  $qr_logo = base_url($qr_image).'?v='.filemtime(FCPATH.$qr_image);
                                                }
                                                else{
                                                  $qr_logo = base_url('uploads/no_logo/nologo.png');
                                                }
                                                ?>
                                                <div class="form-group">
                                                   <div class="col-sm-8 col-sm-offset-4">
                                                      <img class='img-responsive' style='border:3px solid #d2d6de; max-width:220px;' src="<?=$qr_logo;?>" alt="WhatsApp QR">
                                                   </div>
                                                </div>
                                                 <!-- Invoice QR Codes Visibility Controls -->
                                                 <div class="form-group">
                                                    <label class="col-sm-4 control-label">Invoice QR Codes</label>
                                                    <div class="col-sm-8" style="padding-top: 7px;">
                                                       <div class="checkbox" style="margin-top: 0; margin-bottom: 5px;">
                                                          <label>
                                                             <input type="checkbox" name="show_website_qr" value="1" <?= (!empty($show_website_qr)) ? "checked" : ""; ?>>
                                                             <strong>Website QR Code</strong>
                                                          </label>
                                                       </div>
                                                       <div class="checkbox" style="margin-top: 0; margin-bottom: 5px;">
                                                          <label>
                                                             <input type="checkbox" name="show_location_qr" value="1" <?= (!empty($show_location_qr)) ? "checked" : ""; ?>>
                                                             <strong>Location QR Code</strong>
                                                          </label>
                                                       </div>
                                                       <div class="checkbox" style="margin-top: 0; margin-bottom: 5px;">
                                                          <label>
                                                             <input type="checkbox" name="show_whatsapp_qr" value="1" <?= (!empty($show_whatsapp_qr)) ? "checked" : ""; ?>>
                                                             <strong>WhatsApp QR Code</strong>
                                                          </label>
                                                       </div>
                                                       <div class="checkbox" style="margin-top: 0; margin-bottom: 5px;">
                                                          <label>
                                                             <input type="checkbox" name="show_invoice_qr" value="1" <?= (!empty($show_invoice_qr)) ? "checked" : ""; ?>>
                                                             <strong>Invoice QR Code</strong>
                                                          </label>
                                                       </div>
                                                       <div class="checkbox" style="margin-top: 0; margin-bottom: 5px;">
                                                          <label>
                                                             <input type="checkbox" name="show_zatca_qr" value="1" <?= (!empty($show_zatca_qr)) ? "checked" : ""; ?>>
                                                             <strong>ZATCA QR Code</strong>
                                                          </label>
                                                       </div>
                                                    </div>
                                                 </div>
                                                <!-- ########### -->
                                             </div>
                                             <div class="col-md-5">
                                                 <div class="form-group">
                                                    <label for="bank_name" class="col-sm-4 control-label">Bank Name</label>
                                                    <div class="col-sm-8">
                                                       <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="" value="<?= html_escape($bank_name); ?>" onkeyup="shift_cursor(event,'account_name')">
                                                       <span id="bank_name_msg" style="display:none" class="text-danger"></span>
                                                    </div>
                                                 </div>
                                                 <div class="form-group">
                                                    <label for="account_name" class="col-sm-4 control-label">Account Name</label>
                                                    <div class="col-sm-8">
                                                       <input type="text" class="form-control" id="account_name" name="account_name" placeholder="" value="<?= html_escape($account_name); ?>" onkeyup="shift_cursor(event,'account_number')">
                                                       <span id="account_name_msg" style="display:none" class="text-danger"></span>
                                                    </div>
                                                 </div>
                                                 <div class="form-group">
                                                    <label for="account_number" class="col-sm-4 control-label">Account Number</label>
                                                    <div class="col-sm-8">
                                                       <input type="text" class="form-control" id="account_number" name="account_number" placeholder="" value="<?= html_escape($account_number); ?>" onkeyup="shift_cursor(event,'iban_number')">
                                                       <span id="account_number_msg" style="display:none" class="text-danger"></span>
                                                    </div>
                                                 </div>
                                                 <div class="form-group">
                                                    <label for="iban_number" class="col-sm-4 control-label">IBAN Number</label>
                                                    <div class="col-sm-8">
                                                       <input type="text" class="form-control" id="iban_number" name="iban_number" placeholder="" value="<?= html_escape($iban_number); ?>" onkeyup="shift_cursor(event,'routing_number')">
                                                       <span id="iban_number_msg" style="display:none" class="text-danger"></span>
                                                    </div>
                                                 </div>
                                                 <div class="form-group">
                                                    <label for="routing_number" class="col-sm-4 control-label">Routing Number</label>
                                                    <div class="col-sm-8">
                                                       <input type="text" class="form-control" id="routing_number" name="routing_number" placeholder="" value="<?= html_escape($routing_number); ?>" onkeyup="shift_cursor(event,'country')">
                                                       <span id="routing_number_msg" style="display:none" class="text-danger"></span>
                                                    </div>
                                                 </div>
                                                <div class="form-group">
                                                   <label for="country" class="col-sm-4 control-label"><?= $this->lang->line('country'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="country" name="country" required style="width: 100%;" onkeyup="shift_cursor(event,'state')" value="<?php print $country; ?>">
                                                         <?php
                                                            $query1="select * from db_country where status=1";
                                                            $q1=$this->db->query($query1);
                                                            if($q1->num_rows($q1)>0)
                                                             {
                                                              //echo '<option value="">-Select-</option>'; 
                                                                 foreach($q1->result() as $res1)
                                                               {
                                                                 $selected = ($res1->country ==$country) ? 'selected' : '';
                                                                 echo "<option $selected value='".$res1->country."'>".$res1->country."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="country_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="state" class="col-sm-4 control-label"><?= $this->lang->line('state'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="state" name="state" required style="width: 100%;" onkeyup="shift_cursor(event,'city')">
                                                         <?php
                                                            $query2="select * from db_states where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              echo '<option value="">-Select-</option>'; 
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 $selected = ($res1->state ==$state) ? 'selected' : '';
                                                                 echo "<option $selected value='".$res1->state."'>".$res1->state."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="state_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                <div class="form-group">
                                                   <label for="city" class="col-sm-4 control-label"><?= $this->lang->line('city'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="city" name="city" placeholder="" value="<?php print $city; ?>" onkeyup="shift_cursor(event,'postcode')" >
                                                      <span id="city_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="postcode" class="col-sm-4 control-label"><?= $this->lang->line('postcode'); ?></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_char_no_space" id="postcode" name="postcode" placeholder="" value="<?php print $postcode; ?>" onkeyup="shift_cursor(event,'address')">
                                                      <span id="postcode_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="address" class="col-sm-4 control-label"><?= $this->lang->line('address'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <textarea type="text" class="form-control" id="address" name="address" placeholder="" required><?php print $address; ?></textarea>
                                                      <span id="address_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="address_arabic" class="col-sm-4 control-label">Address (Arabic)</label>
                                                   <div class="col-sm-8">
                                                      <textarea type="text" class="form-control" dir="rtl" id="address_arabic" name="address_arabic" placeholder="العنوان بالعربي"><?= html_escape($address_arabic); ?></textarea>
                                                      <span id="address_arabic_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="store_logo" class="col-sm-4 control-label">Logo</label>
                                                   <div class="col-sm-8">
                                                      <input type="file" id="store_logo" name="store_logo">
                                                      <span id="store_logo_msg" style="display:block;" class="text-danger">Max Width/Height: 1000px * 1000px & Size: 1024kb </span>
                                                   </div>
                                                </div>
                                                <?php 
                                                if(empty($store_logo)){
                                                  $logo = base_url('uploads/no_logo/nologo.png');
                                                }
                                                else{
                                                  $logo = base_url($store_logo);
                                                }
                                                ?>
                                                <div class="form-group">
                                                   <div class="col-sm-8 col-sm-offset-4">
                                                      <img class='img-responsive' style='border:3px solid #d2d6de;' src="<?=$logo;?>">
                                                   </div>
                                                </div>
                                             </div>
                                             <!-- ########### -->
                                          </div>
                                       </div>
                                       <!-- /.box-body -->
                                       <!-- /.box-footer -->
                                    
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <div class="tab-pane" id="tab_1">
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                    <!-- form start -->
                                       <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-5">
                                                
                                                <div class="form-group">
                                                   <label for="timezone" class="col-sm-4 control-label"><?= $this->lang->line('timezone'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="timezone" name="timezone"  style="width: 100%;">
                                                         <?php
                                                            $query2="select * from db_timezone where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 if((isset($timezone) && !empty($timezone)) && trim($timezone)==trim($res1->timezone)){$selected='selected';}else{$selected='';}
                                                                 echo "<option ".$selected." value='".$res1->timezone."'>".$res1->timezone."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="timezone_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="date_format" class="col-sm-4 control-label"><?= $this->lang->line('date_format'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="date_format" name="date_format"  style="width: 100%;">
                                                         <option value="dd-mm-yyyy">dd-mm-yyyy</option>
                                                         <!-- <option value="dd/mm/yyyy">dd/mm/yyyy</option> -->
                                                         <option value="mm/dd/yyyy">mm/dd/yyyy</option>
                                                      </select>
                                                      <span id="date_format_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="time_format" class="col-sm-4 control-label"><?= $this->lang->line('time_format'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="time_format" name="time_format"  style="width: 100%;">
                                                         <option value="12">12 Hours</option>
                                                         <option value="24">24 Hours</option>
                                                      </select>
                                                      <span id="time_format_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="currency" class="col-sm-4 control-label"><?= $this->lang->line('currency'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="currency" name="currency"  style="width: 100%;">
                                                         <?php
                                                            $query2="select * from db_currency where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 if((isset($currency_id) && !empty($currency_id)) && $currency_id==$res1->id){$selected='selected';}else{$selected='';}
                                                                 echo "<option ".$selected." value='".$res1->id."'>".$res1->currency_name.' '.$res1->currency_code.' ('.$res1->currency.")</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="currency_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="currency_placement" class="col-sm-4 control-label"><?= $this->lang->line('currency_symbol_placement'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="currency_placement" name="currency_placement"  style="width: 100%;">
                                                         <option value="Right">After Amount</option>
                                                         <option value="Left">Before Amount</option>
                                                      </select>
                                                      <span id="currency_placement_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="decimals" class="col-sm-4 control-label">Decimals for Amount<label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="decimals" name="decimals"  style="width: 100%;">
                                                         <option value="0">0</option>
                                                         <option value="1">1</option>
                                                         <option value="2">2</option>
                                                         <option value="3">3</option>
                                                         <option value="4">4</option>
                                                      </select>
                                                      <span id="decimals_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                <div class="form-group">
                                                   <label for="qty_decimals" class="col-sm-4 control-label"><?= $this->lang->line('qty_decimals'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="qty_decimals" name="qty_decimals"  style="width: 100%;">
                                                         <option value="0">0</option>
                                                         <option value="1">1</option>
                                                         <option value="2">2</option>
                                                         <option value="3">3</option>
                                                         <option value="4">4</option>
                                                      </select>
                                                      <span id="qty_decimals_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                
                                             </div>
                                             <!-- ########### -->
                                             <div class="col-md-5">
                                                
                                                <div class="form-group">
                                                   <label for="language_id" class="col-sm-4 control-label"><?= $this->lang->line('language'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="language_id" name="language_id"  style="width: 100%;">
                                                         <?php
                                                            $query2="select * from db_languages where status=1";
                                                            $q2=$this->db->query($query2);
                                                            if($q2->num_rows()>0)
                                                             {
                                                              
                                                              foreach($q2->result() as $res1)
                                                               {
                                                                 $selected = ($res1->id ==$language_id) ? 'selected' : '';
                                                                 echo "<option ".$selected." value='".$res1->id."'>".$res1->language."</option>";
                                                               }
                                                             }
                                                             else
                                                             {
                                                                ?>
                                                         <option value="">No Records Found</option>
                                                         <?php
                                                            }
                                                            ?>
                                                      </select>
                                                      <span id="language_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                   <label for="barcode_type" class="col-sm-4 control-label">Barcode</label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="barcode_type" name="barcode_type"  style="width: 100%;">
                                                         <option <?= (isset($barcode_type) && $barcode_type=='Automatic')?'selected':'';?> value="Automatic">Automatic</option>
                                                         <option <?= (isset($barcode_type) && $barcode_type=='Manual')?'selected':'';?> value="Manual">Manual</option>
                                                      </select>
                                                      <span id="barcode_type_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php
                                                  ensure_item_barcode_label_setting();
                                                  if(!isset($item_barcode_label) || !in_array($item_barcode_label, array('part_no','barcode_no'), true)){
                                                    $item_barcode_label = 'part_no';
                                                  }
                                                ?>
                                                <div class="form-group">
                                                   <label class="col-sm-4 control-label">Item Label</label>
                                                   <div class="col-sm-8">
                                                      <label style="font-weight:normal;margin-right:15px;">
                                                        <input type="radio" name="item_barcode_label" id="item_barcode_label_part_no" value="part_no" <?= ($item_barcode_label==='part_no')?'checked':''; ?>>
                                                        Part No
                                                      </label>
                                                      <label style="font-weight:normal;">
                                                        <input type="radio" name="item_barcode_label" id="item_barcode_label_barcode_no" value="barcode_no" <?= ($item_barcode_label==='barcode_no')?'checked':''; ?>>
                                                        Barcode No
                                                      </label>
                                                      <span class="help-block" style="margin-bottom:0;">Selected label will show in Item Master, Sales, Purchase, Returns &amp; PDFs.</span>
                                                   </div>
                                                </div>
                                                
                                                <?php 
                                               $round_off_checkbox ='';
                                               if($round_off==1){
                                                $round_off_checkbox='checked';
                                               }
                                               ?>
                                                <div class="form-group">
                                                   <label for="round_off" class="col-sm-4 control-label"><?= $this->lang->line('enable_round_off'); ?> ?</label>
                                                   <div class="col-sm-4">
                                                      <input type="checkbox" <?=$round_off_checkbox;?> class="form-control" id="round_off" name="round_off" >
                                                      <span id="round_off_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                                <div class="form-group">
                                                   <label for="allow_negative_stock" class="col-sm-4 control-label">Stock Negative Allow</label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="allow_negative_stock" name="allow_negative_stock" style="width: 100%;">
                                                         <option value="0" <?= empty($allow_negative_stock) ? 'selected' : ''; ?>>No</option>
                                                         <option value="1" <?= !empty($allow_negative_stock) ? 'selected' : ''; ?>>Yes</option>
                                                      </select>
                                                      <span id="allow_negative_stock_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php
                                                  ensure_pdf_part_number_popup_setting();
                                                  if(!isset($pdf_part_number_popup)){
                                                    $pdf_part_number_popup = 1;
                                                  }
                                                ?>
                                                <div class="form-group" id="pdf_part_number_popup_row" style="<?= ($item_barcode_label==='barcode_no') ? 'display:none;' : ''; ?>">
                                                   <label for="pdf_part_number_popup" class="col-sm-4 control-label">PDF Popup</label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="pdf_part_number_popup" name="pdf_part_number_popup" style="width: 100%;">
                                                         <option value="1" <?= ((int)$pdf_part_number_popup===1) ? 'selected' : ''; ?>>Yes</option>
                                                         <option value="0" <?= ((int)$pdf_part_number_popup===0) ? 'selected' : ''; ?>>No</option>
                                                      </select>
                                                      <span class="help-block" style="margin-bottom:0;">If Yes, With/Without <?= get_item_barcode_label(); ?> popup will show on PDF.</span>
                                                      <span id="pdf_part_number_popup_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                                <?php
                                                  ensure_paid_seal_setting();
                                                  if(!isset($paid_seal)){
                                                    $paid_seal = 0;
                                                  }
                                                ?>
                                                <div class="form-group">
                                                   <label for="paid_seal" class="col-sm-4 control-label">Paid Seal</label>
                                                   <div class="col-sm-8">
                                                      <select class="form-control select2" id="paid_seal" name="paid_seal" style="width: 100%;">
                                                         <option value="0" <?= ((int)$paid_seal===0) ? 'selected' : ''; ?>>No</option>
                                                         <option value="1" <?= ((int)$paid_seal===1) ? 'selected' : ''; ?>>Yes</option>
                                                      </select>
                                                      <span class="help-block" style="margin-bottom:0;">If Yes, PAID seal box will show with QR codes on PDF when invoice is Paid.</span>
                                                      <span id="paid_seal_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>

                                             </div>
                                          </div>
                                       </div>
                                       <!-- /.box-body -->
                                       <!-- /.box-footer -->
                                    
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <!-- /.tab-pane -->
                           <?php 
                           //Change Return
                           $change_return_checkbox ='';
                           if($change_return==1){
                            $change_return_checkbox='checked';
                           }

                           //Previous Balance Checkbox
                           $previous_balance_checkbox ='';
                           if($previous_balance_bit==1){
                            $previous_balance_checkbox='checked';
                           }
                          
                            ?>
                           <div class="tab-pane" id="tab_2">
                              <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="sales_discount" class="col-sm-3 control-label"><?= $this->lang->line('default_sales_discount'); ?></label>
                                                   <div class="col-sm-4">
                                                      <input type="text" class="form-control" id="sales_discount" name="sales_discount" placeholder="" value="<?php print store_number_format($sales_discount,0); ?>" >
                                                      <span id="sales_discount_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="sales_discount" class="col-sm-3 control-label"><?= $this->lang->line('show_paid_amount_and_change_return_in_pos'); ?></label>
                                                   <div class="col-sm-4">
                                                      <input type="checkbox" <?=$change_return_checkbox;?> class="form-control" id="change_return" name="change_return" >
                                                      <span id="change_return_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                            

                                             <div class="col-md-12">
                                             <div class="form-group">
                                                   <label for="sales_invoice_formats" class="col-sm-3 control-label"><?= $this->lang->line('sales_invoice_formats'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="sales_invoice_format_id" name="sales_invoice_format_id"  style="width: 100%;">
                                                         <option value="3">Default</option>
                                                         <option value="4">FTA Format</option>
                                                      </select>
                                                      <span id="sales_invoice_format_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>
                                             <div class="col-md-12">
                                             <div class="form-group">
                                                   <label for="pos_invoice_formats" class="col-sm-3 control-label"><?= $this->lang->line('pos_invoice_formats'); ?><label class="text-danger">*</label> </label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="pos_invoice_format_id" name="pos_invoice_format_id"  style="width: 100%;">
                                                         <option value="1">Default</option>
                                                         <option value="2">FTA Format</option>
                                                      </select>
                                                      <span id="pos_invoice_format_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>

                                              <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="pdf_format" class="col-sm-3 control-label">PDF Format</label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="pdf_format" name="pdf_format"  style="width: 100%;">
                                                         <option value="A4 Format">A4 Format</option>
                                                         <option value="A5 Format">A5 Format</option>
                                                      </select>
                                                      <span id="pdf_format_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>

                                             <?php
                                               $store_tax_defaults = get_store_item_tax_defaults(isset($q_id) ? $q_id : '');
                                               // System default for Tax Type = Inclusive
                                               if(!isset($default_tax_type) || $default_tax_type==='' || $default_tax_type===null){
                                                 $default_tax_type = 'Inclusive';
                                               }
                                               // One-time: previous system default was Exclusive — switch to Inclusive
                                               if($default_tax_type==='Exclusive' && empty($this->session->userdata('tax_type_default_set'))){
                                                 $default_tax_type = 'Inclusive';
                                                 if(!empty($q_id)){
                                                   $this->db->where('id', $q_id)->update('db_store', array('default_tax_type' => 'Inclusive'));
                                                 }
                                                 $this->session->set_userdata('tax_type_default_set', 1);
                                               }
                                               if(!isset($default_tax_id) || $default_tax_id===''){
                                                 $default_tax_id = $store_tax_defaults->default_tax_id;
                                               }
                                             ?>
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="default_tax_type" class="col-sm-3 control-label">Tax Type</label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="default_tax_type" name="default_tax_type" style="width: 100%;">
                                                         <option value="Inclusive" <?= ($default_tax_type=='Inclusive')?'selected':''; ?>>Inclusive</option>
                                                         <option value="Exclusive" <?= ($default_tax_type=='Exclusive')?'selected':''; ?>>Exclusive</option>
                                                      </select>
                                                      <span id="default_tax_type_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="default_tax_id" class="col-sm-3 control-label">Default Tax %</label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="default_tax_id" name="default_tax_id" style="width: 100%;">
                                                         <?= get_tax_select_list($default_tax_id, isset($q_id)?$q_id:''); ?>
                                                      </select>
                                                      <span id="default_tax_id_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>

                                             <input type="hidden" name="mrp_column" value="<?= (int)$mrp_column; ?>">

                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="mrp_column" class="col-sm-3 control-label"><?= $this->lang->line('show_previous_balance_on_invoice'); ?></label>
                                                   <div class="col-sm-4">
                                                      <input type="checkbox" <?=$previous_balance_checkbox;?> class="form-control" id="previous_balance_bit" name="previous_balance_bit" >
                                                      <span id="previous_balance_bit_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-12">
                                             <div class="form-group">
                                                   <label for="number_to_words" class="col-sm-3 control-label">Number to Words Format for Sales<label class="text-danger">*</label> </label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="number_to_words" name="number_to_words"  style="width: 100%;">
                                                         <option value="Yes">Yes</option>
                                                         <option value="No">No</option>
                                                      </select>
                                                      <span id="number_to_words_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>
                                              <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="number_to_words_pos" class="col-sm-3 control-label">Number to Words Format for POS<label class="text-danger">*</label> </label>
                                                   <div class="col-sm-4">
                                                      <select class="form-control select2" id="number_to_words_pos" name="number_to_words_pos" style="width: 100%;">
                                                         <option value="Yes">Yes</option>
                                                         <option value="No">No</option>
                                                      </select>
                                                      <span id="number_to_words_pos_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                              </div>
                                              <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="sales_invoice_footer_text" class="col-sm-3 control-label"><?= $this->lang->line('sales_invoice_footer_text'); ?></label>
                                                   <div class="col-sm-9">
                                                      <textarea class="form-control" id="sales_invoice_footer_text" name="sales_invoice_footer_text"><?= $sales_invoice_footer_text;?></textarea>
                                                      <span id="sales_invoice_footer_text_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   <label for="invoice_declaration" class="col-sm-3 control-label">Invoice Declaration</label>
                                                   <div class="col-sm-9">
                                                      <textarea class="form-control" id="invoice_declaration" name="invoice_declaration" rows="3" placeholder="Declaration text shown below Thank You on invoice PDF"><?= htmlspecialchars((string)$invoice_declaration, ENT_QUOTES, 'UTF-8');?></textarea>
                                                      <span id="invoice_declaration_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-12">
                                                <div class="form-group">
                                                   
                                                     
                                                   

                                                   <label for="invoice_terms" class="col-sm-3 control-label"><?= $this->lang->line('invoiceTermsAndConditions')?></label>
                                                   <div class="col-sm-9">

                                                      <label>
                                                       <input type="radio" name="t_and_c_status" id="t_and_c_status_show" value="1" checked="">
                                                       <?= $this->lang->line('show_on_invoice')?> 
                                                     </label>
                                                   
                                                     <label for="options">
                                                       <input type="radio" name="t_and_c_status" id="t_and_c_status_hide" value="0">
                                                       <?= $this->lang->line('hide_on_invoice')?>
                                                     </label>

                                                     <br>
                                                     <label>
                                                       <input type="radio" name="t_and_c_status_pos" id="t_and_c_status_show_pos" value="1" checked="">
                                                       <?= $this->lang->line('show_on_pos_invoice')?> 
                                                     </label>
                                                   
                                                     <label for="options">
                                                       <input type="radio" name="t_and_c_status_pos" id="t_and_c_status_hide_pos" value="0">
                                                       <?= $this->lang->line('hide_on_pos_invoice')?>
                                                     </label>

                                                     <br/>
                                                     <br/>
                                                      <textarea id="invoice_terms" name="invoice_terms" class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; border: 1px solid #dddddd; padding: 10px;"><?= $invoice_terms;?></textarea>
                                                   </div>
                                                </div>
                                             </div>

                                             


                                          </div>
                                       </div>
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <!-- /.tab-pane -->
                           <div class="tab-pane" id="tab_3">
                             <div class="row">
                                 <!-- right column -->
                                 <div class="col-md-12">
                                       <div class="box-body">
                                          <div class="row">
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="department_init" class="col-sm-4 control-label">Department<label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="department_init" name="department_init" placeholder="" value="<?php print $department_init; ?>" >
                                                      <span id="department_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="category_init" class="col-sm-4 control-label"><?= $this->lang->line('category'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="category_init" name="category_init" placeholder="" value="<?php print $category_init; ?>" >
                                                      <span id="category_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="subcategory_init" class="col-sm-4 control-label">SubCategory<label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="subcategory_init" name="subcategory_init" placeholder="" value="<?php print $subcategory_init; ?>" >
                                                      <span id="subcategory_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="item_init" class="col-sm-4 control-label"><?= $this->lang->line('item'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control no_special_no_space" id="item_init" name="item_init" placeholder="" value="<?php print $item_init; ?>" >
                                                      <span id="item_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="supplier_init" class="col-sm-4 control-label"><?= $this->lang->line('supplier'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="supplier_init" name="supplier_init" placeholder="" value="<?php print $supplier_init; ?>" >
                                                      <span id="supplier_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_init" name="purchase_init" placeholder="" value="<?php print $purchase_init; ?>" >
                                                      <span id="purchase_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_return_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase_return'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_return_init" name="purchase_return_init" placeholder="" value="<?php print $purchase_return_init; ?>" >
                                                      <span id="purchase_return_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="customer_init" class="col-sm-4 control-label"><?= $this->lang->line('customer'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="customer_init" name="customer_init" placeholder="" value="<?php print $customer_init; ?>" >
                                                      <span id="customer_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_init" class="col-sm-4 control-label"><?= $this->lang->line('sales'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_init" name="sales_init" placeholder="" value="<?php print $sales_init; ?>" >
                                                      <span id="sales_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_return_init" class="col-sm-4 control-label"><?= $this->lang->line('sales_return'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_return_init" name="sales_return_init" placeholder="" value="<?php print $sales_return_init; ?>" >
                                                      <span id="sales_return_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="expense_init" class="col-sm-4 control-label"><?= $this->lang->line('expense'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="expense_init" name="expense_init" placeholder="" value="<?php print $expense_init; ?>" >
                                                      <span id="expense_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="accounts_init" class="col-sm-4 control-label"><?= $this->lang->line('accounts'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="accounts_init" name="accounts_init" placeholder="" value="<?php print $accounts_init; ?>" >
                                                      <span id="accounts_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="quotation_init" class="col-sm-4 control-label"><?= $this->lang->line('quotation'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="quotation_init" name="quotation_init" placeholder="" value="<?php print $quotation_init; ?>" >
                                                      <span id="quotation_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="money_transfer_init" class="col-sm-4 control-label"><?= $this->lang->line('money_transfer'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="money_transfer_init" name="money_transfer_init" placeholder="" value="<?php print $money_transfer_init; ?>" >
                                                      <span id="money_transfer_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('sales_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_payment_init" name="sales_payment_init" placeholder="" value="<?php print $sales_payment_init; ?>" >
                                                      <span id="sales_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="sales_return_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('sales_return_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="sales_return_payment_init" name="sales_return_payment_init" placeholder="" value="<?php print $sales_return_payment_init; ?>" >
                                                      <span id="sales_return_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_payment_init" name="purchase_payment_init" placeholder="" value="<?php print $purchase_payment_init; ?>" >
                                                      <span id="purchase_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="purchase_return_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('purchase_return_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="purchase_return_payment_init" name="purchase_return_payment_init" placeholder="" value="<?php print $purchase_return_payment_init; ?>" >
                                                      <span id="purchase_return_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>

                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="expense_payment_init" class="col-sm-4 control-label"><?= $this->lang->line('expense_payment'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="expense_payment_init" name="expense_payment_init" placeholder="" value="<?php print $expense_payment_init; ?>" >
                                                      <span id="expense_payment_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                   <label for="cust_advance_init" class="col-sm-4 control-label"><?= $this->lang->line('customers_advance_payments'); ?><label class="text-danger">*</label></label>
                                                   <div class="col-sm-8">
                                                      <input type="text" class="form-control" id="cust_advance_init" name="cust_advance_init" placeholder="" value="<?php print $cust_advance_init; ?>" >
                                                      <span id="cust_advance_init_msg" style="display:none" class="text-danger"></span>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                 </div>
                                 <!--/.col (right) -->
                              </div>
                              <!-- /.row -->
                           </div>
                           <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                     </div>
                     <!-- nav-tabs-custom -->
                     <div>
                        <div class="col-sm-8 col-sm-offset-2 text-center">
                           <center>
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
                                <a href="<?=base_url('dashboard');?>">
                                 <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                               </a>
                              </div>
                           </center>
                        </div>
                     </div>
                  </div>
                  <!-- /.col -->
               </div>
               <!-- /.row -->
            </section>
            <!-- /.content -->
            <?= form_close(); ?>
         </div>
         <!-- /.content-wrapper -->
         <?php $this->load->view('footer.php');?>
         <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
         <div class="control-sidebar-bg"></div>
      </div>
      <!-- ./wrapper -->
      <?php $this->load->view('comman/code_js_language.php');?>
      <!-- SOUND CODE -->
      <?php $this->load->view('comman/code_js_sound.php');?>
      <!-- TABLES CODE -->
      <?php $this->load->view('comman/code_js.php');?>

      <script type="text/javascript">
         $(document).submit(function(event) {
           event.preventDefault();
           if($("#update").length){
             $("#update").trigger('click');
           }
         });
      </script>
      <script src="<?php echo $theme_link; ?>js/store_profile.js?v=<?= filemtime(FCPATH.'theme/js/store_profile.js'); ?>"></script>
      <script src="<?php echo $theme_link; ?>js/store/store.js"></script>
     


<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo $theme_link; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

     
      <script type="text/javascript">
         $("#number_to_words").val('<?= $number_to_words;?>').select2();
         $("#number_to_words_pos").val('<?= $number_to_words_pos;?>').select2();

        <?php if(!empty($currency_placement)) {?>
         $("#currency_placement").val('<?= $currency_placement;?>').select2();
       <?php }else{ ?>
         $("#currency_placement").select2();
       <?php } ?>

       <?php if(!empty($date_format)) {?>
         $("#date_format").val('<?= $date_format;?>').select2();
       <?php }else{ ?>
         $("#date_format").select2();
       <?php } ?>

       <?php if(!empty($decimals)) {?>
         $("#decimals").val('<?= $decimals;?>').select2();
       <?php }else{ ?>
         $("#decimals").select2();
       <?php } ?>

       <?php if(!empty($qty_decimals)) {?>
         $("#qty_decimals").val('<?= $qty_decimals;?>').select2();
       <?php }else{ ?>
         $("#qty_decimals").select2();
       <?php } ?>
       
       <?php if(!empty($time_format)) {?>
         $("#time_format").val('<?= $time_format;?>').select2();
       <?php }else{ ?>
         $("#time_format").select2();
       <?php } ?>

       <?php if(!empty($sales_invoice_format_id)) {?>
         $("#sales_invoice_format_id").val('<?= $sales_invoice_format_id;?>').select2();
       <?php }else{ ?>
         $("#sales_invoice_format_id").select2();
       <?php } ?>

       <?php if(!empty($pos_invoice_format_id)) {?>
         $("#pos_invoice_format_id").val('<?= $pos_invoice_format_id;?>').select2();
       <?php }else{ ?>
         $("#pos_invoice_format_id").select2();
       <?php } ?>

       <?php if(!empty($pdf_format)) {?>
         $("#pdf_format").val('<?= $pdf_format;?>').select2();
       <?php }else{ ?>
         $("#pdf_format").select2();
       <?php } ?>

       <?php if(!empty($default_tax_type)) {?>
         $("#default_tax_type").val('<?= $default_tax_type;?>').select2();
       <?php }else{ ?>
         $("#default_tax_type").select2();
       <?php } ?>

       <?php if(!empty($default_tax_id)) {?>
         $("#default_tax_id").val('<?= $default_tax_id;?>').select2();
       <?php }else{ ?>
         $("#default_tax_id").select2();
       <?php } ?>

       <?php if($t_and_c_status==1) {?>
         $("#t_and_c_status_show").attr("checked","checked");
       <?php }else{ ?>
         $("#t_and_c_status_hide").attr("checked","checked");
       <?php } ?>

       <?php if($t_and_c_status_pos==1) {?>
         $("#t_and_c_status_show_pos").attr("checked","checked");
       <?php }else{ ?>
         $("#t_and_c_status_hide_pos").attr("checked","checked");
       <?php } ?>

      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <?php if( isset($q_id) && !empty($q_id) && get_current_store_id()==$q_id){ ?>
        <script>$(".store_profile-active-li").addClass("active");</script>
      <?php }else{?>
        <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
      <?php } ?>

      <script>
        $(function () {
          //bootstrap WYSIHTML5 - text editor
         // $("#invoice_terms").wysihtml5()
        })
      </script>

      <script>
        // Item Label = Barcode No: the Barcode No column always shows on PDFs, so the PDF Popup setting is hidden
        $(function () {
          function toggle_pdf_popup_row() {
            var is_barcode = $('#item_barcode_label_barcode_no').is(':checked');
            $('#pdf_part_number_popup_row').toggle(!is_barcode);
          }
          $('input[name="item_barcode_label"]').on('change ifChecked ifChanged', toggle_pdf_popup_row);
          toggle_pdf_popup_row();
        });
      </script>

   </body>
</html>
