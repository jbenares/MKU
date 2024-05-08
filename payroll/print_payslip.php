<?php
#echo "This module is being maintained. Please wait a few minutes.";
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$project_id		= $_REQUEST['project_id'];	
$companyID		= $_REQUEST['companyID'];


function getNumberOfDays($employeeID, $from_date, $to_date){
	$result = mysql_query("
		select
			count(dtr_date) as days
		from
			dtr
		where
			closed = '1'
		and employeeID = '$employeeID'
		and dtr_date between '$from_date' and '$to_date'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	return $r['days'];
}

function isDoublePayAndWorkedTheDayBefore($employeeID,$date){
	$options = new options();
	$rate = $options->getAttribute('holiday','date',$date,'rate');	
	if($rate == 100){
		#check if day before is not sunday or not saturday
		$date_before =  date("Y-m-d",strtotime("-1 day",strtotime($date)));
		//echo date("N",strtotime("-1 day",strtotime($date_before)));
		while((date("N",strtotime($date_before)) <= '7') && (date("N",strtotime($date_before)) >= '6')){	#7 for sunday
			$date_before =  date("Y-m-d",strtotime("-1 day",strtotime($date_before)));			
			//echo "$date_before <br>";			
		}
		#check if he or she worked the day before
		$result = mysql_query("
			select * from dtr where dtr_void = '0' and employeeID = '$employeeID' and dtr_date = '$date_before'
		") or die(mysql_error());
		//echo "select * from dtr where dtr_void = '0' and employeeID = '$employeeID' and dtr_date = '$date_before' <br>";
			
		if(mysql_num_rows($result) > 0) {
			
			return true;
		} else {
			return false;	
		}
		
	}else{
		return false;	
	}
}
function computeEmployeePayroll($employeeID,$from_date,$to_date){	
	$options = new options();
	$employee_statusID	= $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
	$date = $from_date;
	
	$total_salary_amount 		= 0;
	$total_allowance_amount		= 0;
	$total_overtime_amount		= 0;
	$total_work_value			= 0;
	$total_hours_ot				= 0;
	while($date <= $to_date){
		
		/*
		$aRate['daily_rate'] 		= $daily_rate;
		$aRate['daily_allowance'] 	= $daily_allowance;
		$aRate['daily_ot'] 			= $ot_amount;
		$aRate['hours_ot'] 			= $hrs_ot;
		$aRate['daily_work_value']	= $work_value;
		*/
		
		$aRate = computeOnDateSalary($employeeID,$date);
		$total_salary_amount 	+= $aRate['daily_rate'];
		$total_allowance_amount	+= $aRate['daily_allowance'];
		$total_overtime_amount	+= $aRate['daily_ot'];
		$total_work_value		+= $aRate['daily_work_value'];
		$total_hours_ot			+= $aRate['hours_ot'];
		
		//echo "$aRate[daily_rate] <br>";
		
		$date = date("Y-m-d",strtotime("+1 day",strtotime($date)));
	}
	$aRate = array();
	$aRate['total_salary_amount'] 		= $total_salary_amount;
	$aRate['total_allowance_amount']	= $total_allowance_amount;
	$aRate['total_ot_amount']			= $total_overtime_amount;
	$aRate['total_hours_ot']			= $total_hours_ot;
	$aRate['total_work_value']			= $total_work_value;
	
	return $aRate;
}
function computeOnDateSalary($employeeID,$date){
	$options = new options();
	$employee_statusID	= $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
	$no_of_days			= $options->getAttribute('employee_status','employee_statusID',$employee_statusID,'no_of_days');
	
	$result = mysql_query("
		select
			*
		from
			dtr
		where 
			employeeID = '$employeeID'
		and
			closed = '1'
		and
			dtr_void = '0'
		and
			dtr_date = '$date'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	$daily_rate 		= 0;
	$daily_allowance 	= 0;
	$ot_amount			= 0;
	if($employee_statusID == 1) { #MONTHLY
		$daily_rate = $gross_daily_rate = $r['saved_rate'] / $no_of_days;
		$daily_allowance = $r['saved_allowance_rate'] / $no_of_days;
		
		#if work value is less than 8 hours recompute daily allowance
		#if work value is less than 8 hours recompute daily rate
		$work_value = $r['work_value'];
		if($r['work_value'] < 8){
			$daily_allowance = $daily_allowance * ($r['work_value'] / 8);
			$daily_rate = $daily_rate * ($r['work_value'] / 8);
		}
		
		#check ot
		$hrs_ot = $r['hrs_ot'];
		$ot_amount = (($gross_daily_rate / 8) * 1.25) * $hrs_ot;
		#$daily_rate += $ot_amount;  should separate daily rate from ot
		
		#CHECK HOLIDAY RATE
		$holiday_rate = getHolidayRate($date);
		if($holiday_rate > 0){
			#THERE IS A HOLIDAY
			if($daily_rate > 0){ #HAS DTR
				#check if double pay and if he worked the day before
				if(isDoublePayAndWorkedTheDayBefore($employeeID,$date)){
					#$daily_rate = $daily_rate + ($daily_rate * ($holiday_rate/100));
					$ot_amount += ($daily_rate * ($holiday_rate/100));
				}else{
					#no use
					#$daily_rate = $daily_rate; #his rate is still the same. i know its stupid to use this statement but i just want to make my branching clear.	
				}
			}else{ #HAS NO DTR
				$daily_rate = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');	 #GET BASE RATE BECAUSE THERE IS NO SAVED RATE
				#$daily_rate = $daily_rate / $no_of_days;
				$ot_amount += $daily_rate / $no_of_days; #condisder if no dtr but holiday a ot 
				
				$daily_allowance = $options->getAttribute('employee','employeeID',$employeeID,'allowance');
				$daily_allowance = $daily_allowance / $no_of_days;
			}
		}
	}else if($employee_statusID == 2){ #DAILY
		$daily_rate = $gross_daily_rate = $r['saved_rate'] / $no_of_days;
		$daily_allowance = $r['saved_allowance_rate'] / $no_of_days;
		
		#if work value is less than 8 hours recompute daily allowance and daily rate
		$work_value = $r['work_value'];
		if($r['work_value'] < 8){
			$daily_allowance = $daily_allowance * ($r['work_value'] / 8);
			$daily_rate = $daily_rate * ($r['work_value'] / 8);
		}
		
		#check ot
		$hrs_ot = $r['hrs_ot'];
		$ot_amount = (($gross_daily_rate / 8) * 1.25) * $hrs_ot;
		#$daily_rate += $ot_amount;
		
		#CHECK HOLIDAY RATE
		$holiday_rate = getHolidayRate($date);
		if($holiday_rate > 0){
			#THERE IS A HOLIDAY
			if($daily_rate > 0){ #HAS DTR
				if(isDoublePayAndWorkedTheDayBefore($employeeID,$date)){
					#$daily_rate = $daily_rate + ($daily_rate * ($holiday_rate/100));
					$ot_amount += ($daily_rate * ($holiday_rate/100));
				}else{
					#no use
					#$daily_rate = $daily_rate; #his rate is still the same. i know its stupid to use this statement but i just want to make my branching clear.	
				}
				//echo "$daily_rate <br>";
			}
		}
		
	}
	$aRate = array();
	$aRate['daily_rate'] 		= $daily_rate;
	$aRate['daily_allowance'] 	= $daily_allowance;
	$aRate['daily_ot'] 			= $ot_amount;
	$aRate['hours_ot'] 			= $hrs_ot;
	$aRate['daily_work_value']	= $work_value;
	#echo "Daily Rate for $date = $daily_rate <br>";
	#return $daily_rate;	
	return $aRate;
}
function getHolidayRate($date){
	$result = mysql_query("
		select * from holiday where date = '$date' and holiday_void = '0'
	") or die(mysql_error());	
	$r = mysql_fetch_assoc($result);
	return $r['rate'];
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
@media print {
   thead {display: table-header-group;}
}

body{ margin:0px;}

.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
}

.container-body table{
	border-collapse:collapse;
	width:40%;
	display:inline-table;	
}
</style>
</head>
<body>
	<?php
	$result = mysql_query("
		select
			e.employee_fname, e.employee_lname, e.employee_mname, p.*, proj.project_name, total_deductions
		from
			payroll_accumulator as p, employee as e, projects as proj
		where
			p.empID = e.employeeID
		and e.projectsID = proj.project_id
		and pa_from = '$from_date'
		and pa_to = '$to_date'
		and e.projectsID = '$project_id'
		and e.companyID = '$companyID'
		order by proj.project_name asc
	") or die(mysql_error());

	$i = 0;
	while($r = mysql_fetch_assoc($result)):
		$empID = $r['empID'];
		
		$aDeductions = array(
			"rmy_lending",
			"canteeen",
			"house_rental",
			"personal_chargables",
			"pagibig_loan",
			"sss_loan",
			"sss",
			"philhealth",
			"hdmf",
			"taxes",
		);

		$aOvertime	= array(
			'regular_ot_amount',
			'special_ot_amount',
			'legal_ot_amount'
		);

		$t_deductions = 0;	
		foreach($aDeductions as $list){
			$t_deductions += $r[$list];		
		}
		$t_overtime = 0;
		foreach($aOvertime as $o){	
			$t_overtime += round($r[$o],2);
		}

		$t_gross_pay = $r['gross'];
		$t_net_pay	 = $r['gross'] - $t_deductions;
		
	?>
	<div class="container">
    	<div class="container-header">
        	<?=$title?><br />
			<?=$company_address?><br />
            PAYSLIP<br />
            <?php 
			echo date("F j",strtotime($from_date))." - ".date("F j, Y",strtotime($to_date))."<br><br>";
			echo "<b>".htmlentities("$r[employee_lname], $r[employee_fname] $r[employee_mname]")."</b> <br>";
			?>     	
        </div>
        
        <div class="container-body">
        	<table>
            	<thead>
                	<tr>
                        <td></td>
                        <td style="width:5%;" nowrap># of days</td>
                        <td style="width:10%;"><?=$r['no_of_days']?></td>
                    </tr>
                </thead>
                <tbody>
                	<tr>
                    	<td colspan="2">Semi-Monthly Rate</td>
                        <td style="text-align:right;"><?=number_format($r['basic_salary'],2)?></td>
                    </tr>
					<tr>
                    	<td colspan="2">Overtime Pay</td>
                        <td style="text-align:right;"><?=number_format($t_overtime,2)?></td>
                    </tr>
                    <tr>
                    	<td colspan="2">Project Allowance</td>
                        <td style="text-align:right;border-bottom:1px solid #000;"><?=number_format($r['allowance'],2)?></td>
                    </tr>

                    <tr>
                    	<td colspan="2">Gross Pay</td>
                        <td style="text-align:right;"><?=number_format($r['gross'],2)?></td>
                    </tr>
                    <tr>
                    	<td colspan="2">Less Deductions</td>
                        <td style="text-align:right;"><?=number_format($r['total_deductions'],2)?></td>
                    </tr>
                    <tr>
                    	<td colspan="2" style="font-weight:bold;">Net Pay</td>
                        <td style="text-align:right; border-top:1px solid #000; border-bottom:4px double #000;"><?=number_format($r['net_amount'],2)?></td>
                    </tr>
					<tr>
                    	<td colspan="2" style="font-weight:bold;">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
					<tr>
                    	<td colspan="2" style="font-style:italic;">Cash Advance Balance</td>
                        <td style="text-align:right;border-bottom:3px double #000;"><?=number_format($r['out_bal'],2)?></td>
                    </tr>
                    <tr>
                    	<td colspan='3' style="padding-top:40px;">
                    		<p style="text-align:center; border-top:1px solid #000; display:inline-block; width:200px;">
                    			Received by                    			
                    		</p>
                    	</td>
                    </tr>
					
                </tbody>
            </table>
            <table>
            	<tbody>
                	<tr>
                        <td>W/Tax Payables</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['taxes'],2)?></td>
                    </tr>
                    <tr>
                        <td>Personal Chargables</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['personal_chargables'],2)?></td>
                    </tr>
                    <tr>
                        <td>House Rental</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['house_rental'],2)?></td>
                    </tr>
                    <tr>
                        <td>Canteen</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['canteen'],2)?></td>
                    </tr>
                    <tr>
                        <td>RMY Lending</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['rmy_lending'],2)?></td>
                    </tr>
                    <tr>
                        <td>SSS Contribution</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['sss'],2)?></td>
                    </tr>
                    <tr>
                        <td>PHIC Contribution</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['philhealth'],2)?></td>
                    </tr>
                    <tr>
                        <td>Pag-ibig Contribution</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['hdmf'],2)?></td>
                    </tr>
                    <?php if( $r['sss_loan'] > 0 ): ?>
                    <tr>
                        <td>SSS Loan</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['sss_loan'],2)?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if( $r['pagibig_loan'] > 0 ): ?>
                    <tr>
                        <td>Pag-ibig Loan</td>
                        <td style="text-align:right; width:10%;"><?=number_format($r['pagibig_loan'],2)?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                    	<td style="border-top:1px solid #000; border-bottom:3px double #000;">Total Deductions</td>
                        <td style="text-align:right; width:10%; border-top:1px solid #000; border-bottom:3px double #000;"><?=number_format($r['total_deductions'],2)?></td>
                    </tr>
					 
               	</td>
            </table>
        </div><!--end of container-body -->
    
    </div><!--end of container -->
    <?php
	$i++;
	if( $i%2 == 0 ){ #even
		echo '<div style="page-break-after:always;"></div>';
	}else{	
		echo '<div style="margin:60px 0px;"></div>';
	}
    ?>  
    <?php endwhile; ?>
   
</body>
</html>