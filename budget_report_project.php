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
<?php
$b = $_REQUEST['b'];
?>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        
         <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$_REQUEST['project_id']?>" class="required" title="Please select Project" />
        </div>  
        
      	<input type="submit" name="b" value="Budget Report"  />
		<input type="submit" name="b" value="Labor Budget Report"  />
		<input type="submit" name="b" value="SubCon Budget Balance Report"  />
        <input type="submit" name="b" value="Budget Balance VS RTP VS MRR Report"  />
		<input type="submit" name="b" value="Labor Budget VS PR VS PO VS Payroll"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php if(!empty($_REQUEST[project_id])&& $b == "Budget Report" ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="budget_print_project.php?&id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
   	 <?php } else if(!empty($_REQUEST[project_id])&& $b == "Budget Balance VS RTP VS MRR Report") { ?>
     	<iframe id="JOframe" name="JOframe" frameborder="0" src="print_budget_balance.php?&id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
     <?php }else if(!empty($_REQUEST[project_id])&& $b =="Labor Budget Report"){ ?>
		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_labor_budget_reports.php?&id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
	 <?php }else if(!empty($_REQUEST[project_id])&& $b =="Labor Budget VS PR VS PO VS Payroll"){ ?>
		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_labor_budget_balance.php?&id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
	 <?php }else if(!empty($_REQUEST[project_id])&& $b =="SubCon Budget Balance Report"){ ?>
		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_labor_budget_mat_balance.php?&id=<?=$_REQUEST['project_id']?>" width="100%" height="500">
        </iframe>
	 <?php } ?>
    </div>
</div>
</form>