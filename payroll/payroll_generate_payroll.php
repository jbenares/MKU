<?php
require_once(dirname(__FILE__).'/../library/lib.php');

function getEmployeePayrollAttributes($employeeID,$payroll_sequence_id){
	$aReturn = array();
	$aEmp = lib::getTableAttributes("select * from employee where employeeID = '$employeeID'");

	/*compute monthly salary*/
	if( $aEmp['employee_statusID'] == 1 ){ 
		/*monthly*/
		$monthly_salary = $aEmp['base_rate'];

	} else {
		/*daily*/
		$monthly_salary = $aEmp['base_rate'] * 26;
	}

	$aReturn['monthly_salary'] = $monthly_salary;


	/*compute sss salary*/
	$aReturn['sss'] = computeSSS($monthly_salary,$payroll_sequence_id);

	/*compute philhealth*/
	$aReturn['philhealth'] = computePhilhealth($monthly_salary,$payroll_sequence_id);

	/*compute pag-ibig*/
	$aReturn['pagibig'] = ( $payroll_sequence_id == 2 ) ? 100 : 0;

	return $aReturn;
}


function isOnPayroll($employeeID, $from_date,$to_date){
	$sql = "
		select
			*
		from
			payroll_accumulator
		where
			empID = '$employeeID'
		and pa_from = '$from_date'
		and pa_to = '$to_date'
	";
	$result = mysql_query($sql) or die(mysql_error());
	return (mysql_num_rows($result) > 0) ? TRUE : FALSE;
	
}
function getLoanReferences($from_date,$to_date,$employee_id){
	
	$sql = "
		select
			loan_payment_id
		FROM
			$GLOBALS[db_rmy].loan_payment 
		WHERE	
			loan_payment_void = '0'
		and employee_id = '$employee_id'
		and p_from_date = '$from_date'
		and p_to_date = '$to_date'
	";
	$result = mysql_query($sql) or die(mysql_error());
	$a = array();
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r['loan_payment_id'];
	}
	
	return $a;
}

