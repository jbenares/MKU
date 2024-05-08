<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("UPDATE petty_cash_rjr SET is_deleted = '1' WHERE petty_cash_id='$ch'");
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
	<div class="module_title"><img src='images/user_orange.png'>RJR Petty Cash</div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />
        <input type="submit" name="b" value="Search" class="buttons" />
        <input type="button" name="b" value="Request Petty Cash" onclick="xajax_new_pcform_rjr();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />		
		<?php
			// Petty Cash Budget
			$bud = "SELECT * FROM petty_cash_budget_rjr";
			$res_bud = mysql_query($bud);
			$num_bud = mysql_num_rows($res_bud);
			if($num_bud > 0)
			{
				$current_bd_amount = 0;
				while($row_bud = mysql_fetch_assoc($res_bud))
				{
					$bd_amount = $row_bud['amount'];
					$current_bd_amount += $bd_amount;
				}
			}else{ $current_bd_amount = '0.00';}
			
			// Petty Cash Request
			$req = "SELECT * FROM petty_cash_rjr WHERE is_deleted != '1'";
			$res_req = mysql_query($req);
			$num_req= mysql_num_rows($res_req);
			if($num_req > 0)
			{
				$current_rq_amount = 0;
				while($row_req = mysql_fetch_assoc($res_req))
				{
					$rq_amount = $row_req['amount'];
					$current_rq_amount += $rq_amount;
				}
			}else{ $current_rq_amount = '0.00';}
			
			# Get Petty Cash Balance
			$pc_balance = $current_bd_amount - $current_rq_amount;
		?>
		<input type="text" name="bd_amount" class="textbox" value="<?php echo number_format($pc_balance, 2); ?>" readonly />
		<input type="button" name="b" value="Add Petty Cash Amount" onclick="xajax_new_pcamtform_rjr();" class="buttons" />
		<input type="button" name="b" value="Generate Report" onclick="window.location.href='admin.php?view=acd66d15373263b8fb31'" class="buttons" />
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
					petty_cash_rjr
				where
					purpose like '%$keyword%' AND is_deleted != '1'
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
		  <td>Purpose</td>
		  <td>Amount</td>
		  <td>Date Requested</td>		  
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$petty_cash_id		= $r['petty_cash_id'];				
				$purpose		= $r['purpose'];
				$amount 	= $r['amount'];				
				$date_requested	= $r['date_requested'];				
				$daterequested = date("M d, Y",strtotime($date_requested));							

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$petty_cash_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="#" onclick="xajax_edit_pcform_rjr('<?=$petty_cash_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
					<td width="15"><a href="petty_cash/print_petty_cash_rjr.php?pt_cash=<?php echo $petty_cash_id; ?>" title="Print" target=_blank><img src="images/action_print.gif" border="0"></a></td>					
					<td><?=$purpose?></td>
					<td><?=number_format($amount,2)?></td>
					<td><?=$daterequested?></td>					
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