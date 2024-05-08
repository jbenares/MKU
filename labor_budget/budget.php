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
	$labor_budget_id = $_REQUEST['id'];
	$project_id			= $_REQUEST['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$description		= $_REQUEST['description'];
	$date				= $_REQUEST['date'];
	$work_category_id	= $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	$remarks			= $_REQUEST['remarks'];
	$work_type = $_REQUEST['work_type'];
	$qty = $_REQUEST['qty'];
	$per = $_REQUEST['per'];
	
	$user_id			= $_SESSION['userID'];
	$id		= $_REQUEST['id'];
	
	if($b == "Unfinish"){
		mysql_query("
			update 
				labor_budget
			set
				status = 'S'
			where
				id = '$labor_budget_id'
		") or die(mysql_error());
	}	
	
	
	if($b == "M"){
		$lb_id = $_REQUEST['lb_id'];
		mysql_query("
			update
				labor_budget_details
			set
				is_deleted = '1'
			where
				id = '$lb_id'
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
	
	
			$query="
				update
					labor_budget
				set
					project_id		= '$project_id',
					date			= '$date',
					work_category_id = '$work_category_id',
					sub_work_category_id = '$sub_work_category_id',
					remarks	= '$remarks',
					status='S'
				where
					id = '$labor_budget_id'
			";	
			
			mysql_query($query) or die(mysql_error());
			
			$budget_header_id = mysql_insert_id();
					
			$msg="Transaction Saved";
		
	}else if($b=="Update"){
	
		$query="
			update
				labor_budget
			set
				project_id		= '$project_id',
				date			= '$date',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				remarks	= '$remarks',
				status='S'
			where
				id='$labor_budget_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
		
	}else if($b=="Add Details"){
		$t_q = $per*$qty;
			mysql_query("
				insert into
					labor_budget_details
				set	
					work_code_id 	= '$work_type',
					labor_budget_id	= '$labor_budget_id',
					qty = '$qty',
					no_per = '$per',
					price_per_unit='$_REQUEST[price]',
					tag = '0',
					total_qty = '$t_q',
					date_added = NOW()
			") or die(mysql_error());
			
			$msg = "Transaction Added";
	
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
				labor_budget
			set
				status='C'
			where
				id = '$labor_budget_id'
		";	
		mysql_query($query);
		
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				labor_budget
			set
				status='F'
			where
				id = '$labor_budget_id'
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
	}

	$query="
		select
			*
		from
			labor_budget
		where
			id ='$labor_budget_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);

	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	//$description		= $r['description'];	
	$status				= $r['status'];
	$date				= (empty($r['date']) || $r['date']=="0000-00-00")?"":$r['date'];
	
	//$scope_of_work		= $r['scope_of_work'];
	$work_category_id	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$remarks			= $r['remarks'];

?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>LABOR BUDGET</div>
    <form name="header_form" id="header_form" action="" method="post">
    
   	<div style="width:50%; float:left;">
        <div class="module_actions"><b>Budget # :<b/>
            <input type="text" name="labor_budget_id" id="labor_budget_id" value="<?=str_pad($labor_budget_id, 8, "0", STR_PAD_LEFT);?>" readonly class="textbox" style="font-weight: bold;" />
 	    <hr style="border:1px solid #000000;">
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
		$status = 'S';
		if( $status == 'S'):	
		?>
		<div id="accordion">
			<h3><a href="#">ADD LABOR DETAILS</a></h3>
			<div>
				<div class="inline">
					<div>Select Work Type :</div>
					<?php
						$sql = "select * from work_type where is_deleted != '1'";
						$r = mysql_query($sql);
						?>
						<div>
						<select name="work_type" onchange="getcost(this.value);">
						
						<?php
						while($row = mysql_fetch_assoc($r)){
						extract($row);
						?>
							<option value="<?=$work_code_id?>"><?=$company_code;?> | <?=$description?> | per <?=$unit?></option>
						<?php
						}
					?>
						</select>
					</div>	
				</div>
					<div class="inline">
					<div>Quantity: </div>
						<div><input type="text" name="qty" class="textbox3" /></div>
					</div>
					<div class="inline">
					<div>No. of Person :</div>
						<div><input type="text" name="per" class="textbox3" /></div>
					</div>
					<div class="inline">
					<div>Unit Price: </div>        
						<div><input type="text" size="20" name="price" class="textbox3" /></div>
					</div>
					<!--<input type="text" class="textbox hinder-submit" name="stock_name" id="stock_name" onclick="this.select();" />
					<input type="hidden" name="stock_id" id="stock_id"  /> -->
				  
				<div style="margin-top:10px;">
                    <input type="submit" name="b" value="Add Details" />
                    <!-- <input type="submit" name="b" value="Update Details"  /> -->
                    <!--<input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();"  /> -->
               	</div>
		</div>  
		 <?php
		endif;
		?>
	</div>    
 </div>  
    <div style="float:right;width:50%;" >
    	<div class="accordion">
        	<?php
			$query = "
				 select
					  *
					from
						labor_budget_details as d, work_type as p
					where
						d.work_code_id = p.work_code_id
					and
						d.labor_budget_id='$labor_budget_id'
					and
						d.is_deleted !='1'
					order by d.id asc
				
			";
			$result=mysql_query($query) or die(mysql_error());
			$rows = mysql_num_rows($result);
			if($rows > 0):
			?>
            <div class="ui-widget-header head">
                <h3><img src="images/cart.png" style="margin-right:15px;" /> LABOR DETAILS</h3>
            </div>
            <div style="width:100%; overflow:auto;" >
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr>
                        <th width="20">#</th>
                        <td width="20" align="center"></td>
                        <th width="100">Description</th>
                        <th width="200">Work Category</th>                        
                        <th width="100">Unit</th>
						<th width="100">Qty</th>
						<th width="100">No. of Person</th>
                        <th width="100">Price Per Unit</th>
						<th width="100">Total Qty</th>
                        <th width="100">Total Amount</th>
    
                        <!--<th>Warehouse Quantity</th>
                        <th>Project Quantity</th>
                        <th>Issued Quantity</th>
                        <th>Total Stocks</th>
                        <th>Balance</th>-->
                    </tr>
                    <?php
                    $i=1;
                    while($r=mysql_fetch_assoc($result)){
						$id = $r['id'];
                        $labor_budget_id	= $r['labor_budget_id'];
                        $description			= $r['description'];
                        $work_category				= $r['work'];                        
                        $unit				= $r['unit'];
						$qty				= $r['qty'];
						if($r['tag']==1){
							$price_per_unit				= $r['wt_price_per_unit'];
						}else{
							$price_per_unit				= $r['price_per_unit'];
						}
                        
						$per = $r['no_per'];
						
						$t_q 	= $r['total_qty'];
                        $t_total = $t_q * $price_per_unit;             
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><a href="admin.php?view=<?=$view?>&id=<?=$labor_budget_id?>&b=M&lb_id=<?=$id?>" onclick="return approve_confirm();"><img src="images/trash.gif" style="cursor:pointer;" /></a></td>
                            <td><?=$description?></td>
                            <td><?=$work_category?></td>                            
                            <td><?=$unit?></td>
							<td><?=$qty?></td>
							<td><?=$per?></td>
                            <td><?=$price_per_unit?></td>
							<td><?=$t_q?></td>
							<td><?=number_format($t_total, 2)?></td>
                            
    <!--                        <td class="align-right"><?=number_format($warehouse_qty,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($project_warehouse_qty,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($issued_qty,2,'.',',')?></td>
                            <td class="align-right"><?=number_format($total_stocks,2,'.',',')?></td>
                            <td class="align-right highlight"><?=number_format($balance,2,'.',',')?></td>
    -->                        
                           <!-- <input type='hidden' value='<?=$stock_id?>' />
                            <input type='hidden' name='budget_detail_id[]' value='<?=$budget_detail_id?>' /> -->
                        </tr>
                    <?php
                    }
                    ?>
                </table>
       
	</div>
            <?php
			endif;
			?>
         <!-- D ELETED SOME PARTS HERE @ NOTEPAD-->
           
     <div style="clear:both;">
     <?php
		if($b == "Print Preview" && $labor_budget_id){
		
			echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_labor_budget_reports_per.php?id=$labor_budget_id' width='100%' height='500'>
					</iframe>";
	?>
	<?php
    }
    ?>
     </div>
  
     </form>
    
</div>
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
	
	/*j("#qty,#cost").keyup(function(){
		var quantity = j("#quantity").val();
		var cost = j("#cost").val();
		
		var amount = quantity * cost;
		j("#amount").val(amount);
	});*/
	
	
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

function getcost(id){
	j.post("labor_budget/getcost.php",{id:id},function(e)
	{
		j('input[name=price]').val(e);
	});
}
</script>
	