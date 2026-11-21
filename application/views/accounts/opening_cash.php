<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
<style>
  .opening-cash-form .form-group { margin-bottom: 10px; }
  .opening-cash-form .btn-save-col { padding-top: 25px; }
  #opening_cash_table .oc-action { cursor: pointer; font-size: 16px; margin: 0 6px; }
  #opening_cash_table td, #opening_cash_table th { vertical-align: middle; }
</style>
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
        <?= $page_title?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $this->lang->line('accounts_list'); ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <?php $this->load->view('comman/code_flashdata');?>
        <!-- ********** ALERT MESSAGE END******* -->

        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header ">
              <div class="row opening-cash-form">
                <div class="col-md-12">
                  <input type="hidden" id="opening_cash_id" value="">

                  <div class="col-md-3">
                    <div class="form-group">
                       <label for="opening_date"><?= $this->lang->line('date'); ?> <span class="text-danger">*</span></label>
                       <div class="input-group date">
                         <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                         </div>
                         <input type="text" class="form-control pull-right datepicker" id="opening_date" name="opening_date" autocomplete="off" value="<?= show_date(date('Y-m-d')); ?>">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                       <label for="opening_amount">Amount <span class="text-danger">*</span></label>
                       <input type="text" class="form-control text-right only_currency" id="opening_amount" name="opening_amount" autocomplete="off" placeholder="0.00">
                    </div>
                  </div>

                  <div class="col-md-4 btn-save-col">
                    <button type="button" class="btn btn-success" id="opening_cash_save" style="min-width:110px;"><i class="fa fa-save"></i> <span id="opening_cash_save_text">Save</span></button>
                    <button type="button" class="btn btn-default" id="opening_cash_cancel" style="display:none;min-width:90px;">Cancel</button>
                  </div>

                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="opening_cash_table" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray ">
                <tr>
                  <th><?= $this->lang->line('date'); ?></th>
                  <th>Time</th>
                  <th>Type</th>
                  <th class="text-right">Amount</th>
                  <th><?= $this->lang->line('created_by'); ?></th>
                  <th class="text-center"><?= $this->lang->line('action'); ?></th>
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
(function () {
  var base_url = $("#base_url").val();
  var today_value = $("#opening_date").val();
  var table = null;

  function notify(type, msg) {
    if (typeof toastr !== 'undefined') { toastr[type](msg); } else { alert(msg); }
  }
  function esc(text) {
    return $('<div>').text(text === null || text === undefined ? '' : text).html();
  }

  // Draw the list (plain table: no Copy / Excel / PDF / Print / CSV / Columns buttons)
  function load_list() {
    $.post(base_url + 'accounts/opening_cash_list', {}, function (result) {
      var rows = result;
      if (typeof result === 'string') {
        try { rows = JSON.parse(result); } catch (e) { rows = []; }
      }
      if (table) { table.destroy(); table = null; }
      var html = '';
      $.each(rows || [], function (i, r) {
        html += '<tr>'
          + '<td>' + esc(r.date) + '</td>'
          + '<td>' + esc(r.time) + '</td>'
          + '<td>' + esc(r.type) + '</td>'
          + '<td class="text-right">' + esc(r.amount) + '</td>'
          + '<td>' + esc(r.created_by) + '</td>'
          + '<td class="text-center">'
          +   '<i class="fa fa-pencil text-blue oc-action oc-edit" title="Edit" data-id="' + r.id + '" data-date="' + esc(r.date) + '" data-amount="' + esc(r.amount_raw) + '"></i>'
          +   '<i class="fa fa-trash text-red oc-action oc-delete" title="Delete" data-id="' + r.id + '"></i>'
          + '</td>'
          + '</tr>';
      });
      $("#opening_cash_table tbody").html(html);
      table = $("#opening_cash_table").DataTable({
        paging: true,
        lengthChange: true,
        searching: false,
        ordering: false,
        info: true,
        autoWidth: false,
        destroy: true,
        language: { emptyTable: 'No Records Found' }
      });
    });
  }

  function reset_form() {
    $("#opening_cash_id").val('');
    $("#opening_amount").val('');
    $("#opening_date").val(today_value);
    if ($.fn.datepicker) { $("#opening_date").datepicker('update', today_value); }
    $("#opening_cash_save_text").text('Save');
    $("#opening_cash_cancel").hide();
  }

  $("#opening_cash_save").on('click', function () {
    var date = $.trim($("#opening_date").val());
    var amount = $.trim($("#opening_amount").val()).replace(/,/g, '');
    if (date === '') {
      notify('warning', 'Select Date!');
      $("#opening_date").focus();
      return;
    }
    if (amount === '' || isNaN(parseFloat(amount)) || parseFloat(amount) < 0) {
      notify('warning', 'Enter Amount!');
      $("#opening_amount").focus();
      return;
    }
    var btn = $(this).attr('disabled', true);
    $.post(base_url + 'accounts/opening_cash_save', { id: $("#opening_cash_id").val(), opening_date: date, amount: amount }, function (result) {
      result = $.trim(result);
      if (result === 'success') {
        notify('success', $("#opening_cash_id").val() ? 'Opening Cash Updated Successfully!' : 'Opening Cash Saved Successfully!');
        reset_form();
        load_list();
      } else if (result === 'failed') {
        notify('error', 'Sorry! Failed to save record. Try again!');
      } else {
        notify('error', result);
      }
    }).fail(function () {
      notify('error', 'Sorry! Failed to save record. Try again!');
    }).always(function () {
      btn.attr('disabled', false);
    });
  });

  // Edit: load the row into the form above
  $(document).on('click', '.oc-edit', function () {
    $("#opening_cash_id").val($(this).data('id'));
    var d = String($(this).attr('data-date'));
    $("#opening_date").val(d);
    if ($.fn.datepicker) { $("#opening_date").datepicker('update', d); }
    $("#opening_amount").val($(this).attr('data-amount')).focus();
    $("#opening_cash_save_text").text('Update');
    $("#opening_cash_cancel").show();
    $('html, body').animate({ scrollTop: 0 }, 200);
  });

  $("#opening_cash_cancel").on('click', reset_form);

  $(document).on('click', '.oc-delete', function () {
    var id = $(this).data('id');
    if (!confirm('Do you want to delete this record?')) {
      return;
    }
    $.post(base_url + 'accounts/opening_cash_delete', { id: id }, function (result) {
      result = $.trim(result);
      if (result === 'success') {
        notify('success', 'Record Deleted Successfully!');
        if (String($("#opening_cash_id").val()) === String(id)) { reset_form(); }
        load_list();
      } else {
        notify('error', 'Sorry! Failed to delete record. Try again!');
      }
    }).fail(function () {
      notify('error', 'Sorry! Failed to delete record. Try again!');
    });
  });

  load_list();
})();
</script>

<!-- Make sidebar menu hughlighter/selector -->
<script>$(".opening_cash-active-li").addClass("active");</script>
</body>
</html>
