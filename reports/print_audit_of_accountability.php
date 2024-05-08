<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$rr_detail_id = $_REQUEST['rr_detail_id'];
$from_date    = $_REQUEST['from_date'];
$to_date      = $_REQUEST['to_date'];
$account_id   = $_REQUEST['account_id'];

function getReport($rr_detail_id,$from_date,$to_date = NULL, $account_id = NULL){
	if ( !empty($from_date) && !empty($to_date) ) {
		$sql_date = " and a.date between '$from_date' and '$to_date'";
	} else if( !empty($from_date) ) {
		$sql_date = " and a.date <= '$from_date'";
	} else{
		$sql_date = "";
	}

	$sql = "
		select
			a.date,
			a.time,
			proj.project_name,
			ac.account,
			if(a.received=1,'IN','OUT') as received,
			a.item_status,
			a.remarks
		from
			accountables as a 
			inner join rr_detail as d on a.rr_detail_id = d.rr_detail_id
			inner join productmaster as p on d.stock_id = p.stock_id
			inner join account as ac on ac.account_id = a.account_id
			inner join projects as proj on proj.project_id = a.project_id
		where 
			1=1
		and accountables_void = '0'
		and a.rr_detail_id = '$rr_detail_id'
	";

	if( $account_id ) $sql .= " and a.account_id = '$account_id'";

	$sql .= "
		$sql_date
		order by date asc, time asc
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
<title>JOB ORDER</title>
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
            <u>AUDIT OF ACCOUNTABILITY</u>
        </div>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <td>DATE</td>
                       	<td>TIME</td>
                       	<td>PROJECT</td>
                       	<td>EMPLOYEE</td>
                       	<td>IN/OUT</td>
                       	<td>ITEM STATUS</td>
                       	<td>REMARKS</td>
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($rr_detail_id,$from_date,$to_date,$account_id);

					if(count($aReport))
						foreach( $aReport as $r ){
		                    echo "
		                        <tr>
		                        	<td>$r[date]</td>
		                        	<td>$r[time]</td>
		                        	<td>$r[project_name]</td>
		                        	<td>$r[account]</td>
		                        	<td>$r[received]</td>
		                        	<td>$r[item_status]</td>
		                        	<td>$r[remarks]</td>							
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