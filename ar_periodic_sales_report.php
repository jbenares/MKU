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
            <input type="text" class="textbox3 datepicker" id='fromdate' name="fromdate" value='<?php echo $_REQUEST[fromdate];?>' readonly='readonly' >
        </div>
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" id='todate' name="todate" value='<?php echo $_REQUEST[todate];?>' readonly='readonly' >
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
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="ar_print_periodic_sales_report.php?fromdate=<?=$_REQUEST['fromdate'];?>&amp;todate=<?=$_REQUEST['todate'];?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>