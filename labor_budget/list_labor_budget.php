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
	<div class="module_title"><img src='images/wrench.png'><?=$transac->getMname($view);?> (LINKED TO LABOR/MATS WORK TYPE)</div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Labor Budget" class="buttons" />
        <input type="button" name="b" value="Add Labor Budget" onclick="window.location.href='admin.php?view=b31a95d16cff7cbb1fe3'" class="buttons" />
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
					labor_budget lb, projects p
				where
					(p.project_name like '%$keyword%'
					 or lb.id like '%$keyword%')
				AND 
					p.project_id = lb.project_id 
				AND 
					lb.is_deleted !='1'
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
		  <th>Budget #</th>
		  <th>Project</th>
		  <th>Work Category</th>
		  <th>Sub Work Category</th>
		  <th>Remarks</th>
	      <th>Date</th>	
		  <th>Status</th>			  
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$id = $r['id'];
				$projects_name = $r['project_name'];
				$remarks		= $r['remarks'];
				$date		= $r['date'];
				//$work 	= $r['work'];	
				$status  = $r['status'];

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="admin.php?view=bc0d9279e99bdefbd0a6&id=<?php echo $id; ?>" title="Edit Details"><img src="images/edit.gif" border="0"></a></td>
					<td><?=str_pad($r[id], 8, "0", STR_PAD_LEFT);?></td>
					<td><?=$projects_name?></td>
					<td><?=$options->getAttribute('work_category','work_category_id',$r[work_category_id],'work')?></td>
					<td><?=$options->getAttribute('work_category','work_category_id',$r[sub_work_category_id],'work')?></td>					
					<td><?=$remarks?></td>
					<td><?=$date?></td>
					<td><?php
						if($status=='S'){
							echo 'Saved';
						}else if($status=='C'){
							echo 'Cancelled';
						}else{
							echo 'Finished';
						}
					?></td>
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