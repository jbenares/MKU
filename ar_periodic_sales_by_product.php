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
            From Date<br />
            <input type="text" class="textbox3 datepicker" id='fromdate' name="fromdate" value='<?php echo $_REQUEST[fromdate];?>' readonly='readonly' >
        </div>
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" id='todate' name="todate" value='<?php echo $_REQUEST[todate];?>' readonly='readonly' >
        </div>
        
        <!--
        <div class="inline">
            Location<br />
            <?=$options->getAllLocationOptions($_REQUEST[locale_id]);?>
        </div>
        
        -->
        
        <!--
        <div class="inline">
            Type<br />
            <?=$options->getTypeOptions($_REQUEST[type]);?>
        </div>
        -->
        
        <div class="inline">
            Category<br />
            <?=$options->getAllCategoryOptions($_REQUEST[category]);?>
        </div>
        
       <div class="inline">
         	Option <br />
			<select name="option">
            	<option value="">Select Option : </option>
                <option value="Amount" <?=($_REQUEST['option'] == "Amount")?"selected='selected'":""?>   >Amount</option>
                <option value="Quantity" <?=($_REQUEST['option'] == "Quantity")?"selected='selected'":""?> >Quantity</option>
            </select>
     	</div>
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST['fromdate']) && !empty($_REQUEST['todate']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="ar_print_periodic_sales_report_by_product.php?fromdate=<?=$_REQUEST['fromdate'];?>&amp;todate=<?=$_REQUEST['todate'];?>&amp;locale_id=<?=$_REQUEST[locale_id]?>&amp;todate=<?=$_REQUEST['todate'];?>&amp;type=<?=$_REQUEST[type]?>&amp;category=<?=$_REQUEST[category]?>&option=<?=$_REQUEST[option]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>