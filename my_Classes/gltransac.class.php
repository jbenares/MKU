<?php
	function checkPOAccount($form_data){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		$po_header_id	= $form_data['po_header_id'];
		
		$supplier_id   	= $options->getAttribute("po_header","po_header_id",$po_header_id,"supplier_id");
		$objResponse->script("
			var objSelect = document.getElementById(\"account_id\");
			setSelectedValue(objSelect,\"s-$supplier_id\");
		");
		
		return $objResponse;		
	}
	

	function printGLTransac($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printGeneralLedger.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("gltran_header_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function updateGLTransacStatus($id){
		$objResponse=new xajaxResponse();
		$options=new options();

		$query="
			select
				*
			from 
				gltran_header
			where
				gltran_header_id='$id'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$status=$r[status];
		$audit=$options->getAuditFromGLTransac($id);
		
		$datetoday=date("Y-m-d H:i:s");
		$audit.="Printed by: ".$options->getUserName($_SESSION[userID])."on $datetoday, ";
		
		if($status!='C'):
			$query="
				update
					gltran_header
				set
					status='P',
					audit='$audit'
				where
					gltran_header_id='$id'
			";
			mysql_query($query);
		endif;
		
		return $objResponse;
	}
	function refreshGLTable($gltran_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$content=$options->getUpdatedGLTransacTable($gltran_header_id);
		$objResponse->assign('table_container','innerHTML',$content);
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function addTransaction($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		/*if($form_data[enable]){
			$enable='Y';	
		}else{
			$enable='N';
		}*/
		$enable="Y";
		
		$query="
			insert into
				gltran_detail
			set
				gchart_id='$form_data[gchart_id]',
				description='$form_data[description]',
				debit='$form_data[debit]',
				credit='$form_data[credit]',
				enable='$enable',
				gltran_header_id='$form_data[gltran_header_id]',
				project_id = '$form_data[project_id]'
		";
		mysql_query($query) or die(mysql_error());
		
		$objResponse->script("xajax_refreshGLTable('$form_data[gltran_header_id]');");
		return $objResponse;
	}
	
	function removeParent($gltran_header_id,$gltran_detail_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		$objResponse->script("toggleBox('demodiv',1)");
		$query="
			delete from
				gltran_detail
			where
				gltran_detail_id='$gltran_detail_id'
		";	
		mysql_query($query) or die(mysql_error());
		
		$objResponse->script("xajax_refreshGLTable('$gltran_header_id');");
		return $objResponse;
	}	
	
	function importTransactions($form_data){
		
		$objResponse=new xajaxResponse();
		$options=new options();
		$objResponse->script("toggleBox('demodiv',1)");
		
		$gltran_header_id=$form_data[gltran_header_id];
		$journal_code=$options->getJournalCode($form_data[journal_id]);
		$fromdate=$form_data[fromdate];
		$todate=$form_data[todate];
		$date=$form_data['date'];
		
		if($journal_code=="AP"){
			/*
				SOURCE: FINISHED RECEIVING REPORT
				DEBIT : INVENTORY BASED ON CATEGORY - 
				CREDIT : ACCOUNTS PAYABLE - acode 3000
			*/
			$query="
				select 
					stock_id,
					amount,
					h.rr_header_id
				from
					rr_header as h,rr_detail as d
				where
					h.rr_header_id=d.rr_header_id
				and
					date between '$fromdate' and '$todate'
				and
					status='F'
				and
					h.rr_header_id not in 
				(
					select
						header_id as rr_header_id
					from	
						posted_headers
					where
						journal_code='AP'
				)
			";
			$result=mysql_query($query);
			$rm_acode=1600;
			$rm_total=0;
			$pm_total=0;
			
			while($r=mysql_fetch_assoc($result)){
				
				$stock_id=$r[stock_id];				
				$type=$options->getStockType($stock_id);
				$inventory_acode=$options->getInventoryACodeFromStockID($stock_id);
				
				if($type=="RM"){
					$rm_total+=$r[amount];
				}else if($type=="PM"){
					$pm_total+=$r[amount];
				}
				else if($type=="FP"){
					/*Finished Product - insert to AP and Inventory*/
					$options->insertIntoGLDetails($gltran_header_id,$inventory_acode,$r[amount],'');
					$options->insertIntoGLDetails($gltran_header_id,3000,'',$r[amount]);									
				}
			}
			if($rm_total)
			{
				$options->insertIntoGLDetails($gltran_header_id,$rm_acode,$rm_total,'');
				$options->insertIntoGLDetails($gltran_header_id,3000,'',$rm_total);				
			}
			if($pm_total){
				$options->insertIntoGLDetails($gltran_header_id,1610,$pm_total,'');
				$options->insertIntoGLDetails($gltran_header_id,3000,'',$pm_total);				
			}
			
			$options->postRR($gltran_header_id,$fromdate,$todate);
		}
		
		/*
			END OF AP
		*/
		
		/*
			START OF Disbursement Journal
		*/
		else if($journal_code=="DV"){
			/*
				SOURCE: RECEIVING REPORT
				DEBIT : ACCOUNTS PAYABLE
				CREDIT : CASH IN BANK
			*/
			
			$account_id=$raw_account_id=$form_data[account_id];
			$account_id=explode("-",$account_id);
			$account_id=$account_id[1];
			$date=$form_data['date'];
			
			$query="
				select 
					grossamount,
					rr_header_id				
				from
					rr_header
				where
					date between '$fromdate' and '$todate'
				and	
					status!='C'
				and	
					account_id='$account_id'
			";
			
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)){
				
				$payedamount=$options->getPayedAmountToAccountID($date,$raw_account_id,$r[rr_header_id]);
				$amount=$r[grossamount]-$payedamount;
				
				if($amount>0){
					$options->insertIntoGLDetails($gltran_header_id,3000,$amount,'',$r[rr_header_id]);
					$options->insertIntoGLDetails($gltran_header_id,1200,'',$amount);
				}
			}
		}
		/*
			END OF Disbursement Journal
		*/
		
		/*
			START OF Sales Journal
		*/
		
		else if($journal_code=="SJ"){
			/*
				SOURCE: FINISHED DELIVERY REPORT
				DEBIT : ACCOUNTS RECEIVABLES
				CREDIT : INCOME BASED ON CATEGORY
			*/
			
			$datetoday=date("Y-m-d H:i:s");
			$audit="Added by: ".$options->getUserName($_SESSION[userID])."on $datetoday, ";		
			
			$journal_id=$options->getJournalID("AP");
			$generalreference=$options->generateJournalReference($journal_id);
			$gl_account_id="s-".$_REQUEST[account_id]; //not yet used
				
			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$_REQUEST[date]',
					journal_id='$journal_id',
					audit='$audit',
					status='S',
					admin_id='$_SESSION[userID]'
			";	
			mysql_query($query) or die(mysql_error());
			
			$gltran_header_id=mysql_insert_id();
			
			$total_cash_amount=0;
			$total_charge_amount=0;
			
			$query="
				select 
					sum(netamount) as netamount
				from
					dr_header as h
				where
					date between '$fromdate' and '$todate'
				and
					status='F'
				and 
					paytype='C'
				and
					dr_header_id
				not in
				(
					select
						header_id as dr_header_id
					from
						posted_headers
					where
						journal_code='SJ'
				)
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)){
				$netamount		= $r[netamount];
				$total_cash_amount+=$netamount;
				
				if($netamount){
					$options->insertIntoGLDetails($gltran_header_id,1000,$netamount,'');				
				}
			}
			$query="
				select 
					sum(netamount) as netamount
				from
					dr_header as h
				where
					date between '$fromdate' and '$todate'
				and
					status='F'
				and 
					paytype='H'
				and
					dr_header_id
				not in
				(
					select
						header_id as dr_header_id
					from
						posted_headers
					where
						journal_code='SJ'
				)
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)){
				$netamount		= $r[netamount];
				$total_charge_amount+=$netamount;
				
				if($netamount){
					$options->insertIntoGLDetails($gltran_header_id,1500,$netamount,'');				
				}
			}
			
			$total_sales=$total_cash_amount+$total_charge_amount;
			if($total_sales){
				$options->insertIntoGLDetails($gltran_header_id,5000,'',$total_sales);		
			}
			
			$query="
				select 
					d.stock_id,
					p.type,
					p.cost,
					d.package_id,
					d.quantity
				from
					dr_header as h,dr_detail as d, productmaster as p
				where
					h.dr_header_id=d.dr_header_id
				and
					p.stock_id=d.stock_id
				and
					h.status='F'
				and
					h.date between '$fromdate' and '$todate'
				and
					h.dr_header_id
				not in
				(
					select
						header_id as dr_header_id
					from
						posted_headers
					where
						journal_code='SJ'
				)
			";
			
			$result=mysql_query($query);
			$rm_total=0;
			$pm_total=0;
			$fp_total=0;
			$fp_array=array();
			$total_costofgoodsold=0;
			while($r=mysql_fetch_assoc($result)){
				$stock_id		= $r[stock_id];
				$quantity		=$r[quantity];
				$cost			= $r[cost];
				$package_id		= $r[package_id];
				$packageqty		= $options->getPackageQty($package_id);
				$costofgoodsold = $cost * $packageqty * $quantity;
				$total_costofgoodsold+=$costofgoodsold;
				$type			= $r[type];
				$inv_acode		= $options->getInventoryACodeFromStockID($stock_id);
				
				//$objResponse->alert("$stock_id ; $inv_acode");
				if($type=="RM"){
					$acode=1600;
					$rm_total+=$costofgoodsold;
				}else if($type=="PM"){
					$acode=1610;
					$pm_total+=$costofgoodsold;
				}else if($type=="FP"){
					$acode=$inv_acode;
					$options->insertIntoGLDetails($gltran_header_id,$inv_acode,'',$costofgoodsold);				
				}
			}
			
			if($rm_total){
				$options->insertIntoGLDetails($gltran_header_id,1600,'',$rm_total);				
			}
			
			if($pm_total){
				$options->insertIntoGLDetails($gltran_header_id,1610,'',$pm_total);				
			}
			
			if($total_costofgoodsold){
				$options->insertIntoGLDetails($gltran_header_id,6000,$total_costofgoodsold,'');				
			}
			
			$options->postDR($gltran_header_id,$fromdate,$todate);
			
		}
		
		/*
			END OF AP
		*/
		
		/*
			START OF Cash Receipts Journal
		*/
		else if($journal_code=="CR"){
			/*
				SOURCE: COLLECTION
				DEBIT : CASH ON HAND
				CREDIT : ACCOUNTS RECEIVABLES
			*/
			$query="
				select 
					checkamount,
					h.pay_header_id
				from
					pay_header as h,pay_checks as c
				where
					h.pay_header_id=c.pay_header_id
				and
					date between '$fromdate' and '$todate'
				and	
					status!='C'
				and
					h.pay_header_id not in 
				(
					select
						header_id as pay_header_id
					from	
						posted_headers
					where
						journal_code='CR'
				)
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)){
				$options->insertIntoGLDetails($gltran_header_id,1000,$r[checkamount],'');
				$options->insertIntoGLDetails($gltran_header_id,1500,'',$r[checkamount]);
				
				mysql_query("
						insert into
							posted_headers
						set
							header_id='$r[pay_header_id]',
							journal_code='CR',
							gltran_header_id='$gltran_header_id'
					") or die(mysql_error());
			}
		}
		/*
			END OF Cash Receipts Journal
		*/
		
		
		$objResponse->script("xajax_refreshGLTable('$gltran_header_id');");

		return $objResponse;
	}
?>