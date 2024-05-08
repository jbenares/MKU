<?php 
//$conn = mysql_connect('localhost', 'root', '') or die(mysql_error());
	$conn = mysql_connect('localhost', 'root', 'syndy103') or die(mysql_error());
	//$conn = mysql_connect('localhost', 'metrosys', 'sysmetroclick1234') or die(mysql_error());
	
	//$db = mysql_select_db('dbcci') or die(mysql_error());
	$db = mysql_select_db('dynamicbuilders') or die(mysql_error());

	$sql = "SELECT * FROM petty_cash WHERE is_approve = '0' AND is_deleted != '1'";
	$res = mysql_query($sql);
	
?>

		<table border="1" cellpadding="10" cellspacing="10">
		<tr>
			<td>ID</td>
			<td>DATE APPROVED</td>
		</tr>
			<?php
				$ctr = 1;
				while($row = mysql_fetch_assoc($res))
				{
					extract($row);
					
					//$up = "UPDATE petty_cash SET date_approved = '$date_requested' WHERE is_approve = '1' AND is_deleted != '1' AND petty_cash_id = '$petty_cash_id'";
					//mysql_query($up);
					
					$date = $row['date_approved'];
					$date = strtotime($date);
					$date = strtotime("+7 day", $date);
					$targetdt = date('Y-m-d h:i:s', $date) . '<br />';
				//mysql_query("UPDATE petty_cash SET is_liquidated = '2' WHERE petty_cash_id = '$petty_cash_id'");
			?>
					<tr>
						<td><?php echo $ctr++; ?></td>
						<td><?php echo $petty_cash_id; ?></td>
						<td><?php echo $date_approved; ?></td>
					</tr>
			<?php
				}
			?>
		</table>
		