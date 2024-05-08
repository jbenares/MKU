<?php
require_once(dirname(__FILE__).'/../library/lib.php');
?>
<?php
function getItemName($rr_detail_id){
    $result = mysql_query("
                select 
                    p.stock,d.details
                from
                    rr_detail as d, productmaster as p
                where
                    d.stock_id = p.stock_id
                and
                    d.rr_detail_id = '$rr_detail_id'
            ") or die(mysql_error()); 
    $r = mysql_fetch_assoc($result);
    if(mysql_num_rows($result)){
        return $r['stock']. "($r[details])";
    }else{
        return "";  
    }
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
        <div class='inline'>
	        PROJECT : <br />  
	        <input type="text" class="textbox project"  name="project_name" value="<?=$_REQUEST['project_name']?>"  onclick="this.select();"  autocomplete="off" />
	        <input type="hidden" name="project_id" value="<?php if($_REQUEST['project_name']) echo $_REQUEST['project_id'] ?>">
	    </div>   

	    <div class='inline'>
	        EMPLOYEE : <br />  
	        <input type="text" class="textbox"  name="employee_name" value="<?=$_REQUEST['employee_name']?>"  onclick="this.select();"  autocomplete="off" />
	    </div>           

        <div style="display:inline-block;">
            From Date: <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
        </div>

        <div style="display:inline-block;">
            ITEM: <br>
            <input type="text" class="textbox accountability-search" name='search_item_name' value="<?=($_REQUEST['search_item_name'] ? getItemName($_REQUEST['search_item_id']) : '' )?>" onclick='this.select();' />
            <input type="hidden" name="search_item_id" id="search_item_id" value="<?=($_REQUEST['search_item_name'] ? $_REQUEST['search_item_id'] : '')?>" />
        </div>
                
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if(!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="reports/print_accountability_history.php?        
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>&
        employee_name=<?=$_REQUEST['employee_name']?>&
        project_id=<?php if($_REQUEST['project_name']) echo $_REQUEST['project_id'] ?>&
        rr_detail_id=<?php if( $_REQUEST['search_item_name'] ) echo $_REQUEST['search_item_id'] ?>
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
j(".accountability-search").autocomplete({
        source: "list_accountability.php",
        minLength: 1,
        select: function(event, ui) {
            j(this).val(ui.item.value);
            j("#search_item_id").val(ui.item.rr_detail_id);         
        }
    });
</script>


