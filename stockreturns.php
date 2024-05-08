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
	$b				= $_REQUEST['b'];

	$return_header_id	= $_REQUEST['return_header_id'];
	$date				= $_REQUEST['date'];	
	$account_id			= $_REQUEST['account_id'];
	$dr_header_id		= $_REQUEST['dr_header_id'];
	$dr_header_id_pad 	= (!empty($dr_header_id))?str_pad($dr_header_id,7,"0",STR_PAD_LEFT):"";
	$locale_id			= $_REQUEST['locale_id'];
	$user_id			= $_SESSION['userID'];
	$remarks 			= $_REQUEST['remarks'];
	
	$return_detail_id	= $_REQUEST['return_detail_id'];
	
	$stock_id			= $_REQUEST['stock_id'];
	$quantity			= $_REQUEST['quantity'];
	$srp				= $_REQUEST['srp'];
	$discount			= $_REQUEST['discount'];
	$sr_status			= $_REQUEST['sr_status'];
			
	/*UPDATE DELIVERY*/
	if($_REQUEST[b]=="Submit"){
		
		if(!empty($dr_header_id)){
		$query="
			insert into
				return_header
			set
				date='$date',
				account_id='$account_id',
				locale_id='$locale_id',
				user_id='$user_id',
				status='S',
				remarks='$remarks',
				dr_header_id='$dr_header_id'
		";	
		
		mysql_query($query);
		
		$return_header_id = mysql_insert_id();
		
		$x=0;
		foreach($stock_id as $id):
			$price_d 	= $srp[$x] - ($srp[$x] * $discount[$x] / 100);
			$amount		= $quantity[$x] * $price_d;
			
			
			if($quantity[$x] > 0){
				mysql_query("
					insert into
						return_details
					set
						return_header_id = '$return_header_id',
						quantity = '$quantity[$x]',
						srp = '$srp[$x]',
						amount = '$amount',
						stock_id = '$id',
						discount = '$discount[$x]',
						sr_status = '$sr_status[$x]'
				") or die(mysql_error());
			}
			$x++;
		endforeach;	
		
		$msg = "Transaction Saved";
		}else{
		$msg = "Please Enter DR #";
		}
		
	}else if($b=="Update"){
			$query="
				update
					return_header
				set
					date='$date',
					account_id='$account_id',
					locale_id='$locale_id',
					user_id='$user_id',
					status='S',
					remarks='$remarks',
					dr_header_id='$dr_header_id'
				where
					return_header_id = '$return_header_id'
			";	
			
			mysql_query($query);
			$msg="Transaction Updated";
		
	}else if($b=="Update Details"){
		
		$x=0;
		foreach($return_detail_id as $id):
			$price_d 	= $srp[$x] - ($srp[$x] * $discount[$x] / 100);
			$amount		= $quantity[$x] * $price_d;
			
			mysql_query("
				update
					return_details
				set
					quantity = '$quantity[$x]',
					sr_status = '$sr_status[$x]'
				where
					return_detail_id = '$id'
			") or die(mysql_error());
			
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				return_header
			set
				status='C'
			where
				return_header_id = '$return_header_id'
		";
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				return_header
			set
				status='F'
			where
				return_header_id = '$return_header_id'
		";
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	
	$query="
		select
			*
		from
			return_header
		where
			return_header_id='$return_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$status				= $r['status'];
	$date				= $r['date'];	
	$account_id			= $r['account_id'];
	$account_name		= (!empty($account_id))?$options->getAccountName($account_id):"";
	$dr_header_id		= $r['dr_header_id'];
	$dr_header_id_pad 	=(!empty($dr_header_id))?str_pad($dr_header_id,7,"0",STR_PAD_LEFT):"";
	$locale_id			= $r['locale_id'];
	$user_id			= $r['user_id'];
	$remarks 			= $r['remarks'];
	$order_header_id_pad = (!empty($order_header_id))?str_pad($order_header_id,7,"0",STR_PAD_LEFT):"";
?>

<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>STOCKS RETURN</div>
    <div class="module_actions">

        <input type="hidden" name="return_header_id" id="return_header_id" value="<?=$return_header_id;?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    	
    
		<div class='inline'>
        		Date: <br />
            	<input type="text" class="textbox3 required datepicker" name="date" readonly='readonly' value="<?=$date?>" >
        </div>    	
        
        <div class='inline'>
            <div>Account : </div>        
            <div>
                <input type="text" class="textbox" id="account_name" value="<?=$account_name?>" />
                <input type="hidden" name="account_id"  id="account_id" value="<?=$account_id?>" />
            </div>
        </div>   
        
        <div class='inline'>
        	<div>DR # : </div>        
            <div>
               	<input type="text" class="textbox3" name="dr_num" id="dr_num" value="<?=$dr_header_id_pad?>" />
                <input type="hidden" name="dr_header_id" id="dr_header_id" value="<?=$dr_header_id?>" />
            </div>
        </div>  
        
        <div class='inline'>
        	<div>Source Location: </div>        
            <div>
                <?php
					echo $options->getAllLocationOptions($locale_id);
				?> 
         	</div>
        </div>   

        <div>
        	<div>Remarks : </div>        
            <div>
            	<input type='text' name="remarks" id="remarks" class="textbox2" value="<?=$remarks;?>"/>
          	</div>
        </div> 
        
		<?php
		if(!empty($status)){
        ?>
        <div class='inline'>
        	<div>SR # : </div>        
            <div>
               	<input type="text" class="textbox3" name="drnum" readonly="readonly" value="<?=$dr_header_id_pad?>" />
            </div>
        </div>  
        
        <div class='inline'>
        	<div>Status : </div>        
            <div>
				<input type="text" readonly="readonly" value="<?=$options->getTransactionStatusName($status);?>" class="textbox3" />
            </div>
        </div> 
        
        <div class='inline'>
        	<div>User : </div>        
            <div>
            	<input type='text' class="textbox2" readonly="readonly" value="<?=$options->getUserName($user_id);?>"/>
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
        <input type="submit" name="b" id="b" value="Update Details" />
		<input type="submit" name="b" id="b" value="Finish" />
		
		<?php
		}else if(empty($status)){
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
		<input type="submit" name="b" id="b" value="Cancel" />
		<?php
		}
		?>   
 	</div>
   
  	<?php
	if($b == "Print Preview" && $return_header_id){
	?>
		<iframe id='JOframe' name='JOframe' frameborder='0' src='printDeliveryReceipt.php?id=<?=$dr_header_id?>' width='100%' height='500'></iframe>";

    <?php
	}else{
	?>
    <div style="float:left; width:100%; text-align:center;" id="table_container">
         <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20"><b>#</b></th>
                <th><b>Stock</b></th>
                <th><b>Quantity</b></th>
                <th><b>SRP</b></th>
                <th><b>Discount ( % )</b></th>
                <th><b>Price</b></th>
                <th><b>Amount</b></th>
                <th><b>Status</b></th>
            </tr> 
            <?php
            $query="
                select 
                    *
                from
                    return_details
                where
                    return_header_id='$return_header_id'
            ";
            $result=mysql_query($query) or die(mysql_error());
            
            $netamount=0;
            $grossamount=0;
            $totaldiscount=0;
            $i=1;
            while($r=mysql_fetch_assoc($result)):
                $return_detail_id	= $r[return_detail_id];
                $quantity			= $r[quantity];
                $stock_id			= $r[stock_id];
                $stock				= $options->stockAttr($stock_id,'stock');;
                $srp				= $r[srp];
                $discount			= $r[discount];
                $amount				= ( $srp - ($srp * ($discount / 100)) ) * $quantity;
				$sr_status			= $r[sr_status];
                
                $netamount+=$amount;
                $grossamount+=($r[srp]*$r[quantity]);
                
                $discount=($r[discount]/100)*$r[quantity]*$r[srp];
                $totaldiscount+=$discount;
            ?>
            <tr>				
                <td><?=$i++?></td>
                <td><?=$stock?></td>
                <td><div align="right"><input type="text" class="textbox3" name="quantity[]" value="<?=$quantity?>" ></div></td>
                <td><div align="right"><?=number_format($srp,2,'.',',')?></div></td>
                <td><div align="right"><?=$discount?></div></td>
                <td><div align="right"><?=number_format($price,2,'.',',')?></div></td>
                <td><div align="right"><?=number_format($amount,2,'.',',')?></div></td>
                <td><?=$options->getStockReturnStatusOptions($sr_status)?></td>
                
                <input type="hidden" name="return_detail_id[]" value="<?=$return_detail_id?>">
            </tr>
            <?php
            endwhile;
            ?>   
        </table>
        <table style="color:#F00; font-weight:bolder; width:100%;" >
            <tr>
                <td width="90%"><div align="right">Gross Amount:</div></td> 
                <td><div align="right"><?=number_format($grossamount,2,'.',',')?></div></td>
            </tr>
            <tr>
                <td width="90%"><div align="right">Total Discount:</div></td>
                <td><div align="right"><?=number_format($totaldiscount,2,'.',',')?></div></td>
            </tr>
            <tr>
                <td width="90%"><div align="right">Net Amount:</div></td>
                <td><div align="right"><?=number_format($netamount,2,'.',',')?></div></td>
            </tr>
        </table>
		<?php
        if($status=="S"){
            mysql_query("
                update
                    return_header
                set
                    totalamount = '$netamount'
                where
                    return_header_id = '$return_header_id'
            ") or die(mysql_error());	
        }
        ?>
    </div>
    <?php
	}
    ?>
</div>
</form>

<script type="text/javascript">
	j(function(){
		//xajax_refreshDR(xajax.getFormValues('header_form'));
	});
</script>
