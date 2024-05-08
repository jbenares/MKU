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
	$preturn_header_id	= $_REQUEST['preturn_header_id'];
	
	$date				= $_REQUEST['date'];
	$account_id			= $_REQUEST['account_id'];
	$locale_id			= $_REQUEST['locale_id'];
	$user_id			= $_SESSION['userID'];
	
	if($b=="Submit"){
		$query="
			insert into
				preturn_header
			set
				date='$date',
				account_id='$account_id',
				locale_id='$locale_id',
				user_id='$user_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$preturn_header_id = mysql_insert_id();
		
		$msg = "Transaction Saved";		
		
	}else if($b=="Update"){
			
		$query="
			update
				preturn_header
			set
				date='$date',
				account_id='$account_id',
				locale_id='$locale_id',
				user_id='$user_id',
				status='S'
			where
				preturn_header_id='$preturn_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";		
		
	}else if($b=="Cancel"){
		$query="
			update
				preturn_header
			set
				status='C'
			where
				preturn_header_id='$preturn_header_id'
		";	
		mysql_query($query) or die(mysql_error());
	}
	else if($b=="Finish"){
		$query="
			update
				preturn_header
			set
				status='F'
			where
				preturn_header_id='$preturn_header_id'
		";	
		mysql_query($query) or die(mysql_error());
	}

	$query="
		select
			*
		from
			preturn_header
		where
			preturn_header_id='$preturn_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$date			= $r['date'];
	$account_id		= $r['account_id'];
	$account_name	= (!empty($account_id))?$options->getSupplierName($account_id):"";
	$locale_id		= $r['locale_id'];
	$user_id			= $r['user_id'];
	$user_name		= $options->getUserName($user_id);
	$preturn_header_id_pad =	str_pad($preturn_header_id,7,"0",STR_PAD_LEFT);
	$status			= $r['status'];
	

?>

<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE RETURNS</div>
    <div class="module_actions">
    
        <input type="hidden" name="preturn_header_id" id="preturn_header_id" value="<?=$preturn_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    
        <div class='inline'>
            <div>Date: </div>        
            <div>
                <input type="text" name="date" id="date" class="textbox3 datepicker" readonly="readonly" value="<?=$date;?>"/>
            </div>
        </div>    	
        
        <div class='inline'>
            <div>Supplier : </div>        
            <div>
                <input type="text" class="textbox" id="supplier_name" value="<?=$account_name?>" />
                <input type="hidden" name="account_id"  id="account_id" value="<?=$account_id?>" />
            </div>
        </div>   
         <div class='inline'>
            <div>Location: </div>        
            <div>
                <?php
                    echo $options->getAllLocationOptions($locale_id);
                ?> 
            </div>
        </div>      
        <br />
        <?php
		if(!empty($status)){
        ?>
        <div class='inline'>
            <div>PR # : </div>        
            <div>
                <input type="text" readonly="readonly" value="<?=$preturn_header_id_pad?>" class="textbox3" />
            </div>
        </div>  
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" class="textbox3"/>
            </div>
        </div> 
    
        <div class='inline'>
            <div>User : </div>        
            <div>
                <input type='text' class="textbox2" value="<?=$user_name?>"/>
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
	if($status=="S"){
    ?>
    <div class="module_actions">
        <div class="inline">
        	Item: <br />
        	<input type="text" class="textbox" name="stock_name" id="stock_name"  />
            <input type="hidden" name="stock_id" id="stock_id"  />
        </div>    
        <div id="package_field" class="inline">        	
        
       	</div> 	
        <div class="inline">
        	<div>Quantity : </div>        
            <div><input type="text" size="20" name="quantity" id="quantity" class="textbox3" /></div>
        </div> 
        <div class="inline">
        	<div>Cost : </div>        
            <div><input type="text" size="20" name="cost" id="cost" class="textbox3" /></div>
        </div>
        <div class="inline">
        	<div>Amount : </div>        
            <div><input type="text" size="20" name="amount" id="amount" class="textbox3" readonly="readonly"/></div>
        </div> 
        <input type="button" name="addButton" id="addButton" value="Add" onclick="xajax_addPReturnDetails(xajax.getFormValues('header_form'));" />    
    </div>
    <?php
	}
    ?>
    
   <?php
	if($b == "Print Preview" && $preturn_header_id){
	?>
		<iframe id='JOframe' name='JOframe' frameborder='0' src='printPO.php?id=<?=$preturn_header_id?>' width='100%' height='500'></iframe>";
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

<script type="text/javascript" src="scripts/script_po.js">	
</script>
<script type="text/javascript">
j(function(){
	xajax_refreshPReturnDetails(xajax.getFormValues('header_form'));
	
	j("#cost,#quantity").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;
		
		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
});

</script>
	