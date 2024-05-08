<?php
$b		= $_REQUEST['b'];
$fdate	= $_REQUEST['fdate'];
$tdate	= $_REQUEST['tdate'];

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
            From Date<br />
            <input type="text" class="textbox datepicker" name="fdate" value='<?=$fdate?>' readonly='readonly' >
        </div>
       <div class="inline">
            To Date<br />
            <input type="text" class="textbox datepicker" name="tdate" value='<?=$tdate?>' readonly='readonly' >
        </div>    		
        
        <div class="inline">
        	Item <br />
			<input type="text" class="textbox assets" name="asset" value='<?=$_REQUEST['asset']?>'>
            <input type="hidden" name="rr_detail_id" value="<?=($_REQUEST['asset']) ? $_REQUEST['rr_detail_id'] : "" ?>" />
        </div>

        <div class="inline">
            Asset Code<br>
            <input type="text" class="textbox" name="asset_code" value="<?=$_REQUEST['asset_code']?>">
        </div>
        <div class="inline">
            Category<br/>
            <?php
                echo $options->getCategory1OptionsEdit($_REQUEST[categ_id1]);
            ?>
		</div>	
		 <div class="inline">
		 Sub Category<br/>
			<?php
			  echo $options->getCategory2OptionsEdit($_REQUEST[categ_id2]);
			?>
			
        </div>
   	</div>
    <div class="module_actions">
      	<input type="submit" name="b" value="PPE SUMMARY"  />
        <input type="submit" name="b" value="PPE LAPSING SCHEDULE"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="Generate">
     <?php	if(!empty($fdate) && ($tdate ) && ($b == "PPE SUMMARY")){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_ppe_report_summary_of_ppe.php?fdate=<?=$fdate?>&tdate=<?=$tdate?>&
            rr_detail_id=<?=$_REQUEST['rr_detail_id']?>&
            asset_code=<?=$_REQUEST['asset_code']?>&categ1=<?=$_REQUEST[categ_id1]?>
			&categ2=<?=$_REQUEST[categ_id2]?>
        " width="100%" height="500">
        </iframe>
    <?php } else if (!empty($fdate)&& ($tdate)&& ($b == "PPE LAPSING SCHEDULE")) { ?>
    	<iframe id="JOframe" name="JOframe" frameborder="0" src="print_ppe_report_lapsing_schedule.php?categ1=<?=$_REQUEST[categ_id1]?>&categ2=<?=$_REQUEST[categ_id2]?>&fdate=<?=$fdate?>&tdate=<?=$tdate?>&rr_detail_id=<?=($_REQUEST['asset']) ? $_REQUEST['rr_detail_id'] : "" ?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>
<script type="text/javascript">
j(function(){
	j(".assets").autocomplete({
		source: "list_assets.php",
		minLength: 1,
		select: function(event, ui) {
			j(this).val(ui.item.value);
			j(this).next().val(ui.item.rr_detail_id);
		}
	});
});
</script>