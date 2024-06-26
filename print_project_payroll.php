<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$project_payroll_header_id=$_REQUEST[id];

	
	$query="
		select
			  *
		 from
			  project_payroll_header as h,
			  projects as p,
			  employee as e
		 where
			h.project_id = p.project_id and
			h.foreman_id = e.employeeID
		and
			project_payroll_header_id = '$project_payroll_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$foreman	    = $r['employee_fname'] . " " .$r['employee_lname'];
	$date			= $r['date'];
	$remarks		= $r['remarks'];
	
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work = $r['scope_of_work'];
	
	// $work_category = $options->attr_workcategory($work_category_id,'work');
	// $sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');

	
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

@media print and (width: 8.5in) and (height: 11in) {
  @page {
	 
  }
  
  .page-break{
		display:block;
		page-break-before:always;  
  }
}
	
body
{
	size: legal portrait;
	padding:0px;
	font-family:"Times New Roman";
	font-size:12px;
	letter-spacing:2px;
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
	border-collapse:collapse;
	border:1px solid #000;
}
.content table th{
	border:1px solid #000;
	padding:5px;
}
.content table td{
	border-left:1px solid #000;
	border-right:1px solid #000;
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

.inline{
	display:inline-block;	
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
	
   <?php require("form_heading_ieee.php"); ?>
   
    <div style="text-align:right; font-weight:bolder;">
        PROJECT PAYROLL # : <?=str_pad($return_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>       
    <div style="text-align:center; font-size:14px;">
    	PROJECT PAYROLL
    </div>
    <div class="header" style="">
        <table style="width:100%;">
            <tr>
                <td width="12%">From Project / Section:</td>
                <td width="40%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
               

                <td width="6%">Date:</td>
                <td width="17%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
        
            <tr>
            	<td>Remarks :</td>
                <td width="40%"  style="border-bottom:1px solid #000;" ><?=$remarks?></td>

                 <td width="6%">Foreman:</td>
                <td style="border-bottom:1px solid #000;"><?=$foreman?></td>
            </tr>
           
        </table>
    </div><!--End of header--><br />

        <?php
	
			$query="
				select
				*
				from
					project_payroll_detail
				where
					project_payroll_header_id = '$project_payroll_header_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());	

			$query_disc="
				select
				*
				from
					project_payroll_discount
				where
					project_payroll_header_id = '$project_payroll_header_id'
			";
			
			$result_disc=mysql_query($query_disc) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th width="60">Qty</th>
                    <th width="60">Unit</th>
                    <th>Labor Expense</th>
					<th>Unit Cost </th>
					<th>Amount </th>
                    
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$quantity 		= $r['quantity'];
					$labor_expense		= $r['labor_expense'];
					$price			= $r['price'];
					$amount	        = $r['total_price'];
					$unit 	        = $r['uom'];
					$tAmount += $amount;
										
				?>
                    <tr>
                        <td><div align="right"><?=$quantity?></div></td>
                        <td><?=$unit?></td>
                        <td><?=$labor_expense?></td>
						<td style="text-align:right;"><?=number_format(($price),2)?></td>
						<td style="text-align:right;"><?=number_format(($amount),2)?></td>
                    </tr>
					 
					
                <?php
				endwhile;
				?>
				  <tr>
                        <td><div align="right"></div></td>
                        <td></td>
                        <td>(10% Retention)</td>
						<td style="text-align:right;"></td>
						<td style="text-align:right;"><?='('.number_format(($tAmount*.1),2).')'?></td>
                    </tr>
					 

				<?php
			
				while($r_disc=mysql_fetch_assoc($result_disc)):
					$discount_name 		= $r_disc['discount_name'];
					$discount_amount		= $r_disc['discount_amount'];
					$discAmount += $discount_amount;
										
				?>
                    <tr>
                        <td><div align="right"></div></td>
                        <td></td>
                        <td><?='('.$discount_name.')'?></td>
						<td style="text-align:right;"></td>
						<td style="text-align:right;"><?='('.number_format(($discount_amount),2).')'?></td>
                    </tr>
					 
					
                <?php
				endwhile;
				?>

				<tr>
                        <td></td>
                        <td></td>
                        <td>********** Nothing Follows **********</td>
						<td></td>
						<td></td>
                    </tr>
					<tr>
                        <td></td>
                        <td></td>
                        <td></td>
						<td></td>
						<td></td>
                    </tr>
					<tr>
                        <td></td>
                        <td></td>
                        <td></td>
						<td></td>
						<td></td>
                    </tr>
				<?php

				$total = ($tAmount * .9) - $discAmount;
				?>
				<tr>
                        <td colspan='4' style="border:1px solid #000;text-align:right;"><b>Total Amount</b></td>
						<td style ="border:1px solid #000;text-align:right; font-weight:bolder;width:10%">P <?=number_format(($total),2)?></td>
                    </tr>
                
            </table>
           
      
            
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
<div class="divFooter">
    F-WHS-015<br>
    Rev. 0 03/12/16s
</div>
<div class="page-break"></div>
</body>
</html>