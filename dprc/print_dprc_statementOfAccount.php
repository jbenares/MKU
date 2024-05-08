<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	
	
	function getBeginningBalance($date,$application_id){	
		$result = mysql_query("
			select
				* 
			from 
				dprc_payment as p, dprc_ledger as l
			where 
				l.dprc_payment_id = p.dprc_payment_id
			and
				application_id = '$application_id'
			and
				due_date <= '$date'
			order by
				due_date desc
		") or die(mysql_error());	
		
		$r = mysql_fetch_assoc($result);
		return $r;
	}
	
	
	$application_id 	= $_REQUEST['application_id'];
	$date				= $_REQUEST['date'];
	
	
	$result = mysql_query("
		select 
			* 
		from 
			application 
		where 
			application_id = '$application_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	$interest_rate 	= $r['interest_rate']/100;
	$amortization	= $r['amortization'];
	
	$query_period		= date("Ym",strtotime($date));
	$month_due			= date("m",strtotime($date));
	$year_due			= date("Y",strtotime($date));
	$day_due			= $r['date_due'];
	$penalty_rate		= $r['penalty_per_day'];
	$query_date			= "$year_due-$month_due-$day_due";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC STATEMENT OF ACCOUNT</title>
<script>
print();
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/>
<style type="text/css">
	.table-content{
		margin-top:5px;
		width:100%;	
	}
	.table-content tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	.table-content td:nth-child(n+4){
		text-align:right;	
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
	<div style="text-align:left; margin-bottom:5px; font-size:16px;">
		<img id="head_logo" src="../images/chead.png" style="display:inline-block; margin-right:20px;" width="64" height="64" />
		    <p style="display:inline-block; margin:0px; vertical-align:top;">
		        <strong><?=$title?></strong><br />
		        <?=$company_tel_no?><br />
		        <?=$company_address?>
		   	</p>
	    <hr style="margin:5px 0px; border-top:1px solid #000; border-bottom:none;"  />
	</div>

     
     	<div style="font-weight:bolder; border-bottom:1px solid #000;">
			Statement of Account as of <?=dprc::fjy($date)?>
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div>
        	<?php include_once('print_dprc_report_heading.php') ?>
        </div>
        <div class="content" style="">
        	<table class="table-content">            	
            	<tr>
                	<td>Period</td>
                    <td>Date Due</td>
                	<td class="align-right">Principal</td>    
                    <td class="align-right">Interest</td>
                    <td class="align-right">Balance</td>
                    <td>Days</td>
                    <td class="align-right">Penalty</td>
                    <td>Amount</td>
               	</tr>
                
                <?php
				$beg = dprc::getLatestLedger($application_id);
                ?>
                
                <tr>
	                <td>Beginning Balance</td>
                    <td><?=dprc::mdy($beg['due_date'])?></td>
                    <td></td>
                    <td></td>
                	<td class="align-right"><?=number_format($beg['outbal'],2)?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
				<?php
				$period 	= dprc::periodPlusOne($beg['period']);
				$due_date	= dprc::dueDatePlusOne($beg['due_date']);
				$outbal		= $beg['outbal'];
				$rows = 0;
				
				$total_principal = $total_interest = $total_penalty = $total_amount = 0;
				
				while( $period <= $query_period ){
					$days_delayed = dprc::getDaysDiff($due_date,$date);
					$interest 	= ($outbal * $interest_rate) /12;
					$principal 	= $amortization - $interest;
					$outbal -= $principal;
					$penalty = ( $days_delayed * $penalty_rate ) * $amortization;
					$amount = $principal + $interest + $penalty;
					
					$total_interest 	+= $interest;
					$total_principal 	+= $principal;
					$total_penalty 		+= $penalty;
					$total_amount 		+= $amount;
				?>
                <tr>
	                <td><?=dprc::displayPeriod($period)?></td>
                    <td><?=dprc::mdy($due_date)?></td>
                    <td class="align-right"><?=dprc::numform($principal)?></td>
                    <td class="align-right"><?=dprc::numform($interest)?></td>
                	<td class="align-right"><?=dprc::numform($outbal)?></td>
                    <td><?=$days_delayed?></td>
                    <td><?=dprc::numform($penalty)?></td>
                    <td><?=dprc::numform($amount)?></td>
                </tr>
                <?php 
				$due_date	= dprc::dueDatePlusOne($due_date);
				$period 	= dprc::periodPlusOne($period);
				$rows++;
				} 
				?>
				<tr>
                	<td></td>
                    <td></td>
                    <td class="align-right"><?=dprc::numform($total_principal)?></td>
                    <td class="align-right"><?=dprc::numform($total_interest)?></td>
                    <td></td>
                    <td></td>
                    <td class="align-right"><?=dprc::numform($total_penalty)?></td>
                    <td class="align-right"><?=dprc::numform($total_amount)?></td>
                </tr>
            </table>
        </div><!--End of content-->
        
        <!--<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Collection/Cashier</p></td>
                <td>Checked by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
            </tr>
        </table> -->
        <?php include_once("print_dprc_signatories.php") ?>
        
        
    </div><!--End of Form-->
</div>
</body>
</html>