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

	$packaging_id				= $_REQUEST['packaging_id'];
	$date						= $_REQUEST['date'];	
	$packaging_id_pad	 		= (!empty($packaging_id))?str_pad($packaging_id,7,"0",STR_PAD_LEFT):"";
	$user_id					= $_SESSION['userID'];
	
	$piece_stock_id				= $_REQUEST['piece_stock_id'];
	$piece_quantity				= $_REQUEST['piece_quantity'];
	$pack_stock_id				= $_REQUEST['pack_stock_id'];
	$pack_quantity				= $_REQUEST['pack_quantity'];
			
	/*UPDATE DELIVERY*/
	if($_REQUEST[b]=="Submit"){
		$query="
			insert into
				packaging
			set
				date			= '$date',
				user_id			= '$user_id',
				status			= 'S',
				piece_stock_id 	= '$piece_stock_id',
				piece_quantity 	= '$piece_quantity',
				pack_stock_id 	= '$pack_stock_id',
				pack_quantity	= '$pack_quantity'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$packaging_id	= mysql_insert_id();
		
		
		$msg = "Transaction Saved";		
	}else if($b=="Update"){
		$query="
			update
				packaging
			set
				date			= '$date',
				user_id			= '$user_id',
				status			= 'S',
				piece_stock_id 	= '$piece_stock_id',
				piece_quantity 	= '$piece_quantity',
				pack_stock_id 	= '$pack_stock_id',
				pack_quantity	= '$pack_quantity'
			where
				packaging_id	= '$packaging_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$msg="Transaction Updated";
	
	}else if($b=="Cancel"){
		$query="
			update
				packaging
			set
				status='C'
			where
				packaging_id = '$packaging_id'
		";
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($_REQUEST[b]=="Finish"){
		$query="
			update
				packaging
			set
				status='F'
			where
				packaging_id = '$packaging_id'
		";
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	
	$query="
		select
			*
		from
			packaging
		where
			packaging_id = '$packaging_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
		
	$date						= $r['date'];	
	$packaging_id_pad	 		= (!empty($packaging_id))?str_pad($packaging_id,7,"0",STR_PAD_LEFT):"";
	
	$piece_stock_id				= $r['piece_stock_id'];
	$piece_stock				= $options->attr_stock($piece_stock_id,'stock');
	$piece_quantity				= $r['piece_quantity'];
	$pack_stock_id				= $r['pack_stock_id'];
	$pack_stock					= $options->attr_stock($pack_stock_id,'stock');
	$pack_quantity				= $r['pack_quantity'];
	
	$user_id					= $r['user_id'];
	$status						= $r['status'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PACKAGING</div>
    <div class="module_actions">

        <input type="hidden" name="packaging_id" id="packaging_id" value="<?=$packaging_id;?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    	
    
		<div class='inline'>
        		Date: <br />
            	<input type="text" class="textbox3 required datepicker" name="date" readonly='readonly' value="<?=$date?>" >
        </div>   
        
        <div class='inline'>
        		Piece Item: <br />
            	<input type="text" class="textbox" id="piece_stock" value="<?=$piece_stock?>" />
                <input type="hidden" class="required" title="Please select a Piece Item" name="piece_stock_id" id="piece_stock_id" value="<?=$piece_stock_id?>" />
        </div> 
        
         <div class='inline'>
        		Pieces: <br />
            	<input type="text" class="textbox3" name="piece_quantity" value="<?=$piece_quantity?>" />
        </div> 
        
        <div class='inline'>
        		Pack Item: <br />
            	<input type="text" class="textbox" id="pack_stock" value="<?=$pack_stock?>" />
                <input type="hidden" class="required" title="Please select a Pack Item" name="pack_stock_id" id="pack_stock_id" value="<?=$pack_stock_id?>" />
        </div> 
        
         <div class='inline'>
        		Packaging Quantity: <br />
            	<input type="text" class="textbox3" name="pack_quantity" value="<?=$pack_quantity?>" />
        </div> 
         	
       
       	<br /> 
		<?php
		if(!empty($status)){
        ?>
        <div class='inline'>
        	<div>Packaging # : </div>        
            <div>
               	<input type="text" class="textbox3" readonly="readonly" value="<?=$packaging_id_pad?>" />
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
	if($b == "Print Preview" && $production_id){
	?>
		<!--<iframe id='JOframe' name='JOframe' frameborder='0' src='printDeliveryReceipt.php?id=<?=$dr_header_id?>' width='100%' height='500'></iframe>";-->
    <?php
	}else{
	?>
    <?php
	}
    ?>
</div>
</form>
