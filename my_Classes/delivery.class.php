<?php
	function print_delivery($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		
				
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printDeliveryReceipt.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("dr_header_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function updateStatus($id){
		$objResponse=new xajaxResponse();
		//$objResponse->alert($id);
		$query="
			select
				*
			from 
				dr_header
			where
				dr_header_id='$id'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$status=$r[status];
		if($status!='C'):
			$query="
				update
					dr_header
				set
					status='P'
				where
					dr_header_id='$id'
			";
			mysql_query($query);
		endif;
		
		return $objResponse;
	}
	
	function displayCurrentBalance($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$stock_id=$form_data[stock_id];
		$locale_id=$form_data[locale_id];
		$date=$form_data['date'];
				
		$date = date('Y-m-d',strtotime ('+1 day' ,strtotime($date)));
		$type=$options->getStockType($stock_id);		
		
		if($type!="FP"):
			$qty = $options->getCurrentBalanceOfStock($stock_id,$date,$locale_id);
		else:
			$qty = $options->getInventoryBalanceOfFinishedProduct($stock_id,$date,$locale_id);
		endif;
		
		$return = (empty($qty))?0:$qty;
		
		$objResponse->assign("currentbalance","innerHTML",$return);
		
		return $objResponse;
	}
	
	function getSRP($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$stock_id=$form_data[stock_id];
		$package_id=$form_data[package_id];
		$quantity=$form_data[quantity];
		$discount=(empty($form_data[discount_detail]))?0:$form_data[discount_detail];
		
		$costperkilo=$options->getPrice1OfStock($stock_id);
		$kilosperbag=$options->getPackageQty($package_id);
		
		//$srp=$costperkilo*$kilosperbag;
		$srp=$costperkilo;
		
		$price=($srp) - ($srp * $discount / 100);
		
		$amount= $price * $quantity;
		
		$objResponse->assign("srp","value",$srp);
		$objResponse->assign("price","value",$price);
		$objResponse->assign("amount","value",$amount);
		
		return $objResponse;
	}
	
	function addDRDetails($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
				insert into 
					dr_detail
				set
					dr_header_id='$form_data[dr_header_id]',
					stock_id='$form_data[stock_id]',
					quantity='$form_data[quantity]',
					package_id='$form_data[package_id]',
					srp='$form_data[srp]',
					price='$form_data[price]',
					discount='$form_data[discount_detail]',
					amount='$form_data[amount]',
					type='D'
			";	
		mysql_query($query);
		$options->updateDeliveryHeader($form_data[dr_header_id]);
		
		$objResponse->script("xajax_refreshDR(xajax.getFormValues('header_form'));");
		
		return $objResponse;
	}
	
	function addDRReturns($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_detail_id='$form_data[return]'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$quantity=$form_data[quantity_return];
		$amount=$quantity*$r[price];
		
		
		$query="
				insert into 
					dr_detail
				set
					dr_header_id='$r[dr_header_id]',
					stock_id='$r[stock_id]',
					quantity='$quantity',
					package_id='$r[package_id]',
					srp='$r[srp]',
					price='$r[price]',
					discount='$r[discount]',
					amount='$amount',
					type='R'
			";	
		mysql_query($query) or $objResponse->alert(mysql_error());
		
		$options->updateDeliveryHeader($r[dr_header_id]);
		$objResponse->script("xajax_refreshDR(xajax.getFormValues('header_form'));");
		return $objResponse;
	}
	
	function addDRAdjustments($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_detail_id='$form_data[adjustment]'
		";
		$result=mysql_query($query) or die(mysql_error());
		$r=mysql_fetch_assoc($result);
		
		$quantity=$form_data[quantity_adjustment];
		
		$stock_id=$r[stock_id];
		$srp=$r[srp];
		$discount=$r[discount];
		$price=$r[price];
		$package_id=$r[package_id];
		$dr_header_id=$r[dr_header_id];
		
		$packageqty=$options->getPackageQty($package_id);
		
		$amount=$price*$quantity/$packageqty;
		
		$query="
				insert into 
					dr_detail
				set
					dr_header_id='$dr_header_id',
					stock_id='$stock_id',
					quantity='$quantity',
					package_id='$package_id',
					srp='$srp',
					price='$price',
					discount='$discount',
					amount='$amount',
					type='A'
			";	
		mysql_query($query);
		
		$options->updateDeliveryHeader($dr_header_id);
		$objResponse->script("xajax_refreshDR(xajax.getFormValues('header_form'));");

		return $objResponse;
	}
	
	function refreshDR($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$content=$options->getUpdatedDeliveryTable($form_data[dr_header_id]);
		/*Update dr_header on add details*/
		
		$objResponse->assign("table_container","innerHTML",$content);
		
		return $objResponse;
	}
	
	function removeDRDetail($dr_detail_id,$dr_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();		
		
		$query="
			delete from
				dr_detail
			where
				dr_detail_id='$dr_detail_id'
		";
		mysql_query($query);
		
		$options->updateDeliveryHeader($dr_header_id);
		$objResponse->script("xajax_refreshDR(xajax.getFormValues('header_form'));");
		
		return $objResponse;
	}
	
	function getOrderDetails($order_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$content=$options->getUpdatedOrderTable($order_header_id);
		
		$objResponse->assign("table_container","innerHTML",$content);
		return $objResponse;	
	}
	
	function table_orderForDR($order_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$content=$options->table_orderForDR($order_header_id);
		
		$objResponse->assign("table_container","innerHTML",$content);
		return $objResponse;	
	}
	
	function deliveryStatus($form_data){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		
		$dr_header_id		= $form_data['dr_header_id'];
		$order_header_id	= $form_data['order_header_id'];
		
		$content='	
			<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
				<tr bgcolor="#C0C0C0">				
					<th width="20"><b>#</b></th>
					<th>Stock</th>
					<th>Status</th>
				</tr> 
			';
			
			$query="
				select 
					*
				from
					dr_detail
				where
					dr_header_id = '$dr_header_id'
				group by 
					stock_id
			";
			
			$result=mysql_query($query);
			
			$netamount=0;
			$grossamount=0;
			$totaldiscount=0;
			$i=1;
			while($r=mysql_fetch_assoc($result)):
				$dr_detail_id			= $r[dr_detail_id];
				$stock_id				= $r[stock_id];
				$price					= $r[price];
				$srp					= $r[srp];
				$amount					= $r[amount];
				$quantity				= $r[quantity];
				$discount				= $r[discount];
				
				$netamount+=$amount;
				$grossamount+= $srp * $quantity;
				$totaldiscount+=$srp * $quantity *( $discount / 100 );
				
				$orderMinusDR = $options->deliveryStatus($order_header_id,$stock_id);
				if($orderMinusDR > 0){
					$drstatus =	"UNDER DELIVERED";
				}else if($orderMinusDR < 0){
					$drstatus =	"OVER DELIVERED";
				}else{
					$drstatus =	"EXACT AMOUNT DELIVERED";
				}
				
				$content.=' 
					<tr>				
						<td>'.$i++.'</td>
						<td>'.$options->getMaterialName($r[stock_id]).'</td>
						<td>'.$drstatus.'</td>
					</tr>   
				';
			endwhile;
			
			$content.='
				</table>
			';
				
			$objResponse->assign("dialog2_content","innerHTML",$content);
			$objResponse->script("openDialog2();");
			$objResponse->script("j( \"#dialog2\" ).dialog( \"option\", \"title\", 'DELIVERY STATUS' );");
			$objResponse->script("j( \"#dialog2\" ).dialog( \"option\", \"position\", ['right','bottom'] );");
			$objResponse->script("setTimeout(\"closeDialog2();\",3000);");
		
		return $objResponse;
	}
?>