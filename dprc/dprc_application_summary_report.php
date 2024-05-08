<?php
$from_date 	= $_REQUEST['from_date'];
$to_date	= $_REQUEST['to_date'];
?>
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
        
        <div style="display:inline-block;">
            From Date <br />
            <input type="text" class="textbox datepicker" name="from_date" value="<?=$from_date?>" />
        </div>
        
        <div style="display:inline-block;">
            From Date <br />
            <input type="text" class="textbox datepicker" name="to_date" value="<?=$to_date?>" />
        </div>
        
        <div style="display:inline-block;">
            <input type="radio" name="app_option" id="option_all" value="all" <?php if( $_REQUEST['app_option'] == "all" || empty($_REQUEST['app_option']) ) echo "checked" ?> ><label for="option_all">All</label> 
            <input type="radio" name="app_option" id="option_fully_paid" value="fully_paid" <?php if( $_REQUEST['app_option'] == "fully_paid" ) echo "checked" ?> ><label for="option_fully_paid">Fully Paid</label> 
            <input type="radio" name="app_option" id="option_with_balance" value="with_balance" <?php if( $_REQUEST['app_option'] == "with_balance" ) echo "checked" ?> ><label for="option_with_balance">With Balance</label>
        </div>

     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php if(!empty($from_date) && !empty($to_date)): ?>
    <div style="padding:3px; text-align:center;" id="content">
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="dprc/print_dprc_application_summary_report.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&app_option=<?=$_REQUEST['app_option']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php endif; ?>
    </div>
</div>
</form>