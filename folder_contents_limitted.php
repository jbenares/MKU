<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/book.png'> <?=strtoupper($folderName." FOLDER CONTENTS");?></div>
    <div class="module_actions">
    	<?php
			$get_view = mysql_query("select view_keyword from programs where Pfilename='policies_limitted.php'");
			$rW = mysql_fetch_array($get_view);
		?>
        <input type="button" name="b" value="Go Back to Folders" onclick="window.location='admin.php?view=<?=$rW[view_keyword];?>'" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			$sort = $_REQUEST['sort'];
			if(empty($page)) $page = 1;
			if(empty($sort)) $sort = "uploaded_when desc";
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						policyID,
						policy_filename,
						policy_description,
						uploaded_when
					from
						folder_contents
					where
						folderID='$folderID'
					order by
						$sort";
				
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
          <td width="20"><b>#</b></td>
          <td></td>
          <td width="200"><b><a href="admin.php?view=<?=$view;?>&sort=policy_filename&folderID=<?=$folderID;?>">Filename</a></b></td>
          <td><b><a href="admin.php?view=<?=$view;?>&sort=uploaded_when desc&folderID=<?=$folderID;?>">Upload Date/Time</a></b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				
				echo '<td width="15" title="Download File"><a href="My_Uploads/policies/'.$r[policy_filename].'"><img src="images/icon_download.gif" border="0"></a></td>';
				
				echo '<td>'.$r[policy_filename].'</td>';	
				echo '<td>'.$options->convert_sysdate($r[uploaded_when]).'</td>';				
								
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