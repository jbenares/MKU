<?php
	
	class Formulation{
		
		function displayFormulationDetails($formulation_id)
		{
			$options=new options();
			
			$formulationdisplaytype=array("Macro","Micro");
			foreach($formulationdisplaytype as $key):
			
				$query="
					SELECT
						formulation_details.formulationdetail_id,
						formulation_details.formulation_id,
						formulation_details.type,
						formulation_details.material,
						formulation_details.quantity,
						formulation_details.remarks,
						productmaster.cost
						FROM
						formulation_details
						INNER JOIN productmaster ON formulation_details.material = productmaster.stock_id
						where 
						formulation_details.formulation_id='$formulation_id'
						and formulation_details.type='$key'
				";
							
				$result=mysql_query($query);
				$numberOfResults=mysql_num_rows($result);
				
				$newContent.="<input type=hidden name='numberOfResults' value='".$options->getTotalNumberOfResultsInFormulationId($formulation_id)."'>";
				
				$newContent.="<div style='";
					if($key=="Macro")
					{
						$newContent.='float:left;';
					}else{
						$newContent.='float:right;';
					}
				$newContent.=" width:48%; height:inherit; text-align:center; overflow:auto; margin-left:10px;'>";
				$newContent.="
					<div align=\"center\">
						<strong>$key</strong>
					</div>
				";
				$newContent.="<table cellspacing='0' cellpadding='0' width='50%' class='search_formulation_table'>
										<tr bgcolor='#C0C0C0'>
											<th width='20'></th>
											<th>Material</th>
											<th width='10'>Type</th>
											<th>Quantity</th>
											<th>Price</th>
											<th>Remarks</th>
										</tr>							
								";
				
				
				$counter=1;			
				$totalPrice=0;
				$totalQuantity=0;
				
				while($r=mysql_fetch_assoc($result))			
				{
		
					$price=number_format($r[cost]*$r[quantity],3,'.','');
					$newContent.="				
									<tr bgcolor='#FFFFFF'>
										<td width=15><img src='images/trash.gif' class='imagecursor' onclick=xajax_delete_formulationdetail('$r[formulationdetail_id]','$formulation_id');toggleBox('demodiv',1); />
										<input type='hidden' name='".$key."_formulationdetail_id$counter' value='$r[formulationdetail_id]'></td>
										<td>".$options->getMaterialOptions($r[material],$key.'_material'.$counter)."</td>
										<td>".$options->getFormulationTypeOptions($r[type],$key.'_type'.$counter)."</td>
										<td><div align='right'><input type='text' class='textbox4' name='".$key."_quantity$counter' value='$r[quantity]' style='border:none; width:70px; text-align:right;' ></div></td>
										<td><div align='right'>".$price."</div></td>
										<td><input type='text' name='".$key."_remarks$counter' value='$r[remarks]' style='border:none;' ></td>
									</tr>
								";
					$counter++;
					$totalPrice+=$price;
					$totalQuantity+=$r['quantity'];
				}
				$newContent.="	<tr>
									<td></td>
									<td></td>
									<td></td>
									<td><div align='right' style='color:#F00; font-weight:bold;'>".number_format($totalQuantity,3)."</div></td>
									<td><div align='right' style='color:#F00; font-weight:bold;'>".number_format($totalPrice,3)."</div></td>
									<td></td>
								</tr>
				
								
				";
				$newContent.=" 
								</table>";
				$newContent.="</div>";
			endforeach;
			
				$newContent.="<div style='clear:both; font-weight:bolder; color:#F00; text-align:right'>";
	
				$newContent.="<span style='color:#666;'><em>Price per Kilo:    </em></span>
								".$options->getPricePerKiloFromFormulationId($formulation_id);
	
				$newContent.="</div>";
			
				return $newContent;
		}
	}//End of Class
?>