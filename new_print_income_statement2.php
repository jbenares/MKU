<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');


$from 	      = $_REQUEST['from'];
$to   		  = $_REQUEST['to'];
$project_id   = $_REQUEST['project_id'];

$sqlp = mysql_query("Select * from projects as p where
p.project_id = '$project_id'") or die (mysql_error());

$rp = mysql_fetch_assoc($sqlp);

$options=new options();	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	size: legal portrait;		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	text-align:left;	
}
.header table td, .content table td
{
	padding:3px;
	
}

.content table{
	border-collapse:collapse;
}
.content table td,.content table th{
	/*border:1px solid #000;*/
	padding:3px;
}
.withborder td,.withborder th{
	border:1px solid #000;
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

.noborder{
	border:none;	
}

.alignRight{
	text-align:right;	
}
.total{
	border-top: 1px solid black;
	font-weight: bold;
	font-size: 12px;
}
.total2{
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	font-weight: bold;
}
.total3{
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	font-size: 14px;
	padding-top: 5px;
	padding-bottom: 5px;
	font-weight: bold;
}

.total4{
	font-weight: bold;
	font-size: 12px;	
}

.top_total{
	border-bottom: 1px solid black;
	font-weight: bold;
}
#numbers_format{
	text-align: right;
	padding-left: 20px;
}
#totalities{
	font-weight: bold; 
	font-size: 14px;
	padding-left: 20px;
}

#totalities2{
	font-size: 14px;
	padding-left: 40px;
}

#totalities_main{
	font-weight: bold; 
	font-size: 14px;
	padding-left: 0px;
}
a:link{
	color: black;
}
a:hover{
	color: red;
}

a:visited{
	color: black;
}
</style>
</head>
<body>

	<?php
		$tmpStartingDate=explode("-",$startingdate);
	?>
    
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
    
    	<?php
			require("form_heading.php");
        ?>

        <div style="text-align:center; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	MKU CONSTRUCTION AND SUPPLY<br />
           	Summary of Revenue and Expenses<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<!-- Revenue/Sales -->
