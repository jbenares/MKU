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

	$production_id			= $_REQUEST['production_id'];
	$date					= $_REQUEST['date'];	
	$production_id_pad	 	= (!empty($production_id))?str_pad($production_id,7,"0",STR_PAD_LEFT):"";
	$user_id				= $_SESSION['userID'];
	
	$stock_id					= $_REQUEST['stock_id'];
	$required					= $_REQUEST['required'];
	$actual						= $_REQUEST['actual'];
	$formulation_header_id		= $_REQUEST['formulation_header_id'];
	
	$orders						= $_REQUEST['orders'];
	$buffer						= $_REQUEST['buffer'];
	$beginning_balance			= $_REQUEST['beginning_balance'];
	
	
			
	/*UPDATE DELIVERY*/
	if($_REQUEST[b]=="Submit"){
		$query="
			insert into
				production
			set
				date='$date',
				user_id='$user_id',
				status='S',
				stock_id = '$stock_id',
				required ='$required',
				actual = '$actual',
				orders = '$orders',
				buffer = '$buffer',
				beginning_balance = '$beginning_balance'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$production_id	= mysql_insert_id();
		
		
		$msg = "Transaction Saved";		
	}else if($b=="Update"){
		$query="
			update
				production
			set
				date='$date',
				user_id='$user_id',
				stock_id = '$stock_id',
				required ='$required',
				actual = '$actual',
				orders = '$orders',
				buffer = '$buffer',
				beginning_balance = '$beginning_balance'
			where
				production_id = '$production_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		//INSERT FORMULATION
		//print_r($formulation_header_id);
		mysql_query("
				delete from
					production_formulations
				where
					production_id = '$production_id'
			") or die(mysql_error());
			
		foreach($formulation_header_id as $id){
			$result=mysql_query("
				insert into 
					production_formulations
				set
					production_id = '$production_id',
					formulation_header_id = '$id'
			") or die(mysql_error());
			
		}
		
		$msg="Transaction Updated";
	
	}else if($b=="Cancel"){
		$query="
			update
				production
			set
				status='C'
			where
				production_id = '$production_id'
		";
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($_REQUEST[b]=="Finish"){
		$query="
			update
				production
			set
				status='F'
			where
				production_id = '$production_id'
		";
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	
	$query="
		select
			*
		from
			production
		where
			production_id = '$production_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);

	$date						= $r['date'];	
	$production_id_pad	 		= (!empty($production_id))?str_pad($production_id,7,"0",STR_PAD_LEFT):"";
	
	$stock_id					= $r['stock_id'];
	$stock						= $options->attr_stock($stock_id,'stock');
	$required					= $r['required'];
	$actual						= $r['actual'];
	$formulation_header_id		= $r['formulation_header_id'];
	$orders						= $r['orders'];
	$buffer						= $r['buffer'];
	$beginning_balance			= $r['beginning_balance'];
	
	$user_id					= $r['user_id'];
	$status						= $r['status'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PRODUCTION</div>
    <div class="module_actions">

        <input type="hidden" name="production_id" id="production_id" value="<?=$production_id;?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    	
    
		<div class='inline'>
        		Date: <br />
            	<input type="text" id="date" class="textbox3 required datepicker" name="date" readonly='readonly' value="<?=$date?>" >
        </div>   
        
        <div class='inline'>
        		Item: <br />
            	<input type="text" class="textbox" id="stock_name" value="<?=$stock?>" />
                <input type="hidden" class="required" title="Please select an Item" name="stock_id" id="stock_id" value="<?=$stock_id?>" />
        </div> 
        <br />
        <div class="inline">
        	Beginning Balance: <br />
            <input type="text" class="textbox3" name="beginning_balance" readonly="readonly" id="beginning_balance" value="<?=$beginning_balance?>" />
        </div>
        
        <div class="inline">
        	Buffer: <br />
            <input type="text" class="textbox3" name="buffer" id="buffer" value="<?=$buffer?>" />
        </div>
        
        <div class="inline">
        	Orders: <br />
            <input type="text" class="textbox3" name="orders" id="orders" readonly="readonly" value="<?=$orders?>" />
        </div>
        
         <div class='inline'>
        		Required: <br />
            	<input type="text" class="textbox3" name="required" id="required" readonly="readonly" value="<?=$required?>" />
        </div> 
        
         <div class='inline'>
        		Acutal Output: <br />
            	<input type="text" class="textbox3" name="actual" value="<?=$actual?>" />
        </div> 
         	
       
       	<br /> 
		<?php
		if(!empty($status)){
        ?>
        <div class='inline'>
        	<div>Production # : </div>        
            <div>
               	<input type="text" class="textbox3" readonly="readonly" value="<?=$production_id_pad?>" />
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
    <?php
	$formulations = $options->getFormulationsFromProduction($production_id);
	
	if(!empty($status)):
    ?>
    <div class="module_actions">
    	Formulation:
        <div id="radio">
        <?php
		$result=mysql_query("
			select
				*
			from	
				formulation_header
			where
				status!='C'
			and
				product_id='$stock_id'
		") or die(mysql_error());
		
		$data  = array();
		while($r=mysql_fetch_assoc($result)){
			$d_formulation_header_id	= $r[formulation_header_id];
			$formulation_code 		= $r[formulation_code];
			$output					= $r[output];
			$modulo 				= $output % $required;
			
			$data[]= array(
						"formulation_header_id"	=> $d_formulation_header_id,
						"formulation_code" 		=> $formulation_code,
						"output"				=> $output,
						"modulo"				=> $modulo
					);
					
			
		}
			
		$modulos = array();
		 
		if($data){
			foreach ($data as $key => $row) {
				$modulos[] = $row['modulo'];
			}
			array_multisort($modulos, SORT_ASC, $data);
		}
		$x=1;
		foreach($data as $key => $row){
			if(count($formulations)==0){
				$checked = ($x==1)?"checked='checked'":"";
				if($x==1){
					mysql_query("
						insert into
							production_formulations
						set
							production_id = '$production_id',
							formulation_header_id = '$row[formulation_header_id]'
					") or die(mysql_error());	
				}
			}else{
				$checked = (in_array($row['formulation_header_id'],$formulations))?"checked='checked'":"";				
			}
			
		?>
            <input type="checkbox" id="radio<?=$x?>" name="formulation_header_id[]" value="<?=$row['formulation_header_id']?>" <?=$checked?> /><label for="radio<?=$x++?>"><?=$row['formulation_code']." - ".$row['output']?></label>
        <?php
		}
        ?>
        </div>	        
    </div>
    <?php
	endif;
    ?>
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

<script type="text/javascript">
	j(function(){
		j("#date,#stock_name").change(function(){
			xajax_getTotalOrders(xajax.getFormValues('header_form'));
			xajax_getBeginningBalance(xajax.getFormValues('header_form'));
		})
		j("#buffer").change(function(){
			xajax_solveRequired(xajax.getFormValues('header_form'));
		})
	});
</script>