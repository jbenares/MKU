<?php
$b				= $_REQUEST['b'];
$project_name	= $_REQUEST['project_name'];
$project_id		= $_REQUEST['project_id'];
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];
$categ_id		= $_REQUEST['categ_id'];
$driverID		= $_REQUEST['driverID'];

$supplier 		= $_REQUEST['supplier'];
$po_header_id	= $_REQUEST['po_header_id'];
$mother_account_id	= $_REQUEST['mother_account_id'];

$stock_name		= ($_REQUEST['stock_id']) ? $_REQUEST['stock_name'] : "";
$stock_id		= (!empty($stock_name)) ? $stock_id : "";

function getSelect($name,$id,$array,$select_statement){
	$c = "
		<select name='$name' id='$name'>
			<option value=''>$select_statement</option>
	";
	if(!empty($array)){
		foreach($array as $key => $value){
			$c .= "
				<option value='$key' ".(($id == $key) ? "selected='selected'" : "")."  >$value</option>
			";
		}
	}
	
	$c .= "</select>";
	
	return $c;
}

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
	<div class="module_title"><img src="images/user_orange.png"><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
    
    	<!--<table class="table-form">
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

      	</table> -->
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
            	<td>Type:</td>
                <td>
                	<?php
					/*$a = array(
						'84' => 'ADVANCES TO OFFICERS & EMPLOYEES',
						'527' => 'ADVANCES TO SUBCONTRACTORS & OTHERS',
						'677' => 'ADVANCES TO SUPPLIERS',
						'740' => 'RETENTION PAYABLES'
					);
                    echo getSelect('mother_account_id',$mother_account_id,$a,'Select Mother Account');*/
					echo $options->getTableAssoc($_REQUEST[mother_account_id],"mother_account_id","Select Mother Account","Select * from gchart WHERE parent_gchart_id='0' and gchart_void!='1' order by gchart","gchart_id","gchart");
					?>
                      
                </td>
            </tr>
            <!--<tr>
            	<td>PO# :</td>
                <td><input type="text" class="textbox" name="po_header_id" value="<?=$po_header_id?>" /></td>
            </tr> -->
      	</table>
  	</div>
    <div class="module_actions">
      	<input type="submit" name="b" value="CA SUMMARY"  />
        <input type="submit" name="b" value="CA DETAILS"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php	if($b == "CA SUMMARY"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="acctg/print_ca_summary.php?
        	from_date=<?=$from_date?>&
            to_date=<?=$to_date?>&
            project_id=<?=($project_name) ? $project_id : ""?>&
            mother_account_id=<?=$mother_account_id?>
            " width="100%" height="500">
        </iframe>
    <?php } else if ($b == "CA DETAILS"){ ?>
    	<iframe id="JOframe" name="JOframe" frameborder="0" src="acctg/print_ca_details.php?
        	from_date=<?=$from_date?>&
            to_date=<?=$to_date?>&
            project_id=<?=($project_name) ? $project_id : ""?>&
            mother_account_id=<?=$mother_account_id?>
            " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>