
//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}


$('#save,#update,#save_and_print,#update_and_print').on("click", function (e) {
  var base_url=$("#base_url").val();
  var this_id=this.id;
    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


   //Validate Input box or selection box should not be blank or empty
	  check_field("customer_id");
    check_field("sales_date");
    check_field("sales_status");
    check_field("count_id");
    //check_field("warehouse_id");
	/*if(!isNaN($("#amount").val()) && parseInt($("#amount").val())==0){
        toastr["error"]("You have entered Payment Amount! <br>Please Select Payment Type!");
        return;
    }*/
    
    
	if(flag==false)
	{
		toastr["error"]("You have missed Something to Fillup!");
		return;
	}

	// TASK-27: WhatsApp / Email checks before save
	var send_wa = $("#send_invoice_whatsapp").is(":checked");
	var send_em = $("#send_invoice_email").is(":checked");
	if(send_wa){
		var wa_no = ($("#whatsapp_no").val() || '').toString().replace(/[^0-9]/g, '');
		if(wa_no === ''){
			toastr["warning"]("Please enter WhatsApp No to send WhatsApp!");
			$("#whatsapp_no").focus();
			return;
		}
	}
	if(send_em){
		var em = ($("#customer_email").val() || '').toString().trim();
		if(em === ''){
			toastr["warning"]("Please enter Email Address to send Email!");
			$("#customer_email").focus();
			return;
		}
	}

	// Invoice date must not be less than current date
	var sales_date_val = ($("#sales_date").val() || '').toString().trim();
	if(sales_date_val !== ''){
		var sales_parts = sales_date_val.split('-');
		if(sales_parts.length === 3){
			var sales_dt = new Date(parseInt(sales_parts[2],10), parseInt(sales_parts[1],10)-1, parseInt(sales_parts[0],10));
			var today_inv = new Date();
			today_inv.setHours(0,0,0,0);
			sales_dt.setHours(0,0,0,0);
			if(isNaN(sales_dt.getTime()) || sales_dt.getTime() < today_inv.getTime()){
				toastr["warning"]("Invoice Date cannot be less than Current Date!");
				$("#sales_date").focus();
				return;
			}
		}
	}

	//Atleast one record must be added in sales table 
    var rowcount=document.getElementById("hidden_rowcount").value;
	var flag1=false;
	for(var n=1;n<=rowcount;n++){
		if($("#td_data_"+n+"_3").val()!=null && $("#td_data_"+n+"_3").val()!=''){
			flag1=true;
		}	
	}
	
    if(flag1==false){
    	toastr["warning"]("Please Select Item!!");
        $("#item_search").focus();
		return;
    }
    //end

	// TASK-22: Quantity cannot be 0; Sale Price must be > Cost Price (item master)
	for(var n=1;n<=rowcount;n++){
		if(!document.getElementById("tr_item_id_"+n) || !$("#tr_item_id_"+n).val()){
			continue;
		}
		var item_label = ($("#td_data_"+n+"_1").text() || '').toString().trim() || ('Row '+n);
		var qty_val = parseFloat(($("#td_data_"+n+"_3").val() || '0').toString().replace(/,/g,''));
		if(isNaN(qty_val) || qty_val <= 0){
			toastr["warning"](item_label+": Quantity cannot be 0!");
			$("#td_data_"+n+"_3").focus();
			return;
		}
		var sale_price_val = parseFloat(($("#td_data_"+n+"_10").val() || '0').toString().replace(/,/g,''));
		var cost_price_val = parseFloat(($("#tr_item_cost_price_"+n).val() || '0').toString().replace(/,/g,''));
		if(isNaN(sale_price_val)){ sale_price_val = 0; }
		if(isNaN(cost_price_val)){ cost_price_val = 0; }
		if(sale_price_val <= cost_price_val){
			toastr["warning"](item_label+": Sale Price should be greater than Cost Price ("+to_Fixed(cost_price_val)+")!");
			$("#td_data_"+n+"_10").focus();
			return;
		}
	}

    if((this_id=='save' || this_id=='save_and_print') && $('option:selected', "#customer_id").attr('data-delete_bit')==1){
      if(parseFloat($("#total_amt").text()) != ((parseFloat($("#amount").val()) || 0) + ($("#allow_tot_advance").prop("checked") ? (parseFloat($("#advance_amount").val()) || 0) : 0))){
        $("#amount").focus();
        toastr["warning"]("Walk-in Customer Should Pay Complete Amount!!");
        return;
      }

     
        if($("#invoice_type").val() !== 'Credit' && !$("#payment_type").val()){
          toastr["warning"]("Please Select Payment Type!!");
          return;
        }
     


    }
    if($("#amount").val()!=''){
        if($("#invoice_type").val() !== 'Credit' && !$("#payment_type").val()){
          toastr["warning"]("Please Select Payment Type!!");
          return;
        }
    }

    var tot_subtotal_amt=$("#subtotal_amt").text();
    var other_charges_amt=$("#other_charges_amt").text();//other_charges include tax calcualated amount
    var tot_discount_to_all_amt=$("#discount_to_all_amt").text();
    var tot_round_off_amt=$("#round_off_amt").text();
    var tot_total_amt=$("#total_amt").text();
    var coupon_discount_amt=$("#coupon_discount_amt").text();

    
    
			//if(confirm("Do You Wants to Save Record ?")){
				e.preventDefault();
				data = new FormData($('#sales-form')[0]);//form name
        /*Check XSS Code*/
        if(!xss_validation(data)){ return false; }
        
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
				$.ajax({
				type: 'POST',
				url: base_url+'sales/sales_save_and_update?command='+this_id+'&rowcount='+rowcount+'&tot_subtotal_amt='+tot_subtotal_amt+'&tot_discount_to_all_amt='+tot_discount_to_all_amt+'&tot_round_off_amt='+tot_round_off_amt+'&tot_total_amt='+tot_total_amt+"&other_charges_amt="+other_charges_amt+"&coupon_discount_amt="+coupon_discount_amt,
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
         // alert(result);return;
				result=result.split("<<<###>>>");
					if(result[0]=="success" && (this_id=='save' || this_id=='update' || this_id=='save_and_print' || this_id=='update_and_print'))
					{
						// TASK-27: after save — email toast + WhatsApp URL share
						// result: 0=success,1=id,2=sales_code,3=grand_total,4=public_pdf_url,5=whatsapp_no,6=email_status,7=email_message
						var sales_code = result[2] || '';
						var grand_total = result[3] || '';
						var public_pdf_url = result[4] || '';
						var wa_number = (result[5] || ($("#whatsapp_no").val() || '')).toString().replace(/[^0-9]/g, '');
						var email_status = result[6] || '';
						var email_message = result[7] || '';
						if(send_em){
							if(email_status === 'success'){
								toastr["success"](email_message || "Invoice URL emailed successfully.");
							} else if(email_status === 'error'){
								toastr["error"](email_message || "Invoice email failed.");
							}
						}
						if(send_wa && public_pdf_url){
							var message = "Dear Customer,\n\nYour invoice " + sales_code + " for " + ((result[8] || '') + ' ' + grand_total).trim() + " is ready.\n\nView or download your invoice here:\n" + public_pdf_url + "\n\nThank you for your business!";
							var waLink = "https://api.whatsapp.com/send?phone=" + wa_number + "&text=" + encodeURIComponent(message);
							window.open(waLink, '_blank');
						}
						// If store setting (PDF Part No popup) is true:
						// Save / Update / Save&Print / Update&Print → show PDF Options popup on invoice
						if (typeof pdf_part_number_popup_enabled !== 'undefined' && pdf_part_number_popup_enabled) {
							location.href=base_url+"sales/invoice/"+result[1]+"?auto_pdf=1";
						} else if(this_id=='save_and_print' || this_id=='update_and_print') {
							location.href=base_url+"pdf/sales/"+result[1];
						} else {
							location.href=base_url+"sales/invoice/"+result[1];
						}
          }
					else if(result[0]=="failed")
					{
					   toastr['error']("Sorry! Failed to save Record.Try again");
					}
					else
					{
						alert(result);
					}
					$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
					$(".overlay").remove();

			   }
			   });
		//}
  
});


