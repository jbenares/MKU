
<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

define('TITLE', "RETAINED UNITS REPORT");

$job_id       = $_REQUEST['job_id'];
$from_date    = $_REQUEST['from_date'];
$to_date      = $_REQUEST['to_date'];
$conducted_by = $_REQUEST['conducted_by'];

function getReport($from_date,$to_date = NULL, $job_id = NULL, $conducted_by = NULL ){

	if ( !empty($from_date) && !empty($to_date) ) {
		$sql_date = " and h.date between '$from_date' and '$to_date'";
	} else {
		$sql_date = " and h.date <= '$from_date'";
	}

	$sql="
		select
			h.*, concat(e1.employee_fname,' ',e1.employee_lname) as driver_name, 
            concat(e2.employee_fname,' ',e2.employee_lname) as inspected_by_name,
            concat(e3.employee_fname,' ',e3.employee_lname) as conducted_by_name,
            concat(e4.employee_fname,' ',e4.employee_lname) as trial_conductd_by_name,
            concat(e5.employee_fname,' ',e5.employee_lname) as accepted_by_name,
            concat(user_fname,' ',user_lname) as encoded_by_name,
            job,
            if(date_started = '0000-00-00','',concat(date_started,' ',time_started)) as time_in,
            if(date_completed = '0000-00-00','',concat(date_completed,' ',time_completed)) as time_out,
            TIME_FORMAT(TIMEDIFF(concat(date_completed,' ',time_completed),concat(date_started,' ',time_started)),'%kh %imins') as actual_time,
        	TIME_FORMAT(TIMEDIFF(ADDTIME(concat(date_started,' ',time_started), SEC_TO_TIME(s_time * 60) ) ,concat(date_started,' ',time_started)),'%kh %imins') as standard_time,
			p.*
		from
			joborder_header as h
			inner join productmaster as p on h.equipment_id = p.stock_id
            left join employee as e1 on h.driver_id = e1.employeeID
            left join employee as e2 on h.inspected_by = e2.employeeID
            left join employee as e3 on h.conducted_by = e3.employeeID
            left join employee as e4 on h.trial_conducted_by = e4.employeeID
            left join employee as e5 on h.accepted_by = e5.employeeID
            left join admin_access as admin on h.encoded_by = admin.userID
            left join ".DB_HE.".jobs as j on h.job_id = j.job_id
		where
			1=1
		$sql_date		
		and date_completed = '0000-00-00'
		order by stock asc
	";

	if( $job_id ) $sql .= " and h.job_id = '$job_id'";
	if( $conducted_by ) $sql .= " and h.conducted_by = '$conducted_by'";

	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){
		$a[] = $r;
	}

	return $a;
	
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
	size: legal portrait;
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
	width:100%;
	border-collapse:collapse;	
}

table thead tr td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
/*table  tr td:nth-child(n+2){
	text-align:right;	
}*/
table td{
	padding:3px;	
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
        	<?=$title?> <br />
            <u style="text-transform:uppercase;"><?=TITLE?></u>
            <p style="text-align:center;">            	            
            	<?php
            	if( !empty($from_date) && !empty($to_date) ){
		           echo "From ".lib::ymd2mdy($from_date)." to ".lib::ymd2mdy($to_date);
		       	} else {
		       		echo "As of ".lib::ymd2mdy($from_date);
		       	}
            	
            	?>
            </p>
        </div>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <td>JO#</td>
						<td>EQUIPMENT</td>
                        <td>DATE/TIME IN</td>
                        <td>JOB</td>
                        <td>DATE/TIME OUT</td>
                        <td>ACTION TAKEN</td>
                        <td>STANDARD TIME</td>
                        <td>ACTUAL TIME</td>
                        <td>MECHANIC</td>
                        <td>DRIVER</td>
						
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($from_date,$to_date,$job_id, $conducted_by);

					if(count($aReport))
						foreach( $aReport as $r ){
		                    echo "
		                        <tr>
		                        	<td>".str_pad($r['joborder_header_id'],7,0,STR_PAD_LEFT)."</td>
									<td>$r[stock]</td>
		                        	<td>$r[time_in]</td>
		                        	<td>$r[job]</td>
		                        	<td>$r[time_out]</td>
		                        	<td>$r[details]</td>
		                        	<td>$r[standard_time]</td>
		                        	<td>$r[actual_time]</td>
		                        	<td>$r[conducted_by_name]</td>
		                        	<td>$r[driver_name]</td>
									
		                        </tr>
		                    ";	  
		              	}               
                    ?>
           		</tbody>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

