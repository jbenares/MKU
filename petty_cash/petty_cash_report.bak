<?php

	$from_date 		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];

	$sql = "select * from vehicle_pass where vh_void='0'";

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
	<div class="module_title"><img src='images/user_orange.png'>Generate Report - Petty Cash</div>
    <div class="module_actions">       
            
        <div style="display:inline-block;">
            From Date : <br />
            <input type="text" class="datepicker required textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=(!empty($from_date))?$from_date:date("Y-m-d")?>">
        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="datepicker required textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=(!empty($to_date))?$to_date:date("Y-m-d")?>">
        </div>
                
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php  if(!empty($from_date) && !empty($to_date) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="petty_cash/print_petty_cash_report.php?
            from_date=<?=$from_date?>&
            to_date=<?=$to_date?>
            " width="100%" height="500">
        </iframe>
    <?php }?>
    </div>
</div>
</form>