$("#item_search").bind("paste", function(e){
    $("#item_search").autocomplete('search');
} );

$("#item_search").autocomplete({
    source: function(data, cb){
        $.ajax({
        	autoFocus:true,
            url: $("#base_url").val()+'items/get_json_items_details',
            method: 'GET',
            dataType: 'json',
            /*showHintOnFocus: true,
			autoSelect: true, 
			
			selectInitial :true,*/
			
            data: {
                name: data.term,
                store_id:$("#store_id").val(),
                warehouse_id:$("#warehouse_id").val(),
                search_for:"sales",
            },
            beforeSend: function() {
                if($("#warehouse_id").val()==''){
                  toastr['warning']("Please Select Wareshouse!");
                  $("#warehouse_id").select2('open');
                  $("#item_search").removeClass('ui-autocomplete-loading');
                  return;
                }
                $("#item_search").addClass('ui-autocomplete-loading');
            },
            success: function(res){
              //console.log(res);
                var result;
                result = [
                    {
                        //label: 'No Records Found '+data.term,
                        label: 'No Records Found ',
                        value: ''
                    }
                ];

                if (res.length) {
                    result = $.map(res, function(el){
                      // Old format (commented):
                      // qty_ = (el.service_bit!=1) ? '--[Qty:'+el.stock+'] --' : '--';
                      // pur_price = (el.purchase_price!='') ? '[Pur.Price:'+el.purchase_price+'] --' : '';
                      // label: el.item_code +qty_+ pur_price + el.label,

                      var isPart = typeof ITEM_BARCODE_IS_PART_NO !== 'undefined' && ITEM_BARCODE_IS_PART_NO;
                      var code = el.custom_barcode || el.item_code || '';
                      var name = el.label || el.value || '';
                      var price = el.sales_price !== undefined ? el.sales_price : '';
                      var stock = el.service_bit != 1 ? el.stock : '-';
                      var search_label = [(isPart ? 'Part No: ' : 'Barcode No: ') + code,
                        (isPart ? 'Part Name: ' : 'Item Name: ') + name,
                        'Price: ' + price, 'Stock: ' + stock].join(' | ');
                        return {
                            label: search_label,
                            value: '',
                            id: el.id,
                            search_code: code,
                            search_name: name,
                            search_price: price,
                            search_stock: stock,
                            item_name: el.value,
                            stock: el.stock,
                            service_bit: el.service_bit,
                           // mobile: el.mobile,
                            //customer_dob: el.customer_dob,
                            //address: el.address,
                        };
                    });
                }

                cb(result);
            }
        });
    },
        //loader start
        search: function (e, ui) {
        },
        select: function (e, ui) { 
          	 /*if(u.item.value==''){
               $("#item_search").removeClass('ui-autocomplete-loader-center');
             }
          
            if(parseInt(u.item.stock)<=0){
              toastr["warning"](u.item.stock+" Items in Stock!!");
              failed.currentTime = 0; 
              failed.play();
              return false;
            }
            var item_id =u.item.id;
            if(restrict_quantity(item_id)){
              return_row_with_data(item_id);  
            }*/
            
           
            // Only an explicit suggestion selection may add an item.
            if (!ui.item || !ui.item.id || isNaN(Number(ui.item.id))) {
              return false;
            }
            var stock = ui.item.stock;
            var item_id = ui.item.id;
            var service_bit = ui.item.service_bit;
            var allow_negative_stock = ($("#allow_negative_stock").val() == '1' || $("#allow_negative_stock").val() == 'true');
            if(!allow_negative_stock && service_bit==0 && parseFloat(stock)<=0){
              toastr["warning"](stock+" Items in Stock!!");
              failed.currentTime = 0; 
              failed.play();
              return false;
            }


            if(service_bit==1 || allow_negative_stock){
              return_row_with_data(item_id);  
            }
            else {
              if(restrict_quantity(item_id)){
                return_row_with_data(item_id);  
              }
            }
            $("#item_search").val('');
            
            
        },   
        //loader end
});


