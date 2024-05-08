
<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

define('TITLE', "ENCODING HISTORY REPORT");

$from_date    = $_REQUEST['from_date'];
$to_date      = $_REQUEST['to_date'];
$table			= $_REQUEST['table'];

function getRTPMonitoringReport($from_date,$to_date = NULL ){

	if ( !empty($from_date) && !empty($to_date) ) {
		$sql_date = " and h.date between '$from_date' and '$to_date'";
	} else {
		$sql_date = " and h.date <= '$from_date'";
	}

	$sql="
		select
			date, datetime_encoded, concat('Log #',rtp_header_id) as reference, project_name
		from
			rtp_header as h
		left join projects as p on h.project_id = p.project_id
		where
			1=1
		$sql_date		
		and status != 'C'
	";

	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){
		$a[] = $r;
	}

	return $a;
	
}

function getPurchaseRequestReport($from_date,$to_date = NULL ){

	if ( !empty($from_date) && !empty($to_date) ) {
		$sql_date = " and h.date between '$from_date' and '$to_date'";
	} else {
		$sql_date = " and h.date <= '$from_date'";
	}

	$sql="
		select
			date, datetime_encoded, concat('RTP #',pr_header_id) as reference, project_name
		from
			pr_header as h
		left join projects as p on h.project_id = p.project_id
		where
			1=1
		$sql_date		
		and status != 'C'
	";

	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){
		$a[] = $r;
	}

	return $a;
	
}

function getPurchaseOrderReport($from_date,$to_date = NULL ){

	if ( !empty($from_date) && !empty($to_date) ) {
		$sql_date = " and h.date between '$from_date' and '$to_date'";
	} else {
		$sql_date = " and h.date <= '$from_date'";
	}

	$sql="
		select
			date, datetime_encoded, concat('PO #',po_header_id) as reference, project_name
		from
			po_header as h
		left join projects as p on h.project_id = p.project_id
		where
			1=1
		$sql_date		
		and status != 'C'
	";

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
                        <td>DATE#</td>
                        <td>DATE/TIME ENCODED</td>
                        <td>REFERENCE</td>
                        <td>PROJECT</td>                        
                    </tr>
               	</thead>
                <tbody>                  
                    <?php  

                    switch ($_REQUEST['table']) {
                    	case 'rtp':
                    		$aReport = getRTPMonitoringReport($_REQUEST['from_date'],$_REQUEST['to_date']);
                    		break;

                    	case 'pr':
                    		$aReport = getPurchaseRequestReport($_REQUEST['from_date'],$_REQUEST['to_date']);
                    		break;

                    	case 'po':
                    		$aReport = getPurchaseOrderReport($_REQUEST['from_date'],$_REQUEST['to_date']);
                    		break;
                    	
                    	default:
                    		# code...
                    		break;
                    }                    

					if(count($aReport))
						foreach( $aReport as $r ){
		                    echo "
		                        <tr>		                        	
		                        	<td>$r[date]</td>
		                        	<td>$r[datetime_encoded]</td>
		                        	<td>$r[reference]</td>
		                        	<td>$r[project_name]</td>		                        	
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

