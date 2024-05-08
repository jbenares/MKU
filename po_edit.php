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
	$po_header_id	= $_REQUEST['po_header_id'];
	
	$date			= $_REQUEST['date'];
	$account_id		= $_REQUEST['account_id'];
	$paytype		= $_REQUEST['paytype'];
	$locale_id		= $_REQUEST['locale_id'];
	$userID			= $_SESSION['userID'];
	$terms			= $_REQUEST['terms'];
	
	
	

	if($b=="Submit"){
			
		$query="
			update
				po_header
			set
				date='$date',
				account_id='$account_id',
				paytype='$paytype',
				locale_id='$locale_id',
				userID='$userID',
				terms='$terms',
				status='S'
			where
				po_header_id='$_REQUEST[po_header_id]'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$po_header_id = mysql_insert_id();
		
		$msg = "Transaction Saved";		
		
	}else if($b=="Update"){
			
		$query="
			update
				po_header
			set
				date='$date',
				account_id='$account_id',
				paytype='$paytype',
				locale_id='$locale_id',
				userID='$userID',
				terms='$terms',
				status='S'
			where
				po_header_id='$_REQUEST[po_header_id]'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$po_header_id = mysql_insert_id();
		
		$msg = "Transaction Saved";		
		
	}else if($b=="Cancel"){
		$query="
			update
				po_header
			set
				status='C'
			where
				po_header_id='$po_header_id'
		";	
		mysql_query($query) or die(mysql_error());
	}
	else if($b=="Finish"){
		$query="
			update
				po_header
			set
				status='F'
			where
				po_header_id='$po_header_id'
		";	
		mysql_query($query) or die(mysql_error());
	}

	
	$query="
		select
			*
		from
			po_header
		where
			po_header_id='$po_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$date			= $r['date'];
	$account_id		= $r['account_id'];
	$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"";
	$paytype		= $r['paytype'];
	$locale_id		= $r['locale_id'];
	$userID			= $r['userID'];
	$user_name		= $options->getUserName($userID);
	$terms			= $r['terms'];
	

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
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE ORDER</div>
    <div class="module_actions">
    
        <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>" />
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
            <div>Account : </div>        
            <div>
                <input type="text" class="textbox" id="account_name" value="<?=$account_name?>" />
                <input type="hidden" name="account_id"  id="account_id" value="<?=$account_id?>" />
            </div>
        </div>   
         <div class='inline'>
            <div>Location: </div>        
            <div>
                <?php
                    echo $options->getAllLocationOptions($r[locale_id]);
                ?> 
            </div>
        </div>      
        <div class='inline'>
            <div>Pay Type : </div>        
            <div>
            <?php
                echo $options->getPayTypeOptions($r[paytype]);
            ?>
            </div>
        </div> 
        <div class="inline">
                Terms<br />
                <input type="text" class="textbox3" name="terms" value="<?=$r[terms]?>" />
        </div>
        
        <div class='inline'>
            <div>PO # : </div>        
            <div>
                <input type="text" readonly="readonly" value="<?=str_pad($r[po_header_id],7,"0",STR_PAD_LEFT)?>" />
            </div>
        </div>  
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" name="status" id="status" value="<?=$options->getTransactionStatusName($r[status])?>" readonly="readonly"/>
            </div>
        </div> 
        <br />
    
        <div class='inline'>
            <div>User : </div>        
            <div>
                <input type='text' class="textbox2" value="<?=$options->getUserName($r[userID]);?>"/>
            </div>
        </div> 
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
    <div class="module_actions">
        <?php
        	$js="
				onchange=xajax_displayPackageField(xajax.getFormValues('header_form'));
			";
		?>
                   
        <div class="inline">
        	<div>Material : </div>        
            <div><?php echo $options->getStockOptions(NULL,'stock_id',$js); ?></div>
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
        <input type="button" name="addButton" id="addButton" value="Add" onclick="xajax_addPODetails(xajax.getFormValues('header_form'));" />    
    </div>
    
   <?php
	if($b == "Print Preview" && $po_header_id){
	?>
		<iframe id='JOframe' name='JOframe' frameborder='0' src='printPO.php?id=<?=$po_header_id?>' width='100%' height='500'></iframe>";
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
	xajax_refreshPODetails(xajax.getFormValues('header_form'));
	
	j("#cost,#quantity").keyup(function(){
		xajax_solvePODetails(xajax.getFormValues('header_form'));
	});
	
	j("#package_id,#stock_id").keyup(function(){
		xajax_solvePODetails(xajax.getFormValues('header_form'));
	});
});

</script>
	