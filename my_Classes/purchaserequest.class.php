<?php

function get_supplier_term($supplier_id){
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		SELECT	
			terms
		from
			po_header
		where	
			supplier_id = '$supplier_id'
		order by date DESC
		limit 0,1
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	$terms = $r['terms'];
	
	$objResponse->assign('term','value',$terms);
	return $objResponse;
}


function display_warehouse_qty($stock_id,$tb_id = NULL){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$qty = $options->inventory_warehouse(NULL,$stock_id);
	
	$qty = ($qty > 0) ? $qty : "0";
	
	if(empty($tb_id)){
		$objResponse->assign('warehouse_qty','value',$qty);
	}else{
		$objResponse->assign($tb_id,'value',$qty);
	}
	return $objResponse;
}

function pr_stock_id_form($stock_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$stock = mysql_escape_string($options->attr_stock($stock_id,'stock'));
	
	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			
			<div class='form-div'>
            	Details : <br />
            	<input type='text' class='textbox3' name='details' />
            </div>
		
            <div class='form-div'>
                <div>Request Quantity : </div>        
                <div><input type='text' size='20' id='quantity' class='textbox3' autocomplete='off' name='request_quantity' /></div>
            </div> 
                        
            <hr style='border:1px solid #EEEEEE; width:70%; margin:10px 0px;' />
            
            <div class='form-div' style='vertical-align:top;' id='div_warehouse_qty'>
            	Available Warehouse Quantity : <br />
                <input type='text' class='textbox3' id='warehouse_qty'  name='warehouse_qty'  />
                <input type='checkbox' name='use_qty' id='use_qty'  /><label for='use_qty' class='label'>Use Warehouse Quantity</label>
            </div>
            
            <div class='form-div'>
            	Total Quantity : <br />
            	<input type='text' class='textbox3' name='quantity' id='req_qty'  readonly='readonly' />
            </div>
			
			<hr style='border:1px solid #EEEEEE; width:70%; margin:10px 0px;' />
			
			<div class='form-div'>
            	<em>(Optional)</em> Quantity : <br />
            	<input type='text' class='textbox3' name='quantity2' />
            </div>
			
			<div class='form-div'>
            	<em>(Optional)</em> Unit : <br />
            	<input type='text' class='textbox3' name='unit2' />
            </div>
			
			<div class='form-div'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Add Item' name='b' onclick=xajax_pr_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			xajax_display_warehouse_qty($stock_id)
			
			j('#use_qty').change(function(){
				computeReqQty();
			});
			
			j('#quantity,#warehouse_qty').keyup(function(){
				computeReqQty();
			});	
			
			function computeReqQty(){
				var request_qty = j('#quantity').val();
				var warehouse_qty = j('#warehouse_qty').val();
				var total = 0;
				
				if(j('#use_qty').is(':checked')){
					total = request_qty - warehouse_qty;
				}else{
					total = request_qty;
				}
				j('#req_qty').val(total);	
			}
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$stock' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function pr_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$quantity	= $form_data['quantity'];
	$stock_id	= $form_data['stock_id'];
	
	$quantity2	= $form_data['quantity2'];
	$unit2		= $form_data['unit2'];
	$details	= $form_data['details'];
	
	$project_id		= $form_data2['project_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	$request_quantity	= $form_data['request_quantity'];
	
	$use_qty				= $form_data['use_qty'];
	$warehouse_qty			= (!empty($use_qty))?$form_data['warehouse_qty']:0;
	
	/*
	if(!empty($use_qty)){
		mysql_query("
			insert into 	
				pr_warehouse
			set
				stock_id 		= '$stock_id',
				quantity		= '$warehouse_qty',
				type			= 'M',
				pr_header_id	= '$pr_header_id'
		") or $objResponse->alert(mysql_error());
	}
	*/
	
	$in_budget = $options->in_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id,$quantity);
	
	#$objResponse->alert($in_budget);
	if($in_budget){
		$allowed = 1;
	}else{
		$allowed = 0;	
	}
	
	mysql_query("
		insert into
			pr_detail
		set	
			pr_header_id 		= '$pr_header_id',
			stock_id			= '$stock_id',
			request_quantity	= '$request_quantity',
			warehouse_quantity	= '$warehouse_qty',
			quantity			= '$quantity',
			in_budget			= '$in_budget',
			allowed				= '$allowed',
			unit2				= '$unit2',
			quantity2 			= '$quantity2',
			details				= '$details'
	") or $objResponse->alert(mysql_error());
	
	$objResponse->alert("Item Successfully Requested.");	
	$objResponse->redirect("admin.php?view=$view&pr_header_id=$pr_header_id");
	
	return $objResponse;
}


function pr_service_stock_id_form($stock_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$stock = $options->attr_stock($stock_id,'stock');
	
	$content = "
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				No : <br>
				<input type='text' class='textbox' name='quantity' id='service_quantity'  value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='days' id='service_days' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Rate/Day: <br>
				<input type='text' class='textbox' name='rate_per_day' id='service_cost' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='amount' id='service_amount' value='' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Add Item' name='b' onclick=xajax_pr_service_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j(\"#service_quantity,#service_days,#service_cost\").keyup(function(){
				var quantity = j(\"#service_quantity\").val();
				var days = j(\"#service_days\").val();
				var cost = j(\"#service_cost\").val();
				
				var amount = quantity * days * cost;
				j(\"#service_amount\").val(amount);
			});
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$stock' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function pr_service_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$stock_id		= $form_data['stock_id'];
	$quantity		= $form_data['quantity'];
	$days			= $form_data['days'];
	$rate_per_day	= $form_data['rate_per_day'];
	$amount			= $form_data['amount'];
	
	
	$project_id		= $form_data2['project_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$budget = $options->service_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
	
	if($amount > $budget){
		$allowed = 0;
	}else{
		$allowed = 1;
	}
	
	mysql_query("
		insert into
			pr_service_detail
		set	
			pr_header_id		= '$pr_header_id',
			stock_id			= '$stock_id',
			quantity			= '$quantity',
			days				= '$days',
			rate_per_day		= '$rate_per_day',
			amount				= '$amount',
			allowed				= '$allowed'
	") or $objResponse->alert(mysql_error());
	
		$objResponse->alert("Service Successfully Requested.");	
		$objResponse->redirect("admin.php?view=$view&pr_header_id=$pr_header_id");
	
	return $objResponse;
}


function pr_equipment_stock_id_form($stock_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$stock = $options->attr_stock($stock_id,'stock');
	
	$content = "
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				No : <br>
				<input type='text' class='textbox' name='quantity' id='equipment_quantity'  value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='days' id='equipment_days' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Rental/Day: <br>
				<input type='text' class='textbox' name='rate_per_day' id='equipment_cost' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='amount' id='equipment_amount' value='' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Add Item' name='b' onclick=xajax_pr_equipment_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j(\"#equipment_quantity,#equipment_days,#equipment_cost\").keyup(function(){
				var quantity = j(\"#equipment_quantity\").val();
				var days = j(\"#equipment_days\").val();
				var cost = j(\"#equipment_cost\").val();
				
				var amount = quantity * days * cost;
				j(\"#equipment_amount\").val(amount);
			});
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$stock' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function pr_equipment_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$stock_id		= $form_data['stock_id'];
	$quantity		= $form_data['quantity'];
	$days			= $form_data['days'];
	$rate_per_day	= $form_data['rate_per_day'];
	$amount			= $form_data['amount'];
	
	
	$project_id		= $form_data2['project_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$budget = $options->equipment_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
	
	if($amount > $budget){
		$allowed = 0;
	}else{
		$allowed = 1;
	}
	
	mysql_query("
		insert into
			pr_equipment_detail
		set	
			pr_header_id		= '$pr_header_id',
			stock_id			= '$stock_id',
			quantity			= '$quantity',
			days				= '$days',
			rate_per_day		= '$rate_per_day',
			amount				= '$amount',
			allowed				= '$allowed'
	") or $objResponse->alert(mysql_error());
	
		$objResponse->alert("Equipment Successfully Requested.");	
		$objResponse->redirect("admin.php?view=$view&pr_header_id=$pr_header_id");
	
	return $objResponse;
}

function pr_fuel_stock_id_form($fuel_id,$equipment_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$fuel = $options->attr_stock($fuel_id,'stock');
	
	$warehouse_qty = $options->inventory_warehouse(NULL,$fuel_id);
	
	$content = "
	
		<input type='hidden' name='fuel_id' value='$fuel_id' >
		<input type='hidden' name='equipment_id' value='$equipment_id' >
	
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				Consumption / Day : <br>
				<input type='text' class='textbox' name='consumption_per_day' id='fuel_consumption'  value='' autocomplete='off'>
			</div>
		
			<div class='form-div'>
				Request Quantity : <br>
				<input type='text' class='textbox' id='fuel_quantity'  value='' autocomplete='off' name='request_quantity'>
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='fuel_days' id='fuel_days' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Total Fuel Liters : <br>
				<input type='text' class='textbox' name='total_fuel' id='total_fuel' value='' autocomplete='off' readonly='readonly'>
			</div>
			
			<div class='form-div' style='vertical-align:top;' id='div_warehouse_qty'>
            	Available Warehouse Quantity : <br />
                <input type='text' class='textbox3' id='warehouse_qty'  name='warehouse_qty' value='$warehouse_qty' autocomplete=\"off\" />
                <input type='checkbox' name='use_qty' id='use_qty'  /><label for='use_qty' class='label'>Use Warehouse Quantity</label>
				<input type='hidden'  id='hidden_qty'  value='$warehouse_qty'  />
            </div>
			
			<div class='form-div'>
				Total Quantity : <br>
				<input type='text' class='textbox' name='fuel_quantity' id='req_qty' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Fuel Cost/Litter: <br>
				<input type='text' class='textbox' name='fuel_cost_per_litter' id='fuel_cost' value='' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='fuel_amount' id='fuel_amount' value='' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='button' value='Add Item' name='b' onclick=xajax_pr_fuel_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j('#fuel_consumption,#fuel_quantity,#fuel_days,#fuel_cost,#warehouse_qty').keyup(function(){
				computeFuelQty();
			});
			
			j('#use_qty').change(function(){
				computeFuelQty();
			});
			
			j('#warehouse_qty').keyup(function(){
				var warehouse_qty =parseFloat(j(this).val());
				var hidden_qty	= parseFloat(j('#hidden_qty').val());
				
				//alert(warehouse_qty+' '+hidden_qty);	
				if(warehouse_qty > hidden_qty){
					alert('Input should be less than '+hidden_qty);	
				}
			});
			
			
			function computeFuelQty(){
				var consumption = j('#fuel_consumption').val();
				var quantity  = j('#fuel_quantity').val();		
				var days	= j('#fuel_days').val();
				var cost = j('#fuel_cost').val();
				
				
				var fuel_qty = consumption * quantity * days ;
				
				
				j('#total_fuel').val(fuel_qty);
				
				
				var warehouse_qty = j('#warehouse_qty').val();
				
				if(j('#use_qty').is(':checked')){
					total = fuel_qty - warehouse_qty;
				}else{
					total = fuel_qty;
				}
				
				var amount = total * cost;
				j('#fuel_amount').val(amount);
				j('#req_qty').val(total);	
			}
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$fuel' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function pr_fuel_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$fuel_id				= $form_data['fuel_id'];
	$equipment_id			= $form_data['equipment_id'];
	$consumption_per_day	= $form_data['consumption_per_day'];
	$fuel_quantity			= $form_data['fuel_quantity'];
	$fuel_days				= $form_data['fuel_days'];
	$fuel_cost_per_litter	= $form_data['fuel_cost_per_litter'];
	$fuel_amount			= $form_data['fuel_amount'];
	$request_quantity		= $form_data['request_quantity'];
	
	
	
	$project_id		= $form_data2['project_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$use_qty				= $form_data['use_qty'];
	$warehouse_qty			= (!empty($use_qty))?$form_data['warehouse_qty']:0;
	
	/*
	if(!empty($use_qty)){
		mysql_query("
			insert into 	
				pr_warehouse
			set
				fuel_id 		= '$fuel_id',
				equipment_id	= '$equipment_id',
				quantity		= '$warehouse_qty',
				type			= 'F',
				pr_header_id	= '$pr_header_id'
		") or $objResponse->alert(mysql_error());
	}else{
		$warehouse_qty			= 0;
	}
	*/
	
	
	$budget = $options->fuel_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id);
	
	if($fuel_amount > $budget){
		$allowed = 0;
	}else{
		$allowed = 1;
	}
	
	mysql_query("
		insert into
			pr_fuel_detail
		set	
			pr_header_id		= '$pr_header_id',
			fuel_id				= '$fuel_id',
			equipment_id		= '$equipment_id',
			consumption_per_day	= '$consumption_per_day',
			quantity			= '$fuel_quantity',
			request_quantity	= '$request_quantity',
			warehouse_quantity	= '$warehouse_qty',
			days				= '$fuel_days',
			cost_per_litter		= '$fuel_cost_per_litter',
			amount				= '$fuel_amount',
			allowed				= '$allowed'
	") or $objResponse->alert(mysql_error());
	
		$objResponse->alert("Fuel Successfully Requested.");	
		$objResponse->redirect("admin.php?view=$view&pr_header_id=$pr_header_id");
	
	return $objResponse;
}
?>