// Render the search popup as aligned columns while retaining autocomplete navigation.
var salesItemSearch = $("#item_search").data('ui-autocomplete') || $("#item_search").data('autocomplete');
if (salesItemSearch) {
  salesItemSearch.menu.element.addClass('sales-item-search-table');
  salesItemSearch._renderMenu = function(ul, items) {
    var isPart = typeof ITEM_BARCODE_IS_PART_NO !== 'undefined' && ITEM_BARCODE_IS_PART_NO;
    var header = $('<li class="sales-search-header" role="presentation"></li>');
    [isPart ? 'Part No' : 'Barcode No', isPart ? 'Part Name' : 'Item Name', 'Price', 'Stock'].forEach(function(label) {
      $('<span></span>').text(label).appendTo(header);
    });
    header.appendTo(ul);
    var widget = this;
    $.each(items, function(index, item) { widget._renderItemData(ul, item); });
  };
  salesItemSearch._renderItem = function(ul, item) {
    // jQuery UI 1.10 requires an anchor for clickable and keyboard-selectable rows.
    var row = $('<a class="sales-search-row"></a>');
    if (!item.id) {
      $('<span class="sales-search-empty"></span>').text(item.label).appendTo(row);
    } else {
      [item.search_code, item.search_name, item.search_price, item.search_stock].forEach(function(value) {
        $('<span></span>').text(value == null ? '' : value).appendTo(row);
      });
    }
    return $('<li></li>').append(row).appendTo(ul);
  };
}

