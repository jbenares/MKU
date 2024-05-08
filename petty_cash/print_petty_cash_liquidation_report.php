<?php
	ob_start();
	session_start();

	include_once("../conf/ucs.conf.php");
	include_once("../library/lib.php");

	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PETTY CASH</title>
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

table{ border-collapse:collapse; width:100%; }
table td{ padding:3px; }
table thead td{ font-weight:bold;  border-top:1px solid #000; border-bottom:1px solid #000; }
table tfoot td{
	border-top:1px solid #000;
	font-weight:bold;	
}
@media print{
	table { page-break-inside:auto }
	tr{ page-break-inside:avoid; page-break-after:auto }
	thead { display:table-header-group }
	tfoot { display:table-footer-group }
	.pb { page-break-after:always }
}

</style>

</head>
<body>
	<table>
    	<thead>
        	<tr>
            	<td colspan="7">
                	<div>
                    	<?=$title?><br />
                        PETTY CASH LIQUIDATION REPORT<br />
                        <?=lib::ymd2mdy($from_date)?> to <?=lib::ymd2mdy($to_date)?> <br /><br />
                    </div>
                </td>
            </tr>
            <tr>
				<td><b>Petty Cash</b></td>
				<td><b>Date Requested</b></td>
				<td><b>Requested By</b></td>
				<td width="300"><b>Purpose</b></td>
				<td><b>Department</b></td>
				<td><b>Amount</b></td>          	
				<td><b>Liquidated Amount</b></td>
				<td><b>Returned Amount</b></td>
				<td><b>Date Liquidated</b></td>
            </tr>
        </thead>
        <tbody>
	<?php
		$sql = mysql_query("select
						*
					from
						petty_cash p, projects s, employee e
					where
							p.is_deleted = '0' 
						and						
							p.date_liquidated between '$from_date' and '$to_date'
						and
							p.is_liquidated = '0'
						and
							p.is_approve = '1'
						and
							p.employeeID = e.employeeID 
						and
							p.department_id = s.project_id
						and
							p.is_replenish = '1'
					order by
						p.petty_cash_id ASC");

		$i=1;
		$numrows = mysql_num_rows($sql);
		
		while($r=mysql_fetch_array($sql)) {
			$dt_da = date("M d, Y",strtotime($r['date_requested']));
			$dt_liq = date("M d, Y",strtotime($r['date_liquidated']));
			
			$total_amt += $r[amount];
			$total_la += $r[liquidated_amount];
			$total_ra += $r[returned_amount];
			
			echo '<tr><td>'.str_pad($r[petty_cash_id], 7, '0', STR_PAD_LEFT).'</td>';
			echo '<td>'.$dt_da.'</td>';	
			echo '<td>'.$r['employee_lname'] . ',&nbsp;' . $r['employee_fname'].'</td>';
			echo '<td>'.$r[purpose].'</td>';
			echo '<td>'.$r[project_name].'</td>';
			echo '<td>'.$r[amount].'</td>';
			echo '<td>'.$r[liquidated_amount].'</td>';
			echo '<td>'.$r[returned_amount].'</td>';
			echo '<td>'.$dt_liq.'</td></tr>';
			
			$i++;
		}
	?>			
        </tbody>
		<thead>
			<tr>
				<td colspan="5" align="right"><b>Total:</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td><b><?php echo number_format($total_amt, 2); ?></b></td>
				<td><b><?php echo number_format($total_la, 2); ?></b></td>
				<td colspan="2"><b><?php echo number_format($total_ra, 2); ?></b></td>
			</tr>
		</thead>
    </table>
	<br />
	<table width=650>
	<tr>		
	       <td>
		Prepared By :<p>______________________<br /><b>Jonah Roque</b></p>
	       </td>
		   <td width="80px">&nbsp;</td>
		  <td>
		Checked By :<p>______________________<br /><b>May Domingo</b></p>
	       </td>
		   <td width="80px">&nbsp;</td>
		   <td>
		Noted By :<p>______________________<br /><b>Silvestre Lareza</b></p>
	       </td>
		<td width="80px">&nbsp;</td>
		   <td>
		Approved By :<p>______________________<br /><b>J.E. Cruz / R. Yanson Jr.</b></p>
	       </td>
	</tr>
</table>
</body>
</html>
