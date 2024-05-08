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
	$service_pay_header	= $_REQUEST['service_pay_header'];
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			$query="
				update
					service_pay_header
				set
					status='C'
				where
					service_pay_header='$ch'
			";
			mysql_query($query) or die(mysql_error());
			$options->insertAudit($ch,'service_pay_header','C');
			
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
            <input type="submit" name="b" value="Cancel" onclick="return approve_confirm();"  />
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php
	if($b!="Print"){
    ?>
    <div style="padding:3px; text-align:center;" id="content">
        <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
            <?php
                $page = $_REQUEST['page'];
                if(empty($page)) $page = 1;
                 
                $limitvalue = $page * $limit - ($limit);
            
                $sql = "select
                              *
                         from
                              service_pay_header
                         where
                              service_pay_header_id like '%$keyword%'
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
                <td><b>Service Paymnet #</b></td>
                <td><b>Date</b></td>     
                <td><b>Supplier</b></td>     
                <td><b>Status</b></td>     
            </tr>  
            </thead>      
            <?php								
                while($r=mysql_fetch_assoc($rs)) {
                    
                    echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                    echo '<td width="20">'.$i.'</td>';
                    echo '<td><input type="checkbox" name="checkList[]" value="'.$r[service_pay_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
                    echo '<td width="15"><a href="admin.php?view=92d742306163e5ac449a&service_pay_header_id='.$r[service_pay_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
                    echo '<td width="15"><a href="admin.php?view='.$view.'&b=Print&service_pay_header_id='.$r[service_pay_header].'" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
                    echo '<td>'.str_pad($r['service_pay_header_id'],7,"0",STR_PAD_LEFT).'</td>';	
                    echo '<td>'.$r['date'].'</td>';	
					echo '<td>'.$options->attr_Supplier($r['supplier_id'],'account').'</td>';	
                    echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
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
    </div>
    <?php
	}else if($b=="Print"){
    ?>
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=<?=$service_pay_header?>' width='100%' height='500'>
        	</iframe>
    <?php
	}
    ?>
</div>
</form>