function check_same_item(item_id){
  if($("#sales_table tr").length>1){
    var rowcount=$("#hidden_rowcount").val();
    for(i=0;i<=rowcount;i++){
            if($("#tr_item_id_"+i).val()==item_id){
              return i;
            }
      }//end for
  }
  return -1;
}

function return_row_with_data(item_id){
  //CHECK SAME ITEM ALREADY EXIST IN ITEMS TABLE 
  var item_check=check_same_item(item_id);
  if(item_check != -1){
    swal({
      title: "Are you sure?",
      text: "This item is already added. Do you want to add it as a new row?",
      icon: "warning",
      buttons: true,
      dangerMode: false,
    }).then((sure) => {
      if(sure){
        add_row(item_id);
      } else {
        failed.currentTime = 0;
        failed.play();
      }
    });
    return false;
  }
  //END
  add_row(item_id);
}

function add_row(item_id) {
  $("#item_search").addClass('ui-autocomplete-loader-center');
  var base_url=$("#base_url").val();
  var rowcount=$("#hidden_rowcount").val();
  var warehouse_id=$("#warehouse_id").val();
  var customer_id=$("#customer_id").val();
  $.post(base_url+"sales/return_row_with_data/"+rowcount+"/"+item_id,{customer_id:customer_id,warehouse_id:warehouse_id},function(result){
        //alert(result);
        $('#sales_table tbody').append(result);
        $("#hidden_rowcount").val(parseInt(rowcount)+1);
        success.currentTime = 0;
        success.play();
        enable_or_disable_item_discount();
        $("#item_search").removeClass('ui-autocomplete-loader-center');
        $("#item_search").removeClass('ui-autocomplete-loading');
    }); 
}
//INCREMENT ITEM
function increment_qty(rowcount){
  var service_bit =$("#service_bit_"+rowcount).val();
  var allow_negative_stock = ($("#allow_negative_stock").val() == '1' || $("#allow_negative_stock").val() == 'true');
  var flag = restrict_quantity($("#tr_item_id_"+rowcount).val(),service_bit);
  if(!flag && !allow_negative_stock){ return false;}

  var item_qty=$("#td_data_"+rowcount+"_3").val();
  var available_qty=$("#tr_available_qty_"+rowcount+"_13").val();
  if(allow_negative_stock || service_bit==1 || parseFloat(item_qty)<parseFloat(available_qty)){
    item_qty=parseFloat(item_qty)+1;
    $("#td_data_"+rowcount+"_3").val(format_qty(item_qty));
  }
  calculate_tax(rowcount);
}
//DECREMENT ITEM
function decrement_qty(rowcount){
  var service_bit =$("#service_bit_"+rowcount).val();
  var item_qty=parseFloat($("#td_data_"+rowcount+"_3").val());
  // TASK-22: Quantity cannot be 0 — minimum 1
  if(service_bit==1 || item_qty<=1){
    $("#td_data_"+rowcount+"_3").val(format_qty(1));
    calculate_tax(rowcount);
    return;
  }
  $("#td_data_"+rowcount+"_3").val(format_qty(item_qty-1));
  calculate_tax(rowcount);
}

