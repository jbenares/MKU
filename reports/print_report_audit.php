<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : AUDIT REPORT
********************************************/

include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$from_date    = $_REQUEST['from_date'];
$to_date      = $_REQUEST['to_date'];

function getReport($from_date,$to_date = NULL){
	
	$sql_date = " and date(a.time_entry) between '$from_date' and '$to_date'";
	
	$sql = "		
		select 
			a.*, concat(user_fname, ', ', user_lname) as name, concat(trans,'#',header_id) as reference
		from
			audit_trail as a 
			inner join admin_access as ac on a.user_id = ac.userID
		where
			header_id != '0'
		and description != ''
			$sql_date

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
            <u>AUDIT REPORT</u>
        </div>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <td>REFERENCE</td>
                        <td>DESCRIPTION</td>                        
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($from_date,$to_date);                    

					if(count($aReport))
						foreach( $aReport as $r ){
		                    echo "
		                        <tr>
		                        	<td>$r[reference]</td>
		                        	<td>".htmlentities($r['description'])."</td>		                        	
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

