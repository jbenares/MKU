<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= ($project_name) ? $_REQUEST['project_id'] : "";
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$categ_id		= $_REQUEST['categ_id'];
$driverID		= $_REQUEST['driverID'];

$supplier 		= $_REQUEST['supplier'];
$po_header_id	= $_REQUEST['po_header_id'];

$stock_name		= ($_REQUEST['stock_id']) ? $_REQUEST['stock_name'] : "";
$stock_id		= (!empty($stock_name)) ? $stock_id : "";

$vat_type		= $_REQUEST['vat_type'];

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
    
    	<!--<table class="table-form">
        	<tr>      
            	<td>Item :</td>
                <td>
                    <input type="text" class="textbox stock_name" name="stock_name" value="<?=$stock_name?>" onclick="this.select();"  />
                    <input type="hidden" name="stock_id" value="<?=($_REQUEST['stock_name']) ? $_REQUEST['stock_id'] : ""?>"  />
               	</td>
           	</tr>-->
            
            <!--
            <tr>
            	<td>Category :</td>
                <td>
                <?php
				$query="select * from categories where level = '1' order by category asc";
				echo $options->getOptions('categ_id',"Select Category",$query,"categ_id","category",$categ_id)
				?>
                </td>
            </tr>
            <tr>
            	<td>Driver :</td>
                <td>
				<?php
                $query="select * from drivers order by driver_name asc";
                echo $options->getOptions('driverID',"Select Driver",$query,"driverID","driver_name",$driverID)
                ?>                
                </td>
            </tr>
      	</table> -->
        <table class="table-form">
        	<tr>
            	<td>Project :</td>
                <td>
                	<input type="text" class="textbox" id="project_name" name="project_name" value="<?=$project_name?>" onclick="this.select();"  />
		            <input type="hidden" name="project_id"  id="project_id" value="<?=$project_id?>" class="required" title="Please select Project" />
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
            	<td>VAT TYPE:</td>
                <td>
                	<select name='vat_type'>
                    	<option value=''>ALL</option>                        
                        <option value='NON-VAT' <?=($_REQUEST['vat_type'] == "NON-VAT") ? "selected='selected'" : '' ?> >NON-VAT</option>
                        <option value='VAT' <?=($_REQUEST['vat_type'] == "VAT") ? "selected='selected'" : '' ?>>VAT</option>
                    </select>
                </td>
            </tr>
            <!--<tr>
            	<td>Supplier :</td>
                <td><input type="text" class="textbox" name="supplier" value="<?=$supplier?>" /></td>
            </tr>
            <tr>
            	<td>PO #: </td>
                <td><input type="text" class="textbox" name="po_header_id" value="<?=$po_header_id?>" /></td>
            </tr> -->
      	</table>
        <!--<table class="table-form">
            <tr>
            	<td>Type:</td>
                <td>
                	<select name='rr_type'>
                        <option value='M' <?=($_REQUEST['rr_type'] == "M") ? "selected='selected'" : '' ?> >MERCHANDISE INVENTORY</option>
                        <option value='A' <?=($_REQUEST['rr_type'] == "A") ? "selected='selected'" : '' ?>>PROPERTY, PLANT, AND EQUIP.</option>
                    </select>
                </td>
            </tr>
      	</table> -->
        
  	</div>
    <div class="module_actions">
      	<input type="submit" name="b" value="Generate Report"  />
        <!--<input type="submit" name="b" value="View All Projects"  />
        <input type="submit" name="b" value="View Summary"  /> -->
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($from_date && $to_date && $b == "Generate Report"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="print_cash_receipts_history.php?from_date=<?=$from_date?>&to_date=<?=$to_date?>&project_id=<?=$project_id?>&vat_type=<?=$vat_type?>" width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>