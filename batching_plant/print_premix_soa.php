<?php
/********************************************
Author      : Michael Angelo O. Salvio, CpE, MIT
Description : PREMIX STATEMENT OF ACCOUNT
Date        : 2014/01/28
********************************************/

include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");
require_once('../my_Classes/numtowords.class.php');

$from_date  = $_REQUEST['from_date'];
$to_date    = $_REQUEST['to_date'];
$project_id = $_REQUEST['project_id'];

function getReport($project_id,$from_date,$to_date){

	$sql = "		
		select 
			price,volume,stock, remarks, (price * volume) as amount,unit,d.date, (pumpcrete_cost * volume) as pumpcrete_amount, pumpcrete_cost, reference
			from 
			premix_delivery as d left join productmaster as p on d.premix_id = p.stock_id
		where 
			date between '$from_date' and '$to_date'
		and project_id = '$project_id'	
		and d.status != 'C'
	";

	$aReturn                 = array();
	$aReturn['total_amount'] = 0;
	/*premix price volume amount*/
	$result                  = mysql_query($sql) or die(mysql_error());

	while( $r = mysql_fetch_assoc( $result ) ){
		$aReturn['details'][] = $r;
		$aReturn['total_amount'] +=  $r['amount'];
		$aReturn['total_amount'] +=  $r['pumpcrete_amount'];
		$aReturn['pumpcrete_total_amount'] +=  $r['pumpcrete_amount'];
		$aReturn['total_volume'] +=  $r['volume'];
	}

	return $aReturn;
	
}

$sql = "		
		select 
			soa_id
		from 
			soa_history
		order by
			soa_id DESC
	";
	
$result = mysql_query($sql);
$rr = mysql_fetch_assoc($result);
$soa_id = $rr['soa_id'];
$arr = getReport($project_id,$from_date,$to_date);

$convert = new num2words();
$convert->setNumber($arr['total_amount']);
$words = strtoupper($convert->getCurrency());
	
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
	font-size:12px;
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
	border-top: 0px;
	width:140px;
	font-size: 11px;
	text-align: center;
}
</style>
</head>
<body>
<div class="container">    
     	<p style="text-align:right">SOA No.:<?=str_pad($soa_id,4,0,STR_PAD_LEFT)?></p>
 	<p style="text-align:center;">
 		<!-- <b>DYNAMIC BUILDERS & CONSTRUCTION CO. (PHIL) INC.</b> <br>
 		Mailing Address: Dynamic Dept. Alijis Road, Brgy. Alijis Bacolod City, Philippines <br>
 		Tel. # (034) 446-1559; 446-1643 ; 446-3719 Fax #(034) 446-3825 -->
 		<?php require_once(dirname(__FILE__).'/../transactions/form_heading.php'); ?>
 	</p>
	<p style="text-align:center;">SALES AND MARKETING DEPARTMENT</p>        
	<p style="text-align:center;"><span style="text-decoration:underline; font-size:20px; font-weight:bold;">STATEMENT OF ACCOUNT</span></p>
    
    <p>
    	<div style="display:inline-block; vertical-align:top; width:40px;">Date</div>
    	<div style="display:inline-block;" contenteditable="true">
			<?=lib::getFullDate($to_date)?>
		</div>
    </p>
    <p>
    	<div style="display:inline-block; vertical-align:top; width:40px;">To</div>
    	<div style="display:inline-block;">
		<?=lib::getAttribute('projects','project_id',$project_id,'project_name')?>  <br>
		<?=lib::getAttribute('projects','project_id',$project_id,'location')?>
		</div>
    </p>


    <p>
    	Sir: <br><br>

    	We are pleased to submit our Billing Statement for the supply of Concrete Premix for your <b><?=lib::getAttribute('projects','project_id',$project_id,'project_name')?></b>
    	located at <?=lib::getAttribute('projects','project_id',$project_id,'location')?>, representing the amount of <b><?=$words?> Pesos (PhP. <?=number_format($arr['total_amount'],2)?>)</b>

    </p>

    <p>
    	<b>Details as follow:</b>
    	<table>
    		<tbody>
    		<?php
    		if( count($arr['details']) > 0 ){
    			foreach ($arr['details'] as $r) {
    				echo "
    					<tr>
    						<td>PD#: $r[reference] (".lib::getFullDate($r['date']).") $r[stock] x Php. ".number_format($r['price'],2)." / $r[unit] x $r[volume] $r[unit]</td>
    						<td style='text-align:right;'>PhP.".number_format($r['amount'],2)."</td>
    					</tr>
    					<tr>
    						<td style='padding-left:20px;'>Pumpcrete Cost PhP ".number_format($r['pumpcrete_cost'],2)." / $r[unit] x $r[volume] $r[unit]</td>
    						<td style='text-align:right;'>PhP.".number_format($r['pumpcrete_amount'],2)."</td>
    					</tr>

    				";
    			}

    		}    		
    		?>
    		</tbody>
    		<tfoot>
    			<tr>
    				<td style="font-size:15px; font-weight:bold;">TOTAL VOLUME &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
					<?=number_format($arr['total_volume'],2)?> CU.M</td>					
    				<td style='text-align:right;'><span style="border-top:1px solid #000; font-weight:bold;">PhP. <?=number_format($arr['total_amount'],2)?></span></td>
    			</tr>

    			<!--<tr>
    				<td></td>
    				<td></td>
    			</tr> -->   			

    			<tr>
    				<td style="font-size:20px; font-weight:bold;">TOTAL</td>
    				<td style='text-align:right; font-size:20px;'><span style="border-bottom:3px  dashed #000; font-weight:bold;">PhP. <?=number_format($arr['total_amount'],2)?></span></td>
    			</tr>
    		</tfoot>
    	</table>
		</p>
    <p>
    	Here to attached documents for your reference 
    </p>
    <p style="font-weight:bold; font-style:italic;">
    	"Thank you so much for choosing us to be of service to your company. We offer fast and quality service to maintain our continious superb relationship to your company" <br><br>
    	GOD Bless and more power.
    </p>
    <br>
	Very truly yours,
    <p>
    	<table width="100%" cellspacing="0" cellpadding="5" class ="summary" style="text-align:center;">
	        <tr>
		     <td width=100>&nbsp;</td>
	            <td style="padding-right:100px;">
			<p style="text-align:left; margin-bottom:30px;">&nbsp;</p>	            	
	            	<p style="text-align:center;">
	            		<div style="border-bottom:1px solid #000;">Elizalde B. Benedicto</div>
	            		Billing In-Charge
	            	</p>
	            </td>	          	
	        	<td style="padding-right:100px;">	            	
	        		<p style="text-align:left; margin-bottom:30px;">Checked and Verified:</p>
	            	<p style="text-align:center;">
	            		<div style="border-bottom:1px solid #000;">May M. Domingo</div>
	            		Bookkeeper
	            	</p>
	            </td>	          		        	
	            <td>
	            	<p style="text-align:left; margin-bottom:30px;">Approved:</p>
	            	<p style="text-align:center;">
	            		<div style="border-bottom:1px solid #000;">Silvestre G. Lareza</div>
	            		Finance Manager
	            	</p>	   
	            </td>      
		     <td width=100>&nbsp;</td>   	
	        </tr>
	    </table>
    </p>
    
    <p>CC: rjr, jetc, finance, file</p>
</div>
</body>
</html>

