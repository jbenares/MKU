<?php

include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$companyID  = $_REQUEST['companyID'];
$project_id = $_REQUEST['project_id'];
$from_date  = $_REQUEST['from_date'];
$to_date    = $_REQUEST['to_date'];
$employeeID = $_REQUEST['employeeID'];

function getLateOrAbsent($date,$employeeID){

	$emp_time_in = lib::getAttribute('employee','employeeID',$employeeID,'emp_time_in');

	$aReturn = array();
	$aReturn['date'] = $date;
	
	/*get ob here*/
	$aReturn['ob'] = getOB($date,$employeeID);


	if( lib::isSunday($date) ){
		$aReturn['late']     = 0;
		$aReturn['absences'] = 0;

		return $aReturn;
	}

	$sql = "
		select * from dtr where dtr_date = '$date' and dtr_void = '0' and employeeID = '$employeeID'
	";
	

	$result = mysql_query($sql) or die(mysql_error());
	if( mysql_num_rows($result) > 0 ){
		/*has dtr on date*/
		$aReturn['absences'] = 0;
		$r = mysql_fetch_assoc($result);

		$dtr_time_in            =  $time_in = $r['time_in'];
		$aReturn['dtr_time_in'] = $dtr_time_in;

		$duration = 0;
		$duration_sec = 0;
		if( $dtr_time_in != "00:00:00" ){
			if( $emp_time_in == "07:30:00" ){
				
				//1st parsed
				$parsed = date_parse($dtr_time_in);
				
				//2nd parsed
				$parsed2 = date_parse($emp_time_in);
				
				$sec = $parsed['second'] - $parsed2['second']; 
				$min = ($parsed['minute'] - $parsed2['minute']) * 60;
				$hr =  ($parsed['hour'] - $parsed2['hour']) * 60;
				
				$aReturn['hr'] = $hr / 60;
				$aReturn['min'] = $min / 60;
				$aReturn['sec'] = $sec;
			
					
				if( $time_in < "07:36:00" ){						
					$aReturn['late'] = 0;
				} else if( $time_in >= "07:36:00" && $time_in < "07:46:00" ){
					$aReturn['late'] = 30;
				} else if( $time_in >= "07:46:00" && $time_in < "08:01:00" ){
					$aReturn['late'] = 60;
				} else if( $time_in >= "08:01:00" ){
					$aReturn['late'] = 240;
				}
			} else if( $emp_time_in == "08:00:00" ){
				
				//1st parsed
				$parsed = date_parse($dtr_time_in);
				
				//2nd parsed
				$parsed2 = date_parse($emp_time_in);
				
				$sec = $parsed['second'] - $parsed2['second']; 
				$min = ($parsed['minute'] - $parsed2['minute']) * 60;
				$hr =  ($parsed['hour'] - $parsed2['hour']) * 60;
				
				$aReturn['hr'] = $hr / 60;
				$aReturn['min'] = $min / 60;
				$aReturn['sec'] = $sec;
				
				if( $time_in < "08:06:00" ){						
					$aReturn['late'] = 0;
				} else if( $time_in >= "08:06:00" && $time_in < "08:16:00" ){
					$aReturn['late'] = 30;
				} else if( $time_in >= "08:16:00" && $time_in < "09:01:00" ){
					$aReturn['late'] = 60;
				} else if( $time_in >= "09:01:00" ){
					$aReturn['late'] = 240;
				}
			}
		} else {
			/*no time in specified*/
			$aReturn['late'] = 0;
		}


		#if( $date == "2013-09-21" && $employeeID == 20 ) echo "Late : $sql";
		return $aReturn;

	} else{
		/*has no dtr on date*/
		$aReturn['late']     = 0;
		$aReturn['absences'] = 1;		

		return $aReturn;
	}	




}

function getTardiness($from_date,$to_date,$employeeID){

	$date = $from_date;
	$aReturn = array();
	while($date <= $to_date){

		$arr = getLateOrAbsent($date,$employeeID);

		/*add to array if tardinesss or absences is not equal to 0;*/
		if( $arr['late'] > 0 || $arr['absences'] > 0 ){
			$aReturn[] = $arr;
		}

		$date = date("Y-m-d",strtotime("+1 day",strtotime($date)));
	}

	return $aReturn;
}

