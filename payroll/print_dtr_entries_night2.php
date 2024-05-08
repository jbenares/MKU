<?php
	ob_start();
	session_start();

	include_once("../my_Classes/options.class.php");
	include_once("../library/lib.php");

	ini_set('max_execution_time', 1000);


function getOB($day, $e_id){
	$arr = lib::getArrayDetails("select * from official_logbook where employee_id = '$e_id' and date = '$date' and status != 'C'");
	$aReturn = array();
	if( count($arr) > 0 ){
		foreach ($arr as $r) {
			$aReturn[] = "OB#$r[official_logbook_id]";
		}
	}

	return implode(",", $aReturn);
}
?>
<html>

<head>

<link rel="stylesheet" type="text/css" media="screen" href="css/stylemain.css" />

<style>
@media print{
	table{page-break-after:auto;}
}
td {
	font-family: Arial;
	font-size: 12px;
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
table { page-break-inside:auto }
	tr{ page-break-inside:avoid; page-break-after:auto }
	thead { display:table-header-group }
	tfoot { display:table-footer-group }
	.page-break { page-break-after:always }

.header_main{
	font-family: Arial;
	font-size: 13px;
	margin: 0 auto;
}

.table_header{
	font-weight: bold;
}

</style>

<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>

</head>

<body>
<?php
	include_once("../conf/ucs.conf.php");

	function addZeros($num) {
		return str_pad($num, 7, "0", STR_PAD_LEFT);
	}

	$b 					= $_REQUEST['b'];
	$fdate 				= $_REQUEST['fdate'];
	$tdate 				= $_REQUEST['tdate'];
	$projects			= $_REQUEST['projects'];
	$companyID			= $_REQUEST['companyID'];
	$employeeID			= $_REQUEST['employeeID'];
	$ltr				= $_REQUEST['ltr'];
	$counta				= $_REQUEST['counta'];
	$countb				= $_REQUEST['countb'];
	$employee_type_id   = $_REQUEST['employee_type_id'];

	//echo $ltr;

	$options = new options();
	
	function getEmpId($fdate,$tdate,$projects,$employee_type_id,$ltr){
		$sql = "select e.employeeID
						from
						employee as e,
						biometric_entries as be
						where 
						e.employeeID = be.empID and
						be.b_date between '$fdate' and '$tdate' and
						e.projectsID = '$projects' and
						e.employee_type_id = '$employee_type_id'";
						
		if(!empty($ltr)){
		$sql .= " and e.employee_lname like '%$ltr%'";	
		}				
						
		$sql .= " group by e.employeeID";
		
		$result = mysql_query($sql) or die (mysql_error());
							
		while($r = mysql_fetch_assoc($result)){
			$emp_id[] = $r['employeeID'];
		}
		return $emp_id;
	}
	

	
	function getTimeIn($e_id,$day){
		$sql = mysql_query("select 
				min(be.b_time) as time_in
				from
				biometric_entries as be
				where
				be.empID = '$e_id' and
				be.b_date = '$day' and
				be.b_time between '12:00:01' and '24:00:00'") or die (mysql_error());
				
		$r = mysql_fetch_assoc($sql);

		return $r['time_in'];
	}
	
	function getTimeOut($e_id,$day){
		
	$otd = date('Y-m-d', strtotime($day. ' + 1 days'));		
		
		$sql = mysql_query("select 
				min(be.b_time) as time_out
				from
				biometric_entries as be
				where
				be.empID = '$e_id' and
				be.b_date = '$otd' and
				be.b_time between '00:00:00' and '12:00:00'") or die (mysql_error());
				
		$r = mysql_fetch_assoc($sql);

		return $r['time_out'];
	}	
			echo '<table width="100%" border="0" cellspacing="0" cellpadding="3" border="1">
				<tr>
					<td colspan=13 style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#000000;">
					<p><b>DTR Entries 2: Date Range : <u>'.date("F d, Y", strtotime($fdate)).' to '.date("F d, Y", strtotime($tdate)).'</u></b><br>
					</td>
				</tr>
			</table>';
	$emp_id = getEmpId($fdate,$tdate,$projects,$employee_type_id,$ltr);
	foreach($emp_id as $e_id){
	$total_overtime = 0;
	$total_hrs = 0;
	?>	
	<table border="0" cellpadding="3" width="100%" cellspacing="0" class="page-break">
	<tr>
		<td colspan="10" style="border-bottom: 1px solid black;"></td>
	</tr>
	<tr>
		<td class="table_header">#</td>
		<td class="table_header">EMP.#</td>
		<td class="table_header">EMP NAME</td>
		<td class="table_header">CONTRACT #</td>
		<td class="table_header">DATE</td>
		<td class="table_header">IN</td>
		<td class="table_header">OUT</td>
		<td class="table_header">HRS WORKED</td>
		<td class="table_header">OVERTIME(HRS)</td>
		<td class="table_header">OB.#</td>
	</tr>
	<tr>
		<td colspan="10" style="border-top: 1px solid black;"></td>
	</tr>
	<?php
		$i = 1;
		$sql = "select 
				e.employeeID, e.employee_lname, e.employee_fname, e.employee_mname, ec.contract_num, be.b_date
				from
				employee as e,
				biometric_entries as be,
				employee_contracts as ec
				where 
				e.employeeID = be.empID and
				e.employeeID = ec.employeeID and
				be.b_date between '$fdate' and '$tdate'  and
				e.employeeID = '$e_id'
				group by be.b_date";
				
		$result = mysql_query($sql) or die (mysql_error());		
		
		$emp_time_in = '22:06:00';		
		$emp_time_out = '08:00:00';		
		while($r = mysql_fetch_assoc($result)){
			$hrs_worked = 0;
			$overtime = 0;
			
			$day = $r['b_date'];
			$empID = $r['employeeID'];
			$contract = $r['contract_num'];
			$empName = $r['employee_lname'].', '.$r['employee_fname'].' '.$r['employee_mname'];
			
			$time_in = getTimeIn($e_id,$day);
			$time_out = getTimeOut($e_id,$day);
			$OB = getOB($dtrb_date[$key],$e_id);
			
			//$parsed2 = date_parse($time_in);
			$parsed = date_parse($time_out);
			if($parsed['hour'] >= 9){
				$overtime = $parsed['hour'] - 8;
			}
			
			if($overtime == 0){
				$overtime = "";
			}
			
			$total_overtime += $overtime;
		
				
			if((!empty($time_in) && $time_in <= $emp_time_in) && (!empty($time_out) && $time_out >= $emp_time_out)){
				$hrs_worked = 8;
				
				$total_hrs += $hrs_worked;
			}else{
				
				$late = 0;
				if(!empty($time_in) && !empty($time_out)){
					
					if($time_in >= '22:06:01' && $time_in <= '22:16:00'){
						$late = .5;
										
					}else if($time_in >= '22:16:01' && $time_in <= '22:30:00'){
						$late = 1;
						
					}else if($time_in > '22:30:00'){
						$late = 4;
					}
					
					if($time_out < '07:30:00'){
						$late2 = 4;
						
					}else if($time_out >= '07:30:00' && $time_out <= '07:44:00'){
						$late2 = 1;
					}else if($time_out < '07:54:00' && $time_out >= '07:44:01'){
						$late2 = .5;
					}	
									
					$hrs_worked = 8 - ($late + $late2);	
					
				}else{
					$hrs_worked = "";
				}
				
					
				$total_hrs += $hrs_worked;	
			}
		?>
		<tr>
			<td><?=$i++;?></td>
			<td><?=str_pad($empID,7,0,STR_PAD_LEFT)?></td>
			<td><?=$empName?></td>
			<td><?=$contract?></td>
			<td><?=$day?></td>			
			<td><?=$time_in?></td>
			<td><?=$time_out?></td>
			<td style="text-align: center;"><?=$hrs_worked?></td>
			<td><?=$overtime?></td>
			<td><?=$OB?></td>
		</tr>
		<?php
		}
		?>
	<tr>
		<td colspan="7" style="border-top: 1px solid black;"></td>
		<td style="border-top: 1px solid black; text-align: center;"><?=$total_hrs?></td>
		<td style="border-top: 1px solid black; text-align: left;"><?=$total_overtime?></td>
		<td style="border-top: 1px solid black;"></td>
	</tr>
	</table>
	<br />
	<?php
	}
	?>
</body>
</html>
