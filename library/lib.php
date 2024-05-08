<?php
class lib{
	public static function getAttribute($table,$search_column,$search_id,$column){
		$result = mysql_query("
			select
				*
			from
				$table
			where
				$search_column = '$search_id'
		") or die(mysql_error());
		
		$r =  mysql_fetch_assoc($result);
		return $r[$column];
	}
	
	public static function getTableAssoc($selectedid=NULL,$name='name',$label = NULL,$query,$id_column,$display_column,$aDisplay = NULL){
		$content="
			<select name='$name' id='$name'>
				<option value=''>$label:</option>
		";	
		
		$result=mysql_query($query);
		while($r=mysql_fetch_assoc($result)):
			$selected=($selectedid==$r[$id_column])?"selected='selected'":"";
			
			$display_content = $r[$display_column];
			if(!empty($aDisplay)){
				$display_content = "";
				foreach($aDisplay as $a){
					$display_content .="$r[$a] ";
				}
			}
			
			$content.="
				<option value='$r[$id_column]' $selected >".htmlentities(htmlspecialchars($display_content))."</option>
			";
		endwhile;
		
		$content.="
			</select>
		";
		
		return $content;
	}
	
	function getTableAssocEmp($selectedid=NULL,$name='name',$label = NULL,$query,$id_column,$display_column,$aDisplay = NULL){
			$content="
				<select name='$name' id='$name'>
					<option value=''>$label:</option>
			";

			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)){
				$selected=($selectedid==$r[$id_column])?"selected='selected'":"";

				$display_content = $r['employee_lname'].', '.$r['employee_fname'];

				$content.="
					<option value='$r[$id_column]' $selected >".addslashes(htmlentities($display_content))."</option>
				";
			}

			$content.="
				</select>
			";

			return $content;
		}
	
	public static function getArraySelect($selectedid=NULL,$name='name',$label = NULL, $array){
		$content="
			<select name='$name' id='$name'>
				<option value=''>$label:</option>
		";	
		
		foreach($array as $key => $value):
			$selected=($selectedid == $key)?"selected='selected'":"";	
			$content.="
				<option value='$key' $selected >".htmlspecialchars($value)."</option>
			";
		endforeach;
		
		$content.="
			</select>
		";
		
		return $content;
	}
	public static function inputText($a){
		$a['id'] = !empty($a['id']) ? $a['id'] : $a['name'];
		$a['keypress'] = !empty($a['keypress']) ? "if(event.keyCode==13){ jQuery('#$a[keypress]').focus(); return false; } " : "";
		$content = "
			<input type=\"text\" class=\"textbox $a[class] \" id=\"$a[id]\" name=\"$a[name]\" value=\"$a[value]\" 
			onkeypress=\"$a[keypress]\" ".(($a['readonly']) ?  "readonly='readonly'" : "")." ".((!$a['autocomplete']) ?  "autocomplete='off'" : "")." 
			".(($a['required']) ?  "required" : "")."
			 />
		";
		echo $content;		
	}
	
	public static function ymd2mdy($date){
		return date("m/d/Y",strtotime($date));
		
	}

	public static function getTableAttributes($sql){
		$result = mysql_query($sql) or die(mysql_error());
		return mysql_fetch_assoc($result);
	}

	function getTime($name,$end,$selected=NULL){
		$content = "
			<select name='$name' id = '$id'>
		";
		
		for($x = 0 ; $x <= $end ; $x++){
			$s = "";
			if($x == $selected){
				$s = "selected='selected'";	
			}
			$content .="
				<option $s>".str_pad($x,2,0,STR_PAD_LEFT)."</option>
			";
		}
		
		$content.="</select>";
		
		return $content;
	}	

	public static function getArrayDetails($sql){

		$result = mysql_query($sql) or die(mysql_error());
		$a = array();
		while( $r = mysql_fetch_assoc( $result ) ){
			$a[] = $r;		
		}
		return $a;
	}

	public static function isSunday($date){
		if(date("N",strtotime($date)) == '7'){
			return true;
		}else{
			return false;
		}
	}

	public static function getEmployeeName($employee_id){
		$sql  = "select concat(employee_lname,', ',employee_fname) as name from employee where employeeID = '$employee_id'";
		$arr = self::getTableAttributes($sql);	
		return $arr['name'];
	}

	public static function getUserFullName($userID){
		$sql  = "select concat(user_lname,', ',user_fname) as name from admin_access where userID = '$userID'";

		$arr = self::getTableAttributes($sql);
		return $arr['name'];
	}

	public static function now(){
		return date("Y-m-d H:i:s");
	}		
	public static function getFullDate($date){
		return date("F j, Y",strtotime($date));
	}		
}
?>