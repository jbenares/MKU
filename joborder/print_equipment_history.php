<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : EQUIPMENT HISTORY RECORD
********************************************/

include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");
include_once("../library/DB.php");

$equipment_id = $_REQUEST['equipment_id'];
$from_date    = $_REQUEST['from_date'];
$to_date      = $_REQUEST['to_date'];

$aItem = lib::getTableAttributes("select * from productmaster where stock_id = '$equipment_id'");
$sql  = "
	select 
		d.*, s.account as supplier_name
	from
		rr_header as h
		inner join rr_detail as d on h.rr_header_id = d.rr_header_id
		and d.stock_id = '$equipment_id'
		left join supplier as s on h.supplier_id = s.account_id
";
$aInfo = lib::getTableAttributes($sql);



function displayDetails($equipment_id,$from_date,$to_date){
	$sql = "
		select
			h.date, j.job, h.joborder_header_id, stock,d.amount,d.stock_id
		from 
			joborder_header as h 
			inner join joborder_detail as d on h.joborder_header_id = d.joborder_header_id
			and h.status != 'C'
			and h.date between '$from_date' and '$to_date'
			and h.equipment_id = '$equipment_id'
			and d.joborder_detail_void = '0'
			left join productmaster as p on d.stock_id = p.stock_id
			left join ".DB_HE.".jobs as j on h.job_id = j.job_id
	";

	$result = DB::conn()->query($sql);
        $total=0;
	while( $r = $result->fetch_assoc() ) {
                $query= "select * from issuance_detail where joborder_header_id = '".$r[joborder_header_id]."' and stock_id='".$r[stock_id]."'";
		$result2 = DB::conn()->query($query);
                $rr = $result2->fetch_assoc();
                echo "
			<tr>
				<td>".lib::ymd2mdy($r['date'])."</td>
				<td>$r[job]</td>
				<td>$r[joborder_header_id]</td>
				<td>$r[stock]</td>
                                <td>$rr[issuance_header_id]</td>
                                <td align=center>$rr[quantity]</td>
                                <td align=right>".number_format($rr[price],2)."</td>
				<td align=right>".number_format($r['amount'],2)."</td>
			</tr>
		";
             $total +=$r['amount'];
	}
        
                     echo "<tr>
                                <td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
                                <td></td>
                                <td align=right><b>TOTAL</b></td>
				<td align=right><b>".number_format($total,2)."</b></td>
				<td align=right><b>".$equipment_id."</b></td>
			</tr>";
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
	font-family:"Arial";
	font-size:11px;
}
.container{
	margin:0px auto;	
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

.form-heading{
	text-align: center;
	font-weight: bold;
	font-size: 15px;
}

table{
	width:100%;
	border-collapse:collapse;	
}


/*table  tr td:nth-child(n+2){
	text-align:right;	
}*/
table td{
	padding:3px;	
}
.eq-head{
	width:100%;
}
.eq-head tbody td:nth-child(1){
	width:15%;
}
.eq-head tbody td div{
	width:300px;
	border-bottom: 1px solid #000;
}

.eq-basic-information{
	margin-top: 20px;
}

.eq-basic-information td{
	border:1px solid #000;
}

.eq-basic-information thead td{
	padding:10px;
	border-bottom: 3px double #000;
	font-size: 13px;
	text-transform: uppercase;
	text-align: center;;
}

.eq-body{
	margin-top: 20px;	
}

.eq-body td{
	border:1px solid #000;
	vertical-align: top;
}
.eq-body thead td{
	text-align: center;
	font-weight: bold;

}
.eq-body tbody td:nth-child(5){
	text-align: right;6
}

@media screen {
    div.divFooter {
        display: none;
    }
}
@media print {
    div.divFooter {
        position: fixed;
        bottom: 0;

        font-family: "Arial";
        font-size: 11px;
    }
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<?php require_once(dirname(__FILE__).'/../transactions/form_heading.php');  ?>
     	<p class="form-heading">
     		EQUIPMENT HISTORY RECORD
     	</p>
        <div class="content" >
        	<table class='eq-head'>
                <tbody>                  
                	<tr>
                		<td>Equipment Name</td>
                		<td><div><?=$aItem['stock']?></div></td>
                	</tr>
                	<tr>
                		<td>Equipment ID</td>
                		<td><div><?=$aInfo['asset_code']?></div></td>
                	</tr>
           		</tbody>
            </table>   <!-- END OF EQ-HEAD -->

            <table class='eq-basic-information'>
            	<thead>
            		<tr>
            			<td colspan='4'>BASIC INFORMATION</td>
            		</tr>
            	</thead>
            	<tbody>
            		<tr>
            			<td colspan='2'>
            				Make/Brand
            				<p></p>
            			</td>
            			<td colspan='2'>
            				Serial No./ Plate No:
            				<p><?=$aInfo['serial_no']?></p>
            			</td>
            		</tr>
            		<tr>
            			<td>
            				Model:
            				<p>&nbsp;</p>
            			</td>
            			<td colspan='2'>
            				Date Purchased/Reference:
            				<p><?=( $aInfo['date_acquired'] == "0000-00-00" || empty($aInfo['date_acquired']) ) ? "" : lib::ymd2mdy($aInfo['date_acquired'])?>&nbsp;</p>
            			</td>
            			<td>
            				Supplier:
            				<p><?=$aInfo['supplier_name']?>&nbsp;</p>
            			</td>
            		</tr>
            		<tr>
            			<td colspan='4' style="border-bottom:3px double #000;">
            				Other information/references:
            				<p style="min-height:40px;"><?=$aInfo['details']?></p>
            			</td>
            		</tr>
            	</tbody>
            </table>

            <table class='eq-body'>
            	<thead>
            		<tr>
            			<td>Date</td>
            			<td>CM(Repair) or PM(Preventive Maintenance)?</td>
            			<td>Job Order #</td>
            			<td>Parts Replaced/Materials Used</td>
                                <td>Issuance#</td>
                                <td>Qty</td>
                                <td>Price</td>
            			<td>Costs</td>
            		</tr>
            	</thead>
            	<tbody>
            		<?php displayDetails($equipment_id,$from_date,$to_date) ?>
            	</tbody>
            </table>

        </div><!--End of content-->
    </div><!--End of Form-->

    <div class="divFooter">
        F-EMN-002<br>
        Rev. 0 10/07/13
    </div>
</div>
</body>
</html>

