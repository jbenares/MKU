<?php
	ob_start();
	session_start();

	include_once("../conf/ucs.conf.php");
	include_once("../library/lib.php");

	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$stock_id		= $_REQUEST[stock_id];
	
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
                        VEHICLE PASS REPORT<br />
                        <?=lib::ymd2mdy($from_date)?> to <?=lib::ymd2mdy($to_date)?> <br /><br />
                    </div>
                </td>
            </tr>
            <tr>
		<td><b>#</b></td>
		<td><b>Date</b></td>
          	<td><b>V.P. #</b></td>
          	<td><b>P.O. #</b></td>
          	<td><b>Driver</b></td>
          	<td><b>Vehicle</b></td>     
          	<td><b>Purpose</b></td>
            </tr>
        </thead>
        <tbody>
	<?php
		$sql = "select
						*
					from
						vehicle_pass
					where
						vh_void='0' and
						vh_date between '$from_date' and '$to_date'";
						
		if(!empty($stock_id)){
			$sql.="AND stock_id='$stock_id'";
		}
		
		$sql_ = mysql_query($sql);
		$i=1;
		while($r=mysql_fetch_array($sql_)) {
			echo '<tr><td>'.$i.'.</td>';
			echo '<td>'.$r[vh_date].'</td>';	
			echo '<td>'.str_pad($r[vh_number], 7, "0", STR_PAD_LEFT).'</td>';				
			echo '<td>'.$r[po_header_id].'</td>';

			$getDriver = mysql_query("select * from drivers where driverID='$r[driverID]'");
			$rD = mysql_fetch_array($getDriver);

			echo '<td>'.$rD[driver_name].'</td>';

			$getStock = mysql_query("select * from productmaster where stock_id='$r[stock_id]'");
			$rS = mysql_fetch_array($getStock);

			echo '<td>'.$rS[stock].'</td>';


			$getP = mysql_query("select * from vehicle_pass_purpose where vh_purpose_id='$r[vh_purpose_id]'");
			$rP = mysql_fetch_array($getP);

			echo '<td>'.$rP[vh_purpose_description].'</td></tr>';

			$i++;
		}
	?>
        </tbody>
    </table>
</body>
</html>
