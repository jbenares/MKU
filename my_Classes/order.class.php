<?php
	function print_order($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printRR.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("rr_header_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
		
	function removeOrderDetails($order_detail_id,$order_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			delete from
				order_details
			where
				order_detail_id='$order_detail_id'
		";
		
		mysql_query($query) or die(mysql_error());
		
		$objResponse->script("xajax_getUpdatedOrderTable('$order_header_id');");
		return $objResponse;
	}
	
	function getUpdatedOrderTable($order_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		//$objResponse->alert($order_header_id);
		$content=$options->getUpdatedOrderTable($order_header_id);
		
		$objResponse->assign("table_container","innerHTML",$content);
		return $objResponse;	
	}
	
	function addOrderDetails($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$order_header_id		= $form_data[order_header_id];
		$stock_id				= $form_data[stock_id];
		$quantity				= $form_data[quantity];
		$price					= $form_data[price];

		//$price 		= $options->getPrice1OfStock($stock_id);
		
		$amount = $quantity * $price;
		
		$query="
			insert into
				order_details
			set
				order_header_id='$order_header_id',
				stock_id='$stock_id',
				quantity = '$quantity',
				price='$price',
				amount='$amount'
		";
		
		//$objResponse->alert($query);
		mysql_query($query) or die(mysql_error());
		$objResponse->assign("stock_name","value","");
		$objResponse->assign("quantity","value","");
		$objResponse->assign("price","value","");
		$objResponse->assign("amount","value","");
		
		$objResponse->script("xajax_getUpdatedOrderTable('$order_header_id')");
		
		return $objResponse;
	}
	
	function getSRPOfStock($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();

		
		$stock_id 	= $form_data[stock_id];
		$quantity 	= $form_data[quantity];
		$price 		= $options->getPrice1OfStock($stock_id);
		
		$amount		= $quantity * $price;
		

		$objResponse->assign("price","value",$price);
		$objResponse->assign("amount","value",$amount);
		
		return $objResponse;
	}
	
	function computeOrderAmount($form_data){
		$objResponse = new xajaxResponse();
		$options	 = new options();	
				
		$quantity	= $form_data[quantity];
		$price 		= $options->getPrice1OfStock($stock_id);
		
		$amount = $quantity * $price;
		
		$objResponse->assign('amount','value',$amount);
		
		return $objResponse;
	}
	
	function getPriceListOfStock($stock_id){
		$objResponse=new xajaxResponse();
		$options=new options();

		$js = "onchange = document.getElementById('quantity').value='';document.getElementById('amount').value='';";
		$list		= $options->getStockPrices($stock_id,'price',$js);

		$objResponse->assign('price_div','innerHTML',$list);
		return $objResponse;
		
	}
?>