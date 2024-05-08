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
	$rr_header_id	= $_REQUEST['rr_header_id'];
	$user_id = $_SESSION['userID'];
	
	function hasAPV($rr_header_id){
		$result = mysql_query("select rr_id from apv_header as h, apv_detail as d where h.apv_header_id = d.apv_header_id and rr_id = '$rr_header_id' and status != 'C'") or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function checkPO($list){ #CHECK IS ALL RR CHECKED HAS THE SAME PO
		$options = new options();
		
		$po_header_id = 0;
		$x = 1;
		foreach($list as $rr_header_id){
			if($x==1){
				$po_header_id = $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'po_header_id');
			}else{
				$po = $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'po_header_id');
				if($po_header_id != $po){
					return false;	
				}
				
			}
			$x++;
		}
		return $po_header_id;
	}
	
	
	
	if($b=='Generate APV') {
		$po_header_id = checkPO($checkList);
		if($po_header_id){
			
			$terms					= $options->getAttribute('po_header','po_header_id',$po_header_id,'terms');
			$supplier_id			= $options->getAttribute('po_header','po_header_id',$po_header_id,'supplier_id');
			$po_date				= $options->getAttribute('po_header','po_header_id',$po_header_id,'date');
			$project_id				= $options->getAttribute('po_header','po_header_id',$po_header_id,'project_id');
			$work_category_id		= $options->getAttribute('po_header','po_header_id',$po_header_id,'work_category_id');
			$sub_work_category_id	= $options->getAttribute('po_header','po_header_id',$po_header_id,'sub_work_category_id');
			
			$discount_amount = 0;
			if(!empty($checkList)) {
				$date = date("Y-m-d");
	
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
						user_id = '$user_id'
				") or die(mysql_error());
				$apv_header_id = mysql_insert_id();		
				$advance_payments = 0;
				foreach($checkList as $rr_header_id) {	
					$discount_amount += $options->getAttribute("rr_header",'rr_header_id',$rr_header_id,'discount_amount');
					$advance_payments += $options->getAttribute("rr_header",'rr_header_id',$rr_header_id,'advance_payment_amount');
					$result = mysql_query("select * from rr_detail where rr_header_id = '$rr_header_id'") or die(mysql_error());
					while($r = mysql_fetch_assoc($result)){
						mysql_query("
							insert into
								apv_detail
							set
								apv_header_id = '$apv_header_id',
								stock_id = '".$r['stock_id']."',
								quantity = '".$r['quantity']."',
								price = '".$r['cost']."',
								amount = '".$r['amount']."',
								rr_id = '".$rr_header_id."'
						") or die(mysql_error());
					}
				}
				$amount = $discount_amount+$advance_payments;
				mysql_query("
					update apv_header set discount_amount = '$amount' where apv_header_id = '$apv_header_id'
				") or die(mysql_error());
				
				header("Location: admin.php?view=687b880d1beb02fa41b1&apv_header_id=$apv_header_id");
	 		 }
			 
   	  }else{
	  	$msg = "PLEASE SELECT MRR#'s WITH THE SAME PO#";
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
        
        <div style="display:inline-block;">
        	Supplier: <br />
            <input type='text' name='supplier_name' class='textbox supplier' value='<?=$_REQUEST['supplier_name']?>'>            
            <input type="hidden" name="supplier_id" value="<?=($_REQUEST['supplier_name']) ? $_REQUEST['supplier_id'] : ""?>" />
        </div>
        
        <input type="submit" name="b" value="Search" />
        <!--<input type="button" name="b" value="Generate APV" onclick="j('#_dialog').dialog('open');" class="buttons" />             -->
        <input type="submit" name="b" value="Generate APV"  class="buttons" />
        <!--<input type="submit" name="b" value="Cancel" onclick="return approve_confirm();"  /> -->
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="hidden" id="rr_header_id" value="" />
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
                              rr_header
                         where
						 	po_header_id != '0'
						and
                              rr_header_id like '%$keyword%'
                    ";
					
				if($_REQUEST['search_po_header_id']){
				$sql.="
					and
						po_header_id = '".$_REQUEST['search_po_header_id']."'
				";	
				}
				
				if($_REQUEST['supplier_name']){
				$sql.="
					and
						supplier_id = '".$_REQUEST['supplier_id']."'
				";	
				}
					
				$sql.="
					order 
                            by rr_header_id desc
				";            
                    
                $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                        
                $i=$limitvalue;
                $rs = $pager->paginate();
            ?>
            <tr bgcolor="#C0C0C0">				
                <th width="20">#</th>
                <th width="20" align="center"><input type="checkbox" name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
                <th width="20"></th>
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
					
					$disable = (hasAPV($r['rr_header_id']) || $r['status'] == "S" || $r['status'] == "C")?"disabled='disabled'":"";
					
					$currentStats = $options->getTransactionStatusName($r[status]);
					
					if($currentStats == 'Cancelled'){
						$currentStats = "<div style='color: red;'>Cancelled</div>";
					}else if($currentStats == 'Saved'){
						$currentStats = "<div style='color: #000000; font-weight: bold'>Saved</div>";
					}else if($currentStats == 'Finished'){
						$currentStats = "<div style='color: green; font-weight: bold'>Finished</div>";
					}	
					
                    echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                    echo '<td width="20">'.$i.'</td>';
                    echo '<td><input type="checkbox" '.$disable.' name="checkList[]" value="'.$r[rr_header_id].'" onclick="document._form.checkAll.checked=false"></td>';
                    echo '<td width="15"><a href="admin.php?view=b0208e11d5f33ac78fc5&rr_header_id='.$r[rr_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
                    echo '<td width="15"><a href="admin.php?view='.$view.'&b=Print&rr_header_id='.$r[rr_header_id].'" title="Print"><img src="images/action_print.gif" border="0"></a></td>';
                    echo '<td>'.str_pad($r['rr_header_id'],8,"0",STR_PAD_LEFT).'</td>';	
                    echo '<td>'.str_pad($r['po_header_id'],8,"0",STR_PAD_LEFT).'</td>';	
                    echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
					echo '<td>'.$supplier.'</td>';	
                    echo '<td>'.$options->getPayTypeName($r[paytype]).'</td>';	
					echo '<td>'.$rr_in_display.'</td>';	
                    echo '<td>'.$currentStats.'</td>';	
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