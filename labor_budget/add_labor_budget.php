<style type="text/css">
	.ui-widget-header{
		padding:6px;
		margin-top:0px;
		margin-bottom:0px;
	}
	.ui-widget-header h3{
		padding:0px;
		margin:0px;	
	}
	.ui-widget-content{
		padding:0px;	
	}
	.ui-widget-content ul{
		margin-left:20px;
	}
</style>

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
	
	$user_id = $_SESSION['userID'];
	$id		= $_REQUEST['id'];
	$b = $_REQUEST['b'];
	$project_id = $_REQUEST['project_id'];
	$work_category_id = $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	$remarks = $_REQUEST['remarks'];
	$date = $_REQUEST['date'];
	if($b=="Submit"){
		
		
			$query="
				insert into 
					labor_budget
				set
					project_id		= '$project_id',
					work_category_id = '$work_category_id',
					sub_work_category_id = '$sub_work_category_id',
					remarks	= '$remarks',
					date = '$date'
			";	
			
			mysql_query($query) or die(mysql_error());
			
			$labor_budget_id = mysql_insert_id();
					
			$msg="Transaction Saved";
		//echo '<script type="text/javascript">alert("Yes");</script>';
	}else {}
?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>LABOR BUDGET</div>
    <form name="header_form" id="header_form" action="" method="post">
    
   	<div style="width:50%; float:left;">
        <div class="module_actions">
            <input type="hidden" name="labor_budget_id" id="labor_budget_id" value="<?=$labor_budget_id?>" />
            <div id="messageError">
                <ul>
				
                </ul>
            </div>
            <div class="inline">
                Date : <br />
                <input type="text" name="date" class="textbox3 datepicker" value="<?=$date?>" readonly="readonly"  />
            </div>
            
            <div class='inline'>
                Project : <br />  
                <input type="text" class="textbox" id="project_name" value="<?=$project_name_code?>" onclick="this.select();"  />
                <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
            </div>   
            
            <div class="inline">
                Work Category : <br />
                <?=$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')?>
            </div>
            
            <div id="subworkcategory_div" style="display:none;" class="inline">
                Sub Work Category :
                <div id="subworkcategory">
                    
                </div>
            </div>
            
            <br />
            
            <div style="display:inline-block;">
                Remarks : <br />
                <textarea class="textarea_small" name='remarks'><?=$remarks?></textarea>
            </div>   
                      
        </div>
        <div class="module_actions">
                        
            <input type="submit" name="b" id="b" value="Submit" />            
	</div>    
            
     </form>
    
</div>
<script type="text/javascript">
j(function(){
		
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php
	}
	?>
});

</script>
	