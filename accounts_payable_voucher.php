<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>
<?php
	$b						= $_REQUEST['b'];
	#HEADER
	$apv_header_id			= $_REQUEST['apv_header_id'];
	$po_header_id			= $_REQUEST['po_header_id'];
	$date 					= $_REQUEST['date'];
	$po_date				= $_REQUEST['po_date'];
	$project_id 			= $_REQUEST['project_id'];
	$work_category_id 		= $_REQUEST['work_category_id'];
	$sub_work_category_id 	= $_REQUEST['sub_work_category_id'];
	$supplier_id 			= $_REQUEST['supplier_id'];
	$terms 					= $_REQUEST['terms'];
	$discount_amount		= $_REQUEST['discount_amount'];
	$advance_payment_amount	= $_REQUEST['advance_payment_amount'];
	$remarks				= $_REQUEST['remarks'];
	
	$tax_gchart_id			= $_REQUEST['tax_gchart_id'];
	$vat_gchart_id			= $_REQUEST['vat_gchart_id'];
	$vatable				= $_REQUEST['vatable'];
	$w_tax					= $_REQUEST['w_tax'];
	
	$ppe_gchart_id			= $_REQUEST['ppe_gchart_id'];
	$ap_gchart_id			= $_REQUEST['ap_gchart_id'];
	$advances_gchart_id		= $_REQUEST['advances_gchart_id'];
	
	#DETAILS
	

	#OTHERS
	$user_id			= $_SESSION['userID'];	
	$checkList	= $_REQUEST['checkList'];
	
	function checkExistCV($apv_header_id){
		$c = 0;
		
		$q =  mysql_query("select * from cv_header as h, cv_detail as d
							where
							h.cv_header_id = d.cv_header_id and
							h.`status` != 'C' and
							d.apv_header_id = '$apv_header_id'") or die (mysql_error());
		$c = mysql_num_rows($q);

		if($c > 0){
			return true;
		}else{
			return false;
		}
	}
	
	if($b=="Update"){
		
			
		
		$vatable = ($vatable == "on")?1:0;
		
		$sqlcd = mysql_query("select
			sum(amount) as sum_amount
		 from
			  apv_detail
		 where
			apv_header_id = '$apv_header_id'") or die (mysql_error());
		$rcd = mysql_fetch_assoc($sqlcd);
		$sum_amount = $rcd['sum_amount'];
		
		if(!empty($ap_gchart_id) && !empty($ppe_gchart_id)){
		
			$msg = "<span style='color: red; font-size: 20px;'>TRANSACTION NOT SAVED! Choose Only 1 Account, A/P or P/E.</span>";
			
		}else{
			
			
			if($discount_amount >= $sum_amount){
				
			$query="
				update
					apv_header
				set
					date = '$date',
					tax_gchart_id = '$tax_gchart_id',
					vat_gchart_id = '$vat_gchart_id',
					vatable = '$vatable',
					w_tax = '$w_tax',
					ap_gchart_id = '$ap_gchart_id',
					ppe_gchart_id = '$ppe_gchart_id',
					discount_amount = '$sum_amount',
					remarks = '$remarks'
				where
					apv_header_id='$apv_header_id'
			";	
			}else{
			$query="
				update
					apv_header
				set
					date = '$date',
					tax_gchart_id = '$tax_gchart_id',
					vat_gchart_id = '$vat_gchart_id',
					vatable = '$vatable',
					w_tax = '$w_tax',
					ap_gchart_id = '$ap_gchart_id',
					ppe_gchart_id = '$ppe_gchart_id',
					remarks = '$remarks'
				where
					apv_header_id='$apv_header_id'
			";					
			}
		
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($apv_header_id,'apv_header_id','U');		
		
			$msg = "Transaction Updated";
		}	
		
	}else if($b=="Unfinish"){
		
		mysql_query("
			update apv_header set status = 'S' where apv_header_id = '$apv_header_id'
			") or die(mysql_error()); 
			
		mysql_query("
			update gltran_header set status = 'C' where header_id = '$apv_header_id' and header = 'apv_header_id'
			") or die(mysql_error()); 
			
		$msg = "Transaction Unfinished";	
		
	}else if($b=="Cancel"){
		
		$check_exist = checkExistCV($apv_header_id);
		
		if($check_exist == true){
			$msg = "Invalid Request, Check Voucher exist under this APV Reference!";
			
		}else{
			$query="
			update
				apv_header
			set
				status='C'
			where
				apv_header_id='$apv_header_id'
			";	
			
			
			mysql_query("
			update gltran_header set status = 'C' where header_id = '$apv_header_id' and header = 'apv_header_id'
			") or die(mysql_error()); 
			
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($apv_header_id,'apv_header_id','C');
			
			$msg = "Transaction Cancelled";
				
		}	
		
	}else if($b=="Finish"){
		
		if(empty($ap_gchart_id)){
			$msg = "ERROR! Please input A/P or P/E";	
			
		}else{
			$query="
				update
					apv_header
				set
					status='F'
				where
					apv_header_id='$apv_header_id'
			";	
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($apv_header_id,'apv_header_id','F');
			$options->postAPV($apv_header_id);
			
			$msg = "Transaction Finished";	
			
		}
		
	}

	$query="
		select
			*
		from
			apv_header
		where
			apv_header_id ='$apv_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$apv_header_id			= $r['apv_header_id'];
	$apv_header_id_pad		= str_pad($apv_header_id,7,0,STR_PAD_LEFT);
	$po_header_id			= $r['po_header_id'];
	$po_header_id_pad		= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$date 					= $r['date'];
	$po_date				= $r['po_date'];
	$project_id 			= $r['project_id'];
	$project				= $options->getAttribute('projects','project_id',$project_id,'project_name');
	$work_category_id 		= $r['work_category_id'];
	$work_category			= $options->getAttribute('work_category','work_category_id',$work_category_id,'work');
	$sub_work_category_id 	= $r['sub_work_category_id'];
	$sub_work_category		= $options->getAttribute('work_category','work_category_id',$sub_work_category_id,'work');
	$supplier_id 			= $r['supplier_id'];
	$supplier				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
	$terms 					= $r['terms'];
	$status					= $r['status'];
	$user_id				= $r['user_id'];
	$discount_amount		= $r['discount_amount'];
	$advance_payment_amount	= $r['advance_payment_amount'];
	$remarks				= $r['remarks'];
	$ap_gchart_id			= $r['ap_gchart_id'];
	$ppe_gchart_id			= $r['ppe_gchart_id'];
	$advances_gchart_id		= $r['advances_gchart_id'];
	
	$tax_gchart_id			= $r['tax_gchart_id'];
	$vat_gchart_id			= $r['vat_gchart_id'];
	$vatable				= $r['vatable'];
	$w_tax					= $r['w_tax'];
	
	//Advance Payment - based on EV_advance_payment - and default
	//$sqla = mysql_query("Select * from ev_advance_payment where po_header_id = '$po_header_id' and status = '0'") or die (mysql_error());
	//$ra = mysql_fetch_assoc($sqla);
	//$ap_amount = $ra['ap_amount'];
	
	if($discount_amount){
		$final_ap = $discount_amount;
	}else{
		$final_ap = $ap_amount;
	}
	
	
	//Advance Payment - RR
	/*$result1 = mysql_query("select * from rr_header where status != 'C' and po_header_id = '$po_header_id'") or die(mysql_error());
	while($r1 = mysql_fetch_assoc($result1)){
		$advance_payment_amount += $r1['advance_payment_amount'];
	}*/
	
	if($advance_payment_amount){
		
		//AP detail - Specific APV
		$sqlap = mysql_query("Select amount from apv_detail where apv_header_id = '$apv_header_id'") or die (mysql_error());
		while($rap = mysql_fetch_assoc($sqlap)){
			$total_rap += $rap['amount'];
		}
		
		//Remaining Advance Payment
		$sqlrap = mysql_query("Select apv_header_id from apv_header where po_header_id = '$po_header_id' and status != 'C'") or die (mysql_error());
		while($mapv = mysql_fetch_assoc($sqlrap)){
			$apv_header_r = $mapv['apv_header_id'];
				$rapv = mysql_query("Select amount from apv_detail where apv_header_id = '$apv_header_r'") or die (mysql_error());
				while($rtapv = mysql_fetch_assoc($rapv)){
					$total_remaining_ap += $rtapv['amount'];
				}
		}
			$remaining_ap = $advance_payment_amount - $total_remaining_ap;
	}
	
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>ACCOUNTS PAYABLE VOUCHER</div>
    
    <div style="width:50%; float:left;">
        <div class="module_actions">
            <input type="hidden" name="apv_header_id" id="apv_header_id" value="<?=$apv_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <div class='inline'>
                <div>Date: </div>        
                <div>
                    <input type="text" class="datepicker required textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
                </div>
            </div>    
                                
            <div class='inline'>
                <div>PO # : </div>        
                <div>
                    <input type="text" class="textbox3" value="<?=$po_header_id_pad?>" readonly="readonly"/>
                </div>
            </div>  
            
            <div class="inline">
                Supplier : <br />
                <input type="text" class="textbox" id="supplier_name" value="<?=$supplier?>" readonly="readonly" />
            </div>
            
            <div class='inline'>
                <div>Terms : </div>        
                <div>
                    <input type="text" class="textbox3" value="<?=$terms?>" readonly="readonly"/>
                </div>
            </div>  
            
            <br />
            <div class="inline">
                Project : <br />
                <input type="text" class="textbox" value="<?=$project?>" readonly="readonly" />
            </div>
            <div class="inline">
                Work Category: <br />
                <input type="text" class="textbox"  value="<?=$work_category?>" readonly="readonly" />
            </div>
            <div class="inline">
                Sub Work Category : <br />
                <input type="text" class="textbox" value="<?=$sub_work_category?>" readonly="readonly" />
            </div>
            
           <!-- <div class="inline">
            	<span style="color: red;">Advance Payment Amount : </span><br />
            	<input type="text" class="textbox" name="advance_payment_amount" value="<?=$advance_payment_amount?>" />
            </div>
			
            <!--<div class="inline">
            	Advance Payment Deducted : <br />
            	<input type="text" class="textbox" name="discount_amount" value="<?=$total_rap?>" readonly="readonly" />
            </div>
            
            <div class="inline">
            	Remaining Advance Payment : <br />
            	<input type="text" class="textbox" name="discount_amount" value="<?=number_format($remaining_ap,2)?>" readonly="readonly" />
            </div>
			-->
            <br />
            
            <div class="inline">
                Vatable: <br />
                <?php $checked = ($vatable)?"checked=\"checked\"" : "" ?>
                <input type="checkbox" name="vatable" <?=$checked?> />
            </div>
            
            <div class="inline">
                Witholding Tax (%) : <br />
                <input type="text" class="textbox3" name="w_tax" value="<?=$w_tax?>"/>
            </div>
            
            <div>
            	Remarks : <br />
            	<input type="text" class="textbox2" name="remarks" value="<?=$remarks?>" />
            </div>
            
            <!--<div class="inline">
                Witholding Tax Account : <br />
                <?=$options->option_chart_of_accounts($tax_gchart_id,'tax_gchart_id')?>
            </div>
            
            <div class="inline">
                VAT Account : <br />
                <?=$options->option_chart_of_accounts($vat_gchart_id,'vat_gchart_id')?>
            </div> -->
            <div>
                <span style="color: red;">A/P ACCOUNT : </span> <?=$options->getTableAssoc($r['ap_gchart_id'],'ap_gchart_id','Select A/P Account',"select * from gchart order by gchart asc",'gchart_id','gchart');?>
            </div>
			<!--<div>
                <span style="color: red;">Advance Payment Account : </span> <?=$options->getTableAssoc($r['advances_gchart_id'],'advances_gchart_id','Select Advances Payment Account',"select * from gchart order by gchart asc",'gchart_id','gchart');?>
            </div>-->
			<br />
            <div>
                <span style="color: red;">PROPERTY AND EQUIPMENT ACCOUNT: <em>(FOR ASSET ONLY)</em> : </span> <br /><?=$options->getTableAssoc($r['ppe_gchart_id'],'ppe_gchart_id','Select Property and Equipment Account (P/E)',"select * from gchart where parent_gchart_id = '56' or gchart_id = '56' order by gchart asc",'gchart_id','gchart');?>              	
            </div> 
            
            <?php
            if(!empty($status)){
            ?>
            <br />
            <div class="inline">
                APV # : <br />
                <input type="text" class="textbox3" value="<?=$apv_header_id_pad?>" readonly="readonly"/>
            </div>
            
            <div class='inline'>
                <div>Status : </div>        
                <div>
                    <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
                </div>
            </div> 
            
            <div class='inline'>
                Prepared by : <br />
                <input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($user_id)?>" readonly="readonly"/>
            </div> 
            <br />
            
            <!--<div class='inline'>
                <div>Encoded by : </div>        
                <div>
                    <input type='text' class="textbox2" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
                </div>
            </div>  -->
            <?php
            }
            ?>
        </div>
        <div class="module_actions">
            <!--<input type="submit" name="b" value="New" /> -->
            <?php
            if($status=="S"){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish" onclick="return approve_confirm();"/>
            
            <?php
            }else if($status!="F" && $status!="C"){
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php
            }
            
            
            if($b!="Print Preview" && !empty($status)){
            ?>
                <input type="submit" name="b" id="b" value="Print Preview" />
            <?php
            }
        
            if($b=="Print Preview"){
            ?>	
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
        
            <?php
            }
            if($status!="C" && !empty($status)){
            ?>
            <input type="submit" name="b" id="b" value="Cancel" onclick="return approve_confirm();" />
            <?php
            }
			
			if($status == "F"){
			?>
			 <input type="submit" name="b" id="b" value="Unfinish" onclick="return approve_confirm();" />
			<?php } ?>
        </div>
    </div>
    <div style="width:50%; float:right;">
        <div class="module_title"><img src='images/book_open.png'>VOUCHER DETAILS:  </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th>CODE</th>
                <th>DESCRIPTION</th>
                <th>MRR#</th>
                <th width="60">QTY</th>
                <th width="100">UOM</th>
                <th width="100">U.PRICE</th>
                <th width="100">Amount</th>
            </tr> 
            <?php
            $result=mysql_query("
                select
					*
				from
					apv_detail
				where
					apv_header_id = '$apv_header_id'
            ") or die(mysql_error());
            
            $i=1;
			$total_amount = 0;
            while($r=mysql_fetch_assoc($result)){
                $stock_id 		= $r['stock_id'];
                $quantity		= $r['quantity'];
				$price			= $r['price'];
				$amount			= $r['amount'];
				$apv_detail_id	= $r['apv_detail_id'];
				$rr_id			= $r['rr_id'];
				
				$mrr="";
				$rs = mysql_query("
					select	
						*
					from
						apv_mrr_detail as mrr, apv_detail as d
					where
						mrr.apv_detail_id = d.apv_detail_id
					and
						mrr.apv_detail_id = '$apv_detail_id'
				") or die(mysql_error());		
				while($r_mrr = mysql_fetch_assoc($rs)){
					$mrr.="$r_mrr[rr_header_id]<br>";		
				}
				
				$stock		= $options->getAttribute('productmaster','stock_id',$stock_id,'stock');
				$stockcode	= $options->getAttribute('productmaster','stock_id',$stock_id,'stockcode');
				$unit		= $options->getAttribute('productmaster','stock_id',$stock_id,'unit');
                
                $total_amount += $amount;
				$mrr = ($rr_id)?$rr_id:$mrr;
                
            ?>
            <tr>
                <td style="vertical-align:top;"><?=$i++?></td>
                <td style="vertical-align:top;"><?=$stockcode?></td>
                <td style="vertical-align:top;"><?=$stock?></td>
                <td style="vertical-align:top;"><?=$mrr?></td>
                <td style="vertical-align:top;" class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                <td style="vertical-align:top;"><?=$unit?></td>
                <td style="vertical-align:top;" class="align-right"><?=number_format($price,2,'.',',')?></td>
                <td style="vertical-align:top;" class="align-right"><?=number_format($amount,2,'.',',')?></td>
            </tr>
            <?php
            }
            ?>
        </table>
   	</div>
    <div style="clear:both;">
		<?php
        if($b == "Print Preview" && $apv_header_id){
    
            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_apv.php?id=$apv_header_id' width='100%' height='500'>
                    </iframe>";
        }
        ?>
   	</div>
     
    
</div>
</form>
<script type="text/javascript">
j(function(){	
	
	j("#cost,#quantity").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;
		
		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
	
	j("#folder").dblclick(function(){
		xajax_show_po();
	});
	
});

</script>
	