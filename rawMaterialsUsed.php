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
            <input type="text" class="textbox3" id='startdate' name="startdate" value='<?php echo $_REQUEST[startdate];?>' onclick=fPopCalendar("startdate"); readonly='readonly' >

        </div>
        
        <div style="display:inline-block;">
            End Date<br />
            <input type="text" class="textbox3" id='enddate' name="enddate" value='<?php echo $_REQUEST[enddate];?>' onclick=fPopCalendar("enddate"); readonly='readonly' >
        </div>
          <div class='inline'>
        	<div>Location: </div>        
            <div>
                <?php
					echo $options->getAllLocationOptions($_REQUEST[locale_id]);
				?> 
         	</div>
        </div>   
        <div class='inline'>
		  <div>Display Price ?: </div>        
	  	  <div>
			<?php
				echo $options->getPriceRequestOption($_REQUEST[priceoption]);
			?> 
		  </div>
   		</div>   
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST[startdate]) && !empty($_REQUEST[enddate]))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printRawMaterialsUsed.php?startdate=<?=$_REQUEST[startdate];?>&enddate=<?=$_REQUEST[enddate]?>&locale_id=<?=$_REQUEST[locale_id]?>&priceoption=<?=$_REQUEST[priceoption]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>