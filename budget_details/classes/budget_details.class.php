<?php

function add_details($id){
	$options = new bd_options();
	$objResponse = new xajaxResponse();
	
	//$objResponse->alert($id);
	
	$result=mysql_query("
		select
			*
		from
			budget_header b, projects p, work_category w
		where
			b.budget_header_id = '$id' AND w.work_category_id = b.work_category_id AND p.project_id = b.project_id
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);

	
	$budget_header_id = $r['budget_header_id'];
	$project_name = $r['project_name'];
	$work = $r['work'];	
	$project_id = $r['project_id'];
	$work_category_id = $r['work_category_id'];
	
	$content = "<br><img src='images/user_add.png'> Add Section Details<p>
					<form id=add_details action=javascript:void(null); onsubmit=xajax_save_details(xajax.getFormValues('add_details'));>
						<table class=form_table>
						  <tr>
						  	<td>Budget Number :</td>
						  	<td>
								".$r['budget_header_id']."
							</td>
						  </tr>
						  <tr>
						  	<td>Project Name :</td>
						  	<td>
								".$r['project_name']."
							</td>
						  </tr>
						  <tr>
						  	<td>Work Category :</td>
						  	<td>
								".$r['work']."
							</td>
						  </tr>
						  <tr>
						  	<td>Section Name : </td>
						  	<td>
								".$options->option_section_list('', $project_id, $work_category_id, 'section_id', 'Select Section')."
							</td>
						  </tr>
						   <tr>
						  	<td>Material : </td>
						  	<td>
								".$options->option_material_list('', $budget_header_id, 'stock_id', 'Select Material')."
							</td>
						  </tr>	
						  <tr>
						  	<td>Quantity Used : </td>
						  	<td>
								<input type=text name=qty_used class=textbox>								
							</td>
						  </tr>
						  <tr>
						  	<td>Description : </td>
						  	<td>
								<input type=text name=desc_name class=textbox>								
							</td>
						  </tr>						  
						  <tr>
						  	<td></td>
						  	<td>			
								<input type=hidden name=hId value='$id'>
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						</table>
					   </form><br>SECTION DETAILS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden;'></div>";	

	
	
	$objResponse->assign('Rdiv','innerHTML',$content);
	$objResponse->script("xajax_show_details('$id')");
	$objResponse->script("toggleBox('demodiv',0)");
	$objResponse->script("showBox()");
	
	return $objResponse;
}

function save_details($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new bd_options();
	
	if(!empty($form_data[desc_name])) {
			
			$date_exec = date("Y-m-d H:i:s");
			
			// Get budget details
			$get = "SELECT * FROM budget_detail b, productmaster m WHERE b.budget_header_id = '$form_data[hId]' AND b.stock_id = '$form_data[stock_id]' AND m.stock_id = b.stock_id";
			$rs_get = mysql_query($get);
			$rw_get = mysql_fetch_assoc($rs_get);
			$org_qty = $rw_get['quantity'];
			$stock = $rw_get['stock'];
			$unit = $rw_get['unit'];
			$budget_detail_id = $rw_get['budget_detail_id'];
			
					// Check qty
					$ch = "SELECT *, sum(qty_used) as t_qty_u FROM budget_section_detail s, budget_detail b
								WHERE s.budget_header_id = '$form_data[hId]' AND s.budget_detail_id = '$budget_detail_id' AND b.budget_header_id = '$form_data[hId]' 
									AND s.stock_id = b.stock_id AND s.stock_id = '$form_data[stock_id]'";
					$rs = mysql_query($ch);
					$numrow = mysql_num_rows($rs);
					if($numrow > 0)
					{
						while($rw = mysql_fetch_assoc($rs))
						{
							$total_qty_used = $rw['t_qty_u'];
							$qty_left = $org_qty - $total_qty_used;
						}
					}else{ $qty_left = $org_qty; }
					
			if($form_data[qty_used] > $qty_left)
			{
				$objResponse->alert("$stock has $qty_left $unit left. You entered $form_data[qty_used] and it is not enough!");
			}else{
				$sql = "insert into budget_section_detail set
					budget_detail_id='$budget_detail_id',
					budget_header_id='$form_data[hId]',
					section_id='$form_data[section_id]',
					stock_id='$form_data[stock_id]',
					qty_used='$form_data[qty_used]',
					description='$form_data[desc_name]',
					date_added='$date_exec'";
			
				$query = mysql_query($sql);
			}
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_details('$form_data[hId]')");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
			$objResponse->alert("Fill in all fields!");
		}
	
	return $objResponse;
}


function deleteD($sId, $id) {
		$objResponse = new xajaxResponse();
		
		$removeF = mysql_query("DELETE FROM budget_section_detail WHERE budget_section_detail_id='$sId'");
											
		$objResponse->script("xajax_show_details('$id')");
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}

function show_details($id) {
		$objResponse = new xajaxResponse();
		
		$get_details = mysql_query("select
											*
										from
											sections s, productmaster m, budget_section_detail d
										where
											d.budget_header_id='$id' AND d.section_id = s.section_id AND d.stock_id = m.stock_id AND d.is_deleted != '1'
									");
											
		while($rFunctions=mysql_fetch_array($get_details)) {
			$row.='<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;">
				   <img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteD(\''.$rFunctions[budget_section_detail_id].'\',\''.$id.'\');toggleBox(\'demodiv\',1);" title="Remove"> 
				   '.$rFunctions[section_name] . '&nbsp; | &nbsp;' . $rFunctions[stock] . '&nbsp; | &nbsp;' . $rFunctions[qty_used] . $rFunctions[unit] . '&nbsp; used.<br /><b>&nbsp;&nbsp;&nbsp; -- &nbsp;</b>'  . $rFunctions[description] . '</div>';
		}
		
		$content = $row;
		
		$objResponse->assign("funclistdiv","innerHTML", $content);
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}

?>