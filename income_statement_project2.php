<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
       
        <div class="inline">
            From<br />
            <input type="text" class="textbox3 datepicker" name="from" value='<?php echo $_REQUEST['from'];?>' readonly='readonly' >
        </div>
        <div class="inline">
            To<br />
            <input type="text" class="textbox3 datepicker" name="to" value='<?php echo $_REQUEST['to'];?>' readonly='readonly' >
        </div>
		
		<div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$_REQUEST['project_id']?>" class="required" title="Please select Project" />
        </div> 

         <div class="inline">
            WHT %<br />
            <input type="text" class="textbox3" name="tax" value='<?php echo $_REQUEST['tax'];?>' >
        </div>
                
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
     if((!empty($_REQUEST['from']) && !empty($_REQUEST['to'])) || !empty($_REQUEST['project_id']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="new_print_income_statement2.php?from=<?=$_REQUEST['from'];?>&to=<?=$_REQUEST['to']?>&project_id=<?=$_REQUEST['project_id']?>&tax=<?=$_REQUEST['tax']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>