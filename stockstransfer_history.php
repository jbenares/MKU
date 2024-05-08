<?php
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
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
            
        <!--<div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
        </div>   -->
        
        <div class="inline">
            From Date<br />
            <input type="text" class="textbox3 datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' >
        </div>    
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' >
        </div>    
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_stockstransfer_history.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&project_id=<?=$project_id?>" width="100%" height="500">
        </iframe>
    <?php }?>
    </div>
</div>
</form>