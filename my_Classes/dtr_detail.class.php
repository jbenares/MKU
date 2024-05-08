<?php 

	function edit_dtr_detail_form($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								dtr_detail
							where
								dtr_detail_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> EDIT Details<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_dtr_detail(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						
						  <tr>
						  	<td>SSS: </td>
						  	<td><input type=text name=sss class=textbox value='$r[sss]'  ></td>
							<input type=hidden name=dtr_detail_id value='$r[dtr_detail_id]' >
						  </tr>
						  <tr>
						  	<td>PHIC: </td>
						  	<td><input type=text name='phic' class=textbox value='$r[phic]' ></td>
						  </tr>
						 <tr>
						  	<td>HDMF: </td>
						  	<td><input type=text name='hdmf' class=textbox value='$r[hdmf]' ></td>
						  </tr>
						   <tr>
						  	<td>C/A: </td>
						  	<td><input type=text name='ca' class=textbox value='$r[ca]' ></td>
						  </tr>
						  <tr>
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
	
	function edit_dtr_detail($form_data) {
		$objResponse = new xajaxResponse();
		
				
			$sql = "update dtr_detail set
						sss='$form_data[sss]',
						phic='$form_data[phic]',
						hdmf='$form_data[hdmf]',
						ca='$form_data[ca]'
					where
						dtr_detail_id='$form_data[dtr_detail_id]'
					";
			
			$query = mysql_query($sql);		
			
			if(!mysql_error()) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
		
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	

?>