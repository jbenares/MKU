<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	function getLatestcost($stock_id){
		$cost = 0;
			$query="
				select
					cost
				from
					rr_detail
				where
					stock_id = '$stock_id' 
				order by rr_detail_id desc
				LIMIT 0,1	
			";
			
			$result=mysql_query($query) or die(mysql_error());
			
			$r=mysql_fetch_assoc($result);
		
			$cost = $r['cost'];
			
			if($cost == 0){
				
				$q = mysql_query("Select cost from productmaster where stock_id = '$stock_id'") or die (mysql_error());
				$r = mysql_fetch_assoc($q);
				
				$cost = $r['cost'];
				return $cost;
			}else{
				return $cost;
			}
			
		}
		
	$options=new options();	
	
	$pr_header_id=$_REQUEST[id];

	
	$query="
		select
			  *
		 from
			  pr_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			pr_header_id = '$pr_header_id'
	";
	$result=mysql_query($query);
	$r = $aTrans = mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$description	= $r['description'];
	$status			= $r['status'];
	$user_id		= $r['user_id'];
	$approved_by	= $r['approved_by'];
	$date			= $r['date'];
	$date_needed	= $r['date_needed'];
	$type			= $r['type'];
	$requestor		= $r['requestor'];
	
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work = $r['scope_of_work'];
	
	$work_category = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_report2.css" />
<style type="text/css">
.content .rtp_table td{
	border:none;
	border-left:1px solid #000;
	border-right:1px solid #000;	
}
.content .rtp_table tr:last-child td{
	border-bottom:1px solid #000;	
}
body *{
	font-size:15px;	
}
@media screen {
    div.divFooter {
        display: none;
    }
}
@media print {
    div.divFooter {
        position: fixed;
        bottom: 0;

        font-family: "Times New Roman";
        font-size: 11px;
    }
}
</style>

