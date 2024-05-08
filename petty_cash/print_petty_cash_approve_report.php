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
                        PETTY CASH REPLENISHMENT REPORT<br />
                        <?=lib::ymd2mdy($from_date)?> to <?=lib::ymd2mdy($to_date)?> <br /><br />
                    </div>
                </td>
            </tr>
            <tr>
				<td><b>#</b></td>
				<td><b>Date</b></td>
				<td><b>Amount</b></td>          	
            </tr>
        </thead>
        <tbody>
	<?php
		$sql = mysql_query("select
						*
					from
						petty_cash_budget
					where
						is_deleted='0' and
						date_added between '$from_date' and '$to_date'
					order by
						pc_budget_id DESC");

		$i=1;
		while($r=mysql_fetch_array($sql)) {
			$dt_da = date("M d, Y",strtotime($r['date_added']));
			echo '<tr><td>'.$i.'.</td>';
			echo '<td>'.$dt_da.'</td>';						
			echo '<td>'.$r[amount].'</td></tr>';
			
			$i++;
		}
	?>
        </tbody>
    </table>
</body>
</html>
