<?php

	include_once("../conf/ucs.conf.php");
	
	$petty_cash_id = $_REQUEST['pt_cash'];
	
		$sql = "
				select
					*
				from
					petty_cash_rjr
				where
					petty_cash_id='$petty_cash_id' AND is_deleted != '1'
				";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	extract($row);	
	$daterequested = date("M d, Y",strtotime($date_requested));
	
	//print_r($rvh);

?>

<html>

<head>

<script>
	window.print();
</script>

<style>
	
td {
	font-size: 14px;
}

</style>

</head>

<body>
<div style="width:560px;">
<div style="text-align:left; margin-bottom:5px; font-size:16px;">
	<img id="head_logo" src="../images/chead.png" style="display:inline-block; margin-right:20px;" width="64" height="64" />
    	<p style="display:inline-block; margin:0px; vertical-align:top;">
        <strong>DBCCI - Dynamic Builders and Construction Co. (Phils.), Inc.</strong><br />
        416-1559, 446-3580<br />
        Bacolod-Mucia Road. Brgy. Alijis, Bacolod City   	</p>
     	<hr style="margin:5px 0px; border-top:1px solid #000; border-bottom:none;"  />
	<p style="font-size:18px;">PETTY CASH RJR</p>
	<div style="margin-left:400px;">PC No.&nbsp; <b><?php echo str_pad($petty_cash_id, 7, '0', STR_PAD_LEFT); ?></b></div>
<table width=100%>
	<tr>
		<td valign="bottom" width=300>
		Date : <u><?php echo $daterequested; ?></u>
	       </td>	       
	</tr>
</table>
<br>
<table width=100%>	
	<tr>		
		<td>Amount:</td>
		<td><?php echo number_format($amount,2); ?></td>
	</tr>
	<tr>		
		<td>Purpose:</td>
		<td><?php echo $purpose; ?></td>
	</tr>
</table>
<br>
<table width=100%>
	<tr>
		<td width=300>
		Requested By :<p>____________________________</p>
	       </td>
		  <td>
		Approved By :<p>____________________________</p>
	       </td>
	       <td>
		Received By :<p>____________________________</p>
	       </td>
	</tr>
</table>
<br>
<table width=100%>
	<tr>	
	       
		   <td>&nbsp;</td><td>&nbsp;</td>		   
	</tr>
</table>
</div>


</body>

</html>