/* TASK-22: live checks — qty > 0, sale price > cost price */
function parse_money_val(v){
  return parseFloat((v || '0').toString().replace(/,/g,''));
}
$(document).on('blur change', 'input[id^="td_data_"][id$="_3"]', function(){
  var id = this.id; // td_data_{row}_3
  var m = id.match(/^td_data_(\d+)_3$/);
  if(!m){ return; }
  var row = m[1];
  var qty = parse_money_val($(this).val());
  if(isNaN(qty) || qty <= 0){
    toastr["warning"]("Quantity cannot be 0!");
    $(this).val(format_qty(1));
    if(typeof calculate_tax === 'function'){ calculate_tax(row); }
  }
});
$(document).on('blur change', 'input[id^="td_data_"][id$="_10"]', function(){
  var id = this.id; // td_data_{row}_10
  var m = id.match(/^td_data_(\d+)_10$/);
  if(!m){ return; }
  var row = m[1];
  var sale_price = parse_money_val($(this).val());
  var cost_price = parse_money_val($("#tr_item_cost_price_"+row).val());
  if(isNaN(sale_price)){ sale_price = 0; }
  if(isNaN(cost_price)){ cost_price = 0; }
  if(sale_price <= cost_price){
    var item_label = ($("#td_data_"+row+"_1").text() || '').toString().trim() || ('Row '+row);
    toastr["warning"](item_label+": Sale Price should be greater than Cost Price ("+to_Fixed(cost_price)+")!");
    $(this).css({'background-color':'#f8d7da'});
  } else {
    $(this).css({'background-color':''});
  }
});

function update_paid_payment_total() {
  var rowcount=$("#paid_amt_tot").attr("data-rowcount");
  var tot=0;
  for(i=1;i<rowcount;i++){
    if(document.getElementById("paid_amt_"+i)){
      tot += parseFloat($("#paid_amt_"+i).html());
    }
  }
  $("#paid_amt_tot").html(to_Fixed(tot));
}
function delete_payment(payment_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#base_url").val();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"sales/delete_payment",{payment_id:payment_id},function(result){
   //alert(result);return;
   result=result;
     if(result=="success")
        { 
          toastr["success"]("Record Deleted Successfully!");
          $("#payment_row_"+payment_id).remove();
          success.currentTime = 0; 
          success.play();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
        update_paid_payment_total();
        restore_customer_list();
   });
   }//end confirmation   
  }

