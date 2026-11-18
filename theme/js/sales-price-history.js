      function show_item_price_history_popup(row_id) {
         var item_id = $("#tr_item_id_" + row_id).val();
         if (!item_id) {
            toastr["warning"]("Item not found!");
            return;
         }
         var customer_id = $('#customer_id').val();
         if (!customer_id) { toastr.warning('Please select a customer first.'); return; }
         var exclude_sales_id = $("#sales_id").length ? ($("#sales_id").val() || 0) : 0;
         $("#price_history_last_purchase").text('0.00');
         $("#price_history_tbody").html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
         $("#item-price-history-modal").modal('show');
         var modal = $("#item-price-history-modal");
         var historyUrl = modal.attr('data-history-url');
         if (!historyUrl) {
            $("#price_history_tbody").html('<tr><td colspan="5" class="text-center text-danger">Price history URL is missing. Please reload the page.</td></tr>');
            return;
         }
         var request = {
            item_id: item_id,
            customer_id: customer_id,
            exclude_sales_id: exclude_sales_id
         };
         var csrfName = modal.attr('data-csrf-name');
         if (csrfName) request[csrfName] = modal.attr('data-csrf-token');
         $.post(historyUrl, request, function(result) {
            var data;
            try {
               data = (typeof result === 'object') ? result : jQuery.parseJSON(result);
            } catch (e) {
               $("#price_history_tbody").html('<tr><td colspan="5" class="text-center text-danger">Failed to load</td></tr>');
               return;
            }
            if (!data || data.result !== 'success') {
               $("#price_history_tbody").html('<tr><td colspan="5" class="text-center text-danger">Failed to load</td></tr>');
               return;
            }
            $("#price_history_last_purchase").text(data.last_purchase_price || '0.00');
            if (!data.rows || !data.rows.length) {
               $("#price_history_tbody").html('<tr><td colspan="5" class="text-center">No price history found</td></tr>');
               return;
            }
            var html = '';
            for (var i = 0; i < data.rows.length; i++) {
               var r = data.rows[i];
               html += '<tr>';
               html += '<td>' + (r.invoice_date || '') + '</td>';
               html += '<td>' + (r.invoice_no || '') + '</td>';
               html += '<td>' + (r.invoice_type || '') + '</td>';
               html += '<td class="text-right">' + (r.qty || '') + '</td>';
               html += '<td class="text-right">' + (r.price || '') + '</td>';
               html += '</tr>';
            }
            $("#price_history_tbody").html(html);
         }).fail(function() {
            $("#price_history_tbody").html('<tr><td colspan="5" class="text-center text-danger">Failed to load</td></tr>');
         });
      }

