<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$date_from=$_REQUEST[date_from];
	$date_to=$_REQUEST[date_to];
	$project_id = $_REQUEST['project_id'];
	$foreman_id = $_REQUEST['foreman_id'];
	$query="
		select
			*
		from 
			project_payroll_header
			where status != 'C'";

		if($date_from && $date_to){
			$query .= " AND date BETWEEN '$date_from' AND '$date_to'";
		}

		if($project_id){
			$query .= " AND project_id = '$project_id'";
		}
		if($foreman_id){
			$query .= " AND foreman_id = '$foreman_id'";
		}

		$query .= " order by date asc";
	
	$result=mysql_query($query);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Project Payroll Report</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
<style type="text/css">
	.table-content{
		border-collapse:collapse;	
	}
	
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	PROJECT PAYROLL REPORT
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="25%"><strong>Date Period:</strong></td>
                    <td width="75%"><?php if($date_from && $date_to){
                    	echo date("F j, Y",strtotime($date_from)) . " to " . date("F j, Y",strtotime($date_to));
                    } ?></td>
               	</tr>
               	<tr>
                	<td width="25%"><strong>Project:</strong></td>
                    <td width="75%"><?=$options->getAttribute('projects','project_id',$project_id,'project_name');?></td>
               	</tr>
               	<tr>
                	<td width="25%"><strong>Foreman:</strong></td>
                    <td width="75%"><?=$options->getAttribute('employee','employeeID',$foreman_id,'employee_fname') . " " . $options->getAttribute('employee','employeeID',$foreman_id,'employee_lname');?></td>
               	</tr>
               		<tr>
                	<td width="25%"><strong>Total Project Payroll Amount:</strong></td>
                    <td width="75%"><?=number_format($options->getProjectPayrollAmount($date_from,$date_to, $project_id,$foreman_id),2);?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table class="table-content">
            	<tr >
                	<th width="8%">DATE</th>
                    <th>PROJECT</th>
                    <th>FOREMAN</th>
                    <th>AMOUNT</th>
                </tr>	
             	<?php while($head = mysql_fetch_array($result)) { ?>
             		<tr style='border:2px solid #000 '>
             			<td><?php echo date("F j, Y", strtotime($head['date'])); ?></td>
             			<td><?=$options->getAttribute('projects','project_id',$head['project_id'],'project_name');?></td>
             			<td><?=$options->getAttribute('employee','employeeID',$head['foreman_id'],'employee_fname') . " " . $options->getAttribute('employee','employeeID',$head['foreman_id'],'employee_lname');?></td>
             			<td>
             				<table style='border: 2px solid #fff; border-collapse: collapse' cellpadding="0" cellspacing="0">
             				<!-- 	<tr>
             						<td>Labor Expense</td>
             						<td>UOM</td>
             						<td>Quantity</td>
             						<td>Price</td>
             						<td>Total Price</td>
             					</tr> -->

             				<?php $queryd = mysql_query("select * from project_payroll_detail where project_payroll_header_id = '$head[project_payroll_header_id]'");
             						while($detail = mysql_fetch_array($queryd)){ 
             								?>
             							
             								<tr >
             									<td><?php echo $detail['labor_expense'] ?></td>
             									<td><?php echo $detail['uom'] ?></td>
             									<td><?php echo $detail['quantity'] ?></td>
             									<td><?php echo number_format($detail['price'],2) ?></td>
             									<td><?php echo number_format($detail['total_price'],2) ?></td>
             								</tr>
             						<?php
             						} 
             				?>


             							<?php
             							 $querydd = mysql_query("select * from project_payroll_discount where project_payroll_header_id = '$head[project_payroll_header_id]'");
             								while($discount = mysql_fetch_array($querydd)){ 
             										?>
             							
             								<tr>
             									<td colspan='4' style='text-align: right;'><?php echo $discount['discount_name'] ?></td>
             									<td><?php echo "(" .number_format($discount['discount_amount'],2) . ")" ?></td>
             								</tr>
             							
             							<?php
             							} ?>
             				</table>

             			</td>
             		</tr>
             	<?php } ?>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>