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
            Report Date<br />
            <input type="text" class="textbox3 datepicker" id='reportdate' name="reportdate" value='<?php echo ($_REQUEST[reportdate])?$_REQUEST[reportdate]:date("Y-m-d") ;?>' readonly='readonly' >
        </div>    
        
         <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$_REQUEST['project_id']?>" class="required" title="Please select Project" />
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
		if(!empty($_REQUEST[reportdate]) )
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printInventoryBalanceReport.php?&reportdate=<?=$_REQUEST[reportdate];?>&priceoption=<?=$_REQUEST[priceoption]?>&project_id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>