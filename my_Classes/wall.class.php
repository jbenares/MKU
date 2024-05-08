<?php

	function postWall($form_data) {
		$objResponse = new xajaxResponse();

		if(!empty($form_data[wallmsg]) && !empty($form_data[userID])) {

			$id = date("Ymd-his");

			$sql = "insert into wall set
					id='$id',
					wallmsg='$form_data[wallmsg]',
					date_posted=SYSDATE(),
					posted_by='$form_data[userID]'";

			$query = mysql_query($sql);

			$objResponse->script("document.wallform.wallmsg.value='';");
			$objResponse->script("document.wallform.Ndate.value='".date("m/d/Y")."';");
			$objResponse->script("xajax_update_wall(document.getElementById('Ndate').value)");
			$objResponse->script("toggleBox('demodiv',0)");
		}
		else {
			$objResponse->script("toggleBox('demodiv',0)");
		}

		return $objResponse;
	}

	function update_wall($now) {
		$objResponse = new xajaxResponse();

		$options = new options();

		$now_array = explode("/", $now);
		$now = $now_array[2].'-'.$now_array[0].'-'.$now_array[1];

		$getR = mysql_query("select
					w.id,
					w.wallmsg,
					w.date_posted,
					concat(a.user_lname,', ',a.user_fname) as fullname,
					at.name
				from
					wall as w,
					admin_access as a,
					access_type as at
				where
					w.important='0' and
					w.posted_by=a.userID and
					a.access=at.id and
					w.date_posted like '$now%'
				order by
					w.date_posted desc
				limit 0,50");

		$row = '<table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">
					<tr>
						<td colspan=2><b>Posts as of '.$options->convert_sysdate($now).'</b><hr></td>
					</tr>';

		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr>';

			$row .= '<td width="38" valign="top"><img src="images/comment.png"></td>';
			$row .= '<td style="border-bottom: 1px solid #EEEEEE;" valign="top"><b><u>'.$r[fullname].'</u> ('.$r[name].')</b><br>'.$options->convert_sysdate($r[date_posted]).'<p>'.$r[wallmsg].' </p></td>';

			$row .= '</tr>';
		}

		$getR = mysql_query("select
					w.id,
					w.wallmsg,
					w.date_posted,
					concat(a.user_lname,', ',a.user_fname) as fullname,
					at.name
				from
					wall as w,
					admin_access as a,
					access_type as at
				where
					w.important='0' and
					w.posted_by=a.userID and
					a.access=at.id and
					w.date_posted < '$now%'
				order by
					w.date_posted desc
				limit 0,50");

		$row .= '</table><br>';

		$row .= '<table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">
					<tr>
						<td colspan=2><b>Posts before '.$options->convert_sysdate($now).'</b><hr></td>
					</tr>';

		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr>';

			$row .= '<td width="38" valign="top"><img src="images/comment2.png"></td>';
			$row .= '<td style="border-bottom: 1px solid #EEEEEE;" valign="top"><b><u>'.$r[fullname].'</u> ('.$r[name].')</b><br>'.$options->convert_sysdate($r[date_posted]).'<p>'.$r[wallmsg].' </p></td>';

			$row .= '</tr>';
		}

		$row .= '</table><br>';

		$objResponse->assign("wall_div","innerHTML", $row);
		$objResponse->script("setTimeout(\"xajax_update_wall(document.getElementById('Ndate').value)\",30000)");
		$objResponse->script("xajax_update_impt()");

		return $objResponse;
	}

	function update_impt() {
		$objResponse = new xajaxResponse();

		$options = new options();

		$getR = mysql_query("select
					w.id,
					w.wallmsg,
					w.date_posted,
					concat(a.user_lname,', ',a.user_fname) as fullname,
					at.name
				from
					wall as w,
					admin_access as a,
					access_type as at
				where
					w.important='1' and
					w.posted_by=a.userID and
					a.access=at.id
				order by
					w.date_posted desc");

		//$objResponse->addAlert(mysql_error());

		$row = '<table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">';

		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr>';

			$row .= '<td style="border-bottom: 1px solid #EEEEEE;" valign="top"><b><u>'.$r[fullname].'</u> ('.$r[name].')</b><br>'.$options->convert_sysdate($r[date_posted]).'<p>'.$r[wallmsg].'</p></td>';

			$row .= '</tr>';
		}

		$row .= '</table><br>';

		$objResponse->assign("important_div","innerHTML", $row);

		return $objResponse;
	}

	function notifications($now) {
		$objResponse = new xajaxResponse();

		$options = new options();

		//$objResponse->alert($now);

		$getR = mysql_query("select
					w.id,
					w.wallmsg,
					w.date_posted,
					concat(a.user_lname,', ',a.user_fname) as fullname,
					at.name
				from
					wall as w,
					admin_access as a,
					access_type as at
				where
					w.important='0' and
					w.posted_by=a.userID and
					a.access=at.id and
					w.date_posted like '$now%'
				order by
					w.date_posted desc
				limit 0,10");

		$row = '<div style="height:340px;width:700px;overflow-y:scroll;"><table cellspacing="1" cellpadding="5" width="98%" align="center" class="search_table">
					<tr>
						<td colspan=2><img src="images/9.gif"> <b>TOP 10 POSTS OF THE DAY</b><hr></td>
					</tr>';

		while($r=mysql_fetch_array($getR)) {
			$row .= '<tr>';

			$row .= '<td width="38" valign="top"><img src="images/comment.png"></td>';
			$row .= '<td style="border-bottom: 1px solid #EEEEEE;" valign="top"><b><u>'.$r[fullname].'</u> ('.$r[name].')</b><br>'.$options->convert_sysdate($r[date_posted]).'<p>'.$r[wallmsg].' </p></td>';

			$row .= '</tr>';
		}

		$row .= '</table></div>';

		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->assign("Rdiv","innerHTML", $row);
		$objResponse->script("showBox()");

		return $objResponse;
	}

?>
