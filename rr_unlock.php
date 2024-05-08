<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<?php
	$b 				= $_REQUEST['b'];
	$keyword 		= $_REQUEST['keyword'];
	$checkList 		= $_REQUEST['checkList'];
	$rr_header_id	= $_REQUEST['rr_header_id'];
	$user_id 		= $_SESSION['userID'];
	
	
	if($b == "UNLOCK"){
		mysql_query("
			update rr_header set status = 'S' where rr_header_id = '$rr_header_id'
		") or die(mysql_error());	
		
		mysql_query("
			update gltran_header set status = 'C' where header = 'rr_header_id' and header_id = '$rr_header_id'
		") or die(mysql_error());
		
		
		$msg = "GL ENTRY UNPOSTED AND TRANSACTION SET BACK TO SAVED";
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
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
        	RR #: <br />
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>            
        </div>
        
        <div style="display:inline-block;">
        	PO #: <br />
            <input type='text' name='search_po_header_id' class='textbox3' value='<?=$_REQUEST['search_po_header_id']?>'>            
        </div>
        
        <input type="submit" name="b" value="Search" />
        <!--<input type="button" name="b" value="Generate APV" onclick="j('#_dialog').dialog('open');" class="buttons" />             -->
        <input type="hidden" id="rr_header_id" value="" />
    </div>
    <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
    <?php
	if($b!="Print"){
    ?>
    <?php
		$page = $_REQUEST['page'];
		if(empty($page)) $page = 1;
		 
		$limitvalue = $page * $limit - ($limit);
	
		$sql = "select
					  *
				 from
					  rr_header
				 where
					  rr_header_id like '%$keyword%'
			";
			
		if($_REQUEST['search_po_header_id']){
		$sql.="
			and
				po_header_id = '".$_REQUEST['search_po_header_id']."'
		";	
		}
			
		$sql.="
			order 
					by date desc
		";            
			
		$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
				
		$i=$limitvalue;
		$rs = $pager->paginate();
	?>
    <div style="padding:3px; text-align:center;" id="content">
    	<div class="pagination"><?=$pager->renderFullNav("$view")?></div>
        <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
            <tr bgcolor="#C0C0C0">				
                <th width="20">#</th>
                <th width="20"></th>    
                <th>RR #</th>
                <th>PO #</th>     
                <th>Date</th>
                <th>Supplier</th>
                <th>Payment</th>
                <th>Received in</th>
                <th>Status</th>
            </tr>  
            <?php								
                while($r=mysql_fetch_assoc($rs)) {
                    $supplier_id 	= $r['supplier_id'];
					$rr_in			= $r['rr_in'];
					
					$rr_in_display 	= ($rr_in=="P")?"Project":"Warehouse";
					$supplier		= $options->attr_Supplier($supplier_id,'account');
					
					
                    echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                    echo '<td width="20">'.$i.'</td>';
                    echo '<td width="15"><a href="admin.php?view='.$view.'&b=UNLOCK&rr_header_id='.$r[rr_header_id].'" title="Print" onclick="return approve_confirm();" ><input type="button" value="UNLOCK"></a></td>';
                    echo '<td>'.str_pad($r['rr_header_id'],8,"0",STR_PAD_LEFT).'</td>';	
                    echo '<td>'.str_pad($r['po_header_id'],8,"0",STR_PAD_LEFT).'</td>';	
                    echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
					echo '<td>'.$supplier.'</td>';	
                    echo '<td>'.$options->getPayTypeName($r[paytype]).'</td>';	
					echo '<td>'.$rr_in_display.'</td>';	
                    echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
                    echo '</tr>';
                }
            ?>
      	</table>
        <div class="pagination"><?=$pager->renderFullNav("$view")?></div>
    </div>
    <?php
	}else if($b=="Print"){
    ?>
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=<?=$rr_header_id?>' width='100%' height='500'>
        	</iframe>
    <?php
	}
    ?>
</div>
</form>
<div id="_dialog" style="padding:0px;">
    <div id="ap_dialog_content">
    
    	<div style="margin:10px;">
        	PO #:<br />
            <input type="text" name="po_header_id" class="textbox" autocomplete="off" />
        </div>
           
        <input type="submit" name="b" value="Generate" class="buttons" style="margin:10px;" onclick="return approve_confirm();" />
    </div>
</div>
<script type="text/javascript">
	j(function(){
		j(function(){
			var dlg = j("#_dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', title : "AP Voucher Details"});
			dlg.parent().appendTo(jQuery("form:first"));
		});
		
		j("#work_category_id").change(function(){
			xajax_display_subworkcategory(this.value);
		});
	});
</script>