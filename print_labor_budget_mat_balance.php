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
	letter-spacing:2px;
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
			order by
				work_category_id asc, 
				sub_work_category_id asc
	";	
	$result = mysql_query($sql) or die(mysql_error());
	$list = array();
	//echo $budget_header_id;
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
                  <td width="80">Items</td>
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
				*,p.description as work,d.id as id
			from
				labor_budget_details as d, 
				work_type as p, 
				po_header as po
			where
				d.work_code_id = p.work_code_id
			and
				d.labor_budget_id='$budget_header_id'
			and
				d.is_deleted !='1'
			and
				po.po_type='S'
			and
				(po.status='F' or po.status = 'S')
            and
                po.budget_header_id=d.labor_budget_id
			and
				p.is_deleted !='1'
			order by p.work_code_id asc
		";
		
		$result=mysql_query($query) or die(mysql_error());		
		$rows = mysql_num_rows($result);
		if($rows > 0):
		?>
        	<strong>Labor Budget Material Cost</strong>
        	<table cellspacing="0">
            	<tr>
                	<th>Description</th>
                    <th width="100">Budget Amount</th>
					<th>PO Amount</th>
					<th>Payroll Amount</th>
					<th>Budget Balance</th>
                </tr>
           		<?php
				$total_amount=0;
				$balance=0;
				$payroll=0;
				$total_po=0;
				while($r=mysql_fetch_assoc($result)){
					//echo $r[id];
					//work type info
					$unit			= $r['unit'];
					$stock			= $r['work'];
						if($r['tag']==1){
							$cost				= $r['wt_price_per_unit'];
						}else{
							$cost				= $r['price_per_unit'];
						}
			
					//budget
					$quantity 		= $r['qty'];
					$per 			= $r['no_per'];
					$f_qty			= $quantity * $r['no_per'];
					$amount			= $f_qty*$cost;	
						
					//ordered
					$sql=mysql_query("SELECT * FROM spo_detail WHERE po_header_id = '".$r[po_header_id]."'");
					$po_amount = 0;
					while($f=mysql_fetch_assoc($sql)){
						$sql2=mysql_query("SELECT * FROM sub_spo_detail WHERE spo_detail_id = '".$f[spo_detail_id]."'");
						
						while($f2=mysql_fetch_assoc($sql2)){
							$po_amount+=$f2[quantity]*$f2[unit_cost];
						}
					}
					
					//payroll amount
					$py=mysql_query("select sum(amount + overtime) as amt from po_header_payroll where payroll_header_id='".$r[payroll_header_id]."'");
					$fet=mysql_fetch_assoc($py);
					$payroll		= $fet['amt'];
				?>
                    <tr>
                        <td><?=htmlentities($stock.''.$r[po_header_id])?></td>
						<td class="align-center" ><?=number_format($amount,2,'.',',')?></td>
						<td class="align-center" ><?=number_format($po_amount,0,'.',',')?></td>
						<!--<td class="align-center" >&nbsp;</td>
						<td class="align-center" >&nbsp;</td>-->
						<td class="align-center" ><?=number_format($payroll,2,'.',',')?></td>
						<td class="align-center" ><?=number_format($amount-$payroll,2,'.',',')?></td>
                    </tr>
                <?php
				$total_po += $po_amount;
				}
				?>
				<tr style="border-top:2px solid black;">
					<td><b>TOTAL</b></td>
					<td>&nbsp;</td>
					<td style="text-align:right;"><?=number_format($total_po,2)?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
            </table>
        <?php
		endif;
        ?>
        </div><!--End of content-->
    <?php
    endforeach;
    ?>   
</div>
</body>
</html>

