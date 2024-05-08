<?php
require_once(dirname(__FILE__).'/../library/lib.php');
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
            From Date: <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
        </div>

        <div style="display:inline-block;">
            To Date: <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
        </div>

        <div class="inline">
            Project <br>
            <input type="text" class="textbox project" name='to_project_name' value="<?=$_REQUEST['to_project_name']?>">
            <input type="hidden" name="to_project_id" value="<?php if( $_REQUEST['to_project_name'] ) echo $_REQUEST['to_project_id'] ?>" >
        </div>
                    
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if( !empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="raw_mat_fabrication/print_<?=basename(__FILE__)?>?
        from_date=<?=$_REQUEST['from_date']?>&        
        to_date=<?=$_REQUEST['to_date']?>&        
        to_project_id=<?php if( $_REQUEST['to_project_name'] ) echo $_REQUEST['to_project_id'] ?>        
        " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>


