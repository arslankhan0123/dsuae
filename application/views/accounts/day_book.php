<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap">
<?php
  $d = $daybook;
  $ts = strtotime($d['date']);
  $date_label = ($d['is_today'] ? 'Today, ' : date('D, ', $ts)) . date('d M Y', $ts);
  if (!function_exists('daybook_amt')) {
    function daybook_amt($v) { return store_number_format((float) $v); }
    function daybook_minus($v) { return '&ndash; ' . store_number_format(abs((float) $v)); }
    function daybook_plus($v) { return '+ ' . store_number_format(abs((float) $v)); }
  }
  $dot_colors = array('#10b981', '#3b82f6', '#f59e0b', '#14b8a6', '#a855f7', '#ec4899', '#6366f1', '#ef4444');
?>
<style>
  .daybook-page { padding: 18px; }
  .daybook, .daybook input, .daybook button { font-family: 'Outfit', 'Source Sans Pro', 'Helvetica Neue', Arial, sans-serif; }
  .daybook { background: #f6f9fc; border: 1px solid #e6ecf2; border-radius: 18px; overflow: hidden; color: #0f172a; }
  .daybook * { box-sizing: border-box; }

  /* Header */
  .db-head { background: #fff; border-bottom: 1px solid #edf1f5; padding: 16px 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .db-title { display: flex; align-items: center; gap: 12px; font-size: 22px; font-weight: 600; margin: 0; }
  .db-title i { color: #1ea5e8; font-size: 22px; }
  .db-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .db-pill { position: relative; display: inline-flex; align-items: center; gap: 9px; height: 36px; padding: 0 16px; border-radius: 999px; border: 1px solid #dfe6ee; background: #fff; color: #0f172a; font-size: 14px; font-weight: 500; cursor: pointer; margin: 0; }
  .db-pill i { color: #475569; }
  .db-pill input[type=date] { position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; border: 0; }
  .db-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 36px; padding: 0 20px; border-radius: 999px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none !important; transition: all .15s ease; }
  .db-btn-outline { background: #fff; border: 1.5px solid #1ea5e8; color: #1ea5e8; }
  .db-btn-outline:hover, .db-btn-outline:focus { background: #eef8fe; color: #1ea5e8; }
  .db-btn-solid { background: #1ea5e8; border: 1.5px solid #1ea5e8; color: #fff; }
  .db-btn-solid:hover, .db-btn-solid:focus { background: #1492d3; border-color: #1492d3; color: #fff; }
  .db-btn[disabled] { opacity: .6; cursor: not-allowed; }
  .db-close { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #dfe6ee; background: #fff; color: #64748b; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; text-decoration: none !important; }
  .db-close:hover { background: #f1f5f9; color: #0f172a; }

  /* Grid */
  .db-grid { display: grid; grid-template-columns: minmax(340px, 1fr) 1fr 1fr; gap: 26px; padding: 28px; align-items: start; }
  .db-left { grid-row: span 2; }
  .db-card { background: #fff; border: 1px solid #eef2f6; border-radius: 16px; box-shadow: 0 6px 22px rgba(15, 23, 42, .05); overflow: hidden; }
  .db-card-body { padding: 22px 20px 20px; }
  .db-card-title { display: flex; align-items: center; gap: 12px; font-size: 16px; font-weight: 600; margin: 0 0 18px; color: #0f172a; }
  .db-ico { width: 28px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; }
  .db-ico.blue { background: #eaf2ff; color: #2563eb; }
  .db-ico.orange { background: #fff3e0; color: #ea8a0c; }
  .db-ico.purple { background: #f5ebff; color: #9333ea; }

  /* Summary rows */
  .db-row { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; font-size: 14px; color: #334155; }
  .db-row .lbl { display: flex; align-items: center; gap: 11px; }
  .db-row .lbl:before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--dot, #94a3b8); flex: 0 0 8px; }
  .db-row .val { font-weight: 500; color: #0f172a; white-space: nowrap; }
  .db-row.strong .lbl { color: #0f172a; font-weight: 500; }
  .db-row.strong .val { font-weight: 600; }
  .db-total { display: flex; align-items: center; justify-content: space-between; margin-top: 16px; padding: 13px 14px; border-radius: 12px; font-size: 14px; font-weight: 500; }
  .db-total .val { font-size: 19px; font-weight: 700; white-space: nowrap; }
  .db-total.blue { background: #eaf2ff; color: #1e40af; }
  .db-total.blue .val { color: #1d4ed8; }
  .db-total.orange { background: #fff7ed; color: #c2410c; }
  .db-total.orange .val { color: #c2410c; }
  .db-total.red { background: #fef2f2; border: 1px solid #fecdd3; color: #e11d48; }
  .db-total.green { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }
  .db-total.neutral { background: #f1f5f9; border: 1px solid #e2e8f0; color: #334155; }

  /* Denomination card */
  .db-counted { background: #1ea5e8; color: #fff; padding: 20px 20px 22px; }
  .db-counted-top { display: flex; justify-content: space-between; font-size: 13.5px; opacity: .95; }
  .db-counted-num { font-size: 38px; font-weight: 700; line-height: 1.15; margin-top: 8px; letter-spacing: .5px; }
  .db-den-title { font-size: 15px; font-weight: 600; margin: 0 0 12px; }
  .db-den-cols { display: flex; gap: 18px; }
  .db-den-col { flex: 1 1 0; min-width: 0; }
  .db-den-head, .db-den-row { display: grid; grid-template-columns: minmax(34px, 1fr) 42px minmax(52px, 1.2fr); align-items: center; column-gap: 4px; }
  .db-den-head { font-size: 9.5px; letter-spacing: .6px; color: #94a3b8; font-weight: 600; text-transform: uppercase; padding-bottom: 8px; border-bottom: 1px solid #eef2f6; }
  .db-den-head span:nth-child(2) { text-align: center; }
  .db-den-head span:nth-child(3) { text-align: right; }
  .db-den-row { height: 47px; border-bottom: 1px solid #f1f4f8; font-size: 14px; }
  .db-den-row .den { font-weight: 600; color: #0f172a; }
  .db-den-row .sub { text-align: right; color: #334155; white-space: nowrap; }
  .db-den-row .sub.zero { color: #94a3b8; }
  .db-qty { width: 40px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 7px; text-align: center; font-size: 13.5px; font-weight: 500; color: #0f172a; padding: 0 2px; -moz-appearance: textfield; }
  .db-qty::-webkit-outer-spin-button, .db-qty::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
  .db-qty:focus { outline: none; border-color: #1ea5e8; background: #fff; box-shadow: 0 0 0 3px rgba(30, 165, 232, .15); }
  .db-den-sum { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; padding: 6px 8px; border-radius: 7px; background: #eaf2ff; color: #1e40af; font-size: 13.5px; }
  .db-den-sum b { font-weight: 700; }
  .db-den-actions { display: flex; gap: 12px; margin-top: 14px; }
  .db-den-actions .db-btn { flex: 1 1 0; height: 40px; border-radius: 12px; padding: 0 8px; font-size: 13.5px; }
  .db-saved-note { margin-top: 10px; font-size: 12px; color: #64748b; text-align: center; min-height: 16px; }

  @media (max-width: 1280px) {
    .db-grid { grid-template-columns: 1fr 1fr; }
    .db-left { grid-row: auto; grid-column: 1 / -1; max-width: 520px; }
  }
  @media (max-width: 767px) {
    .daybook-page { padding: 10px; }
    .db-grid { grid-template-columns: 1fr; padding: 14px; gap: 16px; }
    .db-left { max-width: none; }
    .db-head { padding: 14px; }
  }
  @media print {
    .main-header, .main-sidebar, .main-footer, .db-actions, .db-den-actions, .control-sidebar-bg { display: none !important; }
    .content-wrapper { margin-left: 0 !important; background: #fff !important; }
    .daybook { border: 0; background: #fff; }
    .daybook-page { padding: 0; }
    .db-grid { grid-template-columns: 1fr 1fr 1fr; gap: 12px; padding: 12px 0; }
    .db-card { box-shadow: none; border: 1px solid #dfe6ee; }
    .db-counted, .db-total, .db-den-sum, .db-row .lbl:before { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->
  <?php $this->load->view('sidebar');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <input type="hidden" id="base_url" value="<?=$base_url;?>">
    <section class="content daybook-page">
      <div class="daybook">

        <!-- Header -->
        <div class="db-head">
          <h2 class="db-title"><i class="fa fa-book"></i> Day Book</h2>
          <div class="db-actions">
            <label class="db-pill" title="Change date">
              <i class="fa fa-calendar-o"></i> <span><?= $date_label; ?></span>
              <input type="date" id="daybook_date" value="<?= $d['date']; ?>" max="<?= date('Y-m-d'); ?>">
            </label>
            <button type="button" class="db-btn db-btn-outline" id="daybook_print"><i class="fa fa-print"></i> Print</button>
            <a class="db-btn db-btn-solid" target="_blank" href="<?= $base_url; ?>accounts/day_book_pdf?date=<?= $d['date']; ?>"><i class="fa fa-download"></i> Export PDF</a>
            <a class="db-close" href="<?= $base_url; ?>dashboard" title="Close"><i class="fa fa-times"></i></a>
          </div>
        </div>

        <div class="db-grid">

          <!-- Denomination count (user entry) -->
          <div class="db-card db-left">
            <div class="db-counted">
              <div class="db-counted-top"><span>Counted so far</span><span>Drawer denominations</span></div>
              <div class="db-counted-num" id="db_counted">0.00</div>
            </div>
            <div class="db-card-body">
              <h4 class="db-den-title">Denomination Count</h4>
              <div class="db-den-cols">
                <div class="db-den-col">
                  <div class="db-den-head"><span>Note</span><span>Qty</span><span>Subtotal</span></div>
                  <?php foreach ($d['notes'] as $n) { ?>
                    <div class="db-den-row">
                      <span class="den"><?= number_format((float) $n); ?></span>
                      <input type="number" min="0" step="1" inputmode="numeric" class="db-qty" data-group="notes" data-key="<?= $n; ?>" data-value="<?= (float) $n; ?>" value="<?= (int) $d['counts']['notes'][$n]; ?>">
                      <span class="sub">0.00</span>
                    </div>
                  <?php } ?>
                  <div class="db-den-sum"><span>Notes</span><b id="db_notes_total">0.00</b></div>
                </div>
                <div class="db-den-col">
                  <div class="db-den-head"><span>Coin</span><span>Qty</span><span>Subtotal</span></div>
                  <?php foreach ($d['coins'] as $c) { ?>
                    <div class="db-den-row">
                      <span class="den"><?= $c; ?></span>
                      <input type="number" min="0" step="1" inputmode="numeric" class="db-qty" data-group="coins" data-key="<?= $c; ?>" data-value="<?= (float) $c; ?>" value="<?= (int) $d['counts']['coins'][$c]; ?>">
                      <span class="sub">0.00</span>
                    </div>
                  <?php } ?>
                  <div class="db-den-sum"><span>Coins</span><b id="db_coins_total">0.00</b></div>
                </div>
              </div>
              <div class="db-den-actions">
                <button type="button" class="db-btn db-btn-outline" id="daybook_calc">Calculate Total</button>
                <button type="button" class="db-btn db-btn-solid" id="daybook_save">Save Cash Declaration</button>
              </div>
              <div class="db-saved-note" id="db_saved_note">
                <?php if (!empty($d['declaration'])) {
                  $dec = $d['declaration'];
                  $by = !empty($dec->updated_by) ? $dec->updated_by : $dec->created_by;
                  $on = !empty($dec->updated_date) ? $dec->updated_date : $dec->created_date;
                  echo 'Saved' . (!empty($by) ? ' by ' . html_escape($by) : '') . (!empty($on) ? ' on ' . show_date($on) : '');
                } else {
                  echo 'Not saved yet for this date';
                } ?>
              </div>
            </div>
          </div>

          <!-- Sales Summary -->
          <div class="db-card">
            <div class="db-card-body">
              <h4 class="db-card-title"><span class="db-ico blue"><i class="fa fa-line-chart"></i></span> Sales Summary</h4>
              <div class="db-row" style="--dot:#3b82f6"><span class="lbl">Gross Sales</span><span class="val"><?= daybook_amt($d['sales']['gross']); ?></span></div>
              <div class="db-row" style="--dot:#f59e0b"><span class="lbl">Sales Discount</span><span class="val"><?= daybook_minus($d['sales']['discount']); ?></span></div>
              <div class="db-row" style="--dot:#a855f7"><span class="lbl">Sales Returns</span><span class="val"><?= daybook_minus($d['sales']['returns']); ?></span></div>
              <div class="db-row strong" style="--dot:#1e3a8a"><span class="lbl">Net Sales excl. VAT</span><span class="val"><?= daybook_amt($d['sales']['net_excl']); ?></span></div>
              <div class="db-row" style="--dot:#3b82f6"><span class="lbl"><?= $d['sales']['vat_label']; ?></span><span class="val"><?= daybook_plus($d['sales']['vat']); ?></span></div>
              <div class="db-total blue"><span>Net Sales (incl. VAT)</span><span class="val"><?= daybook_amt($d['sales']['net_incl']); ?></span></div>
            </div>
          </div>

          <!-- Purchase Summary -->
          <div class="db-card">
            <div class="db-card-body">
              <h4 class="db-card-title"><span class="db-ico orange"><i class="fa fa-shopping-bag"></i></span> Purchase Summary</h4>
              <div class="db-row" style="--dot:#f59e0b"><span class="lbl">Gross Purchase</span><span class="val"><?= daybook_amt($d['purchase']['gross']); ?></span></div>
              <div class="db-row" style="--dot:#10b981"><span class="lbl">Purchase Discount</span><span class="val"><?= daybook_minus($d['purchase']['discount']); ?></span></div>
              <div class="db-row" style="--dot:#14b8a6"><span class="lbl">Purchase Returns</span><span class="val"><?= daybook_minus($d['purchase']['returns']); ?></span></div>
              <div class="db-row strong" style="--dot:#7c2d12"><span class="lbl">Net Purchase excl. VAT</span><span class="val"><?= daybook_amt($d['purchase']['net_excl']); ?></span></div>
              <div class="db-row" style="--dot:#94a3b8"><span class="lbl"><?= $d['purchase']['vat_label']; ?></span><span class="val"><?= daybook_plus($d['purchase']['vat']); ?></span></div>
              <div class="db-total orange"><span>Net Purchase (incl. VAT)</span><span class="val"><?= daybook_amt($d['purchase']['net_incl']); ?></span></div>
            </div>
          </div>

          <!-- Cash Reconciliation -->
          <div class="db-card">
            <div class="db-card-body">
              <h4 class="db-card-title"><span class="db-ico purple"><i class="fa fa-shield"></i></span> Cash Reconciliation</h4>
              <div class="db-row" style="--dot:#94a3b8"><span class="lbl">Opening Cash</span><span class="val"><?= daybook_amt($d['cash']['opening']); ?></span></div>
              <div class="db-row" style="--dot:#3b82f6"><span class="lbl">Cash Sales</span><span class="val"><?= daybook_plus($d['cash']['sales']); ?></span></div>
              <div class="db-row" style="--dot:#f59e0b"><span class="lbl">Cash Refunds</span><span class="val"><?= daybook_minus($d['cash']['refunds']); ?></span></div>
              <div class="db-row" style="--dot:#a855f7"><span class="lbl">Cash Paid Out</span><span class="val"><?= ($d['cash']['paid_out'] < 0) ? daybook_plus($d['cash']['paid_out']) : daybook_minus($d['cash']['paid_out']); ?></span></div>
              <div class="db-row strong" style="--dot:#1e3a8a"><span class="lbl">Expected Cash in Drawer</span><span class="val"><?= daybook_amt($d['cash']['expected']); ?></span></div>
              <div class="db-row strong" style="--dot:#3b82f6"><span class="lbl">Actual Cash Counted</span><span class="val" id="db_actual">0.00</span></div>
              <div class="db-total neutral" id="db_diff_box"><span id="db_diff_label">Cash Difference</span><span class="val" id="db_diff">0.00</span></div>
            </div>
          </div>

          <!-- Payment Summary -->
          <div class="db-card">
            <div class="db-card-body">
              <h4 class="db-card-title"><span class="db-ico blue"><i class="fa fa-credit-card"></i></span> Payment Summary</h4>
              <?php $pi = 0; foreach ($d['payments'] as $pay) { ?>
                <div class="db-row" style="--dot:<?= $dot_colors[$pi % count($dot_colors)]; ?>"><span class="lbl"><?= html_escape($pay['label']); ?></span><span class="val"><?= daybook_amt($pay['amount']); ?></span></div>
              <?php $pi++; } ?>
              <div class="db-row" style="--dot:#94a3b8"><span class="lbl">On Account (credit)</span><span class="val"><?= daybook_amt($d['on_account']); ?></span></div>
              <div class="db-total blue"><span>Total Received</span><span class="val"><?= daybook_amt($d['payment_total']); ?></span></div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
  <?php $this->load->view('footer.php');?>
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
  var daybook_date = <?= json_encode($d['date']); ?>;
  var expected_cash = <?= json_encode(round((float) $d['cash']['expected'], 2)); ?>;

  function fmt(n) {
    return (Math.round(n * 100) / 100).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  function notify(type, msg) {
    if (typeof toastr !== 'undefined') { toastr[type](msg); } else { alert(msg); }
  }

  // Recalculate every subtotal, the Notes / Coins totals, "Counted so far" and the cash difference
  function recalc() {
    var totals = { notes: 0, coins: 0 };
    $(".db-qty").each(function () {
      var qty = parseInt($(this).val(), 10);
      if (isNaN(qty) || qty < 0) { qty = 0; }
      var sub = qty * parseFloat($(this).data('value'));
      totals[$(this).data('group')] += sub;
      $(this).siblings('.sub').text(fmt(sub)).toggleClass('zero', sub === 0);
    });
    var counted = Math.round((totals.notes + totals.coins) * 100) / 100;
    $("#db_notes_total").text(fmt(totals.notes));
    $("#db_coins_total").text(fmt(totals.coins));
    $("#db_counted").text(fmt(counted));
    $("#db_actual").text(fmt(counted));

    var diff = Math.round((counted - expected_cash) * 100) / 100;
    var box = $("#db_diff_box").removeClass('red green neutral');
    if (diff < 0) {
      box.addClass('red');
      $("#db_diff_label").text('Cash Difference (short)');
      $("#db_diff").html('&ndash; ' + fmt(Math.abs(diff)));
    } else if (diff > 0) {
      box.addClass('green');
      $("#db_diff_label").text('Cash Difference (over)');
      $("#db_diff").text('+ ' + fmt(diff));
    } else {
      box.addClass('neutral');
      $("#db_diff_label").text('Cash Difference');
      $("#db_diff").text('0.00');
    }
    return counted;
  }

  $(document).on('input change', '.db-qty', recalc);
  $(document).on('focus', '.db-qty', function () { this.select(); });
  $("#daybook_calc").on('click', recalc);

  $("#daybook_save").on('click', function () {
    recalc();
    var counts = { notes: {}, coins: {} };
    $(".db-qty").each(function () {
      var qty = parseInt($(this).val(), 10);
      counts[$(this).data('group')][String($(this).attr('data-key'))] = (isNaN(qty) || qty < 0) ? 0 : qty;
    });
    var btn = $(this).attr('disabled', true);
    $.post(base_url + 'accounts/day_book_save', { date: daybook_date, counts: JSON.stringify(counts) }, function (result) {
      var res = result;
      if (typeof result === 'string') {
        try { res = JSON.parse(result); } catch (e) { res = { status: 'failed', message: 'Unexpected response from server.' }; }
      }
      if (res && res.status === 'success') {
        expected_cash = parseFloat(res.expected) || 0;
        recalc();
        $("#db_saved_note").text('Saved just now');
        notify('success', 'Cash declaration saved.');
      } else {
        notify('error', (res && res.message) ? res.message : 'Failed to save. Please try again.');
      }
    }).fail(function () {
      notify('error', 'Failed to save. Please try again.');
    }).always(function () {
      btn.attr('disabled', false);
    });
  });

  // Calendar pill: open the picker, reload the page for the chosen date
  $(".db-pill").on('click', function () {
    var input = document.getElementById('daybook_date');
    if (input && typeof input.showPicker === 'function') {
      try { input.showPicker(); } catch (e) {}
    }
  });
  $("#daybook_date").on('change', function () {
    if (this.value) { window.location = base_url + 'accounts/day_book?date=' + encodeURIComponent(this.value); }
  });

  $("#daybook_print").on('click', function () { window.print(); });

  recalc();
})();
</script>

<!-- Make sidebar menu hughlighter/selector -->
<script>$(".day_book-active-li").addClass("active");</script>
</body>
</html>
