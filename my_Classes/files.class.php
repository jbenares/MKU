<?php
	
	function edit_functions($PCode, $Pfilename) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($PCode);
		
		$newContent = "<br><img src='images/user_add.png'> MODIFY XAJAX FUNCTIONS<br>(".strtoupper($Pfilename).")<p>
					<form id=edit_function action=javascript:void(null); onsubmit=xajax_save_functions(xajax.getFormValues('edit_function'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>New Function: </td>
						  	<td>
								<input type=text name=function_name class=textbox>
								<input type=hidden name=PCode value='".$PCode."'>
							</td>
						  </tr>
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						</table>
					   </form><br>INSTALLED FUNCTIONS<hr><div id='funclistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;height:150px;overflow-y:scroll;overflow-x:hidden;'></div>";	
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("xajax_show_functions('$PCode')");
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;	
	}
	
	function save_functions($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[function_name])) {
				
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
				
			$sql = "insert into my_functions set
					FID='$id',
					Fname='$form_data[function_name]',
					PCode='$form_data[PCode]',
					date_modified='$date_exec'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_functions('$form_data[PCode]')");
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
	
	function deleteF($FID, $PCode) {
		$objResponse = new xajaxResponse();
		
		$removeF = mysql_query("delete from my_functions where FID='$FID'");
											
		$objResponse->script("xajax_show_functions('$PCode')");
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}
	
	function show_functions($PCode) {
		$objResponse = new xajaxResponse();
		
		$get_functions = mysql_query("select
											FID,
											Fname
										from
											my_functions
										where
											PCode='$PCode'");
											
		while($rFunctions=mysql_fetch_array($get_functions)) {
			$row.='<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;">
				   <img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteF(\''.$rFunctions[FID].'\',\''.$PCode.'\');toggleBox(\'demodiv\',1);" title="Remove"> '.$rFunctions[Fname].'</div>';
		}
		
		$newContent = $row;
		
		$objResponse->assign("funclistdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
				
		return $objResponse;  			   
	}
	
?>