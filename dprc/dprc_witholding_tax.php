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
$date = $_REQUEST['date'];
$date = (empty($date)) ? date("Y-m-d") : $date;
?>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        
        <!--<div style="display:inline-block;">
            Date <br />
            <input type="text" class="textbox datepicker" name="date" value="<?=$date?>" />
        </div> -->
                
     	<!--<input type="submit" value="Generate Report"  /> -->
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <a href="dprc/print_dprc_paid_wtax.php" target="_new"><input type="button" value="Print Paid w/tax"></a>
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="dprc/print_dprc_witholding_tax.php?date=<?=$date?>" width="100%" height="500">
        </iframe>
    </div>
    </div>
</div>
</form>