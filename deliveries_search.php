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
			/*
			mysql_query("delete from joborder_header where joborder_id='$ch'");
			mysql_query("delete from joborder_formulation where joborder_id='$ch'");
			*/
			//$options->cancelGL($ch,"dr_header_id","SJ");
			$query="
				update
					dr_header
				set
					status='C'
				where
					dr_header_id='$ch'
				
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
        	<div class="inline">
            	DR # : <br />
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
             
            <input type="submit" name="b" value="Search"  />
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();"  />
        </div>
    
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
					 	  dr_header
					 where
					 	  dr_header_id like '%$keyword%'
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
            <td><b>DR #</b></td>       
            <td><b>Date</b></td>
            <td><b>Account</b></td>
            <td><b>Gross Amount</b></td>
            <td><b>Net Amount</b></td>
            <td><b>Discount</b></td>
            <td><b>Payment</b></td>
            <td><b>Status</b></td>
        </tr>  
        </thead>      
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[dr_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=e47a963919d3f2310d14&dr_header_id='.$r[dr_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="#" onclick="xajax_print_delivery(\''.$r[dr_header_id].'\'); toggleBox(\'demodiv\',1);" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($r['dr_header_id'],7,0,STR_PAD_LEFT).'</td>';	
				echo '<td>'.$r['date'].'</td>';	
				echo '<td>'.$options->getAccountName($r[account_id]).'</td>';	
				echo '<td><div align="right">P '.number_format($r[grossamount],2,'.',',').'</div></td>';	
				echo '<td><div align="right">P '.number_format($r[netamount],2,'.',',').'</div></td>';	
				echo '<td><div align="right">P '.number_format($r[discounttotal],2,'.',',').'</div></td>';	
				echo '<td>'.$options->getPayTypeName($r[paytype]).'</td>';	
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