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
			  id
			from
				  labor_budget as h,
				  projects as p
			where
				h.project_id = p.project_id
			and
				 h.project_id ='$project_id'
			and
			     h.is_deleted !='1'
		    and
				h.status !='C'
			order by
				work_category_id asc, 
				sub_work_category_id asc
	";	
	$result = mysql_query($sql) or die(mysql_error());
	$list = array();
	while($r=mysql_fetch_assoc($result)){
		$budget_header_id = $r['id'];
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
                  labor_budget_details as h,
				  work_type as w,
				  labor_budget as lb
             where
                labor_budget_id = '$budget_header_id'
			 and
				w.work_code_id=h.work_code_id
			 and
				lb.id=h.labor_budget_id
			and
				lb.status !='C'
        ";
        $result=mysql_query($query);
        $r=mysql_fetch_assoc($result);
        
       /*$project_id		= $r['project_id'];
        $project_name	= $r['project_name'];
        $owner			= $r['owner'];
        $location		= $r['location'];*/
       $work_category_id		= $r['work_category_id'];
       $sub_work_category_id	= $r['sub_work_category_id'];
        
		$work_category		= $options->attr_workcategory($work_category_id,'work');
        $sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
            
        //$status			= $r['status'];
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
				*
			from
				labor_budget_details as d, work_type as p
			where
				d.work_code_id = p.work_code_id
			and
				d.labor_budget_id='$budget_header_id'
			and
				d.is_deleted !='1'
			order by p.work_code_id asc
		";
		
		$result=mysql_query($query) or die(mysql_error());		
		$rows = mysql_num_rows($result);
		if($rows > 0):
		?>
        	<strong>Labor Budget Cost</strong>
        	<table cellspacing="0">
            	<tr>
                	<th>Description</th>
                    <th width="100">Quantity</th>
					<th width="100">No. of Person</th>
                    <th>Unit</th>
                    <th width="100">Unit Price</th>
                    <th width="100">Amount</th>
                </tr>
           		<?php
				$total_amount=0;
				while($r=mysql_fetch_assoc($result)):
					
					$unit			= $r['unit'];
					$stock			= $r['description'];
					
					$quantity 		= $r['qty'];
					$per 			= $r['no_per'];
					$f_qty			= $r['qty'] * $r['no_per'];
						if($r['tag']==1){
							$cost				= $r['wt_price_per_unit'];
						}else{
							$cost				= $r['price_per_unit'];
						}
					$amount			= $f_qty*$cost;
					
					$total_amount += $amount;
					
					
				?>
                    <tr>
                        <td><?=htmlentities($stock)?></td>
                        <td class="align-center" ><?=number_format($quantity,2,'.',',')?></td>
						<td class="align-center" ><?php
						if($unit!="Lot"){
							echo number_format($per,2,'.',',');
						}
						?></td>
                        <td class="align-center"><?=$unit?></td>
                        <td class="align-right"><?=number_format($cost,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr>
                	<td colspan="5"  class="align-right"><strong>Total Labor Budget Cost</strong></td>
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
    <?php
    endforeach;
    ?>   
    <div class="content">
    <table>
    	<caption style="padding:3px; border-top:1px solid #000; border-bottom:1px solid #000; font-weight:bold;">Summary of Cost</caption>
    	
        <tr>
        	<td>Labor Cost</td>
            <td class="align-right"><?=number_format($total_labor_cost,2,'.',',');?></td>
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
