<?php
	/*
		Author: Michael Francis C. Catague, ECE, MIT and Michael Salvio,CpE 
	*/	

	class pc_options {
			
		/* Modified by Ron starts here */
		function option_employee_list($id=NULL,$name='employeeID',$label="No Employee List"){
			$query = "
				select
					*
				from
					employee
			";
			
			
			$query.="order by employee_lname asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$employeeID	= $r['employeeID'];
				$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
				
				$selected = ($id==$employeeID)?"selected='selected'":"";
				$content.="
					<option value='$employeeID' $selected >$employee</option>
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}
						
		/* Modified by Ron ends here */

	}
?>
