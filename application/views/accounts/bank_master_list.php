<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->
  <?php $this->load->view('sidebar');?>
  <?php $CI =& get_instance(); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        <small>View/Search Banks</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>

    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <?php $this->load->view('comman/code_flashdata');?>
        <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$page_title;?></h3>
              <?php if($CI->permissions('accounts_add')) { ?>
              <div class="box-tools">
                <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>bank_master/add">
                <i class="fa fa-plus"></i> <?= (!empty($this->lang->line('add_bank_master'))) ? $this->lang->line('add_bank_master') : 'Add Bank'; ?></a>
              </div>
              <?php } ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray ">
                <tr>
                  <th class="text-center">
                    <input type="checkbox" class="group_check checkbox" >
                  </th>
                  <th><?= (!empty($this->lang->line('account_number'))) ? $this->lang->line('account_number') : 'Account Number'; ?></th>
                  <th><?= (!empty($this->lang->line('branch_name'))) ? $this->lang->line('branch_name') : 'Branch Name'; ?></th>
                  <th><?= (!empty($this->lang->line('iban_number'))) ? $this->lang->line('iban_number') : 'IBAN Number'; ?></th>
                  <th><?= (!empty($this->lang->line('bank_details'))) ? $this->lang->line('bank_details') : 'Bank Details'; ?></th>
                  <th><?= (!empty($this->lang->line('opening_balance'))) ? $this->lang->line('opening_balance') : 'Opening Balance'; ?></th>
                  <th><?= $this->lang->line('status'); ?></th>
                  <th><?= $this->lang->line('action'); ?></th>
                </tr>
                </thead>
                <tbody>
				
                </tbody>
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    <?= form_close();?>
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

<script type="text/javascript">
$(document).ready(function() {
    //datatables
   var table = $('#example2').DataTable({ 
      "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
      /* FOR EXPORT BUTTONS START*/
      dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
      buttons: {
        buttons: [
            {
                className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left',
                text: 'Delete',
                action: function ( e, dt, node, config ) {
                    multi_delete();
                }
            },
            { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [1,2,3,4,5,6]} },
            { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [1,2,3,4,5,6]} },
            { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [1,2,3,4,5,6]} },
            { extend: 'print', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [1,2,3,4,5,6]} },
            { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [1,2,3,4,5,6]} },
            { extend: 'colvis', className: 'btn bg-teal color-palette btn-flat',text:'Columns' },  
            ]
        },
        /* FOR EXPORT BUTTONS END */

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "responsive": true,
        language: {
            processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>'
        },
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('bank_master/ajax_list')?>",
            "type": "POST",
            
            complete: function (data) {
             $('.column_checkbox').iCheck({
                checkboxClass: 'icheckbox_square-orange',
                radioClass: 'iradio_square-orange',
                increaseArea: '10%' // optional
              });
             call_code();
             },

        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ 0, 7 ], //checkbox column / action column
            "orderable": false, //set not orderable
        },
        {
            "targets" :[0],
            "className": "text-center",
        },
        {
            "targets" :[5],
            "className": "text-right",
        },
        {
            "targets" :[6],
            "className": "text-center",
        }
        
        ],
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>
<script src="<?php echo $theme_link; ?>js/bank_master.js"></script>
<!-- Make sidebar menu highlighter/selector -->
<script>$(".bank_master_list-active-li, .bank_master-active-li").addClass("active");</script>
</body>
</html>
