<?php

	include_once("../conf/ucs.conf.php");
	
	$petty_cash_id = $_REQUEST['pt_cash'];
	
		$sql = "
				select
					*
				from
					petty_cash p, projects s, employee e
				where
					p.petty_cash_id='$petty_cash_id' AND p.employeeID = e.employeeID AND p.department_id = s.project_id AND p.is_deleted != '1'
				";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	extract($row);
	$employee = ucwords(strtolower($employee_lname)) . ',&nbsp;' . ucwords(strtolower($employee_fname));
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
<div style="width:860px;">
<div style="text-align:left; margin-bottom:5px; font-size:16px;">
	<img id="head_logo" src="../images/chead.png" style="display:inline-block; margin-right:20px;" width="64" height="64" />
    	<p style="display:inline-block; margin:0px; vertical-align:top;">
        <strong>DBCCI - Dynamic Builders and Construction Co. (Phils.), Inc.</strong><br />
        446-1559, 446-3580<br />
        Bacolod-Mucia Road, Brgy. Alijis, Bacolod City   	</p>
     	<hr style="margin:5px 0px; border-top:1px solid #000; border-bottom:none;"  />
	<p style="font-size:18px;">PETTY CASH VOUCHER</p>
	<div style="margin-left:730px;">PC No.&nbsp; <b><?php echo str_pad($petty_cash_id, 7, '0', STR_PAD_LEFT); ?></b></div>
<table width=650>
	<tr>
		<td valign="bottom" width=300>
		Date : <u><?php echo $daterequested; ?></u>
	       </td>	       
	</tr>
</table>
<br>
<table width=650>
	<tr>		
		<td>Department:</td>
		<td><?php echo $project_name; ?></td>
	</tr>
	<tr>		
		<td>Amount:</td>
		<td><?php echo number_format($amount,2); ?></td>
	</tr>
	<tr>		
		<td>Purpose:</td>
		<td><p><?php echo $purpose; ?></p></td>
	</tr>
</table>
<br>
<table width=650>
	<tr>
		<td width=300>
		Requested By :<p>______________________<br/><b><?php echo $employee; ?></b></p>
	       </td>
		   <td width="80px">&nbsp;</td>
		   <td>
		Received By  :<p>______________________<br /><b><?php echo $employee; ?></b></p>
		   </td>
	</tr>
</table>
<br />
<table width=650>
	<tr>		
	       <td>
		Prepared By :<p>______________________<br /><b>Ronald Ogue</b></p>
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
<br>
<table width=650>
	<tr>	
		   <td>&nbsp;</td><td>&nbsp;</td>
		   <td><p><b>Note:</b> All petty cash request are required to liquidate after purchased of item the same day of cash being granted</p></td>
	</tr>
</table>
</div>


</body>

</html>