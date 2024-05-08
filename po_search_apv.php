<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<style type="text/css">
	.cancelled td{
		background-color:#FFA6A6;
	}
	
	.approved td{
		background-color:#A4FFA4;
	}
</style>

<?php

	$b = $_REQUEST['b'];
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	$po_header_id = $_REQUEST['po_header_id'];	
	
	if($b == "apv" && !empty($po_header_id)){
		
		$result = mysql_query("
			select
				*
			from
				po_header
			where
				po_header_id = '$po_header_id'
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		$date = date("Y-m-d");
		$po_date = $r['date'];
		$project_id = $r['project_id'];
		$work_category_id = $r['work_category_id'];
		$sub_work_category_id = $r['sub_work_category_id'];
		$supplier_id = $r['supplier_id'];
		$terms = $r['terms'];
		
		mysql_query("
			insert into
				apv_header
			set
				po_header_id = '$po_header_id',
				date = '$date',
				po_date = '$po_date',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				user_id = '{$_SESSION[userID]}'
		") or die(mysql_error());
		$apv_header_id = mysql_insert_id();
		
		$result = mysql_query("
			select
				*
			from
				po_detail
			where
				po_header_id = '$po_header_id'
		") or die(mysql_error());
		
		$apv_details = array();
		$rrs_details = array();
		while($r = mysql_fetch_assoc($result)){
			$stock_id  = $r['stock_id'];	
			#$quantity  = $r['quantity'];
			$cost = $r['cost'];
			#$amount = $r['amount'];
			
			$details = $options->getMRRDetails($po_header_id,$stock_id,$cost);
			
			$rrs = $options->getMRR($po_header_id, $stock_id, $cost);
			/*echo "<pre>";
			print_r($rrs);
			print_r($details);
			echo "</pre>";*/
			
			if(!empty($rrs)){
				$quantity = $details['quantity'];
				$price = $details['price'];
			
			
				$rrs_details[] = $rrs;
				
				$amount = $quantity * $price;
				
				mysql_query("
					insert into
						apv_detail
					set
						apv_header_id = '$apv_header_id',
						stock_id  = '$stock_id',
						quantity = '$quantity',
						price = '$price',
						amount = '$amount'
				") or die(mysql_query());
				
				$apv_detail_id = mysql_insert_id();
				
				$apv_details[] = $apv_detail_id;
				
				/*if(!empty($rrs)){
					foreach($rrs as $rr_header_id ){
						mysql_query("
							insert into
								apv_mrr_detail
							set
								apv_detail_id = '$apv_detail_id',
								rr_header_id = '$rr_header_id'
						") or die(mysql_error());
					}
				}*/
			}
		}
		$x = 0;
		foreach($apv_details as $apv_detail_id){
			if(!empty($rrs_details[$x])){
				foreach($rrs_details[$x] as $rr_header_id ){
					mysql_query("
						insert into
							apv_mrr_detail
						set
							apv_detail_id = '$apv_detail_id',
							rr_header_id = '$rr_header_id'
					") or die(mysql_error());
				}
			}
			$x++;
		}
		
		header("Location:admin.php?view=687b880d1beb02fa41b1&apv_header_id=$apv_header_id");
	}
	
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
        <div class="inline">
            Date : <br />
            <input type="text" class="textbox3 datepicker" name="search_date" value="<?=$_REQUEST['search_date']?>" />
        </div>
        <input type="submit" name="b" value="Search" />
        <input type="submit" name="b" value="View All Approved" />
        <input type="submit" name="b" value="View All Pending" />
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
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
					 	  po_header
					 where
					 	  po_header_id like '%$keyword%'
				";
			if( $b == "View All Approved" || ( $_REQUEST['approval_status']=="A" && empty($b) ) ) {
				$sql.="
					and
						approval_status = 'A'
				";	
			} else if( $b == "View All Pending" || ( $_REQUEST['approval_status']=="P" && empty($b) )) {
				$sql.="
					and
						approval_status = 'P'
				";	
			}
			
			if( $_REQUEST['search_date'] ) {
				$sql.="
					and
						date = '$_REQUEST[search_date]'
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
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th></th>
            <th width="20"></th>
            <th width="20"></th>    
            <th>RTP #</th>  
            <th>PO #</th>   
            <th>Date</th>
            
            <th>Project</th>
            <th>Work Category</th>
            <th>Sub Work Category</th>
            
            <th>Supplier</th>
            <th width="15">Terms</th>
            
            <th>DR Status</th>
            <th>Status</th>
            <th>Approval Status</th>
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
				
				$accomplished = $options->poIsAccomplished($po_header_id);
				$accomplished_status = ($accomplished == 1)? "ACCOMPLISHED" : "PENDING";
				$disabled = ( $approval_status=="P" ) ? "disabled='disabled'" : "";
				
				if($status == 'C'){
					echo '<tr class="cancelled">';
				} else if( $approval_status == "A" ) {
					echo '<tr class="approved">';
				} else {
					echo '<tr>';	
				}
				echo '<td width="20">'.++$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[po_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td><a href="admin.php?view='.$view.'&po_header_id='.$po_header_id.'&b=apv"><input type="button" value="Generate APV" '.$disabled.' ></a></td>';
				echo '<td width="15"><a href="admin.php?view=7903cdd0494e804dde22&po_header_id='.$r[po_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="admin.php?view='.$view.'&b=Print&po_header_id='.$r[po_header_id].'" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.str_pad($r['pr_header_id'],7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.str_pad($r['po_header_id'],7,"0",STR_PAD_LEFT).'</td>';	
				echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
				
				echo '<td>'.$project_name.'</td>';	
				echo '<td>'.$work_category.'</td>';	
				echo '<td>'.$sub_work_category.'</td>';	
					
				echo '<td>'.$options->attr_Supplier($r[supplier_id],'account').'</td>';	
				echo '<td>'.$r['terms'].'</td>';
				
				echo '<td>'.$accomplished_status.'</td>';	
				echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
				echo '<td>'.$options->getApprovalStatus($r[approval_status]).'</td>';	
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