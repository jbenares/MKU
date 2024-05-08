<?php
	/*
		Author: Michael Francis C. Catague, ECE, MIT and Michael Salvio,CpE 
	*/	

	class ac_options {
			
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
		
		function option_project_list($id=NULL,$name='project_id',$label="No Project List"){
			$query = "
				select
					*
				from
					projects
			";
			
			
			$query.="order by project_name asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$project_id	= $r['project_id'];
				$project_name = $r['project_name'];
				
				$selected = ($id==$project_id)?"selected='selected'":"";
				$content.="
					<option value='$project_id' $selected >$project_name</option>
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}
		
		function option_project_list2($id=NULL,$name='project_id2',$label="No Project List"){
			$query = "
				select
					*
				from
					projects
			";
			
			
			$query.="order by project_name asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$project_id	= $r['project_id'];
				$project_name = $r['project_name'];
				
				$selected = ($id==$project_id)?"selected='selected'":"";
				$content.="
					<option value='$project_id' $selected >$project_name</option>
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}
		
		function option_stock_list($id=NULL,$name='stock_id',$label="No Item List"){
			$query = "
				select
					*
				from
					invadjust_header h, invadjust_detail d, productmaster p
				where
					h.project_id = '$id' AND h.invadjust_header_id = d.invadjust_header_id AND p.stock_id = d.stock_id
				group by d.stock_id
			";
			
			
			$query.="order by stock asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$stock_id	= $r['stock_id'];
				$project_id	= $r['project_id'];
				$stock_name = $r['stock'];
				$stock_qty = $r['quantity'];
				
				# Item from MRR
				$prj = "SELECT *, SUM(quantity) as totalqty FROM rr_header h, rr_detail d
							WHERE h.project_id = '$project_id' AND h.rr_header_id = d.rr_header_id AND d.stock_id = '$stock_id'";
				$rs_prj = mysql_query($prj);
				$rw_prj = mysql_fetch_assoc($rs_prj);
				$total_qty = $rw_prj['totalqty'];
				
				# Item IN
				$qin = "SELECT *, SUM(quantity) as totalqin FROM asset_circulation_header h, asset_circulation_detail d
							WHERE h.from_project_id = '$project_id' AND h.ach_id = d.ach_id AND d.stock_id = '$stock_id' AND d.status = 'I'";
				$rs_qin = mysql_query($qin);
				$rw_qin = mysql_fetch_assoc($rs_qin);
				$total_qin = $rw_qin['totalqin'];
				
				# Item OUT
				$out = "SELECT *, SUM(quantity) as totaldd FROM asset_circulation_header h, asset_circulation_detail d
							WHERE h.from_project_id = '$project_id' AND h.ach_id = d.ach_id AND d.stock_id = '$stock_id' AND d.status = 'O'";
				$rs_out = mysql_query($out);
				$rw_out = mysql_fetch_assoc($rs_out);
				$total_out = $rw_out['totaldd'];
				
				$total_dd = $total_qin - $total_out;
				
				$stocknq = $stock_name . ' - ' . (($total_qty + $stock_qty) - $total_dd) . 'qty';
				
				$qty_left = (($total_qty + $stock_qty) - $total_dd);
				if($qty_left == 0)
				{}else{
					$selected = ($id==$stock_id)?"selected='selected'":"";
					$content.="
						<option value='$stock_id' $selected >$stocknq</option>
					";
				}
			}
			$content.="
				</select>
			";
			
			return $content;
		}
		
		function option_stock_list2($id=NULL,$name='stock_id',$label="No Item List"){
			$query = "
				select
					*
				from
					productmaster p, asset_circulation_header h, asset_circulation_detail d
				where
					h.ach_id = d.ach_id AND d.stock_id = p.stock_id AND h.is_deleted != '1'
				group by d.stock_id
			";
			
			
			$query.="order by stock asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$stock_id	= $r['stock_id'];
				$project_id	= $r['project_id'];
				$stock_name = $r['stock'];
				$stock_qty = $r['quantity'];
				
			
					$selected = ($id==$stock_id)?"selected='selected'":"";
					$content.="
						<option value='$stock_id' $selected >$stock_name</option>
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
