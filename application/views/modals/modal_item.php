<?php 
$CI =& get_instance(); 
$barcode_row = $CI->db->select('barcode_type')->where('id', get_current_store_id())->get('db_store')->row();
$barcode_type = (!empty($barcode_row) && !empty($barcode_row->barcode_type)) ? $barcode_row->barcode_type : 'Automatic';
$dept = $CI->db->select("*")->FROM('db_department')->get()->result();
$modal_tax_defaults = get_store_item_tax_defaults();
$item_barcode_label_key = get_item_barcode_label_key();
$m_item_name_label = ($item_barcode_label_key === 'barcode_no') ? 'Item Name' : 'Part Name';
$m_item_code_label = ($item_barcode_label_key === 'barcode_no') ? 'Item Code' : 'SKU';
$m_item_code_preview = get_init_code('item');
?>
<style>
  #item-modal .pos-item-optional { display: none !important; }
  #item-modal .box-body { padding: 5px 10px; }
  #item-modal .form-group { margin-bottom: 10px; }
</style>
<div class="modal fade" id="item-modal" tabindex="-1">
  <?= form_open('#', array('class' => '', 'id' => 'item-form','enctype'=>'multipart/form-data', 'method'=>'POST')); ?>
  <input type="hidden" id="barcode_type" value="<?= $barcode_type; ?>">
  <input type="hidden" name="pos_quick_add" value="1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header header-custom">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center"><?= $this->lang->line('new_item'); ?></h4>
      </div>
      <div class="modal-body">

        <?php /* Row 1: SKU | Part No | Part Name */ ?>
        <div class="row">
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_item_code_display"><?= $m_item_code_label; ?><span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="m_item_code_display" value="<?= html_escape($m_item_code_preview); ?>" readonly style="background-color:#eee;cursor:not-allowed;">
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_custom_barcode"><?= get_item_barcode_label(); ?></label>
                <span id="m_custom_barcode_msg" class="text-danger text-right pull-right"></span>
                <input type="text" class="form-control maxlength" id="m_custom_barcode" name="m_custom_barcode">
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_item_name"><?= $m_item_name_label; ?><span class="text-danger">*</span></label>
                <span id="m_item_name_msg" class="text-danger text-right pull-right"></span>
                <input type="text" class="form-control" id="m_item_name" name="m_item_name" placeholder="">
              </div>
            </div>
          </div>
        </div>

        <?php /* Row 2: Arabic Name (2 cols) | Cost Price */ ?>
        <div class="row">
          <div class="col-md-8">
            <div class="box-body">
              <div class="form-group">
                <label for="m_item_name_arabic">Arabic Name</label>
                <span id="m_item_name_arabic_msg" class="text-danger text-right pull-right"></span>
                <input type="text" class="form-control" id="m_item_name_arabic" name="m_item_name_arabic" placeholder="">
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_price">Cost Price<span class="text-danger">*</span></label>
                <span id="m_price_msg" style="display:none" class="text-danger"></span>
                <input type="text" class="form-control only_currency" id="m_price" name="m_price">
              </div>
            </div>
          </div>
        </div>

        <?php /* Row 3: Sales Price */ ?>
        <div class="row">
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_sales_price"><?= $this->lang->line('sales_price'); ?><span class="text-danger">*</span></label>
                <span id="m_sales_price_msg" class="text-danger text-right pull-right"></span>
                <input type="text" maxlength="10" class="form-control maxlength only_currency" id="m_sales_price" name="m_sales_price" placeholder="">
              </div>
            </div>
          </div>
        </div>

        <?php /* Row 4: Tax | Tax Type | Unit */ ?>
        <div class="row">
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_tax_id"><?= $this->lang->line('tax'); ?><span class="text-danger">*</span></label>
                <span id="m_tax_id_msg" class="text-danger text-right pull-right"></span>
                <select class="form-control" id="m_tax_id" name="m_tax_id" style="width: 100%;">
                  <?= get_tax_select_list($modal_tax_defaults->default_tax_id); ?>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_tax_type"><?= $this->lang->line('tax_type'); ?><span class="text-danger">*</span></label>
                <span id="m_tax_type_msg" class="text-danger text-right pull-right"></span>
                <select class="form-control" id="m_tax_type" name="m_tax_type" style="width: 100%;">
                  <option value="Exclusive" <?= ($modal_tax_defaults->default_tax_type=='Exclusive')?'selected':''; ?>>Exclusive</option>
                  <option value="Inclusive" <?= ($modal_tax_defaults->default_tax_type=='Inclusive')?'selected':''; ?>>Inclusive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_unit_id"><?= $this->lang->line('unit'); ?><span class="text-danger">*</span></label>
                <span id="m_unit_id_msg" class="text-danger text-right pull-right"></span>
                <select class="form-control" id="m_unit_id" name="m_unit_id" style="width: 100%;">
                  <?= get_units_select_list(get_default_unit_id('PCS')); ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <?php /* Row 5: Category | Sub Category | Department */ ?>
        <div class="row">
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_category_id"><?= $this->lang->line('category'); ?></label>
                <span id="m_category_id_msg" class="text-danger text-right pull-right"></span>
                <select class="form-control" id="m_category_id" name="m_category_id" style="width: 100%;">
                  <option value="">-Select-</option>
                  <?= get_categories_select_list(); ?>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_scatid">Sub Category</label>
                <select class="form-control" name="scatid" id="m_scatid" style="width: 100%;">
                  <option value="">Select Sub Category</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="box-body">
              <div class="form-group">
                <label for="m_dptid">Department</label>
                <select class="form-control" name="dptid" id="m_dptid" style="width: 100%;">
                  <option value="">Select One</option>
                  <?php foreach($dept as $value){ ?>
                    <option value="<?php echo $value->dptid; ?>"><?php echo html_escape($value->dptName); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <?php /* Row 6: Brand (right column under Department) */ ?>
        <div class="row">
          <div class="col-md-4 col-md-offset-8">
            <div class="box-body">
              <div class="form-group">
                <label for="m_brand_id"><?= $this->lang->line('brand'); ?></label>
                <span id="m_brand_msg" class="text-danger text-right pull-right"></span>
                <select class="form-control" id="m_brand_id" name="m_brand_id" style="width: 100%;">
                  <option value="">-Select-</option>
                  <?= get_brands_select_list(); ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <?php /* Hidden / optional fields (kept for save compatibility) */ ?>
        <div class="pos-item-optional">
          <select class="form-control" id="m_item_group" name="m_item_group">
            <option value="Single" selected>Single</option>
            <option value="Variants">Variants</option>
          </select>
          <input type="text" class="form-control only_currency" id="m_purchase_price" name="m_purchase_price" value="">
          <input type="text" class="form-control only_currency" id="m_profit_margin" name="m_profit_margin" value="0">
          <input type="text" class="form-control" id="m_hsn" name="m_hsn" value="">
          <input type="text" class="form-control" id="m_sku" name="m_sku" value="">
          <input type="text" class="form-control" id="m_alert_qty" name="m_alert_qty" value="">
          <input type="text" class="form-control" id="m_seller_points" name="m_seller_points" value="">
          <textarea id="m_description" name="m_description"></textarea>
          <input type="text" class="form-control only_currency" id="m_mrp" name="m_mrp" value="">
          <select class="form-control" id="m_warehouse_id" name="m_warehouse_id">
            <?= get_warehouse_select_list(); ?>
          </select>
          <input type="text" class="form-control" id="adjustment_qty" name="adjustment_qty" value="0">
          <input type="hidden" name="clid" id="clid" value="">
          <input type="hidden" name="szid" id="szid" value="">
          <input type="hidden" name="rkid" id="rkid" value="">
          <input type="hidden" name="bnid" id="bnid" value="">
          <input type="hidden" name="bsid" id="bsid" value="">
          <input type="hidden" name="maximum_qty" id="maximum_qty" value="">
          <input type="hidden" name="minimum_qty" id="minimum_qty" value="">
          <input type="hidden" name="reorder_qty" id="reorder_qty" value="">
        </div>

      </div>
      <div class="modal-footer">
        <input type="hidden" name="existing_row_count" value="0">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary add_item">Save</button>
      </div>
    </div>
  </div>
  <?= form_close(); ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
  // Department -> Category
  $(document).on('change', '#m_dptid', function(){
    var id = $(this).val();
    var url = ($("#base_url").val() || '') + "Items/get_category_data";
    $.ajax({
      method: "POST",
      url: url,
      dataType: 'json',
      data: {id: id},
      success: function(data){
        var HTML = '<option value="">-Select-</option>';
        for (var key in data) {
          HTML += '<option value="'+data[key]['id']+'">'+data[key]['category_name']+'</option>';
        }
        $("#m_category_id").html(HTML);
        $("#m_scatid").html('<option value="">Select Sub Category</option>');
      }
    });
  });
  // Enter advances through the quick-add fields; the last field focuses Save.
  $(document).on('keydown', '#item-modal input, #item-modal select, #item-modal textarea', function(event) {
    if ((event.key !== 'Enter' && event.keyCode !== 13) || event.isComposing) return;
    if ($(this).hasClass('select2-search__field')) return;
    event.preventDefault();
    event.stopPropagation();
    var modal = $(this).closest('#item-modal');
    var fields = modal.find('.modal-body input, .modal-body select, .modal-body textarea').filter(':visible:enabled').filter(function() {
      return this.type !== 'hidden' && !this.readOnly;
    });
    var index = fields.index(this);
    var next = index + (event.shiftKey ? -1 : 1);
    if (index < 0 || next < 0) return;
    if (next < fields.length) fields.eq(next).focus();
    else modal.find('.add_item').focus();
  });
  // Category -> Sub Category
  $(document).on('change', '#m_category_id', function(){
    var id = $(this).val();
    var url = ($("#base_url").val() || '') + "Items/get_sub_category_data";
    $.ajax({
      method: "POST",
      url: url,
      dataType: 'json',
      data: {id: id},
      success: function(data){
        var HTML = '<option value="">Select Sub Category</option>';
        for (var key in data) {
          HTML += '<option value="'+data[key]['scatid']+'">'+data[key]['scatName']+'</option>';
        }
        $("#m_scatid").html(HTML);
      }
    });
  });
});
</script>
