<style type="text/css">
.pagination tr td{ font-weight:bold; }
.pagination tr td a{ font-weight:normal; border:1px solid #c0c0c0; padding:3px; margin-left:1px; margin-right:1px; }
</style>


<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	var TSort_Data = new Array ('my_table', '','','S','s','s');
	tsRegister();
	// -->
</script>
<?php
function getNextCV(){
	$result = mysql_query("
		select * from cv_header where status != 'C' order by cv_no desc
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return ($r['cv_no'] + 1);
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
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	$search_supplier		= $_REQUEST['search_supplier'];
	$search_supplier_id		= ($search_supplier)?$_REQUEST['search_supplier_id']:"";
	
	
	$ev_header_id		= $_REQUEST['ev_header_id'];
	$date				= $_REQUEST['date'];
	$vat				= $_REQUEST['vat'];
	$wtax				= $_REQUEST['wtax'];
	$vat_gchart_id		= $_REQUEST['vat_gchart_id'];
	$wtax_gchart_id		= $_REQUEST['wtax_gchart_id'];
	$supplier_id		= $_REQUEST['supplier_id'];
	$cash_gchart_id		= $_REQUEST['cash_gchart_id'];
	$project_id			= $_REQUEST['project_id'];
	$po_header_id		= $_REQUEST['po_header_id'];
	$labor_mat_po		= $_REQUEST['labor_mat_po'];
	
	
	$gchart_id		= $_REQUEST['gchart_id'];
	$amount			= $_REQUEST['amount'];
	
	$id	= $_REQUEST['id'];
	if($b == "Update PO Reference"){
		$options = new options();
		$po_headers = "";
		$po = explode(",",$_REQUEST[po_header_id]);
		//var_dump($po);
		for($i=0;$i<sizeof($po);$i++){
			if(!empty($po_headers)){
				$po_headers .=",";
			}
			if(!empty($po[$i])){
					$po_headers.= (int)$po[$i];
					$exp_id= $options->getAttribute("expense_po_header","po_header_id",(int)$po[$i],"exp_po_header_id");
					$amount+=$options->getAttribute("expense_po_detail","exp_po_header_id",$exp_id,"amount");
			}	
		}
		mysql_query("	
			update 
				ev_header
			set
				po_header_id = '$po_headers',
				labor_mat_po = '$labor_mat_po'
			where
				ev_header_id = '$ev_header_id'
		") or die(mysql_error());	
		$msg = "PO Reference Updated";
	}
	
	if($b == "Generate CV"){
		$result = mysql_query("
			select * from ev_header where ev_header_id = '$ev_header_id'	
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$_date = date("Y-m-d");
		$percent = 100;
		$supplier_id = $r['supplier_id'];
		$cash_gchart_id	= $r['cash_gchart_id'];
		$project_id = $r['project_id'];
		
		mysql_query("
			insert into cv_header (percent,cv_date,check_date,supplier_id,cash_gchart_id,status,user_id,type,wtax,vat,wtax_gchart_id,vat_gchart_id,cv_no) values
			('100','$_date','$_date','$supplier_id','$cash_gchart_id','S','$user_id','E','$wtax','$vat','$wtax_gchart_id','$vat_gchart_id','".getNextCV()."')
		") or die(mysql_error());
		
		$cv_header_id = mysql_insert_id();
		
		$result = mysql_query("select * from ev_detail where ev_header_id = '$ev_header_id'") or die(mysql_error());
		while($r = mysql_fetch_assoc($result)){
			$gchart_id = $r['gchart_id'];	
			$amount	= $r['amount'];
			$project_id = $r['project_id'];
			
			mysql_query("insert into cv_detail (cv_header_id,gchart_id,amount,project_id,ev_header_id) values ('$cv_header_id','$gchart_id','$amount','$project_id','$ev_header_id') ") or die(mysql_error());
		}
		
		header("Location:admin.php?view=d5d75cc95b82df7a5c4e&cv_header_id=$cv_header_id");
		
	}

	if($b == "Add"){
		mysql_query("
			insert into
				ev_detail
			set
				ev_header_id		= '$ev_header_id',
				gchart_id			= '$gchart_id',
				amount				= '$amount',
				project_id			= '$project_id'
		") or die(mysql_error());
		
	}else if($b == "DC"){
		mysql_query("
			delete from
				ev_detail
			where
				ev_detail_id = '$id'
		") or die(mysql_error());
		
	}
	
	if($b=="Submit"){
		$po_headers = "";
		$po = explode(",",$_REQUEST[po_header_id]);
		//var_dump($po);
		for($i=0;$i<sizeof($po);$i++){
			if(!empty($po_headers)){
				$po_headers .=",";
			}
			if(!empty($po[$i])){
					$po_headers.= (int)$po[$i];
					$exp_id= $options->getAttribute("expense_po_header","po_header_id",(int)$po[$i],"exp_po_header_id");
					$amount+=$options->getAttribute("expense_po_detail","exp_po_header_id",$exp_id,"amount");
			}
			
		}
		$query="
			insert into 
				ev_header
			set
				date		= '$date',
				supplier_id	= '$supplier_id',
				user_id		= '$user_id',
				status		= 'S',
				cash_gchart_id	= '$cash_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				wtax_gchart_id = '$wtax_gchart_id',
				vat_gchart_id = '$vat_gchart_id',
				po_header_id = '$po_headers',
				labor_mat_po = '$labor_mat_po'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$ev_header_id = mysql_insert_id();
		
		$msg="Transaction Saved";
		
	}else if($b=="Update"){
		$po_headers = "";
		$po = explode(",",$_REQUEST[po_header_id]);
		//var_dump($po);
		for($i=0;$i<sizeof($po);$i++){
			if(!empty($po_headers)){
				$po_headers .=",";
			}
			if(!empty($po[$i])){
					$po_headers.= (int)$po[$i];
					$exp_id= $options->getAttribute("expense_po_header","po_header_id",(int)$po[$i],"exp_po_header_id");
					$amount+=$options->getAttribute("expense_po_detail","exp_po_header_id",$exp_id,"amount");
			}
			
		}
		$query="
			update
				ev_header
			set
				date		= '$date',
				supplier_id	= '$supplier_id',
				user_id		= '$user_id',
				status		= 'S',
				cash_gchart_id	= '$cash_gchart_id',
				wtax = '$wtax',
				vat = '$vat',
				wtax_gchart_id = '$wtax_gchart_id',
				vat_gchart_id = '$vat_gchart_id',
				po_header_id = '$po_headers',
				labor_mat_po = '$labor_mat_po'
			where
				ev_header_id = '$ev_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				ev_header
			set
				status='C'
			where
				ev_header_id = '$ev_header_id'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";
		$options->cancelGL($ev_header_id,'ev_header_id','DV');
		
	}else if($b=="Finish"){
		$query="
			update
				ev_header
			set
				status='F'
			where
				ev_header_id = '$ev_header_id'
		";	
		
		mysql_query($query);
		
		$po_headers = "";
		$po = explode(",",$_REQUEST[po_header_id]);
		//var_dump($po);
		for($i=0;$i<sizeof($po);$i++){
			if(!empty($po_headers)){
				$po_headers .=",";
			}
			if(!empty($po[$i])){
					$po_headers.= (int)$po[$i];
					$exp_id= $options->getAttribute("expense_po_header","po_header_id",(int)$po[$i],"exp_po_header_id");
					$amount+=$options->getAttribute("expense_po_detail","exp_po_header_id",$exp_id,"amount");
			}	
		}
		
		$datetoday=date("Y-m-d H:i:s");
		$audit="Added by: ".$options->getUserName($_SESSION[userID])."on $datetoday, ";		
		
		$journal_id=$options->getJournalID("DV");
		$generalreference=$options->generateJournalReference($journal_id);
		$gl_account_id="s-".$_REQUEST[account_id]; //not yet used
			
		$query="
			insert into
				gltran_header
			set
				generalreference='$generalreference',
				date='$_REQUEST[date]',
				journal_id='$journal_id',
				account_id='$gl_account_id',
				audit='$audit',
				status='S',
				admin_id='$_SESSION[userID]',
				header_id='$ev_header_id',
				header = 'ev_header_id'
		";	
		mysql_query($query) or die(mysql_error());
		
		$gltran_header_id=mysql_insert_id();
		
		/*END OF INSERT*/
		if(!empty($po_headers)) {
			$s=mysql_query("SELECT * FROM ev_detail WHERE ev_header_id = '$ev_header_id'");
			$totalamount=0;
			while($rw=mysql_fetch_assoc($s)){
				$totalamount+=$rw[amount];
			}
			if($totalamount){
				$options->insertIntoGLDetails($gltran_header_id,2102,$totalamount,'',NULL,$GLOBALS[branch_id]);	
			}
		}else{
			$s=mysql_query("SELECT * FROM ev_detail WHERE ev_header_id = '$ev_header_id'");
			$totalamount=0;
			while($rw=mysql_fetch_assoc($s)){
				$totalamount+=$rw[amount];
				$acode = $options->getAttribute("gchart","gchart_id",$rw[gchart_id],"acode");
				$options->insertIntoGLDetails($gltran_header_id,$acode,$rw[amount],'',NULL,$GLOBALS[branch_id]);	
			}
		}
		
		if($totalamount){		
			$options->insertIntoGLDetails($gltran_header_id,1101,'',$totalamount,NULL,$GLOBALS[branch_id]);	
		}		
		mysql_query("
			insert into
				posted_headers
			set
				header_id='$ev_header_id',
				journal_code='JV',
				gltran_header_id='$gltran_header_id',
				header='ev_header_id'
		") or die(mysql_error());
		
		$msg = "Transaction Finished";
	}
	$query="
		select
			*
		from
			ev_header
		where
			ev_header_id ='$ev_header_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$date			= ($r['date']!="0000-00-00")?$r['date']:"";
	$supplier_id	= $r['supplier_id'];
	$account_name	= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";

	$user_id		= $r['user_id'];
	$status			= $r['status'];
	$cash_gchart_id	= $r['cash_gchart_id'];
	
	$wtax			= $r['wtax'];
	$vat			= $r['vat'];
	$wtax_gchart_id	= $r['wtax_gchart_id'];
	$vat_gchart_id	= $r['vat_gchart_id'];
	$po_header_id	= $r['po_header_id'];
	$labor_mat_po	= $r['labor_mat_po'];
	
?>

<?php
if( $status=="F" || $status == "C" ){
?>
<style type="text/css">
.cp_table tr td:nth-child(1),.cp_table  tr th:nth-child(1){
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
        <input type="text" class="textbox supplier"  name="search_supplier" value="<?=$search_supplier?>"  onclick="this.select();"/>
        <input type="hidden" name="search_supplier_id"  value="<?=$search_supplier_id?>" title="Please Select Supplier" />
    </div>   
    <div class="inline">
    	PO # : <br />
        <input type="text" class="textbox3" name="search_po_header_id" value="<?=$_REQUEST['search_po_header_id']?>"  />
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
			  ev_header
		where 
			1=1 
	";
	
if(!empty($search_supplier_id)){
$sql.="
	and
		supplier_id = '$search_supplier_id'	
	";
}
	
if(!empty($_REQUEST['search_po_header_id'])){
$sql.="
	and
		po_header_id = '$_REQUEST[search_po_header_id]'	
	";
}
$sql.="
		order 
			by date desc
	";
	
//echo $sql;
$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
		
$i=$limitvalue;
$rs = $pager->paginate();
?>
<div class="pagination">
	<?=$pager->renderFullNav("$view&b=Search&search_supplier_id=$search_supplier_id&search_supplier=$search_supplier")?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
	<thead><tr bgcolor="#C0c0c0">				
	    <td width="20"><b>#</b></td>
	    <td width="20"></td>
	    <td>EV # :</td>
	    <td><b>Date</b></td>
	    <td><b>Supplier</b></td>
	    <td><b>Status</b></td>
	</tr> 
</thead> 
<?php								
while($r=mysql_fetch_assoc($rs)) {
	
	echo '<tr bgcolor="'.$transac->row_color($i).'">';
	echo '<td width="20">'.++$i.'</td>';
	echo '<td width="15"><a href="admin.php?view='.$view.'&ev_header_id='.$r[ev_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
	echo '<td>'.str_pad($r['ev_header_id'],7,0,STR_PAD_LEFT).'</td>';
	echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
	echo '<td>'.$options->getSupplierName($r[supplier_id]).'</td>';	
	echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
	echo '</tr>';
}
?>
</table>
<div class="pagination">
<?=$pager->renderFullNav("$view&b=Search&search_supplier_id=$search_supplier_id&search_supplier=$search_supplier")?>
</div>
<?php
}else{
?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>DISBURSEMENT VOUCHER</div>
    
    <div class="module_actions">
        <input type="hidden" name="ev_header_id" id="ev_header_id" value="<?=$ev_header_id?>" />
        <input type="hidden" name="branch_id" id="branch_id" value="<?=$GLOBALS[branch_id]?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class='inline'>
            <div>Date: </div>        
            <div>
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
            </div>
        </div>    
        
        <div class='inline'>
            Supplier : <br />  
            <input type="text" class="textbox supplier" style="width:300px;" value="<?=$account_name?>"  onclick="this.select();"/>
            <input type="hidden" name="supplier_id"  id="account_id" value="<?=$supplier_id?>" title="Please Select Supplier" />

        </div>
        
        <div class="inline">
        	PO # : <br />
            <input type="text" class="textbox3" name="po_header_id" value="<?=$po_header_id?>" />
        </div>
        
        <br/>
 		<div class="inline">
            Cash Account : <br />
            <?=$options->option_chart_of_accounts($cash_gchart_id,'cash_gchart_id')?>
        </div>
        <!--<div class="inline">
        	Labor Mat PO # <em>(For Subcontractors)</em> : <br />
            <input type="text" class="textbox" name="labor_mat_po" value="<?=$labor_mat_po?>" />
        </div>-->
        
       <!-- <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project" />
        </div>  -->
        
        <br />
        
        
        
        <?php
        if(!empty($status)){
        ?>
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
        <?php
        }
        ?>
    </div>
    <div class="module_actions">
		<?php if($status=="S"){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="submit" name="b" id="b" value="Finish" />
        <?php }else if($status!="F" && $status!="C"){ ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php } ?>
        <?php if($b!="Print Preview" && !empty($status)){ ?>
           <!--<input type="submit" name="b" id="b" value="Print Preview" />!-->
        <?php } ?>
        <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
        <?php if($status!="C" && !empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Cancel" />
        <?php } ?>
        <?php if($status=="F"){ ?>
        <input type="submit" name="b" value="Generate CV" />
        <?php } ?>
        <?php if(!empty($status)){ ?>
        <input type="submit" name="b" value="Update PO Reference" />
        <?php } ?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
   	</div>
    <?php
	if( $status == 'S'):	
	?>
    <div class="module_actions">
        
    	<div class="inline">
        	Account : <br /> 
            <?php echo $options->option_chart_of_accounts('','gchart_id'); ?>
        </div>

        <div style="display:inline-block; margin-right:20px;">
        	<div>Amount : </div>        
            <div><input type="text" class="textbox3"  name="amount" value="<?=$amount?>"/></div>
        </div> 
        
        <input type="submit" name="b" value="Add"  />
    </div>
    
    
    <?php endif ?>
</div>
<?php
if($b == "Print Preview" && $ev_header_id){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$ev_header_id' width='100%' height='500'>
			</iframe>";
?>
<?php
}else{
?>
<div class="clearfix">
	<table cellpadding="5"  border="0" class="cp_table" style="width:100%;">
    <tr>
    	<th width="20" style="text-align:left;"></th>
    	<th style="text-align:left;">Account</th>
        <th style="width:20%; text-align:right;">Amount</th>
    </tr>
	<?php
    $result = mysql_query("
		select
			*
		from
			ev_header as h, ev_detail as d
		where
			h.ev_header_id = d.ev_header_id
		and
			h.ev_header_id = '$ev_header_id'		
	") or die(mysql_error());
	$total = 0;
	while($r = mysql_fetch_assoc($result)){
		$gchart_id	 	= $r['gchart_id'];
		$total_amount	= $r['amount'];
		$ev_detail_id	= $r['ev_detail_id'];
		$total			+= $r['amount'];
	?>		
   	<tr>
    	<td align="center"><a href="admin.php?view=<?=$view?>&ev_header_id=<?=$ev_header_id?>&b=DC&id=<?=$ev_detail_id?>" onclick="return approve_confirm();"><img src="images/trash.gif"  /></a></td>
   		<td align="left"><?=$options->getAttribute('gchart','gchart_id',$gchart_id,'gchart')?></td>
   		<td class="align-right"><?=number_format($total_amount,2,'.',',')?></td>
   	</tr>
	<?php } ?>
    <tr>
    	<td></td>
    	<td></td>
        <td style="text-align:right; font-weight:bold;"><?=number_format($total,2,'.',',')?></td>
    </tr>
	</table>		
</div>
<?php } ?>
<?php } ?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>