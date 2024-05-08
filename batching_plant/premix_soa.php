<?php
require_once(dirname(__FILE__).'/../library/lib.php');
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');

if($_REQUEST[printed]){
	$query = mysql_query("INSERT INTO soa_history(soa_id,project_id,from_date,to_date) 
							VALUES (NULL,'".$_REQUEST[project_id]."','".$_REQUEST[from_date]."','".$_REQUEST[to_date]."')");
	if($query){
		echo 1;
	}
}
?>
<script type="text/javascript">
function printIframe(id,project_id,from_date,to_date)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    j.post("batching_plant/premix_soa.php",{printed: 1,project_id: project_id,from_date:from_date,to_date:to_date},function(a){
		//alert(a);
	});
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
        	Project : <br />
            <input type="text" class="textbox project" value="<?=lib::getAttribute('projects','project_id',$_REQUEST['project_id'],'project_name')?>">
			<input type="hidden" name="project_id" value="<?=$_REQUEST['project_id']?>" >
        </div>
        <div style="display:inline-block;">
            From Date: <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
        </div>
                
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe','<?=$_REQUEST[project_id]?>','<?=$_REQUEST[from_date]?>','<?=$_REQUEST[to_date]?>');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if(!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) && !empty($_REQUEST['project_id']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="batching_plant/print_premix_soa.php?
        project_id=<?=$_REQUEST['project_id']?>&
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>
        " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>