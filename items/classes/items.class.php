<?php
function new_agentform(){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
									
				<div class='form-div'>
					Product : <br>
					<input type='text' class='textbox' name='product' id='supplier_name1' onclick='this.select();' />
					<input type='hidden' name='supplier_id' id='account_id' title='Please Select Product'/>
				</div> 
				
				<div class='form-div'>
					KMS RUN : <br>
					<input type='text' class='textbox' name='kms' value=''>
				</div>
				<div class='form-div'>
					DYS RUN : <br>
					<input type='text' class='textbox' name='dys' value=''>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_agent(xajax.getFormValues('Rdiv')); >
				</div>
		</div>
	";
	
	$objResponse->assign('Rdiv','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PART FILE LIST' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('showBox();');
	
	return $objResponse;
}

function new_agent($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$fname 	= $form_data['fname'];
	$lname = $form_data['lname'];
	
	mysql_query("		
	
		insert into
			agent
		set			
			agent_lname	= '$lname',
			agent_fname	= '$fname',		
			date_added	= NOW()
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}


function edit_agentform($id){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			agent
		where
			agent_id = '$id'
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$agent_lname = $r['agent_lname'];
	$agent_fname = $r['agent_fname'];	

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='agent_id' value='$id' >
			
				<div class='form-div'>
					First Name : <br>
					<input type='text' class='textbox' name='fname' value='$agent_fname'>
				</div>
				
				<div class='form-div'>
					Last Name : <br>
					<input type='text' class='textbox' name='lname' value='$agent_lname'>
				</div>
												
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_agent(xajax.getFormValues('Rdiv')); >
				</div>
		</div>
	";
	
	$objResponse->assign('Rdiv','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'AGENT' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('showBox();');
	
	return $objResponse;
}

function edit_agent($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$fname 	= $form_data['fname'];
	$lname = $form_data['lname'];
	$agent_id = $form_data['agent_id'];
		
	mysql_query("
		update
			agent
		set			
			agent_lname	= '$lname',
			agent_fname		= '$fname',			
			date_modified		= NOW()
		where
			agent_id		= '$agent_id'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}


function add_agentform($id){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			agent
		where
			agent_id = '$id'
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$agent_lname = $r['agent_lname'];
	$agent_fname = $r['agent_fname'];	
	$name = $agent_lname . '&nbsp;' . $agent_fname;

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='agent_id' value='$id' >
				
				<div class='form-div'>
					Top Agent : &nbsp; $name<br /><br />
					
				</div>
			
				<div class='form-div'>
					First Name : <br>
					<input type='text' class='textbox' name='fname' value=''>
				</div>
				
				<div class='form-div'>
					Last Name : <br>
					<input type='text' class='textbox' name='lname' value=''>
				</div>
												
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_add_agent(xajax.getFormValues('Rdiv')); >
				</div>
		</div>
	";
	
	$objResponse->assign('Rdiv','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'AGENT' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('showBox();');
	
	return $objResponse;
}

function add_agent($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$fname 	= $form_data['fname'];
	$lname = $form_data['lname'];
	$agent_id = $form_data['agent_id'];
			
	mysql_query("		
	
		insert into
			agent
		set			
			agent_lname	= '$lname',
			agent_fname	= '$fname',	
			top_agent = '$agent_id',
			date_added	= NOW()
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

function view_agents($id){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			agent
		where
			agent_id = '$id'
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);

	$agent_lname = $r['agent_lname'];
	$agent_fname = $r['agent_fname'];	
	$name = $agent_lname . '&nbsp;' . $agent_fname;
	
	$content = "<br><img src='images/user.png'> View Agents<p>
					
						<table class=form_table>
						  <tr>
						  	<td>Top Agent :</td>
						  	<td>
								".$name."
							</td>
						  </tr>						
						</table>
					   <br>AGENTS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden; width:400px;'></div>";	

	
	
	$objResponse->assign('Rdiv','innerHTML',$content);
	$objResponse->script("xajax_show_agents('$id')");
	$objResponse->script("toggleBox('demodiv',0)");
	$objResponse->script("showBox()");
	
	return $objResponse;
}

function deletee($sId, $id) {
		$objResponse = new xajaxResponse();
		
		$removeF = mysql_query("UPDATE agent SET is_deleted = '1' WHERE agent_id='$sId'");
											
		$objResponse->script("xajax_show_details('$id')");
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}

function show_agents($id) {
		$objResponse = new xajaxResponse();
		
		$get_details = mysql_query("select
											*
										from
											agent
										where
											top_agent='$id' AND is_deleted != '1'
									");
											
		while($rFunctions=mysql_fetch_array($get_details)) {
			$row.='<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;">
				   <img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteD(\''.$rFunctions[agent_id].'\',\''.$id.'\');toggleBox(\'demodiv\',1);" title="Remove"> 
				   '.$rFunctions[agent_lname] . ',&nbsp;' . $rFunctions[agent_fname] . '</div>';
		}
		
		$content = $row;
		
		$objResponse->assign("funclistdiv","innerHTML", $content);
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}
?>