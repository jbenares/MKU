<?php

	function new_chartofaccountsform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$parent_select = $options->getTableAssoc($r['parent_gchart_id'],'parent_gchart_id','Select Parent Account',"select * from gchart order by gchart",'gchart_id','gchart');
		
		$newContent = "<br><img src='images/user_orange.png'> NEW CHART OF ACCOUNTS<p>
						<table class=form_table>	
						  <tr>
						  	<td>Account Code: </td>
						  	<td colspan='6'><input type='text' name='acode' class='textbox3'></td>
						  </tr>
						  <tr>
						  	<td>Parent Account: </td>
						  	<td colspan='6'>
								<input type='text' class='textbox gchart-ac' name='parent_gchart'>
								<input type='hidden' name='parent_gchart_id' value=''>								
							</td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td colspan='6'><input type='text' name='gchart' class='textbox2'></td>
						  </tr>
						  <tr>
						  	<td>Classification: </td>
						  	<td colspan='6'>".$options->getClassificationOptions()."</td>
						  </tr>
						  <tr>
						  	<td>Sub Classification: </td>
						  	<td colspan='6'>".$options->getTableAssoc('','sub_mclass','Select Sub Classification',"select * from sub_gchart where sub_gchart_void = '0' order by sub_gchart asc",'sub_gchart_id','sub_gchart')."</td>
						  </tr>
						  <tr>
						  	<td>Beginning Balance</td>
						  <tr/>
						  <tr>
							<td></td>
							<td>Debit:</td>
							<td><input type='text' class='textbox3' name='debit_beg'></td>	
							<td>Credit:</td>
							<td><input type='text' class='textbox3' name='credit_beg'></td>	
							<td>Year:</td>
							<td><input type='text' class='textbox3' name='year_bal' style='width: 40px;'></td>
						  </tr>
						  <tr>
						  	<td>Enable: </td>
						  	<td colspan='6'><input type='checkbox' name='enable' checked='checked' value='Yes'></td>
						  </tr>
						  <tr>
						  	<td></td>
						  	<td colspan='20'>						  	  
						  	  <input type=button name=b value='Submit' class=buttons onclick=xajax_new_chartofaccounts(xajax.getFormValues('dialog_form'));>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
				";	
		$newContent .= "
			<script type='text/javascript'>
				jQuery('.gchart-ac').autocomplete({
					source: 'autocomplete/gchart.php',
					minLength: 1,
					select: function(event, ui) {
						jQuery(this).val(ui.item.value);
						jQuery(this).next().val(ui.item.id);
					}
				});
			</script>
		";						   
		
		$objResponse->assign('dialog_content','innerHTML',$newContent);
		$objResponse->script("jQuery('#dialog').dialog('open');");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function new_chartofaccounts($form_data) {
		$objResponse = new xajaxResponse();
		
		if(	
			!empty($form_data[acode]) &&
			!empty($form_data[gchart]) &&
			!empty($form_data[mclass])
		)
		{
			$enable=($form_data[enable]=="Yes")?"Y":"N";
		
				
			$sql = "insert into 
						gchart 
					set
						acode='$form_data[acode]',
						scode='$form_data[scode]',
						gchart='$form_data[gchart]',
						mclass='$form_data[mclass]',
						sub_mclass='$form_data[sub_mclass]',
						enable='$enable',
						parent_gchart_id = '$form_data[parent_gchart_id]',
						beg_debit = '$form_data[debit_beg]',
						beg_credit = '$form_data[credit_beg]'
					";
			
			$query = mysql_query($sql);		
			
			$last_id = mysql_insert_id();
			
			//beginning history 
			if(!empty($form_data[year_bal])){
			mysql_query("Insert into gchart_beginning
						set	
						gchart_id = '$last_id', 
						year_bal = '$form_data[year_bal]',
						date = NOW(), 
						beg_debit = '$form_data[debit_beg]',
						beg_credit = '$form_data[credit_beg]'
						") or die (mysql_error());
			}
			
			if(!mysql_error()) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	
	function edit_chartofaccountsform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								gchart
							where
								gchart_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
	
		//$current_gchart = $r['gchart_id'];
		//		$sqlbb = mysql_query("Select beg_bal from gchart_beginning where gchart_id = '$current_gchart' order by date DESC limit 1") or die (mysql_error());
		//		$rbb = mysql_fetch_assoc($sqlbb);
		
		$enable=($r['enable']=="Y")?"checked='checked'":'';
		
		//$parent_select = $options->getTableAssoc($r['parent_gchart_id'],'parent_gchart_id','Select Parent Account',"select * from gchart order by gchart",'gchart_id','gchart');
		
		$newContent = "<br><img src='images/user_orange.png'> EDIT CHART OF ACCOUNTS<p>	
						<input type='hidden' name='gchart_id' value='$r[gchart_id]'>
						<table class=form_table>	
						  <tr>
						  	<td>Account Code: </td>
						  	<td colspan='6'><input type='text' name='acode' class='textbox3' value='$r[acode]' autcomplete='off' ></td>
						  </tr>
						  <tr>
						  	<td>Parent Account: </td>
						  	<td colspan='6'>
								<input type='text' id='gchart_ac' class='textbox gchart-ac' name='parent_gchart' value='".$options->getAttribute('gchart','gchart_id',$r['parent_gchart_id'],'gchart')."' onclick='this.select();' >
								<input type='hidden' name='parent_gchart_id' value='$r[parent_gchart_id]'>								
							</td>
						  </tr>
						  <tr>
						  	<td>Description: </td>
						  	<td colspan='6'><input type='text' name='gchart' class='textbox2' value='$r[gchart]'></td>
						  </tr>
						  <tr>
						  	<td>Classification: </td>
						  	<td colspan='6'>".$options->getClassificationOptions($r[mclass])."</td>
						  </tr>
						  <tr>
						  	<td>Sub Classification: </td>
						  	<td colspan='6'>".$options->getTableAssoc($r['sub_mclass'],'sub_mclass','Select Sub Classification',"select * from sub_gchart where sub_gchart_void = '0' order by sub_gchart asc",'sub_gchart_id','sub_gchart')."</td>
						  </tr>
						  <tr>
							<td>Beginning Balance:</td>
						  </tr>
						  <tr>		
							<td></td>
							<td>Debit:</td>
							<td><input type='text' class='textbox3' name='debit_beg' value='$r[beg_debit]' ></td>
							<td>Credit:</td>
							<td><input type='text' class='textbox3' name='credit_beg' value='$r[beg_credit]' ></td>
							<td>Year:</td>
							<td><input type='text' class='textbox3' name='year_bal' style='width: 40px;'></td>
						  </tr>
						  <tr>
						  	<td>Enable: </td>
						  	<td colspan='6'><input type='checkbox' name='enable' $enable value='Yes'></td>
						  </tr>
						  <tr>
						  	<td></td>
						  	<td colspan='6'>						  	  
						  	  <input type=button name=b value='Submit' class=buttons onclick=xajax_edit_chartofaccounts(xajax.getFormValues('dialog_form'));>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>
		";	
		$newContent .= "
			<script type='text/javascript'>
				jQuery('.gchart-ac').autocomplete({
					source: 'autocomplete/gchart.php',
					minLength: 1,
					select: function(event, ui) {
						jQuery(this).val(ui.item.value);
						jQuery(this).next().val(ui.item.id);
					}
				});
			</script>
		";		
		
		$objResponse->assign('dialog_content','innerHTML',$newContent);
		$objResponse->script("jQuery('#dialog').dialog('open');");
		$objResponse->script("toggleBox('demodiv',0)");	
				
		return $objResponse;}
	
	function edit_chartofaccounts($form_data) {
		
						
		$objResponse = new xajaxResponse();
		
	 	if(	
			!empty($form_data[acode]) &&
			!empty($form_data[gchart]) &&
			!empty($form_data[mclass])
		)
		{
		
			$enable=($form_data[enable]=="Yes")?"Y":"N";
			$parent=0;
			
			if(!empty($form_data[parent_gchart])) 
			{
				$parent = $form_data[parent_gchart_id];
			}

			$sql = "update
						gchart 
					set
						acode='$form_data[acode]',
						scode='$form_data[scode]',
						gchart='$form_data[gchart]',
						mclass='$form_data[mclass]',
						sub_mclass='$form_data[sub_mclass]',
						enable='$enable',
						parent_gchart_id = '$parent',
						beg_debit = '$form_data[debit_beg]',
						beg_credit = '$form_data[credit_beg]'
					where
						gchart_id='$form_data[gchart_id]'
				";
			
			//beginning history 
			if(!empty($form_data[year_bal])){
			mysql_query("Insert into gchart_beginning
						set	
						gchart_id = '$form_data[gchart_id]', 
						date = NOW(), 
						year_bal = '$form_data[year_bal]',
						beg_debit = '$form_data[debit_beg]',
						beg_credit = '$form_data[credit_beg]'
						") or die (mysql_error());
			}
			$query = mysql_query($sql);		
			
			
			if(!mysql_error()) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		}
		else {
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	
?>