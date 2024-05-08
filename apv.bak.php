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
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];
	
	$apv_header_id		= $_REQUEST['apv_header_id'];
	$date				= $_REQUEST['date'];
	
	
	if($b=="Update"){
			
		$query="
			update
				apv_header
			set
				date='$date'
			where
				apv_header_id='$apv_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($apv_header_id,'apv_header_id','U');
		
		$msg = "Transaction Updated";		
		
	}else if($b=="Cancel"){
		$query="
			update
				apv_header
			set
				status='C'
			where
				apv_header_id='$apv_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($apv_header_id,'apv_header_id','C');
	}
	else if($b=="Finish"){
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
		
	}

	
	$query="
		select
			*
		from
			apv_header
		where
			apv_header_id='$apv_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	

	$date				= $r['date'];
	$apv_header_id_pad =	str_pad($apv_header_id,7,"0",STR_PAD_LEFT);
	
	
	$supplier_id		= $r['supplier_id'];
	$supplier_name		= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
	
	$status			= $r['status'];

?>


<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>ACCOUNTS PAYABLE VOUCHER</div>
    
    <div class="module_actions">
    
        <input type="hidden" name="apv_header_id" id="apv_header_id" value="<?=$apv_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    
        <div class='inline'>
            Date : <br />
            <input type="text" name="date" id="date" class="textbox3 datepicker required" readonly="readonly" value="<?=$date;?>" title="Please Enter Date" />
        </div>    	
        
        <div class="inline">
            Supplier : <br />
            <input type="text" class="textbox" name="supplier_id_display" value="<?=$supplier_name?>" id="supplier_name" onclick="this.select();" readonly="readonly"/>
            <input type="hidden" class="required" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
        </div>
        
        <?php
        if(!empty($status)){
        ?>
        <div class='inline'>
            <div>APV # : </div>        
            <div>
                <input type="text" readonly="readonly" value="<?=$apv_header_id_pad?>" class="textbox3" />
            </div>
        </div>  
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" class="textbox3"/>
            </div>
        </div> 

        <?php
        }
        ?>
    </div>
    
    <div class="module_actions">
        <?php
        if($status=="S"){
        ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        
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
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php
        }
        ?>   
         
    </div>
    
    <?php
    $result=mysql_query("
		SELECT	
			stockcode,
			stock,
			reference,
			quantity,
			d.cost,
			amount,
			unit
		from
			apv_detail as apv, accounts_payable as ap, rr_header as h, rr_detail as d , productmaster as pm
		WHERE
			apv.ap_id = ap.ap_id		
		and
			ap.header_id = h.rr_header_id
		AND	
			h.rr_header_id = d.rr_header_id
		AND
			d.stock_id = pm.stock_id
		and
			apv.apv_header_id = '$apv_header_id'
		and
			ap.header = 'rr_header_id'
		
	") or die(mysql_error());
	
	?>

   	<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr>
                <th width="20">#</th>
                <th width="100">CODE</th>
                <th>DESCRIPTION</th>
                <th width="150">MRRs #</th>
                <th width="60">QTY</th>
                <th width="60">UOM</th>
                <th width="60">U.PRICE</th>
                <th width="60">AMOUNT</th>
            </tr>

			<?php           
           
            $i=1;
            while($r=mysql_fetch_assoc($result)){
				$stockcode 		= $r['stockcode'];
				$stock			= $r['stock'];
				$reference		= $r['reference'];
				$quantity		= $r['quantity'];
				$cost			= $r['cost'];
				$amount			= $r['amount'];
				$unit			= $r['unit'];
                
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=$stockcode?></td>
                    <td><?=$stock?></td>
                    <td><?=$reference?></td>
                    <td class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                    <td><?=$unit?></td>
                    <td class="align-right"><?=number_format($cost,2,'.',',')?></td>
                    <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                </tr>
            <?php
            }
            ?>
        </table>
        
        
    <div style="clear:both;">
		<?php
        if($b == "Print Preview" && $apv_header_id){
        ?>
            <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_apv.php?id=<?=$apv_header_id?>' width='100%' height='500'></iframe>";
        <?php
        }
        ?>
   	</div>
  
</div>
</form>

<script type="text/javascript" src="scripts/script_po.js">	
</script>
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
		xajax_show_purchase_request();
	});
});

</script>
	