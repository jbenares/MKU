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

.table-form tr td:nth-child(1){
	text-align:right;
	font-weight:bold;
}


</style>
<?php
	$b					= $_REQUEST['b'];
	
	$search_or_no		= $_REQUEST['search_or_no'];
?>	
	
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        OR # : <br />  
        <input type="text" class="textbox"  name="search_or_no" value="<?=$search_or_no?>"  onclick="this.select();"  autocomplete="off" />
    </div>   
    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
$page = $_REQUEST['page'];
if(empty($page)) $page = 1;
 
$limitvalue = $page * $limit - ($limit);

$sql = "
	select	
		or_date, or_no, postcode, payment_amount, date_encoded, penalized, customer_last_name, customer_first_name, customer_middle_name, customer_appel, p.remarks
	from 
		dprc_payment as p, application as a, customer as c
	where
		p.application_id = a.application_id
	and
		a.customer_id = c.customer_id
";
	
if(!empty($search_or_no)){
$sql.="
	and
		or_no like '%$search_or_no%'
";
}

$sql.="
	order by or_date asc, or_no asc
";

$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
		
$i=$limitvalue;
$rs = $pager->paginate();

$pagination	= $pager->renderFullNav("$view&b=Search&search_or_no=$search_or_no");
?>
<div class="pagination">
	<?=$pagination?>
</div>
<table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
<tr>				

	<th width="20">#</th>
	<td>OR Date</td>
	<td>OR No.</td>
	<td>Post Code</td>
	<td>Amount</td>
	<td>Date Encoded</td>
	<td>Penalize</td>
	<td>Customer</td>
	<td>Remarks</td>
</tr>  
<?php								
while($r=mysql_fetch_assoc($rs)) {
?>
	<tr>
    	<td><?=++$i?></td>
		<td><?=dprc::mdy($r['or_date'])?></td>
		<td><?=$r['or_no']?></td>
		<td><?=$options->getAttribute('dprc_post_codes','postcode',$r['postcode'],'postcode_desc')?></td>
		<td class="align-right"><?=number_format($r['payment_amount'],2)?></td>
		<td><?=dprc::mdy($r['date_encoded'])?></td>
		<td><?=($r['penalized']) ? "Yes" : "No" ?></td>
		<td><?="$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]"?></td>
		<td><?=$r['remarks']?></td>
	</tr>
<?php
}
?>
</table>
<div class="pagination">
	 <?=$pagination?>
</div>
<?php
/*if($b == "Print Preview" && $model){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$model' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	