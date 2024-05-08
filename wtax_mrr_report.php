<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$categ_id		= $_REQUEST['categ_id'];
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
             
        <div class="inline">
            From Date<br />
            <input type="text" class="textbox3 datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' >
        </div>    
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' >
        </div>         
		
        <div class="inline">
        	<?php $cleared = ($_REQUEST['cleared'])? " checked='checked'; " : "" ?>
			<input type="checkbox" name="cleared" value="1" <?=$cleared?> /> CLEARED
        </div>
        
        <div class="inline">
        	<?php $summary = ($_REQUEST['summary'])? " checked='checked'; " : "" ?>
			<input type="checkbox" name="summary" value="1" <?=$summary?> /> SUMMARY
        </div>
		
      	<input type="submit" name="b" value="Generate"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date && $b == "Generate"){ ?>
     	<?php if(!$_REQUEST['summary']){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_wtax_mrr_report.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&cleared=<?=$_REQUEST['cleared']?>" width="100%" height="500">
        </iframe>
        <?php } else { ?>
        <iframe id="JOframe" name="JOframe" frameborder="0" src="print_wtax_mrr_summary.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&cleared=<?=$_REQUEST['cleared']?>" width="100%" height="500">
        </iframe>
        <?php } ?>
    <?php } ?>
    </div>
</div>
</form>