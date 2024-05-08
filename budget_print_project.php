<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$project_id=$_REQUEST[id];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

@media print and (width: 8.5in) and (height: 14in) {
	@page {
		margin: 1in;
	}
	.page-break{
		page-break-before:always;  
	} 
}


	
body
{
	size: legal portrait;		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
.container{
	width:100%;
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	width:100%;
	text-align:left;
	

}
.header table td, .content table td
{
	padding:3px;
	
}

.content table{
	margin-top:10px;
	margin-left:30px;
	width:80%;
	border-collapse:collapse;
}
.content table td,.content table th{
	padding:5px;
}
.content table tr th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	text-align:center;
}

.content table tr:last-child td{
	border-top:3px double #000;
}
hr
{
	margin:40px 0px;	
	border:1px dashed #999;

}

.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}

.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}

.footer td{
	border:none;
}
.align-right{
	text-align:right;	
}
.align-center{
	text-align:center;	
}

.costing-header{
	margin-left:10px;	
}

.last-content{
	page-break-after:always;  
} 


</style>
</head>
<body>
<div class="container">
	<?php
		require("form_heading.php");
	?>
	
	<?php
	$query="
		select
			 *		  
		 from
			  projects
		 where
			project_id = '$project_id'

	";
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$owner			= $r['owner'];
	$location		= $r['location'];
	?>

	<div class="header" style="font-weight:bold; margin-bottom:20px;">
        <table style="width:100%;">
            <tr>
                <td width="14%">Project</td>
                <td width="55%">: <?=$project_name?></td>
            </tr>
            <tr>
              <td>Location</td>
              <td >: <?=$location?></td>
            </tr>
            <tr>
              <td>Owner</td>
              <td >: <?=$owner?></td>
            </tr>
        </table>
    </div><!--End of header-->
	
    
    <?php
    $sql = "select
			  budget_header_id
		 from
			  budget_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			  h.project_id ='$project_id'
		order by
			work_category_id asc, 
			sub_work_category_id asc
	";	
	$result = mysql_query($sql) or die(mysql_error());
	$list = array();
	while($r=mysql_fetch_assoc($result)){
		$budget_header_id = $r['budget_header_id'];
		array_push($list,$budget_header_id);
	}
	?>
    
    
    
	<?php
	$total_material_cost 	= 0;
	$total_labor_cost		= 0;
	$total_equipment_cost	= 0;
	$total_fuel_cost		= 0;
    foreach($list as $budget_header_id):
    ?>    
		<?php
        $query="
            select
                 *		  
             from
                  budget_header as h,
                  projects as p
             where
                h.project_id = p.project_id
            and
                budget_header_id = '$budget_header_id'
        ";
        $result=mysql_query($query);
        $r=mysql_fetch_assoc($result);
        
        $project_id		= $r['project_id'];
        $project_name	= $r['project_name'];
        $owner			= $r['owner'];
        $location		= $r['location'];
        $work_category_id		= $r['work_category_id'];
        $sub_work_category_id	= $r['sub_work_category_id'];
        
        $work_category		= $options->attr_workcategory($work_category_id,'work');
        $sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
            
        $status			= $r['status'];
        ?>
        
        <div class="header" style="font-weight:bold; margin-bottom:20px;">
            <table style="width:100%;">
                <tr>
                  <td width="80">Item</td>
                  <td >: <?=$work_category?></td>
                </tr>
                <tr>
                  <td width="80">Sub Item</td>
                  <td >: <?=$sub_work_category?></td>
                </tr>
               
            </table>
        </div><!--End of header-->

		<div class="content" >
        <?php
		$query="
			select
				budget_detail_id,
				d.stock_id,
				p.stockcode,
				p.stock,
				p.unit,
				d.quantity,
				d.cost,
				d.amount
			from
				budget_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				budget_header_id='$budget_header_id'
			order by stock asc
		";
		
		$result=mysql_query($query) or die(mysql_error());		
		$rows = mysql_num_rows($result);
		if($rows > 0):
		?>
        	<strong>Material Cost</strong>
        	<table cellspacing="0">
            	<tr>
                	<th>Description</th>
                    <th width="100">Quantity</th>
                    <th>Unit</th>
                    <th width="100">Unit Price</th>
                    <th width="100">Amount</th>
                </tr>
           		<?php
				$total_amount=0;
				while($r=mysql_fetch_assoc($result)):
					
					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$stockcode		= $r['stockcode'];
					$unit			= $r['unit'];
					
					$quantity 		= $r['quantity'];
					$cost			= $r['cost'];
					$amount			= $r['amount'];
					
					$total_amount += $amount;
					
					
				?>
                    <tr>
                        <td><?=htmlentities($stock)?></td>
                        <td class="align-center" ><?=number_format($quantity,2,'.',',')?></td>
                        <td class="align-center"><?=$unit?></td>
                        <td class="align-right"><?=number_format($cost,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr>
                	<td colspan="4"  class="align-right"><strong>Total Material Cost</strong></td>
                    <td class="align-right" style="font-weight:bold;"><?=number_format($total_amount,2,'.',',')?></td>
                </tr>
                <?php
				$total_material_cost += $total_amount;
                ?>
            </table>
        <?php
		endif;
        ?>
        </div><!--End of content-->
        
        <div class="content" >
        <?php
		$query="
			select
				d.stock_id,
				p.stock,
				p.unit,
				d.quantity,
				d.days,
				d.rate_per_day,
				d.amount
			from
				budget_service_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				budget_header_id='$budget_header_id'
			order by stock asc
		";
		
		$result=mysql_query($query) or die(mysql_error());		
		$rows = mysql_num_rows($result);
		if($rows > 0):
		?>
        	<strong>Labor Cost</strong>
        	<table cellspacing="0">
            	<tr>
                	<th>Designation</th>
                    <th width="100">No.</th>
                    <th width="100">No. of Day</th>
                    <th width="100">Rate/Day</th>
                    <th width="100">Amount</th>
                </tr>
           		<?php
				$total_amount=0;
				while($r=mysql_fetch_assoc($result)):
					
					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$stockcode		= $r['stockcode'];
					$unit			= $r['unit'];
					
					$quantity 		= $r['quantity'];
					$days			= $r['days'];
					$rate_per_day	= $r['rate_per_day'];
					$amount			= $r['amount'];
					$total_amount += $amount;
					
					
				?>
                    <tr>
                        <td><?=$stock?></td>
                        <td class="align-center" ><?=number_format($quantity,0,'.',',')?></td>
                        <td class="align-center"><?=$days?></td>
                        <td class="align-right"><?=number_format($rate_per_day,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr>
                	<td colspan="4"  class="align-right"><strong>Total Labor Cost</strong></td>
                    <td class="align-right" style="font-weight:bold;"><?=number_format($total_amount,2,'.',',')?></td>
                </tr>
                <?php
				$total_labor_cost += $total_amount;
                ?>
                
            </table>
        <?php
		endif;
        ?>
        </div><!--End of content-->
        
        <div class="content" >
       	<?php
		$query="
			select
				d.stock_id,
				p.stock,
				p.unit,
				d.quantity,
				d.days,
				d.rate_per_day,
				d.amount
			from
				budget_equipment_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				budget_header_id='$budget_header_id'
			order by stock asc
		";
		
		$result=mysql_query($query) or die(mysql_error());		
		$rows = mysql_num_rows($result);
		if($rows > 0):
		?>
        	<strong>Equipment Rental</strong>
        	<table cellspacing="0">
            	<tr>
                	<th>Description</th>
                    <th width="100">No.</th>
                    <th width="100">No. of Day</th>
                    <th width="100">Rate/Day</th>
                    <th width="100">Amount</th>
                </tr>
           		<?php
				$total_amount=0;
				while($r=mysql_fetch_assoc($result)):
					
					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$stockcode		= $r['stockcode'];
					$unit			= $r['unit'];
					
					$quantity 		= $r['quantity'];
					$days			= $r['days'];
					$rate_per_day	= $r['rate_per_day'];
					$amount			= $r['amount'];
					$total_amount += $amount;
					
					
				?>
                    <tr>
                        <td><?=$stock?></td>
                        <td class="align-center" ><?=number_format($quantity,0,'.',',')?></td>
                        <td class="align-center"><?=$days?></td>
                        <td class="align-right"><?=number_format($rate_per_day,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr>
                	<td colspan="4"  class="align-right"><strong>Total Equipment Rental Cost</strong></td>
                    <td class="align-right" style="font-weight:bold;"><?=number_format($total_amount,2,'.',',')?></td>
                </tr>
                <?php
				$total_equipment_cost += $total_amount;
                ?>
            </table>
       	<?php
		endif;
        ?>
        </div><!--End of content-->
        
        <div class="content" >
		<?php
		$query="
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
        	<strong>Fuel, Oil, and Lubricants</strong>
        	<table cellspacing="0">
            	<tr>
                    <th>Fuel</th>
                    <th>Equipment</th>
                    <th width="60">Consumption / Day</th>
                    <th width="60">Quantity</th>
                    <th width="60">No. of Days</th>
                    <th width="60">Fuel Cost/Litter</th>
                    <th width="100">Amount</th>
                </tr>
           		<?php
				$total_amount=0;
				while($r=mysql_fetch_assoc($result)):
					
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
					$total_amount += $amount;
					
					
				?>
                    <tr>
                        <td><?=$fuel?></td>
                        <td><?=$equipment?></td>
                        <td class="align-right"><?=$consumption_per_day?></td>
                        <td class="align-right"><?=$quantity?></td>
                        <td class="align-center"><?=$days?></td>
                        <td class="align-right"><?=$cost_per_litter?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <?php
					$oil_lub = $total_amount * 0.10;
                ?>
                <tr>
                	<td colspan="6"  class="align-center"><strong>Oil & Lubricants (10% of Fuel Cost)</strong></td>
                    <td class="align-right" style="font-weight:bold;"><?=number_format($oil_lub,2,'.',',')?></td>
                </tr>
                <?php
					$total_amount = $total_amount + $oil_lub;
                ?>
                <tr>
                	<td colspan="6"  class="align-right"><strong>Total Fuel Cost</strong></td>
                    <td class="align-right" style="font-weight:bold;"><?=number_format($total_amount,2,'.',',')?></td>
                </tr>
                <?php
				$total_fuel_cost += $total_amount;
                ?>
            </table>
       	<?php
		endif;
        ?>
        </div><!--End of content-->
    <?php
    endforeach;
    ?>   
    
    <div class="content">
    <table>
    	<caption style="padding:3px; border-top:1px solid #000; border-bottom:1px solid #000; font-weight:bold;">Summary of Cost</caption>
    	<tr>
        	<td>Material Cost</td>
            <td class="align-right"><?=number_format($total_material_cost,2,'.',',')?></td>
        </tr>
        <tr>
        	<td>Labor Cost</td>
            <td class="align-right"><?=number_format($total_labor_cost,2,'.',',');?></td>
        </tr>
        <tr>
        	<td>Equipment Cost</td>
            <td class="align-right"><?=number_format($total_equipment_cost,2,'.',',')?></td>
        </tr>
        <tr>
        	<td>Fuel Cost</td>
            <td class="align-right"><?=number_format($total_fuel_cost,2,'.',',')?></td>
        </tr>
        <?php
		$total = $total_material_cost + $total_equipment_cost + $total_labor_cost + $total_fuel_cost;
        ?>
        <tr>
        	<td class="align-right">Total Project Cost</td>
            <td class="align-right" style="font-weight:bolder;"><?=number_format($total,2,'.',',')?></td>
        </tr>
        
    </table>
    </div>
</div>
</body>
</html>

