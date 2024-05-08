<form action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/email.png'> SENT MESSAGES</div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						id,
						subject,
						date_sent,
						privatemsg
					from
						private_messages
					where
						sent_by='$registered_userID'
					order by
						date_sent desc";
			
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
	
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="15"><img src="images/star.png" style="cursor:pointer;" title="Show Recipients	" onclick="xajax_view_recipients(\''.$r[id].'\');toggleBox(\'demodiv\',1);"></td>';
				
				echo '<td width="300">'.$r[subject].'</td>';
				//echo '<td>'.str_replace("\r\n","<br>", $r[privatemsg]).'</td>';
				echo '<td>'.$options->convert_sysdate($r[date_sent]).'</td>';				
								
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