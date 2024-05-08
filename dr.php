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

	$date			= $_REQUEST['date'];	
	$account_id		= $_REQUEST['account_id'];
	$dr_header_id	= $_REQUEST['dr_header_id'];
	$order_header_id= $_REQUEST['order_header_id'];
	$order_header_id_pad = (!empty($order_header_id))?str_pad($order_header_id,7,"0",STR_PAD_LEFT):"";
	$paytype		= $_REQUEST['paytype'];
	$locale_id		= $_REQUEST['locale_id'];
	$user_id		= $_SESSION['userID'];
	$remarks 		= $_REQUEST['remarks'];
	$freight		= $_REQUEST['freight'];
	
	$order_detail_id	= $_REQUEST['order_detail_id'];
	$dr_detail_id		= $_REQUEST['dr_detail_id'];
	$quantity			= $_REQUEST['detail_quantity'];
	$price				= $_REQUEST['detail_price'];
	$srp				= $_REQUEST['detail_srp'];
	$stock_id			= $_REQUEST['detail_stock_id'];
	$discount			= $_REQUEST['detail_discount'];
			
	/*UPDATE DELIVERY*/
	if($_REQUEST[b]=="Submit"){
		
		if(!empty($order_header_id)){
			
		$time_entered = date("Y-m-d H:i:s");	
			
		$query="
			insert into
				dr_header
			set
				date='$date',
				account_id='$account_id',
				paytype='$paytype',
				locale_id='$locale_id',
				user_id='$user_id',
				status='S',
				remarks='$remarks',
				order_header_id='$order_header_id',
				freight = '$freight',
				time_entered = '$time_entered'
		";	
		
		mysql_query($query);
		
		$dr_header_id = mysql_insert_id();
		
		$x=0;
		foreach($stock_id as $id):
			$srp = $price[$x];
			$new_price = $srp - ( $srp * $discount[$x] / 100 );
			$amount=$quantity[$x] * $new_price;
			
			mysql_query("
				insert into
					dr_detail
				set
					dr_header_id='$dr_header_id',
					quantity='$quantity[$x]',
					srp='$srp',
					price='$new_price',
					amount='$amount',
					stock_id='$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Saved";
		}else{
		$msg = "Please Enter OS #";
		}
		
	}else if($b=="Update"){
			$query="
				update
					dr_header
				set
					date='$date',
					account_id='$account_id',
					paytype='$paytype',
					locale_id='$locale_id',
					user_id='$user_id',
					status='S',
					remarks='$remarks',
					order_header_id='$order_header_id',
					freight = '$freight'
				where
					dr_header_id='$dr_header_id'
			";	
			
			mysql_query($query);
			$msg="Transaction Updated";
		
	}else if($b=="Update Details"){
		
		$x=0;
		
		foreach($dr_detail_id as $id):
			$new_price = $srp[$x] - ( $srp[$x] * $discount[$x] / 100 );
			$amount = $quantity[$x] * $new_price;
			
			mysql_query("
				update
					dr_detail
				set
					quantity='$quantity[$x]',
					discount = '$discount[$x]',
					srp='$srp[$x]',
					price='$new_price',
					amount='$amount'
				where
					dr_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
		
	}else if($b=="Cancel"){
		$query="
			update
				dr_header
			set
				status='C'
			where
				dr_header_id='$dr_header_id'
		";
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($_REQUEST[b]=="Finish"){
		$query="
			update
				dr_header
			set
				status='F'
			where
				dr_header_id='$_REQUEST[dr_header_id]'
		";
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	
	$query="
		select
			*
		from
			dr_header
		where
			dr_header_id='$dr_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$status			= $r['status'];
	$freight		= $r['freight'];
	$date			= $r['date'];	
	$account_id		= $r['account_id'];
	$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"";
	$dr_header_id	= $r['dr_header_id'];
	$dr_header_id_pad = str_pad($dr_header_id,7,"0",STR_PAD_LEFT);
	$paytype		= $r['paytype'];
	$locale_id		= $r['locale_id'];
	$user_id		= $r['user_id'];
	$remarks 		= $r['remarks'];
	$order_header_id= $r['order_header_id'];
	$order_header_id_pad = (!empty($order_header_id))?str_pad($order_header_id,7,"0",STR_PAD_LEFT):"";
	$time_entered	= $r['time_entered'];
?>



<style type="text/css">
.search_table tr:hover td{
	background-color:#B5E2FE;
}
label.error { float: none; display:block; color: red; padding-left: .5em; vertical-align: top; }
.alignLeft
{
	text-align:left;	
}
#messageError{
	padding:0px 5px;
}

ul{
	list-style:square;	
	margin-left:20px;
}
</style>



<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>DELIVERY RECEIPT</div>
    <div class="module_actions">

        <input type="hidden" name="dr_header_id" id="dr_header_id" value="<?=$dr_header_id;?>" />
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
        <br />
        <div class='inline'>
        	<div>Order # : </div>        
            <div>
               	<input type="text" class="textbox3" name="order_num" id="order_num" value="<?=$order_header_id_pad?>" />
                <input type="hidden" name="order_header_id" id="order_header_id" value="<?=$order_header_id?>" />
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
        
        <div class='inline'>
        	<div>Pay Type : </div>        
            <div>
			<?php
				echo $options->getPayTypeOptions($paytype);
			?>
            </div>
        </div> 
	
    	<br />
    	<div class="inline">
        	Freight : <br />
            <input type="text" class="textbox3" name="freight" value="<?=$freight?>"  />
        </div>

        <div class="inline">
        	<div>Remarks : </div>        
            <div>
            	<input type='text' name="remarks" id="remarks" class="textbox2" value="<?=$remarks;?>"/>
          	</div>
        </div> 
        
        
        
		<?php
		if(!empty($status)){
        ?>
        
        <div class="inline">
        	<div>Time Entered: </div>        
            <div>
            	<input type='text' class="textbox" value="<?=$time_entered;?>" readonly="readonly"/>
          	</div>
        </div> 
        <br />
        <div class='inline'>
        	<div>DR # : </div>        
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
	if($b == "Print Preview" && $dr_header_id){
	?>
		<iframe id='JOframe' name='JOframe' frameborder='0' src='printDeliveryReceipt.php?id=<?=$dr_header_id?>' width='100%' height='500'></iframe>";

    <?php
	}else{
	?>
    <div style="float:left; width:100%; text-align:center;" id="table_container">
    </div>
    <?php
	}
    ?>
</div>
</form>

<script type="text/javascript" src="scripts/script_delivery.js"></script>
<script type="text/javascript">
	j(function(){
	<?php
	if(!empty($status)){
	?>
		xajax_refreshDR(xajax.getFormValues('header_form'));
	<?php
	}
	?>
	<?php
	if(!empty($status) && $status!='C'){
	?>
		xajax_deliveryStatus(xajax.getFormValues('header_form'));	
	<?php			
	}
	?>
	});
</script>
