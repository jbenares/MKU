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

</style>
<?php
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#SEARCH FILTERS
	$search_supplier		= $_REQUEST['search_supplier'];
	
	#HEADER
	$po_header_id			= $_REQUEST['po_header_id'];
	$date					= $_REQUEST['date'];
	$project_id 			= $_REQUEST['project_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	$supplier_id			= $_REQUEST['supplier_id'];
	$terms					= $_REQUEST['terms'];
	$remarks				= $_REQUEST['remarks'];
	$expense_gchart_id		= $_REQUEST['expense_gchart_id'];
	$ap_gchart_id			= $_REQUEST['ap_gchart_id'];
	$wtax					= $_REQUEST['wtax'];
	$vat					= $_REQUEST['vat'];
	
	#DETAILS
	$mat_stock_id		= $_REQUEST['mat_stock_id'];
	$mat_details		= $_REQUEST['mat_details'];
	$mat_quantity		= $_REQUEST['mat_quantity'];
	$mat_cost			= $_REQUEST['mat_cost'];
	$mat_amount			= $_REQUEST['mat_amount'];
	
	$labor_stock_id		= $_REQUEST['labor_stock_id'];
	$labor_unit		= $_REQUEST['labor_unit'];
	$labor_quantity		= $_REQUEST['labor_quantity'];
	$labor_cost			= $_REQUEST['labor_cost'];
	$labor_amount		= $_REQUEST['labor_amount'];
	
	$note				= $_REQUEST['note'];
		
	$id	= $_REQUEST['id'];
	
	#M-ATERIALS L-ABOR R-ENTALS
	if($b == "Add Material"){
		mysql_query("
			insert into
				po_detail
			set
				po_header_id = '$po_header_id',
				stock_id = '$mat_stock_id',
				quantity = '$mat_quantity',
				cost = '$mat_cost',
				amount = '$mat_amount',
				details = '$mat_details',
				_type = 'M'
		") or die(mysql_error());	
	}else if($b == "Add Labor"){
		mysql_query("
			insert into
				po_detail
			set
				po_header_id = '$po_header_id',
				stock_id = '$labor_stock_id',
				quantity = '$labor_quantity',
				cost = '$labor_cost',
				amount = '$labor_amount',
				_unit = '$labor_unit',
				_type = 'L'
		") or die(mysql_error());	
	}
	
	if($b == "Add"){
		mysql_query("
			insert into
				spo_detail
			set
				po_header_id		= '$po_header_id',
				description			= '$description'
		") or die(mysql_error());
		
	}else if($b == "DD"){
		#DD - DELETE DETAILS
		mysql_query("
			delete from
				po_detail
			where
				po_detail_id = '$id'
		") or die(mysql_error());
		
		$msg = "Detail Deleted";
	}
	if($b=="Submit"){
		$query="
			insert into 
				po_header
			set
				date = '$date',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				remarks = '$remarks',
				po_type = 'L',
				status = 'S',
				user_id = '$user_id',
				expense_gchart_id = '$expense_gchart_id',
				ap_gchart_id = '$ap_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				note = '$note'
		";	
		mysql_query($query) or die(mysql_error());
		
		$po_header_id = mysql_insert_id();
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$query="
			update
				po_header
			set
				date = '$date',
				project_id = '$project_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id',
				supplier_id = '$supplier_id',
				terms = '$terms',
				remarks = '$remarks',
				po_type = 'L',
				status = 'S',
				user_id = '$user_id',
				expense_gchart_id = '$expense_gchart_id',
				ap_gchart_id = '$ap_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				note = '$note'
			where
				po_header_id = '$po_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				po_header
			set
				status='C'
			where
				po_header_id = '$po_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		#$options->cancelGL($po_header_id,'po_header_id','JV');
		
	}else if($b=="Finish"){
		$query="
			update
				po_header
			set
				status='F'
			where
				po_header_id = '$po_header_id'
		";	
		
		mysql_query($query);
		$msg = "Transaction Finished";
	}
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
	
	$date					= $r['date'];
	$project_id 			= $r['project_id'];
	$work_category_id		= $r['work_category_id'];
	$sub_work_category_id	= $r['sub_work_category_id'];
	$supplier_id			= $r['supplier_id'];
	$terms					= $r['terms'];
	$remarks				= $r['remarks'];
	$expense_gchart_id		= $r['expense_gchart_id'];
	$ap_gchart_id			= $r['ap_gchart_id'];
	$note					= $r['note'];
	
	$wtax					= $r['wtax'];
	$vat					= $r['vat'];
	
	$date			= ($r['date']!="0000-00-00")?$date:"";
	$project_name	= $options->attr_Project($project_id,'project_name');
	$supplier_name	= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";
	$work_category		= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category	= $options->attr_workcategory($sub_work_category_id,'work');
	

	
	#DO NOT REMOVE
	$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$status			= $r['status'];
	$user_id		= $r['user_id'];

?>

<?php
if( $status=="F" || $status == "C" ){
?>
<style type="text/css">
.cp_table tr td:nth-child(1),.cp_table  tr th:nth-child(1){
	display:none;	
}
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
 	<a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

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
			  po_header as h, supplier as s
		where
			h.supplier_id = s.account_id
		and
			po_type = 'L'
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
    <th>PO # :</th>
    <th><b>Date</b></th>
    <th><b>Supplier</b></th>
    <th><b>Status</b></th>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr>';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&po_header_id='.$r[po_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['po_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
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
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view)?></div>
    
    <div class="module_actions">
        <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>" />
        <input type="hidden" name="view" value="<?=$view?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
    
        <div class='inline'>
            Date : <br />
            <input type="text" name="date" id="date" class="textbox3 datepicker" readonly="readonly" value="<?=$date;?>" title="Please Enter Date" />
        </div>    	
                
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project" />
        </div>   
        
        <div class="inline">
            Work Category : <br />
            <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
        </div>
        
        <div id="subworkcategory_div" style="display:none;" class="inline">
            Sub Work Category :
            <div id="subworkcategory">
                
            </div>
        </div>
        
        <br />
        <div class="inline" id="supplier_div">
            Supplier : <br />
            <input type="text" class="textbox" name="supplier_id_display" value="<?=$supplier_name?>" id="supplier_name" onclick="this.select();" />
            <input type="hidden" name="supplier_id" id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />
        </div>
        
        <div class="inline" id="div_terms">
            Terms ( Days ): <br />
            <input type="text" class="textbox" name="terms" id="term" value="<?=$terms?>" onmouseover="Tip('Term in Days. e.g. 90 for 90 days');" />
        </div>
        
        <div class="inline">
            Witholding Tax (%) : <br />
            <input type="text" class="textbox3"  name="wtax" value="<?=$wtax?>"  />
        </div>
        
        <div class="inline">
            VAT (%) : <br />
            <input type="text" class="textbox3"  name="vat" value="<?=$vat?>"  />
        </div>
        
        <br />
        
        <div style="display:inline-block;">
            Remarks : <br />
            <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
        </div>   
        
        <div style="display:inline-block;">
            Note : <br />
            <textarea class="textarea_small" name='note'><?=$note?></textarea>
        </div>   
        
         <br />
         
        <!--<div class="inline">
            Expense Account : <br />
            <?=$options->option_chart_of_accounts($expense_gchart_id,'expense_gchart_id')?>
        </div>
        
        <div class="inline">
            A/P Account : <br />
            <?=$options->option_chart_of_accounts($ap_gchart_id,'ap_gchart_id')?>
        </div> -->
        
        <?php if(!empty($status)){ ?>
        <br />
        <div class='inline'>
            <div>PO # : </div>        
            <div>
                <input type="text" readonly="readonly" value="<?=$po_header_id_pad?>" class="textbox3" />
            </div>
        </div>  
        
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
		<?php if($status=="S"){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        <?php }else if($status!="F" && $status!="C"){ ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php } ?>
        <?php if($b!="Print Preview" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Print Preview" />
        <?php } ?>
        <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
        <?php if($status!="C" && !empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php } ?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
   	</div>
    <?php
	if( $status == 'S'):	
	?>
    <div class="module_actions">
    	<input type="button" value="Click to PO Materials"  onclick="j('#dialog_materials').dialog('open');" />
        <input type="button" value="Click to PO Labor" onclick="j('#dialog_labor').dialog('open');" />
        <!--<input type="button" value="Click to PO Rentals" onclick="j('#dialog_rentals').dialog('open');" /> -->
    </div>
    
    
    <?php endif ?>
</div>
<?php
if($b == "Print Preview" && $po_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_labormat.php?id=$po_header_id' width='100%' height='500'>
			</iframe>";
}else{
?>
<div class="clearfix">
	<table cellpadding="3"  class="cp_table" style="width:100%;">
    <tr>
    	<th width="20"></th>
    	<th style="text-align:left;">DESCRIPTION</th>
        <th style="width:10%; text-align:right;">QTY</th>
        <th style="width:10%; text-align:left;">UNIT</th>
        <th style="width:10%; text-align:right;">UNIT COST</th>
        <th style="width:10%; text-align:right;">AMOUNT</th>
    </tr>
	<?php
    $result = mysql_query("
		select
			*
		from
			po_header as h, po_detail as d
		where
			h.po_header_id = d.po_header_id
		and
			h.po_header_id = '$po_header_id'		
	") or die(mysql_error());
	while($r = mysql_fetch_assoc($result)){
		$po_detail_id 	= $r['po_detail_id'];
		$stock_id = $r['stock_id'];
		$stock = $options->getAttribute('productmaster','stock_id',$stock_id,'stock');
		$quantity = $r['quantity'];
		$cost = $r['cost'];
		$amount = $r['amount'];
		$details = $r['details'];
		$unit = $options->getAttribute('productmaster','stock_id',$stock_id,'unit');
		$_unit = $r['_unit'];
		$_type = $r['_type'];
		
		if($_type == "M"){
			$unit = $unit;	
		}else{
			$unit = $_unit;	
		}
	?>		
   	<tr>
    	<td align="center"><a class="hide" href="admin.php?view=<?=$view?>&po_header_id=<?=$po_header_id?>&b=DD&id=<?=$po_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
   		<td><?php echo "$stock "; echo ($details)?"($details)":"" ?></td>
        <td style="text-align:right;"><?=number_format($quantity,4,'.',',')?></td>
        <td><?=$unit?></td>
        <td style="text-align:right;"><?=number_format($cost,4,'.',',')?></td>
        <td style="text-align:right;"><?=number_format($amount,4,'.',',')?></td>
   	</tr>
	<?php } ?>
	</table>		
</div>
<?php } ?>
<?php } ?>
</form>
<div id="dialog_materials">
    <div id="_dialog_content">
    	<div style="margin-bottom:5px;">
        	Item : <br />
           	<input type="text" class="textbox stock_name"  />
           	<input type="hidden" name="mat_stock_id" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Details : <br />
           	<input type="text" class="textbox" name="mat_details" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Quantity : <br />
           	<input type="text" class="textbox" name="mat_quantity" id="mat_quantity" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Cost : <br />
           	<input type="text" class="textbox" name="mat_cost" id="mat_cost" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Amount : <br />
           	<input type="text" class="textbox" name="mat_amount" id="mat_amount"  />
       	</div>
        
        <input type="submit" name="b" value="Add Material"  />
    </div>
</div>

<div id="dialog_labor">
    <div id="_dialog_content">
    	<div style="margin-bottom:5px;">
        	Item : <br />
           	<input type="text" class="textbox stock_name"  />
           	<input type="hidden" name="labor_stock_id" />
       	</div>        
        
        <div style="margin-bottom:5px;">
        	Quantity : <br />
           	<input type="text" class="textbox" name="labor_quantity" id="labor_quantity" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Unit : <br />
           	<input type="text" class="textbox" name="labor_unit" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Cost : <br />
           	<input type="text" class="textbox" name="labor_cost" id="labor_cost" />
       	</div>
        
        <div style="margin-bottom:5px;">
        	Amount : <br />
           	<input type="text" class="textbox" name="labor_amount" id="labor_amount"  />
       	</div>
        
        <input type="submit" name="b" value="Add Labor"  />
    </div>
</div>

<div id="dialog_rentals">
    <div id="_dialog_content">
        <input type="submit" name="b" value="Generate CV"  />
    </div>
</div>

<script type="text/javascript">
j(function(){	
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	j("#mat_cost,#mat_quantity").keyup(function(){
		var amount = j("#mat_cost").val() * j("#mat_quantity").val()
		j("#mat_amount").val(amount);
	});
	
	j("#labor_cost,#labor_quantity").keyup(function(){
		var amount = j("#labor_cost").val() * j("#labor_quantity").val()
		j("#labor_amount").val(amount);
	});
	
	<?php if(!empty($status)){ ?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php } ?>
	
	var dlg = j("#dialog_materials").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600, title : "Check Voucher Details"});
	dlg.parent().appendTo(jQuery("form:first"));
	
	var dlg = j("#dialog_labor").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600, title : "Check Voucher Details"});
	dlg.parent().appendTo(jQuery("form:first"));
	
	var dlg = j("#dialog_rentals").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600, title : "Check Voucher Details"});
	dlg.parent().appendTo(jQuery("form:first"));
});
</script>
	