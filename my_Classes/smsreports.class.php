<?php
	
	function tag_sms($id) {	
		$objResponse = new xajaxResponse();		
		
		$options = new options();	
		
		$getR = mysql_query("select
								id,
								name,
								mobileno,
								sms_message,
								received
							from
								sms_reports
							where
								id='$id'");
			
		$r = mysql_fetch_array($getR);
				
		$newContent .= '<p style="padding:5px;text-align:left;"><b><u>'.$r[name].'</u> ('.$r[mobileno].')</b><br>'.$options->convert_sysdate($r[received]).
						'<br><br>'.$r[sms_message].'</p>
						<p style="padding:5px;text-align:right;">
						<input type="button" value="Submit This Report" class="buttons" onclick="tag_confirm(\'SMS\',\''.$r[name].'\',\''.urlencode($r[mobileno]).'\',\''.$r[sms_message].'\',\''.$id.'\')"></p>';
				
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addScript("showBox()");
		
		return $objResponse->getXML();  			   
	}
	
	function update_smsbb() {
		$objResponse = new xajaxResponse();
				
		$options = new options();
		
		$getR = mysql_query("select
					id,
					name,
					mobileno,
					sms_message,
					received
				from
					sms_reports
				where
					read_='0'
				order by
					received desc
				limit 0,100");
		
		$row = '<table cellspacing="1" cellpadding="5" width="100%" class="search_table">';
		
		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr>';
			
			$row .= '<td width="46" valign="top"><img src="images/sms.jpg"></td>';
			$row .= '<td style="border-bottom: 1px solid #EEEEEE;" valign="top"><b><u>'.$r[name].'</u> ('.$r[mobileno].')</b>
 		<a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_tag_sms(\''.$r[id].'\');toggleBox(\'demodiv\',1);"><img src="images/folder_new.gif" border="0"></a>
 		<br>'.$options->convert_sysdate($r[received]).'<p>'.$r[sms_message].'</p></td>';
							
			$row .= '</tr>';
		}
		
		$row .= '</table><br>';
		
		$objResponse->addAssign("smsbb_div","innerHTML", $row);
		$objResponse->addScript("setTimeout(\"xajax_update_smsbb()\",1000)");
			
		return $objResponse->getXML();
	}

?>