<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from projects where project_id='$ch'");
			$options->insertAudit($ch,'project_id','D');
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search Project" class="buttons" />
        <input type="button" name="b" value="Add Project" onclick="xajax_new_projectform();" class="buttons" />
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
					projects
				where
					(
					
					project_name like '%$keyword%' 
						or
					project_code like '%$keyword%'
					
					)
				order by project_name asc
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
          <td>Project Code</td>
		  <td>Type</td>
          <td>Name</td>
          <td>Location</td>
          <td>Owner</td>
          <td>Contract Amount</td>
		  <td>Status</td>
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$project_id		= $r['project_id'];
				$project_name 	= $r['project_name'];
				$project_code	= $r['project_code'];
				$location		= $r['location'];
				$owner			= $r['owner'];
				$contract_amount	= $r['contract_amount'];

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$project_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="#" onclick="xajax_edit_projectform('<?=$project_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
                    <td><?=$project_code?></td>
					<td><?=$options->getAttribute("project_type","project_type_id",$r['project_type_id'],"project_type")?></td>
                    <td><?=$project_name?></td>
                    <td><?=$location?></td>
                    <td><?=$owner?></td>
                    <td class="align-right"><?=number_format($contract_amount,2,'.',',')?></td>
					<td><?=$options->getProjectStatusName($r['pstatus'])?></td>
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