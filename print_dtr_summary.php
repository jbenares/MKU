<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$dtr_header_id= $_REQUEST['dtr_header_id'];
	$options=new options();	
	
	function getEmpDays($employeeID,$from_date,$to_date){
		$days = 0;
		$q = mysql_query("select sum(`day`) as days
							from dtr where
							employeeID = '$employeeID' and
							dtr_date between '$from_date' and '$to_date'") or die(mysql_error());
							
		$r = mysql_fetch_assoc($q);

		$days = $r['days'];
		
		return $days;
		
	}
	
	function getOvertime($employeeID,$from_date,$to_date){
		
		$hr = 0;
		$q = mysql_query("select sum(overtime_hr) as hr
							from dtr where
							employeeID = '$employeeID' and
							period_from = '$from_date' and period_to = '$to_date'") or die(mysql_error());
							
		$r = mysql_fetch_assoc($q);

		$hr = $r['hr'];
		
		return $hr;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">


@media print and (width: 14in) and (height: 8.5in) {
  @page {
	  margin: 1in;
  }
}
	
body
{
	size: legal landscape;
		
	padding:0px;
	/*margin:0px;*/
	font-family:Arial, Helvetica, sans-serif;
	font-size:10pt;
}
.container{
	
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
	font-size: 9px;
}
.content table td,.content table th{
	/*border:1px solid #000;*/
	padding:10px;
	font-size: 9px;
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

</style>
</head>
<body>
<div class="container">
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
	<?php
	$q = mysql_query("Select * from dtr_header where dtr_header_id = '$dtr_header_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($q);
	?>
     	<div align="center" style="font-size:12pt; font-weight:bolder; margin-bottom:10px; font-size: 12px;">
        	M.K.U. CONSTRUCTION AND SUPPLY<br />
        	PAYROLL SUMMARY<br />
			For the Week of <?=date("m/d/Y",strtotime($r['from_date']))?> to <?=date("m/d/Y",strtotime($r['to_date']))?>
       	</div>
        
        <div class="content" >
        	<table cellspacing="0" class="withborder">
                <tr>
                	<th>EMPLOYEE</th>
                    <th>OCCUPATION</th>
                    <th>DAILY RATE</th>
                    <th>HOURLY RATE</th>
                    <th># of DAYS</th>             
                    <th>OT # OF HOURS</th>
                    <th>GROSS PAY</th>
                    <th>SSS</th>
                    <th>PHIC</th>
                    <th>HDMF</th>
                    <th>C/A</th>
                    <th>NET PAY</th>
                    <th>SIGNATURE</th>
                </tr>
            <?php
			$query = mysql_query(" select
				d.dtr_detail_id,
				e.employeeID,
				e.employee_lname,
				e.employee_fname,
				e.employee_mname,
				et.employee_type,
				e.base_rate,
				e.daily_rate,
				d.day,
				d.sss,
				d.overtime,
				d.phic,
				d.hdmf,
				d.ca,
				d.net
				from
				dtr_detail as d,
				employee as e,
				employee_type as et
				where
				d.employeeID = e.employeeID and
				e.employee_type_id = et.employee_type_id and
				d.dtr_header_id = '$dtr_header_id'
				order by et.employee_type asc") or die (mysql_error());
				
			while($res = mysql_fetch_assoc($query)){
				
				$hourly = $res['daily_rate']/8;
				//$days = getEmpDays($res['employeeID'],$r['from_date'],$r['to_date']);
				$ot_pay = $res['overtime'] * $hourly;
				$gross = $res['day'] * $res['daily_rate'] + ($ot_pay);
				$net = $gross -($res['sss'] + $res['phic'] + $res['hdmf'] + $res['ca']);
				
				//$fullname = html_entity_decode($res['employee_lname']),', ',$res['employee_fname'],' ',$res['employee_mname']);
				$lname = utf8_decode(htmlentities($res['employee_lname']));
				$fname = utf8_decode(htmlentities($res['employee_fname']));
				$mname = utf8_decode(htmlentities($res['employee_mname']));
				
				$fullname = $lname.', '.$fname.' '.$mname;
				
				$total_gross += $gross;
				$total_sss += $res['sss'];
				$total_phic += $res['phic'];
				$total_hdmf += $res['hdmf'];
				$total_ca += $res['ca'];
				$total_net += $net;
			?>	

				<tr>
					<td><?=$fullname?></td>
					<td><?=$res['employee_type']?></td>
					<td><?=$res['daily_rate']?></td>
					<td><?=$hourly?></td>
					<td><?=$res['day']?></td>
					<td><?=$res['overtime']?></td>
					<td><?=number_format($gross,2)?></td>
					<td><?=$res['sss']?></td>
					<td><?=$res['phic']?></td>
					<td><?=$res['hdmf']?></td>
					<td><?=$res['ca']?></td>
					<td><?=number_format($net,2)?></td>
					<td></td>
				</tr>
				
				
			<?php	
			}
			?>			  
				<tr>
					<td colspan="6"></td>
					<td style="font-weight: bold;"><?=number_format($total_gross,2)?></td>
					<td style="font-weight: bold;"><?=number_format($total_sss,2)?></td>
					<td style="font-weight: bold;"><?=number_format($total_phic,2)?></td>
					<td style="font-weight: bold;"><?=number_format($total_hdmf,2)?></td>
					<td style="font-weight: bold;"><?=number_format($total_ca,2)?></td>
					<td style="font-weight: bold;"><?=number_format($total_net,2)?></td>
					<td></td>
				</tr>
            </table>
			
			<br />
			<br />
			<table width="100%" border="0">
			<tr>
				<td style="padding: 10px; text-align: center;">Prepared By:</td>
				<td style="padding: 10px; text-align: center;"><br />Liza C. Estoya <br />_________________________________</td>
				<td style="padding: 10px; text-align: center;">Checked By:</td>
				<td style="padding: 10px; text-align: center;"><br />Camille S. Ku <br />_________________________________</td>
				<td style="padding: 10px; text-align: center;">Approved By:</td>
				<td style="padding: 10px; text-align: center;"><br />Michael John S. Ku<br />_________________________________</td>
			</tr>
			<tr>
				<td style="padding: 10px; text-align: center;">Date:</td>
				<td style="padding: 10px; text-align: center;"><?=$r['payroll_date']?><br />_________________________________</td>
				<td style="padding: 10px; text-align: center;">Date:</td>
				<td style="padding: 10px; text-align: center;"><?=$r['payroll_date']?><br />_________________________________</td>
				<td style="padding: 10px; text-align: center;">Date:</td>
				<td style="padding: 10px; text-align: center;"><?=$r['payroll_date']?><br />_________________________________</td>
			</tr>
			</table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>