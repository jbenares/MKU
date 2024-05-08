<?php
$categ_id1 		= $_REQUEST['categ_id1'];
$categ_id2 		= $_REQUEST['categ_id2'];
$categ_id3 		= $_REQUEST['categ_id3'];
$categ_id4 		= $_REQUEST['categ_id4'];
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

<form name="newareaform" id="newareaform" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
            
        <div class="inline">
            Report Date<br />
            <input type="text" class="textbox3 datepicker" id='reportdate' name="reportdate" value='<?php echo ($_REQUEST[reportdate])?$_REQUEST[reportdate]:date("Y-m-d") ;?>' readonly='readonly' >
        </div>   
        
        <div class="inline">
        	Type of Quantity:
            <select name='type'>
                <option value='quantity' <?=( $_REQUEST['type'] == "quantity" ? "selected = 'selected'" : "" )?>  >Normal Qty</option>
                <option value='quantity_cum' <?=( $_REQUEST['type'] == "quantity_cum" ? "selected = 'selected'" : "" )?> >Optional Qty</option>
            </select>
        </div> 
        
        <?=$options->getCategoryOptionsEdit($categ_id1,$categ_id2,$categ_id3,$categ_id4);?>
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST[reportdate]) )
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_warehouse_inventory_balance_report.php?&
        	reportdate=<?=$_REQUEST[reportdate]?>&
            type=<?=$_REQUEST['type']?>&
            categ_id1=<?=$categ_id1?>&
            categ_id2=<?=$categ_id2?>&
            categ_id3=<?=$categ_id3?>&
            categ_id4=<?=$categ_id4?>
            " width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>