function restore_customer_list(){
   var base_url=$("#base_url").val();
   var customer_id = $("#customer_id").val();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post(base_url+"customers/restore_customer_list",{customer_id,customer_id},function(result){
        $("#customer_id").select2().empty();
        $("#customer_id").html(result).select2();
        $("#customer_id").trigger('change');
        
        $(".overlay").remove();
        return false;
   });
}

  //Delete Record start
function delete_sales(q_id)
{
  var base_url=$("#base_url").val();
   if(confirm("Do You Wants to Delete Record ?")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post(base_url+"sales/delete_sales",{q_id:q_id},function(result){
   //alert(result);return;
     if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
        }
        else{
           toastr["error"](result);
        }
        $(".overlay").remove();
        return false;
   });
   }//end confirmation
}
//Delete Record end
function multi_delete(){
  var base_url=$("#base_url").val();
    var this_id=this.id;
    
    if(confirm("Are you sure ?")){
      data = new FormData($('#table_form')[0]);//form name
      /*Check XSS Code*/
      if(!xss_validation(data)){ return false; }
      
      $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
      $.ajax({
      type: 'POST',
      url: base_url+'sales/multi_delete',
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      success: function(result){
        result=result;
  //alert(result);return;
        if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          success.currentTime = 0; 
            success.play();
          $('#example2').DataTable().ajax.reload();
          $(".delete_btn").hide();
          $(".group_check").prop("checked",false).iCheck('update');
        }
        else if(result=="failed")
        {
           toastr["error"]("Sorry! Failed to save Record.Try again!");
           failed.currentTime = 0; 
           failed.play();
        }
        else
        {
          toastr["error"](result);
          failed.currentTime = 0; 
            failed.play();
        }
        $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
       }
       });
  }
  //e.preventDefault
}

function pay_now(sales_id){
  var base_url=$("#base_url").val();
  $.post(base_url+'sales/show_pay_now_modal', {sales_id: sales_id}, function(result) {
    $(".pay_now_modal").html('').html(result);
    //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
    format: 'dd-mm-yyyy',
     todayHighlight: true
    });
    $('#pay_now').modal('toggle');

  });
}
function view_payments(sales_id){
  var base_url=$("#base_url").val();
  $.post(base_url+'sales/view_payments_modal', {sales_id: sales_id}, function(result) {
    $(".view_payments_modal").html('').html(result);
    $('#view_payments_modal').modal('toggle');
  });
}

