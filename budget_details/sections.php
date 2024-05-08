<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("UPDATE sections SET is_deleted = '1' WHERE section_id='$ch'");
			//$options->insertAudit($ch,'section_id','D');
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Section" class="buttons" />
        <input type="button" name="b" value="Add Section" onclick="xajax_new_sectionform();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);
	
		$sql = "
				select
					*
				from
					sections s, work_category w, projects p
				where
					s.section_name like '%$keyword%' AND w.work_category_id = s.work_category_id AND p.project_id = s.project_id AND s.is_deleted != '1'
				";
		
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
	?>
    <div class="pagination">
	<?php
        echo $pager->renderFullNav($view);
    ?>
    </div>
    <table cellspacing="2" cellpadding="5" width="100%" align="left" class="search_table">
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
		  <td>Section Code</td>
		  <td>Section Name</td>
		  <td>Section Description</td>
          <td>Project Name</td>
		  <td>Work Category</td>                   
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$section_id		= $r['section_id'];
				$project_name		= $r['project_name'];
				$section_code 	= $r['section_code'];
				$section_name 	= $r['section_name'];
				$section_description 	= $r['section_description'];
				$work	= $r['work'];				

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$section_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="#" onclick="xajax_edit_sectionform('<?=$section_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
					<td><?=$section_code?></td>
					<td><?=$section_name?></td>
					<td><?=$section_description?></td>
                    <td><?=$project_name?></td>
					<td><?=$work?></td>                    
				</tr>
      	<?php
			}
        ?>
    </table>
    <div class="pagination">
	<?php
        echo $pager->renderFullNav($view);
    ?>
    </div>
    </div>
</div>
</form>