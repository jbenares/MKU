<?php

	include_once("../conf/ucs.conf.php");
	
	$vh_number = $_REQUEST['vh_number'];

	$get_vh = mysql_query("select * from vehicle_pass where vh_number='$vh_number'");
	$rvh = mysql_fetch_array($get_vh);

	//print_r($rvh);

?>

<html>

<head>

<script>
	window.print();
</script>

<style>
	
td {
	font-size: 12px;
}

</style>

</head>

<body>
<div style="width:860px;">
<div style="text-align:left; margin-bottom:5px; font-size:16px;">
	<img id="head_logo" src="../images/chead.png" style="display:inline-block; margin-right:20px;" width="64" height="64" />
    	<p style="display:inline-block; margin:0px; vertical-align:top;">
        <strong>DBCCI - Dynamic Builders and Construction Co. (Phils.), Inc.</strong><br />
        416-1559, 446-3580<br />
        Bacolod-Mucia Road. Brgy. Alijis, Bacolod City   	</p>
     	<hr style="margin:5px 0px; border-top:1px solid #000; border-bottom:none;"  />
	<span style="font-size:15px;">VEHICLE PASS</span>
<table width=100%>
	<tr>
		<td valign="bottom" width=300>
		Date : <u><?=date("F d, Y", strtotime($rvh[vh_date]));?></u><br>
        P.O.#:<u><?=$rvh[po_header_id];?></u>
	       </td>
	       <td>
		VP No. : <u><?=str_pad($vh_number, 7, "0", STR_PAD_LEFT);?></u><br>
		Time Out : <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
	       </td>
	</tr>
</table>
<br>
<table width=100%>
	<tr>
		<?php
			$getDriver = mysql_query("select * from drivers where driverID='$rvh[driverID]'");
			$rD = mysql_fetch_array($getDriver);	
		?>

		<td>Please allow the operator, Mr. <u><?=$rD[driver_name];?></u> to pass the gate with the vehicle :</td>
	</tr>
</table>
<table width=560>
	<tr>
		<?php
			 $getStock = mysql_query("select * from productmaster where stock_id='$rvh[stock_id]'");
			 $rS = mysql_fetch_array($getStock);

		?>

		<td valign=top width=135>Type And Plate No. : </td>
               <td style="border-bottom: 1px solid #000000;"><?=$rS[stock];?></td>
	</tr>
	<tr>
		<?php
			  $getP = mysql_query("select * from vehicle_pass_purpose where vh_purpose_id='$rvh[vh_purpose_id]'");
			  $rP = mysql_fetch_array($getP);

		?>

		<td valign=top>Purpose : </td>
               <td style="border-bottom: 1px solid #000000;"><?=$rP[vh_purpose_description];?></td>
	</tr>
</table>
<table width=100%>
	<tr>
		<td width=300>
		Requesting Party :<p>____________________________</p>
	       </td>
	       <td>
		Checked By :<p>____________________________</p>
	       </td>
	</tr>
</table>
<br>
<table width=560>
	<tr>
		<td width=135 valign=top>Remarks : </td><td style="border-bottom: 1px solid #000000;"><?=$rvh[vh_remarks];?></td>
	</tr>
</table>
<br>
<table width=100%>
	<tr>
		<td width=300>
		Verified By :<p>____________________________<br>Maintenance Officer / Chief Mechanic</p>
	       </td>
	       <td>
		Approved By :<p>____________________________<br>GM / OM / Admin</p>
	       </td>
	</tr>
</table>
</div>

<br>
<span style="font-size:11px;">
F-EMN-007<br>
Rev. 0 10/07/13
</span>
</body>

</html>