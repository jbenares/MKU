<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			$check_tx = mysql_query("select userID from admin_access where access='$ch'");
	
			if(mysql_num_rows($check_tx)==0) {
				mysql_query("delete from access_type where id='$ch'");
			}
			else $msg = "Cannot delete selected, there are users membered in this group!";
		}
	  }
	}
	else if($b=='Clean Privileges Table') {
		$get_priv = mysql_query("select id, PCode from my_privileges");
		
		while($r_priv=mysql_fetch_array($get_priv)) {
			$check_if_in_prog = mysql_query("select Pfilename from programs where PCode='$r_priv[PCode]'");
			$r_in_prog = mysql_fetch_array($check_if_in_prog);
			
			if(mysql_num_rows($check_if_in_prog)==0 && !file_exists($r_in_prog[Pfilename])) {
				mysql_query("delete from my_privileges where id='$r_priv[id]'");
			}
		}
	}

?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/key.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
        <input type="button" name="b" value="Add Access Group" onclick="xajax_new_privilegesform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Clean Privileges Table" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						id,
						name,
						description
					from
						access_type
					where
						id!='1'";
					
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view&b=$b&key=$key");
                ?>
            </td>
        </tr>
        <tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td></td>
          <td></td>
          <td width="200"><b>Name</b></td>
          <td><b>Description</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
						
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15" title="Add privilege to group"><a href="#" onclick="xajax_addPToGroup(\''.$r[id].'\');toggleBox(\'demodiv\',1);"><img src="images/key_add.png" border="0"></a></td>';
			
			
				echo '<td width="15"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_edit_privilegesform(\''.$r[id].'\');toggleBox(\'demodiv\',1);"><img src="images/edit.gif" border="0"></a></td>';
				
				echo '<td>'.$r[name].'</td>';	
				echo '<td>'.$r[description].'</td>';				
								
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view&b=$b&key=$key");
                ?>
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>