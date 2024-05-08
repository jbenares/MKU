<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	$date	= $_REQUEST['date'];
	
	$date_0	= $date;
	$date_30 = date("Y-m-d",strtotime("+30 day",strtotime($date)));
	
	$date_31 = date("Y-m-d",strtotime("+31 day",strtotime($date)));
	$date_60 = date("Y-m-d",strtotime("+60 day",strtotime($date)));
	
	$date_61 = date("Y-m-d",strtotime("+61 day",strtotime($date)));
	$date_90 = date("Y-m-d",strtotime("+90 day",strtotime($date)));
	
	function getTotalAR($from_date,$to_date,$application_id){
		$t_ar = 0;
		$date = $from_date;
		do{
			$t_ar += getAR($application_id,$date);
			$date = date("Y-m-d",strtotime("+1 day",strtotime($date)));
		}while($date <= $to_date);
		
		$payments = getPayments($from_date,$to_date,$application_id);
		
		if(($t_ar - $payments) <= 0){
			return 0;
		}else{
			return $t_ar - $payments;
		}		
	}
	
	function getPayments($from_date,$to_date,$application_id){
		$sql = "
			select
				sum(principal + interest) as amount
			from
				dprc_payment as p, dprc_ledger as l
			where
				p.dprc_payment_id = l.dprc_payment_id
			and p.application_id = '$application_id'
			and due_date between '$from_date' and '$to_date'
		";
		$result = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}
	
	function getOutbal($application_id){
		$sql = "
			select
				min(outbal) as amount
			from
				dprc_payment as p, dprc_ledger as l
			where
				p.dprc_payment_id = l.dprc_payment_id
			and p.application_id = '$application_id'
		";
		$result = mysql_query($sql) or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}
	
	function getAR($application_id,$date){
		$options 		= new options();
		$cutoff 		= $options->getAttribute('application','application_id',$application_id,'datecut');
		$loan_term 		= $options->getAttribute('application','application_id',$application_id,'loan_term');
		$amortization 	= $options->getAttribute('application','application_id',$application_id,'amortization');
		$aDates	= array();
		$i = 1;
		do{
			$cutoff = date("Y-m-d",strtotime("+1 month",strtotime($cutoff)));
			$aDates[] = $cutoff;
			$i++;
		}while($i <= ($loan_term * 12));
		
		if(in_array($date,$aDates)){			
			return 	$amortization;
		}else{
			return 0;	
		}
		
		//get all the due dates
		//if date is in array then return monthly amortization
	}
	
	function getApplications(){
		$a = array();
		$result = mysql_query("
			select 
				*
			from 
				application as a, customer as c
			where
				a.customer_id = c.customer_id
			order by customer_last_name asc, customer_first_name asc, customer_middle_name asc
		") or die(mysql_error());
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r;	
		}
		return $a;
	}
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC MONTHLY WATER CONSUMPTION REPORT</title>
<script>

function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/>
<style type="text/css">
	.table-content{
		margin-top:5px;
		width:100%;	
	}
	.table-content thead tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	
	/*
	.table-content tr td:nth-child(n+5){
		text-align:right;
	}
	*/
	
	.table-content td{
		padding:1px 5px;	
	}
	.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000;">
        	 DPRC A/R AGING<br />
			<?=date("dS \of F Y",strtotime($date))?>
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>    
        <table class="table-content">
        	<thead>
            	<tr>
                	<td>APPLICATION NO</td>
                    <td>CUSTOMER</td>
                    
                    <td style="text-align:right;">OUTSTANDING BALANCE</td>
                    <td style="text-align:right;">CURRENT BALANCE</td>
                    <td style="text-align:right;">31-60 DAYS</td>
                    <td style="text-align:right;">61-90 DAYS</td>
                    <td style="text-align:right;">90 +DAYS</td>
                    
                </tr>
            </thead>
            <tbody><br />
			<br />

			<?php
			$i = 1;
			$t_ar_0_30 = $t_ar_31_60 = $t_ar_61_90 = $t_ar_91 = 0;
			$t_outbal = 0;
            foreach(getApplications() as $r):
			
				$outbal = $ob	= getOutbal($r['application_id']);
				$tmp_outbal = $outbal;
				if($outbal > 0){
					$ar_0_30 	= getTotalAR($date_0,$date_30,$r['application_id']);
					$ar_31_60 	= getTotalAR($date_31,$date_60,$r['application_id']);
					$ar_61_90	= getTotalAR($date_61,$date_90,$r['application_id']);
					
					#if($r['application_id']) echo "$date_61 - $date_90 <br>";
					
					if($ar_0_30 <= $outbal){
						$outbal -= $ar_0_30;	
						if($ar_31_60 <= $outbal){
							$outbal -= $ar_0_30;	
							if($ar_61_90 <= $outbal){
								$outbal -= $ar_61_90;	
								$ar_91 = $outbal;
							}else{
								$ar_61_90 = $outbal;
								$ar_91 = 0;
								$outbal	= 0;	
							}							
						}else{
							$ar_31_60 = $outbal;
							$ar_61_90 = 0;
							$ar_91 = 0;
							$outbal = 0;	
						}
					}else{
						$ar_0_30 = $outbal;	
						$ar_31_60 = 0;
						$ar_61_90 = 0;
						$ar_91 = 0;
						$outbal = 0;
					}
					
					echo "
						<tr>
							<td>$r[application_id]</td>
							<td>".htmlentities("$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name]")."</td>
							
							<td style='text-align:right;'>".number_format($ob,2)."</td>
							<td style='text-align:right;'>".number_format($ar_0_30,2)."</td>
							<td style='text-align:right;'>".number_format($ar_31_60,2)."</td>
							<td style='text-align:right;'>".number_format($ar_61_90,2)."</td>
							<td style='text-align:right;'>".number_format($ar_91,2)."</td>
						</tr>
					";
					$t_ar_0_30 += $ar_0_30;
					$t_ar_31_60 += $ar_31_60;
					$t_ar_61_90 += $ar_61_90;
					$t_ar_91 += $ar_91;
					$t_outbal	+= $ob;	
				}
				set_time_limit(30);
				$i++;
				#if($i == 40) break;
            endforeach;
            
			echo "
				<tr>
					<td></td>
					<td></td>
					
					<td style='text-align:right;'>".number_format($t_outbal,2)."</td>
					<td style='text-align:right;'>".number_format($t_ar_0_30,2)."</td>
					<td style='text-align:right;'>".number_format($t_ar_31_60,2)."</td>
					<td style='text-align:right;'>".number_format($t_ar_61_90,2)."</td>
					<td style='text-align:right;'>".number_format($t_ar_91,2)."</td>
				</tr>
			";
            ?>
            </tbody>
        </table>      
        
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Read by:<p>
                    <input type="text" class="line_bottom" /><br>Project Utility</p></td>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Office Engineer</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>P.I.C.</p></td>
            </tr>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>