<?php
	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	if($b=='Approve Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("UPDATE petty_cash SET is_approve = '1', is_liquidated = '1', date_approved = NOW() WHERE petty_cash_id='$ch'");
			//$options->insertAudit($ch,'petty_cash_id','D');
			
			#Get petty cash details
			$sql = "SELECT * FROM petty_cash WHERE petty_cash_id='$ch'";
			$res = mysql_query($sql);
			$row = mysql_fetch_assoc($res);
				$date = $row['date_approved'];
				$date = strtotime($date);
				$date = strtotime("+7 day", $date);
				$targetdt = date('Y-m-d h:i:s', $date) . '<br />';
			mysql_query("UPDATE petty_cash SET date_target_liquidation = '$targetdt' WHERE petty_cash_id='$ch'");
		}
	  }
	}
	
	if($b=='Replenish Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("UPDATE petty_cash SET is_replenish = '1', date_replenish = NOW() WHERE petty_cash_id='$ch'");
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
        <!--<input type="button" name="b" value="Request Petty Cash" onclick="xajax_new_pcform();" class="buttons" />!-->
        <input type="submit" name="b" value="Approve Selected" onclick="return approve_confirm();" class="buttons" />
		<?php
			// Petty Cash Budget
			$bud = "SELECT * FROM petty_cash_budget";
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
			$req = "SELECT * FROM petty_cash WHERE is_approve = '1' AND is_deleted != '1'";
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
		<input type="button" name="b" value="Add Petty Cash Amount" onclick="xajax_new_pcamtform();" class="buttons" />
		<input type="button" name="b" value="Generate Report" onclick="window.location.href='admin.php?view=3d1c12c00e1e72099154'" class="buttons" />
		<input type="button" name="b" value="Liquidation Report" onclick="window.location.href='admin.php?view=228c3c288fbc28236646'" class="buttons" />
		<input type="button" name="b" value="PC Not Liquidated Report" onclick="window.location.href='admin.php?view=b7d070ca1d68feccdf6d'" class="buttons" />
	<!--	<input type="button" name="b" value="RJR Petty Cash" onclick="window.location.href='admin.php?view=def054fc4974ad3ce9ab'" class="buttons" /> -->
		&nbsp;&nbsp;
		<input type="submit" name="b" value="Replenish Selected" onclick="return approve_confirm();" class="buttons" />
		
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
					petty_cash p, projects s, employee e
				where
					e.employee_lname like '%$keyword%' AND p.employeeID = e.employeeID AND p.department_id = s.project_id AND p.is_deleted != '1'
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
		<tr bgcolor="#333333">
			<td colspan="10"><font color="#ffffff"><center><b>PETTY CASH REQUEST</b></center></font></td>
			<td colspan="4"><font color="#ffffff"><center><b>LIQUIDATION</b></center></font></td>
		</tr>
    	<tr bgcolor="#C0C0C0">				
         <td width="20"><b>#</b></td>		  
		 <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"><b>PC No.</b></td>
		  <td width="20"></td>
          <td width="20"></td>
		  <td width="20"></td>
		  <td>Requested By</td>
          <td>Department</td>
		  <td>Purpose</td>
		  <td width="100">Amount</td>
		  <td>Status</td>
		  <td width="100">Amount</td>		  
		  <td width="110">Status</td>
		  <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
         </tr>        
		<?php					
			$i=1;			
			while($r=mysql_fetch_assoc($rs)) {
				$petty_cash_id		= $r['petty_cash_id'];
				$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
				$project_name		= $r['project_name'];
				$purpose		= $r['purpose'];
				$amount 	= $r['amount'];
				$returned_amount 	= $r['returned_amount'];
				$liquidated_amount 	= $r['liquidated_amount'];
				$difference 	= $r['difference'];
				$is_approve	= $r['is_approve'];
				$is_liquidated	= $r['is_liquidated'];
				$date_target_liquidation	= $r['date_target_liquidation'];
				$is_replenish	= $r['is_replenish'];
		
				$total_liquidation = $liquidated_amount + $returned_amount;
				
				#Start compute date difference
					date_default_timezone_set("Asia/Manila");	
					$today = date("Y-m-d");
					$startTimeStamp = strtotime($today);
					$endTimeStamp = strtotime($date_target_liquidation);
	
					$timeDiff = abs($endTimeStamp - $startTimeStamp);
		
					$numberDays = $timeDiff/86400;  // 86400 seconds in one day

					// and you might want to convert to integer
					$numberDays = intval($numberDays);
					if($numberDays == '1'){$formatday = 'day';}else{$formatday = 'days';}
				#End compute date difference
				
				if($difference != '0.00'){$chargeamt = $difference;}else{$chargeamt = $amount;}
				
				if($is_approve == '0'){$status = '<font color=red><b>Pending</b></font>'; $display = 'style=display:none;'; $disopt = '';}else{$status = '<font color=blue><b>Approved</b></font>'; $display = ''; $disopt = 'style=display:none;';}				
				if($is_liquidated == '0'){$lq_stat = 'Liquidated';}else if($is_liquidated == '1'){$lq_stat = 'Pending';}else if($numberDays == '0'){$lq_stat = 'Charged ' . number_format($chargeamt,2);}else{$lq_stat = $numberDays . '&nbsp;' . $formatday . ' remaining';}
				if($is_liquidated == '0'){$liquidation_opt = 'style=display:none;';}else{$liquidation_opt = '';}
				if($is_replenish == '1'){$replenish_opt = 'style=display:none;';}else{$replenish_opt = '';}

				#Date Requested
				$date_requested	= $r['date_requested'];
				$daterequested = date("M d, Y",strtotime($date_requested));
				
				#Date Approved
				$date_approved	= $r['date_approved'];
				if($date_approved != '0000-00-00 00:00:00'){
					$dateapproved = date("M d, Y",strtotime($date_approved));
				}else{
					$dateapproved = '';
				}

				#Date Liquidated
				$date_liquidated	= $r['date_liquidated'];
				if($date_liquidated != '0000-00-00 00:00:00'){
					$dateliquidated = date("M d, Y",strtotime($date_liquidated));
				}else{
					$dateliquidated = '';
				}				

			echo '<tr bgcolor="'.$transac->row_color($i).'">';
		?>
                    <td width="20"><?=$i++?></td>
					
                    <td><input type="checkbox" name="checkList[]" value="<?=$petty_cash_id?>" onclick="document._form.checkAll.checked=false" <?php echo $disopt; ?>></td>
                    <td><?=str_pad($petty_cash_id,6,0,STR_PAD_LEFT)?></td>
					<td width="15"><a href="#" onclick="xajax_edit_pcform('<?=$petty_cash_id?>');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>
                    <td width="15"><a href="#" onclick="xajax_liquidate_pcform('<?=$petty_cash_id?>');" title="Liquidate Entry"><img src="images/money_add.png" border="0" <?php echo $display; ?> <?php echo $liquidation_opt; ?>></a></td>
					<td width="15"><a href="petty_cash/print_petty_cash.php?pt_cash=<?php echo $petty_cash_id; ?>" title="Print" target=_blank><img src="images/action_print.gif" border="0" <?php echo $display; ?>></a></td>
					<td><?=$employee?></td>
                    <td><?=$project_name?></td>
					<td><?=$purpose?></td>
					<td>
						<?=number_format($amount,2)?><br />
						&nbsp;&nbsp;<?=$daterequested?>						
					</td>
					<td>
						<?=$status?><br />
						&nbsp;&nbsp;<?=$dateapproved?>
					</td>
					<td>
						<?=number_format($total_liquidation,2)?><br />
						&nbsp;&nbsp;<?=$dateliquidated?>
					</td>					
					<td><?=$lq_stat?></td>
					<td><input type="checkbox" name="checkList[]" value="<?=$petty_cash_id?>" onclick="document._form.checkAll.checked=false" <?php echo $replenish_opt; ?>></td>
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