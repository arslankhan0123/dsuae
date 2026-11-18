               <div class="modal fade" id="item-price-history-modal" tabindex="-1" data-history-url="<?= html_escape(site_url('sales/get_item_price_history')); ?>" data-csrf-name="<?= html_escape($this->security->get_csrf_token_name()); ?>" data-csrf-token="<?= html_escape($this->security->get_csrf_hash()); ?>">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header header-custom">
                           <button type="button" class="close" style="color:#fff;opacity:1;font-size:28px;" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                           </button>
                           <h4 class="modal-title text-center">Price History</h4>
                        </div>
                        <div class="modal-body">
                           <div class="form-group" style="margin-bottom:12px;">
                              <label style="font-weight:bold;">Last Purchase Price :</label>
                              <span id="price_history_last_purchase" style="font-weight:bold;margin-left:4px;">0.00</span>
                           </div>
                           <div class="table-responsive">
                              <table class="table table-bordered table-hover" style="margin-bottom:0;">
                                 <thead class="bg-gray">
                                    <tr>
                                       <th>Invoice Date</th>
                                       <th>Invoice No</th>
                                       <th>Invoice Type</th>
                                       <th class="text-right">Qty</th>
                                       <th class="text-right">Price</th>
                                    </tr>
                                 </thead>
                                 <tbody id="price_history_tbody">
                                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

