<?php
	
	function new_questionform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$countQ = mysql_query("select qid from ppiquestions where publish='1'");
		$Qcount = mysql_num_rows($countQ)+1;
		
		$newContent = "<br><img src='images/database_add.png'> NEW QUESTION<p>
					<form id=newquestionform action=javascript:void(null); onsubmit=xajax_new_question(xajax.getFormValues('newquestionform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Number: </td>
						  	<td><input type=text name=qnum class=textbox value='$Qcount'></td>
						  </tr>
						  <tr>
						  	<td>English: </td>
						  	<td><textarea name='englishq' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
						  </tr>
						  <tr>
						  	<td>Tagalog: </td>
						  	<td><textarea name='tagalogq' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'></textarea></td>
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
	
	function new_question($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[qnum]) && !empty($form_data[englishq]) && !empty($form_data[tagalogq])) {
				
			$date_exec = date("Y-m-d H:i:s");
				
			$sql = "insert into ppiquestions set
					orderNum='$form_data[qnum]',
					english='$form_data[englishq]',
					tagalog='$form_data[tagalogq]',
					dateencoded='$date_exec'";			
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {		
				$Newfield = "q".$form_data[qnum];
				$addField = mysql_query("alter table answers add $Newfield varchar(50)");
				
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
	
	function edit_questionform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$getquestion= mysql_query("select
									  orderNum,
									  english,
									  tagalog
									from
									 ppiquestions
									where
									  qid='$id'");
						  
		$rquestion = mysql_fetch_array($getquestion);
						  
		$newContent = "<br><img src='images/database_add.png'> UPDATE question<p>
					<form id=editquestionform action=javascript:void(null); onsubmit=xajax_edit_question(xajax.getFormValues('editquestionform'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						  	<td>Number: </td>
						  	<td>
								<input type=text name=qnum class=textbox value='".$rquestion[orderNum]."'>
								<input type=hidden name=qid class=textbox value='".$id."'>
							</td>
						  </tr>
						  <tr>
						  	<td>English: </td>
						  	<td><textarea name='englishq' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'>".$rquestion[english]."</textarea></td>
						  </tr>
						  <tr>
						  	<td>Tagalog: </td>
						  	<td><textarea name='tagalogq' style='overflow:hidden;width:300px;height:100px;font-size:11px;font-family:Arial;'>".$rquestion[tagalog]."</textarea></td>
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
	
	function edit_question($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[qnum]) && !empty($form_data[englishq]) && !empty($form_data[tagalogq])) {
				
			$date_exec = date("Y-m-d H:i:s");
				
			$sql = "update ppiquestions set
						orderNum='$form_data[qnum]',
						english='$form_data[englishq]',
						tagalog='$form_data[tagalogq]',
						dateencoded='$date_exec'
					where
						qid='$form_data[qid]'";
			
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

	function edit_choices($qid, $orderNum, $english, $tagalog) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($PCode);
		
		$newContent = "<br><img src='images/user_add.png'> MODIFY CHOICES<p>					
					<form id=edit_choices action=javascript:void(null); onsubmit=xajax_save_choices(xajax.getFormValues('edit_choices'));toggleBox('demodiv',1);>
						<table class=form_table>
						  <tr>
						    <td colspan=2>".$english."<br>".$tagalog."</td>
						  </tr>
						  <tr>
						  	<td>Choice:</td>
						  	<td>
								<input type=text name=choice class=textbox>
							</td>
						  </tr>
						  <tr>
						  	<td>Additional Description:</td>
						  	<td>
								<textarea name='description' style='overflow:hidden;width:300px;height:75px;font-size:11px;font-family:Arial;'></textarea>
								<input type=hidden name=qid value='".$qid."'>
							</td>
						  </tr>
						  <tr>
						  	<td>Score:</td>
						  	<td>
								<input type=text name=score class=textbox3>
							</td>
						  </tr>
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						</table>
					   </form>
					   <form id=update_choices action=javascript:void(null); onsubmit=xajax_update_choices(xajax.getFormValues('update_choices'));toggleBox('demodiv',1);><br>CHOICES <input type=submit name=b value='Update' class=buttons><hr><div id='choicelistdiv' style='padding:5px;color:#5e6977;background:#EEEEEE;width:98%;height:100px;overflow-y:scroll;'></div></form>";	
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("xajax_show_choices('$qid')");
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;	
	}
	
	function save_choices($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[qid]) && !empty($form_data[choice])) {
				
			$date_exec = date("Y-m-d H:i:s");
				
			$sql = "insert into ppiscores set
						qid='$form_data[qid]',
						choice='$form_data[choice]',
						description='$form_data[description]',
						score='$form_data[score]',
						date_modified='$date_exec'";
			
			$query = mysql_query($sql);
			
			$objResponse->script("toggleBox('demodiv',0)");
			
			if($query) {
				$objResponse->script("xajax_show_choices('$form_data[qid]')");
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
	
	function show_choices($qid) {
		$objResponse = new xajaxResponse();
		
		$getChoices = mysql_query("select
										id,
										choice,
										score,
										description
									from
										ppiscores
									where
										qid='$qid' and
										enable='1'");
		
		while($rChoices=mysql_fetch_array($getChoices)) {
			if(empty($rChoices[description])) $desc = 'No additional description.';
			else $desc = $rChoices[description];
		
			$row.='<div style="padding:3px;border-bottom:#C0C0C0 1px dashed;text-align:right;" onmouseover="Tip(\''.$desc.'\');">'.$rChoices[choice].' 
			<input type="hidden" name="savedchoice[]" value="'.$rChoices[choice].'" class="textbox3">
			<input type="text" name="savedscore[]" value="'.$rChoices[score].'" class="textbox3">
			<input type="hidden" name="scoreid[]" value="'.$rChoices[id].'"s>
		    <img src=\'images/trash.gif\' style=\'cursor:pointer;\' onclick="xajax_deleteC(\''.$rChoices[id].'\',\''.$qid.'\');toggleBox(\'demodiv\',1);" title="Remove">	</div>';
		}
		
		$newContent = '<input type="hidden" name="qid" value="'.$qid.'">'.$row;
		
		$objResponse->assign("choicelistdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse;  	
	}
	
	function update_choices($form_data) {
		$objResponse = new xajaxResponse();
		
		//$objResponse->alert(print_r($form_data));
		$i = 0;
		$thesame = true;
		foreach($form_data[savedscore] as $newscore) {
					
			$id = $form_data[scoreid][$i];
			$check_ifthesame = mysql_query("select score from ppiscores where id='$id'");
			$r_ifthesame = mysql_fetch_array($check_ifthesame);
			
			if($r_ifthesame[score]!=$newscore) {
				$thesame = false;				
				break;
			}

			$i++;
		}

		if($thesame) {
			$objResponse->alert("Nothing to update, the scores are the same!");
			$objResponse->script("toggleBox('demodiv',0)");
		
			return $objResponse;  
		}
		else {			
			$date_exec = date("Y-m-d H:i:s");
			
			$i = 0;
			foreach($form_data[savedscore] as $newscore) {

				$id = $form_data[scoreid][$i];
				$choice = $form_data[savedchoice][$i];
				$query = mysql_query("update ppiscores set enable='0' where id='$id' and date_modified<'$date_exec'");
				
				$sql = "insert into ppiscores set
						qid='$form_data[qid]',
						choice='$choice',
						score='$newscore',
						date_modified='$date_exec'";
			
				$query = mysql_query($sql);
				
				$i++;
			}
			
			$objResponse->script("toggleBox('demodiv',0)");
			return $objResponse;  
		}
	}
	
	function deleteC($id, $qid) {
		$objResponse = new xajaxResponse();

		$query = mysql_query("update ppiscores set enable='0' where id='$id'");
		
		$objResponse->script("xajax_show_choices('$qid')");
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  
	}
	
?>