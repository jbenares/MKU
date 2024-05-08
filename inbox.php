<form action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/email.png'> PRIVATE MESSAGES</div>
    <div class="module_actions">
        <input type="submit" name="b" value="Show All" class="buttons" />
        <input type="submit" name="b" value="Show Unread" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$b = $_REQUEST['b'];
		
			if(empty($b)) $b = 'Show All';
		
			if($b=='Show All') {
				$sql = "select
						pmr.id as pmrid,
						pmr.pmID,
						pmr.read_,
						pm.date_sent
					from
						pmrecipients as pmr,
						private_messages as pm
					where
						pmr.userID='$registered_userID' and
						pmr.pmID=pm.id
					order by
						pm.date_sent desc";
			}
			else if($b=='Show Unread') {
				$sql = "select
						pmr.id as pmrid,
						pmr.pmID,
						pmr.read_,
						pm.date_sent
					from
						pmrecipients as pmr,
						private_messages as pm
					where
						pmr.userID='$registered_userID' and
						pmr.read_='Unread' and
						pmr.pmID=pm.id
					order by
						pm.date_sent desc";
			}
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				$getMsgDetails = mysql_query("select
												a.userID,
												concat(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,
												pm.subject,
												pm.privatemsg,
												pm.date_sent,
												pm.sent_by
											from
												private_messages as pm,
												admin_access as a
											where
												pm.sent_by=a.userID and
												pm.id='$r[pmID]'");

				$MsgDetails = mysql_fetch_array($getMsgDetails);
		
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="15" title="Read Message"><img src="images/page_text.gif" style="cursor:pointer;" onclick=xajax_read_pm(\''.$r[pmrid].'\',\''.$i.'\');toggleBox(\'demodiv\',1);></td>';
				
				if($r[read_]=="Unread") {
					echo '<td width="300" id="tdname'.$i.'"><b>'.$MsgDetails[fullname].'</b> <img src="images/new.png"></td>';
					echo '<td id="tdsubject'.$i.'"><b>'.$MsgDetails[subject].'</b></td>';
					echo '<td id="tddate'.$i.'" width="200"><b>'.$options->convert_sysdate($MsgDetails[date_sent]).'</b></td>';
				}
				else {
					echo '<td width="300">'.$MsgDetails[fullname].'</td>';
					echo '<td>'.$MsgDetails[subject].'</td>';
					echo '<td width="200">'.$options->convert_sysdate($MsgDetails[date_sent]).'</td>';
				}						
								
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>