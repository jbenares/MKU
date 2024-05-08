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
	
	$material_request_quantity	= $_REQUEST['material_request_quantity'];
	
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
	$request_quantity		= $_REQUEST['request_quantity'];
	$requestor				= $_REQUEST['requestor'];
	
	
	$pr_detail_id		= $_REQUEST['pr_detail_id'];
	$detail_quantity	= $_REQUEST['detail_quantity'];
	$checkList			= $_REQUEST['checkList'];
	
	$user_id			= $_SESSION['userID'];
	
	$quantity2	= $_REQUEST['quantity2'];
	$unit2		= $_REQUEST['unit2'];
	
	$details	= $_REQUEST['details'];
	
	if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){
		$old_id_pr = $_REQUEST['old_id_pr'];
	}
	
	if($b == "Unfinish"){
		mysql_query("update pr_header set status = 'S' where pr_header_id = '$pr_header_id'") or die(mysql_error());	
	}
	
	if($b=="Submit"){
		
		if($old_id_pr > 0){
			$sql = mysql_query("select * from pr_header where pr_header_id = '$old_id_pr'") or die(mysql_error());	
			$count = mysql_num_rows($sql);
			if($count > 0){
				$msg = "Error, PR already exist";
			}else{
				$query="
				insert into 
					pr_header
				set
					pr_header_id			= '$old_id_pr',
					project_id				= '$project_id',
					description				= '$description',
					date					= '$date',
					date_needed				= '$date_needed',
					work_category_id 		= '$work_category_id',
					sub_work_category_id 	= '$sub_work_category_id',
					scope_of_work			= '$scope_of_work',
					user_id					= '$user_id',
					requestor				= '$requestor',
					no_budget				= '1',
					approval_status			= 'A'
				";	
				
				mysql_query($query) or die(mysql_error());
		
				$options->insertAudit($old_id_pr,'pr_header_id','I');		
				
				$msg="Transaction Saved, OLD PR";
				
				header("Location: admin.php?view=$view&pr_header_id=".$old_id_pr);
			}
			
		}else{
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
					requestor				= '$requestor',
					no_budget				= '1',
					approval_status			= 'A'
			";	
			
			mysql_query($query) or die(mysql_error());
		
			$pr_header_id = mysql_insert_id();
			$options->insertAudit($pr_header_id,'pr_header_id','I');		
			
			$msg="Transaction Saved, Normal";
		}
		
		
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
				requestor				= '$requestor',
				user_id 				= '$user_id'
			where
				pr_header_id='$pr_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($pr_header_id,'pr_header_id','U');
		
		$msg = "Transaction Updated, Update";
		
	}else if($b=="Add Details"){
		/*
			CHECK IF IN BUDGET
		*/
		
		$use_qty				= $_REQUEST['use_qty'];
		$warehouse_qty			= (!empty($use_qty))?$_REQUEST['warehouse_qty']:0;
		
		/*
		if(!empty($use_qty)){
			mysql_query("
				insert into 	
					pr_warehouse
				set
					stock_id		= '$stock_id',
					quantity		= '$warehouse_qty',
					type			= 'M',
					pr_header_id	= '$pr_header_id'
			") or die(mysql_error());
		}
		*/
		
		
		$allowed = 1;
		
		mysql_query("
			insert into
				pr_detail
			set	
				pr_header_id 		= '$pr_header_id',
				stock_id			= '$stock_id',
				request_quantity 	= '$material_request_quantity',
				warehouse_quantity	= '$warehouse_qty',
				quantity			= '$quantity',
				in_budget			= '$in_budget',
				allowed				= '$allowed',
				quantity2			= '$quantity2',
				unit2				= '$unit2',
				details				= '$details'
		") or die(mysql_error());
		
		$msg = "Transaction Added";
	
	}else if($b == "Add Service Details"){
		
		$allowed = 0;
		
		mysql_query("
			insert into
				pr_service_detail
			set	
				pr_header_id		= '$pr_header_id',
				stock_id			= '$service_stock_id',
				quantity			= '$service_quantity',
				days				= '$service_days',
				rate_per_day		= '$service_rate_per_day',
				amount				= '$service_amount',
				allowed				= '$allowed'
		") or die(mysql_error());
		
	}else if($b == "Add Equipment Details"){
		
		$allowed = 0;
		
		mysql_query("
			insert into
				pr_equipment_detail
			set	
				pr_header_id		= '$pr_header_id',
				stock_id			= '$equipment_stock_id',
				quantity			= '$equipment_quantity',
				days				= '$equipment_days',
				rate_per_day		= '$equipment_rate_per_day',
				amount				= '$equipment_amount',
				allowed				= '$allowed'
		") or die(mysql_error());
		
		$msg = "Equipment Request Added";
		
	}else if($b == "Add Fuel Details"){
		
		$use_qty				= $_REQUEST['use_qty2'];
		$warehouse_qty			= $_REQUEST['warehouse_qty2'];
		$warehouse_qty			= (!empty($use_qty))?$warehouse_qty:0;
		/*
		if(!empty($use_qty)){
			mysql_query("
				insert into 	
					pr_warehouse
				set
					fuel_id 		= '$fuel_id',
					equipment_id	= '$equipment_id',
					quantity		= '$warehouse_qty',
					type			= 'F',
					pr_header_id	= '$pr_header_id'
			") or die(mysql_error());
		}
		*/
		
		$allowed = 0;
		
		mysql_query("
			insert into
				pr_fuel_detail
			set	
				pr_header_id		= '$pr_header_id',
				fuel_id				= '$fuel_id',
				equipment_id		= '$equipment_id',
				consumption_per_day	= '$consumption_per_day',
				quantity			= '$fuel_quantity',
				request_quantity	= '$request_quantity',
				warehouse_quantity	= '$warehouse_qty',
				days				= '$fuel_days',
				cost_per_litter		= '$fuel_cost_per_litter',
				amount				= '$fuel_amount',
				allowed				= '$allowed'
		") or $objResponse->alert(mysql_error());
		
		$msg = "Fuel Request Added";
		
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
	}
	
	
	$d = $_REQUEST['d'];
	$id = $_REQUEST['id'];
	if($d == "m"){	
		mysql_query("
			delete from
				pr_detail
			where
				pr_detail_id = '$id'
		") or die(mysql_error());
	}else if($d=="s"){	
		mysql_query("
			delete from
				pr_service_detail
			where
				pr_service_detail_id = '$id'
		") or die(mysql_error());
	}else if($d=="e"){	
		mysql_query("
			delete from
				pr_equipment_detail
			where
				pr_equipment_detail_id = '$id'
		") or die(mysql_error());
	}else if($d=="f"){	
		mysql_query("
			delete from
				pr_fuel_detail
			where
				pr_fuel_detail_id = '$id'
		") or die(mysql_error());		
	}else if($d=="wm"){	
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
	
	$scope_of_work		= $r['scope_of_work'];
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	
	$approval_status	= $r['approval_status'];
	$user_id			= $r['user_id'];	
	$approved_by		= $r['approved_by'];
?>

<?php
if($status == "F" || $status == "C"){
?>
<style type="text/css">
	.display_table tr td:nth-child(2), .display_table tr th:nth-child(2){
		display:none;	
	}
</style>
<?php
}
?>


<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE REQUEST</div>
    
    <div class="module_actions">
			<?php if($old_id_pr > 0){
				$pr_header_id = $old_id_pr;
			} ?>
        <input type="hidden" name="pr_header_id" id="pr_header_id" value="<?=$pr_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class="inline">
        	Date Requested : <br />
        	<input type="text" class="textbox3 datepicker required" name="date" value="<?=$date?>" readonly="readonly" />
        </div>
        
        <div class="inline">
        	Date Needed : <br />
        	<input type="text" class="textbox3 datepicker required" name="date_needed" value="<?=$date_needed?>" readonly="readonly" />
        </div>
        
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox required hinder-submit" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" required/>
        </div>   
        
        <div class="inline">
        	Scope of Work :
            <div id="div_scope_of_work">
            	<select class="select">
                	<option value="">Select Project First...</option>
                </select>
            </div>
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
        <div class='inline'>
            Description : <br />
            <textarea class="textarea_small" name='description'><?=$description?></textarea>
        </div>          
      	
		<?php if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){ ?>
		
			<div>
				PR Header ID : <br />
				<input type="text" class="textbox" name="old_id_pr" value="<?=$old_id_pr?>"/>
			</div>
        <?php
		}
		
        if(!empty($status)){
        ?>
        
        <div class='inline' style="vertical-align: top;">
            PR #: <br />
	     	<input type="text" class="textbox" value="<?=str_pad($pr_header_id,7,0,STR_PAD_LEFT)?>" readonly="readonly"/>
        </div> 
        
        <div class='inline' style="vertical-align: top;">
            Requested by : <br />
	     	<input type="text" class="textbox" name="requestor" id="requestor" value="<?=$requestor?>" />
        </div> 
        
        <div class='inline' style="vertical-align: top;">
            Approval Status : <br />
	     	<input type="text" class="textbox3"  value="<?=$options->getApprovalStatus($approval_status)?>" readonly="readonly"/>
        </div> 
        
        <?php
		if(!empty($approved_by)){
        ?>
        <div class='inline' style="vertical-align: top;">
            Approved by : <br />
	     	<input type="text" class="textbox" name="status" id="status" value="<?=$options->getUserName($approved_by)?>" readonly="readonly"/>
        </div>         
        <?php
		}
        ?>
        
        <div class='inline' style="vertical-align: top;">
            Status : <br />
            <input type="text" class="textbox3" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
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
    	<h3><a href="#">MATERIALS ENTRY</a></h3>
        <div>
            <div class="inline">
                Item : <br />
                <input type="text" class="textbox hinder-submit" name="stock_name" id="stock_name" onclick="this.select();" />
                <input type="hidden" name="stock_id" id="stock_id"  />
            </div>    
            
            <div class="inline">
                Details : <br />
                <input type="text" class="textbox hinder-submit" name="details" onclick="this.select();" />
            </div>    
            
            <div class="inline">
                <div>Request Quantity : </div>        
                <div><input type="text" size="20" id="quantity" class="textbox3 hinder-submit" name="material_request_quantity" /></div>
            </div> 
            
            <div class="inline">
                <div>Unit : </div>        
                <div><input type="text" size="20" id="unit" readonly="readonly" class="textbox3" /></div>
            </div> 
            
            <hr style="border:1px solid #EEEEEE; width:70%; margin:10px 0px;" />
            
            <div class="inline" style="vertical-align:top;" id="div_warehouse_qty">
            	Available Warehouse Quantity : <br />
                <input type="text" class="textbox3 hinder-submit" id="warehouse_qty"  name="warehouse_qty"  /> <br />
                <input type="checkbox" name="use_qty" id="use_qty"  /><label for="use_qty" class="label">Use Warehouse Quantity</label>
            </div>
            
            <div class="inline">
            	Total Quantity : <br />
            	<input type="text" class="textbox3 hinder-submit" name="quantity" id="req_qty"  readonly="readonly" />
            </div>
            
            <hr style="border:1px solid #EEEEEE; width:70%; margin:10px 0px;" />
            
            <div class="inline">
            	<em>(Optional)</em> Quantity : <br />
                <input type="text" class="textbox3 hinder-submit" name="quantity2"  />
            </div>
            
            <div class="inline">
            	<em>(Optional)</em> Unit: <br />
                <input type="text" class="textbox3 hinder-submit" name="unit2"  />
            </div>
          
            <input type="submit" name="b" value="Add Details" />
			<!-- <input type="submit" name="b" value="Update Details"  /> -->
       	</div>
        <!--
        <h3><a href="#">SERVICE ENTRY</a></h3>
        <div>
            <div class="inline">
                Item : <br />
                <input type="text" class="textbox" name="stock_name" id="service_name" onclick="this.select();" />
                <input type="hidden" name="service_stock_id" id="service_id"  />
            </div>    
            <div class="inline">
                <div>No : </div>        
                <div><input type="text" size="20" name="service_quantity" id="service_quantity" class="textbox3" /></div>
            </div> 
            
            <div class="inline">
                <div>No. of Days: </div>        
                <div><input type="text" size="20" name="service_days" id="service_days" class="textbox3" /></div>
            </div> 
            
            <div class="inline">
                <div>Rate / Day : </div>        
                <div><input type="text" size="20" name="service_rate_per_day" id="service_cost" class="textbox3" /></div>
            </div> 
            
            <div class="inline">
                <div>Amount : </div>        
                <div><input type="text" size="20" name="service_amount" id="service_amount" class="textbox3" readonly="readonly" /></div>
            </div> 
            
            <input type="submit" name="b" value="Add Service Details" />
            <input type="submit" name="b" value="Update Service Details"  />
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
                    <!--<input type="submit" name="b" value="Update Equipment Details"  />
                </div>
			</div>
            
       	<h3><a href="#">FUEL OIL LUBRICANTS ENTRY</a></h3>
		<div>
        	<div class="inline">
                Fuel : <br />
                <input type="text" class="textbox stock_name hinder-submit" name="fuel_name" onclick="this.select();" />
                <input type="hidden" name="fuel_id"   />
            </div>  
            
            <div class="inline">
                Equipment : <br />
                <input type="text" class="textbox equipment_name hinder-submit" name="equipment_name" onclick="this.select();" />
                <input type="hidden" name="equipment_id"   />
            </div>  
        
            <div class='inline'>
            Consumption / Day : <br>
            <input type='text' class='textbox hinder-submit' name='consumption_per_day' id='fuel_consumption'  value='' autocomplete='off'>
			</div>
		
			<div class='inline'>
				Request Quantity : <br>
				<input type='text' class='textbox hinder-submit' id='fuel_quantity'  value='' autocomplete='off' name="request_quantity">
			</div>
			
			<div class='inline'>
				No. of Days : <br>
				<input type='text' class='textbox hinder-submit' name='fuel_days' id='fuel_days' value='' autocomplete='off'>
			</div>
			
			<div class='inline'>
				Total Fuel Liters : <br>
				<input type='text' class='textbox hinder-submit'  name='total_fuel' id='total_fuel' value='' autocomplete='off' readonly='readonly'>
			</div>
			
			<div class='inline' style='vertical-align:top;' id='div_warehouse_qty2'>
            	Available Warehouse Quantity : <br />
                <input type='text' class='textbox3' id='warehouse_qty2'  name='warehouse_qty2' value='' autocomplete=\"off\" />
                <input type='checkbox' name='use_qty2' id='use_qty2'  /><label for='use_qty2' class='label'>Use Warehouse Quantity</label>
            </div>
			
			<div class='inline'>
				Total Quantity : <br>
				<input type='text' class='textbox' name='fuel_quantity' id='req_qty2' value='' autocomplete='off'>
			</div>
			
			<div class='inline'>
				Fuel Cost/Litter: <br>
				<input type='text' class='textbox' name='fuel_cost_per_litter' id='fuel_cost' value='' autocomplete='off'>
			</div>
			
			<div class='inline'>
				Amount : <br>
				<input type='text' class='textbox' name='fuel_amount' id='fuel_amount' value='' readonly='readonly' autocomplete='off'>
			</div>
			
            
            <div style="margin-top:10px;">
                <input type="submit" name="b" value="Add Fuel Details" />
                <!--<input type="submit" name="b" value="Update Equipment Details"  />
            </div>
		</div>-->
    </div>
    <?php
    }
    ?> 
    
  
        
</div>
  <?php
	if($b == "Print Preview" && $pr_header_id){

		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_purchase_request.php?id=$pr_header_id' width='100%' height='500'>
		       	</iframe>";
	?>
    <?php
	}else if(!empty($status)){
	?>

	<br />

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
	?>

	
	<?php
	if($rows>0){
	?>
    <div style="background-color::#FFF; font-weight:bolder;">PURCHASE REQUEST MATERIAL DETAILS : </div>
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
            <th>Item</th>
            <th width="60">Request Quantity</th>
            <th width="60">Warehouse Quantity</th>
            <th width="60">Total Quantity</th>
            <th width="100">Unit</th>
            <th width="100">(Optional) Qty</th>
            <th width="100">(Optional) Unit</th>
            <th width="100">Status</th>
        </tr>
        <?php
        $i=1;
        while($r=mysql_fetch_assoc($result)){
            $pr_detail_id		= $r['pr_detail_id'];
            $stock_id			= $r['stock_id'];
            $stock				= $r['stock'];
            $unit				= $r['unit'];
			$warehouse_quantity = $r['warehouse_quantity'];
			$request_quantity	= $r['request_quantity'];
            $quantity			= $r['quantity'];
            $in_stock			= $r['in_stock'];
            $in_budget			= $r['in_budget'];
            $allowed			= $r['allowed'];
			$quantity2			= $r['quantity2'];
			$unit2				= $r['unit2'];
			$details			= $r['details'];
            
            $allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
        ?>
            <tr>
                <td><?=$i++?></td>
                <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&d=m&id=<?=$pr_detail_id?>" onclick="return approve_confirm();" ><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                <td><?=$stock?> <?=($details)?"( $details )":""?></td>
                <td><input type='text' class='textbox-int align-right' value='<?=$request_quantity?>'  /></td>
                <td><input type='text' class='textbox-int align-right' value='<?=$warehouse_quantity?>'  /></td>
                <td><input type='text' class='textbox-int align-right' value='<?=$quantity?>'  /></td>
                <td><?=$unit?></td>
                <td style="text-align:right;"><?=number_format($quantity2,4,'.',',')?></td>
				<td><?=$unit2?></td>
                <td><?=$allowed_name?></td>
                <input type='hidden' value='<?=$stock_id?>' />
                <input type='hidden' name='pr_detail_id[]' value='<?=$pr_detail_id?>' />
            </tr>
        <?php
        }
        ?>
    </table>
	<br />
	<?php
	}
	?>
    
    
	<?php
	$query = "
		select
			*
		from
			pr_service_detail as d,
			productmaster as pm
		where
			d.pr_header_id	= '$pr_header_id' 
		and
			pm.stock_id = d.stock_id
	";
	$result=mysql_query($query) or die(mysql_error());
	$rows = mysql_num_rows($result);
	?>
	<?php
	if($rows>0){
	?>
    <div style="background-color::#FFF; font-weight:bolder;">PURCHASE REQUEST SERVICE DETAILS : </div>
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
            <th>Designation</th>
            <th width="60">No</th>
            <th width="60">No. of Days</th>
            <th width="100">Rate/Day</th>
            <th width="100">Amount</th>
            <th width="100">Status</th>
        </tr>
        <?php
        
        $i=1;
        while($r=mysql_fetch_assoc($result)){
            $pr_service_detail_id		= $r['pr_service_detail_id'];
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
                <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&d=s&id=<?=$pr_service_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                <td><?=$stock?></td>
                <td><input type='text' class='textbox3 align-right' name='update_service_quantity[]' value='<?=$quantity?>'  /></td>
                <td><input type='text' class='textbox3 align-right' name='update_service_days[]' value='<?=$days?>'  /></td>
                <td><input type='text' class='textbox3 align-right' name='update_service_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                <td class="align-right"><input type="text" class="textbox3 align-right" value="<?=number_format($amount,2,'.',',')?>" readonly="readonly"  /></td>
                <td><?=$allowed_name?></td>
                <input type='hidden' name='pr_service_detail_id[]' value='<?=$pr_service_detail_id?>' />
            </tr>
        <?php
        }
        ?>
    </table>
    <br />
	<?php
	}
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
    ?>
    <?php
	if($rows>0){
	?>
    <div style="background-color::#FFF; font-weight:bolder;">PURCHASE REQUEST EQUIPMENT RENTALS: </div>
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
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
                <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&d=e&id=<?=$pr_equipment_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                <td><?=$stock?></td>
                <td><input type='text' class='textbox3 align-right' name='update_equipment_quantity[]' value='<?=$quantity?>'  /></td>
                <td><input type='text' class='textbox3 align-right' name='update_equipment_days[]' value='<?=$days?>'  /></td>
                <td><input type='text' class='textbox3 align-right' name='update_equipment_rate_per_day[]' value='<?=$rate_per_day?>'  /></td>
                <td class="align-right"><input type="text" class="textbox3 align-right" value="<?=number_format($amount,2,'.',',')?>" readonly="readonly"  /></td>
                <td><?=$allowed_name?></td>
                <input type='hidden' name='pr_equipment_detail_id[]' value='<?=$pr_equipment_detail_id?>' />
            </tr>
        <?php
        }
        ?>
    </table>
    <br />
    <?php
	}
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
    ?>
    <?php
	if($rows>0){
	?>
     <!--FUEL OIL LUBRICANTS-->
    <div style="background-color::#FFF; font-weight:bolder;">FUEL OIL LUBRICANTS DETAILS : </div>    
    <div style="overflow:auto;">
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
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
                <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&d=f&id=<?=$pr_fuel_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
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
    <br />
    <?php
	}
    ?>
    <!--WAREHOUSE MATERIALS-->
    
    
    <?php
	$query = "
		select
			*
		from
			pr_warehouse
		where
			pr_header_id	= '$pr_header_id' 
		and
			type = 'M'
	";
	$result=mysql_query($query) or die(mysql_error());
	$rows = mysql_num_rows($result);
    ?>
    <?php
	if($rows>0){
	?>
    <div style="background-color::#FFF; font-weight:bolder;">WAREHOUSE MATERIALS REQUEST : </div>    
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
            <th>Item</th>
            <th width="60">Quantity</th>
        </tr>
        <?php
        $i=1;
        while($r=mysql_fetch_assoc($result)){
            $pr_warehouse_id	= $r['pr_warehouse_id'];
            $stock_id		= $r['stock_id'];
            $fuel_id		= $r['fuel_id'];
            $equipment_id	= $r['equipment_id'];
            $quantity		= $r['quantity'];
            
            $stock		= $options->attr_stock($stock_id,'stock');
            $fuel		= $options->attr_stock($fuel_id,'stock');
            $equipment	= $options->attr_stock($equipment_id,'stock');
            
        ?>
            <tr>
                <td><?=$i++?></td>
                <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&d=wm&id=<?=$pr_warehouse_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                <td><?=$stock?></td>
                <td><input type='text' class='textbox3 align-right' value='<?=$quantity?>'  /></td>
            </tr>
        <?php
        }
        ?>
    </table>
    <br />
    <?php
	}
    ?>
    <?php
	$query = "
		select
			*
		from
			pr_warehouse
		where
			pr_header_id	= '$pr_header_id' 
		and
			type = 'F'
	";
	$result=mysql_query($query) or die(mysql_error());
	$rows = mysql_num_rows($result);
    ?>
    <?php
	if($rows>0){
	?>
    <!--WAREHOUSE FUEL-->
    <div style="background-color::#FFF; font-weight:bolder;">WAREHOUSE FUEL REQUEST : </div>       
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
        <tr>
            <th width="20">#</th>
            <td width="20" align="center"></td>
            <th>Fuel</th>
            <th>Equipment</th>
            <th width="60">Quantity</th>
        </tr>
        <?php
        $i=1;
        while($r=mysql_fetch_assoc($result)){
            $pr_warehouse_id	= $r['pr_warehouse_id'];
            $stock_id		= $r['stock_id'];
            $fuel_id		= $r['fuel_id'];
            $equipment_id	= $r['equipment_id'];
            $quantity		= $r['quantity'];
            
            $stock		= $options->attr_stock($stock_id,'stock');
            $fuel		= $options->attr_stock($fuel_id,'stock');
            $equipment	= $options->attr_stock($equipment_id,'stock');
            
        ?>
            <tr>
                <td><?=$i++?></td>
                <td><a href="admin.php?view=<?=$view?>&pr_header_id=<?=$pr_header_id?>&d=wm&id=<?=$pr_warehouse_id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                <td><?=$fuel?></td>
                <td><?=$equipment?></td>
                <td><input type='text' class='textbox3 align-right' value='<?=$quantity?>'  /></td>
            </tr>
        <?php
        }
        ?>
    </table>
	<?php
	}
    ?>
     

   	<?php
	}
    ?>
</form>
<script type="text/javascript">
j(function(){	
	<?php
		if($b == "Add Details"){
			$active_state = 0;	
		}else if($b == "Add Service Details"){
			$active_state = 1;
		}else if($b == "Add Equipment Details"){
			$active_state = 2;	
		}else if($b == "Add Fuel Details"){
			$active_state = 3;	
		}else{
			$active_state = 0;	
		}
	?>
	
	j("#use_qty").change(function(){
		computeReqQty();
	});
	
	j("#quantity,#warehouse_qty").keyup(function(){
		computeReqQty();
	});	
	

	j("#accordion").accordion({active : <?=$active_state?> , collapsible : true});

	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
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
	
	j('#fuel_consumption,#fuel_quantity,#fuel_days,#fuel_cost,#warehouse_qty2').keyup(function(){
		computeFuelQty();
	});
	
	j('#use_qty2').change(function(){
		computeFuelQty();
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

function computeReqQty(){
	var request_qty = j("#quantity").val();
	var warehouse_qty = j("#warehouse_qty").val();
	var total = 0;
	
	if(j("#use_qty").is(":checked")){
		total = request_qty - warehouse_qty;
	}else{
		total = request_qty;
	}
	j("#req_qty").val(total);	
}

function computeFuelQty(){
	var consumption = j('#fuel_consumption').val();
	var quantity  = j('#fuel_quantity').val();		
	var days	= j('#fuel_days').val();
	var cost = j('#fuel_cost').val();
	
	
	var fuel_qty = consumption * quantity * days ;
	
	
	j('#total_fuel').val(fuel_qty);
	
	
	var warehouse_qty = j('#warehouse_qty2').val();
	
	if(j('#use_qty2').is(':checked')){
		total = fuel_qty - warehouse_qty;
	}else{
		total = fuel_qty;
	}
	
	var amount = total * cost;
	j('#fuel_amount').val(amount);
	j('#req_qty2').val(total);	
}
</script>