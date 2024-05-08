<?php
$project_name		= $_REQUEST['project_name'];
if(empty($project_name)){
	$project_id 	= "";
}else{
	$project_id 	= $_REQUEST['project_id'];
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
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        
        <div style="display:inline-block;">
            Start Date<br />
            <input type="text" class="textbox3 datepicker" id='startdate' name="startdate" value='<?php echo $_REQUEST[startdate];?>'  readonly='readonly' >

        </div>
        
        <div style="display:inline-block;">
            End Date<br />
            <input type="text" class="textbox3 datepicker" id='enddate' name="enddate" value='<?php echo $_REQUEST[enddate];?>' readonly='readonly' >
        </div>
        
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project"  />
        </div>
        
        
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST[startdate]) && !empty($_REQUEST[enddate]) && !empty($project_id))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="ar_print_report_account_ledger.php?startdate=<?=$_REQUEST[startdate];?>&amp;enddate=<?=$_REQUEST[enddate]?>&amp;project_id=<?=$project_id?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>