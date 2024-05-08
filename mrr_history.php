<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$categ_id1		= $_REQUEST['categ_id1'];
$categ_id2       = $_REQUEST['categ_id2'];
$driverID		= $_REQUEST['driverID'];

$supplier 		= $_REQUEST['supplier'];
$po_header_id	= $_REQUEST['po_header_id'];

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
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
    	<table class="table-form">
        	<tr>      
            	<td>Item :</td>
                <td>
                    <input type="text" class="textbox stock_name" name="stock_name" value="<?=$stock_name?>" onclick="this.select();"  />
                    <input type="hidden" name="stock_id" value="<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>"  />
               	</td>
           	</tr>
            <tr>
            	<td>Project :</td>
                <td>
                	<input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
		            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
                </td>
            </tr>
            <tr>
            	<td>Category :</td>
                <td>
                <?php
				$query="select * from categories where level = '1' order by category asc";
				echo $options->getOptions('categ_id1',"Select Category",$query,"categ_id","category",$categ_id1)
				?>
                </td>
            </tr>
             <tr>
                <td>Sub Category :</td>
                <td>
                <?php
                $query="select * from categories where level = '2' order by category asc";
                echo $options->getOptions('categ_id2',"Select Category",$query,"categ_id","category",$categ_id2)
                ?>
                </td>
            </tr>
            
      	</table>
        <table class="table-form">
            <tr>
            	<td>From Date :</td>
                <td><input type="text" class="textbox datepicker" name="from_date" value='<?=$from_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td>To Date :</td>
                <td><input type="text" class="textbox datepicker" name="to_date" value='<?=$to_date?>' readonly='readonly' ></td>
            </tr>
            <tr>
            	<td>Supplier :</td>
                <td><input type="text" class="textbox" name="supplier" value="<?=$supplier?>" /></td>
            </tr>
            <tr>
            	<td>PO #: </td>
                <td><input type="text" class="textbox" name="po_header_id" value="<?=$po_header_id?>" /></td>
            </tr>
      	</table>
        <table class="table-form">
            <tr>
            	<td>Type:</td>
                <td>
                	<select name='rr_type'>
                        <option value='M' <?=($_REQUEST['rr_type'] == "M") ? "selected='selected'" : '' ?> >MERCHANDISE INVENTORY</option>
                        <option value='A' <?=($_REQUEST['rr_type'] == "A") ? "selected='selected'" : '' ?>>PROPERTY, PLANT, AND EQUIP.</option>
                    </select>
                </td>
            </tr>
            <!--<tr>
                <td>Driver :</td>
                <td>
                <?php
                $query="select * from drivers order by driver_name asc";
                echo $options->getOptions('driverID',"Select Driver",$query,"driverID","driver_name",$driverID)
                ?>                
                </td>
            </tr>-->
            <tr>
            	<td>Work Category</td>
                <td> <?=$options->option_workcategory($_REQUEST['work_category_id'],'work_category_id','Select Work Category')?></td>
            </tr>
            <tr>
            	<td>Sub Work Category</td>
                <td>
                	<div id="subworkcategory">
                        <select>
                            <option>Select Sub Work Category</option> 
                        </select>	
                    </div>
                </td>
            </tr>

      	</table>
        
  	</div>
    <div class="module_actions">
      	<input type="submit" name="b" value="Generate Report"  />
        <input type="submit" name="b" value="View All Projects"  />
        <!--<input type="submit" name="b" value="View All Suppliers"  />-->
        <input type="submit" name="b" value="View Summary"  />
        <input type="submit" name="b" value="View Item Summary"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date && $project_id && $b == "Generate Report"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_mrr_history.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&project_id=<?=$project_id?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&supplier=<?=$supplier?>&po_header_id=<?=$po_header_id?>&driverID=<?=$driverID?>&stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&rr_type=<?=$_REQUEST['rr_type']?>&account_id=<?=($_REQUEST['account_name']) ? $_REQUEST['account_id'] : ""?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>" width="100%" height="500">
        </iframe>
    <?php }else if ($from_date && $to_date && $b == "View All Projects") {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_mrr_history_all_projects.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&supplier=<?=$supplier?>&po_header_id=<?=$po_header_id?>&driverID=<?=$driverID?>&rr_type=<?=$_REQUEST['rr_type']?>&stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&account_id=<?=($_REQUEST['account_name']) ? $_REQUEST['account_id'] : ""?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>" width="100%" height="500">
        </iframe>
    <?php }else if ($from_date && $to_date && $b == "View All Suppliers") {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_mrr_history_all_suppliers.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&supplier=<?=$supplier?>&po_header_id=<?=$po_header_id?>&driverID=<?=$driverID?>&rr_type=<?=$_REQUEST['rr_type']?>&stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&account_id=<?=($_REQUEST['account_name']) ? $_REQUEST['account_id'] : ""?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>" width="100%" height="500">
        </iframe>
    <?php }else if ($from_date && $to_date && $b == "View Summary") {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_mrr_history_summary.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&supplier=<?=$supplier?>&po_header_id=<?=$po_header_id?>&rr_type=<?=$_REQUEST['rr_type']?>&driverID=<?=$driverID?>&stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&account_id=<?=($_REQUEST['account_name']) ? $_REQUEST['account_id'] : ""?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>" width="100%" height="500">
        </iframe>
    <?php }else if ($from_date && $to_date && $b == "View Item Summary") {?>
	    <iframe id="JOframe" name="JOframe" frameborder="0" src="print_mrr_history_summary_per_item.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&categ_id1=<?=$categ_id1?>&categ_id2=<?=$categ_id2?>&supplier=<?=$supplier?>&po_header_id=<?=$po_header_id?>&rr_type=<?=$_REQUEST['rr_type']?>&driverID=<?=$driverID?>&stock_id=<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>&account_id=<?=($_REQUEST['account_name']) ? $_REQUEST['account_id'] : ""?>&work_category_id=<?=$_REQUEST['work_category_id']?>&sub_work_category_id=<?=$_REQUEST['sub_work_category_id']?>&project_id=<?=$project_id?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    
    </div>
</div>
</form>
<script type="text/javascript">
jQuery(function(){
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	xajax_display_subworkcategory('<?=$_REQUEST['work_category_id']?>','<?=$_REQUEST['sub_work_category_id']?>');
});
</script>