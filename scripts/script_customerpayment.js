// JavaScript Document

j(document).ready(function(){
	numberitems();
	
	j("#header_form").validate({
		errorContainer: "#messageError",
		errorLabelContainer: "#messageError ul",
		wrapper: "li"
	});
				
	j("#addButton").click(function(){
		addDetails();
		
	});
	
	j("#addCheckButton").click(function(){
		addCheckDetail();
	});
	
	j("#dr_header_id").change(function(){
		queryDeliveryAmount();
	});
});


/*****************************************
Query Delivery Amount
*****************************************/
function queryDeliveryAmount(){
	var form_data={
		query:"queryDeliveryAmount",
		dr_header_id:j("#dr_header_id").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(html){
				j("#amount").val(html);
			}
	});			
}


/*****************************************
ADD DETAIL
*****************************************/

function addDetails(){
	
	var form_data={
		query:"addCustomerPaymentDetails",
		dr_header_id:j("#dr_header_id").val(),
		amount:j("#amount").val(),
		pay_header_id:j("#pay_header_id").val()
	};
	
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(html){
				//alert(html);
				//j("#table_container").html(html);
				updateCustomerPaymentTable();
				numberitems();
				j("#amount").val('');
				
				refreshDeliveryDetails();
			}
	});		
}

/*****************************************
REFRESH DELIVERY DETAILS
*****************************************/

function refreshDeliveryDetails(){
	
	var form_data={
		query:"refreshDeliveryDetails",
		dr_header_id:j("#dr_header_id").val(),
		amount:j("#amount").val(),
		pay_header_id:j("#pay_header_id").val()
	};
	
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(content){
				j("#dr_div").html(content);
			}
	});		
}


function addCheckDetail(){
	
	var form_data={
		query:"addCheckDetail",
		bank:j("#bank").val(),
		checkno:j("#checkno").val(),
		datecheck:j("#datecheck").val(),
		checkamount:j("#checkamount").val(),
		checkstatus:j("#checkstatus").val(),
		pay_header_id:j("#pay_header_id").val()
	};
	
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(html){
				//alert(html);
				updateCustomerPaymentTable();
				numberitems();
				j("#amount").val('');
			}
	});		
}


/*****************************************
REMOVE PARENT
*****************************************/

function removeParent(object,id){
	
	var form_data={
		query:"deleteCustomerPaymentDetail",
		payment_detail_id:id,
		pay_header_id:j("#pay_header_id").val()
	};
	
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(html){
				updateCustomerPaymentTable();
				
				refreshDeliveryDetails();
			}
	});		
}

function removeCheckParent(id){
	
	var form_data={
		query:"deleteCheckPaymentDetail",
		pay_check_id:id,
		pay_header_id:j("#pay_header_id").val()
	};
	
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(html){
				updateCustomerPaymentTable();
			}
	});		
}


/*****************************************
UPDATE CUSTOMER PAYMENT TABLE - refresh
  --also updates dr_header
*****************************************/

function updateCustomerPaymentTable(){
	var form_data={
		query:"updateCustomerPaymentTable",
		pay_header_id:j("#pay_header_id").val()
	};
	
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(html){
				j("#table_container").html(html);
				numberitems();
			}
	});	
	
}

/*****************************************
COLOR AND NUMBER TABLE
*****************************************/

function numberitems(){
	
	j("#search_table tr > td:first-child").each(function(i){
		j(this).html(i+1);
		if(i%2==0){
			j(this).parent().css('background','#FFFFCC');	
		}else{
			j(this).parent().css('background','#EEE8AA');	
		}
		
	});	
	
	j("#search_table tr > td:first-child").each(function(i){
		j(this).html(i+1);
		if(i%2==0){
			j(this).parent().css('background','#FFFFCC');	
		}else{
			j(this).parent().css('background','#EEE8AA');	
		}
	});	
	
	j("#search_table_1 tr > td:first-child").each(function(i){
		j(this).html(i+1);
		if(i%2==0){
			j(this).parent().css('background','#FFFFCC');	
		}else{
			j(this).parent().css('background','#EEE8AA');	
		}
		
	});	
	
	j("#search_table_1 tr > td:first-child").each(function(i){
		j(this).html(i+1);
		if(i%2==0){
			j(this).parent().css('background','#FFFFCC');	
		}else{
			j(this).parent().css('background','#EEE8AA');	
		}
	});	
}