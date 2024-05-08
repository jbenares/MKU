<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

<?php
	$b = $_REQUEST['b'];
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					return_header
				set
					status='C'
				where
					return_header_id='$ch'
			";
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($ch,'return_header_id','C');
		}
	  }
	}
	
?>
<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
            <input type="submit" name="b" value="Search" />
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" />
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
	if($b!="Print"){
    ?>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  return_header
					 where
					 	  return_header_id like '%$keyword%'
					order 
						by date desc
				";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
            <td width="20"><b>#</b></td>
            <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
            <td width="20"></td>
            <td width="20"></td>    
            <td><b>SR #</b></td>
            <td><b>Date</b></td>
            <td><b>Account</b></td>
            <td><b>Amount</b></td>
            <td><b>Status</b></td>
        </tr>  
        </thead>      
		<?php		
			$i=1;						
			while($r=mysql_fetch_assoc($rs)) {
				$return_header_id		= $r['return_header_id'];
				$account_id				= $r['account_id'];
				$account_name			= $options->getAccountName($account_id);
				$date					= $r['date'];
				$user_id				= $r['user_id'];
				$username				= $options->getUserName($user_id);
				$status					= $r['status'];
				$totalamount			= $r['totalamount'];
				$dr_header_id			= $r['dr_header_id'];
				
				
				echo '<tr bgcolor="'.$transac->row_color($i).'">';
				echo '<td width="20">'.$i++.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$return_header_id.'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=d6d21a3f95099992ff29&return_header_id='.$return_header_id.'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="admin.php?view=53f12069a0d398d36137&return_header_id='.$return_header_id.'&b=Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($return_header_id,7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.$date.'</td>';	
				echo '<td>'.$account_name.'</td>';	
				echo '<td><div align="right">'.number_format($totalamount,2,'.',',').'</div></td>';	
				echo '<td>'.$options->getTransactionStatusName($status).'</td>';	
				echo '</tr>';
			}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>                
            </td>
      	</tr>
    	</table>
    <?php
	}else{	
    ?>
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='printOrder.php?id=<?=$_REQUEST[order_header_id]?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>