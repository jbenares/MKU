<?php
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$sortby			= $_REQUEST['sortby'];
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
    
    	<div class='inline'>
            Item : <br />  
            <input type="text" class="textbox stock_name" name="stock_name" value="<?=$stock_name?>" onclick="this.select();"  />
		    <input type="hidden" name="stock_id" value="<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>"  />
        </div>  
            
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
        </div>  
        
        <div class="inline">
            From Date<br />
            <input type="text" class="textbox3 datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' >
        </div>    
        
        <div class="inline">
            To Date<br />
            <input type="text" class="textbox3 datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' >
        </div>    
		
		<div class="inline">
            Sort By<br />
            <select name="sortby" class="textbox3" >
				<option <?php if($sortby == 'h.pr_header_id'){ echo 'selected';}?> value="h.pr_header_id">PR #</option>
				<option <?php if($sortby == 'h.date'){ echo 'selected';}?> value="h.date">PR DATE</option>
				<option <?php if($sortby == 'pr.project_name'){ echo 'selected';}?> value="pr.project_name">Project</option>
				<option <?php if($sortby == 'p.stock'){ echo 'selected';}?> value="p.stock" >Item</option>
				<option <?php if($sortby == 'd.quantity'){ echo 'selected';}?> value="d.quantity" >PR Quantity</option>
			</select>
        </div>  
		
		
        
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_outstanding_pr.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&sortby=<?=$sortby?>&project_id=<?=$project_id?>&stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>" width="100%" height="500">
        </iframe>
    <?php }?>
    </div>
</div>
</form>