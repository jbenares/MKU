<?php
	
	function new_menuform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/application_cascade.png'> NEW MENU ITEM<p>
					<form id=newmenuform action=javascript:void(null); onsubmit=xajax_new_menu(xajax.getFormValues('newmenuform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Menu Title: </td>
						  	<td><input type=text name=menu_title class=textbox></td>
						  </tr>	
						  <tr>
						  	<td>Level: </td>
						  	<td>
								<input type=radio name=level value=1 checked> 1
								<input type=radio name=level value=2> 2
								<input type=radio name=level value=3> 3
							</td>
						  </tr>	  
						  <tr>
						  	<td>Icon Filename: </td>
						  	<td><input type=text name=iconf class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Associated File: </td>
						  	<td>".$options->file_options('')."</td>
						  </tr>
						  <tr>
						  	<td>Parent Menu: </td>
						  	<td>".$options->parentMenu_options('')."</td>
						  </tr>
						  <tr>
						  	<td>Placement: </td>
						  	<td><input type=text name=placement class=textbox></td>
						  </tr>
						  <tr>
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
		
		return $objResponse;	
	}
	
	function new_menu($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[menu_title]) && !empty($form_data[level])) {
				
			$sql = "insert into menu set
						Mname='$form_data[menu_title]',
						level='$form_data[level]',
						icon_filename='$form_data[iconf]',
						parent='$form_data[mCode]',
						PCode='$form_data[file_]',
						placement='$form_data[placement]'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
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
	
	function edit_menuform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
					  Mname,
					  level,
					  icon_filename,
					  parent,
					  PCode,
					  placement
					from
					  menu
					where
					  M_id=$id");
						  
		$r = mysql_fetch_array($sql);
						  
		$newContent = "<br><img src='images/application_cascade.png'> UPDATE MENU ITEM<p>
					<form id=editmenuform action=javascript:void(null); onsubmit=xajax_edit_menu(xajax.getFormValues('editmenuform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Menu Title: </td>
						  	<td>
								<input type=text name=menu_title class=textbox value='$r[Mname]'>
								<input type=hidden name=menuID class=textbox value='$id'>
							</td>
						  </tr>	
						  <tr>
						  	<td>Level: </td>
						  	<td><input type=text name=level class=textbox value='$r[level]'></td>
						  </tr>	  
						  <tr>
						  	<td>Icon Filename: </td>
						  	<td><input type=text name=iconf class=textbox value='$r[icon_filename]'></td>
						  </tr>
						  <tr>
						  	<td>Associated File: </td>
						  	<td>".$options->file_options($r[PCode])."</td>
						  </tr>
						  <tr>
						  	<td>Parent Menu: </td>
						  	<td>".$options->parentMenu_options($r['parent'])."</td>
						  </tr>
						  <tr>
						  	<td>Placement: </td>
						  	<td><input type=text name=placement class=textbox value='$r[placement]'></td>
						  </tr>
						  <tr>
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
		
		return $objResponse;	
	}
	
	function edit_menu($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[menuID])) {
				
			$sql = "update menu set
						Mname='$form_data[menu_title]',
						level='$form_data[level]',
						icon_filename='$form_data[iconf]',
						parent='$form_data[mCode]',
						PCode='$form_data[file_]',
						placement='$form_data[placement]'
					where
						M_id='$form_data[menuID]'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
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

?>