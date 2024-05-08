<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

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
<?php
$from = $_REQUEST['from'];
$to   = $_REQUEST['to'];
$b   = $_REQUEST['b'];
$options_bb   = $_REQUEST['options_bb'];

$year_bal = date("Y", strtotime($to));

/*
if($b == 'Archive Beginning Balance'){
	
	$sql = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.parent_gchart_id
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C'
					group by g.gchart
					") or die (mysql_error());	
					
		$balance = 0;
	while($r = mysql_fetch_assoc($sql)){
		$balance = $r['total_debit'] - $r['total_credit'];
		$gchart = $r['gchart_id'];
		
		mysql_query("Insert into gchart_beginning set gchart_id = '$gchart', year_bal = '$year_bal', date = NOW(), beg_bal = '$balance'") or die (mysql_error());
	}
}
*/
?>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
       
        <div class="inline">
            From<br />
            <input type="text" class="textbox3 datepicker" name="from" value='<?php echo $_REQUEST['from'];?>' readonly='readonly' >
        </div>
        <div class="inline">
            To<br />
            <input type="text" class="textbox3 datepicker" name="to" value='<?php echo $_REQUEST['to'];?>' readonly='readonly' >
        </div>
        <div class="inline">
			<select name="options_bb" value="<?=$_REQUEST['options_bb']?>" class="textbox">
				<option value="parent">Parent Accounts</option>
				<option value="all">Detailed Accounts</option>
			</select>
        </div>                
     	<input type="submit" value="Generate Report"  />
     	<!--<input type="submit" name="b" value="Archive Beginning Balance"  />-->
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
     if(!empty($_REQUEST['from']) && !empty($_REQUEST['to']) && !empty($_REQUEST['options_bb']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_beg_bal_report.php?from=<?=$_REQUEST['from'];?>&to=<?=$_REQUEST['to']?>&options_bb=<?=$_REQUEST['options_bb']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>