<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("UPDATE asset_circulation_header SET is_deleted = '1' WHERE ach_id='$ch'");
			//$options->insertAudit($ch,'petty_cash_id','D');
		}
	  }
	}
?>
<script>
	xajax.callback.global.onRequest = function(){toggleBox('demodiv',1);}
	xajax.callback.global.beforeResponseProcessing = function(){toggleBox('demodiv',0);}
</script>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search" class="buttons" />
        <input type="button" name="b" value="Add Circulation" onclick="xajax_new_acform();" class="buttons" />		
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
		&nbsp; &nbsp;
		<input type="button" name="b" value="Generate Circulation Report" onclick="window.location.href='admin.php?view=8de0514b2e475aec428a'" class="buttons" />
		<input type="button" name="b" value="Generate Item Report" onclick="window.location.href='admin.php?view=a55cc1d4ead7b4522562'" class="buttons" />
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
					projects s, employee e, asset_circulation_header ac
				where
					(e.employee_lname like '%$keyword%' OR s.project_name like '%$keyword%') AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id AND ac.is_deleted != '1'				
				order by 
					ac.ach_id DESC
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
		  <td width="20"></td>
		  <td width="20"></td>
		  <td>From Project</td>
		  <td>To Project</td>
          <td>Employee</td>		 
		  <td>Date Added</td>		
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$ach_id		= $r['ach_id'];
				$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
				$from_project_name		= $r['project_name'];								
				$to_project_id		= $r['to_project_id'];
				$dateadded = date("M d, Y",strtotime($r['date_added']));
				
				$pto = "SELECT * FROM projects WHERE project_id = '$to_project_id'";
				$rs_pto = mysql_query($pto);
				$num_pto = mysql_num_rows($rs_pto);
				if($num_pto > 0)
				{
					$rw_pto = mysql_fetch_assoc($rs_pto);
					$to_project_name = $rw_pto['project_name'];
				}else{
					$to_project_name = '--';
				}
								
			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$ach_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="#" onclick="xajax_edit_acform('<?=$ach_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
					<td width="15"><a href="#" onclick="xajax_rec_itemform('<?=$ach_id?>');" title="Add/Receive Item"><img src="images/add.png" border="0"></a></td>
					<td width="15"><a href="admin.php?view=942d7963f16566b7a4a0&headerid=<?php echo $ach_id; ?>" title="List of Items"><img src="images/duplicate.png" border="0"></a></td>
                    <td><?=$from_project_name?></td>
					<td><?=$to_project_name?></td>
					<td><?=$employee?></td>					
					<td><?=$dateadded?></td>					
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