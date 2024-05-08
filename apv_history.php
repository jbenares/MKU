<?php
$b 				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];

$stock_name		= $_REQUEST['stock_name'];
$stock_id		= ($stock_name) ? $stock_id : "";

?>
<style type="text/css">
.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}
</style>
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
    
    	<table class="table-form">
            <tr>
            	<td>Project :</td>
                <td>
                	<input type="text" class="textbox project" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
		            <input type="hidden" name="project_id" value="<?=($_REQUEST['project_name']) ? $_REQUEST['project_id'] : ""?>"  />
                </td>
            </tr>	
            <tr>
            	<td>Supplier :</td>
                <td>
                	<input type="text" class="textbox supplier" name="supplier_name" value="<?=$_REQUEST['supplier_name']?>" onclick="this.select();"  />
		            <input type="hidden" name="supplier_id" value="<?=($_REQUEST['supplier_name']) ? $_REQUEST['supplier_id'] : ""?>"  />
                </td>
            </tr>	
            <tr>
            	<td>From Date :</td>
                <td><input type="text" class="textbox datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td>To Date :</td>
                <td><input type="text" class="textbox datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' ></td>
            </tr>
        </table>
        <table class="table-form">
            <tr>
            	<td>Work Category</td>
                <td> <?=$options->option_workcategory($_REQUEST['work_category_id'],'work_category_id','Select Work Category')?></td>
            </tr>
            <tr>
            	<td>Sub Work Category</td>
                <td>
                	<div id="subworkcategory">
                        <select>
                            <option>Select Sub Work Category</option> 
                        </select>	
                    </div>
                </td>
            </tr>
        </table>	
    </div>
    <div class="module_actions">
   		<input type="submit" name="b" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date && $b=="Generate Report"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_apv_history.php?
        	from_date=<?=$from_date?>&
            to_date=<?=$to_date?>&
            project_id=<?=$project_id?>&
            stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&
            driverID=<?=($_REQUEST['driver_name']) ? $_REQUEST['driverID'] : "" ?>&
            equipment_id=<?=($_REQUEST['equipment_name']) ? $_REQUEST['equipment_id'] : "" ?>&
            work_category_id=<?=$_REQUEST['work_category_id']?>&
            sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>&
            supplier_id=<?=($_REQUEST['supplier_name']) ? $_REQUEST['supplier_id'] : ""?>
            " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>
<script type="text/javascript">	
j(function(){
	jQuery(".eq_name").autocomplete({
		source: "dd_equipment_he.php",
		minLength: 1,
		select: function(event, ui) {
			j(this).val(ui.item.value);
			j(this).next().val(ui.item.id);
		}
	});
	
	jQuery(".driver_name").autocomplete({
		source: "list_drivers.php",
		minLength: 1,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	xajax_display_subworkcategory('<?=$_REQUEST['work_category_id']?>','<?=$_REQUEST['sub_work_category_id']?>');
});
</script>