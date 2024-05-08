<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$file_keyword = $_REQUEST['file_keyword'];
	$dir_plugin = $_REQUEST['dir_plugin'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
	  
		foreach($checkList as $ch) {			
			$getFilename = mysql_query("select Pfilename from programs where PCode='$ch'");
			$rF = mysql_fetch_array($getFilename);
			
			unlink($rF[Pfilename]);
			
			mysql_query("delete from programs where PCode='$ch'");
		}
	  }
	}
	else if($b=='Generate New URL parameters') {
		$qwe = mysql_query("select PCode, Pfilename from programs where Pfilename!='home.php'");
		 while($r=mysql_fetch_array($qwe)) {
			$newview = md5($r[Pfilename].date(ymdhis));
			
			mysql_query("update programs set view_keyword='$newview' where PCode='$r[PCode]'");
		 }
	}
	else if($b=='Save Status') {
		$status = $_REQUEST['status'];
		$protect = $_REQUEST['protect'];
		$PCode = $_REQUEST['PCode'];
		$Fdescription = $_REQUEST['Fdescription'];

		$i=0;
		foreach($status as $s) {
			//echo $i.' - Status : '.$s.' - Protect : '.$protect[$i].' - '.$PCode[$i].' - '.$Fdescription[$i].'<br>'; //For debugging
			mysql_query("update programs set enabled='$s', protect='$protect[$i]', Fdescription='$Fdescription[$i]' where PCode='$PCode[$i]'");
			
			$i++;
		}
	}
	else if($b=='Save New Files') {
		$handle = opendir($dir_plugin);

		while (false !== ($file = readdir($handle))) {
			if($dir_plugin!='.') 
				$ffile = $dir_plugin."/".$file;
			else 
				$ffile = $file;

			$chk = mysql_query("select Pfilename from programs where Pfilename='$ffile'");
			
			if(mysql_num_rows($chk)==0) {
				$newview = md5($ffile.date(ymdhis));
				
				$file_array = explode(".", $file);				
				if($file_array[1]!='php') continue;
				//print_r($file_array);

				mysql_query("insert into programs set Pfilename='$ffile', view_keyword='$newview'");
			}
		}

		$getPrograms = mysql_query("select * from programs where Pfilename like 'print%' or Pfilename like '".$dir_plugin."/print%'");

		while($rgP=mysql_fetch_array($getPrograms)) {
			//print_r($rgP);		
			//echo '<br>';
			$protect = mysql_query("update programs set protect='1' where PCode='$rgP[PCode]'");
		}
	}

?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/application_cascade.png'> MANAGE SYSTEM FILES</div>
    <div class="module_actions">
    	<input type="text" name="file_keyword" class="textbox" value="<?=$file_keyword;?>" />
        <input type="submit" name="b" value="Search Files" class="buttons" />
        <input type="submit" name="b" value="Display All Files" class="buttons" />
    </div>
    <div class="module_actions">
        <input type="submit" name="b" value="Save Status" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Generate New URL parameters" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />        
    </div>
    <div class="module_actions">
	<?php
		$handle = opendir(".");

		echo '<select name="dir_plugin" class="select">';

			echo '<option value=".">- - - Choose Plugin Directory - - -</option>';
		
			while (false !== ($file = readdir($handle))) {
				if($file=="." || $file=="..") continue;	

				if(is_dir($file)) echo '<option value="'.$file.'">'.$file.'</option>';			
			}

		echo '</select>';
	?>
	<input type="submit" name="b" value="Save New Files" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		    
			if($b=='Search Files') {			
				$sql = "select
						PCode,
						Pfilename,
						view_keyword,
						enabled,
						protect,
						Fdescription
					from
						programs
					where
						(Pfilename like '%$file_keyword%' or
							view_keyword like '%$file_keyword%')
					order by
						Pfilename";
			}
			else {
				$sql = "select
						PCode,
						Pfilename,
						view_keyword,
						enabled,
						protect,
						Fdescription
					from
						programs
					order by
						Pfilename";
			}
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="9" align="left">
            	<ol>
                	<li><b>Status</b> is used to enable/disable a file for inclusion. Files that are disabled are not listed during granting of privileges.</li>
                    <li><b>Protect</b> is used to restrict listings during granting of privileges.</li>
                </ol>
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td width="200"><b>Filename</b></td>
          <td width="75"><b>Type</b></td>
          <td width="200"><b>View Keyword</b></td> 
          <td width="75"><b>Status</b></td>
          <td width="75"><b>Protected</b></td>
          <td><b>Description</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[PCode].'" onclick="document._form.checkAll.checked=false"></td>';
				
				echo '<td><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_edit_functions(\''.$r[PCode].'\',\''.$r[Pfilename].'\');toggleBox(\'demodiv\',1);" title="Modify Xajax Functions"><img src="images/page_script.gif" border=0></a></td>';
				
				echo '<td>'.$r[Pfilename].'</td>';
				
				$file_array = explode(".", $r[Pfilename]);
				
				if(!empty($file_array[1])) 
					echo '<td>.'.$file_array[1].' File</td>';
				else
					echo '<td>Directory</td>';
									
				echo '<td>'.$r[view_keyword].'</td>';
					
				echo '<td>';
					echo '<input type="hidden" name="PCode[]" value="'.$r[PCode].'">';
					
					if($r[enabled]=='1') {
						echo '<select name="status[]" style="font-size:11px;">';
							echo '<option value=1>Enabled</option>';
							echo '<option value=0>Disabled</option>';
						echo '</select>';
					}
					else {
						echo '<select name="status[]" style="font-size:11px;border:1px #FF0000 solid;">';
							echo '<option value=0>Disabled</option>';
							echo '<option value=1>Enabled</option>';
						echo '</select>';
					}						
											
				echo '</td>';
				
				echo '<td>';
					
					if($r[protect]=='1') {
						echo '<select name="protect[]" style="font-size:11px;border:1px #007FFF solid;">';
							echo '<option value=1>Protected</option>';
							echo '<option value=0>Not Protected</option>';
						echo '</select>';
					}
					else {
						echo '<select name="protect[]" style="font-size:11px;border:1px #FF0000 solid;">';
							echo '<option value=0>Not Protected</option>';
							echo '<option value=1>Protected</option>';
						echo '</select>';
					}
						
				echo '</td>';
				
				echo '<td><input type="text" name="Fdescription[]" value="'.$r[Fdescription].'" class="textbox"></td>';
				
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
    <div class="module_actions">        
        <input type="submit" name="b" value="Save Status" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Save New Files" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Generate New URL parameters" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
</div>
</form>
