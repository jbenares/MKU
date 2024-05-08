<?php
	/*
		Author: Michael Francis C. Catague, ECE, MIT and Michael Salvio,CpE 
	*/	

	class bd_options {
		
		
		
		function option_workcategory($id=NULL,$name='work_subcategory_id',$label="No Parent Category",$arg=array('level' => '1')){
			$query = "
				select
					*
				from
					work_category
			";
			
			if(!empty($arg)){
				$i=1;
				foreach($arg as $col => $val){
					if($i==1){
						$query.="
							where $col = '$val'
						";	
					}else{
						$query.="
							and $col = '$val'
						";	
					}
					$i++;
				}
			}
			$query.="order by work asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$work_category_id	= $r['work_category_id'];
				$work				= $r['work'];
				
				$selected = ($id==$work_category_id)?"selected='selected'":"";
				$content.="
					<option value='$work_category_id' $selected >$work</option>
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}
		
		/* Modified by Ron starts here */
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
		
		
		function option_workcategory_list($id=NULL,$name='work_category_id',$label="No Parent Category",$arg=array('level' => '1')){
			$query = "
				select
					*
				from
					work_category
			";
			
			if(!empty($arg)){
				$i=1;
				foreach($arg as $col => $val){
					if($i==1){
						$query.="
							where $col = '$val'
						";	
					}else{
						$query.="
							and $col = '$val'
						";	
					}
					$i++;
				}
			}
			$query.="order by work asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$work_category_id	= $r['work_category_id'];
				$work				= $r['work'];
				
				$selected = ($id==$work_category_id)?"selected='selected'":"";
				$content.="
					<option value='$work_category_id' $selected >$work</option>
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}
		
		
		function option_section_list($id=NULL,$project_id, $work_category_id, $name='section_id',$label="No Section List"){
			$query = "
				select
					*
				from
					sections
				where
					project_id = '$project_id' AND work_category_id = '$work_category_id'
															
			";
			
			
			$query.="order by section_name asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$section_id	= $r['section_id'];
				$section_name = $r['section_name'];
				
				$selected = ($id==$section_id)?"selected='selected'":"";
				$content.="
					<option value='$section_id' $selected >$section_name</option>
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}		
		
		
		function option_material_list($id=NULL, $budget_header_id, $name='stock_id', $label="No Material List"){
																			
			$query = "
				select
					*
				from
					budget_detail b, productmaster m
				where
					b.budget_header_id = '$budget_header_id' AND m.stock_id = b.stock_id
															
			";
			
			$query.="order by m.stock asc";
			
			$result=mysql_query($query) or die(mysql_error());
								
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$stock_id	= $r['stock_id'];
				$stock = htmlentities($r['stock']);
				$quantity = $r['quantity'];
				$unit = $r['unit'];
					
					$ch = "SELECT *, sum(s.qty_used) as t_qty_u FROM budget_section_detail s, budget_detail b
								WHERE s.budget_header_id = '$budget_header_id'
									AND s.stock_id = b.stock_id AND s.stock_id = '$stock_id' AND s.budget_detail_id = b.budget_detail_id
										";
					$rss = mysql_query($ch);
					$numrow = mysql_num_rows($rss);
					if($numrow > 0)
					{
						while($rw = mysql_fetch_assoc($rss))
						{
							$total_qty_used = $rw['t_qty_u'];
							$qty_left = $quantity - $total_qty_used;
						}
					}else{ 
						$qty_left = $quantity; 
					}
					
				
				$selected = ($id==$stock_id)? "selected='selected'":"";
				$content.="
					<option value='$stock_id' $selected >$stock &nbsp;|&nbsp; $qty_left $unit left</option>
				
				";
			}
			$content.="
				</select>
			";
			
			return $content;
		}
		
		function option_test(){
			
			$content="
				<select name='test' id='test' class='select' onchange='test();'>
					
			";
			
				$content.="
					<option value='0' disabled selected >-- Choose --</option>
					<option value='1'>Room 1A</option>
					<option value='2'>Room 2A</option>
					<option value='3'>Room 3A</option>
				";
			
			$content.="
				</select>
			";
			
			return $content;
		}
		
		/* added function for labor budget PO (03/08/2014) */
		function option_pr($id=NULL,$name='pr_header_id',$label="No Parent Category"){
				$query = "
						select
							*
						from
							pr_header
						where
							type='labor'
						AND
							(status ='S' or status='F')
						AND
							is_used !='1'
					";
			$query.="order by pr_header_id asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
					$pr_header_id	= $r['pr_header_id'];
				$c = "
					select 
						*
					from
						labor_budget_pr
					where
						pr_header_id = '$pr_header_id'
					";
				$l = mysql_query($c);
				if(mysql_num_rows($l)>0){
					$description	= $r['description'];
					$selected = ($id==$pr_header_id)?"selected='selected'":"";
					$content.="
						<option value='$pr_header_id' $selected >$description - $pr_header_id</option>
					";
				}else{	}				
			}
			$content.="
				</select>
			";
			return $content;
		}
		
		function option_pr2($name='pr_header_id',$label="No Parent Category"){
			$query = "
						select
							*
						from
							pr_header
						where
							type='labor'
						AND
							approval_status='A'
						AND
							(status ='S' or status='F')
						AND
							is_used !='1'
					";
			$query.="order by pr_header_id asc";
			
			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
					$pr_header_id	= $r['pr_header_id'];
				$c = "
					select 
						*
					from
						labor_budget_pr
					where
						pr_header_id = '$pr_header_id'
					";
				$l = mysql_query($c);
				if(mysql_num_rows($l)>0){
					$description	= $r['description'];
					$selected = ($id==$pr_header_id)?"selected='selected'":"";
					$content.="
						<option value='$pr_header_id' $selected >$description - $pr_header_id</option>
					";
				}else{	}				
			}
			$content.="
				</select>
			";
			return $content;
		}
		
		/* Modified by Ron ends here */

	}
?>
