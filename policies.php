<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			$check_tx = mysql_query("select policyID from folder_contents where folderID='$ch'");
	
			if(mysql_num_rows($check_tx)==0) {
				mysql_query("delete from folders where folderID='$ch'");
			}
			else $msg = "Cannot delete selected, there are files found inside the folder!";
		}
	  }
	}

?>
<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/book.png'> MANAGE FOLDERS AND CONTENTS</div>
    <div class="module_actions">
    	<input type="button" name="b" value="Add Folder" onclick="xajax_new_folderform();toggleBox('demodiv',1);" class="buttons" />
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
						folderID,
						folderName,
						folderdescription
					from
						folders
					order by
						folderName";
					
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="6" align="left">
            	<p align="left">1. You may added folders by clicking on the <b>Add Folder</b> button.<br />
               2. <b>Delete Selected</b> button will only remove folders without any contents.</p>
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
          <td width="200"><b>Folder</b></td>
          <td><b>Description</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
						
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[folderID].'" onclick="document._form.checkAll.checked=false"></td>';
				
				$get_view = mysql_query("select view_keyword from programs where Pfilename='folder_contents.php'");
				$rW = mysql_fetch_array($get_view);
				
				echo '<td width="15" title="Open Folder"><a href="admin.php?view='.$rW[view_keyword].'&folderName='.$r[folderName].'&folderID='.$r[folderID].'"><img src="images/folder.png" border="0"></a></td>';
					
				echo '<td width="15"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_edit_folderform(\''.$r[folderID].'\');toggleBox(\'demodiv\',1);"><img src="images/edit.gif" border="0"></a></td>';
				
				echo '<td>'.$r[folderName].'</td>';	
				echo '<td>'.$r[folderdescription].'</td>';				
								
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