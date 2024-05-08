<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$project_id		= $_REQUEST['project_id'];	
$mother_account_id	= $_REQUEST['mother_account_id'];

function getMotherName($mother_account_id){
	$sql = mysql_query("Select * from gchart where gchart_id = '$mother_account_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	return $r['gchart'];
}

$mother_account_id = (empty($mother_account_id)) ? 84 : $mother_account_id;

function getAccounts($mother_account_id){
	$a = array();
	$result = mysql_query("
		select * from gchart where parent_gchart_id = '$mother_account_id' order by gchart asc
	") or die(mysql_error());	
	
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r;	
	}
	return $a;
}	

function getBalance($gchart_id,$from_date,$to_date){
	$options 	= new options();
	$mclass 	= $options->getAttribute('gchart','gchart_id',$gchart_id,'mclass');
	//$beg_bal 	= $options->getAttribute('gchart','gchart_id',$gchart_id,'beg_bal');	
	
	$result = mysql_query("
		SELECT
			(sum(debit) - sum(credit)) as balance
		FROM
			gltran_header as h, gltran_detail as d
		WHERE
			h.gltran_header_id = d.gltran_header_id
		and
			h.status != 'C'
		and
			d.gchart_id = '$gchart_id'		
		and
			date between '$from_date' and '$to_date'	
	") or die(mysql_error());	
	$r = mysql_fetch_assoc($result);
	
	/*if($options->acctg_credit_normal_balance($mclass)){
		$r['balance'] -= $beg_bal;
	}else{
		$r['balance'] += $beg_bal;
	}*/
	
	return $r['balance'];
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
<link rel="stylesheet" type="text/css" href="../css/print.css"/>
<style type="text/css">
td{
	vertical-align:top;
}	
td:nth-child(3){
	text-align:right;
}
tr:last-child td{
	text-align:right;
	font-weight:bold;
	border-top:1px solid #000;	
}
</style>

</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder;">
        	<?=$title?><br />
            <?=$company_address?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                    <th style="width:10%; text-align:left;">ACCOUNT #</th>
                    <th style="text-align:left;">ACCOUNT NAME - <?=getMotherName($mother_account_id)?></th>
                    <th style="width:10%; text-align:right;">BALANCE</th>
                </tr>
                <?php
				$t_balance = 0;
				foreach(getAccounts($mother_account_id) as $gl):
				
					$balance = getBalance($gl['gchart_id'],$from_date,$to_date);
						 
					if($balance != 0):
						$t_balance += $balance;
						echo "
						<tr>
							<td>".$gl['acode']."</td>
							<td>".$gl['gchart']."</td>
							<td>".number_format($balance,2)."</td>
						</tr>
						";			
					endif;
				
				endforeach;
				//$t_balance += getBalance($mother_account_id,$to_date);
				echo "
				<tr>
					<td></td>
					<td></td>
					<td>".number_format($t_balance,2)."</td>
				</tr>
				";		
				
                ?>	
          
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>