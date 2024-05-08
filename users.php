<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from admin_access where userID='$ch'");
		}
	  }
	}

?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
        <input type="button" name="b" value="Add User" onclick="xajax_new_userform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
					  a.userID,
					  concat(a.user_lname,', ',a.user_fname,' ',a.user_mname) as fullname,
					  a.username,
					  a.email,
					  a.access,
					  a.active,
					  a.membered_since,
					  a.companyID,
					  t.name
					from
					  admin_access as a,
					  access_type as t
					where
					  t.id!='1' and
					  a.access=t.id
					order by
					  t.id,
					  a.user_lname";
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("users&b=$b&key=$key");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="15"></td>
          <td width="15"></td>
          <td><b>Name</b></td>
          <td><b>Access Group</b></td>
          <td><b>Username</b></td>
          <td><b>Status</b></td>     
          <td><b>Membered Since</b></td>
          <td><b>Company</b></td>  
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[userID].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_select_to_manage(\''.$r[userID].'\');toggleBox(\'demodiv\',1);"><img src="images/key.png" border="0"></a></td>';
				echo '<td><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_edit_userform(\''.$r[userID].'\');toggleBox(\'demodiv\',1);"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[fullname].'<br>'.$r[email].'</td>';
				echo '<td>'.$r[name].'</td>';
				echo '<td>'.$r[username].'</td>';
				
				if($r[active]=='1')
					echo '<td>Active</td>';
				else
					echo '<td><font color=#FF0000>Inactive</font></td>';		
					
				echo '<td>'.$options->convert_sysdate($r[membered_since]).'</td>';		
				
				$getComp	= mysql_query("select * from companies where companyID='$r[companyID]'");
				$rComp 	= mysql_fetch_array($getComp);
				
				echo '<td>'.$rComp[company_abbrevation].'</td>';		
								
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("users&b=$b&key=$key");
                ?>
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>