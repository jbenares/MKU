<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$categ_id		= $_REQUEST['categ_id'];

$from_cv_no		= $_REQUEST['from_cv_no'];
$to_cv_no		= $_REQUEST['to_cv_no'];
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
        <!--<input type="button" value="Print" onclick="printIframe('JOframe');" /> -->
    </div>
    <div class="module_actions">
    	<div class="inline">
            From CV #<br />
            <input type="text" class="textbox3" name="from_cv_no" value='<?=$from_cv_no?>' >
        </div>    
        
        <div class="inline">
            To CV #<br />
            <input type="text" class="textbox3" name="to_cv_no" value='<?=$to_cv_no?>' >
        </div>    
    </div>
    <div class="module_actions">
	    <input type="submit" name="b" value="Print"  />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($b == "Print"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_batch_cv.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&from_cv_no=<?=$from_cv_no?>&to_cv_no=<?=$to_cv_no?>" width="100%" height="500" style="display:none;" >
        </iframe>
    <?php }?>
    <?php if($b == "Print") { ?>
    <div style="text-align:center; vertical-align:middle; font-size:12px; font-weight:bold;">
        <img src="images/109_.gif" /><br />Processing ...
    </div>
    <?php } ?>
    </div>
</div>
</form>