<?php
$project_name		= $_REQUEST['project_name'];
$project_id			= $_REQUEST['project_id'];

$contractor_name	= $_REQUEST['contractor_name'];
$contractor_id		= $_REQUEST['contractor_id'];

$account			= $_REQUEST['account'];

$account_id = $id	= ($account=="p")?$project_id:$contractor_id;
$header				= ($account=="p")?"project_id":"contractor_id";
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
            <input type="text" class="textbox3 datepicker" id='fromdate' name="fromdate" value='<?php echo $_REQUEST[fromdate];?>'  readonly='readonly' >
        </div>
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" id='todate' name="todate" value='<?php echo $_REQUEST[todate];?>' readonly='readonly' >
        </div>
        
         <div class="inline">
            	Account : <br />
                <select name="account" id="account">
               		<option value="" >Select Account : </option> 
                    <option value="p" <?=($account=="p")?"selected='selected'":""?> >Project</option>
                    <option value="c" <?=($account=="c")?"selected='selected'":""?>>Subcontractor</option>
                </select>
            </div>
            <?php
			if($account == "p"){
				$style="style='display:inline-block;'";	
			}else{
				$style="style='display:none;'";	
			}
            ?>
			<div class="inline" <?=$style?> id="div_project">
                Project : <br />
                <input type="text" class="textbox" name="project_name" value="<?=$project_name?>" id="project_name" onclick="this.select();" />
                <input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" title="Please Select Project" />
            </div>
            
            <?php
			if($account == "c"){
				$style="style='display:inline-block;'";	
			}else{
				$style="style='display:none;'";	
			}
            ?>
            <div class="inline" id="div_contractor" <?=$style?>>
                Subcontractor: <br />
                <input type="text" class="textbox" name="contractor_name" value="<?=$contractor_name?>" id="contractor_name" onclick="this.select();" />
                <input type="hidden" name="contractor_id" id="contractor_id" value="<?=$contractor_id?>" title="Please Select Contractor" />
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
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="ar_print_sales_by_customer.php?fromdate=<?=$_REQUEST['fromdate'];?>&amp;todate=<?=$_REQUEST['todate'];?>&amp;locale_id=<?=$_REQUEST[locale_id]?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>

<script type="text/javascript">
j(function(){
	j("#account").change(function(){
		var account = j(this).val();
		if(account == "p"){
			j("#div_project").show(500);		
			j("#div_contractor").hide(500);		
		}else{
			j("#div_project").hide(500);		
			j("#div_contractor").show(500);		
		}
	});
});
</script>