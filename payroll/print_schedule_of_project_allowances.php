<?php
#echo "This module is being maintained. Please wait a few minutes.";
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];	
	
	
	function getNumberOfDays($employeeID, $from_date, $to_date){
		$result = mysql_query("
			select
				count(dtr_date) as days
			from
				dtr
			where
				closed = '1'
			and employeeID = '$employeeID'
			and dtr_date between '$from_date' and '$to_date'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['days'];
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
<link rel="stylesheet" type="text/css" href="../css/print.css"/>
<style type="text/css">
@media print {
   thead {display: table-header-group;}
}

body{ margin:0px;}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
}
td{
	vertical-align:top;
	padding:3px;
}	
.subtotal td{
	border-top:1px solid #000;
	font-weight:bold;	
}

.grandtotal td{
	border-top:3px double #000;
	font-weight:bold;	
}

tbody tr:first-child td{
	font-weight:bold;
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
tbody td:nth-child(n+5){
	text-align:right;
}

</style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td colspan="2" style="font-weight:bold;">
                    <?=$title?><br />
                    <?=$company_address?><br />
                    Schedule of Project Allowances<br />
                    <?php
					echo date("F j",strtotime($from_date))." - ".date("F j, Y",strtotime($to_date))
                    ?>     	
                </td>
          	</tr>
       	</thead>
        <tbody>
        	<tr>
            	<td style="width:3%;"></td>
            	<td>NAMES</td>
                <td>POSITION</td>
                <td>PROJECT ASSIGNMENT/DEPT.</td>
                <td>MONTHLY</td>
                <td>ALLOWANCE</td>
                <td># OF DAYS</td>
                <td>AMOUNT</td>
            </tr>
		<?php
		$t_amount = $t_no_of_days = $t_allowance = 0;
		$g_amount = $g_no_of_days = $g_allowance = 0;
        $result = mysql_query("
			select
				*
			from
				payroll_accumulator as p, employee as e, projects as proj
			where
				p.empID = e.employeeID
			and e.projectsID = proj.project_id
			and pa_from = '$from_date'
			and pa_to = '$to_date'
			order by proj.project_name asc
        ") or die(mysql_error());
		$project_id = "x";
		$i = 1;
        while($r = mysql_fetch_assoc($result)){
			if($project_id != $r['project_id']){
				if($project_id != "x"){
					echo "
						<tr class='subtotal'>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>".number_format($t_allowance,2)."</td>
							<td>".number_format($t_no_of_days,2)."</td>
							<td>".number_format($t_amount,2)."</td>
						</tr>
					";
				}
				echo "
					<tr>
						<td colspan='2' style='font-weight:bold;'><u>$r[project_name]</u></td>
					</tr>
				";
				
				$project_id = $r['project_id'];
				$i = 1;
				
				$g_amount 		+= $t_amount;
				$g_no_of_days	+= $t_no_of_days;
				$g_allowance	+= $t_allowance;
				$t_amount = $t_no_of_days = $t_allowance = 0;
				
			}
			
			$base_rate 			= $r['base_rate'];
			$employee_statusID 	= $r['employee_statusID'];
			$no_of_days			= $options->getAttribute('employee_status','employee_statusID',$employee_statusID,'no_of_days');
			
			if($employee_statusID == 1){ #monthly
				$monthly = $base_rate;
			}else{ #daily
				$monthly = $base_rate * 26;
			}
			$days = getNumberOfDays($r['employeeID'],$from_date,$to_date);
			
			$t_amount 		+= $r['basic_salary'];
			$t_no_of_days 	+= $days;
			$t_allowance	+= $r['allowance'];
			
            echo "
                <tr>
					<td>".($i++)."</td>
                    <td>$r[employee_lname], $r[employee_fname] $r[employee_mname]</td>
                    <td>$r[position]</td>
					<td>$r[project_name]</td>
					<td>".number_format($monthly,2)."</td>
					<td>".number_format($r['allowance'],2)."</td>
					<td>".number_format($days,2)."</td>
					<td>".number_format($r['basic_salary'],2)."</td>
                </tr>
            ";
        }
		echo "
			<tr class='subtotal'>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>".number_format($t_allowance,2)."</td>
				<td>".number_format($t_no_of_days,2)."</td>
				<td>".number_format($t_amount,2)."</td>
			</tr>
		";
		
		$g_amount 		+= $t_amount;
		$g_no_of_days	+= $t_no_of_days;
		$g_allowance	+= $t_allowance;
		$t_amount = $t_no_of_days = $t_allowance = 0;
				
		echo "
			<tr class='grandtotal'>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>".number_format($g_allowance,2)."</td>
				<td>".number_format($g_no_of_days,2)."</td>
				<td>".number_format($g_amount,2)."</td>
			</tr>
		";
        ?>
       	</tbody>
    </table>
</body>
</html>