<?php
function getTableAssoc($selectedid=NULL,$name='name',$label = NULL,$query,$id_column,$display_column,$js){
	$content="
		<select name='$name' id='$name' $js>
			<option value=''>$label:</option>
	";	
	
	$result=mysql_query($query);
	while($r=mysql_fetch_assoc($result)):
		$selected=($selectedid==$r[$id_column])?"selected='selected'":"";
		
		$display_content = $r[$display_column];
		if(!empty($aDisplay)){
			$display_content = "";
			foreach($aDisplay as $a){
				$display_content .="$r[$a] ";
			}
		}
		
		$content.="
			<option value='$r[$id_column]' $selected >".addslashes(htmlentities($display_content))."</option>
		";
	endwhile;
	
	$content.="
		</select>
	";
	
	return $content;
}

function getUnitRate($eur_unit_id){
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$unit_rate = $options->getAttribute('eur_unit','eur_unit_id',$eur_unit_id,'eur_unit_rate');
	$objResponse->assign('unit_rate','value',$unit_rate);
	
	return $objResponse;
}

function getBranding($eqID,$date){
	$objResponse = new xajaxResponse();
	$options = new options();
	
									$query="SELECT * 
											FROM tiretransfer
											WHERE DATE <=  '$date'
											AND STATUS !=  'C'
											AND branding_num !=  ''
											AND to_eqID = '$eqID'
											ORDER BY branding_num, tiretransfer_header_id DESC";
	//$objResponse->alert($query);
	$result=mysql_query($query) or die(mysql_error());

	$content="<table border=0 style='border-collapse:collapse;font-size:15px;' cellspacing='5' cellpadding='5'>";
	$c=1;
	$branding_num = "";
	while($r=mysql_fetch_assoc($result)){
		if($branding_num != $r['branding_num']){
				/* CHECK LATEST EQUIPMENT WITH THE BRANDING NUMBER INSTALLED */													//echo explode("-",checkLatest($r['branding_num'],$from_date))[1];
				$query2 = "SELECT * 
							FROM  `tiretransfer` 
							WHERE  `branding_num` =  '".$r['branding_num']."'
							AND date <= '$date'
							ORDER BY tiretransfer_header_id DESC ";
				$result2 = mysql_query($query2);
				$rr=mysql_fetch_assoc($result2);
				/* END CHECKING HERE */

			if( $rr['to_eqID'] != $eqID){
				continue;
			}
			$branding_num = $r['branding_num'];
		}else{
																	//$branding_num = $r['branding_num'];
			continue;
		}
		$content.="<tr>
						<td>".$c++.".</td>
						<td style='border-bottom:1px dashed #000;'><b>".$branding_num."</b></td>
						<input type='hidden' name='branding_num[]' value='".$branding_num."'>
					</tr>";
	}

	$content.="</table>";

	$objResponse->assign('b_list','innerHTML',$content);
	
	return $objResponse;
}

function displayPOItem($po_header_id){
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$query = "
		select stock, po_detail_id from po_detail as d, productmaster as p where d.stock_id = p.stock_id and po_header_id = '$po_header_id'
	";

	$js = "onchange='computeRemainingBalanceOfPO(this)'";
	
	$content = getTableAssoc('','po_detail_id','Select Item',$query,'po_detail_id','stock',$js);
	
	$objResponse->assign('po_div','innerHTML',$content);
	
	return $objResponse;
}

?>