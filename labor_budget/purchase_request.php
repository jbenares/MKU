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
				project_id				= '$project_id',
				description				= '$description',
				date					= '$date',
				date_needed				= '$date_needed',
				work_category_id 		= '$work_category_id',
				sub_work_category_id 	= '$sub_work_category_id',
				scope_of_work			= '$scope_of_work',
				user_id					= '$user_id',
				type					= 'labor'
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
		
	}
	
	else if($b=="Cancel"){
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
		
	}else if($b=="Delete"){
		
		header("Location: admin.php?view=$view");
		
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
	.results table td:nth-child(3),.results table th:nth-child(3){
		display:none;	
	}
<?php endif; ?>
</style>

<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE REQUEST | LABOR</div>
    
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
        	<input type="text" class="textbox3 datepicker required" name="date" value="<?=$date?>" />
        </div>
        
        <div class="inline">
        	Date Needed : <br />
        	<input type="text" class="textbox3 datepicker required" name="date_needed" value="<?=$date_needed?>" />
        </div>
        
        
        <div class='inline'>
            Project / Location : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" />
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
        <input type="submit" name="b" value="Unfinish" />
        <?php
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
                        *
                    from
                        labor_budget as l,						
						work_type as w,
						labor_budget_details as d
                    where
                                            
                        l.project_id = '$project_id'                    
                    and
                        l.work_category_id = '$work_category_id'
                    and
                        l.sub_work_category_id = '$sub_work_category_id'
					and
						l.id = d.labor_budget_id
					and
						d.work_code_id = w.work_code_id
					and
						d.is_deleted !='1'
                    
                ") or die(mysql_error());
                $rows = mysql_num_rows($result);
                if($rows > 0):
                ?>
                <h3><a href="#">LABOR SEARCH RESULTS</a></h3>
                <div>
	                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr bgcolor="#C0C0C0">				
                        <th width="20">#</th>
                        <th width="20" align="center"></th>
                        <th>Description</th>                        
                        <th width="100">Unit</th>
                        <th width="100">Qty</th>
						<th width="100">No. of Person</th>
                        <th width="100">Price per Unit</th>
						<th width="100">Total Qty</th>
						<th width="100">Total</th>
                    </tr> 
                    <?php
                    
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $labor_budget_details_id	= $r['id'];
						$labor_budget_id	= $r['labor_budget_id'];
                        $description			= $r['description'];                                          
                        $unit				= $r['unit'];
						$qty				= $r['qty'];
                       
						if($r['tag']==1){
							$price_per_unit				= $r['wt_price_per_unit'];
						}else{
							$price_per_unit				= $r['price_per_unit'];
						}
						$per = $r['no_per'];
						$t_q	=$r['total_qty'];
						$t_total = $t_q * $price_per_unit;
                        
                
                    ?>
                    <tr>
                        <td><?=$i++?></td>
                        <td><input type="button" value="REQUEST" onclick="xajax_pr_labor_form('<?=$labor_budget_details_id?>','<?=$pr_header_id?>');" /></td>
							<td><?=$description?></td>                                             
                            <td><?=$unit?></td>
							<td><?=$qty?></td>
							<td><?php
							if($unit!="Lot"){
								echo $per;
							}
							?></td>
                            <td><?=$price_per_unit?></td>
							<td><?=$t_q?></td>
							<td><?=number_format($t_total, 2)?></td>
                        <input type='hidden' name='labor_budget_id[]' value='<?=$labor_budget_id?>' />
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
            labor_budget_pr as l,
            labor_budget_details as d,
			work_type as w
        where
            l.labor_budget_details_id = d.id 
        and
            w.work_code_id = d.work_code_id		
		and
			l.is_deleted != '1'
		and
			d.is_deleted !='1'
		and
			l.pr_header_id = '$pr_header_id'
			
    ";
    $result=mysql_query($query) or die(mysql_error());
    $rows = mysql_num_rows($result);
    if($rows > 0):
    ?>    
        <div style="background-color::#FFF; font-weight:bolder; margin-top:10px;">PURCHASE REQUEST LABOR DETAILS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" id="search_table" style="border:1px solid #000;">
			<tr bgcolor="#333333">
				<td colspan="4"></td>
				<td colspan="6"><font color="#ffffff"><center><b>Budget</b></center></font></td>
				<td></td>
				<td colspan="4"><font color="#ffffff"><center><b>Requested</b></center></font></td>
				<td colspan="3"><font color="#ffffff"><center><b>Balance</b></center></font></td>
			</tr>
            <tr bgcolor="#C0C0C0" style="text-align:left;">
                <th width="20">#</th>
                <th width="20" align="center"></th>
				<th>Date Requested</th>
                <th>Description</th>
                <th width="60">Unit</th>
                <th width="60">Qty</th>
				<th width="60">No. of Person</th>
                <th width="100">Price/Unit</th>
				 <th width="100">Total Qty</th>
                <th width="100">Total</th>  
				<th width="20"></th>
				<th width="60">Qty</th>
				<th width="60">No. of Person</th>
				<th width="60">Total Qty</th>
                <th width="100">Amount</th>
                <th width="100">Total Qty</th>
				<th width="100">Amount</th>
            </tr>
            <?php
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $pr_lb_id			= $r['pr_lb_id'];
				$description			= $r['description'];
                $unit				= $r['unit'];
				$qty				= $r['qty'];
				$per 				= $r['no_per'];
				$req_t_q			= $r['total_req_qty'];
				$t_q				= $per*$qty;
                $qty				= $r['qty'];
				if($r['tag']==1){
						$price_per_unit				= $r['wt_price_per_unit'];
				}else{
						$price_per_unit				= $r['price_per_unit'];
				}
				$date_requested			= $r['date_requested']; 
				$dr = date("M d, Y",strtotime($date_requested));
				
				$t_total = $t_q* $price_per_unit;
				
				$req_qty				= $r['requested_qty'];
				$req_per = $r['requested_no_per'];
				$req_price = $req_t_q * $price_per_unit;
				$balqty = $t_q - $req_t_q;
				$balamt = $t_total - $req_price;
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <!--<td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&b=Delete"><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>!-->
					<td>
					<?php if($status == "F" || $status == "C"){ ?>
						
					<?php}else{?>
						<a href="#" onclick="xajax_delete_labor_pr('<?=$pr_lb_id?>','<?=$pr_header_id?>');" title="Delete Entry"><img src="images/trash.gif" border="0"></td>
                    <?php}?>
					<td><?=$dr?></td>
					<td><?=$description?></td>
                    <td><?=$unit?></td>
					<td><?=$qty?></td>
					<td><?=$per?></td>
                    <td><?=$price_per_unit?></td>
					<td><?=$t_q?></td>
					<td><?=number_format($t_total, 2)?></td>
					<td></td>
					<td><?=$req_qty?></td>
					<td><?=$req_per?></td>
					<td><?=$req_t_q?></td>
					<td><?=number_format($req_price, 2)?></td>
					<td><?=$balqty?></td>
					<td><?=number_format($balamt, 2)?></td>
                    <input type='hidden' name='pr_service_detail_id[]' value='<?=$pr_service_detail_id?>' />
                </tr>
            <?php
            }
            ?>
        </table>
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
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php if(!empty($status)){ ?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php } ?>
});
</script>
