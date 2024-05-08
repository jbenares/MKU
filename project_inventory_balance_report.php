<?php
set_time_limit(0);
$project_id = $_REQUEST['project_id'];
$categ_id1  = $_REQUEST['categ_id1'];
$categ_id2  = $_REQUEST['categ_id2'];
$categ_id3  = $_REQUEST['categ_id3'];
$categ_id4  = $_REQUEST['categ_id4'];
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
<style type="text/css">
.error{
    font-weight: bold;
    color:#fff;
    background-color: red;
    padding:10px 5px;
} 
</style>

<form name="newareaform" id="newareaform" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
            
        <div class="inline">
            Report Date<br />
            <input type="text" class="textbox3 datepicker" id='reportdate' name="reportdate" value='<?php echo ($_REQUEST['reportdate'])?$_REQUEST['reportdate']:date("Y-m-d") ;?>' readonly='readonly' >
        </div>    
        
        <div class='inline'>
            Project : <br />  
            <input type="text" class="textbox" id="project_name" name="project_name" value="<?=$_REQUEST['project_name']?>" onclick="this.select();"  />
            <input type="hidden" name="project_id"  id="project_id" value="<?=$_REQUEST['project_id']?>" class="required" title="Please select Project" />
        </div>  
        
        <div class="inline">
        	Type of Quantity:
            <select name='type'>
                <option value='quantity' <?=( $_REQUEST['type'] == "quantity" ? "selected = 'selected'" : "" )?>  >Normal Qty</option>
                <option value='quantity_cum' <?=( $_REQUEST['type'] == "quantity_cum" ? "selected = 'selected'" : "" )?> >Optional Qty</option>
            </select>
        </div> 
        
        <?=$options->getCategoryOptionsEdit($categ_id1,$categ_id2,$categ_id3,$categ_id4);?>
        
      	<input type="submit" name="b" value="Generate Report"  />
        <input type="submit" name="b" value="All Projects Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php if( !empty($_REQUEST['reportdate']) && $_REQUEST['b']=="Generate Report" && !empty($project_id) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_project_inventory_balance_report.php?&reportdate=<?=$_REQUEST['reportdate']?>&project_id=<?=$project_id?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&categ_id3=<?=$categ_id3?>&categ_id4=<?=$categ_id4?>&type=<?=$_REQUEST['type']?>" width="100%" height="500">
        </iframe>
    <?php } else { ?>
        <p class="error">
            Unable to generate. Please fill in date, and Project.
        </p>
    <?php } ?>

    
    <?php if(!empty($_REQUEST['reportdate']) && $_REQUEST['b']=="All Projects Report" ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_project_inventory_balance_report_all.php?&reportdate=<?=$_REQUEST['reportdate']?>&project_id=<?=$project_id?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&categ_id3=<?=$categ_id3?>&categ_id4=<?=$categ_id4?>&type=<?=$_REQUEST['type']?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>