<?php
	
	function read_pm($pmrid,$i) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$query = mysql_query("select
									a.userID,
									concat(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,
									pm.privatemsg,
									pm.subject,
									pm.date_sent,
									pm.id as pmid
								from
									pmrecipients as pmr,
									private_messages as pm,
									admin_access as a
								where									
									pmr.id='$pmrid' and
									pmr.pmID=pm.id and
									pm.sent_by=a.userID");
		
		$r = mysql_fetch_array($query);
		
		$recipients = "$r[userID]($r[fullname]);";
		$subject= "RE: $r[subject]";
		
		$get_view = mysql_query("select view_keyword from programs where Pfilename='new_message.php'");
		$rW = mysql_fetch_array($get_view);
		
		$get_attachments = mysql_query("select Afilename from attachments where pmid='$r[pmid]'");		
		
		if(mysql_num_rows($get_attachments)>0) {			
			$attachments = "| <img src='images/email_attach.png'> <a href='javascript:void(0);' onclick=xajax_show_attachments('$r[pmid]');toggleBox('demodiv2',1);> View Attachments</a><div id='demodiv2' class='demo3'></div>";
		}
		
		$newContent = "<br><img src='images/email.png'> MESSAGE<p><table class=form_table width=500>							
							<tr>
								<td><img src='images/user_go.png'> $r[fullname] <img src='images/date.png'> ".$options->convert_sysdate($r[date_sent])."</td>
							</tr>
							<tr>
								<td><img src='images/comment_blue.gif'> Subject : $r[subject]</td>
							</tr>
							<tr>
								<td><img src='images/email_go.png'><a href='javascript:void(0);' onclick=\"window.location='admin.php?view=$rW[view_keyword]&recipients=$recipients&subject=$subject'\"> Reply to this message</a> 
								$attachments</td>
							</tr>
							<tr>
								<td><hr><div style='width:750px;height:300px;padding:5px;border:#C0C0C0 1px dashed;overflow-y:scroll;background:#FFFFFF'>".$r[privatemsg]."</div></td>
							</tr>							
						</table></p>";
						
		$date_exec = date("Y-m-d H:i:s");
		$update_pmr = mysql_query("update pmrecipients set read_='Read', read_when='$date_exec' where id='$pmrid'");
		
		$objResponse->addAssign("Rdiv","innerHTML", $newContent);
		$objResponse->assign("tdname$i","innerHTML", $r[fullname]);
		$objResponse->assign("tdsubject$i","innerHTML", $r[subject]);
		$objResponse->assign("tddate$i","innerHTML", $options->convert_sysdate($r[date_sent]));
		$objResponse->addScript("toggleBox('demodiv',0)");
		$objResponse->addScript("showBox()");
		
		return $objResponse->getXML();	
	}
	
	function show_attachments($pmid) {
		$objResponse = new xajaxResponse();	
		
		$get_attachments = mysql_query("select Afilename from attachments where pmid='$pmid'");		
		
		if(mysql_num_rows($get_attachments)>0) {			
			while($r_A=mysql_fetch_array($get_attachments)) {
				$row .= "<img src='images/bullet_orange.png'><a href='My_Uploads/attachments/$r_A[Afilename]' target='_blank'>$r_A[Afilename]</a><br>";
			}
		
		
			$newContent .= "<img src='images/email_attach.png'> <b>ATTACHMENTS</b><a style='cursor: pointer' onclick=\"toggleBox('demodiv2',0);\">
					   <img src='images/close.gif' style='position:absolute;right:-4px;top:-4px;'></a>
					   <br>".$row;
		}
		
		
		$objResponse->assign("demodiv2","innerHTML", $newContent);
			
		return $objResponse;
	}
	
	function address_book() {
		$objResponse = new xajaxResponse();	
		$option = new options();
		
		$newContent = "<img src='images/user_add.png'> <b>ADD RECIPIENTS</b><a style='cursor: pointer' onclick=\"toggleBox('demodiv2',0);\">
					   <img src='images/close.gif' style='position:absolute;right:-4px;top:-4px;'></a>
					   <br><input type='text' id=addBkey class=textbox onkeyup=\"xajax_show_users(document.getElementById('addBkey').value);toggleBox('demodiv',1);\" /><div id='addbookdiv' style='overflow-y:scroll;overflow-x:hidden;height:250px;'></div>";
		
		$objResponse->assign("demodiv2","innerHTML", $newContent);
		$objResponse->script("document.getElementById('addBkey').focus();");
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function show_users($addBkey) {
		$objResponse = new xajaxResponse();
		
		if(!empty($addBkey)) {
			$query = mysql_query("select
										userID,
										concat(user_lname,', ',user_fname) as fullname
									from
										admin_access
									where
										(user_lname like '%$addBkey%' or
										user_fname like '%$addBkey%') and
										access!='1'");
								
			while($r=mysql_fetch_array($query)) {
				$row .= '<div style="padding:3px;border-bottom:#EEEEEE 1px dashed;"><img src=\'images/vcard_add.png\' style=\'cursor:pointer;\' onclick="document.getElementById(\'recipients\').value+=\''.$r[userID].'('.$r[fullname].');\'"> '.$r[fullname].'</div>';
			}
		}
		
		$objResponse->assign("addbookdiv","innerHTML", $row);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function show_groups() {
		$objResponse = new xajaxResponse();	
		$option = new options();
		
		$newContent = "<img src='images/group_add.png'> <b>SELECT GROUPS</b><a style='cursor: pointer' onclick=\"toggleBox('demodiv2',0);\"><img src='images/close.gif' style='position:absolute;right:-4px;top:-4px;'></a>".$option->GM_options('')."<p><img src='images/star.png'><a href='javascript:void(0);' onclick=\"xajax_putGM('', 1);\"> Send to All Users</a></p>";
				
		$objResponse->assign("demodiv2","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;
	}
	
	function putGM($groupID, $all) {
		$objResponse = new xajaxResponse();
		
		if($all==0) {
			$q = mysql_query("select
									a.userID,
									concat(a.user_lname,', ',a.user_fname) as fullname
								from
									admin_access as a,
									group_members as gm
								where
									gm.groupID='$groupID' and
									gm.userID=a.userID");
		}
		else {
			$q = mysql_query("select
									userID,
									concat(user_lname,', ',user_fname) as fullname
								from
									admin_access
								where
									access!='1'");
		}
								
		while($r=mysql_fetch_array($q)) {								
			$objResponse->script("document.getElementById('recipients').value+='$r[userID]($r[fullname]);'");
		}
		
		return $objResponse;
	}
	
	function view_recipients($pmid) {
		$objResponse = new xajaxResponse();	
		$options = new options();
		
		//$objResponse->alert($index);
		
		$getMsgDetails = mysql_query("select
											a.userID,
											concat(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,
											pmr.read_,
											pmr.read_when
										from
											pmrecipients as pmr,
											admin_access as a
										where
											pmr.userID=a.userID and
											pmr.pmID='$pmid'");

		$i=1;
		while($MsgDetails=mysql_fetch_array($getMsgDetails)) {
			if($MsgDetails[read_]=='Read') $img = "<img src='images/email_open.png' onmouseover=\"Tip('Read : ".$options->convert_sysdate($MsgDetails[read_when])."');\">";
			else $img = "<img src='images/email.png' onmouseover=\"Tip('The message has not been read by the recipient yet.');\">";
		
			$row .= ' <div style="padding:3px;border-bottom:#C0C0C0 1px dashed;">'.$img.' '.$MsgDetails[fullname].'</div>';
		}	
		
		
		$get_attachments = mysql_query("select Afilename from attachments where pmid='$pmid'");		
		
		if(mysql_num_rows($get_attachments)>0) {			
			$attachments = "<img src='images/email_attach.png'> <a href='javascript:void(0);' onclick=xajax_show_attachments('$pmid');toggleBox('demodiv2',1);> View Attachments</a><div id='demodiv2' class='demo3'></div>";
		}
				
		$newContent	= "<br><img src='images/group.png'> <b>RECIPIENTS</b><div style='padding:5px;color:#5e6977;background:#EEEEEE;width:700px;height:100px;overflow-y:scroll;overflow-x:hidden;'>".$row."</div>";
					   
		$query = mysql_query("select
									id,
									subject,
									date_sent,
									privatemsg
								from
									private_messages
								where
									id='$pmid'");
		
		$r = mysql_fetch_array($query);
		
		$newContent .= "<br><img src='images/email.png'> <b>MESSAGE</b><table class=form_table>
							<tr>
								<td><img src='images/comment_blue.gif'> Subject : $r[subject]</td>
							</tr>
							<tr>
								<td><img src='images/date.png'> ".$options->convert_sysdate($r[date_sent])." $attachments</td>
							</tr>
							<tr>
								<td colspan=2><hr><div style='width:750px;height:200px;padding:5px;border:#C0C0C0 1px dashed;overflow-y:scroll;background:#FFFFFF'>".$r[privatemsg]."</div></td>
							</tr>						
							</table>";
				
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;
	}

?>