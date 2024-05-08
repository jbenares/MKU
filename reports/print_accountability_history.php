<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : ACCOUNTABILITY HISTORY 
Date        : 2014/1/29
********************************************/
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");

$employee_name = $_REQUEST['employee_name'];
$project_id    = $_REQUEST['project_id'];
$from_date     = $_REQUEST['from_date'];
$to_date       = $_REQUEST['to_date'];
$rr_detail_id  = $_REQUEST['rr_detail_id'];

function getReport($project_id, $employee_name,$from_date,$to_date,$rr_detail_id){
	
	$sql = "
		select 
			p.stock , p.stock_id, d.rr_detail_id, account, qty, proj.project_name, accountable_id, a.received, date ,d.asset_code
		from
			accountables as a, rr_detail as d, productmaster as p, account as ac, projects as proj
		where
			a.rr_detail_id  = d.rr_detail_id
		and d.stock_id = p.stock_id
		and a.account_id = ac.account_id
		and proj.project_id = a.project_id
		and date between '$from_date' and '$to_date'	
    ";
        
    if(!empty($project_id)){ $sql.=" and a.project_id = '$project_id' "; }
	if(!empty($employee_name)){ $sql.=" and  account like '$employee_name%'"; }
	if(!empty($rr_detail_id)){ $sql.=" and  d.rr_detail_id = '$rr_detail_id'"; }
	
	$sql.=" order by account asc  ";

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
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 1px;
	width:150px;
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
        	<?=$title?> <br />
            <u>ACCOUNTABILITY HISTORY REPORT</u><br />
			From <?=date("F j, Y",strtotime($from_date))?> to <?=date("F j, Y",strtotime($to_date))?>
	</div><br/>
        </div>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <td>DATE</td>
                        <td>ACCOUNT</td>
                        <td>ITEM</td>
						<td>ASSET CODE</td>
                        <td>QTY</td>
                        <td>PROJECT</td>
                        <td>TRANS</td>
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    $aReport= getReport($project_id,$employee_name,$from_date,$to_date,$rr_detail_id);

                    /*echo "<pre>";
                    print_r($aReport);
                    echo "</pre>";*/

					if(count($aReport))
						foreach( $aReport as $r ){
							$trans  = ($r['received']) ? "IN" : "OUT";

		                    echo "
		                        <tr>
		                        	<td>".lib::ymd2mdy($r['date'])."</td>
		                        	<td>$r[account]</td>
		                        	<td>$r[stock]</td>
									<td>$r[asset_code]</td>
		                        	<td>$r[qty]</td>
		                        	<td>$r[project_name]</td>
		                        	<td>$trans</td>
		                        </tr>
		                    ";	  
		              	}               
                    ?>
           		</tbody>
            </table>  
		<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:50px;" class="summary">
        <tr>
            <td>Prepared & Checked By:<p>
                <input type="text" class="line_bottom" /><br></p></td>
            <!--<td>Checked By:<p>
                <input type="text" class="line_bottom" /><br>S. Lareza/R. Armenion</p></td>-->
            <td>Conformed By:<p>
                <input type="text" class="line_bottom" /><br></p></td>
        </tr>
    </table>			
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>