function getReport($from_date,$to_date, $companyID = NULL, $project_id = NULL,$employeeID){
	$aReturn = array();

	/*get employees here*/
	$sql = "
		select 
			employeeID, concat(employee_lname,', ', employee_fname,' ',employee_mname) as employee_name, emp_time_in
		from 
			employee
		where
			1 = 1
		and
			inactive = 0
		and
			(emp_time_in = '07:30:00' OR emp_time_in = '08:00:00')
	";

	if( !empty($project_id) ) $sql .= " and projectsID = '$project_id'";
	if( !empty($companyID) ) $sql  .= " and companyID = '$companyID'";
	if( !empty($employeeID) ) $sql .= " and employeeID = '$employeeID'";

	$sql .= " order by employee_lname asc, employee_fname asc";

	$arr_employees = lib::getArrayDetails($sql);

	if( count($arr_employees) ){
		foreach ($arr_employees as $arr_employee) {
			$arr_employee['dtr'] = getTardiness($from_date,$to_date,$arr_employee['employeeID']);			
			$aReturn[] = $arr_employee;
			set_time_limit(30);
		}
	}

	return $aReturn;
}

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
	margin:0px auto;
	padding:0.1in;
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

table{
	
	border-collapse:collapse;	
}

table thead tr td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
table  tr td:nth-child(n+3){
	text-align:center;	
}
table td{
	padding:3px;	
	padding-right: 20px;
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
        	<?=$title?> <br />
            <u>TARDINESS SUMMARY REPORT</u>
        </div>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <td>EMP ID</td>
                        <td>EMPLOYEE</td>
                        <td>EMP TIME-IN</td>
                        <td>DATE</td>
                        <td>DTR TIME-IN</td>
                        <td>ABSENT (DAYS)</td>
                        <td>LATE (MINS)</td>
                        <td>HR:MIN:SEC</td>
                        <td>OB#</td>
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($from_date,$to_date,$companyID,$project_id,$employeeID);

                    /*echo "<pre>";
                    print_r($aReport);
                    echo "</pre>";*/

					if(count($aReport))
						foreach( $aReport as $r ){
		                    if(count($r['dtr']) > 0){
		                    	echo "
		                    		<tr>
		                    			<td>$r[employeeID]</td>
		                    			<td>$r[employee_name]</td>
		                    			<td>$r[emp_time_in]</td>
		                    	";
		                    	$i = 0;
		                    	foreach( $r['dtr'] as $arr_dtr ){
		                    		if( $i == 0 ){
		                    			echo "
			                    				<td>$arr_dtr[date]</td>
			                    				<td>$arr_dtr[dtr_time_in]</td>
			                    				<td>$arr_dtr[absences]</td>
			                    				<td>$arr_dtr[late] (".round(($arr_dtr['late'] / 60),2)." hrs)</td>
			                    				<td>".sprintf("%02d", $arr_dtr['hr']).":".sprintf("%02d", $arr_dtr['min']).":".sprintf("%02d", $arr_dtr['sec'])."</td>
			                    				<td>$arr_dtr[ob]</td>
		                    				</tr>
		                    			";
		                    		} else{
		                    			echo "
		                    				<tr>
		                    					<td></td>
		                    					<td></td>
		                    					<td></td>
		                    					<td>$arr_dtr[date]</td>
		                    					<td>$arr_dtr[dtr_time_in]</td>
			                    				<td>$arr_dtr[absences]</td>
			                    				<td>$arr_dtr[late] (".round(($arr_dtr['late'] / 60),2)." hrs)</td>
			                    				<td>".sprintf("%02d", $arr_dtr['hr']).":".sprintf("%02d", $arr_dtr['min']).":".sprintf("%02d", $arr_dtr['sec'])."</td>
			                    				<td>$arr_dtr[ob]</td>
		                    				</tr>
		                    			";
		                    		}

		                    		$i++;
		                    	}
		                    }
		                    
		              	}               
                    ?>
           		</tbody>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

