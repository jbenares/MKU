<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
	.alert{background-color:#FFA893;}
</style>


<?php

	$b	 			= $_REQUEST['b'];
	$checkList 		= $_REQUEST['checkList'];
	$keyword		= $_REQUEST['keyword'];
	$barcode		= $_REQUEST['barcode'];
	$categ_id1 		= $_REQUEST['categ_id1'];
	$categ_id2 		= $_REQUEST['categ_id2'];
	$categ_id3 		= $_REQUEST['categ_id3'];
	$categ_id4 		= $_REQUEST['categ_id4'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {
			
			
			if(	$options->stockIsPresent('budget_detail',$ch) || 
				$options->stockIsPresent('budget_equipment_detail',$ch) || 
				$options->stockIsPresent('budget_service_detail',$ch) || 
				$options->stockIsPresent('invadjust_detail',$ch) || 
				$options->stockIsPresent('issuance_detail',$ch) || 
				$options->stockIsPresent('po_detail',$ch) || 
				$options->stockIsPresent('po_equipment_detail',$ch) || 
				$options->stockIsPresent('po_service_detail',$ch) || 
				$options->stockIsPresent('pr_detail',$ch) || 
				$options->stockIsPresent('pr_equipment_detail',$ch) ||
				$options->stockIsPresent('pr_service_detail',$ch) ||
				$options->stockIsPresent('rr_detail',$ch)
			){
				$msg = "Unable to delete Item. Item is found in another transaction.";	
			}else{
				$msg ="Deleted";
				mysql_query("Update productmaster set status = 'C' where stock_id='$ch'");
			}
		}
	  }
	}
?>

<form name="newareaform" id="newareaform" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" placeholder ="Search Stock"/>
		<input type="text" name="barcode" class="textbox" value="<?=$barcode;?>" placeholder ="Search OEM #" />
        <!--<?=$options->option_category1($categ_id1)?>-->
        <?=$options->getCategoryOptionsEdit($categ_id1,$categ_id2,$categ_id3,$categ_id4);?>
        <input type="submit" name="b" value="Search Product Master"  />
        <input type="button" name="b" value="Add Product Master" onclick="xajax_new_productmasterform();toggleBox('demodiv',1);"  />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();"  />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  productmaster
					 where
					 	  stock like '%$keyword%'";
			if(!empty($barcode)){
			$sql.="	
				and
					barcode like '%$barcode%'
			";
			}			  
			if(!empty($categ_id1)){
			$sql.="	
				and
					categ_id1 = '$categ_id1'
			";
			}
			if(!empty($categ_id2)){
			$sql.="	
				and
					categ_id2 = '$categ_id2'
			";
			}
			if(!empty($categ_id3)){
			$sql.="	
				and
					categ_id3 = '$categ_id3'
			";
			}
			if(!empty($categ_id4)){
			$sql.="	
				and
					categ_id4 = '$categ_id4'
			";
			}
			
			$sql.="
				and status != 'C'
				order by stock asc
			";
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
            	<?php
					$pagination = $view;
					$pagination .= ( $keyword ) ? "&keyword=".$keyword : "";
					$pagination .= ( $categ_id1 ) ? "&categ_id1=".$categ_id1: "";
					$pagination .= ( $categ_id2 ) ? "&categ_id2=".$categ_id2: "";
					$pagination .= ( $categ_id3 ) ? "&categ_id3=".$categ_id3: "";
					$pagination .= ( $categ_id4 ) ? "&categ_id4=".$categ_id4: "";
                ?>
                <?php
                    $page = $pager->renderFullNav($pagination);
					echo $page;
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td><b>Stock</b></td>
          <td><b>Stock Code</b></td>
          <td><b>OEM No.</b></td>
          <td><b>Description</b></td>
          <td><b>Category</b></td>
          <td><b>Type</b></td>
          <td><b>Unit</b></td>
          <td><b>Cost</b></td>
          <td><b>Status</b></td>
          <td><b>Inventory Balance</b></td>
          <td><b>Reorder Level</b></td>
         
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				$category = $options->attr_Category($r[categ_id1],'category');
				
				if($r['reorderlevel'] >= $options->inventory_warehouse(date("Y-m-d"),$r['stock_id'], NULL, NULL)){
					$alert = "class = 'alert'";
				}
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r['stock_id'].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=a09dc2e7caa66cf3d3ec&stock_id='.$r['stock_id'].'" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td align="left">'.$r['stock'].'</td>';	
				echo '<td align="left">'.$r['stockcode'].'</td>';	
				echo '<td>'.$r['barcode'].'</td>';	
				echo '<td>'.$r['description'].'</td>';	
				echo '<td align="left">'.$category.'</td>';	
				echo '<td>'.$options->getTypeName($r['type']).'</td>';	
				echo '<td align="left">'.$r['unit'].'</td>';	
				echo '<td><div align="right">P '.number_format($r['cost'],2,'.','').'</div></td>';	
				echo '<td>'.$options->getStatusName($r['status']).'</td>';	
				echo '<td>'.$options->inventory_warehouse(date("Y-m-d"),$r['stock_id'], NULL, NULL).' '.$r['unit'].'</td>';	
				echo '<td '.$alert.'>'.$r['reorderlevel'].'</td>';	
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $page;
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>