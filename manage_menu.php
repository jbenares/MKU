<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$menu_keyword = $_REQUEST['menu_keyword'];
	
	if($b=='Save Status') {
		$status = $_REQUEST['status'];
		$M_id = $_REQUEST['M_id'];
		
		$i=0;
		foreach($status as $s) {
			mysql_query("update menu set enable='$s' where M_id='$M_id[$i]'");
			
			$i++;
		}
	}
	else if($b=='Delete Selected') {	
		if(!empty($checkList)) {		  
			foreach($checkList as $ch) {			
				mysql_query("delete from menu where M_id='$ch'");
			}
		}
	}

?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/application_cascade.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="menu_keyword" class="textbox" value="<?=$menu_keyword;?>" />
        <input type="submit" name="b" value="Search Menu" class="buttons" />
        <input type="submit" name="b" value="Display All Entries" class="buttons" />
    </div>
    <div class="module_actions">
        <input type="button" name="b" value="Add Menu Item" onclick="xajax_new_menuform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Save Status" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
			
			if($b=='Search Menu') {
				$sql = "select
						M_id,
						Mname,
						level,
						icon_filename,
						PCode,
						enable,
						placement
					from
						menu as m
					where
						Mname like '%$menu_keyword%' or
						level='$menu_keyword'
					order by
						level, placement, Mname";
			}
			else {
				$sql = "select
						M_id,
						Mname,
						level,
						icon_filename,
						PCode,
						enable,
						placement
					from
						menu as m
					order by
						level, placement, Mname";
			}
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="7" align="left">
            	<ol>
                	<li>2nd Level Parent menu will not appear if no associated file is selected.</li>
                    <li><b>Status</b> is used to enable/disable menu item.</li>
                </ol>
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td></td>
          <td width="175"><b>Menu Title</b></td>
          <td width="50"><b>Menu Level</b></td>
          <td width="200"><b>Icon</b></td>
          <td width="150"><b>View Keyword</b></td>
          <td width="50"><b>Placement</b></td>  
          <td><b>Status</b></td>        
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				$getviewKey = mysql_query("select view_keyword from programs where PCode='$r[PCode]'");
				$rKey = mysql_fetch_array($getviewKey);
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[M_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_edit_menuform(\''.$r[M_id].'\');"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[Mname].'</td>';
				echo '<td>'.$r[level].'</td>';
				
				if(!empty($r[icon_filename]))
					echo '<td>images/'.$r[icon_filename].' <img src="images/'.$r[icon_filename].'"></td>';
				else
					echo '<td></td>';
					
				echo '<td>'.$rKey[view_keyword].'</td>';
				echo '<td>'.$r[placement].'</td>';				
				
				echo '<td>';
					echo '<input type="hidden" name="M_id[]" value="'.$r[M_id].'">';
					
					if($r[enable]=='1') {
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
        <input type="button" name="b" value="Add Menu Item" onclick="xajax_new_menuform();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Save Status" onclick="return approve_confirm();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
</div>
</form>