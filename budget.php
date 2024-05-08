<style type="text/css">
	.ui-widget-header{
		padding:6px;
		margin-top:0px;
		margin-bottom:0px;
	}
	.ui-widget-header h3{
		padding:0px;
		margin:0px;	
	}
	.ui-widget-content{
		padding:0px;	
	}
	.ui-widget-content ul{
		margin-left:20px;
	}
</style>

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
	$budget_header_id	= $_REQUEST['budget_header_id'];
	$project_id			= $_REQUEST['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $_REQUEST['description'];
	$date				= $_REQUEST['date'];
	$scope_of_work		= $_REQUEST['scope_of_work'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	$remarks			= $_REQUEST['remarks'];
		
	$stock_id			= $_REQUEST['stock_id'];
	$quantity			= $_REQUEST['quantity'];
	$cost				= $_REQUEST['price'];
	$amount				= $_REQUEST['amount'];
	$detail_date		= $_REQUEST['detail_date'];
	
	$budget_detail_id	= $_REQUEST['budget_detail_id'];
	$update_quantity	= $_REQUEST['update_quantity'];
	$update_date		= $_REQUEST['update_date'];
	$checkList			= $_REQUEST['checkList'];
	
	$service_stock_id			= $_REQUEST['service_stock_id'];
	$service_quantity 			= $_REQUEST['service_quantity'];
	$service_rate_per_day		= $_REQUEST['service_rate_per_day'];
	$service_days				= $_REQUEST['service_days'];
	$service_amount				= $_REQUEST['service_amount'];
	
	
	$equipment_stock_id			= $_REQUEST['equipment_stock_id'];
	$equipment_quantity 		= $_REQUEST['equipment_quantity'];
	$equipment_rate_per_day		= $_REQUEST['equipment_rate_per_day'];
	$equipment_days				= $_REQUEST['equipment_days'];
	$equipment_amount			= $_REQUEST['equipment_amount'];

	
	$fuel_id				= $_REQUEST['fuel_id'];
	$equipment_id			= $_REQUEST['equipment_id'];
	$consumption_per_day	= $_REQUEST['consumption_per_day'];
	$fuel_quantity			= $_REQUEST['fuel_quantity'];
	$fuel_days				= $_REQUEST['fuel_days'];
	$fuel_cost_per_litter	= $_REQUEST['fuel_cost_per_litter'];
	$fuel_amount			= $_REQUEST['fuel_amount'];

	

	$budget_service_detail_id 		= $_REQUEST['budget_service_detail_id'];
	$update_service_stock_id		= $_REQUEST['update_service_stock_id'];
	$update_service_quantity 		= $_REQUEST['update_service_quantity'];
	$update_service_rate_per_day	= $_REQUEST['update_service_rate_per_day'];
	$update_service_days			= $_REQUEST['update_service_days'];

	
	$user_id			= $_SESSION['userID'];
	$id		= $_REQUEST['id'];
	
	if($b == "Unfinish"){
		mysql_query("
			update 
				budget_header
			set
				status = 'S'
			where
				budget_header_id = '$budget_header_id'
		") or die(mysql_error());
	}	
	
	
	if($b == "M"){
		mysql_query("
			delete from
				budget_detail
			where
				budget_detail_id = '$id'
		") or die(mysql_error());	
	}else if($b == "S"){
		mysql_query("
			delete from
				budget_service_detail
			where
				budget_service_detail_id = '$id'
		") or die(mysql_error());	
	}else if($b == "E"){
		mysql_query("
			delete from
				budget_equipment_detail
			where
				budget_equipment_detail_id = '$id'
		") or die(mysql_error());	
	}else if($b == "F"){
		mysql_query("
			delete from
				budget_fuel_detail
			where
				budget_fuel_detail_id = '$id'
		") or die(mysql_error());	
	}
	
	
	if($b=="Submit"){
		
		if($options->budgetIsPresent($project_id,$scope_of_work,$work_category_id,$sub_work_category_id)){
			
			$id = $options->getBudgetId($project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
			$msg = "Budget is already avaible. Click <a  style='color:#F00;' href='admin.php?view=29a6d2e5c71d0ae94395&budget_header_id=$id'>HERE</a> to proceed to that budget.";
		
		}else{
			$query="
				insert into 
					budget_header
				set
					project_id		= '$project_id',
					date			= '$date',
					scope_of_work	= '$scope_of_work',
					work_category_id = '$work_category_id',
					sub_work_category_id = '$sub_work_category_id',
					remarks	= '$remarks'
			";	
			
			mysql_query($query) or die(mysql_error());
			
			$budget_header_id = mysql_insert_id();
					
			$msg="Transaction Saved";
		}
	}else if($b=="Update"){
		$query="
			update
				budget_header
			set
				project_id		= '$project_id',
				date			= '$date',
				scope_of_work	= '$scope_of_work',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				remarks	= '$remarks'
				/*quantity = '$quantity'*/
				
			where
				budget_header_id='$budget_header_id'
				/*budget_detail_id='$budget_detail_id'*/
		";	
		mysql_query($query);
		$msg = "Transaction Updated";
		
	}else if($b=="Add Details"){
		if($stock_id){
			mysql_query("
				insert into
					budget_detail
				set	
					budget_header_id 	= '$budget_header_id',
					stock_id			= '$stock_id',
					quantity			= '$quantity',
					cost				= '$cost',
					amount				= '$amount'
			") or die(mysql_error());
			
			$msg = "Transaction Added";
		}else{
			$msg = "Transaction Error";
		}
	
	}else if($b=="Update Details"){
				
		$x=0;
		
		foreach($budget_detail_id as $id):
			
			mysql_query("
				update
					budget_detail
				set
					quantity = '$update_quantity[$x]'
				where
					budget_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Cancel"){
		$query="
			update
				budget_header
			set
				status='C'
			where
				budget_header_id = '$budget_header_id'
		";	
		mysql_query($query);
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				budget_header
			set
				status='F'
			where
				budget_header_id = '$budget_header_id'
		";	
		mysql_query($query);
		
		$msg = "Transaction Finished";
		
	}else if($b=="Delete Selected"){	
		if(!empty($checkList)){
			foreach($checkList as $list){
				mysql_query("
					delete from
						budget_detail
					where
						budget_detail_id = '$list'
				") or die(mysql_error());
			}
		}
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}else if($b=="Add Service Details"){
		if($service_stock_id){
			mysql_query("
				insert into
					budget_service_detail
				set	
					budget_header_id 	= '$budget_header_id',
					stock_id			= '$service_stock_id',
					quantity			= '$service_quantity',
					days				= '$service_days',
					rate_per_day		= '$service_rate_per_day',
					amount				= '$service_amount'
			") or die(mysql_error());
			
			$msg = "Transaction Added";
		}else{
			$msg = "Transaction Error";
		}
	
	}else if($b=="Update Service Details"){		
	
		$x=0;
		
		foreach($budget_service_detail_id as $id):
			$amount = $update_service_quantity[$x] * $update_service_days[$x] * $update_service_rate_per_day[$x];
			
			mysql_query("
				update
					budget_service_detail
				set
					quantity 		= '$update_service_quantity[$x]',
					days			= '$update_service_days[$x]',
					rate_per_day	= '$update_service_rate_per_day[$x]',
					amount			= '$amount'
				where
					budget_service_detail_id = '$id'
			") or die(mysql_error());
			$x++;
		endforeach;	
		
		$msg = "Transaction Details Updated";
	}else if($b=="Delete Service Selected"){	
		if(!empty($checkList)){
			foreach($checkList as $list){
				mysql_query("
					delete from
						budget_service_detail
					where
						budget_service_detail_id = '$list'
				") or die(mysql_error());
			}
		}
	}else if($b=="Add Equipment Details"){
		
		if($equipment_stock_id){
			mysql_query("
				insert into
					budget_equipment_detail
				set	
					budget_header_id 	= '$budget_header_id',
					stock_id			= '$equipment_stock_id',
					quantity			= '$equipment_quantity',
					days				= '$equipment_days',
					rate_per_day		= '$equipment_rate_per_day',
					amount				= '$equipment_amount'
			") or die(mysql_error());
			
			$msg = "Transaction Added";
		}else{
			$msg = "Transaction Error";
		}
	
	}else if($b=="Delete Equipment Selected"){	
		if(!empty($checkList)){
			foreach($checkList as $list){
				mysql_query("
					delete from
						budget_equipment_detail
					where
						budget_equipment_detail_id = '$list'
				") or die(mysql_error());
			}
		}
	}else if($b=="Add Fuel Details"){
		
		if($fuel_id){
			mysql_query("
				insert into
					budget_fuel_detail
				set	
					budget_header_id 	= '$budget_header_id',
					fuel_id				= '$fuel_id',
					equipment_id		= '$equipment_id',
					consumption_per_day	= '$consumption_per_day',
					quantity			= '$fuel_quantity',
					days				= '$fuel_days',
					cost_per_litter		= '$fuel_cost_per_litter',
					amount				= '$fuel_amount'
			") or die(mysql_error());
			
			$msg = "Transaction Added";
		}else{
			$msg = "Transaction Error";
		}
	
	}else if($b=="Delete Fuel Selected"){	
		if(!empty($checkList)){
			foreach($checkList as $list){
				mysql_query("
					delete from
						budget_fuel_detail
					where
						budget_fuel_detail_id = '$list'
				") or die(mysql_error());
			}
		}
	}
	

	$query="
		select
			*
		from
			budget_header
		where
			budget_header_id ='$budget_header_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);

	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $r['description'];	
	$status				= $r['status'];
	$date				= (empty($r['date']) || $r['date']=="0000-00-00")?"":$r['date'];
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$remarks			= $r['remarks'];

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>BUDGET</div>
    <form name="header_form" id="header_form" action="" method="post">
    
   	<div style="width:50%; float:left;">
        <div class="module_actions">
            <input type="hidden" name="budget_header_id" id="budget_header_id" value="<?=$budget_header_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <div class="inline">
                Date : <br />
                <input type="text" name="date" class="textbox3 datepicker" value="<?=$date?>" readonly="readonly"  />
            </div>
            
            <div class='inline'>
                Project : <br />  
                <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
                <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
            </div>   
            
            <div class="inline">
                Work Category : <br />
                <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
            </div>
            
            <div id="subworkcategory_div" style="display:none;" class="inline">
                Sub Work Category :
                <div id="subworkcategory">
                    
                </div>
            </div>
            
            <br />
            
            <div style="display:inline-block;">
                Remarks : <br />
                <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
            </div>   
           
            <?php
            if(!empty($status)){
            ?>
            <div>
                Status : <br />
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div> 
            <?php
            }
            ?>
        </div>
        <div class="module_actions">
            <input type="submit" name="b" id="b" value="New" />
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
            <?php
            }
            ?>
            
            <?php if($status=="F"){ ?>
            <input type="submit" name="b" value="Unfinish" />
            
            <?php } ?>
        </div>
        <?php
		if( $status == 'S'):	
		?>
		<div id="accordion">
			<h3><a href="#">MATERIALS ENTRY</a></h3>
			<div>
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
					<div><input type="text" size="20" name="price" id="cost" class="textbox3 hinder-submit" /></div>
				</div> 
				<div class="inline">
					<div>Amount : </div>        
					<div><input type="text" size="20" name="amount" id="amount" class="textbox3 hinder-submit" readonly="readonly" /></div>
				</div> 
				<div style="margin-top:10px;">
                    <input type="submit" name="b" value="Add Details" />
                    <!-- <input type="submit" name="b" value="Update Details"  /> -->
                    <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();"  />
               	</div>
			</div>
			<!--
			<h3><a href="#">SERVICE ENTRY</a></h3>
			<div>
				<div class="inline">
					Item : <br />
					<input type="text" class="textbox hinder-submit" name="stock_name" id="service_name" onclick="this.select();" />
					<input type="hidden" name="service_stock_id" id="service_id"  />
				</div>    
				<div class="inline">
					<div>No : </div>        
					<div><input type="text" size="20" name="service_quantity" id="service_quantity" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>No. of Days: </div>        
					<div><input type="text" size="20" name="service_days" id="service_days" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>Rate / Day : </div>        
					<div><input type="text" size="20" name="service_rate_per_day" id="service_cost" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>Amount : </div>        
					<div><input type="text" size="20" name="service_amount" id="service_amount" class="textbox3 hinder-submit" readonly="readonly" /></div>
				</div> 
				<div style="margin-top:10px;">
                    <input type="submit" name="b" value="Add Service Details" />
                    <input type="submit" name="b" value="Update Service Details"  />
                    <input type="submit" name="b" value="Delete Service Selected" onclick="return approve_confirm();"  />
                </div>
			</div>
            
            <h3><a href="#">EQUIPMENT RENTALS ENTRY</a></h3>
			<div>
				<div class="inline">
					Item : <br />
					<input type="text" class="textbox hinder-submit" name="stock_name" id="equipment_name" onclick="this.select();" />
					<input type="hidden" name="equipment_stock_id" id="equipment_id"  />
				</div>    
				<div class="inline">
					<div>No : </div>        
					<div><input type="text" size="20" name="equipment_quantity" id="equipment_quantity" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>No. of Days: </div>        
					<div><input type="text" size="20" name="equipment_days" id="equipment_days" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>Rental / Day : </div>        
					<div><input type="text" size="20" name="equipment_rate_per_day" id="equipment_cost" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>Amount : </div>        
					<div><input type="text" size="20" name="equipment_amount" id="equipment_amount" class="textbox3 hinder-submit" readonly="readonly" /></div>
				</div> 
				<div style="margin-top:10px;">
                    <input type="submit" name="b" value="Add Equipment Details" />
                    <input type="submit" name="b" value="Update Equipment Details"  /
                    <input type="submit" name="b" value="Delete Equipment Selected" onclick="return approve_confirm();"  />
                </div>
			</div>
            
            <h3><a href="#">FUEL, OIL, LUBRICANTS ENTRY</a></h3>
			<div>
				<div class="inline">
					Fuel : <br />
					<input type="text" class="textbox hinder-submit stock_name" onclick="this.select();" />
					<input type="hidden" name="fuel_id"   />
				</div>    
                
                <div class="inline">
					Equipment : <br />
					<input type="text" class="textbox hinder-submit equipment_name" onclick="this.select();" />
					<input type="hidden" name="equipment_id" />
				</div>    
                
                <div class="inline">
					<div>Consumption per day : </div>        
					<div><input type="text" size="20" name="consumption_per_day" id="fuel_consumption" class="textbox3 hinder-submit" /></div>
				</div> 
                
				<div class="inline">
					<div>Quantity : </div>        
					<div><input type="text" size="20" name="fuel_quantity" id="fuel_quantity" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>No. of Days: </div>        
					<div><input type="text" size="20" name="fuel_days" id="fuel_days" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>Fuel Cost / Liter : </div>        
					<div><input type="text" size="20" name="fuel_cost_per_litter" id="fuel_cost" class="textbox3 hinder-submit" /></div>
				</div> 
				
				<div class="inline">
					<div>Amount : </div>        
					<div><input type="text" size="20" name="fuel_amount" id="fuel_amount" class="textbox3 hinder-submit" readonly="readonly" /></div>
				</div> 
				<div style="margin-top:10px;">
                    <input type="submit" name="b" value="Add Fuel Details" />
                    <!--<input type="submit" name="b" value="Update Equipment Details"  />-->
                   <!-- <input type="submit" name="b" value="Delete Fuel Selected" onclick="return approve_confirm();"  />
                </div>
			</div>-->
		</div>
		 <?php
		endif;
		?>
	</div>    
    
    <div style="float:right;width:50%;" >
    	<div class="accordion">
        	<?php
			$query = "
				select
					budget_detail_id,
					d.stock_id,
					pm.stock,
					quantity,
					d.cost,
					amount,
					unit
				from
					budget_detail as d,
					productmaster as pm
				where
					d.budget_header_id	= '$budget_header_id' 
				and
					pm.stock_id = d.stock_id
				order by stock asc
			";
			$result=mysql_query($query) or die(mysql_error());
			$rows = mysql_num_rows($result);
			if($rows > 0):
			?>
            <div class="ui-widget-header head">
                <h3><img src="images/cart.png" style="margin-right:15px;" /> MATERIALS</h3>
            </div>
            <div style="width:100%; overflow:auto;" >
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                         <td width="20" align="center"></td>
                        <th>Item</th>
                        <th width="60">Budget</th>
                        <th width="100">Unit</th>
                        <th width="100">U. Price</th>
                        <th width="100">Amount</th>
                        
    
                        <!--<th>Warehouse Quantity</th>
                        <th>Project Quantity</th>
                        <th>Issued Quantity</th>
                        <th>Total Stocks</th>
                        <th>Balance</th>-->
                    </tr>
                    <?php
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $budget_detail_id	= $r['budget_detail_id'];
                        $stock_id			= $r['stock_id'];
                        $stock				= $r['stock'];
                        $quantity			= $r['quantity'];
                        $cost				= $r['cost'];
                        $amount				= $r['amount'];
                        $unit				= $r['unit'];
                                            
                        
                        $total_stocks = $warehouse_qty + $project_warehouse_qty + $issued_qty;
                        $balance = $quantity - $total_stocks ;
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><a href="admin.php?view=<?=$view?>&budget_header_id=<?=$budget_header_id?>&b=M&id=<?=$budget_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                             <td><a href="javascript:void(0)" onclick="xajax_update_budget('<?=$budget_detail_id?>',xajax.getFormValues('form1'))">
                             <img src="images/edit.gif" style="cursor:pointer;" /></a></td>
                            <td><?=$stock?></td>
                            <td>
								<form method="POST" action="" id="form1">
									<input type="text" class='textbox3 align-right' id="q_<?=$budget_detail_id?>" name='q_<?=$budget_detail_id?>' value='<?=$quantity?>'>
								</form>
							</td>
                            <td><?=$unit?></td>
                            <td class="align-right"><?=number_format($cost,2,'.',',')?></td>
                            <td class="highlight align-right"><?=number_format($amount,2,'.',',')?></td>
                            
    <!--                        <td class="align-right"><?=number_format($warehouse_qty,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($project_warehouse_qty,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($issued_qty,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($total_stocks,2,'.',',')?></td>
                            <td class="align-right highlight"><?=number_format($balance,2,'.',',')?></td>
    -->                        
                            <input type='hidden' value='<?=$stock_id?>' />
                            <input type='hidden' name='budget_detail_id[]' value='<?=$budget_detail_id?>' />
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
					budget_service_detail as d,
					productmaster as pm
				where
					d.budget_header_id	= '$budget_header_id' 
				and
					pm.stock_id = d.stock_id
			";
			$result=mysql_query($query) or die(mysql_error());
			$rows = mysql_num_rows($result);
			if($rows > 0):
			?>
            <div class="ui-widget-header head">
                <h3><img src="images/cart.png" style="margin-right:15px;" />SERVICES</h3>
            </div>
            <div style="width:100%; overflow:auto;" >
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                        <th>Designation</th>
                        <th width="60">No</th>
                        <th width="60">No. of Days</th>
                        <th width="60">Rate/Day</th>
                        <th width="100">Amount</th>
                        
                        <!--<th>Service Amount Received</th>
                        <th>Balance</th>-->
                        
                    </tr>
                    <?php
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
                        $budget_service_detail_id	= $r['budget_service_detail_id'];
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
                            <td><a href="admin.php?view=<?=$view?>&budget_header_id=<?=$budget_header_id?>&b=S&id=<?=$budget_service_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_quantity[]' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_days[]' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_service_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                            <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                            
                            
                            <!--<td class="align-right"><?=number_format($service_received,2,'.',',')?></td>
                            <td class="align-right highlight"><?=number_format($balance,2,'.',',')?></td>-->
                            
                            
                            <input type='hidden' value='<?=$stock_id?>' />
                            <input type='hidden' name='budget_service_detail_id[]' value='<?=$budget_service_detail_id?>' />
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
					budget_equipment_detail as d,
					productmaster as pm
				where
					d.budget_header_id	= '$budget_header_id' 
				and
					pm.stock_id = d.stock_id
			";
			$result=mysql_query($query) or die(mysql_error());
			$rows = mysql_num_rows($result);
			if($rows > 0):
			?>
            <div class="ui-widget-header head">
                <h3><img src="images/cart.png" style="margin-right:15px;" />EQUIPMET RENTALS</h3>
            </div>
            <div style="width:100%;  overflow:auto;" >
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                        <th>Designation</th>
                        <th width="60">No</th>
                        <th width="60">No. of Days</th>
                        <th width="60">Rate/Day</th>
                        <th width="100">Amount</th>
                        
                        <!--<th>Equipment Amount Received</th>
                        <th>Balance</th>-->
                        
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
                            <td><a href="admin.php?view=<?=$view?>&budget_header_id=<?=$budget_header_id?>&b=E&id=<?=$budget_equipment_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                            <td><?=$stock?></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_quantity[]' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_days[]' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' name='update_equipment_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                            <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                            
                            
                            <input type='hidden' value='<?=$stock_id?>' />
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
			 $query = "
				select
					*
				from
					budget_fuel_detail as d
				where
					d.budget_header_id	= '$budget_header_id' 

			";
			$result=mysql_query($query) or die(mysql_error());
			$rows = mysql_num_rows($result);
			if($rows > 0):
			?>
            <div class="ui-widget-header head">
                <h3><img src="images/cart.png" style="margin-right:15px;" />FUEL, OIL, & LUBRICANTS</h3>
            </div>
            <div style="width:100%; text-align:center; overflow:auto;" >
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                        <th>Fuel</th>
                        <th>Equipment</th>
                        <th width="60">Consumption / Day</th>
                        <th width="60">Quantity</th>
                        <th width="60">No. of Days</th>
                        <th width="60">Fuel Cost/Litter</th>
                        <th width="100">Amount</th>
                        
                        <!--<th>Equipment Amount Received</th>
                        <th>Balance</th>-->
                        
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
                            <td><a href="admin.php?view=<?=$view?>&budget_header_id=<?=$budget_header_id?>&b=F&id=<?=$budget_fuel_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                            <td><?=$fuel?></td>
                            <td><?=$equipment?></td>
                            <td><input type='text' class='textbox3 align-right' value='<?=$consumption_per_day?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' value='<?=$quantity?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' value='<?=$days?>'  /></td>
                            <td><input type='text' class='textbox3 align-right' value='<?=$cost_per_litter?>'  /></td>
                            <td class="align-right highlight"><?=number_format($amount,2,'.',',')?></td>
                            
                            <input type='hidden' name='budget_fuel_detail_id[]' value='<?=$budget_fuel_detail_id?>' />
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
     </div>
     <div style="clear:both;">
     <?php
		if($b == "Print Preview" && $budget_header_id){
		
			echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_budget.php?id=$budget_header_id' width='100%' height='500'>
					</iframe>";
	?>
	<?php
    }
    ?>
     </div>
  
     </form>
    
</div>
<script type="text/javascript">
j(function(){
	
	j("#service_quantity,#service_days,#service_cost").keyup(function(){
		var quantity = j("#service_quantity").val();
		var days = j("#service_days").val();
		var cost = j("#service_cost").val();
		
		var amount = quantity * days * cost;
		j("#service_amount").val(amount);
	});
	
	j("#equipment_quantity,#equipment_days,#equipment_cost").keyup(function(){
		var quantity = j("#equipment_quantity").val();
		var days = j("#equipment_days").val();
		var cost = j("#equipment_cost").val();
		
		var amount = quantity * days * cost;
		j("#equipment_amount").val(amount);
	});
	
	j("#fuel_consumption,#fuel_quantity,#fuel_days,#fuel_cost").keyup(function(){

		var consumption = j("#fuel_consumption").val();
		var quantity  = j("#fuel_quantity").val();		
		var days	= j("#fuel_days").val();
		var cost = j("#fuel_cost").val();
		
		var amount = consumption * quantity * days * cost;
		j("#fuel_amount").val(amount);
	});
	
	j("#quantity,#cost").keyup(function(){
		var quantity = j("#quantity").val();
		var cost = j("#cost").val();
		
		var amount = quantity * cost;
		j("#amount").val(amount);
	});
	
	
	<?php
		if($b == "Add Details"){
			$active_state = 0;	
		}else if($b == "Add Service Details"){
			$active_state = 1;
		}else if($b == "Add Equipment Details"){
			$active_state = 2;	
		}else{
			$active_state = 3;	
		}
	?>
	
	j("#accordion").accordion({active : <?=$active_state?> , collapsible : true, autoHeight: false});
	
	j('.accordion .head').click(function() {
		j(this).next().toggle('slow');
		return false;
	});
		
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php
	}
	?>
});

</script>
	