<?php
class Depreciation{
	
	public static function getMonthsDiff($date1,$date2){
		
		$date2 = new DateTime($date2);
		$date1 = new DateTime($date1);
	
		$interval = date_diff($date2,$date1);
		$months = $interval->format("%m");
		$years = $interval->format("%y");
		
		$total_months = $months + ($years * 12);
		return $total_months;
	}
	public static function getStartofDepreciation($date){
		$month 	= date("m",strtotime($date));	
		$day	= date("d",strtotime($date));	
		$year 	= date("Y",strtotime($date));	
		
		if($day <= 15){
			$new_date = "$year-$month-01";
		}else{
			$new_date = date("Y-m-d",strtotime("+1 month",strtotime("$year-$month-1")));
		}
		return $new_date;
	}
	
	public static function getAsOfDate($date){
		$month 	= date("m",strtotime($date));	
		$day	= date("d",strtotime($date));	
		$year 	= date("Y",strtotime($date));	
		
		$new_date = date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-1")));
		return $new_date;
	}
	
	public static function getAccumulatedDepreciation($date_acquired,$as_of_date,$acquisition_cost,$estimated_life){
		$date_acquired 	= self::getStartofDepreciation($date_acquired);
		$as_of_date		= self::getAsOfDate($as_of_date);
		
		$months_diff 	= self::getMonthsDiff($date_acquired,$as_of_date);
		
		return $months_diff * self::getMonthlyDepreciation($acquisition_cost,$estimated_life);
	}
	
	public static function computeMonthlyLapsingSchedule($date_acquired,$as_of_date,$acquisition_cost,$estimated_life){
		$date_acquired 	= self::getStartofDepreciation($date_acquired);
		$as_of_date		= self::getAsOfDate($as_of_date);
		
		$months_diff 	= self::getMonthsDiff($date_acquired,$as_of_date);
		
		$monthly_dep  	= self::getMonthlyDepreciation($acquisition_cost,$estimated_life);
		
		$r = array();
		
		$a = $acquisition_cost;
		for($i = 1; $i <= $months_diff ; $i++){
			
			$a = $a - $monthly_dep;
			
			$temp = array();
			$temp['date'] = date("Y-m-d",strtotime("+$i month",strtotime($date_acquired)));
			$temp['monthly_dep'] = $monthly_dep;
			$temp['net_book_value'] = $a;
			
			$r[] = $temp;
		}
		
		return $r;
	}
	
	public static function getNetBookValue($date_acquired,$as_of_date,$acquisition_cost,$estimated_life){
		$date_acquired 	= self::getStartofDepreciation($date_acquired);
		$as_of_date		= self::getAsOfDate($as_of_date);
		
		$months_diff 	= self::getMonthsDiff($date_acquired,$as_of_date);
		
		$accu_depreciation =  self::getAccumulatedDepreciation($date_acquired,$as_of_date,$acquisition_cost,$estimated_life);
		
		$net_book_value = $acquisition_cost - $accu_depreciation;
		return ($net_book_value > 0) ? $net_book_value : 0;
	}
	
	public static function getMonthlyDepreciation($acquisition_cost,$estimated_life){
		return (!empty($estimated_life)) ? $acquisition_cost / $estimated_life : 0;
	}
}
?>