<?php
include_once('library/lib.php');

$b 				= $_REQUEST[b];
$stock_id		= $_REQUEST[stock_id];
$stockcode		= $_REQUEST[stockcode];
$barcode		= $_REQUEST[barcode];
$stock			= addslashes($_REQUEST[stock]);
$type			= $_REQUEST[type];
$categ_id1		= $_REQUEST[categ_id1];
$categ_id2		= $_REQUEST[categ_id2];
$categ_id3		= $_REQUEST[categ_id3];
$categ_id4		= $_REQUEST[categ_id4];
$unit			= $_REQUEST[unit];
$cost			= $_REQUEST[cost];
$price1			= $_REQUEST[price1];
$price2			= $_REQUEST[price2];
$price3			= $_REQUEST[price3];
$price4			= $_REQUEST[price4];
$price5			= $_REQUEST[price5];
$price6			= $_REQUEST[price6];
$price7			= $_REQUEST[price7];
$price8			= $_REQUEST[price8];
$price9			= $_REQUEST[price9];
$price10		= $_REQUEST[price10];
$reorderlevel	= $_REQUEST[reorderlevel];
$reorderqty		= $_REQUEST[reorderqty];
$supplier_id	= $_REQUEST[supplier_id];
$size					= $_REQUEST[size];
$description	= addslashes($_REQUEST[description]);
$buffer			= $_REQUEST[buffer];
$batching_plant_categ_id	= $_REQUEST['batching_plant_categ_id'];
$kg				= $_REQUEST['kg'];

$eq_catID			= $_REQUEST['eq_catID'];
$parent_stock_id	= $_REQUEST['parent_stock_id'];
$plate_num			= $_REQUEST['plate_num'];
$eq_model			= $_REQUEST['eq_model'];
$rate_per_hour		= $_REQUEST['rate_per_hour'];
$min_time			= $_REQUEST['min_time'];

$budget_category	= $_REQUEST['budget_category'];
$e_status			= $_REQUEST['e_status'];

