<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from groups where groupID='$ch'");
			mysql_query("delete from group_members where groupID='$ch'");
			echo mysql_error();
		}
	  }
	}

?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/group.png'> MESSAGING GROUPS</div>
    <div class="module_actions">
        <input type="button" name="b" value="Add Messaging Group" onclick="xajax_new_groupform();toggleBox('demodiv',1);" class="buttons" />
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
						groupID,
						name,
					  	description
					from
						groups";
					
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="6" align="left">
            	<p align="left">* <b>Delete Selected</b> button will remove a group and its group members.</p>
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td></td>
          <td></td>
          <td width="300"><b>Name</b></td>
          <td><b>Description</b></td>        
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[groupID].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15" title="Add users to group"><a href="#" onclick="xajax_addUsersToGroup(\''.$r[groupID].'\');toggleBox(\'demodiv\',1);"><img src="images/group_add.png" border="0"></a></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_groupform(\''.$r[groupID].'\');toggleBox(\'demodiv\',1);"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[name].'</td>';
				echo '<td>'.$r[description].'</td>';				
								
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