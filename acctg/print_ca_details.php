<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$project_id		= $_REQUEST['project_id'];	
$mother_account_id	= $_REQUEST['mother_account_id'];

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
function getBalance($gchart_id, $from_date){
	$options 	= new options();
	$mclass 	= $options->getAttribute('gchart','gchart_id',$gchart_id,'mclass');
	$beg_bal 	= $options->getAttribute('gchart','gchart_id',$gchart_id,'beg_bal');	
	
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
			date < '$from_date'			
	") or die(mysql_error());	
	$r = mysql_fetch_assoc($result);
	
	/*if($options->acctg_credit_normal_balance($mclass)){
		$r['balance'] += $beg_bal;
	}else{
		$r['balance'] += $beg_bal;
	}*/
	
	return $r['balance'];
}

function getAccountDetails($gchart_id, $from_date,$to_date){
	$result = mysql_query("
		SELECT
			date,particulars,xrefer,generalreference,description,debit,credit,project_id,header,header_id
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
	$a = array();
	while($r = mysql_fetch_assoc($result)):
		$a[] = $r;
	endwhile;
	
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
<link rel="stylesheet" type="text/css" href="../css/print.css"/>
<style type="text/css">
td{
	vertical-align:top;
}	
td:nth-child(n+4){
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
        	<table cellpadding="3">
            	<tr>
                    <th style="width:10%; text-align:left;">DATE</th>
                    <th style="width:20%; text-align:left;">REF</th>
                    <th style="text-align:left;">DESCRIPTION</th>
                    <th style="width:10%; text-align:right;">DEBIT</th>
                    <th style="width:10%; text-align:right;">CREDIT</th>
                    <th style="width:10%; text-align:right;">BALANCE</th>
                </tr>
                <?php
				$t_balance = 0;
				
				$grandtotald=0;
				$grandtotalc=0;

				foreach(getAccounts($mother_account_id) as $gl):
					//$balance = getBalance($gl['gchart_id'],$from_date,$to_date);
					
					$flag_disp_end = false; #status flag to display end summary per account	 
					$t_debit = $t_credit = 0;
					$flag_disp_header = true; #status flag to display header only once
					foreach(getAccountDetails($gl['gchart_id'],$from_date,$to_date) as $det):
						if(!empty($det) && $flag_disp_header):
							echo "
							<tr>
								<td colspan='5' style='font-weight:bold;'>ACCOUNT : ".$gl['gchart']."</td>
								<td style='text-align:right;'>".number_format($balance,2)."</td>
							</tr>
							";
							$flag_disp_end = true;	
							$flag_disp_header = false;
						elseif(empty($det)):
							continue;
						endif;			
						
						$t_debit 	+= $det['debit'];
						$t_credit 	+= $det['credit'];
						$mclass 	= $options->getAttribute('gchart','gchart_id',$gl['gchart_id'],'mclass');
						if($options->acctg_credit_normal_balance($mclass)){
							$balance -= $det['debit'];
							$balance += $det['credit'];
						}else{
							$balance += $det['debit'];
							$balance -= $det['credit'];
						}
						
						
						$desc_disp = (!empty($det['description']) ? $det['description'] : $det['particulars']);
						#get desc in cv if cv
						if(empty($desc_disp)):
							if($det['header'] == "cv_header_id"):
								$desc_disp = $options->getAttribute('cv_header','cv_header_id',$det['header_id'],'particulars');
								$det['xrefer'] = "CV # : ".$options->getAttribute('cv_header','cv_header_id',$det['header_id'],'cv_no');
							endif;
						endif;
						echo "
						<tr>
							<td>".date("m/d/Y",strtotime($det['date']))."</td>
							<td>".$det['xrefer']."/".$det['generalreference']."</td>
							<td>".$desc_disp."</td>
							<td>".number_format($det['debit'],2)."</td>
							<td>".number_format($det['credit'],2)."</td>
							<td>".number_format($balance,2)."</td>
						</tr>
						";		
					endforeach;
					
					if($flag_disp_end):
						echo "
						<tr>
							<td style='border-top:1px solid #000;'></td>
							<td style='border-top:1px solid #000;'></td>
							<td style='border-top:1px solid #000;'></td>
							<td style='font-weight:bold; border-top:1px solid #000;'>".number_format($t_debit,2)."</td>
							<td style='font-weight:bold; border-top:1px solid #000;'>".number_format($t_credit,2)."</td>
							<td style='border-top:1px solid #000;'></td>
						</tr>
						";
						$grandtotald+=$t_debit;
						$grandtotalc+=$t_credit;	
					endif;
				
				endforeach;
					echo "
						<tr>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
						</tr>
					";
					echo "
						<tr>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
							<td style='border-top:0px solid #000;'></td>
							<td style='font-weight:bold; border-top:1px solid #000;'>".number_format($grandtotald,2)."</td>
							<td style='font-weight:bold; border-top:1px solid #000;'>".number_format($grandtotalc,2)."</td>
							<td style='border-top:0px solid #000;'></td>
						</tr>
					";
                ?>	
          
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>