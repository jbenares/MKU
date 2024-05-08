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
<style type="text/css">
.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
.table-fields{
	display:inline-table;	
}
.table-fields td:nth-child(1){
	text-align:right;
	font-weight:bold;	
}

</style>
<?php
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#SEARCH FILTERS
	$search_supplier		= $_REQUEST['search_supplier'];
	
	#HEADER
	$sub_apv_header_id		= $_REQUEST['sub_apv_header_id'];
	$po_header_id			= $_REQUEST['po_header_id'];
	$date					= $_REQUEST['date'];
	$po_date				= $_REQUEST['po_date'];
	$project_id 			= $_REQUEST['project_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	$supplier_id			= $_REQUEST['supplier_id'];
	$terms					= $_REQUEST['terms'];
	$remarks				= $_REQUEST['remarks'];
	$discount_amount		= $_REQUEST['discount_amount'];
	$retention_rate			= $_REQUEST['retention_rate'];
	$chargable_amount		= $_REQUEST['chargable_amount'];
	$other_chargable_amount	= $_REQUEST['other_chargable_amount'];
	
	$wtax_gchart_id			= $_REQUEST['wtax_gchart_id'];		
	$wtax					= $_REQUEST['wtax'];
	$vat_gchart_id			= $_REQUEST['vat_gchart_id'];
	$vat					= $_REQUEST['vat'];
	
	
	#DETAILS
	$description 			= $_REQUEST['description'];
	
	#SUB DETAILS
	$sub_description		= $_REQUEST['sub_description'];
	$unit					= $_REQUEST['unit'];
	$unit_cost				= $_REQUEST['unit_cost'];
	$amount					= $_REQUEST['amount'];
	
	#UPDATES
		
	$update_description		= $_REQUEST['update_description'];
	$update_sub_description	= $_REQUEST['update_sub_description'];
	$update_unit			= $_REQUEST['update_unit'];
	$update_unit_cost		= $_REQUEST['update_unit_cost'];
	$update_amount			= $_REQUEST['update_amount'];
	$update_quantity		= $_REQUEST['update_quantity'];
	$update_sub_apv_detail_id	= $_REQUEST['update_sub_apv_detail_id'];
	
	$id	= $_REQUEST['id'];
		
	if($b=="Submit"){
		
		$query="
			insert into
				sub_apv_header
			set
				po_header_id = '$po_header_id',
				date = '$date',
				po_date = '$po_date',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				wtax_gchart_id = '$wtax_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				discount_amount = '$discount_amount',
				remarks = '$remarks',
				user_id = '$user_id',
				retention_rate = '$retention_rate',
				chargable_amount = '$chargable_amount',
				other_chargable_amount = '$other_chargable_amount'
		";	
		mysql_query($query) or die(mysql_error());
		$sub_apv_header_id = mysql_insert_id();
		
		#ADD DETAILS
		
		$i = 0;
		
		if(!empty($update_quantity)){		
			foreach($update_quantity as $qty){
				
				$amount = $qty * $update_unit_cost[$i];
				
				$s = addslashes($update_sub_description[$i]);
				
				mysql_query("
					insert into
						sub_apv_detail
					set
						sub_apv_header_id = '$sub_apv_header_id',
						description = '$update_description[$i]',
						sub_description = '$s',
						quantity = '$qty',
						unit = '$update_unit[$i]',
						unit_cost = '$update_unit_cost[$i]',
						amount = '$amount'
				") or die(mysql_error());
				
				$i++;
			}
		}
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				sub_apv_header
			set
				po_header_id = '$po_header_id',
				date = '$date',
				po_date = '$po_date',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				wtax_gchart_id = '$wtax_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				discount_amount = '$discount_amount',
				remarks = '$remarks',
				user_id = '$user_id',
				retention_rate = '$retention_rate',
				chargable_amount = '$chargable_amount',
				other_chargable_amount = '$other_chargable_amount'
			where
				sub_apv_header_id = '$sub_apv_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		#UPDATE DETAILS
		
		$i = 0;
		
		if(!empty($update_quantity)){		
		
			
			foreach($update_quantity as $qty){
			
				$s = addslashes($update_sub_description[$i]);	
				$amount = $qty * $update_unit_cost[$i];
				
				mysql_query("
					update
						sub_apv_detail
					set
						sub_apv_header_id = '$sub_apv_header_id',
						description = '$update_description[$i]',
						sub_description = '$s',
						quantity = '$qty',
						unit = '$update_unit[$i]',
						unit_cost = '$update_unit_cost[$i]',
						amount = '$amount'
					where
						sub_apv_detail_id = '$update_sub_apv_detail_id[$i]'
				") or die(mysql_error());
				
				$i++;
			}
		}
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				sub_apv_header
			set
				status='C'
			where
				sub_apv_header_id = '$sub_apv_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		
	}else if($b=="Finish"){
		$query="
			update
				sub_apv_header
			set
				status='F'
			where
				sub_apv_header_id = '$sub_apv_header_id'
		";	
		
		mysql_query($query);
		$msg = "Transaction Finished";
	}
	
	
	if($sub_apv_header_id){
		$query="
			select
				*
			from
				sub_apv_header
			where
				sub_apv_header_id = '$sub_apv_header_id'
		";
		$result=mysql_query($query) or die(mysql_error());
		$r=mysql_fetch_assoc($result);
	}else{
		$query="
			select
				*
			from
				po_header
			where
				po_header_id ='$po_header_id'
		";
		$result=mysql_query($query) or die(mysql_error());
		$r=mysql_fetch_assoc($result);
	}
	
	$sub_apv_header_id		= $r['sub_apv_header_id'];
	$po_header_id			= $r['po_header_id'];
	$date					= $r['date'];
	$po_date				= $r['po_date'];
	$project_id 			= $r['project_id'];
	$work_category_id		= $r['work_category_id'];
	$sub_work_category_id	= $r['sub_work_category_id'];
	$supplier_id			= $r['supplier_id'];
	$terms					= $r['terms'];
	$remarks				= $r['remarks'];
	$discount_amount		= $r['discount_amount'];
	$other_chargable_amount	= $r['other_chargable_amount'];
	$retention_rate			= $r['retention_rate'];
	$chargable_amount		= $r['chargable_amount'];
	
	$wtax_gchart_id			= $r['wtax_gchart_id'];		
	$wtax					= $r['wtax'];
	$vat_gchart_id			= $r['vat_gchart_id'];
	$vat					= $r['vat'];
	
	#DO NOT REMOVE
	$status					= $r['status'];
	$user_id				= $r['user_id'];

?>

<?php
if( $status=="F" || $status == "C" ){
?>
<style type="text/css">
.hide{
	display:none;	
}
</style>
<?php
}
?>
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Supplier : <br />  
        <input type="text" class="textbox"  name="search_supplier" value="<?=$search_supplier?>"  onclick="this.select();"/>
    </div>   
    <input type="submit" name="b" value="Search" />
</div>


<?php if(empty($_REQUEST['po_header_id']) && empty($_REQUEST['b'])){  if(empty($sub_apv_header_id)){ die(""); }} ?>

<?php
if($b == "Search"){
?>
<?php
$page = $_REQUEST['page'];
if(empty($page)) $page = 1;
 
$limitvalue = $page * $limit - ($limit);

$sql = "select
			  *
		 from
			  sub_apv_header as h, supplier as s
		where
			h.supplier_id = s.account_id
	";
	
if(!empty($search_supplier)){
$sql.="
	and
		account like '%$search_supplier%'	
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
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier=$search_supplier")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				

    <th width="20"><b>#</b></th>
    <th width="20"></th>
    <th>SUB APV #</th>
    <th>PO #</th>
    <th><b>Date</b></th>
	<th><b>Project</b></th>
    <th><b>Supplier</b></th>
    <th><b>Status</b></th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&sub_apv_header_id='.$r[sub_apv_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['sub_apv_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.str_pad($r['po_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->getAttribute('projects','project_id',$r['project_id'],'project_name').'</td>';	
	echo '<td>'.$options->getSupplierName($r[supplier_id]).'</td>';	
	echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
	echo '</tr>';
}
?>
</table>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier=$search_supplier")?>
</div>
<?php
}else{
?>
<div class=form_layout>

	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>SUBCONTRACTOR APV</div>
    
    <div class="module_actions">
        <input type="hidden" name="sub_apv_header_id" value="<?=$sub_apv_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    	<table class="table-fields">
        	<tr>
            	<td>APV DATE:</td>
                <td><input type="text" name="date"  class="textbox datepicker" readonly="readonly" value="<?=!empty($sub_apv_header_id)?$date:""?>" title="Please Enter Date" /></td>
            </tr>
            
            <tr>
            	<td>PO DATE:</td>
                <td><input type="text" name="po_date" class="textbox datepicker" readonly="readonly" value="<?=empty($sub_apv_header_id)?$date:$po_date?>" title="Please Enter Date" /></td>
            </tr>
            
            <tr>
            	<td>PO #:</td>
                <td>
                	<input type="text" readonly="readonly" value="<?=(!empty($po_header_id)) ? str_pad($po_header_id,7,0,STR_PAD_LEFT) : "" ?>" class="textbox" />
                	<input type="hidden" name="po_header_id" value="<?=$po_header_id?>" />
               	</td>
            </tr>
            <tr>
            	<td>Project:</td>
                <td>
                	<input type="text" class="textbox" id="project_name" value="<?=$options->getAttribute('projects','project_id',$project_id,'project_name')?>" onclick="this.select();" readonly="readonly"  />
		            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project" />
               	</td>
            </tr>
       	</table>
        <table class="table-fields">
            <tr>
            	<td>Work Category:</td>
                <td>
                	<input type="text" class="textbox" value="<?=$options->getAttribute("work_category","work_category_id",$work_category_id,"work")?>" readonly="readonly" />
		            <input type="hidden" name="work_category_id" value="<?=$work_category_id?>"  />
                </td>
            </tr>
            <tr>
            	<td>Sub Work Category:</td>
                <td>	
                	<input type="text" class="textbox" value="<?=$options->getAttribute("work_category","work_category_id",$sub_work_category_id,"work")?>" readonly="readonly" />
		            <input type="hidden" name="sub_work_category_id" value="<?=$sub_work_category_id?>" />
                </td>
            </tr>
            <tr>
            	<td>Supplier:</td>
                <td>
                	<input type="text" class="textbox" name="supplier_id_display" value="<?=$options->getAttribute('supplier','account_id',$supplier_id,'account')?>" id="supplier_name" onclick="this.select();" readonly="readonly" />
		            <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
                </td>
            </tr>
            <tr>
            	<td>Terms ( Days ):</td>
                <td><input type="text" class="textbox" name="terms" id="term" value="<?=$terms?>" onmouseover="Tip('Term in Days. e.g. 90 for 90 days');" readonly="readonly" /></td>
            </tr>
            <tr>
            	<td>Discount Amount :</td>
                <td><input type="text" class="textbox" name="discount_amount" value="<?=$discount_amount?>" readonly="readonly" /></td>
            </tr>
            <tr>
            	<td></td>
                <td></td>
            </tr>
        </table>
        
        <table class="table-fields">
        	<tr>
            	<td>Vat (%):</td>
                <td><input type="text" class="textbox3" name="vat" value="<?=$vat?>" /></td>
            </tr>
            
            <tr>
            	<!--<td>Witholding Tax Account :</td>
                <td><?=$options->getTableAssoc($wtax_gchart_id,'wtax_gchart_id','SELECT WTAX ACCOUNT',"select * from gchart order by gchart asc",'gchart_id','gchart')?></td> -->
                <td>Witholding Tax (%) :</td>
                <td><input type="text" class="textbox3" name="wtax" value="<?=$wtax?>"/></td>
            </tr>
            
            <tr>
                <!--<td>Retention Payable :</td>
                <td><?=$options->option_chart_of_accounts($retention_gchart_id,'retention_gchart_id')?></td> -->
                <td>Retention Percent (%) :</td>
                <td><input type="text" class="textbox" name="retention_rate" value="<?=$retention_rate?>" /> </td>
            </tr>
            <tr>
                <!--<td>Chargable :</td>
                <td><?=$options->option_chart_of_accounts($chargable_gchart_id,'chargable_gchart_id')?></td> -->
                <td>Chargable Amount :</td>
                <td><input type="text" class="textbox" name="chargable_amount" value="<?=$chargable_amount?>" /> </td>
            </tr>
            <tr>
                <td>RMY Lending :</td>
                <td><input type="text" class="textbox" name="other_chargable_amount" value="<?=$other_chargable_amount?>" /> </td>
            </tr>
       	</table>
        <div>
            Remarks : <br />
            <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
        </div>   
        
        <?php if(!empty($status) && !empty($sub_apv_header_id)){ ?>
        <br />
        <div class='inline'>
            <div>Status : </div>        
            <div>
                <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
            </div>
        </div> 
        
        <div class='inline'>
            <div>Encoded by : </div>        
            <div>
                <input type='text' class="textbox" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
            </div>
        </div> 
        <?php } ?>
    </div>
    <div class="module_actions">
		<?php if($status=="S" && !empty($sub_apv_header_id)){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        <?php }else if(empty($sub_apv_header_id)){ ?>
        <input type="submit" name="b" id="b" value="Submit" onclick="return approve_confirm();" />
        <?php } ?>
        <?php if($b!="Print Preview" && !empty($sub_apv_header_id)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php } ?>
        <?php if($b!="Preview Ledger" && !empty($sub_apv_header_id)){ ?>
            <input type="submit" name="b" id="b" value="Preview Ledger" />
        <?php } ?>
        <?php if($b!="Preview CV Payments Ledger" && !empty($sub_apv_header_id)){ ?>
            <input type="submit" name="b" id="b" value="Preview CV Payments Ledger" />
        <?php } ?>
        <?php if($b=="Print Preview" || $b == "Preview Ledger" || $b == "Preview CV Payments Ledger"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
        <?php if($status!="C" && !empty($status) && !empty($sub_apv_header_id)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php } ?>
   	</div>
</div>
<?php
if($b == "Print Preview" && $sub_apv_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_subcontractor_apv.php?id=$sub_apv_header_id' width='100%' height='500'>
			</iframe>";
}else if($b == "Preview Ledger" && $sub_apv_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_subcontractor_ledger.php?id=$po_header_id' width='100%' height='500'>
			</iframe>";
}else if($b == "Preview CV Payments Ledger" && $sub_apv_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_subcontractor_payments_ledger.php?id=$po_header_id' width='100%' height='500'>
			</iframe>";
}else{
?>
    <div class="clearfix">
        <table cellpadding="3"  class="cp_table" style="width:100%;">
        <tr>
            <th style="text-align:left;">DESC</th>
            <th style="text-align:left;">SUB DESC</th>
            <th style="text-align:right;">QTY</th>
			<th style="text-align:right;">NO. OF PERSON</th>
            <th style="text-align:left;">UNIT</th>
            <th style="text-align:right;">UNIT COST</th>
            <th style="text-align:right;">AMOUNT</th>
        </tr>
        <?php
		if(empty($sub_apv_header_id)){
			$result = mysql_query("
				select
					*
				from
					po_header as h, spo_detail as d, sub_spo_detail as sub
				where
					h.po_header_id = d.po_header_id
				and
					d.spo_detail_id = sub.spo_detail_id
				and
					h.po_header_id = '$po_header_id'		
        ") or die(mysql_error());
		}else{
			$result = mysql_query("
				select
					*
				from
					sub_apv_header as h, sub_apv_detail as d
				where
					h.sub_apv_header_id = d.sub_apv_header_id
				and
					h.sub_apv_header_id = '$sub_apv_header_id'
			") or die(mysql_error());
		}
        while($r = mysql_fetch_assoc($result)){
            $spo_detail_id 	= $r['spo_detail_id'];
            $description	= $r['description'];
			$type		= $r['po_type'];
        ?>		
       	
        <tr>
        	<td><?=$r['description']?></td>
            <td><?=$r['sub_description']?></td>
            <td style="width:5%; text-align:right;"><input type='text' class="textbox3" name="update_quantity[]" style="text-align:right;" value="<?=$r['quantity']?>" onclick="this.select();" autocomplete="off" /></td>
			<td style="width:5%;text-align:right;"><?php
			if($type=="L"){
				echo $r['person'];
			}else{}
			?></td>
			<td style="width:5%;"><?=$r['unit']?></td>
            <td style="width:5%; text-align:right"><?=number_format($r['unit_cost'],4,'.',',')?></td>
            <td style="width:5%; text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td>
            <input type="hidden" name="update_description[]" value="<?=htmlspecialchars($r['description'])?>" />
            <input type="hidden" name="update_sub_description[]" value="<?=htmlspecialchars($r['sub_description'])?>" />
            <input type="hidden" name="update_unit[]" value="<?=$r['unit']?>" />
            <input type="hidden" name="update_unit_cost[]" value="<?=$r['unit_cost']?>" />
            <input type="hidden" name="update_sub_apv_detail_id[]" value="<?=$r['sub_apv_detail_id']?>" />
        </tr>
        <?php } ?>
        </table>		
    </div>
    <?php } ?>
<?php } ?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	