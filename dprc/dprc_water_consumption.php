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
	$b                = $_REQUEST['b'];
	$user_id          = $_SESSION['userID'];
	
	$wc_id            = $_REQUEST['wc_id'];
	
	$search_reference = $_REQUEST['search_reference'];
	
	$wc_id            = $_REQUEST['wc_id'];
	$customer_id      = $_REQUEST['customer_id'];
	$date             = $_REQUEST['date'];
	$volume           = $_REQUEST['volume'];
	$remarks          = $_REQUEST['remarks'];
	$reference        = $_REQUEST['reference'];
	$water_cost       = $_REQUEST['water_cost'];
	
	if($b == 'D'){ 
		mysql_query("
			delete from
				dprc_water_consumption
			where
				wc_id = '$wc_id'
		") or die(mysql_error());
		$msg = "Transaction Deleted";
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				dprc_water_consumption
			set
				customer_id          = '$customer_id',
				date                 = '$date',
				volume               = '$volume',
				remarks              = '$remarks',
				reference            ='$reference',
				water_cost           = '$water_cost',
				user_id              = '$user_id',
				
				prev_reading_date    = '$_REQUEST[prev_reading_date]',
				prev_reading         = '$_REQUEST[prev_reading]',
				present_reading_date = '$_REQUEST[present_reading_date]',
				present_reading      = '$_REQUEST[present_reading]'
		";	
		
		mysql_query($query) or die(mysql_error());
		$wc_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				dprc_water_consumption
			set
				customer_id          = '$customer_id',
				date                 = '$date',
				volume               = '$volume',
				remarks              = '$remarks',
				reference            ='$reference',
				water_cost           = '$water_cost',
				user_id              = '$user_id',
				
				prev_reading_date    = '$_REQUEST[prev_reading_date]',
				prev_reading         = '$_REQUEST[prev_reading]',
				present_reading_date = '$_REQUEST[present_reading_date]',
				present_reading      = '$_REQUEST[present_reading]'				
			where
				wc_id = '$wc_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	$query="
		select
			*
		from
			dprc_water_consumption 
		where
			wc_id = '$wc_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r = $aVal = mysql_fetch_assoc($result);
	
	$customer_id	= $r['customer_id'];
	$date			= $r['date'];
	$volume			= $r['volume'];
	$remarks		= $r['remarks'];
	$reference		= $r['reference'];
	$water_cost		= $r['water_cost'];
	$user_id		= $r['user_id'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Search Reference : <br />  
        <input type="text" class="textbox"  name="search_reference" value="<?=$search_reference?>"  onclick="this.select();"  autocomplete="off" />
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
    
    $sql = "
		select
        	*
        from
			dprc_water_consumption as wc, customer as c
		where 
			wc.customer_id = c.customer_id
    ";
        
    if(!empty($search_reference)){
    $sql.=" and reference like '$search_reference%' ";
    }
	
	$sql.=" order by date asc ";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_reference=$search_reference");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th>CUSTOMER</th>
        <th>DATE</th>
        <th>VOLUME</th>
        <th>AMOUNT</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        $amount - $r[volume] * $r[water_cost];
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&wc_id='.$r['wc_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'."$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name]".'</td>';	
		echo '<td>'."$r[date]".'</td>';	
		echo '<td>'."$r[volume]".'</td>';
		echo '<td>'.number_format($amount,2).'</td>';	
        echo '</tr>';
    }
    ?>
    </table>
    <div class="pagination">
	   	 <?=$pagination?>
    </div>
<?php
}else{
?>
    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>WATER CONSUMPTION MODULE</div>
        <div class="module_actions">
            <input type="hidden" name="wc_id" value="<?=$wc_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form">
            	<tr>
                	<td>Customer:</td>
                    <td><?=$options->getTableAssoc($customer_id,'customer_id','Select Customer',"select * from customer order by customer_first_name asc",'customer_id','customer_last_name', array("customer_first_name",'customer_middle_name','customer_last_name'))?></td>
                </tr>
                <tr>
                	<td>Date:</td>
                    <?php $date = (empty($date)) ? date("Y-m-d") : $date ?>
                    <td><input type="text" class="textbox datepicker"  name="date" value="<?=$date?>"/></td>
                </tr>

                <tr>
                	<td>Previous Reading Date</td>
                	<td><input type="text" class="textbox datepicker" name="prev_reading_date" id="prev_reading_date" value="<?=$aVal['prev_reading_date']?>" readonly></td>
               	</tr>
               	<tr>
                	<td>Previous Reading Volume</td>
                	<td><input type="text" class="textbox" name="prev_reading" id="prev_reading" value="<?=$aVal['prev_reading']?>"></td>
                </tr>

                <tr>
                	<td>Present Reading Date</td>
                	<td><input type="text" class="textbox datepicker" name="present_reading_date" id="present_reading_date" value="<?=$aVal['present_reading_date']?>" readonly></td>
               	</tr>
               	<tr>
                	<td>Present Reading Volume</td>
                	<td><input type="text" class="textbox" name="present_reading" id="present_reading" value="<?=$aVal['present_reading']?>"></td>
                </tr>

                <tr>
                	<td>Volume:</td>
                    <td><input type="text" class="textbox"  name="volume" id="volume" value="<?=$volume?>" placeholder="CU.M." /></td>
                </tr>
                <tr>
               		<?php $water_cost = (empty($water_cost)) ? $options->getAttribute('dprc_setup','dprc_key','WATER_COST','dprc_value') : $water_cost ?> 
                	<td>Cost per cu.m.:</td>
                    <td><input type="text" class="textbox"  name="water_cost" value="<?=$water_cost?>"  readonly="readonly" /></td>
                </tr>
                <tr>
                	<td>Reference:</td>
                    <td><input type="text" class="textbox"  name="reference" value="<?=$reference?>" /></td>
                </tr>
                <tr>
                	<td style="vertical-align:top;">Remarks</td>
                    <td colspan="3">
                    	<textarea style="border:1px solid #c0c0c0; width:100%;" name="remarks"><?=$remarks?></textarea>
                    </td>
                </tr>
                
                <?php
				if(!empty($user_id))
				echo "
					<tr>
						<td>Encoded By:</td>
						<td><input type=\"text\" class=\"textbox\" style=\"width:100%;\" value=\"".$options->getUserName($user_id)."\" /></td>
					</tr>
				";
                ?>
            
            </table>
            
        </div>
        <div class="module_actions">
            <?php
            if(!empty($wc_id)){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php
            }else{
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php
            }
            ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
            <?php
			if(!empty($wc_id)){
            ?>
            <a href="admin.php?view=<?=$view?>&wc_id=<?=$wc_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
            <?php
			}
            ?>
        </div>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $dprc_water_consumption){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$dprc_water_consumption' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
jQuery(function(){	
	jQuery("#present_reading, #prev_reading").keyup(function(){
		var present_reading = parseFloat(jQuery("#present_reading").val());
		var prev_reading    = parseFloat(jQuery("#prev_reading").val());

		if( isNaN(present_reading) ) present_reading = 0;
		if( isNaN(prev_reading) ) prev_reading = 0;

		var volume = present_reading - prev_reading;
		jQuery("#volume").val(volume);

	});
});
</script>
	