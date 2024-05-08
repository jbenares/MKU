<style type="text/css">
	.ui-widget-header{
		padding:6px;
		margin-top:0px;
		margin-bottom:0px;
	}
	.ui-widget-header h3{
		padding:0px;
		margin:0px;	
	}
	.ui-widget-content{
		padding:6px;	
	}
	.ui-widget-content ul{
		margin-left:20px;
	}
</style>

<?php
$b = $_REQUEST['b'];
$checkList = $_REQUEST['checkList'];
$keyword = $_REQUEST['keyword'];

if($b=='Delete Selected') {
  if(!empty($checkList)) {
	foreach($checkList as $ch) {	
		mysql_query("update dtr set dtr_void = '1' where dtrID = '$ch'") or die (mysql_error());
	}
  }
}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search" class="buttons" />
        <input type="button" name="b" value="Add" onclick="xajax_new_dtr_form();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
        <!--<a href="print_scope_of_work_summary.php" target="new"><input type="button" value="Print Summary" /></a>-->
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    
    <?php
	$page = $_REQUEST['page'];
	if(empty($page)) $page = 1;
	 
	$limitvalue = $page * $limit - ($limit);

	$sql = "
		select * from dtr as d,
				employee as e
				 where 
				 d.employeeID = e.employeeID and
				( e.employee_lname like '%$keyword%' OR e.employee_fname like '%$keyword%' OR e.employee_mname like '%$keyword%') and
				 d.dtr_void = '0' order by d.date_encoded desc
					";
	
	$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
			
	$i=$limitvalue;
	$rs = $pager->paginate();
	?>
    <div class="pagination">
	    <?=$pager->renderFullNav($view)?>
    </div>
    <div style="padding:3px;">            
		<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
			<tr bgcolor="#C0C0C0">		
			    <th width="20"></th>
                <!--<th width="20"></th>-->
                <th>Employee</th>
                <th>Remarks</th>
                <th>Period From</th>
                <th>Period To</th>
                <th>Overtime Hr/s</th>
                <th>Legal Holiday</th>
                <th>Special Holiday</th>
            </tr> 
		<?php								
        while($r=mysql_fetch_assoc($rs)) {
			$dtrID 	= $r['dtrID'];
			
				$list = $options->list_dtr_entries($dtrID);
				foreach($list as $list_item){
					
				$emp = $list_item['employeeID'];
				$q = mysql_query("Select * from employee where employeeID = '$emp'") or die (mysql_error());
				$f = mysql_fetch_assoc($q);
					
				
				if($r['legal_holiday'] == 0){
					$legal = "NO";
				}else{
					$legal = "YES";
				}
				
				if($r['special_holiday'] == 0){
					$special = "NO";
				}else{
					$special = "YES";
				}
				
			?>
                <tr>
					<td><input type="checkbox" name="checkList[]" value="<?=$list_item['dtrID']?>" onclick="document._form.checkAll.checked=false"></td>
					<!--<td><a href="#" onclick="xajax_edit_dtr_form('<?=$list_item['dtrID']?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>-->
					<td><?=$f['employee_lname'],', ',$f['employee_fname'],' ',$f['employee_mname']?></td>
					<td><?=$list_item['remarks']?></td>
					<td><?=date("F d, Y", strtotime($list_item['period_from']))?></td>
					<td><?=date("F d, Y", strtotime($list_item['period_to']))?></td>
					<td><?=$list_item['overtime_hr']?></td>
					<td><?=$legal?></td>
					<td><?=$special?></td>
                </tr>
           	<?php
				}
        }
        ?>
		</table>
        <div class="pagination">
            <?=$pager->renderFullNav($view)?>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
	j(function(){
		j('.accordion .head').click(function() {
			j(this).parent().next().toggle('slow');
			return false;
		}).next().hide();
	});
</script>