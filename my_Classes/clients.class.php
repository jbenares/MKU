<?php
	
	function new_client($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[m2clientid]) && !empty($form_data[client_m2name]) && !empty($form_data[branchid]) 
		   && !empty($form_data[centernum]) && !empty($form_data[area])) {
		
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
			
			$now_array = explode("/", $form_data[Ndate]);
			$now = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
				
			$sql = "insert into clients set
					m2clientid='$form_data[m2clientid]',
					m2clientname='$form_data[client_m2name]',
					branchID='$form_data[branchid]',
					centerno='$form_data[centernum]',
					typeofarea='$form_data[area]',
					educationalattainment='$form_data[educationalattainment]'";
			
			$query = mysql_query($sql);		
			
			if(!mysql_error()) {
				$objResponse->script("document._form.reset();");
				$objResponse->alert("Query Successful!");
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
	
	function edit_client($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[m2clientid]) && !empty($form_data[clientname])
			&& !empty($form_data[branchid]) && !empty($form_data[centernum])) {
			
			$sql = "insert into clients set
						m2clientname='$form_data[client_m2name]',
						branchID='$form_data[branchid]',
						centerno='$form_data[centernum]',
						typeofarea='$form_data[area]',
						educationalattainment='$form_data[educationalattainment]'
					where
						m2clientid='$form_data[m2clientid]'";
			
			$query = mysql_query($sql);
			
			
			
			if($query) {
				$objResponse->alert("Query Successful!");
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
	
	function show_clients($keyWord) {
		$objResponse = new xajaxResponse();
		
		if(!empty($keyWord)) {		
			$queryC = mysql_query("select
										m2clientname,
										m2clientid
									from
										clients
									where
										m2clientname like '$keyWord%' or
										m2clientid like '$keyWord%'
									limit
										0, 20");
			
			while($rC=mysql_fetch_array($queryC)) {
				$row .= '<div style="padding:3px;border-bottom:#EEEEEE 1px dashed;"><img src=\'images/vcard_add.png\' style=\'cursor:pointer;\' onclick="document.getElementById(\'client_keyword\').value=\''.$rC[m2clientname].'\';document.getElementById(\'client_m2id\').value=\''.$rC[m2clientid].'\';toggleBox(\'demodiv3\',0)"> ('.$rC[m2clientid].') <font color=red>'.$rC[m2clientname].'</font></div>';
			}	
			
			$objResponse->script("toggleBox('demodiv3',1)");
			$objResponse->assign("clientdiv","innerHTML", $row);
		}
				
		$objResponse->script("toggleBox('demodiv',0)");
		
		return $objResponse; 
	}
	
	function submit_scorecard($form_data) {
		$objResponse = new xajaxResponse();
		
		$countQ = mysql_query("select count(orderNum) as Qnum from ppiquestions");
		$rQ = mysql_fetch_array($countQ);
		
		for($ii=1;$ii<=$rQ[Qnum];$ii++) {
			if(!empty($form_data["q$ii"])) {
				$q[$ii] = $form_data["q$ii"];
			}
			else {
				$objResponse->alert("Please answer all the questions!");
				$objResponse->script("toggleBox('demodiv',0)");
				return $objResponse;
			}
		}
		
		if(!empty($form_data[m2clientid]) && !empty($form_data[Ndate]) && !empty($form_data[Ddate])
		 	&& !empty($form_data[product]) && !empty($form_data[stay]) && !empty($form_data[cycle])
			&& !empty($form_data[busnum])  && !empty($form_data[noofhousehold])) {
			
			$busnum = $form_data[busnum];	
			$existing = $form_data[existing];
			$startup = $form_data[starting];
			
			if(($startup+$existing)!=$busnum) {
				$objResponse->script("toggleBox('demodiv',0)");
				$objResponse->alert("Total # of businesses, # of existing and start-up do not match.");				
				return $objResponse;
			}
			
			$now_array = explode("/", $form_data[Ddate]);
			$Ddate = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
			$now_array = explode("/", $form_data[Ndate]);
			$Ndate = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
			
			$i=0;
			foreach($q as $choiceid) {
				$get_choice_details = mysql_query("select
													choice,
													score
												from
													ppiscores
												where
													id='$choiceid'");
													
				$r = mysql_fetch_array($get_choice_details);
				
				$choice[$i] = $r[choice];
				$score += $r[score];
				
				$i++;
			}
			
			$txid = date("Ymd-his");			
			
			$saveScore = mysql_query("insert into scores set
											txid='$txid',
											m2clientid='$form_data[m2clientid]',
											datedisbursed='$Ddate',
											product='$form_data[product]',
											dateofinterview='$Ndate',
											yearsofstay='$form_data[stay]',
											loancycle='$form_data[cycle]',
											noofbusiness='$busnum',
											startup='$startup',
											existing='$existing',
											noofhousehold='$form_data[noofhousehold]',
											ppiscore='$score'");
				
			$answerid = date("Ymd-his");
											
			$saveAnswers = mysql_query("insert into answers set
											answerid='$answerid',
											txid='$txid',
											q1='$choice[0]',
											q2='$choice[1]',
											q3='$choice[2]',
											q4='$choice[3]',
											q5='$choice[4]',
											q6='$choice[5]',
											q7='$choice[6]',
											q8='$choice[7]',
											q9='$choice[8]',
											q10='$choice[9]'");
											
			$Bid = date("Ymd-his");
			$ii = 1; // business id followed from business table
			foreach($form_data[business] as $bb) {
				$cbID = $Bid.$ii;
			
				if(!empty($bb)) {
					$saveBusiness = mysql_query("insert into clients_businesses set
													cbID='$cbID',
													txid='$txid',
													Bid='$bb'");
				}
				
				$ii++;
				
				if(mysql_error()) $msg .= 'Business : '.mysql_error().'<br>';
			}
											
			if(mysql_error())
				$objResponse->alert(mysql_error());
			else {
				$objResponse->script("document._form.reset();");
				$objResponse->alert("Query Successful!");
			}
		}
		else {
			$objResponse->alert("Please fill in Client Information fieldset!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function update_scorecard($form_data) {
		$objResponse = new xajaxResponse();
		
		$countQ = mysql_query("select count(orderNum) as Qnum from ppiquestions");
		$rQ = mysql_fetch_array($countQ);
		
		for($ii=1;$ii<=$rQ[Qnum];$ii++) {
			if(!empty($form_data["q$ii"])) {
				$q[$ii] = $form_data["q$ii"];
			}
			else {
				$objResponse->alert("Please answer all the questions!");
				$objResponse->script("toggleBox('demodiv',0)");
				return $objResponse;
			}
		}
		
		if(!empty($form_data[m2clientid]) && !empty($form_data[Ndate]) && !empty($form_data[Ddate])
		 	&& !empty($form_data[product]) && !empty($form_data[stay]) && !empty($form_data[cycle])
			&& !empty($form_data[busnum])  && !empty($form_data[noofhousehold])) {
			
			$busnum = $form_data[busnum];	
			$existing = $form_data[existing];
			$startup = $form_data[starting];
			
			if(($startup+$existing)!=$busnum) {
				$objResponse->script("toggleBox('demodiv',0)");
				$objResponse->alert("Total # of businesses, # of existing and start-up do not match.");				
				return $objResponse;
			}
			
			
			$now_array = explode("/", $form_data[Ddate]);
			$Ddate = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
			$now_array = explode("/", $form_data[Ndate]);
			$Ndate = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];
			
			$i=0;
			foreach($q as $choiceid) {
				$get_choice_details = mysql_query("select
													choice,
													score
												from
													ppiscores
												where
													id='$choiceid'");
													
				$r = mysql_fetch_array($get_choice_details);
				
				$choice[$i] = $r[choice];
				$score += $r[score];
				
				$i++;
			}		
			
			$saveScore = mysql_query("update scores set
											m2clientid='$form_data[m2clientid]',
											datedisbursed='$Ddate',
											product='$form_data[product]',
											dateofinterview='$Ndate',
											yearsofstay='$form_data[stay]',
											loancycle='$form_data[cycle]',
											noofbusiness='$busnum',
											startup='$startup',
											existing='$existing',
											noofhousehold='$form_data[noofhousehold]',
											ppiscore='$score',
											status='1'
										where
											txid='$form_data[txid]'");
											
			$saveAnswers = mysql_query("update answers set
											q1='$choice[0]',
											q2='$choice[1]',
											q3='$choice[2]',
											q4='$choice[3]',
											q5='$choice[4]',
											q6='$choice[5]',
											q7='$choice[6]',
											q8='$choice[7]',
											q9='$choice[8]',
											q10='$choice[9]'
										where
											txid='$form_data[txid]'");
			
			$removeBus = mysql_query("delete from clients_businesses where txid='$form_data[txid]'");
											
			$Bid = date("Ymd-his");
			$ii = 1; // business id followed from business table
			foreach($form_data[business] as $bb) {
				$cbID = $Bid.$ii;			
				
				if(!empty($bb)) {
					$saveBusiness = mysql_query("insert into clients_businesses set
													cbID='$cbID',
													txid='$form_data[txid]',
													Bid='$bb'");
				}
				
				$ii++;
				
				if(mysql_error()) $msg .= 'Business : '.mysql_error().'<br>';
			}
											
			if(mysql_error())
				$objResponse->alert(mysql_error());
			else {
				$objResponse->alert("Query Successful!");
			}
		}
		else {
			$objResponse->alert("Please fill in Client Information fieldset!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function preview_score($form_data) {
		$objResponse = new xajaxResponse();
		
		$getClient = mysql_query("select m2clientname from clients where m2clientid='$form_data[m2clientid]'");
		$rClient = mysql_fetch_array($getClient);
		
		$newContent = "<div><img src='images/user.png'> PREVIEW SCORECARD
						<div style='width:500px;height:400px;overflow-y:scroll;overflow-x:hidden;background:#EEEEEE;color:#000000;padding:5px;'>
							 M2 Client ID : <u>".$form_data[m2clientid]."</u><br>
							 M2 Client Name : <u>".$rClient[m2clientname]."</u><br>
							 Date Disbursed : <u>".$form_data[Ddate]."</u><br>
							 Date of Interview : <u>".$form_data[Ndate]."</u><br>
							 Product : <u>".$form_data[product]."</u><br>
							 Years of Stay : <u>".$form_data[stay]."</u><br>
							 Loan Cycle : <u>".$form_data[cycle]."</u><br>
							 # of Household Living With : <u>".$form_data[noofhousehold]."</u><br>
							 <hr>
							 No of Businesses : <u>".$form_data[busnum]."</u><br>
							 Start Up : <u>".$form_data[starting]."</u><br>
							 Existing : <u>".$form_data[existing]."</u>
							</div></div>";
				
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");

		return $objResponse;
	}

?>