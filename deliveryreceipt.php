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
	<div class="module_title"><img src='images/user_orange.png'>Delivery Reciept</div>
    <div class="module_actions">       
        <div style="display:inline-block;">
            Finished Product<br />
            <?php
                echo $options->getFinishedProductOptionsForJO($_REQUEST[finishedproduct]);
            ?>
        </div>
        <div style="display:inline-block;">
            Packaging Material<br />
            <?php
                echo $options->getPackagingMaterialOptions($_REQUEST[packagingmaterial]);
            ?>
        </div>
      	<input type="submit" value="Generate Delivery Receipt"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST[finishedproduct]))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printDeliveryReceipt.php?finishedproduct=<?=$_REQUEST[finishedproduct];?>&packagingmaterial=<?=$_REQUEST[packagingmaterial]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>