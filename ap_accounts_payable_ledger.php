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
        
        <div style="display:inline-block;">
            Start Date<br />
            <input type="text" class="textbox3 datepicker" id='startdate' name="startdate" value='<?php echo $_REQUEST[startdate];?>'  readonly='readonly' >

        </div>
        
        <div style="display:inline-block;">
            End Date<br />
            <input type="text" class="textbox3 datepicker" id='enddate' name="enddate" value='<?php echo $_REQUEST[enddate];?>' readonly='readonly' >
        </div>
          <div class='inline'>
        	<div>Supplier: </div>        
            <div>
                <?php
					echo $options->getSupplierOptions($_REQUEST[supplier_id]);
				?> 
         	</div>
        </div>   
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST[startdate]) && !empty($_REQUEST[enddate]) && !empty($_REQUEST[supplier_id]))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="ap_print_accounts_payable_ledger.php?startdate=<?=$_REQUEST[startdate];?>&amp;enddate=<?=$_REQUEST[enddate]?>&amp;supplier_id=<?=$_REQUEST[supplier_id]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>