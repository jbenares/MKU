<?php

	function new_productmasterform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		/*
		$newContent = "<br><img src='images/user_orange.png'> NEW PRODUCT MASTER<p>
					<form enctype='multipart/form-data' id=newareaform action=javascript:void(null); onsubmit=xajax_new_productmaster(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Stock Code: </td>
						  	<td>
								<input type=text name=stockcode class='textbox' >
							</td>
							<td>Reorder Level: </td>
						  	<td><input type=text name=reorderlevel class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Bar Code: </td>
						  	<td><input type=text name=barcode class=textbox></td>
						  	<td>Reorder Quantity: </td>
						  	<td><input type=text name=reorderqty class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Stock: </td>
						  	<td><input type=text name=stock class=textbox></td>
						  	<td>Supplier ID: </td>
						  	<td>".$options->getSupplierOptions()."</td>
						  
						  </tr>
						  <tr>
						  	<td>Type: </td>
						  	<td>".$options->getTypeOptions()."</td>
						  	<td>Many Suppliers: </td>
						  	<td><input type=checkbox name=manysuppliers></td>
						  
						  </tr>
						  <tr>
						  	<td>Category: </td>
						  	<td>
							".
								$options->getCategoryOptions()							
							."
							</td>
						  	<td>Pic Name: </td>
						  	<td><input type=text name=picname class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Unit: </td>
						  	<td><input type=text name=unit class=textbox></td>
						  	<td>Pic Location: </td>
						  	<td><input type=text name=piclocate class=textbox></td>
						  </tr>
						   <tr>
						  	<td>Price: </td>
						  	<td>
								<img src='images/edit.gif'  class='pointer' onClick=\"toggleBox('myDiv',1)\" alt='Price' title=\"Show Prices\" />
								<div style=\"position:relative;\">
									<div id='myDiv' style='bottom:-120px; left:10px;'>
										<img id='close' src='images/close.gif' onClick=\"toggleBox('myDiv',0)\" alt='Close' title=\"Close this window\" />
										<div id=\"myDivContent\">
											<table>
												<tr>
													<td>Price 1 : </td>
													<td><input type=textbox name=price1 class='textbox'></td>
												</tr>
												<tr>
													<td>Price 2 : </td>
													<td><input type=textbox name=price2 class='textbox'></td>
												</tr>
												<tr>
													<td>Price 3: </td>
													<td><input type=textbox name=price3 class='textbox' ></td>
												</tr>
												<tr>
													<td>Price 4 : </td>
													<td><input type=textbox name=price4 class='textbox' ></td>
												</tr>			
												<tr>
													<td>Price 5 : </td>
													<td><input type=textbox name=price5 class='textbox'></td>
												</tr>
												<tr>
													<td>Price 6 : </td>
													<td><input type=textbox name=price6 class='textbox' ></td>
												</tr>			
												<tr>
													<td>Price 7 : </td>
													<td><input type=textbox name=price7 class='textbox'></td>
												</tr>
												<tr>
													<td>Price 8 : </td>
													<td><input type=textbox name=price8 class='textbox'></td>
												</tr>			
												<tr>
													<td>Price 9 : </td>
													<td><input type=textbox name=price9 class='textbox' ></td>
												</tr>
												<tr>
													<td>Price 10 : </td>
													<td><input type=textbox name=price10 class='textbox' ></td>
												</tr>														
											</table>
										</div>
									</div>
								</div>
							</td>
						  	<td>Status: </td>
						  	<td>".$options->getStatusOptions()."</td>

						  </tr>
						  <tr>
							<td>Cost: </td>
							<td><input type=textbox class=textbox name=cost ></td>
						  </tr>
						   <tr>
						  	<td>Describe: </td>
						  	<td><textarea name='description' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'></textarea></td>
						  </tr>
						  <tr>
						  	<td>Audit: </td>
						  	<td><textarea name='audit' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'></textarea></td>
						  </tr>
						  <tr>
						  	<td></td>
							<td></td>
							<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";							   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		*/
		$objResponse->redirect("admin.php?view=a09dc2e7caa66cf3d3ec");
		
		return $objResponse;
	}
	
	function new_productmaster($form_data) {
		$objResponse = new xajaxResponse();
		
		if( !empty($form_data[stock])){
		
			$audit="Last added by ".$_SESSION[user_lname].', '.$_SESSION[user_fname];
			
			if($form_data[manysuppliers]=='on')
			{
				$manysuppliers='1';
			}
			else
			{
				$manusuppliers='0';
			}
				
			$sql = "insert into productmaster set
						stockcode='$form_data[stockcode]',
						barcode='$form_data[barcode]',
						stock='$form_data[stock]',
						type='$form_data[type]',
						categ_id1='$form_data[categ_id1]',
						categ_id2='$form_data[categ_id2]',
						categ_id3='$form_data[categ_id3]',
						categ_id4='$form_data[categ_id4]',
						unit='$form_data[unit]',
						cost='$form_data[cost]',
						price1='$form_data[price1]',
						price2='$form_data[price2]',
						price3='$form_data[price3]',
						price4='$form_data[price4]',
						price5='$form_data[price5]',
						price6='$form_data[price6]',
						price7='$form_data[price7]',
						price8='$form_data[price8]',
						price9='$form_data[price9]',
						price10='$form_data[price10]',
						reorderlevel='$form_data[reorderlevel]',
						reorderqty='$form_data[reorderqty]',
						supplier_id='$form_data[supplier_id]',
						manysuppliers='$manysuppliers',
						picname='$form_data[picname]',
						piclocate='$form_data[piclocate]',
						status='$form_data[status]',
						audit='$audit',
						description='$form_data[description]'";
			
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
	
	function edit_productmasterform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
				
		$sql = mysql_query("select
								*
							from
								productmaster
							where
								stock_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> EDIT PRODUCT MASTER<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_productmaster(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Stock Code: </td>
						  	<td>
								<input type=text name=stockcode class='textbox' value='$r[stockcode]' >
								<input type=hidden name=stock_id value='$r[stock_id]' >
							</td>
							<td>Reorder Level: </td>
						  	<td><input type=text name=reorderlevel class=textbox value='$r[reorderlevel]'></td>
						  </tr>
						  <tr>
						  	<td>Bar Code: </td>
						  	<td><input type=text name=barcode class=textbox value='$r[barcode]' ></td>
						  	<td>Reorder Quantity: </td>
						  	<td><input type=text name=reorderqty class=textbox value='$r[reorderqty]'></td>
						  </tr>
						  <tr>
						  	<td>Stock: </td>
						  	<td><input type=text name=stock class=textbox value='$r[stock]' ></td>
						  	<td>Supplier ID: </td>
						  	<td>".$options->getSupplierOptionsEdit($r[supplier_id])."</td>
						  </tr>
						  <tr>
						  	<td>Type: </td>
						  	<td>".$options->getTypeOptions($r[type])."</td>
						  	<td>Many Suppliers: </td>
						  	<td>";
								if($r[manysuppliers]=='1')
								{
									$newContent.="<input type=checkbox name=manysuppliers checked=checked >";
								}
								else
								{
									$newContent.="<input type=checkbox name=manysuppliers>";
								}
							
					$newContent.="
						  	</td>
						  </tr>
						  <tr>
						  	<td>Category: </td>
						  	<td>
							".
								$options->getCategoryOptionsEdit($r[categ_id1],$r[categ_id2],$r[categ_id3],$r[categ_id4])							
							."
							</td>
						  	<td>Pic Name: </td>
						  	<td><input type=text name=picname class=textbox value='$r[picname]'></td>
						  </tr>
						  <tr>
						  	<td>Unit: </td>
						  	<td><input type=text name=unit class=textbox value='$r[unit]' ></td>
						  	<td>Pic Location: </td>
						  	<td><input type=text name=piclocate class=textbox value='$r[piclocate]' ></td>
						  </tr>
						   <tr>
						  	<td>Price: </td>
						  	<td>
								<img src='images/edit.gif'  class='pointer' onClick=\"toggleBox('myDiv',1)\" alt='Price' title=\"Show Prices\" />
								<div style=\"position:relative;\">
									<div id='myDiv' style='bottom:-120px; left:10px;'>
										<img id='close' src='images/close.gif' onClick=\"toggleBox('myDiv',0)\" alt='Close' title=\"Close this window\" />
										<div id=\"myDivContent\">
											<table>
												<tr>
													<td>Price 1 : </td>
													<td><input type=textbox name=price1 class='textbox' value='$r[price1]'></td>
												</tr>
												<tr>
													<td>Price 2 : </td>
													<td><input type=textbox name=price2 class='textbox' value='$r[price2]'></td>
												</tr>
												<tr>
													<td>Price 3: </td>
													<td><input type=textbox name=price3 class='textbox' value='$r[price3]' ></td>
												</tr>
												<tr>
													<td>Price 4 : </td>
													<td><input type=textbox name=price4 class='textbox' value='$r[price4]' ></td>
												</tr>			
												<tr>
													<td>Price 5 : </td>
													<td><input type=textbox name=price5 class='textbox' value='$r[price5]' ></td>
												</tr>
												<tr>
													<td>Price 6 : </td>
													<td><input type=textbox name=price6 class='textbox' value='$r[price6]'></td>
												</tr>			
												<tr>
													<td>Price 7 : </td>
													<td><input type=textbox name=price7 class='textbox' value='$r[price7]'></td>
												</tr>
												<tr>
													<td>Price 8 : </td>
													<td><input type=textbox name=price8 class='textbox' value='$r[price8]'></td>
												</tr>			
												<tr>
													<td>Price 9 : </td>
													<td><input type=textbox name=price9 class='textbox' value='$r[price9]'></td>
												</tr>
												<tr>
													<td>Price 10 : </td>
													<td><input type=textbox name=price10 class='textbox' value='$r[price10]'></td>
												</tr>														
											</table>
										</div>
									</div>
								</div>
							</td>
						  	<td>Status: </td>
						  	<td>".$options->getStatusOptionsEdit($r[status])."</td>

						  </tr>
						  <tr>
							<td>Cost: </td>
							<td><input type=textbox class=textbox name=cost value='".number_format($r[cost],3,'.','')."'></td>
						  </tr>
						   <tr>
						  	<td>Describe: </td>
						  	<td><textarea name='description' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'>$r[description]</textarea></td>
						  </tr>
						  <tr>
						  	<td>Audit: </td>
						  	<td><textarea name='audit' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'>$r[audit]</textarea></td>
						  </tr>
						  <tr>
						  	<td></td>
							<td></td>
							<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";					   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;}
	
	function edit_productmaster($form_data) {
		$objResponse = new xajaxResponse();
		$options=new options();
		
		if( !empty($form_data[stock]))
		{
		   
			if($form_data[manysuppliers]=='on')
			{
				$manysuppliers='1';
			}
			else
			{
				$manusuppliers='0';
			}
			
			$audit=$options->getAuditOfProductMaster($form_data[stock_id]);
			
			$audit+=" Updated by ".$options->getUserName($_SESSION[userID]);
				
			$sql = "update productmaster set
						stockcode='$form_data[stockcode]',
						barcode='$form_data[barcode]',
						stock='$form_data[stock]',
						type='$form_data[type]',
						categ_id1='$form_data[categ_id1]',
						categ_id2='$form_data[categ_id2]',
						categ_id3='$form_data[categ_id3]',
						categ_id4='$form_data[categ_id4]',
						unit='$form_data[unit]',
						cost='$form_data[cost]',
						price1='$form_data[price1]',
						price2='$form_data[price2]',
						price3='$form_data[price3]',
						price4='$form_data[price4]',
						price5='$form_data[price5]',
						price6='$form_data[price6]',
						price7='$form_data[price7]',
						price8='$form_data[price8]',
						price9='$form_data[price9]',
						price10='$form_data[price10]',
						reorderlevel='$form_data[reorderlevel]',
						reorderqty='$form_data[reorderqty]',
						supplier_id='$form_data[supplier_id]',
						manysuppliers='$manysuppliers',
						picname='$form_data[picname]',
						piclocate='$form_data[piclocate]',
						status='$form_data[status]',
						audit='$audit',
						description='$form_data[description]'
					where
						stock_id='$form_data[stock_id]'";
			
			$query = mysql_query($sql);
			
			
			
			if($query) {
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
	
	function getCategory2Options($form_data)
	{
		$subcateg_id=$form_data[categ_id1];
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="select * from categories where level='2' and subcateg_id='$subcateg_id'";
		$result=mysql_query($query);
		
		$content="
			<option value=''></option>";
			
		while($row=mysql_fetch_assoc($result))
		{
			$content.="
				<option value='$row[categ_id]'>$row[category]</option>";				
		}
		
		
		$objResponse->assign('categ_id2','innerHTML',$content);	
		$objResponse->assign('categ_id3','innerHTML','');
		$objResponse->assign('categ_id4','innerHTML','');	
		
		
		return $objResponse;
	}
	
	function getCategory3Options($form_data)
	{
		$subcateg_id=$form_data[categ_id2];
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="select * from categories where level='3' and subcateg_id='$subcateg_id'";
		$result=mysql_query($query);
		
		$content="
			<option value=''></option>";
						
		while($row=mysql_fetch_assoc($result))
		{
			$content.="
				<option value='$row[categ_id]'>$row[category]</option>";				
		}
					
		$objResponse->assign('categ_id3','innerHTML',$content);	
		$objResponse->assign('categ_id4','innerHTML','');			
		
		return $objResponse;
	}
	
	function getCategory4Options($form_data)
	{
		$subcateg_id=$form_data[categ_id3];
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="select * from categories where level='4' and subcateg_id='$subcateg_id'";
		$result=mysql_query($query);
		
		$content="
			<option value=''></option>";
						
		while($row=mysql_fetch_assoc($result))
		{
			$content.="
				<option value='$row[categ_id]'>$row[category]</option>";				
		}
					
		$objResponse->assign('categ_id4','innerHTML',$content);	
		
		
		return $objResponse;
	}
	
	function editFormulationForm($formulation_header_id){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		
		$result=mysql_query("
			select
				*
			from
				formulation_header
			where
				formulation_header_id='$formulation_header_id'
		") or $objResponse->alert(mysql_error());
		
		$r=mysql_fetch_assoc($result);
		
		$date					= $r['date'];
		$formulation_header_id	= $r['formulation_header_id'];
		$formulation_header_id_pad		= ($formulation_header_id)?str_pad($formulation_header_id,7,"0",STR_PAD_LEFT):"";
			
		$formulation_code			= $r['formulation_code'];
		$description				= $r['description'];
		$date_created				= $r['date_created'];
		$date_updated				= $r['date_updated'];
		$output						= $r['output'];
		$product_id					= $r['product_id'];
		$product_name				= $options->getMaterialName($product_id);
	
		$user_id					= $r['user_id'];
		$status						= $r['status'];
		
		$content="
			<div class='module_actions'>
				<input type='hidden' name='formulation_id' id='formulation_id' value='$formulation_header_id' >
				
				<div class='inline'>
					Formulation Code : <br />
					<input type='text' class='textbox3' name='formulation_code' value='$formulation_code' />
				</div>
				
				<div class='inline'>
					Item : <br />
					<input type='text' class='textbox' name='product_name' id='product_name' value='$product_name' readonly='readonly' />
					<input type='hidden' name='product_id' id='product_id' value='$product_id' />
				</div>
				
				<div class='inline'>
					Description : <br />
					<input type='text' class='textbox2' name='description' value='$description' />
				</div>
				<br />
				
				<div class='inline'>
					Output : <br />
					<input type='text' class='textbox3' name='output' value='$output' />
				</div>
			</div>
			<div class='module_actions'>
				<input type='button' value='Update' id='dialog_update' onclick=\"xajax_updateFormulationHeaderPM(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form'));\">
			</div>
			
        	<div class='module_actions' id='formulation_detail_content'>
				<div class='inline'>
					Item : <br />
					<input type='text' class='textbox' name='stock_name' id='stock_name' value='' />
					<input type='hidden' name='stock_id' id='stock_id'  />
				</div>    
				<div class='inline'>
					<div>Quantity : </div>        
					<div><input type='text' size='20' name='quantity' id='quantity' class='textbox3' /></div>
				</div> 
				
				<input type='button' id='addButton' value='Add' onclick='xajax_addFormulationDetailPM(xajax.getFormValues(\"dialog_form\"));' />
			</div>
			<div id='dialog_table_container'>
		    </div>
			
			<script type='text/javascript'>
				j(\"input:button\").button();
				
				j(\"#stock_name\").autocomplete({
				source: \"stocks.php\",
				minLength: 2,
				select: function(event, ui) {
					j(\"#stock_name\").val(ui.item.value);
					j(\"#stock_id\").val(ui.item.id);
				}
			});
			
			j(\"#main_name\").autocomplete({
				source: \"stocks.php\",
				minLength: 2,
				select: function(event, ui) {
					j(\"#main_name\").val(ui.item.value);
					j(\"#main_id\").val(ui.item.id);
				}
			});
			
			j(\"#product_name\").autocomplete({
				source: \"stocks.php\",
				minLength: 2,
				select: function(event, ui) {
					j(\"#product_name\").val(ui.item.value);
					j(\"#product_id\").val(ui.item.id);
				}
			});
			</script>
		";
		
		$objResponse->script("xajax_getDialogFormulationTable('$formulation_header_id')");
		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script('openDialog();');
			
		return $objResponse;
	}
	
	
	function addFormulationForm($form_data){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		
		$stock_id 	= $form_data['stock_id'];
		$stock		= $options->getMaterialName($stock_id);
		
		$content="
			<div class='module_actions'>
				<input type='hidden' name='formulation_id' id='formulation_id' >
				
				<div class='inline'>
					Formulation Code : <br />
					<input type='text' class='textbox3' name='formulation_code' value='' />
				</div>
				
				<div class='inline'>
					Item : <br />
					<input type='text' class='textbox' name='product_name' id='product_name' value='$stock' readonly='readonly' />
					<input type='hidden' name='product_id' id='product_id' value='$stock_id' />
				</div>
				
				<div class='inline'>
					Description : <br />
					<input type='text' class='textbox2' name='description' value='' />
				</div>
				<br />
				
				<div class='inline'>
					Output : <br />
					<input type='text' class='textbox3' name='output' value='' />
				</div>
			</div>
			<div class='module_actions'>
				<input type='button' value='Submit' id='dialog_submit' onclick=\"xajax_addFormulationHeaderPM(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form'));\">
				<input type='button' value='Update' id='dialog_update' onclick=\"xajax_updateFormulationHeaderPM(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form'));\" style=\"display:none;\">
			</div>
			
        	<div class='module_actions' id='formulation_detail_content' style='display:none;'>
				<div class='inline'>
					Item : <br />
					<input type='text' class='textbox' name='stock_name' id='stock_name'  />
					<input type='hidden' name='stock_id' id='stock_id'  />
				</div>    
				<div class='inline'>
					<div>Quantity : </div>        
					<div><input type='text' size='20' name='quantity' id='quantity' class='textbox3' /></div>
				</div> 
				
				<input type='button' id='addButton' value='Add' onclick='xajax_addFormulationDetailPM(xajax.getFormValues(\"dialog_form\"));' />
			</div>
			<div id='dialog_table_container'>
		    </div>
			
			<script type='text/javascript'>
				j(\"input:button\").button();
				
				j(\"#stock_name\").autocomplete({
				source: \"stocks.php\",
				minLength: 2,
				select: function(event, ui) {
					j(\"#stock_name\").val(ui.item.value);
					j(\"#stock_id\").val(ui.item.id);
				}
			});
			
			j(\"#main_name\").autocomplete({
				source: \"stocks.php\",
				minLength: 2,
				select: function(event, ui) {
					j(\"#main_name\").val(ui.item.value);
					j(\"#main_id\").val(ui.item.id);
				}
			});
			
			j(\"#product_name\").autocomplete({
				source: \"stocks.php\",
				minLength: 2,
				select: function(event, ui) {
					j(\"#product_name\").val(ui.item.value);
					j(\"#product_id\").val(ui.item.id);
				}
			});
			</script>
		";
		
		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script('openDialog();');
			
		return $objResponse;
	}
	
	function addFormulationDetailPM($form_data){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		
		$formulation_header_id		= $form_data['formulation_id'];
		$stock_id					= $form_data['stock_id'];
		$quantity					= $form_data['quantity'];
		
		
		$query="
			insert into
				formulation_details
			set
				formulation_header_id='$formulation_header_id',
				stock_id='$stock_id',
				quantity='$quantity'
		";
		mysql_query($query) or $objResponse->alert(mysql_error());
		
		$objResponse->script("xajax_getDialogFormulationTable('$formulation_header_id')");
		
		return $objResponse;
	}
	
	function addFormulationHeaderPM($form_data,$stock_form){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		
		$stock_id					= $stock_form['stock_id'];
		$formulation_code			= $form_data['formulation_code'];
		$description				= $form_data['description'];
		$main_id					= $form_data['main_id'];
		$kilosperbag				= $form_data['kilosperbag'];
		$output						= $form_data['output'];
		$product_id					= $form_data['product_id'];
	
		$user_id					= $_SESSION['userID'];
		$username					= $options->getUserName($user_id);
		
		$query="
			insert into
				formulation_header
			set
				formulation_code='$formulation_code',
				description='$description',
				date_created='".date("Y-m-d")."',
				date_updated='".date("Y-m-d")."',
				main_id='$main_id',
				kilosperbag='$kilosperbag',
				output='$output',
				user_id='$user_id',
				product_id='$product_id'
		";	
		
		mysql_query($query) or $objResponse->alert(mysql_error());

		$formulation_header_id = mysql_insert_id();
		
		$objResponse->script("document.getElementById('formulation_id').value='$formulation_header_id';");
		$objResponse->script("j('#formulation_detail_content').show();");
		$objResponse->script("j('#dialog_submit').hide();");
		$objResponse->script("j('#dialog_update').show();");
		$objResponse->script("xajax_updateFormulation('$stock_id')");
		
		return $objResponse;
	}
	
	function updateFormulationHeaderPM($form_data,$stock_form){
		$objResponse 	= new xajaxResponse();
		$options		= new options();

		$stock_id					= $stock_form['stock_id'];		
		$formulation_header_id		= $form_data['formulation_id'];
		$formulation_code			= $form_data['formulation_code'];
		$description				= $form_data['description'];
		$main_id					= $form_data['main_id'];
		$kilosperbag				= $form_data['kilosperbag'];
		$output						= $form_data['output'];
		$product_id					= $form_data['product_id'];
	
		$user_id					= $_SESSION['userID'];
		$username					= $options->getUserName($user_id);
		
		$query="
			update
				formulation_header
			set
				formulation_code='$formulation_code',
				description='$description',
				date_updated='".date("Y-m-d")."',
				main_id='$main_id',
				kilosperbag='$kilosperbag',
				output='$output',
				user_id='$user_id',
				product_id='$product_id'
			where
				formulation_header_id='$formulation_header_id'
		";	
		
		mysql_query($query) or $objResponse->alert(mysql_error());		
		
		$objResponse->alert("Formulation Updated");
		$objResponse->script("xajax_updateFormulation('$stock_id')");
		
		return $objResponse;
	}
	
	function getDialogFormulationTable($formulation_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		$content=$options->getFormulationTable($formulation_header_id,TRUE);
		
		$objResponse->assign("dialog_table_container","innerHTML",$content);
		return $objResponse;	
	}

	function removeFormulationDetailPM($formulation_detail_id,$formulation_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			delete from
				formulation_details
			where
				formulation_detail_id='$formulation_detail_id'
		";
		mysql_query($query) or $objResponse->alert(mysql_error());
		
		$objResponse->script("xajax_getDialogFormulationTable('$formulation_header_id')");
		
		return $objResponse;
	}
	
	function updateFormulation($stock_id){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
	
		$result=mysql_query("
			select
				*
			from	
				formulation_header
			where
				status!='C'
			and
				product_id='$stock_id'
		") or die(mysql_error());
		$content="";
		while($r=mysql_fetch_assoc($result)){
			$formulation_header_id	= $r[formulation_header_id];
			$formulation_code 		= $r[formulation_code];
		
			$content.="
				<input type=\"text\" class=\"textbox\" value=\"$formulation_code\"  readonly=\"readonly\" /> <img src=\"images/note.png\" style=\"cursor:pointer;\" onclick=\"xajax_editFormulationForm('$formulation_header_id')\"  /><br />
               ";
		}
		
		$objResponse->assign('formulations','innerHTML',$content);
		
		return $objResponse;
	}
	
?>