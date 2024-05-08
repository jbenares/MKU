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
            From:<br />
            <input type="text" class="textbox3 datepicker" id='from' name="from" value='<?php echo $_REQUEST['from'];?>' readonly='readonly' >
        </div>
		
        <div class="inline">
            To:<br />
            <input type="text" class="textbox3 datepicker" id='to' name="to" value='<?php echo $_REQUEST['to'];?>' readonly='readonly' >
        </div>
        
        <div>
        	Subcontractor<br />
            <?=$options->getTableAssoc($_REQUEST['supplier_id'],'supplier_id',"Select Subcon","select * from supplier where subcon = '1' order by account asc",'account_id','account')?>
        </div>
		
        <br/>
     	<input type="submit" value="Generate Report"  />
		<!--<input type="reset" value="Reset"  />-->
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php if(!empty($_REQUEST['from']) && !empty($_REQUEST['to'])){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="subcon_print_retention_report.php?from=<?=$_REQUEST['from']?>&to=<?=$_REQUEST['to']?>&supplier_id=<?=$_REQUEST['supplier_id']?>" width="100%" height="500">
        </iframe>
    <?php }?>
    </div>
</div>
</form>