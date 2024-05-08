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
	$order_header_id	= $_REQUEST['order_header_id'];
	$date				= $_REQUEST['date'];
	$account_id			= $_REQUEST['account_id'];
	$time				= $_REQUEST['time'];	
	$remarks			= $_REQUEST['remarks'];
	$user_id			= $_SESSION['userID'];
	
	$order_detail_id	= $_REQUEST['order_detail_id'];
	$quantity			= $_REQUEST['detail_quantity'];
	$price				= $_REQUEST['detail_price'];
	$stock_id			= $_REQUEST['detail_stock_id'];
	
	if($b=="Submit"){
		$query="
			insert into 
				order_header
			set
				date='$date',
				account_id='$account_id',
				time='$time',
				user_id='$user_id',
				status='S'
		";	
		
		mysql_query($query) or die(mysql_error());
		$order_header_id = mysql_insert_id();
		
		$msg = "Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				order_header
			set
				date='$date',
				account_id='$account_id',
				time='$time',
				user_id='$user_id',
				status='S',
				remarks = '$remarks'
			where
				order_header_id='$order_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$msg="Transaction Updated";

	}else if($b=="Cancel"){
		$query="
			update
				order_header
			set
				status='C'
			where
				order_header_id='$order_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				order_header
			set
				status='F'
			where
				order_header_id='$order_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Finished";
		
	}else if($b=="Update Details"){
		
		$x=0;
		
		foreach($order_detail_id as $id):
			$amount=$quantity[$x] * $price[$x];
			
			mysql_query("
				update
					order_details
				set
					quantity='$quantity[$x]',
					amount='$amount'
				where
					order_detail_id='$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}

	$query="
		select
			*
		from
			order_header
		where
			order_header_id='$order_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$date					= $r['date'];
	$order_header_id		= $r['order_header_id'];
	$order_header_id_pad	= ($order_header_id)?str_pad($order_header_id,7,"0",STR_PAD_LEFT):"";
		
	$date			= ($r['date']!="0000-00-00")?$r['date']:"";
	$account_id		= $r['account_id'];
	$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"";
	$time			= $r['time'];	
	$user_id		= $r['user_id'];
	$status			= $r['status'];
	$remarks		= $r['remarks'];
	

?>
<div class=form_layout>
	 <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>ORDER SHEET</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="order_header_id" id="order_header_id" value="<?=$order_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            <div>Date: </div>        
            <div>
                <input type="text" class="required textbox3 datepicker" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
            </div>
        </div>    
    
        <div class='inline'>
            <div>Account : </div>        
            <div>
                <input type="text" class="textbox" id="account_name" value="<?=$account_name?>" />
                <input type="hidden" name="account_id"  id="account_id" value="<?=$account_id?>" />
            </div>
        </div>   
    
        <div class='inline'>
            <div>Time to be delivered : </div>        
            <div>
                <input type="text" class="textbox3" name="time" value="<?=$time?>" />
            </div>
        </div>     
        
        <br />
        <div class='inline'>
            Remarks : <br />
	        <input type="text" class="textbox2" name="remarks" value="<?=$remarks?>" />
        </div>   
        <br />
        <?php
        if(!empty($status)){
        ?>
        <div class='inline'>
            <div>Order # : </div>        
            <div>
                <input type="text" class="textbox" value="<?=$order_header_id_pad?>" readonly="readonly"/>
            </div>
        </div> 
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>
        </div>     
    
        <div class='inline'>
            <div>User : </div>        
            <div>
                <input type='text' class="textbox2" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
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
            <input type="button" value="Print Order Sheet" onclick="printIframe('JOframe');" />
    
        <?php
        }
        if($status!="C"){
        ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php
        }
        ?>
    </div>
	<?php
	if( $status == 'S'):	
	?>
    <div class="module_actions">
        <?php
		?>     
        <div class="inline">
        	Item : <br />
            <input type="text" class="textbox" name="stock_name" id="stock_name_order"  onclick="this.select();" />
            <input type="hidden" name="stock_id" id="stock_id"  />
            
        </div>    
        <div class="inline">
        	<div>Quantity : </div>        
            <div><input type="text" size="20" name="quantity" id="quantity" class="textbox3"  /></div>
        </div> 
        <div class="inline">
        	<div>Price : </div>        
            <div id="price_div"><input type="text" size="20" name="price" id="price" class="textbox3" readonly="readonly" /></div>
        </div>
        <div class="inline">
        	<div>Amount : </div>        
            <div><input type="text" size="20" name="amount" id="amount" class="textbox3" readonly="readonly"/></div>
        </div> 
        <input type="button" name="addButton" id="addButton" value="Add" onclick="xajax_addOrderDetails(xajax.getFormValues('header_form'));" />
        <input type="submit" name="b" value="Update Details"  />
	 </div>        
    <?php
    endif;
    ?>

   
    
    <?php
	if($b == "Print Preview" && $order_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printOrder.php?id=$order_header_id' width='100%' height='500'>
		       	</iframe>";
	?>
    <?php
	}else{
	?>
    <div style="float:left; width:100%; text-align:center;" id="table_container">
    </div>
   	<?php
	}
    ?>
     </form>
    
</div>


<script type="text/javascript">
j(function(){	
	
	xajax_getUpdatedOrderTable('<?=$order_header_id?>');
	
	j("#quantity").keyup(function(){
		var price = document.getElementById("price").value;
		var quantity = document.getElementById("quantity").value;
		
		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
	
	j("#stock_name_order").autocomplete({
		source: "stocks.php?account_id="+document.getElementById("account_id").value,
		minLength: 2,
		select: function(event, ui) {
			j("#stock_name").val(ui.item.value);
			j("#stock_id").val(ui.item.id);
			j("#amount,#quantity").val("");
			j("#price").val(ui.item.stock_price);
		}
	});
	
});
</script>
	