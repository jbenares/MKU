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
	$project_id			= $options->getProjectIDFromPR($pr_header_id);
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";

	$b					= $_REQUEST['b'];
	$po_header_id		= $_REQUEST['po_header_id'];
	$pr_header_id		= $_REQUEST['pr_header_id'];
	$project_id			= $_REQUEST['project_id'];
	$supplier_id		= $_REQUEST['supplier_id'];
	$terms				= $_REQUEST['terms'];
	$type			= $_REQUEST['type'];
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	
	$checkList			= $_REQUEST['checkList'];
	
	$date				= $_REQUEST['date'];
	$user_id			= $_SESSION['userID'];
	
	$stock_id			= $_REQUEST['stock_id'];
	$quantity			= $_REQUEST['quantity'];
	$cost				= $_REQUEST['cost'];
	$discount			= $_REQUEST['discount'];
	$amount				= $_REQUEST['amount'];
	
	$gchart_id			= $_REQUEST['gchart_id'];

	$id	= $_REQUEST['id'];
	$pr_lb_id = $_REQUEST[pr_lb_id];
	
	if($b == "Undo"){
		$result = mysql_query("
			update po_header set approval_status = 'P' where po_header_id = '$po_header_id'
		") or die(mysql_error());
			if($result){
				//update labor_budget_details
				 foreach($pr_lb_id as $id){
					$q=mysql_query("select * from labor_budget_pr where pr_lb_id='$id'");
					$fe=mysql_fetch_assoc($q);
					$detid=$fe['labor_budget_details_id'];
					$reqty=$fe['requested_qty'];
					
					$cur=mysql_query("select * from labor_budget_details where id='$detid'");
					$fet=mysql_fetch_assoc($cur);
					$qty=$fet['qty'];
					
					$new_q = $qty+$reqty;
					
					//update budget
					mysql_query("update labor_budget_details set qty='$new_q' where id='$detid'");
				}
			}
		$msg = "Transaction brought back to PENDING";
	}
	
	if($b == "Add Details"){
		if($stock_id){
			mysql_query("
				insert into
					po_detail
				set
					po_header_id = '$po_header_id',
					stock_id = '$stock_id',
					quantity = '$quantity',
					cost = '$cost',
					discount = '$discount',
					amount = '$amount'
			") or die(mysql_error());
			
			$msg = "Transaction Added";
		}else{
			$msg = "Transaction Error";
		}
	}
	
	if($b == "M"){
		#INSERT AUDIT TRAIL
		$stock_id = $options->getAttribute("po_detail","po_detail_id",$id,"stock_id");
		$_header_id = $options->getAttribute("po_detail","po_detail_id",$id,"po_header_id");
		$stock		= $options->getAttribute("productmaster","stock_id",$stock_id,"stock");
		$_desc = $options->getUserName($user_id)." deleted $stock in PO# $_header_id on ".date("m/d/Y h:i:s A");
		$options->insertAuditTrail($_desc,$user_id,$_header_id,"PO");
		
		mysql_query("
			delete from
				po_detail
			where
				po_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Material Details Deleted";
	}else if($b=='SP'){
		mysql_query("
			delete from
				sub_spo_detail
			where
				sub_spo_detail_id = '$id'
		") or die(mysql_error());
		//delete from labor_budget_pr
		mysql_query("
					update labor_budget_pr set is_deleted='1' where pr_lb_id='".$_REQUEST[pr_lb_id]."'");
		
		$msg = "Labor Details Deleted";
	}else if($b == "S"){
		mysql_query("
			delete from
				po_service_detail
			where
				po_service_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Service Details Deleted";
	}else if($b == "E"){
		mysql_query("
			delete from
				po_equipment_detail
			where
				po_equipment_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Equipment Rentals Details Deleted";
	}else if($b == "F"){
		mysql_query("
			delete from
				po_fuel_detail
			where
				po_fuel_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Fuel, Oil, and, Lubricants Details Deleted";
	}
	
	
	else if($b=="Approve"){
		$query="
			update
				po_header
			set
				approval_status = 'A',
				approved_by = '$user_id'
			where
				po_header_id='$po_header_id'
			";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($po_header_id,'po_header_id','A');
		
		//update labor_budget_details
		/* foreach($pr_lb_id as $id){
			$q=mysql_query("select * from labor_budget_pr where pr_lb_id='$id'");
			$fe=mysql_fetch_assoc($q);
			$detid=$fe['labor_budget_details_id'];
			$reqty=$fe['requested_qty'];
			
			$cur=mysql_query("select * from labor_budget_details where id='$detid'");
			$fet=mysql_fetch_assoc($cur);
			$qty=$fet['qty'];
			
			$new_q = $qty-$reqty;
			
			//update budget
			mysql_query("update labor_budget_details set qty='$new_q' where id='$detid'");
		}*/
		$result = mysql_query("
			select
				sum(amount) as amount,
				date,
				terms,
				supplier_id
			from
				po_header as h, po_service_detail as d
			where
				h.po_header_id = d.po_header_id
			and
				h.po_header_id = '$po_header_id'
		") or die(mysql_error());
		
		$r=mysql_fetch_assoc($result);
		$date 			= $r['date'];
		$terms			= $r['terms'];
		$amount			= $r['amount'];
		$supplier_id	= $r['supplier_id'];
		
		$due_date = date("Y-m-d",strtotime("+ $terms days",strtotime($date)));
		
		if($amount > 0){
		
			mysql_query("
				insert into
					accounts_payable
				set
					header			= 'po_header_id',
					header_id 		= '$po_header_id',
					total_amount	= '$amount',
					due_date		= '$due_date',
					supplier_id		= '$supplier_id',
					status			= 'S',
					reference		= 'PO # : ".str_pad($po_header_id,7,0,STR_PAD_LEFT)."'
			") or die(mysql_error());
			
			$options->postServicePO($po_header_id,$gchart_id);
		}
		
		$msg = "Transaction Approved.";
		
	}if($b=="Disapprove"){
		$query="
			update
				po_header
			set
				approval_status = 'D'
			where
				po_header_id='$po_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($po_header_id,'po_header_id','D');
		$msg = "Transaction Disapproved.";
		
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
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= (!empty($project_id))?"$project_name - $project_code":"";
	$terms				= $r['terms'];

	$date				= $r['date'];
	$po_header_id_pad =	str_pad($po_header_id,7,"0",STR_PAD_LEFT);
	
	$pr_header_id 		= $r['pr_header_id'];
	$pr_header_id_pad 	=(!empty($pr_header_id))?str_pad($pr_header_id,7,"0",STR_PAD_LEFT):"";
	
	$supplier_id		= $r['supplier_id'];
	$supplier_name		= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
	
	$status			= $r['status'];
	
	$scope_of_work		= $r['scope_of_work'];
	$type				= $r['po_type'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	
	$work_category		= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
	
	$approval_status	= $r['approval_status'];
	$approved_by		= $r['approved_by'];

?>

<?php
if($approval_status != "P"){
?>
<style type="text/css">
	.display_table tr th:nth-child(2), .display_table tr td:nth-child(2){
		display:none;	
	}
</style>
<?php
}
?>


<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE ORDER</div>
    <div class="module_actions">
    
        <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
		<input type="hidden" name="type" value="<?=$type?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    
        <div class='inline'>
            Date : <br />
            <input type="text" name="date" id="date" class="textbox3 datepicker required" readonly="readonly" value="<?=$date;?>" title="Please Enter Date" />
        </div>    	
        
        <div class="inline">
        	PR # : <br />
        	<input type="text" class="textbox3" name="pr_header_id_display" id="pr_name" value="<?=$pr_header_id_pad?>"  />
            <input type="hidden" class="required" name="pr_header_id" id="pr_header_id" value="<?=$pr_header_id?> " title="Please Select PR#" />
            <img src="images/folder.png" id="folder" style="cursor:pointer;" />
        </div>
        <br />
        
        <div class="inline">
        	Supplier : <br />
            <input type="text" class="textbox" name="supplier_id_display" value="<?=$supplier_name?>" id="supplier_name" onclick="this.select();" />
            <input type="hidden" class="required" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
        </div>
        
        <div class="inline">
        	Project : <br />
            <input type="text" class="textbox" id="project_display" readonly="readonly" value="<?=$project_name_code?>" />
            <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" />
        </div>
        
        <br />
        
        <!--<div class="inline">
        	Scope of Work : <br />
            <input type="text" class="textbox" id="scope_of_work"  name="scope_of_work" value="<?=$scope_of_work?>" readonly="readonly" />
        </div>-->
        
        <div class="inline">
        	Work Category : <br />
            <input type="text" class="textbox" name="work_category" id="work_category" value="<?=$work_category?>"  readonly="readonly" />
            <input type="hidden" id="work_category_id" name="work_category_id" value="<?=$work_category_id?>"  />
        </div>
        
        <div class="inline">
        	Sub Work Category : <br />
            <input type="text" class="textbox" name="sub_work_category" id="sub_work_category" value="<?=$sub_work_category?>" readonly="readonly"  />
            <input type="hidden" name="sub_work_category_id" id="sub_work_category_id" value="<?=$sub_work_category_id?>" />
        </div>
        
        <br />
        <div class="inline" id="div_terms">
        	Terms ( Days ): <br />
            <input type="text" class="textbox" name="terms" id="term" value="<?=$terms?>"  />
        </div>
        
        <br />
        <?php
		if(!empty($status)){
        ?>
        <div class='inline'>
            <div>PO # : </div>        
            <div>
                <input type="text" readonly="readonly" value="<?=$po_header_id_pad?>" class="textbox3" />
            </div>
        </div>  
        
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" class="textbox3"/>
            </div>
        </div> 
        <br />
        
        <div class="inline">
        	Approval Status : <br />
            <input type="text" class="textbox" value="<?=$options->getApprovalStatus($approval_status)?>" readonly="readonly" />
        </div>
        <?php
		if(!empty($approved_by)){
        ?>
        <div class="inline">
        	Approved By : <br />
            <input type="text" class="textbox" value="<?=$options->getUserName($approved_by)?>" />
       	</div>
        <?php
		}
        ?>

        <?php
		}
        ?>
    </div>
	<?php if($approval_status != "P"){ ?>
    <!--<div class="module_actions">
    	<input type="submit" name="b" value="Undo"  />
    </div>-->
    <?php } ?>
    <?php
	if($approval_status == "P"){
	?>
    <div class="module_actions">
		SERVICE EXPENSE <span style="font-style:italic; font-size:8px;">( If there is a Service Entry )</span> :  <br />
        <?=$options->option_chart_of_accounts()?>
   	</div>
    <div class="module_actions">
	    <input type="submit" name="b" value="Update" onclick="return approve_confirm();" />
    	<input type="submit" name="b" value="Approve" onclick="return approve_confirm();" />
        <input type="submit" name="b" value="Disapprove" onclick="return approve_confirm();" />        
   	</div>
    <div style="margin:10px;">
        <div class="inline">
            Item : <br />
            <input type="text" class="textbox hinder-submit" name="stock_name" id="stock_name" onclick="this.select();" />
            <input type="hidden" name="stock_id" id="stock_id"  />
        </div>    
        <div class="inline">
            <div>Quantity : </div>        
            <div><input type="text" size="20" name="quantity" id="quantity" class="textbox3 hinder-submit" /></div>
        </div> 
        <div class="inline">
            <div>Unit: </div>        
            <div><input type="text" size="20" id="unit" readonly="readonly" class="textbox3 hinder-submit" /></div>
        </div> 
        <div class="inline">
            <div>Unit Price: </div>        
            <div><input type="text" size="20" name="cost" id="cost" class="textbox3 hinder-submit" /></div>
        </div> 
        <div class="inline">
            <div>Discount: </div>        
            <div><input type="text" size="20" name="discount" id="discount" class="textbox3 hinder-submit" /></div>
        </div> 
        <div class="inline">
            <div>Amount : </div>        
            <div><input type="text" size="20" name="amount" id="amount" class="textbox3 hinder-submit" readonly="readonly" /></div>
        </div> 
        <div style="margin-top:10px;" class="inline">
            <input type="submit" name="b" value="Add Details" />
        </div>
    </div>
    <?php
	}
	?>
   
    
   <?php
	if($b == "Print Preview" && $po_header_id){
	?>
		<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_po.php?id=<?=$po_header_id?>' width='100%' height='500'></iframe>";
    <?php
	}else{
	?>
	
    	<div id="tabs">
        	<ul>
					<!-- NEW CODE STARTS HERE !-->
				<li><a href="#tabs-0">LABOR</a></li>
					<!-- NEW CODE ENDS HERE !-->
                <li><a href="#tabs-1">MATERIALS</a></li>
                <li><a href="#tabs-2">SERVICES</a></li>
                <li><a href="#tabs-3">EQUIPMENT RENTALS</a></li>
                <li><a href="#tabs-4">FUEL, OIL, AND LUBRICANTS</a></li>
            </ul>
			<!-- NEW CODE STARTS HERE !-->
		<div id="tabs-0">
				<div class="module_title"><img src='images/database_table.png'>PURCHASE ORDER LABOR DETAILS : </div>
				<table cellspacing="2" cellpadding="5" width="100%" class="display_table" align="center" id="search_table" style="border:1px solid #000;">
				<tr bgcolor="#C0C0C0" style="text-align:left;">
					<th width="20">#</th>
					<th width="20" align="center"></th>
					<th>Description</th>
					<th width="60">Unit</th>
					<th width="60">Total Qty</th>
					<th width="100">Price/Unit</th>
					<th width="100">Total</th>
				</tr>
				<?php
					$query = "
						select
							*
						from
							spo_detail as sp,
							sub_spo_detail as ss
						where
							sp.po_header_id = '$po_header_id'
						and
							sp.spo_detail_id=ss.spo_detail_id
					";
					$result=mysql_query($query) or die(mysql_error());
				$i=1;
				while($r=mysql_fetch_assoc($result)){
					$pr_lb_id			= $r['sub_spo_detail_id'];
					$description		= $r['sub_description'];
					$unit				= $r['unit'];
					
					$price_per_unit				= $r['unit_cost'];
					 
					$dr = date("M d, Y",strtotime($date_requested));
					
					
					$req_qty	= $r['quantity'];
					$req_price = $req_qty * $price_per_unit;
					$t_total = $req_qty * $price_per_unit;
				?>
					<input type="hidden" name="pr_lb_id[]" value="<?=$r[pr_lb_id]?>">
					<tr>
						<td><?=$i++?></td>
						<td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$pr_lb_id?>&b=SP&pr_lb_id=<?=$r[pr_lb_id]?>" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
						<td><?=$description?></td>
						<td><?=$unit?></td>
						<td align="center"><?=number_format($req_qty,0,'.',',')?></td>
						<td><?=$price_per_unit?></td>
						<td><?=number_format($t_total, 2)?></td>
					</tr>
					<?php
					}
					?>
				</table>
			</div>
			<!-- NEW CODE ENDS HERE !-->
            <div id="tabs-1">
                <div class="module_title"><img src='images/database_table.png'>PURCHASE ORDER MATERIAL DETAILS : </div>
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"><!--<input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" />--></td>
                        <th>Item</th>
                        <th width="100">Quantity</th>
                        <th width="100">Cost</th>
                        <th width="100">Discount</th>
                        <th width="100">Amount</th>
                    </tr>
                    <?php
                    $query = "
                        select
                            po_detail_id,
                            d.stock_id,
                            stock,
                            d.quantity,
                            d.cost,
                            d.discount,
                            d.amount
                        from
                            po_detail as d,
                            productmaster as pm
                        where
                            d.po_header_id = '$po_header_id' 
                        and
                            pm.stock_id = d.stock_id
                    ";
                    $result=mysql_query($query) or die(mysql_error());
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $po_detail_id		= $r['po_detail_id'];
                        $stock_id			= $r['stock_id'];
                        $stock				= $r['stock'];
                        $quantity			= $r['quantity'];
                        $cost				= $r['cost'];
                        
                        $amount				= $r['amount'];

                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_detail_id?>&b=M" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='detail_quantity[]' value='<?=$quantity?>'  /></td>
                            <td class="align-right"><?=number_format($cost,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($r['discount'] * $quantity,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                            <input type='hidden' name='po_detail_id[]' value='<?=$po_detail_id?>' />
                        </tr>
                    <?php
                    }
                    ?>
                </table>   
           	</div>
    
    		<div id="tabs-2">
                <div class="module_title"><img src='images/database_table.png'>PURCHASE ORDER SERVICE DETAILS : </div>
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"><!--<input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" />--></td>
                        <th>Designation</th>
                        <th width="60">No</th>
                        <th width="60">No. of Days</th>
                        <th width="100">Rate/Day</th>
                        <th width="100">Amount</th>
                    </tr>
                    <?php
                    $query = "
                        select
                            *
                        from
                            po_service_detail as d,
                            productmaster as pm
                        where
                            d.po_header_id	= '$po_header_id' 
                        and
                            pm.stock_id = d.stock_id
                    ";
                    $result=mysql_query($query) or die(mysql_error());
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $po_service_detail_id		= $r['po_service_detail_id'];
                        $stock_id			= $r['stock_id'];
                        $stock				= $r['stock'];
                        $quantity			= $r['quantity'];
                        $days				= $r['days'];
                        $rate_per_day		= $r['rate_per_day'];
                        $unit				= $r['unit'];
                        $amount				= $r['amount'];
                    ?>
                        <tr>
                            <td><?=$i++?></td>
							<td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_service_detail_id?>&b=S" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_quantity[]' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_days[]' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                            <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                            <input type='hidden' name='po_service_detail_id[]' value='<?=$po_service_detail_id?>' />
                        </tr>
                    <?php
                    }
                    ?>
                </table>
           	</div>
            
            <div id="tabs-3">
                <div class="module_title"><img src='images/database_table.png'>PURCHASE ORDER EQUIPMENT RENTAL DETAILS : </div>
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                        <th>Description</th>
                        <th width="60">No</th>
                        <th width="60">No. of Days</th>
                        <th width="100">Rental/Day</th>
                        <th width="100">Amount</th>
                    </tr>
                    <?php
                    $query = "
                        select
                            *
                        from
                            po_equipment_detail as d,
                            productmaster as pm
                        where
                            d.po_header_id	= '$po_header_id' 
                        and
                            pm.stock_id = d.stock_id
                    ";
                    $result=mysql_query($query) or die(mysql_error());
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $po_equipment_detail_id		= $r['po_equipment_detail_id'];
                        $stock_id			= $r['stock_id'];
                        $stock				= $r['stock'];
                        $quantity			= $r['quantity'];
                        $days				= $r['days'];
                        $rate_per_day		= $r['rate_per_day'];
                        $unit				= $r['unit'];
                        $amount				= $r['amount'];
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_equipment_detail_id?>&b=E" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_quantity[]' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_days[]' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                            <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                            <input type='hidden' name='po_equipment_detail_id[]' value='<?=$po_equipment_detail_id?>' />
                        </tr>
                    <?php
                    }
                    ?>
                </table>
          	</div>
            
            <div id="tabs-4">
                <div class="module_title"><img src='images/database_table.png'>PURCHASE ORDER FUEL, OIL, AND LUBRICANTS DETAILS : </div>
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <th width="20" align="center"></th>
                        <th>Fuel</th>
                        <th>Equipment</th>
                        <th width="60">Consumption / Day</th>
                        <th width="60">No. of Days</th>
                        <th width="60">Quantity</th>
                        
                        <th width="60">Fuel Request</th>
                        <th width="60">Warehouse Request</th>
                        <th width="60">Total Fuel Quantity</th>
                        
                        <th width="60">Fuel Cost/Litter</th>
                        <th width="100">Amount</th>
                    </tr>
                    <?php
                    $query = "
                        select
							*
						from
							po_fuel_detail
						where
							po_header_id	= '$po_header_id' 
                    ";
                    $result=mysql_query($query) or die(mysql_error());
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                       $po_fuel_detail_id			= $r['po_fuel_detail_id'];
						$fuel_id					= $r['fuel_id'];
						$equipment_id				= $r['equipment_id'];
						$consumption_per_day		= $r['consumption_per_day'];
						$request_quantity			= $r['request_quantity'];
						$warehouse_quantity			= $r['warehouse_quantity'];
						
						$total_quantity			 	= ($request_quantity * $consumption_per_day * $days ) - $warehouse_quantity;
						
						$days						= $r['days'];
						$cost_per_litter			= $r['cost_per_litter'];
						$amount						= $r['amount'];
						
						$fuel		= $options->attr_stock($fuel_id,'stock');
						$equipment	= $options->attr_stock($equipment_id,'stock');
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><a href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&id=<?=$po_fuel_detail_id?>&b=F" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                            <td><?=$fuel?></td>
                            <td><?=$equipment?></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$consumption_per_day?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$days?>'  /></td>
                            
                            <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity * $consumption_per_day * $days?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$warehouse_quantity?>'  /></td>
                            <td><input type='text' class='textbox-int align-right' value='<?=$total_quantity?>'  /></td>
                            
                            
                            <td><input type='text' class='textbox-int align-right' value='<?=$cost_per_litter?>'  /></td>
                            <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
          	</div>
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
	j("#tabs").tabs();
	
	j("#cost,#quantity,#discount").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;
		var discount = document.getElementById("discount").value;
		
		var amount = (( price - discount ) * quantity);
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});
	
	j("#folder").dblclick(function(){
		xajax_show_purchase_request();
	});
});

</script>
	