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
	$project_id			= $_REQUEST['project_id'];
	$date			 	= $_REQUEST['date'];
	$cancellation_id	= $_REQUEST['cancellation_id'];
	$po_header_id		= $_REQUEST['po_header_id'];
	$justification		= $_REQUEST['justification'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$supplier_id		= $_REQUEST['supplier_id'];
	
	$user_id			= $_SESSION['userID'];
	$id					= $_REQUEST['id'];
	
	function getProjectName($project_id){
		$sql = mysql_query("select project_name from projects where project_id = '$project_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['project_name'];
	}
	
	function getSupplierName($supplier_id){
		$sql = mysql_query("select account from supplier where account_id = '$supplier_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['account'];
	}	
	
	function getWorkCategoryName($work_category_id){
		$sql = mysql_query("select work from work_category where work_category_id = '$work_category_id'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		return $r['work'];
	}	
	
	if($b == "Unfinish"){
		mysql_query("
		update po_cancellation 
		set status = 'S' 
		where 
		cancellation_id = '$cancellation_id'") or die(mysql_error());	
	}

	
	if($b=="Submit"){
		
		$sql = mysql_query("Select * from po_header where po_header_id = '$po_header_id' and status != 'C'") or die (mysql_error());
		$count = mysql_num_rows($sql);
		
		if($count > 0){
		$r = mysql_fetch_assoc($sql);
		
		$work = $r['work_category_id'];
		$project = $r['project_id'];
		$supplier = $r['supplier_id'];
		$po = $r['po_header_id'];
		
		$query="
			insert into 
				po_cancellation
			set
				supplier_id = '$supplier',
				project_id = '$project',
				date = '$date',
				work_category_id = '$work',
				po_header_id = '$po',
				justification = '$justification',
				userID = '$user_id',
				status = 'S'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$cancellation_id = mysql_insert_id();
		//$options->insertAudit($pr_header_id,'pr_header_id','I');		
		
		$msg="Transaction Saved";
		
		header("Location: admin.php?view=$view&cancellation_id=$cancellation_id");
		
		}else{
		$msg="No Matching PO Found!";			
		}
		
	}else if($b=="Update"){
		$query="
			update
			po_cancellation set
			date = '$date',
			supplier_id = '$supplier_id',
			project_id = '$project_id',
			work_category_id = '$work_category_id',
			justification = '$justification'
			where
			cancellation_id = '$cancellation_id';
		";	
		
		mysql_query($query) or die(mysql_error());
		//$options->insertAudit($pr_header_id,'pr_header_id','U');
		
		$msg = "Transaction Updated";
			
		header("Location: admin.php?view=$view&cancellation_id=$cancellation_id");
		
	}else if($b=="Cancel"){
		$query="
			update
				po_cancellation
			set
				status='C'
			where
				cancellation_id = '$cancellation_id'
		";	
		mysql_query($query);
		//$options->insertAudit($pr_header_id,'pr_header_id','C');
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				po_cancellation
			set
				status='F'
			where
				cancellation_id = '$cancellation_id'
		";	
		mysql_query($query);
		//$options->insertAudit($pr_header_id,'pr_header_id','F');
		
		$msg = "Transaction Finished";
		
	}else if($b=="New"){
		
		header("Location: admin.php?view=$view");
		
	}

	//show dets
	if($cancellation_id){
	
	$query="
		select 
		*
		from
		po_cancellation where cancellation_id = '$cancellation_id';
	";
	
	$result = mysql_query($query);
	$r = mysql_fetch_assoc($result);

	$cancellation_id	= $r['cancellation_id'];
	$po_header_id		= $r['po_header_id'];
	$date				= $r['date'];	
	$supplier_id		= $r['supplier_id'];
	$project_id			= $r['project_id'];	
	$work_category_id 	= $r['work_category_id'];	
	$justification 		= $r['justification'];	
	$user_id			= $r['userID'];
	$status				= $r['status'];
	
	$project_name		= getProjectName($project_id);
	$supplier			= getSupplierName($supplier_id);
	$work_category	 	= getWorkCategoryName($work_category_id);

	
	}
?>
<style type="text/css">
.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}	
<?php if($status == "F" || $status == "C"): ?>
	.results table td:nth-child(2),.results table th:nth-child(2){
		display:none;	
	}
<?php endif; ?>
</style>

<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>NEW CANCELLATION ORDER</div>
    	<div class="module_actions">
			<input type="hidden" name="view" value="<?=$view?>" />

			<div class="inline">
				Date: <br />
				<input type="text" class="textbox3 datepicker" name="date" value="<?=$date?>" readonly />
			</div>
			
			<div class='inline'>
				P.O. # / Subcon P.O. #<br />  
				<input type="number" class="textbox" id="po_header_id" name="po_header_id" value="<?=$po_header_id?>" />
			</div>   
			
			<?php if($cancellation_id){ ?>
			<div class='inline'>
				C.O. # <br />  
				<input type="text" class="textbox" <?phpif($cancellation_id){ ?> value="<?=str_pad($cancellation_id,7,0,STR_PAD_LEFT)?>" <?php } ?> readonly />
				<input type="hidden" class="textbox" id="cancellation_id" name="cancellation_id" value="<?=$cancellation_id?>" />
			</div>		
			<br />
			<div class='inline'>
				Supplier: <br />  
				<input type="text" class="textbox" value="<?=$supplier?>" readonly />
				<input type="hidden" class="textbox" id="supplier_id" name="supplier_id" value="<?=$supplier_id?>" />
			</div>					
			
			<div class='inline'>
				Project: <br />  
				<input type="text" class="textbox" value="<?=$project_name?>" readonly />
				<input type="hidden" class="textbox" id="project_id" name="project_id" value="<?=$project_id?>" />
			</div>	
			
			<div class='inline'>
				Scope of Work: <br />  
				<input type="text" class="textbox" value="<?=$work_category?>" readonly />
				<input type="hidden" class="textbox" id="work_category_id" name="work_category_id" value="<?=$work_category_id?>" />
			</div>

			<?php } ?>
			<br />
			
			<div class='inline'>
				Justification / Reason for Cancellation : <br />
				<textarea class="textarea_small" name="justification" id="justification" ><?=$justification?></textarea>
			</div>   
			
			
			
		</div>
		<div class="module_actions">
			<input type="submit" name="b" value="New" />
			<?php
			if($status=="S"){
			?>
			<input type="submit" name="b" id="b" value="Update" />
			<input type="submit" name="b" id="b" value="Finish" />
			<input type="submit" name="b" id="b" value="Cancel" />
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
				<input type="button" value="Print" onclick="printIframe('JOframe');" />
		
			<?php
			}
			if($status=="F" && !empty($status)){
			?>
			<input type="submit" name="b" id="b" value="Cancel" />
			<input type="submit" name="b" value="Unfinish" />
			<?php
			}
			?>
		</div>
</div>

    
<div style="clear:both">
    <?php
    if($b == "Print Preview" && $cancellation_id){
        echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_po_cancellation.php?id=$po_header_id&cancellation_id=$cancellation_id' width='100%' height='500'>
                </iframe>";
    ?>
    <?php
    }
    ?>
</div>
	    

</form>
