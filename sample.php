<?php
	include_once("conf/ucs.conf.php");
	include_once("my_Classes/options.class.php");
	/*
	function new_stockcode($categ_id1){
		$options = new options();
		
		$result=mysql_query("
			select
				stock_id,
				stockcode
			from
				productmaster
			where
				categ_id1 = '$categ_id1'
			order by
				stockcode DESC
			limit 0,1
		") or die(mysql_error());
		
		$r=mysql_fetch_assoc($result);
		
		$last_stockcode = $r['stockcode'];
		
		echo "$last_stockcode - $r[stock_id] <br>";
		
		$category_code = $options->attr_Category($categ_id1,'category_code');
		$trimmed_category_code  = trim($category_code,0);
		
		echo "$trimmed_category_code<br>";
		
		$category_code_length = strlen($trimmed_category_code);
		
		$last_num = explode($trimmed_category_code,$last_stockcode);
		
		$pad_len = strlen($last_num[1]);
		
		$inc_last_num = $last_num[1] + 1;
		
		
		$new_pad = str_pad($inc_last_num,$pad_len,0,STR_PAD_LEFT);
		
		$new_stockcode = $trimmed_category_code.$new_pad;
	
		return $new_stockcode;
	}
	
	echo new_stockcode(3);
	*/
	/*
	$result=mysql_query("
		select
			*
		from
			gchart
	") or die(mysql_error());
	while($r=mysql_fetch_assoc($result)){
		$gchart_id = $r['gchart_id'];
		$sub_mclass = $r['sub_mclass'];	
		
		if($sub_mclass == "BCA" || $sub_mclass == "BLA" || $sub_mclass == "BFA" ){
			$mclass = "A";
		}else if($sub_mclass == "BCL" || $sub_mclass == "BLL" || $sub_mclass == "BOL" || $sub_mclass == "TAX" ){
			$mclass = "L";	
		}else if( $sub_mclass == "EOI" || $sub_mclass == "INC"  ){
			$mclass = "I";
		}else if($sub_mclass == "BEQ"){
			$mclass = "R";
		}else if($sub_mclass == "EXP" || $sub_mclass == "COG" ){
			$mclass = "E";
		}	
		
		mysql_query("
			update
				gchart
			set
				mclass = '$mclass'
			where
				gchart_id = '$gchart_id'
		") or die(mysql_error());
	}*/
	
	/*
	$c = 1;
	$result	= mysql_query("
		select	
			*
		from
			productmaster
		where
			categ_id1 = '3'
		order by
			stock asc		
	") or die(mysql_error());
	
	while($r = mysql_fetch_assoc($result)){
		$stock_id	= $r['stock_id'];
		$stock_code = "HARD".str_pad($c,4,0,STR_PAD_LEFT);
		$c++;
		mysql_query("
			update
				productmaster
			set
				stockcode = '$stock_code'
			where
				stock_id = '$stock_id'
		") or die(mysql_error());
	}*/
	
	#UPDATE GL ISSUANCE IN JV
	/*
	$result = mysql_query("
		select * from gltran_header where header = 'issuance_header_id'
	") or die(mysql_error());
	while($r = mysql_fetch_assoc($result)){
		$gltran_header_id = $r['gltran_header_id'];
		$account_id = $r['account_id'];
		$a = explode('-',$account_id);
		$project_id = $a[1];
		
		mysql_query("
			update gltran_detail set project_id = '$project_id' where gltran_header_id = '$gltran_header_id'
		") or die(mysql_error());
		
		set_time_limit(30);
	}*/
	
	
	#update cv amount
	$options = new options();
	$result = mysql_query("
		select * from cv_header
	") or die(mysql_error());
	
	while($r = mysql_fetch_assoc($result)){
		$cv_header_id = $r['cv_header_id'];
		$cash_amount = round($options->getCashAmount($cv_header_id),2);
		set_time_limit(30);
		mysql_query("
			update
				cv_header
			set
				cash_amount = '$cash_amount'
			where
				cv_header_id = '$cv_header_id'
		") or die(mysql_error());
	}
?>
