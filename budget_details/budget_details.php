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
	}else if($b=='Generate Report'){
		header('location: admin.php?view=1ee1b9cd9d1bd63f04db');
	}else if($b=='Material Budget Report'){
		
	}
?>
<script type="text/javascript" src="js/test.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Budget" class="buttons" />        
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
		<input type="submit" name="b" value="Generate Report" class="buttons" />
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
					budget_header b,
					projects p,
					work_category w
				where
					(
					b.budget_header_id like '%$keyword%' or
					p.project_name like '%$keyword%'
					) AND
					w.work_category_id = b.work_category_id AND 
					p.project_id = b.project_id
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
		  <td>Budget #</td>		  
          <td>Project Name</td>
		  <td>Work Category</td> 
		  <td>Sub Work Category</td> 		  
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$budget_header_id	= addslashes($r['budget_header_id']);
				$project_name		= $r['project_name'];
				$section_name 	= $r['section_name'];
				$work	= $r['work'];
				$sub_cat_id = $r['sub_work_category_id'];
					
				$subquery="select * from work_category where work_category_id='$sub_cat_id'";
				$sub_q=mysql_query($subquery);
				$fetch =mysql_fetch_assoc($sub_q);
				$subw = $fetch['work'];

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$budget_header_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="javascript:void(0);" onclick="xajax_add_details('<?=$budget_header_id;?>');" title="Add Section Details">
					<img src="images/edit.gif" border="0"></a></td>
					<td><?=str_pad($budget_header_id,7,"0",STR_PAD_LEFT)?></td>					
                    <td><?=$project_name?></td>
					<td><?=$work?></td>
					<td><?=$subw?></td>                     
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