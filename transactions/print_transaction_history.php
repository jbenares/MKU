<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date				= $_REQUEST['from_date'];
$to_date				= $_REQUEST['to_date'];
$project_id				= $_REQUEST['project_id'];
$project 				= $options->getAttribute("projects","project_id",$project_id,"project_name");

function getEmployees(){
	$result = mysql_query("
		select
			*
		from 
			admin_access
		where
			active = '1'
		and userID not in ('20080228-111008','20100426-061923')
		order by
			user_lname asc, user_fname asc		
	") or die(mysql_error());
	
	$a = array();
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r;	
	}
	return $a;
}

function getTransaction($userID){
	$a = array();
	$a['rtp'] = getRTPMonitoringTransaction($userID);
	$a['po'] = getPOTransaction($userID);
	$a['mrr'] = getMRRTransaction($userID);
	$a['ris'] = getRISTransaction($userID);
	$a['transfer'] = getTransferTransaction($userID);
	$a['adjust'] = getAdjustTransaction($userID);
	$a['cv'] = getCVTransaction($userID);
	$a['rtp_monitoring'] = getRTPMonitoringTransaction($userID);
	$a['eur'] = getEURTransaction($userID);
	$a['batching_plant'] = getBatchingPlantTransaction($userID);
	
	$a['total'] = 	$a['rtp'] + $a['po'] + $a['mrr'] + $a['ris'] + 
					$a['transfer'] + $a['adjust'] + $a['cv'] + 
					$a['rtp_monitoring'] + $a['eur'] + $a['batching_plant'];
	return $a;
}

#RTP
function getRTPTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			pr_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#PO
function getPOTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			po_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#MRR
function getMRRTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			rr_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#RIS
function getRISTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			issuance_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}


#TRANSFER
function getTransferTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			transfer_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#ADJUST
function getAdjustTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			invadjust_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#DISBURSEMENTS
function getCVTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			cv_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}


#RTP MONITORING
function getRTPMonitoringTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			rtp_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#EUR
function getEURTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			eur_header
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
}

#BATCHING PLANT
function getBatchingPlantTransaction($userID){
	$result = mysql_query("
		select
			count(user_id) as quantity
		from
			batch_prod
		where
			user_id = '$userID'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['quantity'];
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
*{ font-family:Arial, Helvetica, sans-serif; font-size:11px; }
thead{ display:table-header-group; }
table{ border-collapse:collapse; width:100%; }
table thead td{
	font-weight:bold;	
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	
}
table td{
	vertical-align:top;
	padding:3px;
}
table td:nth-child(n+2){
	text-align:right;	
	width:5%;
}
table td:nth-child(11){
	text-align:left;	
}
.subtotal td{ border-top:1px solid #000; font-weight:bold; }
.grandtotal td{ border-top:1px solid #000; border-bottom:3px double #000; font-weight:bold; }
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	RTP MONITORING LOG REPORT <br />
        </div>           
        
        <table>
        	<thead>
                <tr>
                	<td>EMPLOYEE</td>
                    <td>RTP</td>
                    <td>PO</td>
                    <td>MRR</td>
                    <td>RIS</td>
                    <td>TRANSFER</td>
                    <td>ADJUST</td>
                    <td>DISBURSEMENTS</td>
                    <td>RTP MONITORING</td>
                    <td>EUR</td>
                    <td>BATCHING PLANT</td>
                    <td>TOTAL</td>
                </tr>
           	</thead>
            <tbody>
            	<?php
				foreach(getEmployees() as $r):
					$aTrans = getTransaction($r['userID']);
					echo "
						<tr>
							<td>$r[user_lname], $r[user_fname]</td>
							<td style='text-align:right;'>".number_format($aTrans['rtp'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['po'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['mrr'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['ris'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['transfer'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['adjust'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['cv'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['rtp_monitoring'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['eur'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['batching_plant'])."</td>
							<td style='text-align:right;'>".number_format($aTrans['total'])."</td>

						</tr>
					";
				endforeach;
                ?>
            </tbody>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>
