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

        <div class="inline">
            EMPLOYEE <br>
            <input type="text" class="textbox accounts" name="account" value="<?=(( $_REQUEST['account'] ) ? $options->getAttribute('account','account_id',$_REQUEST['account_id'],'account') : "" )?>" />
            <input type="hidden" name="account_id" value="<?=(($_REQUEST['account']) ? $_REQUEST['account_id'] : "" )?>" />
        </div>
            
        <div class="inline">
        	Item  <br />
            <input type="text" class="textbox accountability" name="rr_detail"  value="<?=$_REQUEST['rr_detail']?>" onclick="this.select();" />
            <input type="hidden" name="rr_detail_id" id="rr_detail_id" value="<?=(  ( !empty($_REQUEST['rr_detail']) ) ? $_REQUEST['rr_detail_id'] : ""  )?>" />
        </div>
        <div style="display:inline-block;">
            From Date <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
        </div>
        
        <div style="display:inline-block;">
            To Date  <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
        </div>
                
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if(!empty($_REQUEST['rr_detail_id']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="reports/print_audit_of_accountability.php?
        rr_detail_id=<?=$_REQUEST['rr_detail_id']?>&
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>&
        account_id=<?=( ( $_REQUEST['account'] ) ? $_REQUEST['account_id'] : ""  )?>
        " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>
<script type="text/javascript">
jQuery(".accountability").autocomplete({
    source: "list_accountability.php",
    minLength: 1,
    select: function(event, ui) {
        jQuery(this).val(ui.item.value);
        jQuery(this).next().val(ui.item.rr_detail_id);        
    }
});
</script>