<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
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
	$submit=array('delete','add','update','print');
	foreach($submit as $key){
		if(!empty($_REQUEST[$key])){
			$b=$_REQUEST[$key];
			break;	
		}
	}
	
	$checkList = $_REQUEST['checkList'];
	$checkList2 = $_REQUEST['checkList2'];

	//Request Formulation Header ID
	$formulation_id=$_REQUEST[formulation_id];
	//$formulation_id="20110317-102423";
	
	if($b=='Delete') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from formulation_details where formulation_id='$formulation_id' and formulationdetail_id='$ch'");
		}
	  }
	  
	   if(!empty($checkList2)) {
		foreach($checkList2 as $ch) {	
			mysql_query("delete from formulation_details where formulation_id='$formulation_id' and formulationdetail_id='$ch'");
		}
	  }
	}else if ($b=='Add'){
		$material=$_REQUEST[material];
		$type=$_REQUEST[type];
		$quantity=$_REQUEST[quantity];
		$remarks=$_REQUEST[remarks];		
		
		//Insert to database the formulation_details 
		$query="insert into formulation_details set
			formulation_id='$formulation_id',
			type='$type',
			material='$material',
			quantity='$quantity',
			remarks='$remarks'
		";
		mysql_query($query) or die(mysql_error());		
	}else if($b=="Update"){
		
		$formulationcode=$_REQUEST[formulationcode];
		$formulationdate=$_REQUEST[formulationdate];
		$category=$_REQUEST[category];
		$description=$_REQUEST[description];
		$customername=$_REQUEST[customername];
		$finishedproduct=$_REQUEST[finishedproduct];
		
		//Process Formulation Date
		$formulationdate=explode('/',$formulationdate);
		$formulationdate="$formulationdate[2]-$formulationdate[0]-$formulationdate[1]";
		
		$query="
			update formulation_header set
				formulationcode='$formulationcode',
				formulationdate='$formulationdate',
				category='$category',
				description='$description',
				customername='$customername',
				finishedproduct='$finishedproduct'
			where
				formulation_id='$formulation_id'
		";
		
		mysql_query($query) or die(mysql_error());	
		
		
	}else if($b=="Print"){
		header("Location:admin.php?view=7fc22aed40086746a977&formulation_id=$formulation_id");	
	}
	
	$query="select 
					*
				from	
					formulation_header
				where
					formulation_id='$formulation_id'
		";
	$result=mysql_query($query);
	
	$r=mysql_fetch_assoc($result);
	
	//Process Formulation Date
	$formulationdate=explode('-',$r[formulationdate]);
	$formulationdate="$formulationdate[1]/$formulationdate[2]/$formulationdate[0]";
	
