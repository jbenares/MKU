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
            From Date<br />
            <input type="text" class="textbox3 datepicker" name="from_date" value='<?=$_REQUEST['from_date'];?>'  readonly='readonly'  />
        </div>
        
        <div style="display:inline-block;">
            To Date<br />
            <input type="text" class="textbox3 datepicker" name="to_date" value='<?=$_REQUEST['to_date'];?>'  readonly='readonly'  />
        </div>
      
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) )
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="dprc/print_water_receipts_history.php?from_date=<?=$_REQUEST['from_date']?>&to_date=<?=$_REQUEST['to_date']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>