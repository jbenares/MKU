<?php
$date			= $_REQUEST['date'];
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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
            
        <div style="display:inline-block;">
            Date<br />
            <input type="text" class="textbox3 datepicker" name="date" value='<?php echo ($date)?$date:date("Y-m-d") ;?>' readonly='readonly' >

        </div>
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
	if(!empty($date))
	{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_report_productionSchedule.php?date=<?=$date?>" width="100%" height="500">
        </iframe>
    </div>
    <?php
    }
	?>
    </div>
</div>
</form>