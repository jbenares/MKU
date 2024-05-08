<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	$achId = $_REQUEST['id'];
	
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
					projects s, employee e, asset_circulation_header ac, productmaster p, asset_circulation_detail ad
				where
					(p.stock like '%$keyword%') AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id AND ac.is_deleted != '1'
				AND
					ac.ach_id = ad.ach_id AND ad.stock_id = p.stock_id AND ad.status = 'I' AND ac.ach_id = '$achId'
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
          <td width="20"></td>		  
		  <td>Item Name</td>
		  <td>Quantity</td>
          <td>Status</td>		 
		  <td>Date IN</td>
		  <td>From Project</td>
		  <td>To Project</td>		  
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$ach_id		= $r['ach_id'];
				$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
				$from_project_name		= $r['project_name'];								
				$to_project_id		= $r['to_project_id'];
				$stock_name		= $r['stock'];
				$quantity		= $r['quantity'];
				$datereceived = date("M d, Y",strtotime($r['date_received']));
				
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
					<td width="15"><a href="#" onclick="xajax_ret_itemform('<?=$ach_id?>');" title="Return Item"><img src="images/edit.gif" border="0"></a></td>
                    <td><?=$stock_name?></td>
					<td><?=$quantity?></td>
					<td>IN</td>
					<td><?=$datereceived?></td>
					<td><?=$from_project_name?></td>
					<td><?=$to_project_name?></td>
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