?>

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'>EDIT FORMULATION</div>
    <div class="module_actions">
    <form name="header_form" action="" method="post">
		<div class='inline'>
        	<div>Formulation Code : </div>        
            <div>
            	<input type="text" name="formulationcode" class="textbox3" value="<?=$r[formulationcode];?> " />
                <input type="hidden" name="formulation_id" value="<?=$r[formulation_id];?>" />
         	</div>
        </div>    	
        <div class='inline'>
        	<div>Description : </div>        
            <div>
                <input type="text" name="description" class="textbox3" value="<?=$r[description];?>"/>
            </div>
        </div>    	
        <div class='inline'>
        	<div>Formulation Date : </div>        
            <div>
            	<input type="text" name="formulationdate" id="formulationdate" class="textbox3" readonly="readonly" value="<?=$formulationdate;?>"/>
				<script language='JavaScript'>
                    new tcal ({
                        // form name
                        'formname': 'header_form',
                        // input name
                        'controlname': 'formulationdate'
                    });
                </script>						
            </div>
        </div>    	
        <div class='inline'>
        	<div>Category : </div>        
            <div>
            	<?php
					echo $options->getAllCategoryOptions($r[category]);
				?>
          	</div>
        </div>    
        
        <div class='inline'>
        	<div>Customer Name : </div>        
            <div><?php echo $options->getAccountOptions($r[customername]);?></div>
        </div>  
        
        <div class='inline'>
        	<div>Finished Product : </div>        
            <div>
            	<?php
					echo $options->getFinishedProductOptions($r[finishedproduct]);
				?>
          	</div>
        </div>
        
        <div class='inline'>
        	<div>Total Quantity : </div>        
            <div>
				<input type=text id="totalqty" class="textbox3" readonly="readonly"/>
          	</div>
        </div>
        
        <br />
        
         <div class='inline'>
        	<div>Price per Kilo : </div>        
            <div>
				<input type=text class="textbox3" readonly="readonly" id='pricePerKilo'/>
          	</div>
        </div>
        
        <br />
        <input type="submit" name="update" value="Update" />
        
        <hr />
                   
        <div style="display:inline-block; margin-right:20px;">
        	<div>Material : </div>        
            <div><?php echo $options->getMaterialOptions(); ?></div>
        </div>    	
        <div style="display:inline-block; margin-right:20px;">
        	<div>Type : </div>        
            <div><?php echo $options->getFormulationTypeOptions(); ?></div>
        </div>   
         <div style="display:inline-block; margin-right:20px;">
        	<div>Quantity : </div>        
            <div><input type="text" size="20" name="quantity" /></div>
        </div> 
        <div style="display:inline-block; margin-right:20px;">
        	<div>Remarks : </div>        
            <div><input type="text" class="textbox" name="remarks" /></div>
        </div> 	
        <br />
  		<div id="buttonsContent">
            <input type="submit" name="add" value="Add" />
            <input type="submit" name="delete" value="Delete" />
            <input type="button" name="print" value="Print" onclick="xajax_print_formulation('<?php echo $formulation_id;?>');"/>
        </div>
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div id="content">
        <div style="float:left; width:50%; text-align:center;">
            <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
                <?php
        
                    $sql="
                        SELECT
                            formulation_details.formulationdetail_id,
                            formulation_details.formulation_id,
                            formulation_details.type,
                            formulation_details.material,
                            formulation_details.quantity,
                            formulation_details.remarks,
                            productmaster.cost
                            FROM
                            formulation_details
                            INNER JOIN productmaster ON formulation_details.material = productmaster.stock_id
                            where formulation_details.formulation_id='$formulation_id'
                            and formulation_details.type='Macro'
                    ";
                    
                    $rs=mysql_query($sql);
                ?>
                <tr>
                    <td colspan="5" align="left">
                        <div style="font-weight:bolder; font-size:15px;">Macro</div>
                    </td>
                </tr>
                <tr bgcolor="#C0C0C0">				
                  <td width="20"><b>#</b></td>
                  <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('header_form', this)" title="Check/Uncheck All" /></td>
                  <td><b>Matrial</b></td>
                  <td><b>Type</b></td>
                  <td><b>Quantity</b></td>
                  <td><b>Price</b></td>
                  <td><b>Remarks</b></td>
          
                </tr>        
                <?php				
                    $totalMacroPrice=0;
                    $totalQuantity=0;				
                    while($r=mysql_fetch_assoc($rs)) {
                        
                        $price=number_format($r['cost']*$r['quantity'],3,'.','');
                        
                        echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                        echo '<td width="20">'.$i.'</td>';
                        echo '<td><input type="checkbox" name="checkList[]" value="'.$r[formulationdetail_id].'" onclick="document._form.checkAll.checked=false"></td>';
                        echo '<td>'.$options->getMaterialName($r[material]).'</td>';	
                        echo '<td>'.$r[type].'</td>';	
                        echo '<td><div align="right">'.$r[quantity].'</div></td>';
                        echo '<td><div align="right">'.$price.'</div></td>';	
                        echo '<td>'.$r[remarks].'</td>';	
                        echo '</tr>';
                        
                        $totalMacroPrice+=$price;
                        $totalQuantity+=$r['quantity'];
                    }
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div align="right" style="color:#F00; font-weight:bolder;"><?php echo number_format($totalQuantity,3);?></div></td>
                    <td><div align="right" style="color:#F00; font-weight:bolder;"><?php echo number_format($totalMacroPrice,3);?></div>
                    </td>
                    </tr>
                    
                </tr>
            </table>
        </div>
        <div style="float:right; width:50%; text-align:center;">
            <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
                <?php
                    $sql="
                        SELECT
                            formulation_details.formulationdetail_id,
                            formulation_details.formulation_id,
                            formulation_details.type,
                            formulation_details.material,
                            formulation_details.quantity,
                            formulation_details.remarks,
                            productmaster.cost
                            FROM
                            formulation_details
                            INNER JOIN productmaster ON formulation_details.material = productmaster.stock_id
                            where formulation_details.formulation_id='$formulation_id'
                            and formulation_details.type='Micro'
                    ";
                    $rs=mysql_query($sql);
                    
                ?>
                <tr>
                    <td colspan="5" align="left">
                        <div style="font-weight:bolder; font-size:15px;">Micro</div>
                    </td>
                </tr>
                <tr bgcolor="#C0C0C0">				
                  <td width="20"><b>#</b></td>
                  <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all2('header_form', this)" title="Check/Uncheck All" /></td>
                  <td><b>Matrial</b></td>
                  <td><b>Type</b></td>
                  <td><b>Quantity</b></td>
                  <td><b>Price</b></td>
                  <td><b>Remarks</b></td>
          
                </tr>        
                <?php				
                    $totalMicroPrice=0;
                    $microQty=0;
                    //$totalQuantity=0;		
                    $i=0;		
                    while($r=mysql_fetch_assoc($rs)) {
                        
                        $price=number_format($r['cost']*$r['quantity'],3,'.','');
                        
                        echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                        echo '<td width="20">'.$i.'</td>';
                        echo '<td><input type="checkbox" name="checkList2[]" value="'.$r[formulationdetail_id].'" onclick="document._form.checkAll.checked=false"></td>';
                        echo '<td>'.$options->getMaterialName($r[material]).'</td>';	
                        echo '<td>'.$r[type].'</td>';	
                        echo '<td><div align="right">'.$r[quantity].'</div></td>';
                        echo '<td><div align="right">'.$price.'</div></td>';	
                        echo '<td>'.$r[remarks].'</td>';	
                        echo '</tr>';
                        
                        $totalMicroPrice+=$price;
                        $totalQuantity+=$r['quantity'];
                        $microQty+=$r['quantity'];
                    }
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div align="right" style="color:#F00; font-weight:bolder;"><?php echo number_format($microQty,3);?></div></td>
                    <td><div align="right" style="color:#F00; font-weight:bolder;"><?php echo number_format($totalMicroPrice,3);?></div>
                    </td>
                    </tr>
                    
                </tr>
            </table>
        </div>
    </div>
    <div style="clear:both; font-weight:bolder; color:#F00; text-align:right">
    <?php
		$totalQty=(empty($totalQuantity))?1:$totalQuantity;
		$pricePerKilo=number_format(($totalMacroPrice+$totalMicroPrice)/$totalQty,3);
		//echo "<span style='color:#666;'><em>Price per Kilo:    </em></span>";
		//echo "P    ".$pricePerKilo;
	?>
    </div>
    </form>
</div>
<script type="text/javascript">
	document.getElementById('totalqty').value='<?php echo number_format($totalQuantity,3);?>';
	document.getElementById('pricePerKilo').value='<?php echo $pricePerKilo;?>';
</script>
