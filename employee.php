<?php
$b = $_REQUEST['b'];
$checkList = $_REQUEST['checkList'];
$keyword = $_REQUEST['keyword'];

if($b=='Delete Selected') {
  if(!empty($checkList)) {
	foreach($checkList as $ch) {
		mysql_query("delete from employee where employee_id ='$ch'") or die (mysql_error());
	}
  }
}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Employee" class="buttons" />
        <input type="button" name="b" value="Add Empoyee" onclick="xajax_new_employeesform();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px;" >
    	<?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;

		$limitvalue = $page * $limit - ($limit);

		$sql = "
			select
				*
			from
				employee
			where
				employee_lname like '%$keyword%'
			or
				employee_fname like '%$keyword%'
			or
				employeeNUM like '%$keyword%'
		";

		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);

		$i=$limitvalue;
		$rs = $pager->paginate();
		?>
        <div class="pagination">
        	<?=$pager->renderFullNav($view);?>
        </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" >
            <tr bgcolor="#C0C0C0">
              <th width="20">#</th>
              <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
              <th width="20"></th>
              <th>Employe No</th>
              <th>Name</th>
              <th>Contact #</th>
            </tr>
			<?php
			$i=1;
			while($r=mysql_fetch_assoc($rs)) {

				echo '<tr>';
				echo '<td width="20">'.$i++.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[employee_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_employeeform(\''.$r[employee_id].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[employee_no].'</td>';
				echo '<td>'."$r[last_name], $r[first_name] $r[middle_name]".'</td>';
				echo '<td>'.$r[contact_no].'</td>';
				echo '</tr>';
			}
    	    ?>
    	</table>
        <div class="pagination">
        	<?=$pager->renderFullNav($view);?>
        </div>
    </div>
</div>
</form>
