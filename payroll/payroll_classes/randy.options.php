<?php

	class randy_options 
	{
		
		function projects_options($id) {
			$sql = "select * from projects order by project_name";
			$query = mysql_query($sql);		
			
			while($r=mysql_fetch_array($query)) {
				if($id==$r[project_id]) continue;
				
				$string_row.='<option value="'.$r[project_id].'">'.$r[project_name].'</option>';
			}
			
			if(!empty($id)) {
			  $sql = "select * from projects where project_id='$id'";	
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);
				
			  return '<select style="width:208px" name=projects class=select><option value="'.$result[project_id].'">'.$result[project_name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select style="width:208px"name=projects class=select><option value=0>SELECT PROJECT NAME</option>'.$string_row.'</select>';
		}
		
		function empStat_options($id) {
			$sql = "select * from employee_status order by employee_status";
			$query = mysql_query($sql);		
			
			while($r=mysql_fetch_array($query)) {
				if($id==$r[employee_statusID]) continue;
				
				$string_row.='<option value="'.$r[employee_statusID].'">'.$r[employee_status].'</option>';
			}
			
			if(!empty($id)) {
			  $sql = "select * from employee_status where employee_statusID='$id'";	
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);
				
			  return '<select style="width:208px" name=empStat class=select><option value="'.$result[employee_statusID].'">'.$result[employee_status].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select style="width:208px" name=empStat class=select><option value=0>PLEASE SELECT</option>'.$string_row.'</select>';
		}
		
		function day_status_options($id) {
			$sql = "select * from day_status order by dayStatID";
			$query = mysql_query($sql);		
			
			while($r=mysql_fetch_array($query)) {
				if($id==$r[dayStatID]) continue;
				
				$string_row.='<option value="'.$r[dayStatID].'">'.$r[dayStat].'</option>';
			}
			
			if(!empty($id)) {
			  $sql = "select * from day_status where dayStatID='$id'";	
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);
				
			  return '<select name=dayStatID class="select"><option value="'.$result[dayStatID].'">'.$result[dayStat].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=dayStatID class="select"><option value="">PLEASE SELECT</option>'.$string_row.'</select>';
		}

	
	}


?>