if($b =="Submit"){
	if( !empty($stock)){

		$audit="Added by ".$options->getUserName($_SESSION[userID]);

		if($_REQUEST[manysuppliers]=='on')
		{
			$manysuppliers='1';
		}
		else
		{
			$manysuppliers='0';
		}

		/* Start File Manipulation for Upload */
		/*function upload_img($pic_size, $pic_type, $pic_tmp, $pic_name, $folder, $current_filename)*/

		$picSize=$_FILES[uploadedpicture][size];
		$picType=$_FILES[uploadedpicture][type];
		$picTemp=$_FILES[uploadedpicture][tmp_name];
		$picName=$_FILES[uploadedpicture][name];
		$folder="productmaster";
		$current_filename=NULL;


		$dir=$upload->upload_img($picSize,$picType,$picTemp,$picName,$folder,$current_filename);

		/* End File Manipulation for Upload */

		//$new_stockcode = $options->new_stockcode($_REQUEST[categ_id1]);

		$stock	= mysql_escape_string($_REQUEST["stock"]);

		$sql = "insert into productmaster set
					stockcode='$stockcode',
					barcode='$_REQUEST[barcode]',
					stock=\"$stock\",
					type='$_REQUEST[type]',
					categ_id1='$_REQUEST[categ_id1]',
					categ_id2='$_REQUEST[categ_id2]',
					categ_id3='$_REQUEST[categ_id3]',
					categ_id4='$_REQUEST[categ_id4]',
					unit='$_REQUEST[unit]',
					cost='$_REQUEST[cost]',
					price1='$_REQUEST[price1]',
					price2='$_REQUEST[price2]',
					price3='$_REQUEST[price3]',
					price4='$_REQUEST[price4]',
					price5='$_REQUEST[price5]',
					price6='$_REQUEST[price6]',
					price7='$_REQUEST[price7]',
					price8='$_REQUEST[price8]',
					price9='$_REQUEST[price9]',
					price10='$_REQUEST[price10]',
					reorderlevel='$_REQUEST[reorderlevel]',
					reorderqty='$_REQUEST[reorderqty]',
					supplier_id='$_REQUEST[supplier_id]',
					manysuppliers='$manysuppliers',
					picname='$picName',
					piclocate='$dir',
					status='$_REQUEST[status]',
					audit='$audit',
					description='$_REQUEST[description]',
					buffer='$buffer',
					eq_catID = '$eq_catID',
					parent_stock_id = '$parent_stock_id',
					plate_num = '$plate_num',
					eq_model = '$eq_model',
					rate_per_hour = '$rate_per_hour',
					min_time = '$min_time',
					batching_plant_categ_id = '$batching_plant_categ_id',
					kg = '$kg',
					budget_category = '$budget_category',
                    stock_length = '$_REQUEST[stock_length]',
					e_status	='$e_status',
					branding_number = '$_REQUEST[branding_num]',
					size = '$size',
					brand = '$_REQUEST[brand]',
					manufacturer = '$_REQUEST[manufacturer]'

				";

		$query = mysql_query($sql);
		$stock_id	= mysql_insert_id();

		if(!mysql_error()) {
			$msg = "Query Successful";
		}
		else{
			$msg= mysql_error();
		}
	}
	else if( empty($_REQUEST[stock]) &&
			!empty($_REQUEST[b])){
		$msg="Fill in Stock Field";
	}
}else if($b=="Update"){

	$stock	= mysql_escape_string($_REQUEST["stock"]);

	$sql = "
		update
			productmaster
		set
			stockcode='$_REQUEST[stockcode]',
			barcode='$_REQUEST[barcode]',
			stock=\"$stock\",
			type='$_REQUEST[type]',
			categ_id1='$_REQUEST[categ_id1]',
			categ_id2='$_REQUEST[categ_id2]',
			categ_id3='$_REQUEST[categ_id3]',
			categ_id4='$_REQUEST[categ_id4]',
			unit='$_REQUEST[unit]',
			cost='$_REQUEST[cost]',
			price1='$price1',
			price2='$price2',
			price3='$price3',
			price4='$price4',
			price5='$price5',
			price6='$_REQUEST[price6]',
			price7='$_REQUEST[price7]',
			price8='$_REQUEST[price8]',
			price9='$_REQUEST[price9]',
			price10='$_REQUEST[price10]',
			reorderlevel='$_REQUEST[reorderlevel]',
			reorderqty='$_REQUEST[reorderqty]',
			supplier_id='$_REQUEST[supplier_id]',
			manysuppliers='$manysuppliers',
			picname='$picName',
			piclocate='$dir',
			status='$_REQUEST[status]',
			audit='$audit',
			description='$_REQUEST[description]',
			buffer = '$buffer',
			eq_catID = '$eq_catID',
			parent_stock_id = '$parent_stock_id',
			plate_num = '$plate_num',
			eq_model = '$eq_model',
			rate_per_hour = '$rate_per_hour',
			min_time = '$min_time',
			batching_plant_categ_id = '$batching_plant_categ_id',
			kg = '$kg',
			budget_category = '$budget_category',
            stock_length = '$_REQUEST[stock_length]',
			e_status	= '$e_status',
			branding_number = '$_REQUEST[branding_num]',
			size = '$size',
			brand = '$_REQUEST[brand]',
			manufacturer = '$_REQUEST[manufacturer]'
		where
			stock_id='$stock_id'
		";

	$query = mysql_query($sql) or die(mysql_error());
	$msg = "Transaction Updated";
}
//echo "STOCK ID :$stock_id";
$result=mysql_query("
	select
		*
	from
		productmaster
	where
		stock_id='$stock_id'
") or die(mysql_error());
$r =  $aVal = mysql_fetch_assoc($result);

$stock_id		= $r[stock_id];
$stockcode		= $r[stockcode];
$barcode		= $r[barcode];
$stock			= $r[stock];
$type			= $r[type];
$categ_id1		= $r[categ_id1];
$categ_id2		= $r[categ_id2];
$categ_id3		= $r[categ_id3];
$categ_id4		= $r[categ_id4];
$unit			= $r[unit];
$cost			= $r[cost];
$price1			= $r[price1];
$price2			= $r[price2];
$price3			= $r[price3];
$price4			= $r[price4];
$price5			= $r[price5];
$price6			= $r[price6];
$price7			= $r[price7];
$price8			= $r[price8];
$price9			= $r[price9];
$price10		= $r[price10];
$reorderlevel	= $r[reorderlevel];
$reorderqty		= $r[reorderqty];
$supplier_id	= $r[supplier_id];
$description	= $r[description];
$manysuppliers	= $r[manysuppliers];
$status			= $r[status];
$buffer			= $r[buffer];
$branding_num	= $r[branding_number];
$eq_catID		= $r['eq_catID'];
$parent_stock_id = $r['parent_stock_id'];
$plate_num		= $r['plate_num'];
$eq_model		= $r['eq_model'];
$rate_per_hour	= $r['rate_per_hour'];
$min_time		= $r['min_time'];
$kg				= $r['kg'];
$batching_plant_categ_id	= $r['batching_plant_categ_id'];
$budget_category = $r['budget_category'];
$e_status		= $r['e_status'];
$size = $r['size'];
$brand = $r['brand'];
$manufacturer = $r['manufacturer'];
?>

<form enctype='multipart/form-data' method="post" action="" name="newareaform" id="newareaform" >
<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'>Product Master</div>
    <?php echo $success;?>
    <div>
            <fieldset style="border:none; border-top:1px solid #CCC;">
            <legend>ITEM DETAILS</legend>
            <input type="hidden" name="stock_id" value="<?=$stock_id?>" />
            <table style="text-align:left;padding:5px;" >
              <tr>
                <td>Stock Code: </td>
                <td>
                    <input type=text name=stockcode class='textbox'  value="<?=$stockcode;?>" placeholder="PLEASE PROVIDE CODE" >
                </td>
                <td>Reorder Level: </td>
                <td><input type=text name=reorderlevel class=textbox value="<?=$reorderlevel?>"></td>
              </tr>
              <tr>
                <td>O.E.M: </td>
                <td><input type=text name=barcode class=textbox value="<?=$barcode?>"></td>
                <td>Reorder Quantity: </td>
                <td><input type=text name=reorderqty class=textbox value="<?=$reorderqty?>" ></td>
              </tr>
              <tr>
              	 <td>Branding #: </td>
              	 <td><input type=text name=branding_num class=textbox value="<?=$branding_num?>"></td>
									<td>Size: </td>
	                <td><input type=text name=size class=textbox3 value="<?=$size?>" ></td>
              </tr>
              <tr>
                <td>Stock: </td>
                <td><input type=text name=stock class=textbox value="<?php echo htmlentities($stock,ENT_QUOTES); ?>" ></td>
                <td>Supplier ID: </td>
                <td><?php echo $options->getSupplierOptions($supplier_id); ?></td>

              </tr>
			  <tr>
				<td>Brand:</td>
				<td><input type=text name=brand class=textbox value="<?php echo htmlentities($brand,ENT_QUOTES); ?>" ></td>
				<td>Manufacturer:</td>
				<td><input type=text name=manufacturer class=textbox value="<?php echo htmlentities($manufacturer,ENT_QUOTES); ?>" ></td>
			  </tr>
              <tr>
                <td>Type: </td>
                <td><?php echo $options->getTypeOptions($type); ?></td>
                <td>Many Suppliers: </td>
                <td><input type=checkbox name=manysuppliers <?=($manysuppliers)?"checked='checked'":""?>></td>

              </tr>
              <tr>
                <td>Category: </td>
                <td>
                <?php
                    if(empty($status)){
                        echo $options->getCategoryOptions();
                    }else{
                        echo $options->getCategoryOptionsEdit($categ_id1,$categ_id2,$categ_id3,$categ_id4);
                    }
                ?>
                </td>
                <td>Upload Picture: </td>
                <td><input name="uploadedpicture" type="file" /></td>
              </tr>
              <tr>
                <td>Unit: </td>
                <td><input type=text name=unit class=textbox value="<?=$unit;?>"></td>
                <td>Status : </td>
                <td><?php echo $options->getStatusOptions($status); ?></td>
				<?php
					if($categ_id1==25){
						?>

						<td>Equipment Status: </td>
						<td>
						<?php
							if($e_status==1){
									?>
										<select name="e_status">
											<option value="1" selected=selected>Active</option>
											<option value="2">Non-Active</option>
										</select>
									<?php
							}else{
									?>
										<select name="e_status">
											<option value="1">Active</option>
											<option value="2" selected=selected>Non-Active</option>
										</select>
									<?php
							}
						?>
						</td>

						<?php
					}
				?>
              </tr>
               <tr>
                <td>Price: </td>
                <td>
                    <img src='images/edit.gif'  class='pointer' onClick="toggleBox('myDiv',1)" alt='Price' title="Show Prices" />
                    <div style="position:relative;">
                        <div id='myDiv' style='bottom:3px; left:3px;'>
                            <img id='close' src='images/close.gif' onClick="toggleBox('myDiv',0)" alt='Close' title="Close this window" />
                            <div id=\"myDivContent\">
                                <table>
                                    <tr>
                                        <td>Price 1 : </td>
                                        <td><input type=textbox name='price1' class='textbox' value="<?=$price1?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Price 2 : </td>
                                        <td><input type=textbox name='price2' class='textbox' value="<?=$price2?>" /></td>
                                    </tr>
                                    <tr>
                                        <td>Price 3: </td>
                                        <td><input type=textbox name='price3' class='textbox' value="<?=$price3?>" ></td>
                                    </tr>
                                    <tr>
                                        <td>Price 4 : </td>
                                        <td><input type=textbox name='price4' class='textbox' value="<?=$price4?>" ></td>
                                    </tr>
                                    <tr>
                                        <td>Price 5 : </td>
                                        <td><input type=textbox name='price5' class='textbox' value="<?=$price5?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Price 6 : </td>
                                        <td><input type=textbox name='price6' class='textbox' value="<?=$price6?>" ></td>
                                    </tr>
                                    <tr>
                                        <td>Price 7 : </td>
                                        <td><input type=textbox name='price7' class='textbox' value="<?=$price7?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Price 8 : </td>
                                        <td><input type=textbox name='price8' class='textbox' value="<?=$price8?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Price 9 : </td>
                                        <td><input type=textbox name='price9' class='textbox' value="<?=$price9?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Price 10 : </td>
                                        <td><input type=textbox name='price10' class='textbox' value="<?=$price10?>"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </td>
                <?php
                if($stock_id){
                ?>
                <td>Formulation : <img src="images/add.png" style="cursor:pointer;" onclick="xajax_addFormulationForm(xajax.getFormValues('header_form'))" /></td>
                <td rowspan="3" style="vertical-align:top;">
                    <div id="formulations">
                    <?php
                    $result=mysql_query("
                        select
                            *
                        from
                            formulation_header
                        where
                            status!='C'
                        and
                            product_id='$stock_id'
                    ") or die(mysql_error());
                    while($r=mysql_fetch_assoc($result)){
                        $formulation_header_id	= $r[formulation_header_id];
                        $formulation_code 		= $r[formulation_code];
                    ?>
                    <input type="text" class="textbox" value="<?=$formulation_code?>"  readonly="readonly" /> <img src="images/note.png" style="cursor:pointer;" onclick="xajax_editFormulationForm('<?=$formulation_header_id?>')"  /><br />
                    <?php
                    }
                    ?>
                    </div>
                </td>
                <?php
                }
                ?>
              </tr>
              <tr>
                <td>Cost: </td>
                <td><input type=textbox class=textbox name=cost value="<?=$cost?>"></td>

              </tr>
              <tr>
                <td>Kilogram <em>(weight)</em>: </td>
                <td><input type='textbox' class='textbox' name='kg' value="<?=$kg?>"></td>
              </tr>
               <tr>
                <td>Describe: </td>
                <td><textarea class="textarea_small" name='description' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'><?=$description?></textarea></td>
              </tr>
			  <!--
              <tr>
                <td>Buffer: </td>
                <td><input type="text" class="textbox" name="buffer" value="<?=$buffer?>"  /></td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>MOTHER ITEM:</td>
                <td>
                    <input type='text' class="stock_name textbox" value="<?=$options->getAttribute('productmaster','stock_id',$parent_stock_id,'stock')?>" />
                    <input type="hidden" name="parent_stock_id" value="<?=$parent_stock_id?>" />
                </td>
              </tr>
              <tr>
              	<td>Batching Plant Item Category:</td>
                <td>
                	<?=$options->getTableAssoc($batching_plant_categ_id,'batching_plant_categ_id','Select Batching Plant Category',"select * from batching_plant_categ order by batching_plant_categ asc",'batching_plant_categ_id','batching_plant_categ' )?>
                </td>
              </tr>
              <tr>
              	<td>Budget Category</td>
                <td>
                <?php

				echo lib::getArraySelect($budget_category,'budget_category',"Select Budget Category",
					array(
						'M' => 'Materials',
						'L' => 'Labor',
						'E' => 'Equipment',
						'F' => 'Fuel'
					)
				)
                ?>
                </td>
              </tr>
			  -->
        </table>
        </fieldset>
		<!--
        <fieldset style="border:none; border-top:1px solid #CCC;">
            <legend>EQUIPMENT DETAILS</legend>
            <table>
                  <tr>
                    <td>EQPMT CATEGORY</td>
                    <td><?=$options->getTableAssoc($eq_catID,'eq_catID','Select Equipment Category','select * from equipment_categories order by eq_cat_name asc','eq_catID','eq_cat_name')?></td>
                  </tr>
                  <tr>
                    <td>EQPMT PLATE #</td>
                    <td><input type="text" class="textbox" name="plate_num" value="<?=$plate_num?>" /></td>
                  </tr>
                  <tr>
                    <td>EQPMT MODEL</td>
                    <td><input type="text" class="textbox" name="eq_model" value="<?=$eq_model?>" /></td>
                  </tr>
                  <tr>
                    <td>RATE PER HOUR</td>
                    <td>
                        <input type="text" class="textbox" name="rate_per_hour" value="<?=$rate_per_hour?>" />
                    </td>
                  </tr>
                  <tr>
                    <td>MINIMUM TIME (HRS)</td>
                    <td>
                        <input type="text" class="textbox" name="min_time" value="<?=$min_time?>" />
                    </td>
                  </tr>
            </table>
         </fieldset>

         <fieldset style="border:none; border-top:1px solid #CCC;">
            <legend>FABRICATION LENGTH</legend>
            <table>
                  <tr>
                    <td>LENGTH</td>
                    <td><input type="text" class="textbox" name="stock_length" value="<?=$aVal['stock_length']?>" /></td>
                  </tr>
            </table>
         </fieldset>
		-->
    </div>
    <div class="module_actions">
    	<?php
		if(empty($status)){
		?>
    	<input type='submit' name='b' value='Submit' >
       	<?php
		}else{
        ?>
       	<input type='submit' name='b' value='Update' >
        <?php
		}
        ?>
        <input type='reset' value='Clear Form' >
    </div>
</div>
</form>
