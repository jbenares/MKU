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
            From Date<br />
            <input type="text" class="textbox3" id='fromdate' name="fromdate" value='<?php echo $_REQUEST[fromdate];?>' onclick=fPopCalendar("fromdate"); readonly='readonly' >
        </div>
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3" id='todate' name="todate" value='<?php echo $_REQUEST[todate];?>' onclick=fPopCalendar("todate"); readonly='readonly' >
        </div>
        
        <div class="inline">
            Location<br />
            <?=$options->getAllLocationOptions($_REQUEST[locale_id]);?>
        </div>
        
        <div class="inline">
            Type<br />
            <?=$options->getTypeOptions($_REQUEST[type]);?>
        </div>
        
        <div class="inline">
            Category<br />
            <?=$options->getAllCategoryOptions($_REQUEST[category]);?>
        </div>
        
         <div class="inline">
            Report Type<br />
            <?=$options->getSalesReportOptions($_REQUEST[reporttype]);?>
        </div>
        
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST['fromdate']) && !empty($_REQUEST['todate']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printPeriodicSalesByProduct.php?fromdate=<?=$_REQUEST['fromdate'];?>&todate=<?=$_REQUEST['todate'];?>&locale_id=<?=$_REQUEST[locale_id]?>&todate=<?=$_REQUEST['todate'];?>&reporttype=<?=$_REQUEST[reporttype]?>&type=<?=$_REQUEST[type]?>&category=<?=$_REQUEST[category]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>