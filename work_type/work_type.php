<?php
	
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			//echo $ch;
			mysql_query("UPDATE work_type SET is_deleted = '1' WHERE work_code_id='$ch'") or die (mysql_error());
			//$options->insertAudit($ch,'section_id','D');
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<!--<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>-->
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Work Type" class="buttons" />
        <input type="button" name="b" value="Add Work Type" onclick="xajax_new_worksection();" class="buttons" />
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
					work_type wt, work_category w
				where
					wt.company_code like '%$keyword%' AND w.work_category_id = wt.work_cat_id AND wt.is_deleted != '1'
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
		  <td>Work Code</td>
          <td>Description</td>
		  <td>Work Category</td>	      
		  <td>Unit</td>
	      <td>Price Per Unit</td>		  
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$work_code_id = $r['work_code_id'];
				$company_code		= $r['company_code'];
				$description		= $r['description'];
				$work 	= $r['work'];				
				$unit = $r['unit'];
				$price_per_unit = $r['wt_price_per_unit'];

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$work_code_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="#" onclick="xajax_edit_workform('<?=$work_code_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
					<td><?=$company_code?></td>
                    <td><?=$description?></td>
					<td><?=$work?></td>					
					<td><?=$unit?></td>
					<td><?=number_format($price_per_unit,2)?></td>
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