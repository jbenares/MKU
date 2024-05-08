<?php
	function print_productconversion($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
				
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printProductConversion.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("product_convert_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function updateProductConversionStatus($id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		//$objResponse->alert($id);
		
		$query="
			select
				*
			from 
				product_convert
			where
				product_convert_id='$id'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$status=$r[status];
		$audit=$r[audit];
		
		if($status!='C'):
			$query="
				update
					product_convert
				set
					status='P',
					audit='$audit Printed by ".$options->getUserName($_SESSION[userID])."'
				where
					product_convert_id='$id'
			";
			mysql_query($query) or die(mysql_error());
		endif;
		
		return $objResponse;
	}
	
	function displayProductConvertQty($form_data){
		$objResponse		= new xajaxResponse();
		$options			= new options();
		
		$finishedproduct_id	= $form_data['finishedproduct_id'];
		$type				= $options->getStockType($finishedproduct_id);
		
		$product_convert_id	= $form_data['product_convert_id'];
		$js="
				onkeyup=xajax_computeConvertQuantity(xajax.getFormValues('header_form'));
			";
			
		$package_js="
			onchange=xajax_computeConvertQuantity(xajax.getFormValues('header_form'));
		";
		
		if(!empty($product_convert_id)){
			$result=mysql_query("
				select
					*
				from
					product_convert
				where
					product_convert_id='$product_convert_id'
			");
			
			$r=mysql_fetch_assoc($result);
			$packagetype	= $r[packagetype];
			$packqty		= $r[packqty];
			$quantity		= $r[quantity];
		}
		
		if($type=="FP"){
			$content="
				 <div class='inline'>
					<div>Package Type: </div>        
					<div>".$options->getAllPackageOptions($packagetype,'packagetype',$package_js)."</div>
				</div>  
				
				<div class='inline'>
					<div>Package Quantity: </div>        
					<div>
						<input type='text' name='packqty' id='packqty' value='$packqty' class='textbox3' $js />
					</div>
				</div> 
			";
		}else if($type=="RM"){
			
			
			
			$content="
				<div class='inline'>
					<div>Quantity: </div>        
					<div>
						<input type='text' name='quantity' id='quantity' value='$quantity' class='textbox3' onkeyup=document.getElementById('qty').value=this.value; />
					</div>
				</div> 
			";
		}
		$objResponse->assign("qty_div","innerHTML",$content);
			
		return $objResponse;
	}
	
	function computeConvertQuantity($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$finishedproduct_id	= $form_data['finishedproduct_id'];
		$type				= $options->getStockType($finishedproduct_id);
		
		$quantity			= $form_data['quantity'];
		
		$packagetype		= $form_data['packagetype'];
		$packqty			= $form_data['packqty'];
		
		$package_qty		= $options->getPackageQty($packagetype) * $packqty;
		
		if($type=="RM"){
			$objResponse->assign("qty","value",$quantity);
		}else if($type=="FP"){
			$objResponse->assign("qty","value",$package_qty);
		}
		
		return $objResponse;
	}
?>