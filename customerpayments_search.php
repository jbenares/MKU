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
			/*
			mysql_query("delete from joborder_header where joborder_id='$ch'");
			mysql_query("delete from joborder_formulation where joborder_id='$ch'");
			*/
			$query="
				update
					pay_header
				set
					status='C'
				where
					pay_header_id='$ch'
			";
			mysql_query($query) or die(mysql_error());
			
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
             
            <input type="submit" name="b" value="Search" class="buttons" />
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" class="buttons" />
        </div>
      <!--  <input type="submit" name="b" value="Display All" class="buttons" />-->
<!--        <input type="button" name="b" value="Add Brand" onclick="xajax_new_brandform();toggleBox('demodiv',1);" class="buttons" />-->
        <!--<input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />-->
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="hidden" id="dr_header_id" value="" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  pay_header
					 where
					 	  pay_header_id like '%$keyword%'
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
            <td><b>Date</b></td>      
            <td><b>Reference</b></td>      
            <td><b>Account</b></td>      
            <td><b>Total Amount</b></td>      
            <td><b>Status</b></td>
        </tr>  
        </thead>      
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[pay_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=520c72159bd689361b66&pay_header_id='.$r[pay_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="#" onclick="xajax_print_delivery(\''.$r[pay_header_id].'\'); toggleBox(\'demodiv\',1);" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.$r['date'].'</td>';	
				echo '<td>'.$r['reference'].'</td>';	
				echo '<td>'.$options->getAccountName($r[account_id]).'</td>';	
				echo '<td><div align="right">P '.number_format($r[total_amount],2,'.',',').'</div></td>';	
				echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
				echo '</tr>';
			}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav($view);
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>