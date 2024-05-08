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
<title>ASSET CIRCULATION</title>
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
                        ASSET CIRCULATION REPORT<br />
                        <?=lib::ymd2mdy($from_date)?> to <?=lib::ymd2mdy($to_date)?> <br /><br />
                    </div>
                </td>
            </tr>
            <tr>
				<td><b>#</b></td>
				<td><b>Date</b></td>
				<td><b>Status</b></td>
				<td><b>From</b></td>
				<td><b>To</b></td>
				<td><b>Item</b></td>
				<td><b>Qty</b></td>          	
				<td><b>Qty Balance</b></td>				
            </tr>
        </thead>
        <tbody>
	<?php
		$sql = mysql_query("select
						*
					from
						asset_circulation_header ch, productmaster p, asset_circulation_detail cd
					where
						ch.ach_id = cd.ach_id AND p.stock_id = cd.stock_id AND
						((cd.date_received between '$from_date' and '$to_date') OR (cd.date_returned between '$from_date' and '$to_date'))
					order by
						cd.acd_id DESC");

		$i=1;
		while($r=mysql_fetch_array($sql)) {
			$tamount += $r[amount];
			$f_prj = $r['from_project_id'];
			$t_prj = $r['to_project_id'];
			$st_id = $r['stock_id'];
			$st_name = $r['stock'];
				# FROM
				$fprj = "SELECT * FROM projects WHERE project_id = '$f_prj'";
				$rs_fprj = mysql_query($fprj);
				$rw_fprj = mysql_fetch_assoc($rs_fprj);
				$from_prj = $rw_fprj['project_name'];
				# TO
				$tprj = "SELECT * FROM projects WHERE project_id = '$t_prj'";
				$rs_tprj = mysql_query($tprj);
				$rw_tprj = mysql_fetch_assoc($rs_tprj);
				$to_prj = $rw_tprj['project_name'];
			
			if($r['status'] == 'I')
			{
				$date_t = date("M d, Y",strtotime($r['date_received']));
				$status = '<font color=black>IN</font>';
				$pf = $from_prj;
				$pt = $to_prj;
				
			}else{
				$date_t = date("M d, Y",strtotime($r['date_returned']));
				$status = '<font color=black>OUT</font>';
				$pf = $to_prj;
				$pt = $from_prj;
			}
							
				# Get qty of inventory adjustment
				$qadv = "SELECT *, SUM(quantity) as invqty FROM invadjust_header h, invadjust_detail d
							WHERE h.project_id = '$f_prj' AND h.invadjust_header_id = d.invadjust_header_id AND d.stock_id = '$st_id'";
				$rs_qadv = mysql_query($qadv);
				$num_qadv = mysql_num_rows($rs_qadv);
				$rw_qadv = mysql_fetch_assoc($rs_qadv);
				$total_qadv = $rw_qadv['invqty'];
				# Get qty of MRR
				$qmrr = "SELECT *, SUM(quantity) as mrrqty FROM rr_header h, rr_detail d
							WHERE h.project_id = '$f_prj' AND h.rr_header_id = d.rr_header_id AND d.stock_id = '$st_id'";
				$rs_qmrr = mysql_query($qmrr);
				$num_qmrr = mysql_num_rows($rs_qmrr);
				$rw_qmrr = mysql_fetch_assoc($rs_qmrr);
				$total_qmrr = $rw_qmrr['mrrqty'];
				
				$qty_bal = ($total_qadv + $total_qmrr) - $r['quantity'] . ' as of ' . $date_t;
			
						
			echo '<tr>';
			echo '<td>'.$i.'.</td>';
			echo '<td>'.$date_t.'</td>';	
            echo '<td>'.$status.'</td>';
			echo '<td>'.$pf.'</td>';
			echo '<td>'.$pt.'</td>';
			echo '<td>'.$st_name.'</td>';
			echo '<td>'.$r[quantity].'</td>';
			echo '<td>'.$qty_bal.'</td>';
			echo '</tr>';
		
			$i++;
		}
			
	?>
			<!--<tr style="border-top:1px solid #000">
			<td style="text-align:left;font-size:16px;font-weight:bold">TOTAL</td>
			<td>&nbsp </td>
			<td>&nbsp </td>
			<td>&nbsp </td>
			<td>&nbsp </td>
			<td style="font-size:12px;font-weight:bolder"></?= number_format($tamount,2);?></td>
			<td></td>
			</tr>!-->
        </tbody>
    </table>
</body>
</html>
