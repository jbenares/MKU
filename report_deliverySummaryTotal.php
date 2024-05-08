<?php
$fromdate		= $_REQUEST['fromdate'];
$todate			= $_REQUEST['todate'];
$account_id		= $_REQUEST['account_id'];
$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"";
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
            From Date : <br />
            <input type="text" class="textbox3 datepicker" name="fromdate" value='<?php echo ($fromdate)?$fromdate:date("Y-m-d") ;?>' readonly='readonly' >

        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="textbox3 datepicker" name="todate" value='<?php echo ($todate)?$todate:date("Y-m-d") ;?>' readonly='readonly' >

        </div>
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
	if(!empty($fromdate) && !empty($todate))
	{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_report_deliverySummaryTotal.php?fromdate=<?=$fromdate?>&todate=<?=$todate?>&account_id=<?=$account_id?>" width="100%" height="500">
        </iframe>
    </div>
    <?php
    }
	?>
    </div>
</div>
</form>