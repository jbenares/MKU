<?php

	function new_journalform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW JOURNAL<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_journal(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Journal: </td>
						  	<td><input type='text' name='journal' class='textbox2'></td>
						  </tr>
						  <tr>
						  	<td>Journal Code: </td>
						  	<td><input type='text' name='journal_code' class='textbox3'></td>
						  </tr>
						  <tr>
						  	<td>Enable: </td>
						  	<td><input type='checkbox' name='enable' checked='checked' value='Yes'></td>
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
	
	function new_journal($form_data) {
		$objResponse = new xajaxResponse();
		
		if(	
			!empty($form_data[journal]) &&
			!empty($form_data[journal_code])
		)
		{
			$enable=($form_data[enable]=="Yes")?"Y":"N";
		
				
			$sql = "insert into 
						journal 
					set
						journal='$form_data[journal]',
						journal_code='$form_data[journal_code]',
						enable='$enable'
					";
			
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
	
	function edit_journalform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								journal
							where
								journal_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
		
		$enable=($r[enable]=="Y")?"checked='checked'":'';
		
		$newContent = "<br><img src='images/user_orange.png'> EDIT JOURNAL<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_journal(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						
						<input type='hidden' name='journal_id' value='$r[journal_id]'>
						<table class=form_table>	
						  <tr>
						  	<td>Journal: </td>
						  	<td><input type='text' name='journal' class='textbox2' value='$r[journal]'></td>
						  </tr>
						  <tr>
						  	<td>Journal Code: </td>
						  	<td><input type='text' name='journal_code' class='textbox3' value='$r[journal_code]' ></td>
						  </tr>
						  <tr>
						  	<td>Enable: </td>
						  	<td><input type='checkbox' name='enable' $enable value='Yes'></td>
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
		
		return $objResponse;}
	
	function edit_journal($form_data) {
		$objResponse = new xajaxResponse();
		
		if(	
			!empty($form_data[journal]) &&
			!empty($form_data[journal_code])
		)
		{
			$enable=($form_data[enable]=="Yes")?"Y":"N";
			
			$sql = "update
						journal 
					set
						journal='$form_data[journal]',
						journal_code='$form_data[journal_code]',
						enable='$enable'
					where
						journal_id='$form_data[journal_id]'
					";
			
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
	
	function print_journal($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		if($id=="DV"){		
			$newContent="
				<iframe id='JOframe' name='JOframe' frameborder='0' src='printJournal.php?journal_code=$id' width='100%' height='500'>
				</iframe>
			";
			$objResponse->script("hideBox();");
			$objResponse->assign("content","innerHTML", $newContent);
			$objResponse->assign("joborder_id","value",$id);
			
		}
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
?>