</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
	
    <?php
		require("form_heading.php");
    ?>
    <div style="text-align:right; font-weight:bolder;">
        RTP #. : <?=str_pad($pr_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>       
    <div style="text-align:center; font-size:12px;">
    	Bill of Materials
    </div>
    <div class="header" style="">
        <table style="width:100%;">
            <tr>
                <td width="13%">Project / Section:</td>
                <td width="43%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
                
                <td width="16%">Date Requested</td>
                <td width="28%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <tr>
              <td>Scope of Work:</td>
              <td style="border-bottom:1px solid #000;"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>
              
              <td>Date Needed:</td>
              <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date_needed))?></td>
            </tr>
            <tr>
            	<td>Description :</td>
                <td colspan="3" style="border-bottom:1px solid #000;"><?=$description?></td>
            </tr>
           
        </table>
    </div><!--End of header--><br />
	<div class="content" >
        <?php
			if($type=='labor'){
				$query="
					select
						*
					from
						work_type as w,labor_budget_pr as lb,
						labor_budget_details as d
					where
						lb.pr_header_id='$pr_header_id'
					and
						lb.labor_budget_details_id=d.id
					and
						w.work_code_id=d.work_code_id
					and
						lb.is_deleted !='1'
					and
						w.is_deleted !='1'
					and
						d.is_deleted !='1'
					";
				$result=mysql_query($query) or die(mysql_error());	
				
				?>
				<table cellspacing="0" class="rtp_table">
					<tr>
						<th width="40">Qty</th>
						<th width="40">Unit</th>
						<th width="40">No. of Person</th>
						<th width="40">Qty<br />(Optional)</th>
						<th width="40">Unit<br />(Optional)</th>
						<th>Item Description </th>
						<th width="40" >In-House Budget</th>
						<th width="40">Acutal Received</th>
						<th width="40">Balance</th>
					</tr>
				
				
				<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)){
					$request_quantity = $r['requested_qty'];
					$stock = $r['description'];
					$unit = $r['unit'];
					$per = $r['no_per'];
					
					$total_budget = $r['total_qty'];
					$actual_received 			= $options->inventory_actual_received(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
					$balance = $total_budget - $actual_received;
				?>
                    <tr>
                        <td><div align="right"><?=$request_quantity?></div></td>
                        <td><?=$unit?></td>
						<td style="text-align:right;"><?=$per?></td>
                        <td style="text-align:right;"><?=$quantity2?></td>
                        <td><?=$unit2?></td>
                        <td><?=$stock?></td>
                        <td class="align-right"><?=number_format($total_budget,0,'.',',')?></td>
                        <td class="align-right"><?=number_format($actual_received,0,'.',',')?></td>
                        <td class="align-right"><?=number_format($balance,0,'.',',')?></td>
                    </tr>
                <?php
					}
				?>
				   <?php
                echo '<tr>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>********** Nothing Follows **********</td>';
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';
				echo '</tr>';
				?>
				</table>
				<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top: -7px;" class="summary">
					<tr>
						<td>Prepared By:<p>
                        <input type="text" class="line_bottom" value="<?=$options->getUserName($user_id);?>"/> 
						<br><input type="text" style="border:none; text-align:center; font-size:15px;" value ="Authorized Personnel"/> <br>
                        <span style='font-size:10px;'><?=$aTrans['datetime_encoded']?></span>
                   	</p></td>
                   	<td>Requested By:<p>
                        <input type="text" class="line_bottom" /><br>Requisitioner</p></td>
                    <td>Checked By:<p>
                        <input type="text" class="line_bottom" /><br>Warehouseman</p></td>
                    <td>Approved By:<p>
                        <input type="text" class="line_bottom" /><br>G.M./ President </p></td>
					</tr>
				</table>
				<?php
			}else{
				
				$query="
				select
					pr_detail_id,
					d.stock_id,
					p.stockcode,
					p.barcode,
					p.stock,
					/*p.cost,*/
					p.unit,
					d.quantity,
					d.request_quantity,
					d.warehouse_quantity,
					d.quantity2,
					d.unit2,
					d.details
				from
					pr_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					pr_header_id='$pr_header_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        
        	<table cellspacing="0" class="rtp_table">
            	<tr>
                	<th width="50">Qty</th>
                    <th width="50">Unit</th>
                    <th width="50">Item Code</th>
                    <!--<th width="50">Part #</th>-->
                    <th>Item Description </th>
					<th width="100">Latest Price</th>
                    <!--<th width="40">c/o MCD</th>
                    <th width="40" >In-House Budget</th>
                    <th width="40">Acutal Received</th>
                    <th width="40">Balance</th>
					<th width="40">Inventory Balance</th>-->
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$quantity 		= $r['quantity'];
					$request_quantity = $r['request_quantity'];
					$warehouse_quantity	= $r['warehouse_quantity'];
					
					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$cost 			= $r['cost'];
					$stockcode		= $r['stockcode'];
					$barcode		= $r['barcode'];
					$unit			= $r['unit'];
					$budget_detail_id	= $r['budget_detail_id'];
					
					$quantity2		= $r['quantity2'];
					$unit2			= $r['unit2'];
					$details		= $r['details'];
					
					$rr_cost					= getLatestcost($stock_id);
					$actual_received 			= $options->inventory_actual_received(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
					$total_budget 				= $options->budget_stock($stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
					$balance = $total_budget - $actual_received;
					
				?>
                    <tr>
                        <td><div align="right"><?=$request_quantity?></div></td>
                        <td><?=$unit?></td>
                        <td><?=$stockcode?></td>
                        <!--<td><?=$barcode?></td>-->
                        <td><?=$stock?> <?=($details)?"( $details )":""?></td>
						<td style= "text-align:right;"><?=number_format($rr_cost,2)?></td>
                       <!--<td class="align-right"><?=number_format($warehouse_quantity,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($total_budget,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($actual_received,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($balance,2,'.',',')?></td>
						<td class="align-right"><?=number_format($options->inventory_warehouse(date("Y-m-d"),$r['stock_id']),2)?></td>-->
                    </tr>
					
                <?php
				endwhile;
				?>
                
                <?php
				$result = mysql_query("
					select
						*
					from
						pr_service_detail as d, productmaster as p
					where
						d.stock_id = p.stock_id
					and
						pr_header_id = '$pr_header_id'
					and
						allowed = '1'
				") or die(mysql_error());
				while($r = mysql_fetch_assoc($result)){
					$stock = $r['stock'];
					$unit  = $r['unit'];
					
					$quantity 		= $r['quantity'];
					$days	 		= $r['days'];
					$rate_per_day	= $r['rate_per_day'];
					$amount			= $r['amount'];
					
                ?>
                <tr>
                    <td><div align="right">&nbsp;</div></td>
                    <td>&nbsp;</td>
                    <td><div align="right">&nbsp;</div></td>
                    <td>&nbsp;</td>
                    <td><?="$quantity $stock X $days DAYS X $rate_per_day / DAY"?></td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php } ?>
                
                <?php
                echo '<tr>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				//echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>********Nothing Follows******** </td>';
				echo '<td>&nbsp;</td>';	
				/*echo '<td>&nbsp;</td>';	
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';
				echo '<td>&nbsp;</td>';	
                echo '<td>&nbsp;</td>';		*/				
				echo '</tr>';
				?>
                
                
                
                <?php
				if($i<15) {
					for($newi=$i;$newi<=17;$newi++) {
						echo '<tr>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						/*echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';
						echo '<td>&nbsp;</td>';	
						echo '<td>&nbsp;</td>';	*/
						echo '</tr>';
					}
				}
                ?>
                
            </table>
            <?php
				$result=mysql_query("
					select
						pr_service_detail_id,
						d.stock_id,
						p.stock,
						p.unit,
						d.quantity,
						d.days,
						d.rate_per_day,
						d.amount
					from
						pr_service_detail as d, productmaster as p
					where
						d.stock_id = p.stock_id
					and
						pr_header_id='$pr_header_id'
					and	
						allowed = '1'
						
				") or die(mysql_error());
            ?>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$stock_id 		= $r['stock_id'];
					$quantity 		= $r['quantity'];
					$stock 			= $r['stock'];
					$days			= $r['days'];
					$rate_per_day	= $r['rate_per_day'];
					$amount			= $r['amount'];
					
					$service_budget = $options->service_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
					$service_received = $options->service_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
					$service_balance = $service_budget - $service_received;
					
				?>
                    <tr>
                        <td><?=$stock?></td>
                        <td><?=$quantity?></td>
                        <td><?=$days?></td>
                        <td class="align-right"><?=number_format($rate_per_day,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($service_budget,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($service_received,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($service_balance,4,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                
            </table>
            <?php
				$result=mysql_query("
					select
						pr_equipment_detail_id,
						d.stock_id,
						p.stock,
						p.unit,
						d.quantity,
						d.days,
						d.rate_per_day,
						d.amount
					from
						pr_equipment_detail as d, productmaster as p
					where
						d.stock_id = p.stock_id
					and
						pr_header_id='$pr_header_id'
					and	
						allowed = '1'
						
				") or die(mysql_error());
            ?>
            <!--
        	<table cellspacing="0" style="margin-top:30px;">
            	<tr>
                	<th>Description</th>
                    <th width="40">No</th>
                    <th width="40">No. of Days</th>
                    <th width="40">Rental / Day</th>
                    <th width="40">Amount</th>
                    <th width="40">In-House Budget</th>
                    <th width="40">Rental Approved</th>
                    <th width="40">Balance</th>
                    
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$stock_id 		= $r['stock_id'];
					$quantity 		= $r['quantity'];
					$stock 			= $r['stock'];
					$days			= $r['days'];
					$rate_per_day	= $r['rate_per_day'];
					$amount			= $r['amount'];
					
					$equipment_budget = $options->equipment_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
					$equipment_received = $options->equipment_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
					$equipment_balance = $equipment_budget - $equipment_received;
					
				?>
                    <tr>
                        <td><?=$stock?></td>
                        <td><?=$quantity?></td>
                        <td><?=$days?></td>
                        <td class="align-right"><?=number_format($rate_per_day,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($equipment_budget,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($equipment_received,4,'.',',')?></td>
                        <td class="align-right"><?=number_format($equipment_balance,4,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                
            </table>
           	-->
            
            <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top: 20px;" class="summary">
                <tr>
                    <!--<td>
					<p>
                        <input type="text" class="line_bottom" style="text-align:center;" value="<?=$options->getUserName($user_id);?>"/> 
						<br><input type="text" style="border:none; text-align:center; font-size:15px;" value ="Authorized Personnel"/> <br>
                        <span style='font-size:10px;'><?=$aTrans['datetime_encoded']?></span>
                   	<br />Prepared By:</p>
					</td>-->
                   	<td><p>
                        <input type="text" style="width: 200px; text-align: center;" class="line_bottom" value="<?=$requestor?>" /><br>Requested By:</p>
					</td>
                    <td><p>
                        <input type="text" class="line_bottom" /><br>Noted By:</p>
					</td>
                    <td><p>
                        <input type="text" class="line_bottom" /><br>Approved By: </p>
					</td>
                </tr>
            </table>
            
            
        </div><!--End of content-->
				<?php
			}
			
			?>
    </div><!--End of Form-->
</div>
<!--
<div class="divFooter">
    F-PUR-003a<br>
    Rev. 1 06/21/16
</div>
<div class="page-break"></div>-->
</body>
</html>