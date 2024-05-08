<?php
#require_once("../my_Classes/options.class.php");
require_once(dirname(__FILE__).'/../library/lib.php');
class dprc{
	
	public static function mdy($date){
		if($date == "0000-00-00"){
			return "";
		}else{
			return date("m/d/Y",strtotime($date));	
		}
		
	}
	
	public static function datediff($to_date,$from_date){
		$result = mysql_query("
			select datediff('$to_date','$from_date') as late_days
		") or die(mysql_error());	
		
		$r = mysql_fetch_assoc($result);
		return $r['late_days'];
	}
	
	public static function fjy($date){
		if($date == "0000-00-00"){
			return "";
		}else{
			return date("F j, Y",strtotime($date));	
		}
		
	}
	
	public static function numform($num) {
		if($num==0) $num = "0.00";
		else if($num < 0 ) $num = "( ".number_format(abs($num),2)." )";
		else $num = number_format($num, 2);
		
		return $num;
	}
	
	public static function getOutstandingBalance($application_id){
		$options = new options();
		$net_loan = $options->getAttribute('application','application_id',$application_id,'net_loan');
		$result = mysql_query("
			select 
				outbal
			from
				application as a, dprc_payment as p, dprc_ledger as l
			where
				a.application_id = p.application_id
			and
				p.dprc_payment_id = l.dprc_payment_id
			and
				a.application_id = '$application_id'
			order by
				period desc, or_date desc, or_no desc
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return ($r['outbal'] > 0) ? $r['outbal'] : $net_loan;
	}
	
	public static function getDownpaymentBalance($application_id){
		$options = new options();
		$dp_amount = $options->getAttribute('application','application_id',$application_id,'dp_amount');
		
		#$result = mysql_query("
		#	select sum(payment_amount) as total_downpayment from dprc_payment where postcode in ('D','V') and application_id = '$application_id'
		#") or die(mysql_error());
		
		$result = mysql_query("
			select min(dp_outbal) as outbal from dprc_dp where application_id = '$application_id'
		") or die(mysql_error());
			
		if(mysql_num_rows($result) > 0){
			$r = mysql_fetch_assoc($result);
			$outbal	= $r['outbal'];	
		}else{
			$outbal = $dp_amount;		
		}
		
		return $outbal;
	}
			
	#date_due -- e.g. 15 = 15th of the month
	public static function amortizationTable($years_payable,$interest_rate,$total_liability,$application_date,$date_due,$dp_period,$stop = NULL){
	
		if($interest_rate == 16 && $years_payable == 5) {
			$amortization_factor = 0.0243180571;	
		} else if($interest_rate == 16.25 && $years_payable == 7 ){
			$amortization_factor = 0.0200047141;
		} else if($interest_rate == 16.50 && $years_payable == 10 ){
			$amortization_factor = 0.0170642299;
		} else {
			return 0;
		}
		
		$amortization 		= $total_liability * $amortization_factor;
		$interest_factor 	= $interest_rate / 100;
		$balance 			= $total_liability;
	
		$i = 0;
		$aAmortTable		= array();
		while($balance > 0){
			$amount 	= $amortization;
			$interest 	= ( $balance * $interest_factor )	/ 12;
			$principal 	= $amount - $interest;
			$balance 	-= $principal;
			
			$inc_month	= $dp_period + $i;
			$due_date 	= date("Y-m-$date_due",strtotime("+$inc_month month",strtotime($application_date)));
			$period		= date("Ym",strtotime("+$inc_month month",strtotime($application_date))); 
			
			$aAmort					= array();
			$aAmort['amount'] 		= $amount;
			$aAmort['interest']		= $interest;
			$aAmort['principal']	= $principal;
			$aAmort['period']		= $period;
			$aAmort['balance']		= $balance;	
			$aAmort['due_date']		= $due_date;	
			$aAmortTable[]			= $aAmort;
			$i++;
			
			if( $balance <= $stop && !empty($stop) ){ 
				break; 
			}	
		} 
		return $aAmortTable;
	}
	
	public static function computeNewBalance($years_payable,$interest_rate,$total_liability,$application_date,$date_due,$dp_period,$balance,$or_date,$payment,$grace_period){
		
		if($interest_rate == 16 && $years_payable == 5) {
			$amortization_factor = 0.0243180571;	
		} else if($interest_rate == 16.25 && $years_payable == 7 ){
			$amortization_factor = 0.0200047141;
		} else if($interest_rate == 16.50 && $years_payable == 10 ){
			$amortization_factor = 0.0170642299;
		} else {
			return 0;
		}
		
		$amortization 		= $total_liability * $amortization_factor;
		$interest_factor 	= $interest_rate / 100;
		
		$aAmortTable 	= self::amortizationTable($years_payable,$interest_rate,$total_liability,$application_date,$date_due,$dp_period,$balance);
		$due_date		= $aAmortTable[count($aAmortTable)-1]['period'];
		
		$late_days = 0;
		if(strtotime($due_date) < strtotime($or_date)){
			$datediff = strtotime($or_date) - strtotime($due_date);
			$late_days = floor($datediff/(60*60*24));
		}
		
		if($grace_period < $late_days){
			$penalty 	= ( $amortization * 0.02 * $late_days ) / 30;
			#$penalty = 0.02 / 30;
		}
		
		$interest 	= ( $balance * $interest_factor ) / 12;
		$principal	= $payment - $interest - $penalty;
		$new_balance = $balance - $principal;
		
		$a = array();
		$a['penalty']		= $penalty;
		$a['interest']		= $interest;
		$a['principal']		= $principal;
		$a['new_balance']	= $new_balance;
		$a['payment']		= $payment;
		$a['late_days']		= $late_days;
		$a['due_date']		= $due_date;
		
		return $a;
	}
	
	public static function getNextDueDate($application_id){
		$options = new options();
		$datecut = $options->getAttribute('application','application_id',$application_id,'datecut');
		$date_due = $options->getAttribute('application','application_id',$application_id,'date_due');
		
		$r = self::getLatestLedger($application_id);
		
		if( empty($r['period']) ){
			return date("Y-m-d",strtotime("+1 month",strtotime($datecut)));
		} else {
			return self::getNextDueDateFromPeriod($r['period'],$date_due);
		}  
		
	}
	
	public static function dateToPeriod($date){
		$d = explode("-",$date);
		
		return "$d[0]$d[1]";
	}
	
	public static function getRemainingBalanceFromPeriod($application_id,$period){
		#$options = new options();
		#$amortization = $options->getAttribute('application','application_id',$application_id,'amortization');
		$query = "
			select
				sum(principal + interest) as total_amount
			from
				dprc_ledger as l, dprc_payment as p
			where
				l.dprc_payment_id = p.dprc_payment_id
			and
				application_id = '$application_id'
			and
				period = '$period'
		";
		$rs = mysql_query($query) or die(mysql_error());
		$s = mysql_fetch_assoc($rs);
		
		$amount  = $s['total_amount'];
	
		return $amount;
	}
	
	public static function getTotalLedgerOfPeriod($application_id,$period){
		$result = mysql_query("
			select
				sum(principal) as principal, sum(interest) as interest
			from
				dprc_ledger as l, dprc_payment as p
			where
				l.dprc_payment_id = p.dprc_payment_id
			and
				application_id = '$application_id'
			and
				period = '$period'
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		return $r;
	}
	
	public static function getOutBalOfPrevPeriod($application_id,$period){
		$result = mysql_query("
			select
				min(outbal) as outbal
			from
				dprc_ledger as l, dprc_payment as p
			where
				l.dprc_payment_id = p.dprc_payment_id
			and
				application_id = '$application_id'
			and
				period < '$period'
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		return $r['outbal'];
	}
	
	
	public static function dprcLedger($application_id,$or_date,$payment_amount,$dprc_payment_id,$postcode = "E", $do_not_apply_interest = 0){
		$result = mysql_query("
			select * from application where application_id = '$application_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		
		$amortization 		= $r['amortization'];
		$date_due			= $r['date_due'];
		$grace_period		= $r['grace_period'];

		if( $do_not_apply_interest ){
			$r['interest_rate'] = 0;
		} 

		$interest_factor	= $r['interest_rate'] / 100;	
		
		$net_loan			= $r['net_loan'];
		
		$r = self::getLatestLedger($application_id);
		$l_period		= $r['period'];
		$l_due_date		= $r['due_date'];
		$l_late_days	= $r['late_days'];
		$outbal 		= $r['outbal'];
		
		#get if latest period is more that or equal to the amortization
		#if more than or equal, then get next due date
		
		$remaining_balance  = self::getRemainingBalanceFromPeriod($application_id,$l_period);
		#echo "$remaining_balance ; $outbal ; $amortization";
		$flag_has_balance = false;
		
		//echo "$remaining_balance >= $amortization <br>";


		if( $remaining_balance >= $amortization || (empty($remaining_balance))){

			$due_date			= self::getNextDueDate($application_id);
			$late_days = self::getDaysDiff($due_date,$or_date);
			#echo "$due_date | $or_date";
			#echo "Due date : $due_date <br>";
		}else{
			//get total interest of the period
			//get total principal of the period
			//get outstanding balance of the previous period
			
			$flag_has_balance = true;
			
			$prev_outbal = self::getOutBalOfPrevPeriod($application_id,$l_period);
			$aLedger 	= self::getTotalLedgerOfPeriod($application_id,$l_period);
			
			$prev_interest 	= ( $prev_outbal * $interest_factor ) / 12;
			#echo "Previous Outbal : $prev_outbal <br>";
			#echo "Previous Interest -( $prev_outbal * $interest_factor ) / 12 <br>";
			$prev_principal = $amortization - $prev_interest;
			
			$t_principal 	= $aLedger['principal'];
			$t_interest		= $aLedger['interest'];
			
			$computed_interest 	= $prev_interest - $t_interest;
			$computed_principal = $prev_principal - $t_principal;
			
			$due_date = $l_due_date;
			$flag_has_balance = true; #status flag to check if it has remaining balance

			/*check period here*/
			$_period =  self::dateToPeriod($due_date);
			$_r = lib::getTableAttributes("
				select
					*
				FROM
					dprc_payment as p 
					inner join dprc_ledger as l on p.dprc_payment_id = l.dprc_payment_id
				where
					application_id = '$application_id'
				and period = '$_period'
				order by or_date desc
			");

			if( !empty($_r['or_date']) ){
				#echo "$or_date, $_r[or_date]";
				$late_days = self::datediff($or_date,$_r['or_date']);	
			} else {
				$late_days = self::datediff($or_date,$due_date);	
				$late_days = $late_days - $l_late_days;
			}

			
			

			
			//echo "INTEREST: $t_interest <br> PRINCIPAL: $t_principal <br> PREV OUTBAL : $prev_outbal";
			//echo "Computed Interest + Principal : ".($computed_interest + $computed_principal);
		}
		


		$penalty_amortization = (($computed_interest + $computed_principal) > 0) ? ($computed_interest + $computed_principal) : $amortization;
		
		

		$penalty = 0;
		if(( $grace_period < $late_days )){
			$penalty 	= ( $penalty_amortization * 0.02 * $late_days ) / 30;
			$penalty 	= round($penalty,2);
		}

		#echo "$payment_amount | $penalty <br> " ;

		/*if penalty is greater than payment amount then penalty is equal to payment amount*/
		if( $penalty > $payment_amount ){
			$penalty = $payment_amount;
		}
		
		#BALANCE IS EQUAL TO NET LOAN IF NO LEDGER EXISTS ELSE OUT BAL
		
		$balance = ($outbal > 0) ? $outbal : $net_loan;
		$interest = 0;
		$principal = 0;
		$excess = 0;
				
		if(!$flag_has_balance){ //has no balance
			
			$payment_amount -= $penalty;
			$tmp_interest 		= ( $balance * $interest_factor ) / 12;		
			$tmp_interest 		= round($tmp_interest,2);
			
			//echo "$payment_amount >= $tmp_interest <br>";
			if($payment_amount >=  $tmp_interest){
				$interest = $tmp_interest;	
				$interest = round($interest,2);
				$payment_amount -= $interest;
				$computed_principal = $amortization - $interest;
				$computed_principal = round($computed_principal,2);
				
				
				if(($payment_amount >= $computed_principal)&& $postcode == "R"){
					$excess	= $payment_amount - $computed_principal;
					$excess 	= round($excess,2);
					$principal	 	= $computed_principal;
					$principal 		= round($principal,2);
				}else{	
					$principal 		= $payment_amount;
					$principal 		= round($principal,2);
				}
				
			}else{
				$interest = $payment_amount;	
			}
			$new_balance 	= $balance - $principal;
		}else{ // has balance

			$payment_amount -= $penalty;
			if($payment_amount >=  $computed_interest) {
				
				/*
				if($prev_interest >= $t_interest){ #interest is fully paid
					$interest = 0;
					#echo "Previous Interest : $prev_interest - Total Interest : $t_interest <br>";
				}
				*/
				
				$interest = $computed_interest;
				$interest		= round($interest,2);
				$payment_amount -= $interest;
				
				
				
				if(($payment_amount >= $computed_principal)&& $postcode == "R"){
					$excess	= $payment_amount - $computed_principal;
					$excess = round($excess,2);
					$principal = $computed_principal;
					$principal 		= round($principal,2);
				}else{	
					$principal 		= $payment_amount;
					$principal 		= round($principal,2);
				}
				
			}else{
				$interest = $payment_amount;	
			}
			
			$new_balance = $balance - $principal;
		}
			

		#echo "$interest <br>";

		$aBreakDown = array();
		$aBreakDown['penalty']		= $penalty;
		$aBreakDown['interest']		= $interest;
		$aBreakDown['principal']	= $principal;
		$aBreakDown['new_balance']	= $new_balance;
		$aBreakDown['payment']		= $payment;
		$aBreakDown['late_days']	= $late_days;
		$aBreakDown['due_date']		= $due_date;
		$aBreakDown['period']		= self::dateToPeriod($due_date);

		/*echo "<pre>";
		print_r($aBreakDown);
		echo "</pre>";*/
		
		mysql_query("
				insert into
					dprc_ledger
				set	
					dprc_payment_id = '$dprc_payment_id',
					period			= '$aBreakDown[period]',
					amount  		= '$payment_amount',
					principal		= '$aBreakDown[principal]',
					interest		= '$aBreakDown[interest]',
					penalty			= '$aBreakDown[penalty]',
					late_days		= '$aBreakDown[late_days]',
					outbal			= '$aBreakDown[new_balance]',
					due_date		= '$aBreakDown[due_date]'
			") or die(mysql_error());
		
		if($excess > 0){
			#compute to next ledger	
			//echo "excess : $excess";
			self::dprcLedger($application_id,$or_date,$excess,$dprc_payment_id,$postcode);
		}
		
		return $aBreakDown;
	}
	public static function displayPeriod($period){
		$year = substr($period,0,4);
		$month = substr($period,4,5);
		
		#return "$year - $month";
		return date( "Y/m" , strtotime(date("$year-$month-1")));
	}
	
	public static function getDaysDiff($date1,$date2){
		
		$date2 = new DateTime($date2);
		$date1 = new DateTime($date1);
	
		$interval = date_diff($date2,$date1,true);
		#$months = $interval->format("%m");
		#$years = $interval->format("%y");
		$days	= $interval->format("%a");
		
		$total_days =  $days;
		
		return ($date2->getTimestamp() < $date1->getTimestamp()) ? 0: $total_days ;
	}
	
	public static function getReservationDateOfApplication($application_id){
		$result = mysql_query("	
			select * from dprc_payment where application_id = '$application_id' and postcode = 'V' order by or_date asc
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['or_date'];
	}
	
	
	public static function dprcSpotCash($application_id,$payment_id,$manual_discount_amount = NULL){
		$options = new options();
		
		$application_date 	= $options->getAttribute('application','application_id',$application_id,'application_date');
		$loan_value		 	= $options->getAttribute('application','application_id',$application_id,'loan_value');
		$dp_amount		 	= $options->getAttribute('application','application_id',$application_id,'dp_amount');
		
		$payment_amount 	= $options->getAttribute('dprc_payment','dprc_payment_id',$payment_id,'payment_amount');
		$or_date			= $options->getAttribute('dprc_payment','dprc_payment_id',$payment_id,'or_date');
		
		$period  = dprc::dateToPeriod($application_date);
		
		$new_bal = $loan_value - $dp_amount - $payment_amount;
		
		#INSERT TO LEDGER
		mysql_query("
				insert into
				dprc_ledger
			set	
				dprc_payment_id = '$payment_id',
				period			= '$period',
				amount  		= '$payment_amount',
				principal		= '$payment_amount',
				interest		= '0',
				penalty			= '0',
				late_days		= '0',
				outbal			= '$new_bal',
				due_date		= '$application_date'
		") or die(mysql_error());
		
		
		if( $manual_discount_amount > 0 ){ #MANUAL DISCOUNT
			$discount = $manual_discount_amount;
			$new_bal -= $discount;
			
			mysql_query("
					insert into
					dprc_ledger
				set	
					dprc_payment_id = '$payment_id',
					period			= '$period',
					amount  		= '$discount',
					principal		= '$discount',
					interest		= '0',
					penalty			= '0',
					late_days		= '0',
					outbal			= '$new_bal',
					due_date		= '$application_date'
			") or die(mysql_error());
			
		}else{
			$discount = round($loan_value * 0.08,2);
			$new_bal -= $discount;
			#INSERT DISCOUNT 8% TO TO LEDGER
			
			mysql_query("
					insert into
					dprc_ledger
				set	
					dprc_payment_id = '$payment_id',
					period			= '$period',
					amount  		= '$discount',
					principal		= '$discount',
					interest		= '0',
					penalty			= '0',
					late_days		= '0',
					outbal			= '$new_bal',
					due_date		= '$application_date'
			") or die(mysql_error());
		}
		
	}
	
	
	public static function dprcDPLedger($application_id,$payment_id, $manual_discount_amount = NULL, $discount_remarks = NULL){		
		$options = new options();
		$application_date 	= $options->getAttribute('application','application_id',$application_id,'application_date');
		$dp_amount			= $options->getAttribute('application','application_id',$application_id,'dp_amount');
		$dp_period			= $options->getAttribute('application','application_id',$application_id,'dp_period');
		
		$dp_code 			= $options->getAttribute('application','application_id',$application_id,'dp_code');
		$dp_type			= $options->getAttribute('dprc_dp_code','dp_code',$dp_code,'dp_type');
		
		#echo "Code : $dp_code ; Type : $dp_type";
		
		$payment_amount 	= $options->getAttribute('dprc_payment','dprc_payment_id',$payment_id,'payment_amount');
		$or_date			= $options->getAttribute('dprc_payment','dprc_payment_id',$payment_id,'or_date');
		
		#$reservation_date = self::getReservationDateOfApplication($application_id);
		
		#$dp_start_date = date("Y-m-d",strtotime("+1 month",strtotime($application_date)));		
		
		$dp_start_date = $application_date;
		$dp_end_date	= date("Y-m-d",strtotime("+ $dp_period month",strtotime($dp_start_date)));
		
		#COMPUTE DAYS DELAYED STARTING FORM END DATE  TO OR DATE
			#~ IF POSTCODE IS RESERVATION DAYS DELAYED SHOULD REFERENCE DP START DATE
		#echo "End date :$dp_end_date";

		#echo "$dp_end_date - $or_date";
		
		$days_delayed = $days_diff = self::getDaysDiff($dp_end_date,$or_date);
		
		$dp_disc = 0;
		if( $dp_amount <= $payment_amount ){ #SHOULD BE FULL PAYMENT
		
			if($days_diff <= 7){
				$dp_disc = 0.1;
			}else if($days_diff <= 15){
				$dp_disc = 0.07;
			}else if($days_diff <= 30){
				$dp_disc = 0.05;
			}
		}
		
		$outbal = self::getDPOutstanding($application_id);
		#echo "Outbal : $outbal";


		
		if( !empty($manual_discount_amount) ){
			if($manual_discount_amount > 0){
				$outbal = $outbal - $manual_discount_amount;
				mysql_query("
					insert into
						dprc_dp
					set
						application_id = '$application_id',
						dprc_payment_id = '$payment_id',
						dp_principal = '$manual_discount_amount',
						dp_days = '0',
						dp_penalty = '0',
						dp_outbal = '$outbal',
						discount = '1',
						remarks = '$discount_remarks'
				") or die(mysql_error());
			}
		}else{

			$discount_amount = $outbal * $dp_disc;
			$outbal = $outbal - $discount_amount;
			#INSERT DISCOUNT INTO LEDGER
			if($discount_amount > 0){
				mysql_query("
					insert into
						dprc_dp
					set
						application_id = '$application_id',
						dprc_payment_id = '$payment_id',
						dp_principal = '$discount_amount',
						dp_days = '0',
						dp_penalty = '0',
						dp_outbal = '$outbal',
						discount = '1',
						remarks = 'Early Payment Discount'
				") or die(mysql_error());
			}
		}


				
		$penalty_amount = ( $outbal * 0.02 * $days_delayed ) / 30;

		/*echo "$dp_principal = $payment_amount - $penalty_amount;";*/
		$dp_principal = $payment_amount - $penalty_amount;
		$new_outbal = $outbal - $dp_principal;
		
		$dp_days = ($days_delayed > 0) ? $days_delayed : 0;

		


		#INSERT TO LEDGER
		mysql_query("
			insert into
				dprc_dp
			set
				application_id = '$application_id',
				dprc_payment_id = '$payment_id',
				dp_principal = '$dp_principal',
				dp_days = '$dp_days',
				dp_penalty = '$penalty_amount',
				dp_outbal = '$new_outbal',
				discount = '$disc_status'
		") or die(mysql_error());
		
	}
	
	public static function getDPOutstanding($application_id){
		$options = new options();
		$result  = mysql_query("
			select
				*
			from	
				dprc_dp
			where
				application_id = '$application_id'
			order by
				dprc_dp_id desc
		") or die(mysql_error());		
		
		if( mysql_num_rows($result) > 0 ){ #HAS DOWNPAYMENT
			$r = mysql_fetch_assoc($result);
			$outbal = $r['dp_outbal'];
		}else{	#HAS NO DOWNPAYMENT
			$outbal = $options->getAttribute('application','application_id',$application_id,'dp_amount');
		}
		
		return $outbal;
	}
	
	public static function getAmortSched($application_id){
		$result = mysql_query("
			select * from application where application_id = '$application_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		$net_loan 		= $r['net_loan'];
		$datecut		= $r['datecut'];
		$loan_term		= $r['loan_term'];
		$interest_rate	= $r['interest_rate'];
		$amortization 	= $r['amortization'];
		$outbal 		= $net_loan;
		
		$aResult  = array();
		$period = $datecut;
		$period = 		date("Y-m-d",strtotime("+1 month",strtotime($period)));
		while( $outbal > 0 ){
			$interest 	= ($outbal * ($interest_rate / 100)) / 12;
			$interest	= round($interest,2);
			$principal 	=  $amortization - $interest;
			$outbal 	-= $principal;
			
			$t = array();
			$t['principal']	= $principal;
			$t['interest']	= $interest;
			$t['outbal']	= $outbal;
			$t['period']	= date("Y/m",strtotime($period));
			$t['due_date']	= date("m/d/Y",strtotime($period));
			
			$period = 		date("Y-m-d",strtotime("+1 month",strtotime($period)));

			$aResult[] = $t;
		}

		return $aResult;
	}
	
	public static function getLatestLedger($application_id){
		$result = mysql_query("
			select
				*
			from
				dprc_ledger as l, dprc_payment as p
			where
				l.dprc_payment_id = p.dprc_payment_id
			and
				application_id = '$application_id'
			order by
				period desc, or_date desc, or_no desc
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r;
	}
	
	public static function getNextDueDateFromPeriod($period,$date_due){
		$year = substr($period,0,4);
		$month = substr($period,4,5);
		
		#return "$year - $month";

		$last_day_of_month = lib::getLastDayOfMonth("$year-$month-01");
		#echo "$month $last_day_of_month  < $date_due <br>";
		if( $last_day_of_month < $date_due ){
			$date = date("Y-m-$date_due",strtotime( "+1 month" , strtotime(date("$year-$month-01"))));				
			/*echo "a";
			echo $date;*/
			return $date;
		} else {
			/*echo "b";*/
			if( $date_due > date("t",strtotime( "+1 month" , strtotime(date("$year-$month-01")))) ){
				$date = date("Y-m-t",strtotime( "+1 month" , strtotime(date("$year-$month-01"))));								
			} else {
				$date = date("Y-m-d",strtotime( "+1 month" , strtotime(date("$year-$month-$date_due"))));								
			}
						
			/*echo $date;*/
			return $date;
		}
		
		return date("Y-m-d",strtotime( "+1 month" , strtotime(date("$year-$month-$date_due"))));					
		
	}
	
	public static function periodPlusOne($period){
		$year = substr($period,0,4);
		$month = substr($period,4,5);
		
		#return "$year - $month";
		return date("Ym",strtotime( "+1 month" , strtotime(date("$year-$month-1"))));
	}
	public static function dueDatePlusOne($due_date){
		
		#return "$year - $month";
		return date("Y-m-d",strtotime( "+1 month" , strtotime($due_date)));
	}
}

#echo dprc::getDaysDiff("2012-12-31","2012-12-31");
?>