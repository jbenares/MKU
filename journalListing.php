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
        
        <div class="inline">
            Starting Date<br />
            <input type="text" class="textbox3" id='startingdate' name="startingdate" value='<?php echo $_REQUEST[startingdate];?>' onclick=fPopCalendar("startingdate"); readonly='readonly' >
        </div>
        
        <div class="inline">
            Ending Date<br />
            <input type="text" class="textbox3" id='endingdate' name="endingdate" value='<?php echo $_REQUEST[endingdate];?>' onclick=fPopCalendar("endingdate"); readonly='readonly' >
        </div>
        
        <div class="inline">
            Journal<br />
            <?=$options->getJournalOptions($_REQUEST[journal_id]);?>
        </div>
        
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST['startingdate']) && !empty($_REQUEST['endingdate']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printJournalListing.php?startingdate=<?=$_REQUEST['startingdate'];?>&endingdate=<?=$_REQUEST['endingdate'];?>&journal_id=<?=$_REQUEST['journal_id']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>