<?php 
$rev = mysql_query("select
					sum(h.amount) as amount
					from
					cr_header as h
					where
					h.project_id = '$project_id'") or die (mysql_error());

$rsrev = mysql_fetch_assoc($rev);	

$tax = $rsrev['amount'] * (7/100);	

$net = $rsrev['amount'] - $tax; 
?>
<div style="text-align: left; font-weight: bold; font-size: 12px;">
Project ID : <?=$rp['project_code']?><br />
Project : <?=$rp['project_name']?><br />
Address : <?=$rp['address']?></div>
<br/>
<br/>
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">Contract Revenue</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"><?=number_format($rsrev['amount'],2)?></td>
	</tr>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">Less: Witholding Tax 7%</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"><?=number_format($tax,2)?></td>
	</tr>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">Net Collectibles</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right; border-top: 1px solid black;"><?=number_format($net,2)?></td>
	</tr>
	
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">Less: Operating Expenses</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
	<!-- Opertions -->
	<?php
	// $sqlm = mysql_query("select
	// 					sum(h.netamount) as amount,
	// 					c.category
	// 					from
	// 					rr_header as h,
	// 					rr_detail as d,
	// 					productmaster as p,
	// 					categories as c
	// 					where
	// 					h.rr_header_id = d.rr_header_id and
	// 					d.stock_id = p.stock_id and
	// 					p.categ_id1 = c.categ_id and
	// 					h.`status` != 'C' and
	// 					h.project_id = '$project_id' and
	// 					h.date between '$from' and '$to'
	// 					group by c.categ_id") or die (mysql_error());

	$querym = "select
						sum(h.netamount) as amount,
						c.category
						from
						rr_header as h,
						rr_detail as d,
						productmaster as p,
						categories as c
						where
						h.rr_header_id = d.rr_header_id and
						d.stock_id = p.stock_id and
						p.categ_id1 = c.categ_id and
						h.`status` != 'C'";

			if(!empty($project_id)){
				$querym .= " and h.project_id = '$project_id' ";
			}

			if(!empty($from) && !empty($to)){
				$querym .= " and h.date between '$from' and '$to' ";
			}

				$querym .= "group by c.categ_id";

		$sqlm = mysql_query($querym) or die (mysql_error());
						
	while($rm = mysql_fetch_assoc($sqlm)){	

	$total_mat += $rm['amount'];	
		
	?>
	<tr>
		<td width="300" id="totalities2"><?=$rm['category']?></td>
		<td width="170" style="text-align: right;"><?=number_format($rm['amount'],2)?></td>
		<td width="170" style="text-align: right;"></td>
		<td width="170" style="text-align: right;"></td>
	</tr>
	<?php } ?>
	<tr>
		<td width="300" id="totalities2" style=" font-weight: bold;">Total Materials</td>
		<td width="170" style="text-align: right; border-top: 1px solid black; font-weight: bold;"><?=number_format($total_mat,2)?></td>
		<td></td>
		<td></td>
	</tr>
	<!-- JV Based Posted Accounts-->
	<?php 
	$query_e = "select
						g.gchart,
						sum(d.debit) as amount
						from
						gltran_header as h,
						gltran_detail as d,
						gchart as g
						where
						h.gltran_header_id = d.gltran_header_id and
						d.gchart_id = g.gchart_id and
						h.`status` != 'C' and
						g.mclass = 'E'";

			if(!empty($from) && !empty($to)){
				$query_e .= " and h.date between '$from' and '$to'";
			}
			if(!empty($from)){
				$query_e .= " and d.project_id = '$project_id' ";
			}
						
				$query_e .= " group by
						g.gchart_id 
						order by
						g.acode asc";


				$sqle =mysql_query($query_e) or die (mysql_error());

	
	?>
	<tr>
		<td width="300" id="totalities2" style="font-weight: bold; padding-left: 40px;"><br />Labor and Expenses: </td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>	
	<?php while($re = mysql_fetch_assoc($sqle)){ 
	$total_labor_expense += $re['amount'];
	?>
	<tr>

		<td style='padding-left: 100px;'><?=$re['gchart']?></td>
		<td style="text-align: right;"><?=number_format($re['amount'],2)?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="4"><br /></td>
	</tr>
	<tr>
		<td width="300" id="totalities2" style=" font-weight: bold;">Total Labor and Expenses</td>
		<td width="170" style="text-align: right; border-top: 1px solid black; font-weight: bold;"><?=number_format($total_labor_expense,2)?></td>
		<td></td>
		<td></td>
	</tr>
	<?php 
	$query_pay = "select pd.project_payroll_header_id, pd.labor_expense, pd.total_price from project_payroll_header as ph, project_payroll_detail as pd 
					where
						ph.project_payroll_header_id = pd.project_payroll_header_id and
					    ph.status != 'C'";
			if(!empty($from) && !empty($to)){
				$query_pay .= " and ph.date between '$from' and '$to'";
			}
			if(!empty($project_id)){
				$query_pay .= " and ph.project_id = '$project_id' ";
			}

						
				$query_pay .= " group by pd.labor_expense 
						order by
						pd.labor_expense asc";



				$sqlpay =mysql_query($query_pay) or die (mysql_error());



			
	?>
	<tr>
		<td width="300" id="totalities2" style="font-weight: bold; padding-left: 40px;"><br />Project Payroll: </td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>	
	<?php while($rpay = mysql_fetch_assoc($sqlpay)){ 
			$query_disc = "select  * from project_payroll_discount where
						project_payroll_header_id = '$rpay[project_payroll_header_id]'";
		

			$sqldisc =mysql_query($query_disc) or die (mysql_error());

		$total_labor_expense_pay += $rpay['total_price'];
	?>
	<tr>

		<td style='padding-left: 100px;'><?=$rpay['labor_expense']?></td>
		<td style="text-align: right;"><?=number_format($rpay['total_price'],2)?></td>
	</tr>
	<?php } ?>
	<?php while($rdisc = mysql_fetch_assoc($sqldisc)){ 

		$total_labor_expense_pay -= $rdisc['discount_amount'];
		?>
	<tr>

		<td style='padding-left: 100px;'><?=$rdisc['discount_name']?></td>
		<td style="text-align: right;"><?= "(" . number_format($rdisc['discount_amount'],2) . ")"?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="4"><br /></td>
	</tr>
	<tr>
		<td width="300" id="totalities2" style=" font-weight: bold;">Total Project Payroll</td>
		<td width="170" style="text-align: right; border-top: 1px solid black; font-weight: bold;"><?=number_format($total_labor_expense_pay,2)?></td>
		<td></td>
		<td></td>
	</tr>
	

	
	<?php
	// $sqls = mysql_query("select
	// 					g.gchart,
	// 					sum(d.debit) as amount
	// 					from
	// 					gltran_header as h,
	// 					gltran_detail as d,
	// 					gchart as g
	// 					where
	// 					h.gltran_header_id = d.gltran_header_id and
	// 					d.gchart_id = g.gchart_id and
	// 					h.`status` != 'C' and
	// 					g.mclass = 'E' and
	// 					g.sub_mclass = '14' and
	// 					h.date between '$from' and '$to'
	// 					group by
	// 					g.gchart_id 
	// 					order by
	// 					g.acode asc
	// 					") or die (mysql_error());

	$query = "select
						g.gchart,
						sum(d.debit) as amount
						from
						gltran_header as h,
						gltran_detail as d,
						gchart as g
						where
						h.gltran_header_id = d.gltran_header_id and
						d.gchart_id = g.gchart_id and
						h.`status` != 'C' and
						g.mclass = 'E' and g.sub_mclass = '14'";

			if(!empty($from) && !empty($to)){
				$query .= " and h.date between '$from' and '$to'";
			}
			if(!empty($from)){
				$query .= " and d.project_id = '$project_id' ";
			}
						
				$query .= " group by
						g.gchart_id 
						order by
						g.acode asc";


				$sqls =mysql_query($query) or die (mysql_error());
						
	
	?>
	
	<tr>
		<td width="300" id="totalities2" style="font-weight: bold; padding-left: 40px;"><br />S.O.P</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>	
	<?php while($rse = mysql_fetch_assoc($sqls)){ 
	$total_sop += $rse['amount'];
	?>
	<tr>
		<td><?=$rse['gchart']?></td>
		<td><?=number_format($rse['amount'],2)?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="4"><br /></td>
	</tr>
	<tr>
		<td width="300" id="totalities2" style=" font-weight: bold;">Total S.O.P.</td>
		<td width="170" style="text-align: right; border-top: 1px solid black; font-weight: bold;"><?=number_format($total_sop,2)?></td>
		<td></td>
		<td></td>
	</tr>
	<?php
	
	$total_operating_expense = $total_sop + $total_labor_expense + $total_mat + $total_labor_expense_pay;
	
	?>
	<tr>
		<td width="300" id="totalities2" style=" font-weight: bold;">Total Operating Expenses</td>
		<td></td>		
		<td></td>
		<td width="170" style="text-align: right; border-bottom: 1px solid black; font-weight: bold;"><?=number_format($total_operating_expense,2)?></td>
	</tr>
	<?php
	$net_income = $net - $total_operating_expense;
	?>
	<tr>
		<td width="300" id="totalities2" style=" font-weight: bold;">Net Income(Loss)</td>
		<td></td>		
		<td></td>
		<td width="170" style="text-align: right; border-bottom: 3px double black; font-weight: bold;"><?=number_format($net_income,2)?></td>
	</tr>
</table>
</div>
</body>
</html>