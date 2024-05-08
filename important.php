<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/email.png'> IMPORTANT POSTS</div>
    <div class="module_actions">
    	<textarea name="imptmsg" class="textbox2"></textarea>
        <input type="submit" name="b" value="Post Your Message" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$b = $_REQUEST['b'];
			$imptmsg = $_REQUEST['imptmsg'];
			$page = $_REQUEST['page'];
			
			if(empty($page)) $page = 1;
			
			if(!empty($imptmsg)) {
				$id = date("Ymd-his");
				
				$sql = "insert into wall set
							id='$id',
							wallmsg='$imptmsg',
							date_posted=SYSDATE(),
							posted_by='$registered_userID',
							important='1'";
			
				$query = mysql_query($sql);
			}
			 
			$limitvalue = $page * $limit - ($limit);
			
			$sql = "select
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
						w.date_posted desc";
			
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
        <tr bgcolor="#C0C0C0">				
            <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
            <td><b>Name</b></td>    
            <td><b>Date Posted</b></td>    
        </tr> 
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
						
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width=20><input type="checkbox" name="checkList[]" value="'.$r[userID].'" onclick="document._form.checkAll.checked=false"></td>';
				
				echo '<td width=300 onmouseover="Tip(\''.$r[wallmsg].'\');">'.$r[fullname].' ('.$r[name].')</td>';
				echo '<td>'.$options->convert_sysdate($r[date_posted]).'</td>';							
								
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