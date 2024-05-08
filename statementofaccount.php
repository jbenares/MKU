<?php
$startdate 		= $_REQUEST['startdate'];
$enddate		= $_REQUEST['enddate'];
if(!empty($_REQUEST['account_name'])){
	$account_id		= $_REQUEST['account_id'];
}
$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"";


?>

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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        
        <div style="display:inline-block;">
            Start Date<br />
			<input type="text" class="textbox3 datepicker" name="startdate" value='<?php echo ($startdate)?$startdate:date("Y-m-d") ;?>' readonly='readonly' >
        </div>
        
        <div style="display:inline-block;">
            End Date<br />
            <input type="text" class="textbox3 datepicker" name="enddate" value='<?php echo ($enddate)?$enddate:date("Y-m-d") ;?>' readonly='readonly' >
        </div>
        <div class='inline'>
            Account : <br />        
            <input type="text" class="textbox" id="account_name" name="account_name" value="<?=$account_name?>"  onmouseover="Tip('Leave empty to search all accounts');" onclick="this.select();"/>
            <input type="hidden" name="account_id"  id="account_id" value="<?=$account_id?>" />
        </div>   
        
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
		if(!empty($_REQUEST[startdate]) && !empty($_REQUEST[enddate]) && !empty($_REQUEST[account_id]))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printSOA.php?startdate=<?=$_REQUEST[startdate];?>&enddate=<?=$_REQUEST[enddate]?>&account_id=<?=$_REQUEST[account_id]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>