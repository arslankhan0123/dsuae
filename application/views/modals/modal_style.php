<div class="modal fade " id="style_modal" tabindex='-1'>
  <?= form_open('#', array('class' => '', 'id' => 'style_form')); ?>
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header header-custom">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"><?= (!empty($this->lang->line('add_style'))) ? $this->lang->line('add_style') : 'Add Style'; ?></h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="box-body">
                <div class="form-group">
                  <label for="style"><?= (!empty($this->lang->line('style_name'))) ? $this->lang->line('style_name') : 'Style Name'; ?>*</label>
                  <span id="style_msg" class="text-danger text-right pull-right"></span>
                  <input type="text" class="form-control" id="style" name="style" placeholder="" >
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="box-body">
                <div class="form-group">
                  <label for="description"><?= $this->lang->line('description'); ?></label>
                  <span id="description_msg" class="text-danger text-right pull-right"></span>
                  <textarea type="text" class="form-control" id="description" name="description" placeholder="" ></textarea>
                </div>
              </div>
            </div>

          </div>
         
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary add_style">Save</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
 <?= form_close();?>
</div>
<!-- /.modal -->