function save_payment(sales_id){
  var base_url=$("#base_url").val();

    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


   //Validate Input box or selection box should not be blank or empty
    check_field("amount");
    check_field("payment_date");


    var payment_date=$("#payment_date").val();
    var amount=$("#amount").val();
    var payment_type=$("#payment_type").val();
    var payment_note=$("#payment_note").val();
    var account_id=$("#account_id").val();
    var customer_id=$("#customer_id").val();
    var cheque_number=$("#cheque_number").val();
    var cheque_period=$("#cheque_period").val();

    if(amount == 0){
      toastr["error"]("Please Enter Valid Amount!");
      return false; 
    }

    if(amount > parseFloat($("#due_amount_temp").html())){
      toastr["error"]("Entered Amount Should not be Greater than Due Amount!");
      return false;
    }

    //verify advance is checked or not
    allow_tot_advance = 'checked';
    if(!$("#allow_tot_advance").prop("checked")){
      allow_tot_advance='';
    }

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $(".payment_save").attr('disabled',true);  //Enable Save or Update button
    $.post(base_url+'sales/save_payment', {cheque_number:cheque_number,cheque_period:cheque_period,allow_tot_advance:allow_tot_advance,customer_id:customer_id,account_id:account_id,sales_id: sales_id,payment_type:payment_type,amount:amount,payment_date:payment_date,payment_note:payment_note}, function(result) {
      result=result;
  //alert(result);return;
        if(result=="success")
        {
          $('#pay_now').modal('toggle');
          toastr["success"]("Payment Recorded Successfully!");
          success.currentTime = 0; 
          success.play();
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed")
        {
           toastr["error"]("Sorry! Failed to save Record.Try again!");
           failed.currentTime = 0; 
           failed.play();
        }
        else
        {
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".payment_save").attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
    });
}

function delete_sales_payment(payment_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#base_url").val();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"sales/delete_payment",{payment_id:payment_id},function(result){
   //alert(result);return;
   result=result;
     if(result=="success")
        {
          $('#view_payments_modal').modal('toggle');
          toastr["success"]("Record Deleted Successfully!");
          success.currentTime = 0; 
          success.play();
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
   });
   }//end confirmation   
  }

  function restrict_quantity(item_id,service_bit=0) {
    var allow_negative_stock = ($("#allow_negative_stock").val() == '1' || $("#allow_negative_stock").val() == 'true');
    if(allow_negative_stock){ return true; }
  	var rowcount=$("#hidden_rowcount").val();
  	var available_qty = 0;
  	var count_item_qty = 0;
  	var selected_item_id = 0;
      for(i=1;i<=rowcount;i++){
        if(document.getElementById("tr_item_id_"+i)){
        	selected_item_id = $("#tr_item_id_"+i).val();
            if(parseInt(item_id)==parseInt(selected_item_id)){
	             available_qty = parseFloat($("#tr_available_qty_"+i+"_13").val());
	             count_item_qty += parseFloat($("#td_data_"+i+"_3").val());
          }

        }
      }//end for
      if( service_bit==0 && available_qty!=0 && count_item_qty>=available_qty){
        toastr["warning"]("Only "+available_qty+" Items in Stock!!");
        failed.currentTime = 0; 
        failed.play();
      	return false;
      }
      return true;
  }

  /*$("#warehouse_id").on("change",function(event) {
    $('#sales_table tbody').html('');
    final_total();
    if($("#warehouse_id").val()!=''){
      $("#item_search").attr({ disabled: false,});
    }
    else{
     $("#item_search").attr({ disabled: true,}); 
    }
  });*/

function get_coupon_details(){
  var input_box = $("#coupon_code");
  var coupon_code = $.trim(input_box.val());
  var customer_id = $("#customer_id").val();
  var base_url=$("#base_url").val();

  var coupon_type='';
  var coupon_value=0;
  if(coupon_code!=''){
    input_box.addClass('ui-autocomplete-loading');
    $.post(base_url+'customer_coupon/get_coupon_details', {invoice_type:'sales',coupon_code: coupon_code,customer_id:customer_id}, function(data, textStatus, xhr) {
      var json = $.parseJSON(data);
      coupon_value=json.coupon_value;
      coupon_type=json.coupon_type;

      $(".coupon_value").html(to_Fixed(coupon_value));
      $(".coupon_type").html(coupon_type);  

      $(".div1").removeClass('hide');
      
      if(json.expire_status=='Valid'){
        $(".msg_color").removeClass('alert-warning').addClass('alert-success');
      }
      else{
       $(".msg_color").removeClass('alert-success').addClass('alert-warning'); 
      }
      $("#coupon_code_msg").text(json.message);

      input_box.removeClass('ui-autocomplete-loading');

      final_total();
    });
  }else{
    $(".div1").addClass('hide');
    $(".coupon_value").html(to_Fixed(coupon_value));
    $(".coupon_type").html(coupon_type);  
    final_total();
  }  
}


$("#coupon_code, #customer_id").on("change",function() {
  get_coupon_details();
});
$('#coupon_code').keypress(function (e) {
 var key = e.which;
 // the enter key code
 if(key == 13){
    get_coupon_details();  
  }
});

$('#item_search').keypress(function (e) {
 var key = e.which;
 // the enter key code
 if(key == 13){
    $("#item_search").autocomplete('search');
  }
});
