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
	$b							= $_REQUEST['b'];
	$formulation_header_id		= $_REQUEST['formulation_header_id'];
	
	$formulation_code			= $_REQUEST['formulation_code'];
	$description				= $_REQUEST['description'];
	$date_created				= $_REQUEST['date_created'];
	$date_updated				= $_REQUEST['date_updated'];
	$main_id					= $_REQUEST['main_id'];
	$kilosperbag				= $_REQUEST['kilosperbag'];
	$output						= $_REQUEST['output'];
	$product_id					= $_REQUEST['product_id'];

	$user_id					= $_SESSION['userID'];
	$username					= $options->getUserName($user_id);
	
	if($b=="Submit"){
		$query="
			insert into
				formulation_header
			set
				formulation_code='$formulation_code',
				description='$description',
				date_created='".date("Y-m-d")."',
				date_updated='".date("Y-m-d")."',
				output='$output',
				user_id='$user_id',
				product_id='$product_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$formulation_header_id = mysql_insert_id();
				
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				formulation_header
			set
				formulation_code='$formulation_code',
				description='$description',
				date_updated='".date("Y-m-d")."',
				output='$output',
				user_id='$user_id',
				product_id='$product_id'
			where
				formulation_header_id='$formulation_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				formulation_header
			set
				status='C'
			where
				formulation_header_id='$formulation_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				formulation_header
			set
				status='F'
			where
				formulation_header_id='$formulation_header_id'
		";	
		mysql_query($query);
		
		$msg = "Transaction Finished";
		
	}else if($b=="Update Details"){
		$formulation_detail_id=$_REQUEST[formulation_detail_id];		
		$quantity		= $_REQUEST[quantity];
		
		$x=0;
		
		foreach($formulation_detail_id as $id):
			
			mysql_query("
				update
					formulation_details
				set
					quantity='$quantity[$x]'
				where
					formulation_detail_id='$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}

	$query="
		select
			*
		from
			formulation_header
		where
			formulation_header_id ='$formulation_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$date					= $r['date'];
	$formulation_header_id	= $r['formulation_header_id'];
	$po_header_id_pad		= ($formulation_header_id)?str_pad($formulation_header_id,7,"0",STR_PAD_LEFT):"";
		
	$formulation_code			= $r['formulation_code'];
	$description				= $r['description'];
	$date_created				= $r['date_created'];
	$date_updated				= $r['date_updated'];
	$output						= $r['output'];
	$product_id					= $r['product_id'];
	$product_name				= $options->getMaterialName($product_id);

	$user_id					= $r['user_id'];
	$status						= $r['status'];
	

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>FORMULATION</div>
    <form name="header_form" id="header_form" action="" method="post">
    <div class="module_actions">
        <input type="hidden" name="formulation_header_id" id="formulation_header_id" value="<?=$formulation_header_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class="inline">
        	Formulation Code : <br />
            <input type="text" class="textbox3" name="formulation_code" value="<?=$formulation_code?>" />
        </div>
        
        <div class="inline">
        	Item : <br />
            <input type="text" class="textbox" name="product_name" id="product_name" value="<?=$product_name?>" />
            <input type="hidden" name="product_id" id="product_id" value="<?=$product_id?>" class="required" title="Please Choose an Item" />
        </div>
        
        <div class="inline">
        	Description : <br />
            <input type="text" class="textbox2" name="description" value="<?=$description?>" />
        </div>
		<br />
        
        <div class="inline">
        	Output : <br />
            <input type="text" class="textbox3" name="output" value="<?=$output?>" />
        </div>
    
        
        <?php
        if(!empty($status)){
        ?>
        
        <div class='inline'>
            Date Created : <br />
                <input type="text" class="textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date_created?>">
        </div>    
        
        <div class='inline'>
            Date Updated : <br />
            <input type="text" class="textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date_updated?>">
        </div>    
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>
        </div> 
        <br />
        
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
    
        if($b=="Print"){
        ?>	
            <input type="button" value="Print RR" onclick="printIframe('JOframe');" />
    
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
	if( $status == 'S'):	
	?>
    <div class="module_actions">
		
        <?php
        	$js="
				onchange=xajax_displayPackageField(xajax.getFormValues('header_form'));
			";
		?>     
        <div class="inline">
        	Item : <br />
            <input type="text" class="textbox" name="stock_name" id="stock_name"  />
            <input type="hidden" name="stock_id" id="stock_id"  />
        </div>    
        <div id="package_field" class="inline">        	
        
       	</div> 	
        <div class="inline">
        	<div>Quantity : </div>        
            <div><input type="text" size="20" name="quantity" id="quantity" class="textbox3" /></div>
        </div> 
        
        <input type="button" name="addButton" id="addButton" value="Add" onclick="xajax_addFormulationDetail(xajax.getFormValues('header_form'));" />
        <input type="submit" name="b" value="Update Details"  />
   
    </div>
     <?php
    endif;
    ?>
    <?php
	if($b == "Print Preview" && $formulation_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printRR.php?id=$rr_header_id' width='100%' height='500'>
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
<script type="text/javascript" src="scripts/script_po.js">	
</script>
<script type="text/javascript">
j(function(){	
	xajax_getFormulationTable('<?=$formulation_header_id?>');
});

</script>
	