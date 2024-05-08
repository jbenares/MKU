<?php
	$b				= $_REQUEST['b'];

	$date			= $_REQUEST['date'];	
	$account_id		= $_REQUEST['account_id'];
	$dr_header_id	= $_REQUEST['dr_header_id'];
	$paytype		= $_REQUEST['paytype'];
	$locale_id		= $_REQUEST['locale_id'];
	$user_id		= $_SESSION['userID'];
	$remarks 		= $_REQUEST['remarks'];


	
			
	/*UPDATE DELIVERY*/
	if($_REQUEST[b]=="Submit"){
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
				remarks='$remarks'
		";	
		
		mysql_query($query);
		
		$dr_header_id = mysql_insert_id();
		
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
					remarks='$remarks'
				where
					dr_header_id='$dr_header_id'
			";	
			
			mysql_query($query);
		
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
	$date			= $r['date'];	
	$account_id		= $r['account_id'];
	$dr_header_id	= $r['dr_header_id'];
	$dr_header_id_pad = str_pad($dr_header_id,7,"0",STR_PAD_LEFT);
	$paytype		= $r['paytype'];
	$locale_id		= $r['locale_id'];
	$user_id		= $r['user_id'];
	$remarks 		= $_REQUEST['remarks'];
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
	<div class="module_title"><img src='images/user_orange.png'>DELIVERY RECEIPT</div>
    <div class="module_actions">

        <input type="hidden" name="dr_header_id" id="dr_header_id" value="<?=$dr_header_id;?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    	
    
		<div class='inline'>
        		Date: <br />
            	<input type="text" class="textbox3 required" id='date' name="date" onclick='fPopCalendar("date");' readonly='readonly' value="<?=$date?>" >
        </div>    	
        <div class='inline'>
        	<div>Account : </div>        
            <div>
                <?php
					echo $options->getSpecificAccountOptions($account_id);
				?> 
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
		<input type="submit" name="b" id="b" value="Finish" />
		
		<?php
		}else if(empty($status)){
		?>
		<input type="submit" name="b" id="b" value="Submit" />
		<?php
		}
		
		if($b!="Print Preview"){
		?>
			<input type="submit" name="b" id="b" value="Print Preview" />
		<?php
		}
	
		if($b=="Print Preview"){
		?>	
			<input type="button" value="Print" onclick="printIframe('JOframe');" />
	
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
	if($status=="S") :
	?>         
    <div class="module_actions">
       
        <div style="display:inline-block; margin-right:20px;">
        	<div>Material : </div>        
            <div><?php echo $options->getAllMaterialOptions($r[dr_header_id],'stock_id'); ?></div>
        </div>  
        <div style="display:inline-block; margin-right:20px;">
        	<div>Quantity : </div>        
            <div style="display:inline-block;"><input type="text" size="12" name="quantity" id="quantity" class="textbox3" /></div>
        </div> 
        <div style="display:inline-block; margin-right:20px;">
        	<div>SRP : </div>        
            <div><input type="text" size="12" name="srp" id="srp" readonly="readonly" class="textbox3" /></div>
        </div>
        <div style="display:inline-block; margin-right:20px;">
        	<div>Discount (%): </div>        
            <div><input type="text" size="12" name="discount_detail" id="discount_detail" class="textbox3" /></div>
            
        </div> 
        <div style="display:inline-block; margin-right:20px;">
        	<div>Price : </div>        
            <div><input type="text" size="12" name="price" id="price" class="textbox3"  /></div>
        </div> 
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Amount : </div>        
            <div><input type="text" size="12" name="amount" id="amount" readonly="readonly" class="textbox3" /></div>
        </div> 
  		
        <input type="button" name="addButton" id="addButton" value="Add" />
         <div style="color:#F00;" id="currentbalance"></div>
    </div>
   	<?php
	endif;
	?>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
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
		xajax_refreshDR(xajax.getFormValues('header_form'));
	});
</script>