function getLoanDeduction($from_date,$to_date,$employee_id){
	
	$sql = "
		select
			sum(payment_amount) as amount
		FROM
			$GLOBALS[db_rmy].payment_header as h 
			inner join $GLOBALS[db_rmy].payment_detail as d on h.payment_header_id = d.payment_header_id
			and h.status = 'F'
			and p_from_date = '$from_date'
			and p_to_date = '$to_date'
			and payment_detail_void = '0'
			and employee_id = '$employee_id'
	";
	
	$result = mysql_query($sql) or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	/*$r['references'] = getLoanReferences($from_date,$to_date,$employee_id);*/
	return $r;
}
function getCanteenDeduction($from_date,$to_date,$employee_id){
	
	/*canteen deductions should be finished*/
	
	$sql = "
		select
			h.canteen_ded_header_id,deduction_amount as amount
		FROM
			$GLOBALS[db_rmy].canteen_ded_header as h
			inner join $GLOBALS[db_rmy].canteen_ded_detail as d on h.canteen_ded_header_id = d.canteen_ded_header_id
		WHERE		
			h.status             = 'F'
			and p_from_date      = '$from_date'
			and p_to_date        = '$to_date'
			and employee_id      = '$employee_id'
			and canteen_ded_void = '0'
	";
	$result = mysql_query($sql) or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	return $r;
}
function isAbsent($date,$employeeID){
	$result = mysql_query("
		select
			*
		from
			dtr
		where 
			employeeID = '$employeeID'
		and closed = '0'
		and dtr_void = '0'
		and dtr_date = '$date'
	") or die(mysql_error());
	$hasDtrOnDate = (mysql_num_rows($result) > 0);
		
	if($hasDtrOnDate){
		return false;
	}else{
		if(date("N",strtotime($date)) == 7){
			return false;
		}else{
			if(getHolidayRate($date) == 100){ #regular holiday
				return false;	
			}else{
				return true;	
			}
		}
	}
}

function getNumberOfAbsences($employeeID,$from_date,$to_date){
	$date = $from_date;
	$no_of_absences = 0;
	while($date <= $to_date){
		if(isAbsent($date,$employeeID)){
			$no_of_absences++;
		}
		$date = date("Y-m-d",strtotime("+1 day",strtotime($date)));
	}
	
}
function getNoOfDays($from_date,$to_date,$employeeID){
	$result = mysql_query("
		SELECT (sum( work_value ) /8) as no_of_days
		FROM dtr
		WHERE employeeID = '$employeeID'
		AND dtr_date
		BETWEEN '$from_date' AND '$to_date'
		AND dtr_void = '0'
		and closed = '0'
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	return $r['no_of_days'];
}
function isSunday($date){
	if(date("N",strtotime($date)) == '7'){
		return true;
	}else{
		return false;
	}
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
function getBIRTax($employeeID,$computed_salary,$payroll_sequence){
	$options = new options();
	$dependents 		= $options->getAttribute('employee','employeeID',$employeeID,'dependents');
	$employee_statusID	= $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');

	if($payroll_sequence == 1 && $employee_statusID == 1){	 #if payroll sequence is 1 and monthly return 0 else proceed
		return 0;	
	}
	
	$result = mysql_query("
		select 
			* 
		from 
			bir_contrib 
		where
			empStatID		 	= '$employee_statusID' 
		and	no_of_dependents 	= '$dependents'
		and salary_cutoff 		<= '$computed_salary'
		order by salary_cutoff desc
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	$excess = $computed_salary - $r['salary_cutoff'];
	$total_tax = $r['tax_value'] + ($excess * ($r['over']/100));
	return $total_tax;
}

function getFirstPayrollSalary($employeeID,$fdate){
	$get_prev_salary_one = mysql_query("select
											*
										from
											payroll_accumulator
										where
											empID='$employeeID' and
											pa_to<'$fdate' and
											payroll_sequence='1'
										order by
											pa_to desc");
											
	$rps_one = mysql_fetch_array($get_prev_salary_one);	
	/*$aSalary = array('basic_salary','regular_ot_amount','special_ot_amount','legal_ot_amount');*/
	$aSalary = array('basic_salary');
	$t_salary = 0;
	foreach($aSalary as $s){
		$t_salary += $rps_one[$s];
	}
	return $t_salary;
}

function computePhilhealth($computed_salary,$payroll_sequence){
	/********* Start PhilHealth Computation ************/					
						
	if($computed_salary>50) {
		$getPH = mysql_query("select
									employee_share as max_ee
								from
									philhealth_contrib
								where
									ph_range_min<='$computed_salary' and
									ph_range_max>='$computed_salary' and phvoid = '1'");
		
		
		if(mysql_num_rows($getPH)==0) {
			$getPH = mysql_query("select
										max(employee_share) as max_ee
									from
										philhealth_contrib
									where
										ph_range_min<='$computed_salary' and phvoid = '1'");
			
			$philhealth = 0;
			return  $philhealth;							
		}					
		
		$rPH = mysql_fetch_assoc($getPH);				 
		
		if($payroll_sequence=='2'){
			#$philhealth = $rPH['max_ee'] - $prev_philhealth;
			$philhealth = $rPH['max_ee'];
			#if($philhealth<0) $refund += $philhealth*(-1);
		}						
		else if($payroll_sequence=='1'){
			#$philhealth = 50.00;
			$philhealth = 0.00;
		}
		else $philhealth = 0.00;
	}
	
	#if(empty($rE[philhealth])) $philhealth = 0.00;
	
	return $philhealth;
	
	/********** End PhilHealth Computation ************/	
}
function computeSSS($computed_salary,$payroll_sequence){
	
		
	/********* Start SSS Computation **********************/					
	
	$getSSS = mysql_query("select
								ee
							from
								sss_contrib
							where
								range_value_min<='$computed_salary' and
								range_value_max>='$computed_salary'");
								
	#echo "$employeeID <br>
	#	$prev_sal + $salary <br><br>
	#";
	
	if(mysql_num_rows($getSSS)==0) {
		/*$getSSS = mysql_query("select
									ee,
									range_value_min
								from
									sss_contrib
								order by
									range_value_min desc");		*/			
		$sss = 0;
		return $sss;								
	}					
	
	$rSSS = mysql_fetch_array($getSSS);				 
	
	if($payroll_sequence == '2'){
		$sss = $rSSS['ee'] - $prev_sss;
		
		if($sss<0) $refund = $sss*(-1); // In case there are refunds
	}						
	else if($payroll_sequence=='1') {
	
		//echo $computed_salary.' '.$rSSS[range_value_min].'<br>';
		
		if($computed_salary < $rSSS['range_value_min'] ) $sss = 0.00;
		else{ 
			//$sss = 50.00; #50 pesos sss for payroll_sequence 1
			$sss = 0.00; #50 pesos sss for payroll_sequence 1
		}
	}
	else $sss = 0.00;
	
	//echo "$computed_salary - $sss<br>";
	
	/********** End SSS Computation *************************/
	return $sss;
}

function getEmployees($companyID,$project_id,$employee_type_id, $release_type_id = 1){
	$sql = "
		select 
			* 
		from 
			employee 
		where 
			companyID = '$companyID' 
		and projectsID = '$project_id' 
		and release_type_id = '$release_type_id'
		and inactive = '0'
		and work_category_id = '$_REQUEST[work_category_id]'
	";
	if( !empty($employee_type_id) ) $sql .= " and employee_type_id = '$employee_type_id'";

	$sql .= " order by employee_lname asc, employee_fname asc";
	
	$result = mysql_query($sql) or die(mysql_error());
	$employees = array();
	while($r = mysql_fetch_assoc($result)){
		$employees[] = $r['employeeID'];	
	}
	
	#echo "select * from employee where companyID = '$companyID' and projectsID = '$project_id'";
	return $employees;
}

function computeEmployeePayroll($employeeID,$from_date,$to_date){	
	$options = new options();
	$employee_statusID	= $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
	
	if($employee_statusID == 1){
		$monthly_allowance = $options->getAttribute('employee','employeeID',$employeeID,'allowance');
		$daily_allowance   = $monthly_allowance / 26;
	}else{
		$daily_allowance = $options->getAttribute('employee','employeeID',$employeeID,'allowance');
		$daily_base_rate =  $options->getAttribute('employee','employeeID',$employeeID,'base_rate');
	}
	
	$date = $from_date;
	
	$t_daily_allowance 		=	$t_regular_salary		=
	$t_regular_hrs_ot		=  	$t_regular_ot_amount	= 
	$t_special_hrs_ot		= 	$t_special_ot_amount	= 
	$t_legal_hrs_ot			= 	$t_legal_ot_amount		= 
	$t_work_value			=	$t_taxes				= 0;
	
	while($date <= $to_date){
		$aRate = computeOnDateSalary($employeeID,$date);
					
		$t_regular_salary    += $aRate['regular_salary'];

		$t_regular_hrs_ot    += $aRate['regular_hrs_ot'];
		$t_regular_ot_amount += $aRate['regular_ot_amount'];

		$t_special_hrs_ot    += $aRate['special_hrs_ot'];
		$t_special_ot_amount += $aRate['special_ot_amount'];

		$t_legal_hrs_ot      += $aRate['legal_hrs_ot'];
		$t_legal_ot_amount   += $aRate['legal_ot_amount'];

		$t_work_value        += $aRate['work_value'];
				
		$date = date("Y-m-d",strtotime("+1 day",strtotime($date)));
	}
	
	$days                       = $t_work_value / 8;
	
	$aRate                      = array();
	$aRate['regular_salary']    = $t_regular_salary;
	
	$aRate['regular_hrs_ot']    = $t_regular_hrs_ot;
	$aRate['regular_ot_amount'] = $t_regular_ot_amount;
	
	$aRate['special_hrs_ot']    = $t_special_hrs_ot;
	$aRate['special_ot_amount'] = $t_special_ot_amount;
	
	$aRate['legal_hrs_ot']      = $t_legal_hrs_ot;
	$aRate['legal_ot_amount']   = $t_legal_ot_amount;
	
	$aRate['work_value']        = $t_work_value;
	
	#$aRate['total_tax']        = $t_taxes;
	$aRate['total_tax']         = 0; /*taxes are 0 for daily employees*/
	
	$aRate['total_allowance']   = round($days,2) * $daily_allowance;
	
	/*overide previous values for monthly type of employees*/
	/*if employee type is monthly*/
	if($employee_statusID == 1){ 
		$monthly_allowance   = $options->getAttribute('employee','employeeID',$employeeID,'allowance');
		$daily_allowance     = $monthly_allowance / 26;
		
		/*compute semi-monthly allowance*/
		/*get total allowance and multiply by 13*/
		$t_allowance         = $daily_allowance * 13;
		
		/*compute semi-monthly salary*/
		/*get daily salary and multiply by 13*/
		$monthly_base_rate   = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');
		$daily_base_rate     = $monthly_base_rate / 26;
		$t_base_rate         = $daily_base_rate * 13;		
		
		/*check if he/she has fixed ot*/		
		$daily_fixed_ot      = $options->getAttribute('employee','employeeID',$employeeID,'fixed_ot');
		$daily_fixed_ot      = (!empty($daily_fixed_ot)) ? $daily_fixed_ot : 0;
		$hourly_base_rate    = $daily_base_rate / 8;
		
		$ot_hourly_base_rate = $hourly_base_rate * 1.25;
		$regular_hours_ot    = $daily_fixed_ot * 13;
		$regulary_ot_amount  = $ot_hourly_base_rate * $regular_hours_ot;
		
		$aRate                      = array();
		$no_of_absenses             = getNumberOfAbsences($employeeID,$from_date,$to_date);
		$aRate['total_allowance']   = $t_allowance;
		$aRate['regular_salary']    = $t_base_rate;
		$aRate['regular_hrs_ot']    = $regular_hours_ot;
		$aRate['regular_ot_amount'] = $regulary_ot_amount;
		$aRate['special_hrs_ot']    = $t_special_hrs_ot;
		$aRate['special_ot_amount'] = $t_special_ot_amount;
		$aRate['legal_hrs_ot']      = $t_legal_hrs_ot;
		$aRate['legal_ot_amount']   = $t_legal_ot_amount;
		$aRate['work_value']        = 104 - $no_of_absenses; /*104 is 8 * 13*/
		$aRate['total_tax']         = 0;
		
	}
	
	
	
	#return $total_salary;
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
			closed = '0'
		and
			dtr_void = '0'
		and
			dtr_date = '$date'
	") or die(mysql_error());

	$hasDtrOnDate = (mysql_num_rows($result) > 0);
	$r = $aDtr = mysql_fetch_assoc($result);
	
	$daily_rate      = 0;
	$daily_allowance = 0;
	$special_hrs_ot  = $special_ot_amount 	= 0;
	$legal_hrs_ot    = $legal_ot_amount		= 0;
	$regular_hrs_ot  = $regular_ot_amount 	= 0;
	$regular_salary  = 0;

	/*get holiday rate*/
	$holiday_rate 	= getHolidayRate($date);

	/*monthly type of employees*/
	if($employee_statusID == 1) {

		/*computation of basic salary and regular holiday rate is found in the function computeEmployeePayroll*/

		/*computation for hourly base rate*/
		$monthly_base_rate = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');
		$daily_base_rate   = $monthly_base_rate / 26;	
		$hourly_base_rate  = $daily_base_rate / 8;
		
		#THERE IS A HOLIDAY
		#check if double pay and if he worked the day before

		/*is legal holiday*/
		if($holiday_rate == 100){		

			if( $hasDtrOnDate ){					
				/*legal hours ot is assumed to be 8 hours*/
				$legal_hrs_ot    = 8;
				$legal_ot_amount = $hourly_base_rate * $legal_hrs_ot;					
			}		
			
		/*is special holiday*/
		}else if($holiday_rate == 130){ #no work no pay

			if( $hasDtrOnDate ){
				/*special hours ot will be assumed as 8 hours*/
				$special_hrs_ot		= 8;			
				$special_ot_amount	= ($hourly_base_rate * 1.3) * $special_hrs_ot;
			}						
		}
		

	/*dailly type of employees*/
	}else if($employee_statusID == 2){ 
		/*computation for hourly base rate*/
		$daily_base_rate  = $options->getAttribute('employee','employeeID',$employeeID,'base_rate');	
		$hourly_base_rate = $daily_base_rate / 8;

		/*compute for daily salary*/
		/*assume work value of employee who has dtr to be 8 hrs*/
		/*Sundays are not included, because it only is a Overtime Rate of 130%*/
		if( $hasDtrOnDate && !isSunday($date) ){				
			
			$work_value        = 8;
			$regular_salary    = $daily_base_rate;	


			/*overide work value. get employee time in, in dtr table and compare it with time in, in dtr.*/
			/*for if time_in in dtr is 00:00:00, continue, do nothing*/
			$emp_time_in = lib::getAttribute('employee','employeeID',$employeeID,'emp_time_in');

			/*get dtr here*/

			$dtr_sql = "
				select
					time_in, max(work_value) as work_value
				from
					dtr
				where
					employeeID = '$employeeID'
				and dtr_date = '$date'		
				and dtr_void = '0'
			";
			$arr_dtr = lib::getTableAttributes($dtr_sql);

			$dtr_time_in = $arr_dtr['time_in'];

			if( $dtr_time_in != "00:00:00" ){
				if( $emp_time_in == "07:30:00" ){
					if( $time_in < "07:36:00" ){						
						$work_value = 8;
					} else if( $time_in >= "07:36:00" && $time_in < "07:46:00" ){
						$work_value = 7.5;
					} else if( $time_in >= "07:46:00" && $time_in < "08:01:00" ){
						$work_value = 7;
					} else if( $time_in >= "08:01:00" ){
						$work_value = 4;
					}
				} else if( $emp_time_in == "08:00:00" ){
					if( $time_in < "08:06:00" ){						
						$work_value = 8;
					} else if( $time_in >= "08:06:00" && $time_in < "08:16:00" ){
						$work_value = 7.5;
					} else if( $time_in >= "08:16:00" && $time_in < "09:01:00" ){
						$work_value = 7;
					} else if( $time_in >= "09:01:00" ){
						$work_value = 4;
					}
				}
			}

			/*overide work value. work value will be taken from dtr as stated by mr. catague*/
			$work_value = $arr_dtr['work_value'];
			//if( $employeeID == 658 ) echo "$work_value $date | ";

			
			/*if employee has fixed ot, get the attribute else, make regular overtime to 0*/
			$daily_fixed_ot    = $options->getAttribute('employee','employeeID',$employeeID,'fixed_ot');
			$daily_fixed_ot    = (!empty($daily_fixed_ot)) ? $daily_fixed_ot : 0;
			
			$regular_hrs_ot    = $daily_fixed_ot;
			$regular_ot_amount = ($hourly_base_rate * 1.25) * $regular_hrs_ot;
		}

		/*is legal holiday*/
		if ($holiday_rate == 100){
		
			if( $hasDtrOnDate ){

				$legal_hrs_ot		= 8;
				$legal_ot_amount	= $hourly_base_rate * $legal_hrs_ot;	

			}			
			
		}else if($holiday_rate == 130){

			if( $hasDtrOnDate ){

				/*special hours ot will be assumed as 8 hours*/
				$special_hrs_ot		= 8;			
				$special_ot_amount	= ($hourly_base_rate * 1.3) * $special_hrs_ot;

			}	
		}					
		
	}	
	
	$aRate = array();
	$aRate['daily_allowance']	= $daily_allowance;

	$aRate['regular_salary']	= $regular_salary;
	$aRate['regular_hrs_ot']	= $regular_hrs_ot;
	$aRate['regular_ot_amount']	= $regular_ot_amount;

	$aRate['special_hrs_ot']	= $special_hrs_ot;
	$aRate['special_ot_amount']	= $special_ot_amount;

	$aRate['legal_hrs_ot']		= $legal_hrs_ot;
	$aRate['legal_ot_amount']	= $legal_ot_amount;

	$aRate['work_value']		= $work_value;
	
	$aRate['daily_rate'] 		= $daily_rate;

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

<script type="text/javascript">
/*function printContent(id)
{
	console.log(jQuery('form').serializeObject());
	var x = jQuery('form').serializeObject();
	var x = JSON.stringify(x,null,2);
	jQuery("#div_iframe").html("<iframe id='JOframe' name='JOframe' style='display:none;' frameborder='0' src='payroll/print_tmp_payroll.php?json="+x+"' width='100%' height='500'></iframe>");

	var html = jQuery("#payroll_container").html();
	var mywindow = window.open('', 'my div', '');
	mywindow.document.write('<html><head><title>Payroll</title>');
	mywindow.document.write('<link rel="stylesheet" href="css/div_print.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(html);
	mywindow.document.write('</body></html>');

	mywindow.print();
	//mywindow.close();
	
    return false;
}
*/
function printContent(id)
{
	//console.log(jQuery('form').serializeObject());
	var x = jQuery('form').serializeObject();
	var x = JSON.stringify(x,null,2);
	
	//alert(x);
	jQuery.post( "payroll/print_tmp_payroll.php", { json : x }, function( data ) {
		//alert( "Data Loaded: " + data );
		jQuery("#div_iframe").html("<iframe id='JOframe' name='JOframe' style='display:none;' frameborder='0' width='100%' height='500'>"+data+"</iframe>");
		//jQuery("#JOframe").html(data);
		
		var newWin = window.frames["JOframe"];
		newWin.document.write('<body onload="window.print()">'+data+'</body>');
		newWin.document.close();
		
	});
	
	//jQuery("#div_iframe").html("<iframe id='JOframe' name='JOframe' style='display:none;' frameborder='0' src='payroll/print_tmp_payroll.php?json="+x+"' width='100%' height='500'></iframe>");
		
    return false;
}
Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep)
{ 
   var n = this,
   c = isNaN(decimals) ? 2 : Math.abs(decimals), //if decimal is zero we must take it, it means user does not want to show any decimal
   d = decimal_sep || '.', //if no decimal separator is passed we use the dot as default decimal separator (we MUST use a decimal separator)

   /*
   according to [http://stackoverflow.com/questions/411352/how-best-to-determine-if-an-argument-is-not-sent-to-the-javascript-function]
   the fastest way to check for not defined parameter is to use typeof value === 'undefined' 
   rather than doing value === undefined.
   */   
   t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, //if you don't want to use a thousands separator you can pass empty string as thousands_sep value

   sign = (n < 0) ? '-' : '',

   //extracting the absolute value of the integer part of the number and converting to string
   i = parseInt(n = Math.abs(n).toFixed(c)) + '', 

   j = ((j = i.length) > 3) ? j % 3 : 0; 
   return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : ''); 
}
</script>
<style type="text/css">
.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
.payroll-table{
	border-collapse:collapse;	
}
.payroll-table tr:first-child{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
	font-weight:bold;
}
.payroll-table td{
	padding:3px 10px;
}
</style>
<?php
	$b                     = $_REQUEST['b'];
	$user_id               = $_SESSION['userID'];	
	$from_date             = $_REQUEST['from_date'];
	$to_date               = $_REQUEST['to_date'];
	$companyID             = $_REQUEST['companyID'];
	$project_id            = $_REQUEST['project_id'];
	$employee_type_id      = $_REQUEST['employee_type_id'];
	$payroll_sequence_id   = $_REQUEST['payroll_sequence_id'];
	
	#PAYROLL ENTRIES
	$basic_salary          = $_REQUEST['basic_salary'];
	$sss                   = $_REQUEST['sss'];
	$philhealth            = $_REQUEST['philhealth'];
	$hdmf                  = $_REQUEST['hdmf'];
	$taxes                 = $_REQUEST['taxes'];
	$employeeID            = $_REQUEST['employeeID'];
	$fdate                 = $_REQUEST['fdate'];
	$tdate                 = $_REQUEST['tdate'];
	$payroll_sequence      = $_REQUEST['payroll_sequence'];
	$allowance             = $_REQUEST['allowance'];
	
	$pagibig_loan          = $_REQUEST['pagibig_loan'];
	$sss_loan              = $_REQUEST['sss_loan'];
	
	$rmy_lending           = $_REQUEST['rmy_lending'];
	$canteen               = $_REQUEST['canteen'];
	$house_rental          = $_REQUEST['house_rental'];
	$personal_chargables   = $_REQUEST['personal_chargables'];
	$out_bal               = $_REQUEST['out_bal'];
	
	$no_of_days            = $_REQUEST['no_of_days'];
	
	$regular_hrs_ot        = $_REQUEST['regular_hrs_ot'];
	$regular_ot_amount     = $_REQUEST['regular_ot_amount'];
	
	$special_hrs_ot        = $_REQUEST['special_hrs_ot'];
	$special_ot_amount     = $_REQUEST['special_ot_amount'];
	
	$legal_hrs_ot          = $_REQUEST['legal_hrs_ot'];
	$legal_ot_amount       = $_REQUEST['legal_ot_amount'];
	$no_of_absences        = $_REQUEST['no_of_absences'];
	$canteen_ded_header_id = $_REQUEST['canteen_ded_header_id'];
	
	$total_deductions      = $_REQUEST['total_deductions'];
	$net_amount            = $_REQUEST['net_amount'];
	$gross                 = $_REQUEST['gross'];

	if( $b == "Save Entries" ){
		#SAVE CANTEEN DEDUCTIONS
			
		$x = 0;
		if(!empty($employeeID)){
			foreach($employeeID as $id){
				mysql_query("
					insert into
						payroll_accumulator
					set
						basic_salary          = '$basic_salary[$x]',
						sss                   = '$sss[$x]',
						philhealth            = '$philhealth[$x]',
						hdmf                  = '$hdmf[$x]',
						taxes                 = '$taxes[$x]',
						pa_from               = '$fdate[$x]',
						pa_to                 = '$tdate[$x]',
						empID                 = '$id',
						payroll_sequence      = '$payroll_sequence[$x]',
						allowance             = '$allowance[$x]',
						rmy_lending           = '$rmy_lending[$x]',
						canteen               = '$canteen[$x]',
						house_rental          = '$house_rental[$x]',
						personal_chargables   = '$personal_chargables[$x]',
						no_of_days            = '$no_of_days[$x]',
						regular_hrs_ot        = '$regular_hrs_ot[$x]',
						regular_ot_amount     = '$regular_ot_amount[$x]',
						special_hrs_ot        = '$special_hrs_ot[$x]',
						special_ot_amount     = '$special_ot_amount[$x]',
						legal_hrs_ot          = '$legal_hrs_ot[$x]',
						legal_ot_amount       = '$legal_ot_amount[$x]',
						pagibig_loan          = '$pagibig_loan[$x]',
						sss_loan              = '$sss_loan[$x]',
						no_of_absences        = '$no_of_absences[$x]',
						canteen_ded_header_id = '$canteen_ded_header_id[$x]',
						total_deductions      = '$total_deductions[$x]',
						net_amount            = '$net_amount[$x]',
						gross                 = '$gross[$x]',
						out_bal               = '$out_bal[$x]'


				") or die(mysql_error());
				$paID = mysql_insert_id();
				
				$aRef = $_REQUEST['e_'.$id];
				if( $aRef ):
					foreach( $aRef as $ref ):
						mysql_query("
							insert into
								loan_ref
							set
								loan_payment_id = '$ref',
								paID = '$paID'
						") or die(mysql_error());
					endforeach;
				endif;
				
				#close entry	
				mysql_query("
					update
						dtr
					set
						closed = '1'
					where
						employeeID = '$id'
					and
						dtr_date between '$fdate[$x]' and '$tdate[$x]'
				") or die(mysql_error());
				$x++;
			}
			$msg = "Entries are Saved.";
		}else{
			#NO ENTRIES	
			$msg = "No Entries are Saved.";
		}
	}
	
	$msg = (empty($msg)) ? "Please print in Landscape mode." : $msg;
?>
<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
	<table>
    	<tr>
        	<td>FROM DATE</td>
            <td><input type="text" class="textbox datepicker" name="from_date" value="<?=$from_date?>"  /></td>
        </tr>
        <tr>
        	<td>TO DATE</td>
            <td><input type="text" class="textbox datepicker" name="to_date" value="<?=$to_date?>" /></td>
        </tr>
        <tr>
        	<td>PAYROLL SEQUENCE</td>
            <td><?=$options->getTableAssoc($payroll_sequence_id,'payroll_sequence_id','Select Payroll',"select * from payroll_sequence",'payroll_sequence_id','description')?></td>
        </tr>
        <tr>
        	<td>COMPANY</td>
            <td><?=$options->getTableAssoc($companyID,'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_name')?></td>
        </tr>
        <tr>
        	<td>PROJECT</td>
            <td><?=$options->getTableAssoc($project_id,'project_id','Select Project',"select * from projects order by project_name asc",'project_id','project_name')?></td>
        </tr>
        <tr>
        	<td>EMPLOYEE TYPE</td>
            <td><?=$options->getTableAssoc($employee_type_id,'employee_type_id','Select Employee Type',"select * from employee_type where employee_type_void = '0' order by employee_type asc",'employee_type_id','employee_type')?></td>
        </tr>
        <tr>
            <td>WORK CATEGORY</td>
            <td><?=lib::getTableAssoc($_REQUEST['work_category_id'],'work_category_id',"Select Work Category","select * from work_category order by work asc",'work_category_id','work')?></td>
        </tr>
    </table>
</div>
<div class="module_actions">
	<input type="submit" name="b" value="Generate Payroll" />
    <input type="submit" name="b" value="Save Entries" />
    <input type="button" value="Print" onclick="printContent('JOframe');" />
</div>
<?php if($b == "Generate Payroll"){
	$employees = getEmployees($companyID,$project_id,$employee_type_id);	 #Get all employees with company and project
	echo "	
	<div id='payroll_container' style='overflow:auto;'>
		<table class='payroll-table' id='payroll_table'>
			<tr>
				<td>EMPLOYEE</td>
				<td>FROM DATE</td>
				<td>TO DATE</td>
				<td>NO OF DAYS</td>
				<td style='text-align:right;'>SEMI MONTHLY</td>
				<td style='text-align:right;'>NO OF HRS</td>
				<td style='text-align:right;'>REGULAR 125%</td>
				<td style='text-align:right;'>NO OF HRS</td>
				<td style='text-align:right;'>SPECIAL 130%</td>
				<td style='text-align:right;'>NO OF HRS</td>
				<td style='text-align:right;'>LEGAL 200%</td>
				<td style='text-align:right;'>TOTAL OVERTIME</td>
				<td style='text-align:right;'>ABSENCES</td>
								
				<td style='text-align:right;'>ALLOWANCE</td>
				<td style='text-align:right;'>GROSS</td>
				<td style='text-align:right;'>SSS</td>
				<td style='text-align:right;'>PHILHEALTH</td>
				<td style='text-align:right;'>HDMF</td>
				<td style='text-align:right;'>TAXES</td>
				
				<td style='text-align:right;'>SSS LOAN</td>
				<td style='text-align:right;'>PAG-IBIG LOAN</td>
				
				<td style='text-align:right;'>RMY LENDING</td>
				<td style='text-align:right;'>CANTEEN</td>
				<td style='text-align:right;'>HOUSE RENTAL</td>
				<td style='text-align:right;'>PERSONAL CHARGABLES</td>
				
				<td style='text-align:right;'>TOTAL DEDUCTIONS</td>
				
				
				<td style='text-align:right;'>NET</td>
				<td style='text-align:right;'>OUTSTANDING BALANCE</td>
							
			</tr>
	";
	foreach($employees as $employeeID){

		$aEmp              = lib::getTableAttributes("select * from employee where employeeID = '$employeeID'");
		
		$aPayroll          = getEmployeePayrollAttributes($employeeID,$payroll_sequence_id);
		
		#CANTEED DEDUCTIONS 
		$aCanteen          = getCanteenDeduction($from_date,$to_date,$employeeID);
		
		#LOAN DEDUCTION
		$aLoan             = getLoanDeduction($from_date,$to_date,$employeeID);
		
		$employee_statusID = $options->getAttribute('employee','employeeID',$employeeID,'employee_statusID');
		$employee_fixed_ot = $options->getAttribute('employee','employeeID',$employeeID,'fixed_ot');
		
		$aRate             = computeEmployeePayroll($employeeID,$from_date,$to_date);
		
		$aSalary           = array('regular_salary','regular_ot_amount','special_ot_amount','legal_ot_amount');
		$aOvertime         = array('regular_ot_amount','special_ot_amount','legal_ot_amount');
		$total_salary      = $total_overtime = 0;
		foreach($aSalary as $s){	
			$total_salary += round($aRate[$s],2);
		}
		foreach($aOvertime as $o){	
			$total_overtime += round($aRate[$o],2);
		}
		
		$total_allowance 	= $aRate['total_allowance'];
		$employee_name 		= $options->getAttribute('employee','employeeID',$employeeID,'employee_lname').", ".$options->getAttribute('employee','employeeID',$employeeID,'employee_fname');
		

		/*get monthly salary*/		
		$computed_salary      = $aPayroll['monthly_salary'];

		/*deductions*/

		$sss        = $aPayroll['sss'];		
		$philhealth = $aPayroll['philhealth'];
		$hdmf       = $aPayroll['pagibig'];
		
		
		if($employee_statusID == 1){ #monthly
			$basic_minus_deductions = $computed_salary - $sss - $philhealth - $hdmf;
			$taxes                  = getBIRTax($employeeID,$basic_minus_deductions,$payroll_sequence_id);
		}else{ #if daily get from $aRate
			#$taxes = $aRate['total_tax'];
			/*no taxes for the daily*/
			$taxes = 0;
		}
			
		$apply_tax        = $options->getAttribute('employee','employeeID',$employeeID,'apply_tax');		
		$apply_sss        = $options->getAttribute('employee','employeeID',$employeeID,'apply_sss');
		$apply_philhealth = $options->getAttribute('employee','employeeID',$employeeID,'apply_philhealth');
		$apply_hdmf       = $options->getAttribute('employee','employeeID',$employeeID,'apply_hdmf');
		
		$taxes            = ($apply_tax) ? $taxes : 0;
		$sss              = ($apply_sss) ? $sss : 0;
		$philhealth       = ($apply_philhealth) ? $philhealth : 0;
		$hdmf             = ($apply_hdmf) ? $hdmf : 0;
		
		#new computation
		#$no_of_days = getNoOfDays($from_date,$to_date,$employeeID);
		$no_of_days = round($aRate['work_value'] / 8,2);
		
		#compute for no of absences if monthly
		if($employee_statusID == 1){
			$no_of_absences = getNumberOfAbsences($employeeID,$from_date,$to_date);	
		}else{
			$no_of_absences = 0;
		}
		
		
		//compute for fixed ot should be no of days * fixed rate
		$__no_of_days		= $options->getAttribute('employee_status','employee_statusID',$employee_statusID,'no_of_days');
		
		if( $__no_of_days <= 0 ){
			echo "<b><u>Please supply EMPLOYEE STATUS of $employee_name w/ Emp ID of $employeeID</u></b><br>";
			$daily_base_rate = 0;
			$_hourly_rate    = 0;
					
			$daily_allowance = 0;

		} else {
			$daily_base_rate = ( $options->getAttribute('employee','employeeID',$employeeID,'base_rate') / $__no_of_days );		
			$_hourly_rate    = $daily_base_rate /  8;		

			/*compute for daily allowance*/
			$monthly_allowance = $options->getAttribute('employee','employeeID',$employeeID,'allowance');
			$daily_allowance   = $monthly_allowance / $__no_of_days;
		}
		


		$ot_rate         = $_hourly_rate * 1.25;
		$special_ot_rate = $_hourly_rate * 1.30;
		$legal_ot_rate   = $_hourly_rate * 1;



		if($employee_fixed_ot > 0){								
			$regular_hrs_ot 	= $employee_fixed_ot * $no_of_days;
			$regular_ot_amount	= $regular_hrs_ot * $ot_rate;
		}else{
			$regular_hrs_ot 	= $aRate['regular_hrs_ot'];
			$regular_ot_amount	= $aRate['regular_ot_amount'];
		}
		
		/*recompute total overtime*/
		$t_overtime = round($regular_ot_amount,2) + round($aRate['special_ot_amount'],2) + round($aRate['legal_ot_amount'],2);

		/*compute for gross salary*/
		$t_gross = round($t_overtime,2) + round($aRate['regular_salary'],2) + round($total_allowance,2);
		
		if(	!empty($total_salary) && !isOnPayroll($employeeID,$from_date,$to_date)
			#|| 	!empty($sss) 
			#||	!empty($philhealth)
			#||	!empty($hdmf)
			#||	!empty($taxes)
			#|| 1
		):		
			echo "
				<tr>
					<td>".$employee_name."</td>
					<td>$from_date</td>
					<td>$to_date</td>
					<td style='text-align:right;'><input type='text' data-dailyRate='$daily_base_rate' data-dailyAllowance='$daily_allowance' onkeyup='updateSeminMonthly(this);' class='textbox' name='no_of_days[]' id='no_of_days' style='width:60px; text-align:right;' onclick='this.select();' value='".round($no_of_days,2)."'></td>		
					<td style='text-align:right;'><input type='text'  class='textbox' name='basic_salary[]' id='basic_salary' style='width:60px; text-align:right;' onclick='this.select();' value='".round($aRate['regular_salary'],2)."'></td>		
				
									
					<td style='text-align:right;'><input type='text' data-otRate='$ot_rate' onkeyup='updateOvertime(this,\"regular\");' class='textbox' name='regular_hrs_ot[]' id='regular_hrs_ot' style='width:60px; text-align:right;' onclick='this.select();' value='".round($regular_hrs_ot,2)."'></td>		
					<td style='text-align:right;'><input type='text' class='textbox' name='regular_ot_amount[]' onkeyup='computeSalary();' onclick='this.select();' style='width:60px; broder: 1px solid #c0c0c0; text-align:right;' id='regular_ot_amount' value='".round($regular_ot_amount,2)."' ></td>
										
					<td style='text-align:right;'><input type='text' data-otRate='$special_ot_rate' onkeyup='updateOvertime(this,\"special\");' class='textbox' name='special_hrs_ot[]' id='special_hrs_ot' style='width:60px; text-align:right;' onclick='this.select();' value='".round($aRate['special_hrs_ot'],2)."'></td>		
					<td style='text-align:right;'><input type='text' class='textbox' name='special_ot_amount[]' onkeyup='computeSalary();' onclick='this.select();' style='width:60px; broder: 1px solid #c0c0c0; text-align:right;' id='special_ot_amount' value='".round($aRate['special_ot_amount'],2)."' ></td>
										
					<td style='text-align:right;'><input type='text' data-otRate='$legal_ot_rate' onkeyup='updateOvertime(this,\"legal\");' class='textbox' name='legal_hrs_ot[]' id='legal_hrs_ot' style='width:60px; text-align:right;' onclick='this.select();' value='".round($aRate['legal_hrs_ot'],2)."'></td>		
					<td style='text-align:right;'><input type='text' class='textbox' name='legal_ot_amount[]' onkeyup='computeSalary();' onclick='this.select();' style='width:60px; broder: 1px solid #c0c0c0; text-align:right;' id='legal_ot_amount' value='".round($aRate['legal_ot_amount'],2)."' ></td>					
				
					<td style='text-align:right;'><input type='text' class='textbox' name='total_overtime[]' onclick='this.select();' style='width:60px; broder: 1px solid #c0c0c0; text-align:right;' id='total_overtime' value='".round($t_overtime,2)."' ></td>
					<td style='text-align:right;'>".number_format($no_of_absences,2)."</td>				
					
					<td style='text-align:right;'><input type='text'  class='textbox' name='allowance[]' id='allowance' onchange='computeSalary();' style='width:60px; text-align:right;' onclick='this.select();' value='".round($total_allowance,2)."'></td>		
					<td style='text-align:right;'><input type='text' class='textbox' name='gross[]' style='width:60px; text-align:right;' value='".round($t_gross,2)."'></td>
					
					<td style='text-align:right;'>".number_format($sss,2)."</td>
					<td style='text-align:right;'>".number_format($philhealth,2)."</td>
					<td style='text-align:right;'>".number_format($hdmf,2)."</td>
					<td style='text-align:right;'>".number_format($taxes,2)."</td>
					
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='sss_loan[]' onkeyup='computeSalary();' autocomplete=\"off\"' ></td>
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='pagibig_loan[]' onkeyup='computeSalary();' autocomplete=\"off\" ></td>
					
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='rmy_lending[]' onkeyup='computeSalary();' autocomplete=\"off\"' value='".$aLoan['amount']."' ></td>
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='canteen[]' onkeyup='computeSalary();' autocomplete=\"off\" value='".$aCanteen['amount']."' ></td>
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='house_rental[]' onkeyup='computeSalary();' autocomplete=\"off\" ></td>
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='personal_chargables[]' onkeyup='computeSalary();' autocomplete=\"off\" ></td>
					
					
					<td style='text-align:right;' ><input type='text' style='width:60px; text-align:right;'  class='textbox t_deductions' name='total_deductions[]' value='".round($sss + $philhealth + $hdmf + $taxes + + $aLoan['amount'] + $aCanteen['amount'] ,2)."' ></td>
					
					<td style='text-align:right;'><input type='text' style='width:60px; text-align:right;' class='textbox net' name='net_amount[]' value='".round(round($t_gross,2) - ( round($sss,2) + round($philhealth,2) + round($hdmf,2) + round($taxes,2) + $aLoan['amount'] + $aCanteen['amount']),2)."'></td>
					<td style='text-align:right;'><input type='text' class='textbox' style='width:60px; text-align:right;' name='out_bal[]' onkeyup='computeSalary();' autocomplete=\"off\" ></td>					
			";
			
			#loan references
			/*if( $aLoan['references'] ):
				foreach( $aLoan['references'] as $ref ):
					echo "<input type='hidden' name='e_".$employeeID."[]' value='".$ref."'>";
				endforeach;
			endif;*/
			
			echo "
					<input type='hidden' name='canteen_ded_header_id[]' value='".$aCanteen['canteen_ded_header_id']."'>
													
					<input type='hidden' name='sss[]' value='".round($sss,2)."' >
					<input type='hidden' name='philhealth[]' value='".round($philhealth,2)."' >
					<input type='hidden' name='hdmf[]' value='".round($hdmf,2)."' >
					<input type='hidden' name='taxes[]' value='".round($taxes,2)."' >
					<input type='hidden' name='employeeID[]' value='$employeeID'>
					<input type='hidden' name='fdate[]' value='$from_date'>
					<input type='hidden' name='tdate[]' value='$to_date'>
					<input type='hidden' name='payroll_sequence[]' value='$payroll_sequence_id'>					
					
					<input type='hidden' name='special_hrs_ot[]' value='".round($aRate['special_hrs_ot'],2)."'>				
					
					<input type='hidden' name='legal_hrs_ot[]' value='".round($aRate['legal_hrs_ot'],2)."'>				
					
					<input type='hidden' name='no_of_absences[]' value='".round($no_of_absences,2)."'>
					
				</tr>
			";
		endif;
		set_time_limit(30);
	}
	echo "
		</div>
	</table>";

} ?>
<div id="div_iframe">

</div>
</form>

<script type="text/javascript">
jQuery(function(){	

});

function updateSeminMonthly(e){
	var daily_base_rate        = parseFloat(jQuery(e).data('dailyRate'));
	var daily_allowance        = parseFloat(jQuery(e).data('dailyAllowance'));
	var no_of_days             = parseFloat(jQuery(e).val());
	
	var semi_monthly_salary    = daily_base_rate * no_of_days;
	var semi_monthly_allowance = daily_allowance * no_of_days;

	jQuery(e).parent().parent().find("[name='basic_salary[]']").val(semi_monthly_salary.toFixed(2));
	jQuery(e).parent().parent().find("[name='allowance[]']").val(semi_monthly_allowance.toFixed(2));
	setTimeout(computeSalary,100);
}


/*A script that updates the regular 125% rate on chnage of no of hrs*/
function updateOvertime(e,ot_field){
	var ot_rate      = parseFloat(jQuery(e).data('otRate'));
	var no_of_hrs    = parseFloat(jQuery(e).val());
	
	var overtime_pay = ot_rate * no_of_hrs;
	
	/*console.log(ot_rate + ' ' + no_of_hrs);
	console.log(overtime_pay);*/
	if( ot_field == "regular" ){
		jQuery(e).parent().parent().find("[name='regular_ot_amount[]']").val(overtime_pay.toFixed(2));
	} else if( ot_field == "special" ) {
		jQuery(e).parent().parent().find("[name='special_ot_amount[]']").val(overtime_pay.toFixed(2));
	} else if( ot_field == "legal" ) {
		jQuery(e).parent().parent().find("[name='legal_ot_amount[]']").val(overtime_pay.toFixed(2));
	}

	setTimeout(computeSalary,100);
}
function computeSalary(){
	jQuery('#payroll_table tr').each(function(index,value){
		//apply discount to hidden field
		if(index >= 1){
			var basic_salary		= jQuery(this).find("[name='basic_salary[]']").val();
			var allowance 			= jQuery(this).find("[name='allowance[]']").val();
			
			var sss 				= jQuery(this).find("[name='sss[]']").val();
			var philhealth 			= jQuery(this).find("[name='philhealth[]']").val();
			var hdmf 				= jQuery(this).find("[name='hdmf[]']").val();
			var taxes 				= jQuery(this).find("[name='taxes[]']").val();
			
			var pagibig_loan 		= jQuery(this).find("[name='pagibig_loan[]']").val();
			var sss_loan 			= jQuery(this).find("[name='sss_loan[]']").val();
			
			var rmy_lending 		= jQuery(this).find("[name='rmy_lending[]']").val();
			var canteen 			= jQuery(this).find("[name='canteen[]']").val();
			var house_rental 		= jQuery(this).find("[name='house_rental[]']").val();
			var personal_chargables = jQuery(this).find("[name='personal_chargables[]']").val();
					
			rmy_lending 		= (rmy_lending == "") ? 0 : rmy_lending;
			canteen  			= (canteen == "") ? 0 : canteen;
			house_rental  		= (house_rental == "") ? 0 : house_rental;
			personal_chargables = (personal_chargables == "") ? 0 : personal_chargables;
			
			sss_loan = (sss_loan == "") ? 0 : sss_loan;
			pagibig_loan = (pagibig_loan == "") ? 0 : pagibig_loan;


			var regular_ot_amount = jQuery(this).find("[name='regular_ot_amount[]']").val();
			var special_ot_amount = jQuery(this).find("[name='special_ot_amount[]']").val();
			var legal_ot_amount   = jQuery(this).find("[name='legal_ot_amount[]']").val();
			
			regular_ot_amount     = (regular_ot_amount == "") ? 0 : regular_ot_amount;
			special_ot_amount     = (special_ot_amount == "") ? 0 : special_ot_amount;
			legal_ot_amount       = (legal_ot_amount == "") ? 0 : legal_ot_amount;

			var total_overtime = parseFloat(regular_ot_amount) + parseFloat(special_ot_amount) + parseFloat(legal_ot_amount);
			
			var gross				= 	parseFloat(basic_salary) + parseFloat(allowance) + total_overtime;
			var total_deductions 	= 	parseFloat(sss) + 
										parseFloat(philhealth) +
										parseFloat(hdmf) + 
										parseFloat(taxes) + 
										parseFloat(rmy_lending) + 
										parseFloat(canteen) + 
										parseFloat(house_rental) + 
										parseFloat(personal_chargables)+
										parseFloat(pagibig_loan) + 
										parseFloat(sss_loan);
										
			var net					= 	gross - total_deductions;
			
			//put discount % to td
			//jQuery(this).find('td:nth-child(26)').find('.t_deductions').val(total_deductions.toMoney(2,'.',',')) //total deductions
			jQuery(this).find("[name='gross[]']").val(gross.toFixed(2));
			jQuery(this).find('td:nth-child(12)').find('#total_overtime').val(total_overtime.toFixed(2)) //total over time		
			jQuery(this).find('td:nth-child(26)').find('.t_deductions').val(total_deductions.toFixed(2)) //total deductions
			jQuery(this).find('td:nth-child(27)').find('.net').val(net.toFixed(2)) //net
		}
	});	
}

(function($){
    $.fn.serializeObject = function(){

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
})(jQuery);</script>
	