<?php
	
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Labor Budget') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			//echo $ch;
			mysql_query("UPDATE labor_budget SET is_deleted = '1' WHERE id='$ch'") or die (mysql_error());
			//$options->insertAudit($ch,'section_id','D');
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<!--<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>-->
	<div class="module_title"><img src='images/user_orange.png'>PURCHASE REQUEST | LABOR</div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox3" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search PR #" class="buttons" />
        <input type="button" name="b" value="Add PR Labor" onclick="window.location.href='admin.php?view=f4e9cf5b43526307214f'" class="buttons" />
        <input type="submit" name="b" value="Delete Labor Budget" onclick="return approve_confirm();" class="buttons" />
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
						pr_header h, work_category w, projects p
				where
						h.project_id = p.project_id
					and
						h.work_category_id = w.work_category_id
					and
						h.type = 'labor'
					and
						(h.pr_header_id like '%$keyword%' or p.project_name like '%$keyword%')
													
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
		  <td>PR #</td>
		  <td>Project</td>
		  <td>Work Category</td>
		  <td>Sub Work Category</td>		  
		  <td>Descriptiom</td>	      
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$id = $r['id'];
				$pr_header_id = $r['pr_header_id'];
				$projects_name = $r['project_name'];
				$description		= $r['description'];
				$date		= $r['date'];
				$work 	= $r['work'];
				$sub 	= $r['sub_work_category_id'];				

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="admin.php?view=f4e9cf5b43526307214f&pr_header_id=<?php echo $pr_header_id; ?>" title="Edit Details"><img src="images/edit.gif" border="0"></a></td>
					<td><?=$pr_header_id?></td>
					<td><?=$projects_name?></td>
					<td><?=$work?></td>
					<td><?=$options->getAttribute('work_category','work_category_id',$sub,'work')?></td>					
					<td><?=$description?></td>					
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