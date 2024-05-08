<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$filter			= $_REQUEST['filter'];

$stock_name		= ($_REQUEST['stock_id']) ? $_REQUEST['stock_name'] : "";
$stock_id		= (!empty($stock_name)) ? $stock_id : "";

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
.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}
</style>
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='../images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
    	<table class="table-form">
        	
            <tr>
            	<td>Project :</td>
                <td>
                	<input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
		            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
                </td>
            </tr>
			<tr>      
            	<td>Specification :</td>
                <td>
                    <input type="text" class="textbox stock_name" name="stock_name" value="<?=$stock_name?>" onclick="this.select();"  />
                    <input type="hidden" name="stock_id" value="<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>"  />
               	</td>
           	</tr> 
			<tr>
            	<td>From Date :</td>
                <td><input type="text" class="textbox datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td>To Date :</td>
                <td><input type="text" class="textbox datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' ></td>
            </tr>
			<tr>
            	<td>Filter :</td>
                <td>
                	<select name="filter">
                    	<option value="1" <?php if($filter == "1") echo "selected = 'selected'"; ?> >PROJECTS AND CLIENTS</option>
                        <option value="2" <?php if($filter == "2") echo "selected = 'selected'"; ?> >DBCCI PROJECTS</option>
                    </select>
                </td>
            </tr>
      	</table>
  	</div>
    <div class="module_actions">
      	<input type="submit" name="b" value="Generate Report"  />
       
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($b == "Generate Report"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="batching_plant/print_premix_delivery_report.php?
        	from_date=<?=$from_date?>&
            to_date=<?=$to_date?>&
            project_id=<?=($project_name) ? $project_id : ""?>&
			stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&
            filter=<?=$filter?>
            " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>