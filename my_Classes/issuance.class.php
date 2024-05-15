<?php
	function issue_to_form(){
		$objResponse 	= new xajaxResponse();
		$options		= new options();	
		
		
		$content = "
			<div class='module_actions'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<div class='form-div'>
					Issue to : <br>
					<select name='issue_to' id='issue_to'>
						<option value='P'>Project</option>
						<option value='C'>Subcontractor</option>
					</select>
				</div>
				
				<div class='form-div' style='display:none;' id='div_contractor'>
					Subcontractor : <br>
					".$options->option_Contractor()."
				</div>
				
				<div class='form-div'>
					<input type='button' value='Finish' name='b' onclick=xajax_issue_to(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />
				</div>
			</div>
			
			<script type='text/javascript'>
				j(\"input:button\").button();
				
				j('#issue_to').change(function(){
					if( j(this).val() == 'C' ){
						j('#div_contractor').show(500);
					}else{
						j('#div_contractor').hide(500);
					}
				});
			</script>
		";
		
		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script('openDialog();');
		$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"ISSUE FORM\" );");
		return $objResponse;
	}



	function issue_to($form_data,$form_data2){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
			
		$issue_to		= $form_data['issue_to'];
		$account_id	= $form_data['account_id'];
		
		$date					= $form_data2['date'];
		$issuance_header_id		= $form_data2['issuance_header_id'];
		$project_id				= $form_data2['project_id'];
		$view					= $form_data2['view'];
		
		
		$query="
			update
				issuance_header
			set
				status='F'
		";
		
		if(!empty($account_id)){
		$query.="
			,issued_to = '$account_id'
		";	
		}else{
		$query.="
			,issued_to = '0'
		";	
		}
		
		$query.="
			where
				issuance_header_id = '$issuance_header_id'
		";	
		
		mysql_query($query) or $objResponse->alert(mysql_error());
		
		$options->insertAudit($issuance_header_id,'issuance_header_id','F');
		$options->postIssuance($issuance_header_id,$contractor_id);
				
		$objResponse->alert("Issuance Finished and Posted to GL");	
		$objResponse->redirect("admin.php?view=$view&issuance_header_id=$issuance_header_id");

		
		return $objResponse;
	}

	function issuance_stock_id_form($stock_id,$form_data){
		$objResponse 	= new xajaxResponse();
		$options		= new options();	
		
		$stock 		= addslashes(htmlentities($options->attr_stock($stock_id,'stock')));
		
		$driver_dd = $options->getTableAssoc(NULL,'driverID',"Select Driver","select * from drivers order by driver_name asc","driverID","driver_name");

		$content = "
			<div class='module_actions'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				
				<div class='form-div'>
					Reference : <br>
					<input type='text' class='textbox3' name='_reference' value='' autocomplete=\"off\">
				</div>
				
				<div class='form-div'>
					<em>(Optional)</em> Driver : <br>
					".$driver_dd."
				</div>
				
				<div class='form-div'>
					Quantity : <br>
					<input type='text' class='textbox3' name='quantity' id='quantity' value='' autocomplete=\"off\">
				</div>
				
				<div class='form-div'>
					<em>(Optional)</em> Qty : <br>
					<input type='text' class='textbox3' name='quantity_cum' id='quantity_cum' value='' autocomplete=\"off\">
				</div>
				
				<div class='form-div'>
					<em>(Optional)</em> Unit : <br>
					<input type='text' class='textbox3' name='_unit' value='' autocomplete=\"off\">
				</div>
				
				<div class='form-div'>
					Price : <br>
					".$options->option_price_issuance('price',$stock_id,NULL,NULL,NULL)."
				</div>
				<!--
				<div class='form-div'>
					Price : <br>
					<input type='text' class='textbox3' id='price' name='price'  >
				</div>
				-->
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox3' id='amount' readonly='readonly' >
				</div>
				
				<hr style='border:none; border-top:1px solid #CCC;' >
				
				<div class='form-div'>
					J.O. #: <br>
					<input type='textbox' class='textbox' name='joborder_header_id' id='joborder_header_id' >
				</div>
				
				<hr style='border:none; border-top:1px solid #CCC;' >
				
				<div class='form-div'>
					Charge to Account : <br>
					<input type='textbox' class='accounts textbox'>
					<input type='hidden' name='account_id'>
				</div>
				
				<div class='form-div'>
					Charge to Equipment : <br>
					<input type='textbox' class='equipment_name textbox'>
					<input type='hidden' name='equipment_id'>
				
				<div class='form-div'>
					<input type='button' value='Add Item' name='b' onclick=xajax_issuance_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />
				</div>
			</div>
			
			<script type='text/javascript'>
				j(\"input:button\").button();
				
				j('#quantity').keyup(function(){
					solveIssueAmount();
				});
				
				j('#price').change(function(){
					solveIssueAmount();
				});
				
				j(\".accounts\").autocomplete({
					source: \"dd_accounts.php\",
					minLength: 1,
					select: function(event, ui) {
						j(this).val(ui.item.value);
						j(this).next().val(ui.item.id);
					}
				});
				
				j(\".equipment_name\").autocomplete({
					source: \"dd_equipment_he.php\",
					minLength: 2,
					select: function(event, ui) {
						j(this).val(ui.item.value);
						j(this).next().val(ui.item.id);
					}
				});

				jQuery('#joborder_header_id').blur(function(){
					jQuery.post(\"ajax/issuance.php\", { action: 'joIsAvailable', params : { joborder_header_id : jQuery('#joborder_header_id').val() } }, function(data){
					    //actions					    
					    if(!data){
					    	// no rows
					    	if( !confirm( 'JO# entered was not found. Would you like to continue? ' ) ){
					    		jQuery('#joborder_header_id').val('');
					    	} 
					    }
					} );
					return false;
				});
				
				function solveIssueAmount(){
					var quantity = j('#quantity').val();
					var price = j('#price').val();
					
					var amount = quantity * price ; 
					
					j('#amount').val(amount);
				}
				
			</script>
		";
		
		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script('openDialog();');
		//$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"$stock\" );");
		return $objResponse;
	}
	
	function issuance_stock_id($form_data,$form_data2){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		
		$quantity_cum	= $form_data['quantity_cum'];
		$driver			= $form_data['driverID'];
		$_reference 	= $form_data['_reference'];
			
		$quantity		= $form_data['quantity'];
		$stock_id		= $form_data['stock_id'];
		$description	= $form_data['description'];
		$price			= $form_data['price'];
		$_unit			= $form_data['_unit'];
		
		$account_id		= $form_data['account_id'];
		$equipment_id	= $form_data['equipment_id'];
		
		$joborder_header_id = $form_data['joborder_header_id'];
		
		$issuance_header_id	= $form_data2['issuance_header_id'];
		$date				= $form_data2['date'];
		$project_id			= $form_data2['project_id'];	
		$view				= $form_data2['view'];
		$work_category_id	= $form_data2['work_category_id'];
		$sub_work_category_id = $form_data2['sub_work_category_id'];
		$scope_of_work		= $form_data2['scope_of_work'];
		
		// if($project_id == 14){
		// 	#FOR WAREHOUSE	
		// 	$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);
		// }else{
		// 	#FOR PROJECT
		// 	$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
			
		// }

			$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
		
		#$issued_qty 				= $options->issuance_issuedToProject($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
		#$requested_qty				= $options->total_approved_stocks_requested($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
		
		$balance = $project_warehouse_qty;
		
		$balance = round($balance,6);
		$quantity = round($quantity,6);
		
		#$objResponse->alert(  gettype($balance) ." < ". gettype($quantity));
		#$objResponse->alert( "$balance < $quantity ");
		if($balance < $quantity){
			$objResponse->alert("Error : Quantity is Greater than Balance or Project does not have enough STOCKS");	
		}else{
			
			$amount =  $quantity * $price;
			
			mysql_query("
				insert into
					issuance_detail
				set
					stock_id = '$stock_id',
					quantity = '$quantity',
					price = '$price',
					amount = '$amount',
					issuance_header_id = '$issuance_header_id',
					account_id = '$account_id',
					equipment_id = '$equipment_id',
					joborder_header_id = '$joborder_header_id',
					quantity_cum = '$quantity_cum',
					driverID = '$driver',
					_reference = '$_reference',
					_unit = '$_unit'
			") or $objResponse->alert(mysql_error());
			
			$objResponse->alert("Item Successfully Issued.");	
			$objResponse->redirect("admin.php?view=$view&issuance_header_id=$issuance_header_id");
		}
		
		return $objResponse;
	}

?>