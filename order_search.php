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
	
	if($_REQUEST['account_name']){
		$account_id		= $_REQUEST['account_id'];
		$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"";
	}
	
	$date 	= $_REQUEST['date'];
	
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					order_header
				set
					status='C'
				where
					order_header_id='$ch'
			";
			mysql_query($query);
			
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
        	<div class="inline">
            	OS # : <br />
	            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
           	</div>
            
            <div class='inline'>
                Account : <br />        
                <input type="text" class="textbox" id="account_name" name="account_name" value="<?=$account_name?>" />
                <input type="hidden" name="account_id"  id="account_id" value="<?=$account_id?>" />
            </div>   
            
            <div style="display:inline-block;">
                Date : <br />
                <input type="text" class="textbox3 datepicker" name="date" value='<?=$date?>' readonly='readonly' >
            </div>
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
					 	  order_header
					 where
					 	  order_header_id like '%$keyword%'
				";
				
			if($account_id){
			$sql.= "
				and
					account_id = '$account_id'
			";
			}
			
			if($date){
			$sql.="
				and
					date = '$date'
			";	
			}
				
			
			$sql.="
					order 
					by date desc";
						  
						  
				
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
            <td><b>ORDER #</b></td>
            <td><b>Date</b></td>
            <td><b>Account</b></td>
            <td><b>Amount</b></td>
            <td><b>Time of Delivery</b></td>
            <td><b>Status</b></td>
        </tr>  
        </thead>      
		<?php		
			$i=1;						
			while($r=mysql_fetch_assoc($rs)) {
				$order_header_id		= $r['order_header_id'];
				$account_id				= $r['account_id'];
				$account_name			= $options->getAccountName($account_id);
				$time					= $r['time'];
				$date					= $r['date'];
				$user_id				= $r['user_id'];
				$status					= $r['status'];
				$netamount				= $r['netamount'];
				
				
				
				echo '<tr bgcolor="'.$transac->row_color($i).'">';
				echo '<td width="20">'.$i++.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$order_header_id.'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=80a667cfc2e56e49eb5f&order_header_id='.$order_header_id.'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="admin.php?view=ea48bced27d95dc9ad6c&order_header_id='.$order_header_id.'&b=Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($order_header_id,7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.$date.'</td>';	
				echo '<td>'.$account_name.'</td>';	
				echo '<td><div align="right">'.number_format($netamount,2,'.',',').'</div></td>';	
				echo '<td>'.$time.'</td>';	
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