<?php
	ob_start();
	session_start();

	include_once("../my_Classes/options.class.php");
	include_once("../library/lib.php");

	ini_set('max_execution_time', 1000);


function getOB($date, $employeeID){
	$arr = lib::getArrayDetails("select * from official_logbook where employee_id = '$employeeID' and date = '$date' and status != 'C'");
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
	$employee_type_id               = $_REQUEST['employee_type_id'];

	//echo $ltr;

	$options = new options();

	if(!empty($fdate) && !empty($tdate)) {
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td colspan=13 style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#000000;">
					<p><b>DTR Entries : Date Range : <u>'.date("F d, Y", strtotime($fdate)).' to '.date("F d, Y", strtotime($tdate)).'</u></b><br>
					</td>
				</tr>
			</table>';

		$sql = mysql_query("select
						*
					from
						biometric_entries as be,
						employee as e,
						employee_type as et,
						employee_contracts as c
					where
						be.b_date between '$fdate' and '$tdate' and
						be.empID=e.employeeID and
						e.employeeID = c.employeeID and
						e.employee_lname like '$ltr%' and
					    c.projectsID='$projects' and
						e.employee_type_id='$employee_type_id' and
						processed='1'
					order by
						e.employee_lname asc, e.employee_fname asc,
						be.b_date asc
					limit
						$counta, $countb
					");

		echo '<table width="100%" border="0" cellspacing="0" cellpadding="3">';

			echo '<tr>
					<td class=line_bottom><b>#</b></td>
					<td class=line_bottom><b>EMP. #</b></td>
					<td class=line_bottom align="center"><b>Employee Name</b></td>
					<td class=line_bottom><b>Contract #</b></td>
					<td class=line_bottom><b>Date</b></td>
					<td class=line_bottom><b>In</b></td>
				    <td class=line_bottom><b>Out</b></td>
				    <td class=line_bottom><b>In</b></td>
				    <td class=line_bottom><b>Out</b></td>
				    <td class=line_bottom><b>Hrs Worked</b></td>
					<td class=line_bottom><b>OB#</b></td>
				</tr>';


	       $main_index = 0;
		while($r=mysql_fetch_array($sql)) {

			$emp = $r[employee_lname].', '.$r[employee_fname];

			$dtremp[$main_index] = $emp;
			$dtrempID[$main_index] = $r[employeeID];
			$dtrb_date[$main_index] = $r[b_date];

			if($dtrb_date[$main_index-1]!=$r[b_date] || $dtrempID[$main_index-1]!=$r[employeeID]) {

				$sql_rw = mysql_query("select
								min(b_time) as inam
							from
								biometric_entries
							where
								empID='$r[employeeID]' and
								b_date='$r[b_date]' and
								b_time between '03:00' and '12:00'");

				$rw = mysql_fetch_array($sql_rw);
				$dtrin1[$main_index] = $rw[inam];

				$sql_rw = mysql_query("select
								min(b_time) as outam
							from
								biometric_entries
							where
								empID='$r[employeeID]' and
								b_date='$r[b_date]' and
								b_time between '12:00' and '13:00'");

				$rw = mysql_fetch_array($sql_rw);
				$dtrout1[$main_index] = $rw[outam];

				$sql_rw = mysql_query("select
								max(b_time) as inpm
							from
								biometric_entries
							where
								empID='$r[employeeID]' and
								b_date='$r[b_date]' and
								b_time between '12:00' and '15:00'");

				$rw = mysql_fetch_array($sql_rw);
				$dtrin2[$main_index] = $rw[inpm];

				$sql_rw = mysql_query("select
								max(b_time) as outpm
							from
								biometric_entries
							where
								empID='$r[employeeID]' and
								b_date='$r[b_date]' and
								b_time between '15:00' and '24:00'");

				$rw = mysql_fetch_array($sql_rw);
				$dtrout2[$main_index] = $rw[outpm];

				$main_index++;
			}

			$i++;
		}

		//print_r($dtr);
		$keyin++;
		$totalhrs = 0;

                $x=0;
		//var_dump($dtrempID);
		foreach ($dtrempID as $key => $demp) {
			$sql2=mysql_query("SELECT contract_num FROM employee_contracts WHERE employeeID = '$demp' ORDER by effectivity_date desc");
			$r=mysql_fetch_assoc($sql2);
			$contract_num = $r[contract_num];

			$outpm = strtotime($dtrout2[$key]);
			$inpm = strtotime($dtrin2[$key]);
			$outam = strtotime($dtrout1[$key]);
			$inam = strtotime($dtrin1[$key]);

			$get_time_in = mysql_query("select emp_time_in from employee where employeeID='$demp'");
			$rti = mysql_fetch_array($get_time_in);

			if($inam<=strtotime($rti[emp_time_in])) $inam = strtotime($rti[emp_time_in]);

			if($rti[emp_time_in]=="07:30:00" && $outpm>=strtotime("16:30")) $outpm = strtotime("16:30");
			else if($rti[emp_time_in]=="08:00:00" && $outpm>=strtotime("17:00")) { $outpm = strtotime("17:00"); }

			$hrs_worked = round(abs( ($outpm - $inam) )/3600,2) - 1; // Less (1) Hour Break

			$hrs_late = 8 - $hrs_worked; // Late in hours
			$mins_late = $hrs_late * 60; // Late in mins

			if($mins_late < 6) $late = 0;
			if($mins_late >= 6 && $mins_late <16) $late = 0.5;
			if($mins_late >= 16 && $mins_late <= 30) $late = 1;
			if($mins_late > 30) $late = 4;
			
			$hrs_worked = 8 - $late;

			if(empty($dtrin1[$key]) || empty($dtrout1[$key]) || empty($dtrin2[$key]) || empty($dtrout2[$key])) $hrs_worked = 0;

			echo '
					<tr>
						<td>'.($keyin).'.</td>
						<td>'.addZeros($demp).'</td>
						<td>'.$dtremp[$key].'</td>
						<td align=left>'.$contract_num.'</td>
						<td>'.date("m/d/Y",strtotime($dtrb_date[$key])).'</td>
						<td>'.$dtrin1[$key].'</td>
						  <td>'.$dtrout1[$key].'</td>
						  <td>'.$dtrin2[$key].'</td>
						  <td>'.$dtrout2[$key].'</td>
						  <!-- <td>'.number_format($hrs_worked,2).' - '.number_format($mins_late, 5).' - '.$late.' - '.$hrs_late.' * '.$old_hrs_worked.'</td> -->
						  <td>'.number_format($hrs_worked,2).'</td>
						  <td>'.getOB($dtrb_date[$key],$demp).'</td>
					</tr>';

			$totalhrs += $hrs_worked;

			if($demp!=$dtrempID[$key+1]) {
				echo '<tr>
		   	        	<td colspan=9 style="border-top:1px solid #000000;text-align:right;">Total :</td>
			        	<td style="border-top:1px solid #000000;">'.$totalhrs.' Hrs</td>
						<td colspan=1 style="border-top:1px solid #000000;text-align:right;">&nbsp;
						<td colspan=1 style="border-top:1px solid #000000;text-align:right;">&nbsp;
						</td>

				</tr>
				<tr><td>&nbsp;</td></tr>';

				$totalhrs = 0;
				$keyin = 0;

                                echo '<table width="100%" border="0" cellspacing="0" cellpadding="3" class="page-break">';

                                if($x != $main_index){
                                   // echo $x." - ".$main_index;
                                    echo '<table width="100%" border="0" cellspacing="0" cellpadding="3">
                                        <tr>
                                                <td class=line_bottom><b>#</b></td>
                                                <td class=line_bottom><b>EMP. #</b></td>
                                                <td class=line_bottom align="center"><b>Employee Name</b></td>
                                                <td class=line_bottom><b>Contract #</b></td>
                                                <td class=line_bottom><b>Date</b></td>
                                                <td class=line_bottom><b>In</b></td>
                                                <td class=line_bottom><b>Out</b></td>
                                                <td class=line_bottom><b>In</b></td>
                                                <td class=line_bottom><b>Out</b></td>
                                                <td class=line_bottom><b>Hrs Worked</b></td>
                                                <td class=line_bottom><b>OB#</b></td>
                                        </tr>';
                                }

			}


			$keyin++;

                        $x++;
		}


		echo '<table>';
	}

?>


</body>
</html>
