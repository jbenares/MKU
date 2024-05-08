<?php

	$b = $_REQUEST['b'];
	$folderID = $_REQUEST['folderID'];
	$policy_filename = $_REQUEST['policy_filename'];
	$folderName = $_REQUEST['folderName'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
	  
	  	$i=0;
		foreach($checkList as $ch) {	
			mysql_query("delete from folder_contents where policyID='$ch'");
						
			if(file_exists("My_Uploads/policies/$policy_filename[$i]")) unlink("My_Uploads/policies/$policy_filename[$i]");
			$i++;
		}
	  }
	}
	else if($b=='Add File') {
		$policy = $_FILES['policy'];
		
		if(!empty($policy[size])) {
			$policy_filename =  $upload->upload_img($policy[size], $policy[type], $policy[tmp_name], $policy[name], "policies", "");
			
			$id = date("Ymd-his");
			
			$sql = "insert into folder_contents set
							policyID='$id',
							policy_filename='$policy_filename',
							folderID='$folderID',
							uploaded_when=SYSDATE()";
					
			$query = mysql_query($sql);
			
			if($query) header("location: admin.php?view=$view&folderName=$folderName&folderID=$folderID");
		}
		else
			$msg = "Please choose a file to upload.";
	}

?>
<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/book.png'> <?=strtoupper($folderName." FOLDER CONTENTS");?></div>
    <div class="module_actions">
    	<?php
			$get_view = mysql_query("select view_keyword from programs where Pfilename='policies.php'");
			$rW = mysql_fetch_array($get_view);
		?>
        <input type="button" name="b" value="Go Back to Folders" onclick="window.location='admin.php?view=<?=$rW[view_keyword];?>'" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <div class="module_actions">
    	<input type="file" name="policy" class="textbox"  onmouseover="Tip('Choose a file by clicking on Browse button and <br>then click on Add File button to upload.');"/>
        <input type="submit" name="b" value="Add File" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
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
						uploaded_when desc";
								
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
            	<p align="left">Files are listed from upload date/time latest to oldest.</p>
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
        <tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td></td>
          <td width="200"><b>Filename</b></td>
          <td><b>Upload Date/Time</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
						
				echo '<td>
						<input type="checkbox" name="checkList[]" value="'.$r[policyID].'" onclick="document._form.checkAll.checked=false">
						<input type="hidden" name="policy_filename[]" value="'.$r[policy_filename].'">
					  </td>';
				
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