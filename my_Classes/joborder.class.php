<?php
	function print_joborder($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printJO.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("joborder_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function updateJOStatus($id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			select
				*
			from 
				joborder_header
			where
				joborder_id='$id'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$status=$r[status];
		
		$audit=$options->getAuditOfJO($id);
		$audit+=" Printed by ".$options->getUserName($_SESSION[userID]);
		
		if($status!='C'):
			$query="
				update
					joborder_header
				set
					status='P',
					audit='$audit'
				where
					joborder_id='$id'
			";
			mysql_query($query);
		endif;
		
		return $objResponse;
	}
	
	function updateheader($actualoutput,$typeofpackage){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		//$objResponse->alert("$actualoutput, $typeofpackage");
		$qtypackagetype=$options->getPackageQty($typeofpackage);
		
		$packageqty=number_format($actualoutput/$qtypackagetype,3,'.','');
		$packageqty=intval($packageqty);
		$remainder=number_format($actualoutput%$qtypackagetype,3,'.','');
		//$remainder=$actualoutput-(intval($actualoutput/$qtypackagetype)*$qtypackagetype);
		$objResponse->assign('packageqty','value',$packageqty);
		$objResponse->assign('remaining','value',$remainder);
		return $objResponse;
	}
	
	function displayformulationonchange($formulation_id){
		$options=new options();
		$objResponse=new xajaxResponse();
		
		$query="
			select
				*
			from
				formulation_header
			where
				formulation_id='$formulation_id'
		";
		
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		// format formulationdate
		$date=explode("/",$r[formulationdate]);
		$formulationdate="$date[2]-$date[0]-$date[1]";	
		
		$content="
			<div class='inline'>
				<div>Formulation Code : </div>        
				<div>
					<input type='text' name='formulationcode' class='textbox3' value='$r[formulationcode]' readonly='readonly'>
				</div>
			</div>    	
			<div class='inline'>
				<div>Description : </div>        
				<div>
					<input type='text' name='description' class='textbox3' value='$r[description]' readonly='readonly'>
				</div>
			</div>    	
			<div class='inline'>
				<div>Formulation Date : </div>        
				<div>
					<input type=text class=textbox3 id='formulationdate' name=formulationdate value='$r[formulationdate]' readonly='readonly' >				
				</div>
			</div>    	
			<div class='inline'>
				<div>Category : </div>        
				<div>".
					$options->getAllCategoryOptions($r[category])
				."</div>
			</div>    
			
			<div class='inline'>
				<div>Customer Name : </div>        
				<div>".$options->getAccountOptions($r[customername],'customername_jodetail')."</div>
			</div>  
			
			<div class='inline'>
				<div>Finished Product : </div>        
				<div>".
					$options->getFinishedProductOptions($r[finishedproduct],'finishedproduct_jodetail')
				."</div>
			</div>
			
			<div class='inline'>
				<div>Price per Kilo : </div>        
				<div>
					<input type=text class='textbox3' id='pricePerKilo' value='".$options->getPricePerKiloFromFormulationId($formulation_id)."' name='priceperkilo' />
				</div>
			</div>
			
			<div class='inline'>
				<div>Total Quantity : </div>        
				<div>
					<input type=text id='totalqty' class='textbox3' value='".$options->getTotalQtyFromFormulationId($formulation_id)."' name='totalqty' />
				</div>
			</div>
			
			<div id='joborder_formulation_details'>
			
			</div>
		
		";
		
		$objResponse->assign('joborder_formulation','innerHTML',$content);
		$objResponse->script("xajax_displayformulationdetails('$formulation_id');");
		
		return $objResponse;
		
	}	
	function displayformulationdetails($formulation_id){
		$objResponse=new xajaxResponse();
		$options=new options();	
		$transac=new query();
	
		if(!empty($formulation_id)){
			$content.='
				<div style="float:left; width:50%; text-align:center;" >
				<div style="font-weight:bolder; font-size:15px; color:#5e6977; padding: 5px 2px;" align="left">Macro</div>
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table search_table_tr" id="Macro_table">	
			';
	
			$sql="
				SELECT
					formulation_details.type,
					formulation_details.material,
					formulation_details.quantity,
					formulation_details.remarks
				FROM
					formulation_header
				INNER JOIN formulation_details ON formulation_details.formulation_id = formulation_header.formulation_id
				where formulation_details.type='Macro'
				and formulation_header.formulation_id='$formulation_id'
	
			";
				
			$rs=mysql_query($sql);
			
			$content.='
				<tr bgcolor="#C0C0C0">				
				  <th width="20">#</th>
				  <th><b>Matrial</b></th>
				  <th><b>Type</b></th>
				  <th><b>Quantity</b></th>
   	  			  <th><b>Price</b></th>
				</tr>        
			';
					
			$macroQty=0;				
			while($r=mysql_fetch_assoc($rs)) {
				
				$cost=number_format(($options->getCostOfStock($r[material]) * $r[quantity]),2,'.','');
				
				$content.= '<tr>';
				$content.= '<td></td>';
				$content.= '<td>'.$options->getMaterialOptions($r[material],'formulationmaterial[]').'</td>';	
				$content.= '<td>'.$options->getFormulationTypeOptions($r[type],'formulationtype[]').'</td>';	
				$content.= '<td><div align="right"><input type="text" name="formulationqty[]" value="'.$r[quantity].'" style="width:100%;"></div></td>';
				$content.= '<td><div align="right"><input type="text" name="formulationcost[]" value="'.$cost.'" style="width:100%;"></div></td>';
				$content.= '</tr>';
				
				$macroQty+=$r['quantity'];
			}
		
			$content.='    
				</table>
				</div>
				<div style="float:right; width:50%; text-align:center;">
				<div style="font-weight:bolder; font-size:15px; color:#5e6977; padding: 5px 2px;" align="left">Micro</div>
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table search_table_tr"  id="Micro_table">
			';
				
		
			$sql="
				SELECT
				formulation_details.type,
				formulation_details.material,
				formulation_details.quantity,
				formulation_details.remarks
				FROM
				formulation_header
				INNER JOIN formulation_details ON formulation_details.formulation_id = formulation_header.formulation_id
				where formulation_details.type='Micro'
							and formulation_header.formulation_id='$formulation_id'
	
			";
			$rs=mysql_query($sql);
					
			$content.='
				<tr bgcolor="#C0C0C0"> 			
				  <th width="20">#</th>	
				  <th><b>Matrial</b></th>
				  <th><b>Type</b></th>
				  <th><b>Quantity</b></th>
				  <th><b>Cost</b></th>
		  
				</tr>   
			';     
						
			$microQty=0;
			$i=0;		
			while($r=mysql_fetch_assoc($rs)) {
				
				$cost=number_format(($options->getCostOfStock($r[material]) * $r[quantity]),2,'.','');
				
				$content.= '<tr>';
				$content.= '<td></td>';
				$content.= '<td>'.$options->getMaterialOptions($r[material],'formulationmaterial[]').'</td>';	
				$content.= '<td>'.$options->getFormulationTypeOptions($r[type],'formulationtype[]').'</td>';	
				$content.= '<td><div align="right"><input type="text" name="formulationqty[]" value="'.$r[quantity].'" style="width:100%;"></div></td>';
				$content.= '<td><div align="right"><input type="text" name="formulationcost[]" value="'.$cost.'" style="width:100%;"></div></td>';
				$content.= '</tr>';
				
				$totalMicroPrice+=$price;
				$microQty+=$r['quantity'];
			}
			$content.='   
				</table>
				</div>
			
			';	
		
		
		}else{
			$content="";	
		}
		
		$objResponse->assign('joborder_formulation_details','innerHTML',$content);
		$objResponse->script("numberitems();");
		return $objResponse;	
	}
	
	function jofinishedproductonchange($stock_id,$formulation_id=NULL,$status=NULL){
		$options=new options();
		$objResponse=new xajaxResponse();
		
		
		$query="
			select 
				*
			from 
				formulation_header
			where
				finishedproduct='$stock_id'
		";
		$result=mysql_query($query);
		
		$dd="
			<select name='formulation_id' onchange=xajax_displayformulationonchange(this.value); >
				<option value=''>Select Formulation</option>
		";		
		
		while($r=mysql_fetch_assoc($result)){
			if($formulation_id==$r['formulation_id']){
				$dd.="
					<option value='$r[formulation_id]' selected='selected'>$r[formulationcode] - $r[description]</option>
				";
			}else{
				$dd.="
					<option value='$r[formulation_id]'>$r[formulationcode] - $r[description]</option>
				";
			}
		}
		$dd.="
			</select>
		";
		
		if($status!='C'):
			$content="
				<div style='display:inline-block; margin-right:20px;'>
					<div>Formulation : </div>        
					<div>$dd</div>
				</div>   
				<input type='submit' name='b' value='Submit' />
			";
		endif;
		
		$objResponse->assign('formulationdiv','innerHTML',$content);
		return $objResponse;
	}
	
?>