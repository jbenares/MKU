<?php

$stock_name 	= $_REQUEST['stock_name'];
$stock_id 		= ($stock_name)?$_REQUEST['stock_id']:"";
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
            
        <div style="display:inline-block;">
            Stock:<br />
			<input type="text" class="textbox" id="stock_name" name="stock_name" value="<?=$stock_name?>"  onclick="this.select();" />
            <input type="hidden" name="stock_id" id="stock_id" value="<?=$stock_id?>" />
        </div>
        
        <div class='inline'>
            Project : <br />  
            <!-- <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" title="Please select Project" /> -->
            <?=lib::getTableAssoc($project_id,'project_id',"Select Project","select * from projects where project_id not in (9) order by project_name asc",'project_id','project_name')?>
        </div>  
        
        <div class="inline">
        	From Date : <br />
        	<input type="text" class="textbox3 datepicker" name="from_date" value="<?=$_REQUEST['from_date']?>" />
        </div>	
        
        <div class="inline">
        	To Date : <br />
        	<input type="text" class="textbox3 datepicker" name="to_date" value="<?=$_REQUEST['to_date']?>" />
        </div>	
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($stock_id) && !empty($from_date) && !empty($to_date) && !empty($project_id) )
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_report_stock_card_all.php?stock_id=<?=$stock_id?>&project_id=<?=$project_id?>&from_date=<?=$from_date?>&to_date=<?=$to_date?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>