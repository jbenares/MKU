<?php
	ob_start();
	session_start();
	
	include_once("../my_Classes/options.class.php");
	include_once("../conf/ucs.conf.php");

	$options 	= new options();
	$json		= $_REQUEST['json'];
	
	function numform($num) {
		if($num==0) $num = "0.00";
		else $num = number_format($num, 2);
		
		return $num;
	}
	
	function mdy($date){
		return date("m/d/Y",strtotime($date));
	}
	function getProjects(){
		$result = mysql_query("
			select * from projects order by project_name asc
		") or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r;
		}
		return $a;
	}
?>
<html>

<head>
<title>PAYROLL</title>
<link rel="stylesheet" type="text/css" media="screen" href="../css/stylemain.css" />
<style>

*{font-size:14px;}

body{
	size:landscape;
}


td {
	font-family: Arial;
	font-size: 14px;
	text-align:left;
	vertical-align:bottom;
}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
}

.payroll-table{
	border-collapse:collapse;	
	width:100%;
}
.payroll-table tr:first-child{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}

.payroll-table tr:first-child td{
	font-weight:bold;
}


.payroll-table tr td{
	padding:3px;
}

.payroll-table tr td:nth-child(n+4){
	text-align:right;	
}

.head-table td{
	font-weight:bold;
}

</style>

<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>

</head>

<body>
<?php
$j = json_decode($json,true);
#echo "<pre>";
#echo print_r($j);
#echo "</pre>";

echo "
	<table class='head-table'>
		<tr>
			<td>COMPANY:</td>
			<td>".$options->getAttribute('companies','companyID',$j['companyID'],'company_name')."</td>
		</tr>
		<tr>
			<td>PROJECT:</td>
			<td>".$options->getAttribute('projects','project_id',$j['project_id'],'project_name')."</td>
		</tr>
		<tr>
			<td>PAYROLL SEQUENCE:</td>
			<td>".$options->getAttribute('payroll_sequence','payroll_sequence_id',$j['payroll_sequence_id'],'description')."</td>
		</tr>
	</table>
";

echo "
	<table class='payroll-table' id='payroll_table'>
		<tr>
			<td>EMPLOYEE</td>
			<td>FROM DATE</td>
			<td>TO DATE</td>
			
			<td style='text-align:right;'>NO OF DAYS</td>
			<td style='text-align:right;'>SEMI MONTHLY</td>
			
			<td style='text-align:right;'>NO OF HRS</td>
			<td style='text-align:right;'>REGULAR 125%</td>
			
			<td style='text-align:right;'>NO OF HRS</td>
			<td style='text-align:right;'>SPECIAL 130%</td>
			
			<td style='text-align:right;'>NO OF HRS</td>
			<td style='text-align:right;'>LEGAL 200%</td>
			
			<td style='text-align:right;'>TOTAL OVERTIME</td>
			
			<td style='text-align:right;'>ABSENCES</td>
			
			<td style='text-align:right;'>ALLOWANCE</td>
			<td style='text-align:right;'>GROSS</td>
			
			<td style='text-align:right;'>SSS</td>
			<td style='text-align:right;'>PHILHEALTH</td>
			<td style='text-align:right;'>HDMF</td>
			<td style='text-align:right;'>TAXES</td>
			
			<td style='text-align:right;'>SSS LOAN</td>
			<td style='text-align:right;'>PAG-IBIG LOAN</td>
			<td style='text-align:right;'>RMY LENDING</td>
			<td style='text-align:right;'>CANTEEN</td>
			<td style='text-align:right;'>HOUSE RENTAL</td>
			<td style='text-align:right;'>PERSONAL CHARGABLES</td>
			
			<td style='text-align:right;'>TOTAL DEDUCTIONS</td>
			
			<td style='text-align:right;'>NET</td>
		</tr>
";
if(!empty($j)){
	$i = 0;
	
	$aFields = array(
		'basic_salary','regular_hrs_ot','regular_ot_amount','special_hrs_ot','special_ot_amount','legal_hrs_ot','legal_ot_amount','total_overtime',
		'no_of_absences','allowance','gross','sss','philhealth','hdmf','taxes','sss_loan','pagibig_loan','rmy_lending','canteen','house_rental',
		'personal_chargables','total_deductions','net_amount'
	);
	
	$aTotals = array();
	for($i = 0 ; $i < count($j['basic_salary']) ; $i++){
		$employee_name 		= $options->getAttribute('employee','employeeID',$j['employeeID'][$i],'employee_lname').", ".$options->getAttribute('employee','employeeID',$j['employeeID'][$i],'employee_fname');
		$gross	= $j['basic_salary'][$i] + $j['allowance'][$i];
		$t_deductions = $j['sss'][$i] + $j['philhealth'][$i] + $j['hdmf'][$i] + $j['taxes'][$i] +
						$j['rmy_lending'][$i] + $j['canteen'][$i] + $j['house_rental'][$i] + $j['personal_charges'][$i];
		
		$net = $gross - $t_deductions;
		
		foreach($aFields as $f){
			$aTotals[$f] += $j[$f][$i];
		}
		
		
		echo "
			<tr>
				<td>".$employee_name."</td>
				<td>".mdy($j['fdate'][$i])."</td>
				<td>".mdy($j['tdate'][$i])."</td>
				
				<td>".$j['no_of_days'][$i]."</td>
				<td>".numform($j['basic_salary'][$i])."</td>
				
				<td>".numform($j['regular_hrs_ot'][$i])."</td>
				<td>".numform($j['regular_ot_amount'][$i])."</td>
				
				<td>".numform($j['special_hrs_ot'][$i])."</td>
				<td>".numform($j['special_ot_amount'][$i])."</td>
				
				<td>".numform($j['legal_hrs_ot'][$i])."</td>
				<td>".numform($j['legal_ot_amount'][$i])."</td>
				
				<td>".numform($j['total_overtime'][$i])."</td>
				<td>".numform($j['no_of_absences'][$i])."</td>
				
				<td>".numform($j['allowance'][$i])."</td>												
				<td>".numform($j['gross'][$i])."</td>											
				
				<td>".numform($j['sss'][$i])."</td>
				<td>".numform($j['philhealth'][$i])."</td>
				<td>".numform($j['hdmf'][$i])."</td>
				<td>".numform($j['taxes'][$i])."</td>
				
				<td>".numform($j['sss_loan'][$i])."</td>
				<td>".numform($j['pagibig_loan'][$i])."</td>
				<td>".numform($j['rmy_lending'][$i])."</td>
				<td>".numform($j['canteen'][$i])."</td>
				<td>".numform($j['house_rental'][$i])."</td>
				<td>".numform($j['personal_chargables'][$i])."</td>
				
				<td>".numform($j['total_deductions'][$i])."</td>
				<td>".numform($j['net_amount'][$i])."</td>
			</tr>
		";	
	}
	
	echo "
			<tr>
				<td style='border-top:1px solid #000;'></td>
				<td style='border-top:1px solid #000;'></td>
				<td style='border-top:1px solid #000;'></td>
				<td style='border-top:1px solid #000;'></td>
	";
	
	foreach($aFields as $field){
		
		echo "<td style='border-top:1px solid #000;'>".numform($aTotals[$field])."</td>";
	}
}
?>
</body>
</html>
<script>
onload = function(){
	printPage();
};
</script>