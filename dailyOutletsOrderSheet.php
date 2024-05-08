<?php
$b			= $_REQUEST['b'];
$date		= $_REQUEST['date'];

if(empty($date) && !empty($b) ){
	$error="Date Value Required";
}


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
<?php
if(!empty($error)){
?>
<div class="ui-state-error ui-widget" style="padding: 0 .7em;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<strong>Alert:</strong>
		<?=$error?>
	</p>
</div>
<?php
}
?>
<div class="form_layout">
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    
    <div class="module_actions">       
            
        <div style="display:inline-block;">
            Date :<br />
            <input type="text" class="textbox3 datepicker required" name="date" value="<?=$date?>" />
        </div>
      	
    </div>
    <div class="module_actions">
	    <input type="submit" value="Generate Report" name="b"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($date))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printDailyOutletsOrderSheet.php?date=<?=$date?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>