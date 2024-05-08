<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<style type="text/css">
</style>

<?php

	$b = $_REQUEST['b'];
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			$query="
				update
					po_header
				set
					status='C'
				where
					po_header_id='$ch'
			";
			mysql_query($query);
			$options->insertAudit($ch,'po_header_id','C');
		}
	  }
	} else if($b == "Close"){
		if(!empty($checkList)) {
			foreach($checkList as $ch) {	
				$query="
					update
						po_header
					set
						closed = '1',
						date_closed = now()
					where
						po_header_id='$ch'
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
        	PO # : <br />
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
        </div>
        <div style="display:inline-block;">
        	Supplier : <br />
        	<input type="text" name="search_supplier" class="textbox3" value="<?=$_REQUEST['search_supplier']?>" autocomplete="off" />
        </div>
        <input type="submit" name="b" value="Search" />
        <input type="submit" name="b" value="Close" onclick="return approve_confirm();" />
        <input type="hidden" id="po_header_id" value="" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php
	if($b!="Print"){
    ?>
    <div style="padding:3px; text-align:center;" id="content">
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  po_header as h 
					 	  left join  supplier as s on s.account_id = h.supplier_id
					 	  left join projects as p on h.project_id = p.project_id
					 where					 	
					 	po_header_id like '%$keyword%'
					 and po_type = 'S' and h.supplier_id != '0'
				";
			
			if( $_REQUEST['search_supplier'] ) {
				$sql.="
					and
						account like '$_REQUEST[search_supplier]%'
				";	
			}
			$sql.="
				and
					status != 'C'
				order by date desc
			";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
    	<tr bgcolor="#C0C0C0">				
            <th width="20">#</th>
            <th width="20" align="center"><!--<input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" />--></th>
            <!--<th width="20"></th>-->
            <!--<th width="20"></th>-->   
            <th>PO #</th>   
            <th>Date</th> 
            <th>Project</th> 
            <th>Supplier</th>
            <th>Status</th>
            <th>Close Status</th>
			<th>Date</th>
        </tr>    
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				$po_header_id 		= $r['po_header_id'];
				$project_id			= $r['project_id'];
				$project_name		= $options->attr_Project($project_id,'project_name');
				$scope_of_work		= $r['scope_of_work'];
				$work_category_id 	= $r['work_category_id'];
				$work_category  = $options->attr_workcategory($work_category_id,'work');
				$sub_work_category_id = $r['sub_work_category_id'];
				$sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
				$status = $r['status'];
				$approval_status = $r['approval_status'];
				$date_closed = $r['date_closed'];
				
				$accomplished = $options->poIsAccomplished($po_header_id);
				$accomplished_status = ($accomplished == 1)? "ACCOMPLISHED" : "PENDING";
				$disabled = ($accomplished != 1 || $options->hasAPV($po_header_id)) ? "disabled='disabled'" : "";
				
				if($status == 'C'){
					echo '<tr class="cancelled">';
				} else if( $approval_status == "A" ) {
					echo '<tr class="approved">';
				} else {
					echo '<tr>';	
				}
				echo '<td width="20">'.++$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[po_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
				#echo '<td width="15"><a href="admin.php?view=62e77ed88c61ca0618ee&po_header_id='.$r[po_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				#echo '<td width="15"><a href="admin.php?view='.$view.'&b=Print&po_header_id='.$r[po_header_id].'" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($r['po_header_id'],7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
				echo '<td>'.$project_name.'</td>';	
				echo '<td>'.$options->attr_Supplier($r[supplier_id],'account').'</td>';	
				echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';		
				echo '<td>'.(($r['closed']) ? "<span style='font-weight:bold; color:#F00;'>CLOSED</span>" : "").'</td>';
				if($date_closed == "0000-00-00"){
					echo '<td> </td>';
				}else{
					echo '<td>'.date("F j, Y",strtotime($date_closed)).'</td>';	
					}
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
    }else{
    ?>
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_po.php?id=<?=$_REQUEST['po_header_id']?>' width='100%' height='500'></iframe>
    <?php
	}
    ?>
</div>
</form>