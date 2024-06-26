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
	$pr_header_id		= $_REQUEST['pr_header_id'];
	$project_id			= $_REQUEST['project_id'];
	$date			 	= $_REQUEST['date'];
	$date_needed		= $_REQUEST['date_needed'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $_REQUEST['description'];
	
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
		
	$stock_id			= $_REQUEST['stock_id'];
	$quantity			= $_REQUEST['quantity'];
	
	$pr_detail_id		= $_REQUEST['pr_detail_id'];
	$detail_quantity	= $_REQUEST['detail_quantity'];
	$checkList			= $_REQUEST['checkList'];
	
	$user_id			= $_SESSION['userID'];
	$id					= $_REQUEST['id'];
	
	$pr_detail_warehouse_quantity = $_REQUEST['pr_detail_warehouse_quantity'];
	$pr_detail_request_quantity = $_REQUEST['pr_detail_request_quantity'];
	$pr_detail_total_quantity	= $_REQUEST['pr_detail_total_quantity'];
	
	
	function checkExistPO($pr_header_id){
		$c = 0;
		
		$q =  mysql_query("select * from pr_header as h,
							po_header as p 
							where
							h.pr_header_id = p.pr_header_id and
							p.`status` != 'C' and
							h.pr_header_id = '$pr_header_id'") or die (mysql_error());
		$c = mysql_num_rows($q);

		if($c > 0){
			return true;
		}else{
			return false;
		}
	}
	
	if($b == "Unfinish"){
		mysql_query("update pr_header set status = 'S' where pr_header_id = '$pr_header_id'") or die(mysql_error());	
	}
	
	
	if( $b == "Update Warehouse Quantity" ){
		
		$x = 0;
		foreach($pr_detail_id as $id){
			$total_quantity = $pr_detail_request_quantity[$x] - $pr_detail_warehouse_quantity[$x];
			mysql_query("
				update
					pr_detail
				set
					warehouse_quantity = '$pr_detail_warehouse_quantity[$x]',
					quantity	= '$total_quantity'
				where
					pr_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		}
		$msg = "Updated Warehouse Quantity";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				pr_header
			set
				project_id           = '$project_id',
				description          = '$description',
				date                 = '$date',
				date_needed          = '$date_needed',
				work_category_id     = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				scope_of_work        = '$scope_of_work',
				user_id              = '$user_id',
				datetime_encoded     = '".lib::now()."'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$pr_header_id = mysql_insert_id();
		$options->insertAudit($pr_header_id,'pr_header_id','I');		
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				pr_header
			set
				project_id				= '$project_id',
				description				= '$description',
				date					= '$date',
				date_needed				= '$date_needed',
				work_category_id		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work',
				user_id 				= '$user_id'
			where
				pr_header_id='$pr_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($pr_header_id,'pr_header_id','U');
		
		$msg = "Transaction Updated";
		
	}else if($b=="Add Details"){
		/*
			CHECK IF IN BUDGET
		*/
		$in_budget	= $options->in_budget($project_id,$stock_id,$quantity);
		
		/*
			CHECK IF IN STOCK
		*/
		$in_stock	= $options->in_stock($date,$project_id,$stock_id);
		
		if($in_stock!='1' && $in_budget=='1'){
			$allowed = 1;
		}else{
			$allowed = 0;	
		}
		
		mysql_query("
			insert into
				pr_detail
			set	
				pr_header_id 		= '$pr_header_id',
				stock_id			= '$stock_id',
				quantity			= '$quantity',
				in_budget			= '$in_budget',
				in_stock			= '$in_stock',
				allowed				= '$allowed'
		") or die(mysql_error());
		
		$msg = "Transaction Added";
	
	}else if($b=="Update Details"){
		$x=0;
		foreach($pr_detail_id as $id):
			
			mysql_query("
				update
					budget_detail
				set
					quantity = '$detail_quantity[$x]'
				where
					budget_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Cancel"){
		
		$check_exist = checkExistPO($pr_header_id);
		
		if($check_exist == true){
			$msg = "Invalid Request, Purchase Order exist under this Purchase Request Reference!";
			
		}else{
		$query="
			update
				pr_header
			set
				status='C'
			where
				pr_header_id = '$pr_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($pr_header_id,'pr_header_id','C');
		
		$msg = "Transaction Cancelled";
		
		}	
		
	}else if($b=="Finish"){
		$query="
			update
				pr_header
			set
				status='F'
			where
				pr_header_id = '$pr_header_id'
		";	
		mysql_query($query);
		$options->insertAudit($pr_header_id,'pr_header_id','F');
		
		$msg = "Transaction Finished";
		
	}else if($b=="New"){
		
		header("Location: admin.php?view=$view");
		
	}else if($b=="M"){	
		mysql_query("
			delete from
				pr_detail
			where
				pr_detail_id = '$id'
		") or die(mysql_error());
	}else if($b=="S"){	
		mysql_query("
			delete from
				pr_service_detail
			where
				pr_service_detail_id = '$id'
		") or die(mysql_error());
	}
	else if($b=="E"){	
		mysql_query("
			delete from
				pr_equipment_detail
			where
				pr_equipment_detail_id = '$id'
		") or die(mysql_error());
	}
	else if($b=="F"){
		mysql_query("
			delete from
				pr_fuel_detail
			where
				pr_fuel_detail_id = '$id'
		") or die(mysql_error());
	}else if($b=="W"){	
		mysql_query("
			delete from
				pr_warehouse
			where
				pr_warehouse_id = '$id'
		") or die(mysql_error());
	}

	$query="
		select
			*
		from
			pr_header
		where
			pr_header_id ='$pr_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);

	$project_id			= $r['project_id'];
	$date				= $r['date'];
	$date_needed		= $r['date_needed'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $r['description'];
	$status				= $r['status'];
	$approval_status	= $r['approval_status'];
	$po_generated		= $r['po_generated'];
	$user_id			= $r['user_id'];
	$approved_by		= $r['approved_by'];
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
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
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE REQUEST</div>
    
    <div>
    	<div class="module_actions">
        <input type="hidden" name="pr_header_id" id="pr_header_id" value="<?=$pr_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class="inline">
        	Date Requested : <br />
        	<input type="text" class="textbox3 datepicker required" name="date" value="<?=$date?>" required />
        </div>
        
        <div class="inline">
        	Date Needed : <br />
        	<input type="text" class="textbox3 datepicker required" name="date_needed" value="<?=$date_needed?>" required />
        </div>
        
        
        <div class='inline'>
            Project / Location : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  required />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" required />
        </div>   
        
        <!--<div class="inline">
        	Scope of Work :
            <div id="div_scope_of_work">
            	<select class="select">
                	<option value="">Select Project First...</option>
                </select>
            </div>
        </div>-->
        
        <div class="inline">
        	Work Category : <br />
            <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
        </div>
        
        <div id="subworkcategory_div" style="display:none;" class="inline">
        	Sub Work Category :
			<div id="subworkcategory">
            	
            </div>
        </div>
        
        <div>
            Description : <br />
            <textarea class="textarea_small" name='description'><?=$description?></textarea>
        </div>          
        
        <?php
        if(!empty($status)){
        ?>
        
        <div class='inline'>
            PR #: <br />
	     	<input type="text" class="textbox" value="<?=str_pad($pr_header_id,7,0,STR_PAD_LEFT)?>" readonly="readonly"/>
        </div> 
        
        <div class='inline'>
            Requested by : <br />
	     	<input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($user_id)?>" readonly="readonly"/>
        </div> 
        
        <?php
		if(!empty($approved_by)){
        ?>
        <div class='inline'>
            Approved by : <br />
	     	<input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($approved_by)?>" readonly="readonly"/>
        </div> 
        <?php
		}
        ?>
        
        <div class='inline'>
            Status : <br />
	     	<input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
        </div> 
        <?php
        }
        ?>
    </div>
    
    	<div class="module_actions">
    	<input type="submit" name="b" value="New" />
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
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
    
        <?php
        }
        if($status!="C" && !empty($status)){
        ?>
        <input type="submit" name="b" id="b" value="Cancel" />
		
		<?php if($registered_access == ''){ ?>
			<input type="submit" name="b" value="Unfinish" />
		
        <?php
			}
        }
		?>
   	</div>
    
		<?php
        if($status=="S"){

        ?>
            <div id="accordion">
                <?php
                $result=mysql_query("
                    select
                        stock,
                        d.stock_id,	
						sum(quantity) as quantity,
                        unit,
						h.budget_header_id
                    from
                        budget_detail as d,
                        budget_header as h,
                        productmaster as pm
                    where
                        h.budget_header_id = d.budget_header_id
                    and
                        pm.stock_id = d.stock_id
					AND
						h.status != 'C'
                    and
                        h.project_id = '$project_id'
                    and
                        work_category_id = '$work_category_id'
                    and
                        sub_work_category_id = '$sub_work_category_id'
					group by
						stock_id
					order by stock asc
                    
                ") or die(mysql_error());
                
                $rows = mysql_num_rows($result);
                if($rows > 0):
                ?>
                <h3><a href="#">MATERIALS SEARCH RESULTS</a></h3>
                <div>
	                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr bgcolor="#C0C0C0">				
                        <th width="20">#</th>
                        <th width="20"></th>
                        <th>Item</th>
                        <th width="100">Budget</th>
                        <th width="100">Unit</th>
                        <th width="100">Total RTP</th>
                        <th width="100">Balance</th>
                        
                        
                        <!--<th>Warehouse Quantity</th>
                        <th>Project Warehouse Quantity</th>
                        <th>Issued Quantity</th>-->
                    </tr> 
                    <?php
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $stock_id 		= $r['stock_id'];
                        $stock			= $r['stock'];
                        $quantity		= $r['quantity'];
                        $unit			= $r['unit'];
                        
                        $total_rtp = $options->total_rtp($stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
                        $balance =  $quantity - $total_rtp;
                
                    ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td><input type="button" value="REQUEST" onclick="xajax_pr_stock_id_form('<?=$stock_id?>');" /></td>
                        <td><a href="print_budget_card.php?stock_id=<?=$stock_id?>&project_id=<?=$project_id?>&work_category_id=<?=$work_category_id?>&sub_work_category_id=<?=$sub_work_category_id?>" target="_blank"><?=$stock?></a></td>
                        <td class="align-right highlight"><?=number_format($quantity,2,'.',',')?></td>
                        <td><?=$unit?></td>
                        <td class="align-right highlight"><?=number_format($total_rtp,2,'.',',')?></td>
                        <td class="align-right highlight"><?=number_format($balance,2,'.',',')?></td>
                        
                    </tr>
                    <?php
                    }
                    ?>
                </table>
               	</div>
                <?php
                endif;
                ?>
                                               
                <?php
                $result=mysql_query("
                    select
                        stock,
                        d.stock_id,
                        quantity,
                        days,
                        rate_per_day,
                        amount
                    from
                        budget_equipment_detail as d,
                        budget_header as h,
                        productmaster as pm
                    where
                        h.budget_header_id = d.budget_header_id
                    and
                        pm.stock_id = d.stock_id
                    and
                        h.project_id = '$project_id'
                    and
                        h.scope_of_work = '$scope_of_work'
                    and
                        work_category_id = '$work_category_id'
                    and
                        sub_work_category_id = '$sub_work_category_id'
                    
                ") or die(mysql_error());
                $rows = mysql_num_rows($result);
                if($rows > 0):
                ?>
                <h3><a href="#">EQUIPMENT RENTALS SEARCH RESULTS</a></h3>
                <div>	
	                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr bgcolor="#C0C0C0">				
                        <th width="20">#</th>
                        <th width="20" align="center"></th>
                        <th>Description</th>
                        <th width="60">No</th>
                        <th width="100">No. of Days</th>
                        <th width="100">Rental/Day</th>
                        <th width="100">Amount</th>
                    </tr> 
                    <?php
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $budget_equipment_detail_id	= $r['budget_equipment_detail_id'];
                        $stock_id					= $r['stock_id'];
                        $stock						= $r['stock'];
                        $quantity					= $r['quantity'];
                        $days						= $r['days'];
                        $rate_per_day				= $r['rate_per_day'];
                        $unit						= $r['unit'];
                        $amount						= $r['amount'];
                        
                
                    ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td><input type="button" value="REQUEST" onclick="xajax_pr_equipment_stock_id_form('<?=$stock_id?>');" /></td>
                        <td><?=$stock?></td>
                        <td class="align-right"><?=$quantity?></td>
                        <td class="align-right"><?=$days?></td>
                        <td class="align-right"><?=$rate_per_day?></td>
                        <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                        <input type='hidden' name='budget_equipment_detail_id[]' value='<?=$budget_equipment_detail_id?>' />
                    </tr>
                    <?php
                    }
                    ?>
                </table>
               	</div>
                <?php
                endif;
                ?>
            
                <?php
                $result=mysql_query("
                    select
                        *
                    from
                        budget_fuel_detail as d,
                        budget_header as h
                    where
                        h.budget_header_id = d.budget_header_id
                    and
                        h.project_id = '$project_id'
                    and
                        h.scope_of_work = '$scope_of_work'
                    and
                        work_category_id = '$work_category_id'
                    and
                        sub_work_category_id = '$sub_work_category_id'
                    
                ") or die(mysql_error());
                $rows = mysql_num_rows($result);
                if($rows > 0):
                ?>
                
                <h3><a href="#">FUEL OIL LUBRICANTS SEARCH RESULTS </a></h3>
                <div>
                    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr bgcolor="#C0C0C0">				
                        <th width="20">#</th>
                        <th width="20" align="center"></th>
                        <th>Fuel</th>
                        <th>Equipment</th>
                        <th width="60">Consumption / Day</th>
                        <th width="60">Quantity</th>
                        <th width="100">No. of Days</th>
                        <th width="100">Fuel Cost/Litter</th>
                        <th width="100">Amount</th>
                    </tr> 
                    <?php
                    
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        
                        $budget_fuel_detail_id		= $r['budget_fuel_detail_id'];
                        $fuel_id					= $r['fuel_id'];
                        $equipment_id				= $r['equipment_id'];
                        $consumption_per_day		= $r['consumption_per_day'];
                        $quantity					= $r['quantity'];
                        $days						= $r['days'];
                        $cost_per_litter			= $r['cost_per_litter'];
                        $amount						= $r['amount'];
                        
                        $fuel		= $options->attr_stock($fuel_id,'stock');
                        $equipment	= $options->attr_stock($equipment_id,'stock');
                
                    ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td><input type="button" value="REQUEST" onclick="xajax_pr_fuel_stock_id_form('<?=$fuel_id?>','<?=$equipment_id?>');" /></td>
                        <td><?=$fuel?></td>
                        <td><?=$equipment?></td>
                        <td class="align-right"><?=$consumption_per_day?></td>
                        <td><?=$quantity?></td>
                        <td><?=$days?></td>
                        <td class="align-right"><?=$cost_per_litter?></td>
                        <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
                </div>
                <?php
                endif;
                ?>
           	</div>
        <?php
        }
        ?> 
    </div>
</div>
<div class="results">
    <?php
    $query = "
        select
            *
        from
            pr_detail as d,
            productmaster as pm
        where
            d.pr_header_id	= '$pr_header_id' 
        and
            pm.stock_id = d.stock_id
    ";
    $result=mysql_query($query) or die(mysql_error());
    $rows = mysql_num_rows($result);
    if($rows > 0):
    ?>
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE REQUEST MATERIAL DETAILS : <!--<input type="submit" name="b" value="Update Warehouse Quantity"  /> --></div>
        <div style="overflow:auto; border:1px solid #000;">
            <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                <tr>
                    <th width="20">#</th>
                    <th width="20" align="center"></th>
                    <th>Item</th>
                    <th width="60">Request Quantity</th>
                    <!--<th width="60">Warehouse Quantity</th>-->
                    <th width="60">Total Quantity</th>
                    <th width="100">Unit</th>
                   <!-- <th width="100">(Optional) Qty</th>
                    <th width="100">(Optional) Unit</th>
                    <th width="100">In-House Budget</th>
                    <th width="100">Actual Received</th>
                    <th width="100">Balance</th>-->
                    <th width="100">Status</th>
                </tr>
                <?php
                $i=1;
                while($r=mysql_fetch_assoc($result)){
                    $pr_detail_id		= $r['pr_detail_id'];
                    $stock_id			= $r['stock_id'];
                    $stock				= $r['stock'];
                    $unit				= $r['unit'];
                    $quantity			= $r['quantity'];
                    $request_quantity	= $r['request_quantity'];
                    //$warehouse_quantity = $r['warehouse_quantity'];
                    $in_stock			= $r['in_stock'];
                    $in_budget			= $r['in_budget'];
                    $allowed			= $r['allowed'];
                    $quantity2			= $r['quantity2'];
					$unit2				= $r['unit2'];
					$details			= $r['details'];
					
                    $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
                    $actual_received 			= $options->inventory_actual_received(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
                    $total_budget 				= $options->budget_stock($stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
                    $balance = $total_budget - $actual_received;
                ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_detail_id?>&b=M" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                        <td><?=$stock?> <?=($details)?"( $details )":""?></td>
                        <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>' name='pr_detail_request_quantity[]'  /></td>
                        <!--<td><input type='text' class='textbox-int align-right' value='<?=$warehouse_quantity?>' name='pr_detail_warehouse_quantity[]'  /></td>-->
                        <td><input type='text' class='textbox-int align-right' value='<?=$quantity?>' name='pr_detail_total_quantity[]'  /></td>
                        <td><?=$unit?></td>
                         <!--<td style="text-align:right;"><?=number_format($quantity2,4,'.',',')?></td>
                        <td><?=$unit2?></td>
						<td class="align-right"><?=number_format($total_budget,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($actual_received,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($balance,2,'.',',')?></td>-->
                        <td><?=$allowed_name?></td>
                        <input type='hidden' value='<?=$stock_id?>' />
                        <input type='hidden' name='pr_detail_id[]' value='<?=$pr_detail_id?>' />
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    <?php
    endif;
    ?>   
    
    <?php
    $query = "
        select
            *
        from
            pr_equipment_detail as d,
            productmaster as pm
        where
            d.pr_header_id	= '$pr_header_id' 
        and
            pm.stock_id = d.stock_id
    ";
    $result=mysql_query($query) or die(mysql_error());
    $rows = mysql_num_rows($result);
    if($rows > 0):
    ?>
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE REQUEST EQUIPMENT RENTAL DETAILS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
            <tr>
                <th width="20">#</th>
                <th width="20" align="center"></th>
                <th>Description</th>
                <th width="60">No</th>
                <th width="60">No. of Days</th>
                <th width="100">Rental/Day</th>
                <th width="100">Amount</th>
                <th width="100">Status</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $pr_equipment_detail_id		= $r['pr_equipment_detail_id'];
                $stock_id			= $r['stock_id'];
                $stock				= $r['stock'];
                $quantity			= $r['quantity'];
                $days				= $r['days'];
                $rate_per_day		= $r['rate_per_day'];
                $unit				= $r['unit'];
                $amount				= $r['amount'];
                $allowed			= $r['allowed'];
                
                $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_equipment_detail_id?>&b=E" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                    <td><?=$stock?></td>
                    <td><input type='text' class='textbox3 align-right' name='update_equipment_quantity[]' value='<?=$quantity?>'  /></td>
                    <td><input type='text' class='textbox3 align-right' name='update_equipment_days[]' value='<?=$days?>'  /></td>
                    <td><input type='text' class='textbox3 align-right' name='update_equipment_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                    <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    <td><?=$allowed_name?></td>
                    <input type='hidden' name='pr_equipment_detail_id[]' value='<?=$pr_equipment_detail_id?>' />
                </tr>
            <?php
            }
            ?>
        </table>
    <?php
    endif;
    ?>
    
    <?php
    $query = "
        select
            *
        from
            pr_fuel_detail
        where
            pr_header_id	= '$pr_header_id' 
    ";
    $result=mysql_query($query) or die(mysql_error());
    $rows = mysql_num_rows($result);
    if($rows > 0):
    ?>
        <!--FUEL OIL LUBRICANTS-->
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">FUEL OIL LUBRICANTS DETAILS : </div>
        <div style="overflow:auto;">
            <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
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
                <th width="100">Status</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $pr_fuel_detail_id	= $r['pr_fuel_detail_id'];
                $fuel_id					= $r['fuel_id'];
                $equipment_id				= $r['equipment_id'];
                $consumption_per_day		= $r['consumption_per_day'];
                $request_quantity			= $r['request_quantity'];
                $warehouse_quantity			= $r['warehouse_quantity'];
                $total_quantity			 	= $r['quantity'];
                
                $days						= $r['days'];
                $cost_per_litter			= $r['cost_per_litter'];
                $amount						= $r['amount'];
                $allowed					= $r['allowed'];
                
                $fuel		= $options->attr_stock($fuel_id,'stock');
                $equipment	= $options->attr_stock($equipment_id,'stock');
                
                $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&id=<?=$pr_fuel_detail_id?>&b=F" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                    <td><?=$fuel?></td>
                    <td><?=$equipment?></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$consumption_per_day?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$days?>'  /></td>
                    
                    <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity * $consumption_per_day * $days?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$warehouse_quantity?>'  /></td>
                    <td><input type='text' class='textbox-int align-right' value='<?=$total_quantity?>'  /></td>
                    
                    
                    <td><input type='text' class='textbox-int align-right' value='<?=$cost_per_litter?>'  /></td>
                    <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                    <td><?=$allowed_name?></td>
                    <input type='hidden' name='pr_fuel_detail_id[]' value='<?=$pr_equipment_detail_id?>' />
                </tr>
            <?php
            }
            ?>
        </table>
        </div>
    <?php
    endif;
    ?>
  
</div>
    
<div style="clear:both">
    <?php
    if($b == "Print Preview" && $pr_header_id){
        echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_purchase_request.php?id=$pr_header_id' width='100%' height='500'>
                </iframe>";
    ?>
    <?php
    }
    ?>
</div>
	    

</form>
<script type="text/javascript">
j(function(){	
	j("#accordion").accordion();

	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
		xajax_update_scope_of_work('<?=$project_id?>','<?=$scope_of_work?>');
	<?php
	}
	?>
});
</script>
