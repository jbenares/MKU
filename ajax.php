<?php
	include_once("conf/ucs.conf.php");
	include_once("my_Classes/options.class.php");
	
	$options=new options();
	
	
	if($_REQUEST[query]=="computeVariance"){
		$expectedoutput=$_REQUEST[expected];
		$actualoutput=$_REQUEST[actual];
		
		echo $options->computeVariance($expectedoutput,$actualoutput);
	
	}else if($_REQUEST[query]=="addDeliveryDetails"){
		
		$query="
				insert into 
					dr_detail
				set
					dr_header_id='$_REQUEST[dr_header_id]',
					stock_id='$_REQUEST[stock_id]',
					quantity='$_REQUEST[quantity]',
					package_id='$_REQUEST[package_id]',
					srp='$_REQUEST[srp]',
					price='$_REQUEST[price]',
					discount='$_REQUEST[discount]',
					amount='$_REQUEST[amount]',
					type='D'
			";	
		mysql_query($query);
		
		$content=$options->getUpdatedDeliveryTable($_REQUEST[dr_header_id]);
		/*Update dr_header on add details*/
		
		$options->updateDeliveryHeader($_REQUEST[dr_header_id]);
		
		echo $content;
	}
	
	else if($_REQUEST[query]=="addDeliveryReturns"){
		
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_detail_id='$_REQUEST[dr_return]'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$quantity=$_REQUEST[quantity_return];
		
		$amount=$r[price]*$quantity;
		
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
		mysql_query($query) or die(mysql_error());
		
		
		$options->updateDeliveryHeader($r[dr_header_id]);
		
		echo $content;
	}
	
	else if($_REQUEST[query]=="addDeliveryAdjustment"){
		
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_detail_id='$_REQUEST[dr_adjustment]'
		";
		$result=mysql_query($query) or die(mysql_error());
		$r=mysql_fetch_assoc($result);
		
		$quantity=$_REQUEST[quantity_adjustment];
		
		$stock_id=$options->getStockFromDeliveryDetailID($_REQUEST[dr_adjustment]);
		
		$srp=$options->getPrice1OfStock($stock_id);
		
		$discount=$r[discount];
		
		$price=$srp -($srp * ($discount / 100));
		
		$amount=$price*$quantity;
		
		$query="
				insert into 
					dr_detail
				set
					dr_header_id='$r[dr_header_id]',
					stock_id='$r[stock_id]',
					quantity='$quantity',
					package_id='',
					srp='$srp',
					price='$price',
					discount='$r[discount]',
					amount='$amount',
					type='A'
			";	
		mysql_query($query);
		
		$options->updateDeliveryHeader($r[dr_header_id]);
		
		echo $content;
	}
	
	else if($_REQUEST[query]=="updateDeliveryTable"){
		echo $options->getUpdatedDeliveryTable($_REQUEST[dr_header_id]);
	}
	
	else if($_REQUEST[query]=="updateCustomerPaymentTable"){
		
		echo $options->getUpdatedCustomerPaymentTable($_REQUEST[pay_header_id]);
	}
		
	else if($_REQUEST[query]=="getDeliveryGrossAmount"){
	
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_header_id='$_REQUEST[dr_header_id]'
		";
		
		$result=mysql_query($query);
		$grossamount=0;
		while($r=mysql_fetch_assoc($result)):
			$grossamount+=($r[quantity]*$r[srp]);
		endwhile;
		
		echo $grossamount;
	
	}else if($_REQUEST[query]=="solveDeliveryPrice"){
		$discount=$_REQUEST[discount];
		$srp=$_REQUEST[srp];
		$quantity=$_REQUEST[quantity];
		
		$discountedamount=($discount / 100)*$srp;
		$price=$srp-$discountedamount;
		
		echo $price;
	}else if($_REQUEST[query]=="solveDeliveryAmount"){
		$price=$_REQUEST[price];
		$quantity=$_REQUEST[quantity];
		
		$amount=$price*$quantity;
		
		echo $amount;
	}else if($_REQUEST[query]=="getDeliveryNetAmount"){
	
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_header_id='$_REQUEST[dr_header_id]'
		";
		
		$result=mysql_query($query);
		$discountedamount=0;
		while($r=mysql_fetch_assoc($result)):
			$discountedamount+=($r[amount]);
		endwhile;
		
		echo $discountedamount;
	
	}else if($_REQUEST[query]=="getDeliveryTaxAmount"){
	
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_header_id='$_REQUEST[dr_header_id]'
		";
		
		$result=mysql_query($query);
		$discountedamount=0;
		while($r=mysql_fetch_assoc($result)):
			$discountedamount+=($r[amount]);
		endwhile;
		
		$netamount=$discountedamount/1.12;
		
		echo round($netamount*0.12,2);
	
	}else if($_REQUEST[query]=="getDeliveryDiscountAmount"){
	
		$query="
			select 
				*
			from
				dr_detail
			where
				dr_header_id='$_REQUEST[dr_header_id]'
		";
		
		$result=mysql_query($query);
		$discountamount=0;
		while($r=mysql_fetch_assoc($result)):
			$discountamount+=($r[quantity]*$r[srp]*($r[discount]/100));
		endwhile;
		
		echo $discountamount;
	
	}else if($_REQUEST[query]=="deleteDeliveryDetail"){
		
		$query="
			delete from
				dr_detail
			where
				dr_detail_id='$_REQUEST[dr_detail_id]'
		";
		mysql_query($query);
		
		$options->updateDeliveryHeader($_REQUEST[dr_header_id]);
		
	}
	
	else if($_REQUEST[query]=="deleteCustomerPaymentDetail"){
		
		$query="
			delete from
				payment_detail
			where
				payment_detail_id='$_REQUEST[payment_detail_id]'
		";
		
		mysql_query($query);
		
		$options->updateCustomerPaymentHeader($_REQUEST[pay_header_id]);
		
	}
	
	else if($_REQUEST[query]=="deleteCheckPaymentDetail"){
		
		$query="
			delete from
				pay_checks
			where
				pay_check_id='$_REQUEST[pay_check_id]'
		";
		
		mysql_query($query) or die(mysql_error());
		
		$options->updateCustomerPaymentHeader($_REQUEST[pay_header_id]);
		
	}
	
	else if($_REQUEST[query]=="getSRP"){
		$stock_id=$_REQUEST[stock_id];	
		$package_id=$_REQUEST[package_id];
	
		$costperkilo=$options->getPrice1OfStock($stock_id);
		$kilosperbag=$options->getPackageQty($package_id);
		
		$srp=$costperkilo*$kilosperbag;
		
		echo $srp;
		
	}else if($_REQUEST[query]=="getCostOfStock"){
		$stock_id=$_REQUEST[stock_id];	
	
		$costperkilo=$options->getCostOfStock($stock_id);
		echo $costperkilo;
	}
	else if($_REQUEST[query]=="addReceivingReportDetails"){
		
		/*
			UPDATE PRODUCT MASTER
		*/
		
		$locale_id=$_REQUEST[locale_id];
		$date=$_REQUEST['date'];
		$stock_id=$_REQUEST[stock_id];
		$price=$_REQUEST[price];
		$quantity=$_REQUEST[quantity];
		
		$date = date('Y-m-d',strtotime ('+1 day' , strtotime ( $date)));
		
		$inventorybalance=$options->getTotalCurrentBalanceOfStock($stock_id,$date);
		
		$oldcost=$options->getCostOfStock($stock_id);
		
		$newcost=(($oldcost*$inventorybalance)+($price * $quantity))/($inventorybalance + $quantity);
		 
		$query="
			update
				productmaster
			set
				cost='$newcost'
			where	
				stock_id='$stock_id'
		";
		
		mysql_query($query) or die(mysql_error());
		
		$query="
				insert into 
					rr_detail
				set
					rr_header_id='$_REQUEST[rr_header_id]',
					stock_id='$_REQUEST[stock_id]',
					quantity='$_REQUEST[quantity]',
					cost='$_REQUEST[cost]',
					price='$_REQUEST[price]',
					discount='$_REQUEST[discount]',
					amount='$_REQUEST[amount]'
			";	
		mysql_query($query);
		
		$options->updateRRHeader($_REQUEST[rr_header_id]);
		
	}else if($_REQUEST[query]=="refreshReceivingReportDetails"){
		
		echo $options->getUpdatedRRTable($_REQUEST[rr_header_id]);

	}else if($_REQUEST[query]=="deleteReceivingReportDetail"){
		$query="
			delete from
				rr_detail
			where
				rr_detail_id='$_REQUEST[rr_detail_id]'
		";
		mysql_query($query);
		
		$options->updateRRHeader($_REQUEST[rr_header_id]);
		
	}else if($_REQUEST[query]=="queryExcess"){
		$finishedproduct=$_REQUEST[finishedproduct];
		
		$excess=$options->getExcessForNewJO($finishedproduct);
		
		echo ($excess)?$excess:0;
	}else if($_REQUEST[query]=="queryExcessForJOEdit"){
		$finishedproduct=$_REQUEST[finishedproduct];
		$jobdate=$_REQUEST[jobdate];
		
		$date=explode("/",$jobdate);
		$jobdate="$date[2]-$date[0]-$date[1]";		
		
		$excess=$options->getExcessForEditJO($finishedproduct,$jobdate);
		
		echo ($excess)?$excess:0;
	}
	else if($_REQUEST[query]=="queryInventoryBalance"){
		$finishedproduct=$_REQUEST[finishedproduct];
		$typeofpackage=$_REQUEST[typeofpackage];
		
		echo $options->getUnsoldBagsForNewJO($finishedproduct,$typeofpackage);
				
	}else if($_REQUEST[query]=="queryTypeOfPackage"){
		echo $options->getPackageName($_REQUEST[typeofpackage]);
	}else if($_REQUEST[query]=="queryTypeOfPackage_1"){
		$package_id=$options->getPackage_1($_REQUEST[typeofpackage]);
		echo $package_id;
	}else if($_REQUEST[query]=="queryPackageName_1"){
		echo $options->getPackageName($_REQUEST[typeofpackage]);
	}else if($_REQUEST[query]=="queryTypeOfPackage_2"){
		$package_id=$options->getPackage_2($_REQUEST[typeofpackage]);
		echo $package_id;
	}
	else if($_REQUEST[query]=="queryPackageName_2"){
		echo $options->getPackageName($_REQUEST[typeofpackage]);
	}else if($_REQUEST[query]=="queryTypeOfPackage_3"){
		$package_id=$options->getPackage_3($_REQUEST[typeofpackage]);
		echo $package_id;
	}
	else if($_REQUEST[query]=="queryPackageName_3"){
		echo $options->getPackageName($_REQUEST[typeofpackage]);
	}
	else if($_REQUEST[query]=="queryFormulation"){
		//$stock_id $formulation_id
		
		$query="
			select 
				*
			from 
				formulation_header
			where
				finishedproduct='$stock_id'
		";
		$result=mysql_query($query);
		
		$dd="
			<select name='formulation_id' onchange=xajax_displayformulationonchange(this.value); >
				<option value=''>Select Formulation</option>
		";		
		
		while($r=mysql_fetch_assoc($result)){
			if($formulation_id==$r['formulation_id']){
				$dd.="
					<option value='$r[formulation_id]' selected='selected'>$r[formulationcode] - $r[description]</option>
				";
			}else{
				$dd.="
					<option value='$r[formulation_id]'>$r[formulationcode] - $r[description]</option>
				";
			}
		}
		$dd.="
			</select>
		";
		
		$content="
			<div style='display:inline-block; margin-right:20px;'>
                <div>Formulation : </div>        
                <div>$dd</div>
			</div>   
			<input type='submit' name='b' value='Submit' />
		";
			
		$objResponse->assign('formulationdiv','innerHTML',$content);
		return $objResponse;
		
	}else if($_REQUEST[query]=="computeBalance"){
		
		echo $_REQUEST[expected];
		
/*		$inventorybalance=$_REQUEST[inventorybalance];//excess used
		$expectedoutput=$_REQUEST[expected];
		$typeofpackage=$_REQUEST[typeofpackage];
		
		$package_qty=$options->getPackageQty($typeofpackage);
		
		$inventorybalanceqty=$package_qty*$inventorybalance;
				
		$expectedoutput+=$inventorybalanceqty;
		
		echo $expectedoutput;
*/	
	}
	
	/************************
	STOCKS TRANSFER
	************************/
	else if($_REQUEST[query]=="queryStocksFromLocation"){
		
		$from_locale=$_REQUEST[from_locale];
		
		$content='<option value="">Select Stocks:</option>';
		
		/*Query All Stocks*/
		$query="
			select
				*
			from
				productmaster
			where
				type='RM'
		";
		
		$result=mysql_query($query);
		while($r=mysql_fetch_assoc($result)):
			/*Get Current Balance Of Stock*/
			$qty=$options->getCurrentBalanceOfStock($r[stock_id],NULL,$from_locale);
			
			if($qty>0){
				$content.="
					<option value='$r[stock_id]'>".$options->getMaterialName($r[stock_id])."</option>
				";
			}
		
		endwhile;
		
		echo $content;
	}
	
	else if($_REQUEST[query]=="queryStocksQuantity"){
		$stock_id=$_REQUEST[stock_id];
		$from_locale=$_REQUEST[from_locale];
		$qty=$options->getCurrentBalanceOfStock($stock_id,NULL,$from_locale);
		echo $qty;
	}
	
	else if($_REQUEST[query]=="addStockTransferDetail"){
		$transfer_hdr_id=$_REQUEST[transfer_hdr_id];
		$stock_id=$_REQUEST[stock_id];
		$qty=$_REQUEST[qty];
		$typeofpackage=$_REQUEST[typeofpackage];
		
		$query="
			insert into
				transfer_detail
			set
				transfer_hdr_id='$transfer_hdr_id',
				stock_id='$stock_id',
				qty='$qty',
				typeofpackage='$typeofpackage'
		";
		mysql_query($query);
		
		$content=$options->getUpdatedStockTransfer($transfer_hdr_id);
		
		echo $content;
	}
	
	else if($_REQUEST[query]=="removeStockTransferDetail"){
		$transfer_hdr_id=$_REQUEST[transfer_hdr_id];
		$transfer_det_id=$_REQUEST[transfer_det_id];
		
		$query="
			delete from
				transfer_detail
			where
				transfer_det_id='$transfer_det_id'
		";
		
		mysql_query($query);
		
		$content=$options->getUpdatedStockTransfer($transfer_hdr_id);
		
		echo $content;
	}
	else if($_REQUEST[query]=="refreshStockTransferDetails"){
		$transfer_hdr_id=$_REQUEST[transfer_hdr_id];
		$transfer_det_id=$_REQUEST[transfer_det_id];
		
		$content=$options->getUpdatedStockTransfer($transfer_hdr_id);
		
		echo $content;
	}
	/************************
	FINISHED PRODUCT CONVERSION
	************************/
	else if($_REQUEST[query]=="queryPackageType"){
		$finishedproduct_id=$_REQUEST[finishedproduct_id];
		$packagetype=$_REQUEST[packagetype];
		
		$query="
			select
				finishedproduct,
				typeofpackage,
				sum(packageqty) as totalqty
			FROM
				joborder_header
			WHERE
				finishedproduct='$finishedproduct_id'	
			group by finishedproduct,typeofpackage
		";				
		$result=mysql_query($query);		
		
		$content="<option value=''>Select Package:</option>";
		while($r=mysql_fetch_assoc($result)):
			if($packagetype==$r[typeofpackage]){
				$content.="
					<option value='$r[typeofpackage]' selected='selected' >".$options->getPackageName($r[typeofpackage])."</option>
				";
			}else{
				$content.="
					<option value='$r[typeofpackage]'>".$options->getPackageName($r[typeofpackage])."</option>
				";
			}
				
		endwhile;
		
		echo $content;
	}
	else if($_REQUEST[query]=="queryPackageQty"){
		$finishedproduct_id=$_REQUEST[finishedproduct_id];
		$packagetype=$_REQUEST[packagetype];
		$locale_id=$_REQUEST[locale_id];
		
		$query="
			select
				finishedproduct,
				typeofpackage,
				sum(packageqty) as totalqty
			FROM
				joborder_header
			WHERE
				finishedproduct='$finishedproduct_id'
			and
				typeofpackage='$packagetype'	
			and
				locale_id='$locale_id'
			and
				status!='C'
			group by finishedproduct,typeofpackage
		";				
		$result=mysql_query($query);		
		
		$r=mysql_fetch_assoc($result);
		
		$totalqty=$r[totalqty];
		
		/*SUBTRACT DELIVERY*/
		$query="
			SELECT
				sum(quantity) as totaldrqty
			FROM
				dr_header
			INNER JOIN dr_detail ON dr_header.dr_header_id = dr_detail.dr_header_id
			WHERE
				stock_id='$finishedproduct_id'
			AND
				status!='C'
			AND
				package_id='$packagetype'
			group by 
				stock_id,package_id
		";
		$result=mysql_query($query) or die(mysql_error());
		$r=mysql_fetch_assoc($result);
		
		$totaldrqty=$r[totaldrqty];
		
		/*SUBTRACT CONVERTED FINISHED PROUDCT*/
		$query="
			select
				sum(packqty) as totalconvertqty
			from
				product_convert
			where
				finishedproduct_id='$finishedproduct_id'
			and
				packagetype='$packagetype'
			and
				locale_id='$locale_id'
			and
				status!='C'
			group by
				finishedproduct_id,packagetype
		";
		$result=mysql_query($query) or die(mysql_error());
		$r=mysql_fetch_assoc($result);
		
		$totalconvertqty=$r[totalconvertqty];
		
		
		echo $totalqty-$totaldrqty-$totalconvertqty;
	}
	
	else if($_REQUEST[query]=="computeConversionQty"){
		$packqty=$_REQUEST[packqty];
		$packagetype=$_REQUEST[packagetype];
		
		$packagekilos=$options->getPackageQty($packagetype);		
		
		echo number_format(($packagekilos*$packqty),3,'.','');
	}
	
	else if($_REQUEST[query]=="getMaterialType"){
		$stock_id=$_REQUEST[stock_id];
		
		echo $options->getStockType($stock_id);
	}
	
	else if($_REQUEST[query]=="queryDeliveryAmount"){
		$dr_header_id=$_REQUEST[dr_header_id];
		
		$query="
			select
				netamount
			from
				dr_header
			where
				dr_header_id='$dr_header_id'
		";
		
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		echo $r[netamount];
	}
	
	else if($_REQUEST[query]=="addCustomerPaymentDetails"){
		
		$query="
				insert into 
					payment_detail
				set
					payment_header_id='$_REQUEST[pay_header_id]',
					dr_header_id='$_REQUEST[dr_header_id]',
					amount='$_REQUEST[amount]'
			";	
			
		mysql_query($query);
		
		
		//$content=$options->getUpdatedCustomerPaymentTable($_REQUEST[pay_header_id]);
		/*Update dr_header on add details*/
		
		$options->updateCustomerPaymentHeader($_REQUEST[pay_header_id]);
		
		echo $content;
		
	}
	
	else if($_REQUEST[query]=="addCheckDetail"){
		
		$date=explode("/",$_REQUEST['datecheck']);
		$date="$date[2]-$date[0]-$date[1]";		
		
		$query="
				insert into 
					pay_checks
				set
					pay_header_id='$_REQUEST[pay_header_id]',
					bank='$_REQUEST[bank]',
					checkno='$_REQUEST[checkno]',
					datecheck='$date',
					checkamount='$_REQUEST[checkamount]',
					checkstatus='$_REQUEST[checkstatus]'
			";	
			
		mysql_query($query) or die(mysql_error());
		
		$content=$options->getUpdatedCustomerPaymentTable($_REQUEST[pay_header_id]);
		
		$options->updateCustomerPaymentHeader($_REQUEST[pay_header_id]);
		
		echo $content;
		
	}
	
	else if($_REQUEST[query]=="refreshDeliveryDetails"){
		
		$content=$options->getDeliveryOptions();
		
		echo $content;
		
	}
	else if($_REQUEST[query]=="queryInputFieldsForStocksTransfer"){
		$stock_id=$_REQUEST[stock_id];
		
		$type=$options->getStockType($stock_id);
		
		if($type=="FP"){
			$content="
				<div class='inline'>
					<div>Type of Package : </div>        
					<div>".$options->getAllPackageOptions()."</div>
				</div> 
				
				<div class='inline'>
					<div>Quantity : </div>        
					<div><input type='text' class='textbox3' name='qty' id='qty' /></div>
				</div> 
			";	
		}else{
			$content="
				<div class='inline'>
					<div>Quantity : </div>        
					<div><input type='text' class='textbox3' name='qty' id='qty' /></div>
				</div> 
			";	
		}
		
		echo $content;
	}
	/****************************
	ACCOUNTING 	
	****************************/
	else if($_REQUEST[query]=="addGLTransacDetails"){
		
		$query="
			insert into
				gltran_detail
			set
				gchart_id='$_REQUEST[gchart_id]',
				description='$_REQUEST[description]',
				debit='$_REQUEST[debit]',
				credit='$_REQUEST[credit]',
				enable='$_REQUEST[enable]',
				gltran_header_id='$_REQUEST[gltran_header_id]'
		";
		mysql_query($query) or die(mysql_error());
		
	}
	
	else if($_REQUEST[query]=="updateGLTransacTable"){
		$content=$options->getUpdatedGLTransacTable($_REQUEST[gltran_header_id]);
		echo $content;
	}
	
	else if($_REQUEST[query]=="deleteGLTransacDetail"){
		$query="
			delete from
				gltran_detail
			where
				gltran_detail_id='$_REQUEST[gltran_detail_id]'
		";	
		mysql_query($query) or die(mysql_error());
	}
	
	
?>