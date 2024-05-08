<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from categories where categ_id='$ch'");
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Categories" />
        <input type="button" name="b" value="Add Category" onclick="xajax_new_categoriesform();toggleBox('demodiv',1);" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();"  />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table" style="text-align:left;">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "
				select
					*
				from
					categories
				where
					category like '$keyword%'
				order by
					category asc
				
				";
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav($view);
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td><b>Category</b></td>
          <td><b>Category Code</b></td>
          <td><b>Category Type</b></td>
          <td><b>Level</b></td>
          <td><b>Subcategory</b></td>
          <td><b>Income Account</b></td>
          <td><b>Expense Account</b></td>
          <td><b>Remark</b></td>
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[categ_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_categoriesform(\''.$r[categ_id].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[category].'</td>';	
				echo '<td>'.$r[category_code].'</td>';	
				echo '<td>'.$options->category_type($r[category_type]).'</td>';	
				echo '<td>'.$r[level].'</td>';	
				echo '<td>'.$options->getCategory($r[subcateg_id]).'</td>';	
				echo "<td>".$options->getGchartName($r[income_id])."</td>";
				echo "<td>".$options->getGchartName($r[expense_id])."</td>";
				echo '<td>'.$r[remark].'</td>';	
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav($view);
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>