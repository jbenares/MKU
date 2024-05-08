<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/book.png'> POLICIES</div>
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
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
        <tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td></td>
          <td width="200"><b>Folder</b></td>
          <td><b>Description</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				
				$get_view = mysql_query("select view_keyword from programs where Pfilename='folder_contents_limitted.php'");
				$rW = mysql_fetch_array($get_view);
				
				echo '<td width="15" title="Open Folder"><a href="admin.php?view='.$rW[view_keyword].'&folderName='.$r[folderName].'&folderID='.$r[folderID].'"><img src="images/folder.png" border="0"></a></td>';
				
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