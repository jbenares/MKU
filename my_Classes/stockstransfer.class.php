<?php
	function print_stockstransfer($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
				
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printStocksTransfer.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("transfer_hdr_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function transfer_stock_id_form($stock_id){
		$objResponse 	= new xajaxResponse();
		$options		= new options();	
		
		$stock = $options->attr_stock($stock_id,'stock');
		
		$content = "
			<div class='module_actions'>
				<div>
					Quanity : <br>
					<input type='text' class='textbox3' name='quantity' id='quantity' value='' autocomplete=\"off\" >
				</div>
				<div>
					Price : <br>
					".$options->option_price_issuance('price',$stock_id,NULL,NULL,NULL)."
				</div>
				<div>
					Amount : <br>
					<input type='text' class='textbox3' name='amount' id='amount' readonly=\"readonly\" >
				</div>
				
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Add Item' name='b' onclick=xajax_transfer_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />
			</div>
			
			<script type='text/javascript'>
				j(\"input:button\").button();
				j(\"#dialog\").dialog(\"option\",\"title\",\"STOCKS TRANSFER\");
				j('#quantity,#price').change(function(){
					solveAmount();
				});
				
				function solveAmount(){
					var quantity = j('#quantity').val();
					var price = j('#price').val();
					
					var amount = quantity * price ; 
					
					j('#amount').val(amount);
				}
			</script>
		";
		
		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script('openDialog();');
		return $objResponse;
	}
	
	function transfer_stock_id($form_data,$form_data2){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
			
		$quantity	= $form_data['quantity'];
		$stock_id	= $form_data['stock_id'];
		$price		= $form_data['price'];
		$amount 	= $form_data['amount'];
		
		$transfer_header_id	= $form_data2['transfer_header_id'];
		$date				= $form_data2['date'];

		$project_id = $form_data2['from_project_id'];

		/*$project_id			= $form_data2['project_id'];	*/
		
		if( $project_id == 9 ){
			$balance = $options->inventory_warehouse($date,$stock_id);
		} else {
			$balance = $options->inventory_projectwarehousebalance($date,$stock_id,$project_id);
		}

		
		$view = $form_data2['view'];

		$quantity = round($quantity,2);
		$balance = round($balance,2);
		if($balance < $quantity){
			$objResponse->alert("Error : Quantity is Greater than Warehouse Balance \n Balance is: $balance and Requested Qty is: $quantity");	
		}else{
			
			mysql_query("
				insert into
					transfer_detail
				set
					stock_id = '$stock_id',
					quantity = '$quantity',
					price = '$price',
					amount = '$amount',
					transfer_header_id = '$transfer_header_id'
			") or $objResponse->alert(mysql_error());
			
			$objResponse->alert("Item Successfully Transferred.");	
			$objResponse->redirect("admin.php?view=$view&transfer_header_id=$transfer_header_id");
		}
		
		return $objResponse;
	}

?>