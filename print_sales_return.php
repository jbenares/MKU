<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_header_id=$_REQUEST[id];

	
	$query="
		select
			  *
		 from
			  sales_return_header as s,
			  projects as p
		 where
			s.project_id = p.project_id
		and
			sales_return_header_id = '$return_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$status			= $r['status'];
	$user_id		= $r['prepared_by'];
	$date			= $r['date'];
	$remarks		= $r['remarks'];
	
	$work_category_id 	= $r['work_category_id'];
	$sub_work_category_id = $r['sub_work_category_id'];
	$scope_of_work = $r['scope_of_work'];
	
	$work_category = $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');

	
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
        SALES RETURN # : <?=str_pad($return_header_id,7,0,STR_PAD_LEFT)?><br />
    </div>       
    <div style="text-align:center; font-size:14px;">
    	SALES RETURN
    </div>
    <div class="header" style="">
        <table style="width:100%;">
            <tr>
                <td width="12%">From Project / Section:</td>
                <td width="40%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
                
                <td width="6%">Date:</td>
                <td width="17%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <!--<tr>
              <td>Scope of Work:</td>
              <td style="border-bottom:1px solid #000;" colspan="3"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>              
            </tr>-->
            <tr>
            	<td>Remarks :</td>
                <td style="border-bottom:1px solid #000;"  colspan="3"><?=$remarks?></td>
            </tr>
           
        </table>
    </div><!--End of header--><br />

        <?php
	
			$query="
				select
					d.stock_id,
					p.stockcode,
					p.stock,
					p.unit,
					d.quantity,
					d.price,
					d.amount
				from
					sales_return_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					sales_return_header_id = '$return_header_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th width="60">Qty</th>
                    <th width="60">Unit</th>
                    <th>Item Description </th>
					<th>Unit Cost </th>
					<th>Amount </th>
                    
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$quantity 		= $r['quantity'];
					$stock_id		= $r['stock_id'];
					$stock 			= $r['stock'];
					$stockcode		= $r['stockcode'];
					$price			= $r['price'];
					$amount	        = $r['amount'];
					$unit 	        = $r['unit'];
					$tAmount += $amount;
										
				?>
                    <tr>
                        <td><div align="right"><?=$quantity?></div></td>
                        <td><?=$unit?></td>
                        <td><?=$stock?></td>
						<td style="text-align:right;"><?=number_format(($price),2)?></td>
						<td style="text-align:right;"><?=number_format(($amount),2)?></td>
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
				
				<tr>
                        <td colspan='4' style="border:1px solid #000;text-align:right;"><b>Total Amount</b></td>
						<td style ="border:1px solid #000;text-align:right; font-weight:bolder;width:10%">P <?=number_format(($tAmount),2)?></td>
                    </tr>
                
            </table>
           
            <div style="margin-top:30px;">
				<div class="inline" style="width:150px; margin:0px 10px;">
                	<span style="margin-bottom:30px; display:block;text-align:center;">Requested by: </span>
                    <span style="height:30px;">
	                	<!--<?=$options->getUserName($user_id);?>-->&nbsp;
                   	</span>
                	<p style="border-top:1px solid #000; padding-top:5px; text-align:center;" >
                    	Requisitioner 
                    </p>
                </div>
                
                <div class="inline" style="width:150px; margin:0px 10px; vertical-align:top;">
                	<span style="margin-bottom:30px; display:block;text-align:center;">Checked by: </span>
                    <span style="height:30px;">
	                	&nbsp;
                   	</span>
                	<p style="border-top:1px solid #000; padding-top:5px; text-align:center;" >
                    	Warehouseman
                    </p>
                </div>
                <div class="inline" style="width:150px; margin:0px 10px;">
                	<span style="margin-bottom:30px; display:block;text-align:center;">Approved by: </span>
                    <span style="height:30px;">
	                	&nbsp;
                   	</span>
                	<p style="border-top:1px solid #000; padding-top:5px; text-align:center;" >
                    	Dept. Head / P.I.C. 
                    </p>
                </div>
            </div>
            
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