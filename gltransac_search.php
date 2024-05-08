<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	
	var TSort_Data = new Array ('my_table', '','','','','s','s','s','s','s','s','s','s','s','s','s','s');
	tsRegister();
	
</script>


<?php

	$b = $_REQUEST['b'];
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			$options->unpost($ch);
					
			$query="
				update
					gltran_header
				set
					status='C'
				where
					gltran_header_id='$ch'
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
             <?=$options->getJournalOptions($_REQUEST[journal_id]);?>
            <input type="submit" name="b" value="Search" class="buttons" />
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" class="buttons" />
        </div>
      <!--  <input type="submit" name="b" value="Display All" class="buttons" />-->
<!--        <input type="button" name="b" value="Add Brand" onclick="xajax_new_brandform();toggleBox('demodiv',1);" class="buttons" />-->
        <!--<input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />-->
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="hidden" id="gltran_header_id" value="" />
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
					 	  gltran_header
					 where
					 (
					 	  xrefer like '%$keyword%' or generalreference like '%$keyword%' or gltran_header_id = '$keyword'
					  )
					and
						journal_id like '%$_REQUEST[journal_id]%'
					order by
						date desc
					";
			
		/*	$sql = "select
						  *
					 from
					 	  gltran_header
					 where
					 	gltran_header_id = '3049'
					";*/
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
			
			$journal_id_dv=$options->getJournalID("DV");
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
            <td width="20"><b>#</b></td>
            <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
            <td width="20"></td>
            <td width="20"></td>  
            <td><b>General Ref</b></td>
            <td><b>Reference</b></td>       
            <td><b>Date</b></td>
            <?php
			if($_REQUEST[journal_id]==$journal_id_dv){
            echo "<td><b>Total Amount</b></td>";
			}
			?>
            <td><b>Journal</b></td>
            <td><b>Details</b></td>
            <td><b>Account</b></td>
            <td><b>Address</b></td>
            <td><b>Account ID</b></td>
            <td><b>Check Date</b></td>
            <td><b>Check Details</b></td>
            <td><b>Status</b></td>
        </tr>  
        </thead>      
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				$checkdate  = ($r['checkdate']=="0000-00-00")?"":$r['checkdate'];
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[gltran_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id='.$r[gltran_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="#" onclick="xajax_printGLTransac(\''.$r[gltran_header_id].'\'); toggleBox(\'demodiv\',1);" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.$r['generalreference'].'</td>';	
				echo '<td>'.$r['xrefer'].'</td>';	
				echo '<td>'.$r['date'].'</td>';	
				if($_REQUEST[journal_id]==$journal_id_dv){
				echo '<td>'.number_format($options->getTotalDebitOfGL($r[gltran_header_id],$r[journal_id]),2,'.',',').'</td>';	
				}
				echo '<td>'.$options->getJournalName($r['journal_id']).'</td>';	
				echo '<td>'.$r['details'].'</td>';	
				echo '<td>'.$r['account'].'</td>';	
				echo '<td>'.$r['address'].'</td>';	
				echo '<td>'.$options->getGLAccountName($r['account_id']).'</td>';	
				echo '<td>'.$checkdate.'</td>';	
				echo '<td>'.$r['mcheck'].'</td>';	
				echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
				echo '</tr>';
			}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view&journal_id=$_REQUEST[journal_id]");
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>