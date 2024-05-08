<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("UPDATE petty_cash SET is_deleted = '1' WHERE petty_cash_id='$ch'");
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
        <input type="button" name="b" value="Request Petty Cash" onclick="xajax_new_pcform();" class="buttons" />
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
					petty_cash p, projects s, employee e,employee_contracts as ec
				where
					e.employee_lname like '%$keyword%' AND p.employeeID = e.employeeID AND p.department_id = s.project_id AND p.is_deleted != '1'
				and
					e.employeeID = ec.employeeID
				order by 
					petty_cash_id DESC
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
		  <td>Requested By</td>
          <td>Department</td>
		  <td>Purpose</td>
		  <td>Amount</td>
		  <td>Date Requested</td>
		  <td>Status</td>
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$petty_cash_id		= $r['petty_cash_id'];
				$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
				$project_name		= $r['project_name'];
				$purpose		= $r['purpose'];
				$amount 	= $r['amount'];
				$is_approve	= $r['is_approve'];
				$date_requested	= $r['date_requested'];
				if($is_approve == '0'){$status = '<font color=red><b>Pending</b></font>'; $display = 'style=display:none;'; $disopt = '';}else{$status = '<font color=blue><b>Approved</b></font>'; $display = ''; $disopt = 'style=display:none;';}
				$daterequested = date("M d, Y",strtotime($date_requested));
				
				#Date Approved
				$date_approved	= $r['date_approved'];
				if($date_approved != '0000-00-00 00:00:00'){
					$dateapproved = date("M d, Y",strtotime($date_approved));
				}else{
					$dateapproved = '';
				}

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$petty_cash_id?>" onclick="document._form.checkAll.checked=false" <?php echo $disopt; ?>></td>
                    <td width="15"><a href="#" onclick="xajax_edit_pcform('<?=$petty_cash_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0" <?php echo $disopt; ?>></a></td>
					<td width="15"><a href="petty_cash/print_petty_cash.php?pt_cash=<?php echo $petty_cash_id; ?>" title="Print" target=_blank><img src="images/action_print.gif" border="0" <?php echo $display; ?>></a></td>
					<td><?=$employee?></td>
                    <td><?=$project_name?></td>
					<td><?=$purpose?></td>
					<td><?=number_format($amount,2)?></td>
					<td><?=$daterequested?></td>
					<td>
						<?=$status?><br />
						&nbsp;&nbsp;<?=$dateapproved?>
					</td>
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