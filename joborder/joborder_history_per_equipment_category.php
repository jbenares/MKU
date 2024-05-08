<?php
define("REPORT_FILE", "joborder/print_joborder_history_per_equipment_category.php")
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
            
        <!-- <div class="inline">
        	Item : <br />
            <input type="text" class="textbox stock_name" name="stock_name"  value="<?=$_REQUEST['stock_name']?>" onclick="this.select();" />
            <input type="hidden" name="equipment_id" id="equipment_id" value="<?=(  ( !empty($_REQUEST['stock_name']) ) ? $_REQUEST['equipment_id'] : ""  )?>" />
        </div> -->

        <div style="display:inline-block;">
            From Date: <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
        </div>

        <div style="display:inline-block;">
            Equipment Category : <br>
            <?=lib::getTableAssoc($_REQUEST['eq_catID'],'eq_catID',"Select Equipment Cateogry","select * from equipment_categories","eq_catID","eq_cat_name")?>
        </div>
		<div class="inline">
        	Item : <br />
            <input type="text" class="textbox stock_name" name="stock_name"  value="<?=$_REQUEST['stock_name']?>" onclick="this.select();" />
            <input type="hidden" name="equipment_id" id="equipment_id" value="<?=(  ( !empty($_REQUEST['stock_name']) ) ? $_REQUEST['equipment_id'] : ""  )?>" />
        </div>

                
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if( !empty($_REQUEST['from_date']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="<?=REPORT_FILE?>?
        eq_catID=<?=$_REQUEST['eq_catID']?>&
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>&
	eqID=<?=$_REQUEST['equipment_id']?>
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


