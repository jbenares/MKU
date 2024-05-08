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
		mysql_query("update employee_type set employee_type_void = '1' where employee_type_id = '$ch'") or die (mysql_error());
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
        <input type="button" name="b" value="Add" onclick="xajax_new_employee_type_form();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
        <!--<a href="print_scope_of_work_summary.php" target="new"><input type="button" value="Print Summary" /></a>-->
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    
    <?php
	$page = $_REQUEST['page'];
	if(empty($page)) $page = 1;
	 
	$limitvalue = $page * $limit - ($limit);

	$sql = "
		select * from employee_type where employee_type like '%$keyword%' and employee_type_void = '0'
	";
	
	$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
			
	$i=$limitvalue;
	$rs = $pager->paginate();
	?>
    <div class="pagination">
	    <?=$pager->renderFullNav($view)?>
    </div>
    <div style="padding:3px;">            
	    <div id="accordion" class="accordion">
		<?php								
        while($r=mysql_fetch_assoc($rs)) {
			$employee_type_id 	= $r['employee_type_id'];
			$employee_type 				= $r['employee_type'];
        ?>		
        	<div class="ui-widget-header">
	            <input type="checkbox" name="checkList[]" value="<?=$r['employee_type_id']?>" onclick="document._form.checkAll.checked=false">
                <a href="#" onclick="xajax_edit_employee_type_form('<?=$r['employee_type_id']?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a>
				<h3 class="head" style="display:inline-block; padding:0px; cursor:pointer;"><?=$employee_type?></h3>
           	</div>
            <div class="ui-widget-content" style="display:none;">
            	<ul>
           	<?php
				$list = $options->list_employee_type($employee_type_id);
				foreach($list as $list_item){
			?>
                    <li>
                        <input type="checkbox" name="checkList[]" value="<?=$list_item['employee_type_id']?>" onclick="document._form.checkAll.checked=false">
                        <a href="#" onclick="xajax_edit_employee_type_form('<?=$list_item['employee_type_id']?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a>
                        <?=$list_item['employee_type']?>
                    </li>
           	<?php
				}
            ?>
            	</ul>
            </div>      
            <?php
            }
            ?>
       	</div>
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