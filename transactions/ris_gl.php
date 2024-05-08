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

	$stock_name 	= $_REQUEST['stock_name'];
	$stock_id		= ($stock_name) ? $_REQUEST['stock_id'] : "";
	$project_name	= $_REQUEST['project_name'];
	$project_id		= ($project_name) ? $_REQUEST['project_id'] : "";
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$account_id		= $_REQUEST['account_id'];
	
	if($b == "Post to GL"){
		$gl_project_id 		= $_REQUEST['gl_project_id'];	
		$issuance_detail_id = $_REQUEST['issuance_detail_id'];
		$amount 			= $_REQUEST['amount'];
		$gchart_id 			= $_REQUEST['gchart_id'];
		
		if(!empty($gchart_id)){
			
			$i = 0;
			$journal_id			= $options->getJournalID("JV");
			$generalreference	= $options->generateJournalReference($journal_id);
			
			$query="
				insert into
					gltran_header
				set
					generalreference	= '$generalreference',
					date				= '".date('Y-m-d')."',
					journal_id			= '$journal_id',
					status				= 'S',
					user_id				= '$_SESSION[userID]',
					account_id			= '',
					xrefer 				= '',
					header 				= 'issuance_header_id',
					header_id 			= '0'
			";	
			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();	
			#13 - DIRECT MATERIALS
			
			#echo "<pre>";
			#echo $query;
			#echo "</pre> <br>";
				
			foreach($gchart_id as $id){
				
				if(!empty($id)){
					#CREDIT DIRECT MATERIALS
					$query = "
						insert into gltran_detail set
							gltran_header_id = '$gltran_header_id',
							debit = '0',
							credit = '$amount[$i]',
							enable = 'Y',
							gchart_id = '13',
							project_id = '$gl_project_id[$i]'
					";
					mysql_query($query) or die(mysql_error());
					
					#echo "<pre>";
					#echo $query;
					#echo "</pre> <br>";
					
					#DEBIT ACCOUNT
					$query = "
						insert into gltran_detail set
							gltran_header_id = '$gltran_header_id',
							debit = '$amount[$i]',
							credit = '0',
							enable = 'Y',
							gchart_id = '$id',
							project_id = '$gl_project_id[$i]'
					";
					mysql_query($query) or die(mysql_error());
					
					#echo "<pre>";
					#echo $query;
					#echo "</pre> <br>";
					
					$query = "
						update issuance_detail set posted = '1', posted_to = '$gltran_header_id' where issuance_detail_id = '$issuance_detail_id[$i]'
					";
					mysql_query($query) or die(mysql_error());
					
					#echo "<pre>";
					#echo $query;
					#echo "</pre> <br>";
					
				}#END OF IF
				$i++;
			}#END OF FOREACH
			
			if(!empty($gltran_header_id)){
				$msg = "Transaction Finished and Posted. Click <a style='text-decoration:underline; font-weight:bold; color:#F00;' href='admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=$gltran_header_id'>me</a> to see Postings.";	
			}
		}
	}
?>

<form name="header_form" id="header_form" action="" method="post">
<div style='padding:5px; background-color:#F00; color:#FFF; font-weight:bold; font-size:11px;'>
	<em>"The display is only limitted to 100 items only."</em>
</div>
<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
<div class="module_actions">	
    <table class="table-form">
        <tr>
            <td>Item :</td>
            <td>
                <input type="text" class="textbox stock_name" name="stock_name" value="<?=$stock_name?>" onclick="this.select();"  />
                <input type="hidden" name="stock_id" value="<?=$stock_id?>"  />
            </td>
        </tr>	
        <tr>
            <td>Project :</td>
            <td>
                <input type="text" class="textbox project" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
                <input type="hidden" name="project_id" value="<?=$project_id?>"  />
            </td>
        </tr>	
        <tr>
            <td>From Date :</td>
            <td><input type="text" class="textbox datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' ></td>
        </tr>
        <tr>
            <td>To Date :</td>
            <td><input type="text" class="textbox datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' ></td>
        </tr>
        <tr>
        	<td>A/R :</td>
            <td>
            	<?=$options->getTableAssoc($account_id,'account_id','Select A/R','select * from account order by account asc','account_id','account')?>
            </td>
        </tr>        
        <tr>
        	<td colspan="2">
            	<?php
				$check_accountables = ($_REQUEST['accountables']) ? "checked='checked'" : "";
                ?>
            	<input type="checkbox" id="accountables" name="accountables" value="1" <?=$check_accountables?> />
                <label for="accountables">Check to see All A/R</label>
            </td>
        </tr>	
    </table>
</div>
<div class="module_actions">
	<input type="submit" name="b" value="Search" />
    <input type="submit" name="b" value="Post to GL" onclick="return approve_confirm();" />
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				

	<th style="width:5%;">RIS#</th>
    <th style="width:15%;">PROJECT</th>
	<th>ITEM</th>
	<th style="width:5%;">QTY</th>
    <th style="width:5%;">U.PRICE</th>
    <th style="width:5%;">AMOUNT</th>
    <th style="width:15%;">A/R</th>
    <th style="width:10%;">ACCOUNT</th>
</tr>  
<?php							
$query = "
	select 
		h.issuance_header_id,d.issuance_detail_id, stock, quantity, price, amount, account_id, h.project_id
	from 
		issuance_header as h, issuance_detail as d, productmaster as p
	where
		h.issuance_header_id = d.issuance_header_id
	and
		d.stock_id = p.stock_id
	and
		h.status = 'F'
	and
		posted = '0'
";	
if($_REQUEST['accountables']){
$query.="
	and
		account_id != '0'
";	
}
if($stock_id){
$query.="
	and d.stock_id = '$stock_id'
";	
}
if($project_id){
$query.="
	and h.project_id = '$project_id'
";	
}
if($from_date && $to_date){
$query.="
	and h.date between '$from_date' and '$to_date'
";	
}
if($account_id){
$query.="
	and d.account_id = '$account_id'
";	
}

$query.="
	limit 0,100
";

$result = mysql_query($query) or die(mysql_error());
while($r=mysql_fetch_assoc($result)) {
	
	echo "
		<tr>
			<td>".str_pad($r['issuance_header_id'],7,0,STR_PAD_LEFT)."</td>
			<td>".$options->getAttribute('projects','project_id',$r['project_id'],'project_name')."</td>
			<td>$r[stock]</td>
			<td style='text-align:right;'>".number_format($r['quantity'],2)."</td>
			<td style='text-align:right;'>".number_format($r['price'],2)."</td>
			<td style='text-align:right;'>".number_format($r['amount'],2)."</td>
			<td>".$options->getAttribute('account','account_id',$r['account_id'],'account')."</td>
			<td>
				<input type='text' class='textbox gchart'>
				<input type='hidden' name='gchart_id[]' >
			</td>
		</tr>
		
		<input type='hidden' name='issuance_detail_id[]' value='$r[issuance_detail_id]'>
		<input type='hidden' name='amount[]' value='$r[amount]'>
		<input type='hidden' name='gl_project_id[]' value='$r[project_id]' >
	";
}
?>
</table>
<div class="pagination">
	 <?=$pagination?>
</div>
</form>
<script type="text/javascript">
j(function(){	
	jQuery(".gchart").autocomplete({
		source: "autocomplete/gchart.php",
		minLength: 1,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
});
</script>
	