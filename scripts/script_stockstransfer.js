/*****************************************
QUERY STOCKS FROM LOCATION
*****************************************/
j(function(){
	/*queryStocksFromLocation();*/
	
	j("#header_form").validate({
		errorContainer: "#messageError",
		errorLabelContainer: "#messageError ul",
		wrapper: "li"
	});
	
	j("#addButton").click(function(){
		addStockTransferDetail();
	});
	
	j("#stock_id").change(function(){
		queryInputFieldsForStocksTransfer();
	});
	
});

function queryInputFieldsForStocksTransfer(){
	var form_data={
		query:"queryInputFieldsForStocksTransfer",
		stock_id:j("#stock_id").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#qty_input").html(data);
			}
	});		
}

function queryStocksFromLocation(){
	var form_data={
		query:"queryStocksFromLocation",
		from_locale:j("#from_locale").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#stock_id").html(data);
			}
	});		
}

function queryStocksQuantity(){
	var form_data={
		query:"queryStocksQuantity",
		stock_id:j("#stock_id").val(),
		from_locale:j("#from_locale").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#qty").val(data);
			}
	});		
}

function addStockTransferDetail(){
	var form_data={
		query:"addStockTransferDetail",
		stock_id:j("#stock_id").val(),
		typeofpackage:j("#typeofpackage").val(),
		qty:j("#qty").val(),
		transfer_hdr_id:j("#transfer_hdr_id").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#table_container").html(data);
				/*queryStocksFromLocation();*/
				j("#qty").val('');
				numberitems();
			}
	});		
}

function removeStockTransferDetail(id){
	
	var form_data={
		query:"removeStockTransferDetail",
		transfer_det_id:id,
		transfer_hdr_id:j("#transfer_hdr_id").val()
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

function refreshStockTransferDetails(){
	
	var form_data={
		query:"refreshStockTransferDetails",
		transfer_hdr_id:j("#transfer_hdr_id").val()
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
}

