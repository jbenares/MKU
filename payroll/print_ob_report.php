<?php
ob_start();
session_start();
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];

$aHead = array('OB#' => 'official_log_book_id','DATE' => 'date','EMPLOYEE' => 'employee_name','FROM PROJECT' => 'from_project', 'TO PROJECT' => 'to_project',
'NOTES' => 'notes');

function getReport($from_date,$to_date = NULL){
	#deliveries
	#customer payments
	
	if(!empty($from_date) && !empty($to_date)){
		$sql_date = " and date between '$from_date' and '$to_date' ";
	}else{
		$sql_date = " and date < '$from_date' ";
	}
	
	$sql = "
	
		-- ob report
		select 
			lpad(official_logbook_id,7,'0') as official_log_book_id,
			date_format(date,'%m/%d/%Y') as date,
			concat(employee_lname,', ',employee_fname,' ',employee_mname) as employee_name,
			p1.project_name as from_project,
			p2.project_name as to_project,
			notes
		from
			official_logbook as o left join employee as e on o.employee_id = e.employeeID
			left join projects as p1 on o.from_project_id = p1.project_id 
			left join projects as p2 on o.to_project_id = p2.project_id
		where
			1=1
		$sql_date
	";		
	
	$result = mysql_query($sql) or die(mysql_error());
	while($r = mysql_fetch_assoc($result)){
		$aTrans[] = $r;	
	}
	
	return $aTrans;

}

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
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

table{ border-collapse:collapse; width:100%; }
table td{ padding:3px; }
table thead td{ font-weight:bold;  border-top:1px solid #000; border-bottom:1px solid #000; }
table thead td:nth-child(2){ width:5%;}
table thead td:nth-child(3){ width:15%;}
table tfoot td{
	border-top:1px solid #000;
	font-weight:bold;	
}
@media print{
	table { page-break-inside:auto }
	tr{ page-break-inside:avoid; page-break-after:auto }
	thead { display:table-header-group }
	tfoot { display:table-footer-group }
	.pb { page-break-after:always }
}
</style>
</head>
<body>
	<table>
    	<thead>
        	<tr>
            	<td colspan="6">
                	<div>
                    	<?=$title?><br />
                        OFFICIAL BUSINESS REPORT<br />
                        <?=lib::ymd2mdy($from_date)?> to <?=lib::ymd2mdy($to_date)?> <br /><br />
                    </div>
                </td>
            </tr>
        	<tr>
            	<?php
				foreach($aHead as $key => $value){
					echo "<td>$key</td>";	
				}
                ?>
            </tr>
        </thead>
        <tbody>
        	<tr>
                <?php
				$aReport = getReport($from_date,$to_date);		
				#echo "<pre>";
				#echo $aReport
				#echo "</pre>";
				if( $aReport ):
					$t_quantity = $t_amount = 0;
					foreach( $aReport as $r ) :
						$t_quantity += $r['quantity'];
						$t_amount 	+= $r['amount'];
						echo "<tr>";
						foreach($aHead as $key => $value){
							echo "<td>$r[$value]</td>";	
						}
						echo "</tr>";						
					endforeach;
				endif;					
                ?>
            </tr>
        </tbody>
    </table>
</body>
</html>
