<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("update vehicle_pass set vh_void='1' where vh_number='$ch'");
		}
	  }
	}

?>
<script>
xajax.callback.global.onRequest = function(){toggleBox('demodiv',1);}
xajax.callback.global.beforeResponseProcessing = function(){toggleBox('demodiv',0);}
</script>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
        <input type="button" name="b" value="Encode Vehicle Pass" onclick="xajax_new_vehiclepassform();" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select * from vehicle_pass where vh_void='0' order by vh_number desc";
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="15"></td>
	  <td width="15"></td>
	  <td><b>Date</b></td>
          <td><b>V.P. #</b></td>
          <td><b>P.O. #</b></td>
          <td><b>Driver</b></td>
          <td><b>Vehicle</b></td>     
          <td><b>Purpose</b></td> 
        </tr>        
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				
				echo '<td width="20">'.$i.'</td>';
				
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[vh_number].'" onclick="document._form.checkAll.checked=false"></td>';
				
				echo '<td><a href="javascript:void(0);" style="cursor:pointer;" onclick="xajax_edit_vehiclepassform(\''.$r[vh_number].'\');"><img src="images/edit.gif" border="0"></a></td>';
			      echo '<td><a href="vehicle_pass/print_vehicle_pass.php?vh_number='.$r[vh_number].'" target=_blank><img src="images/action_print.gif" border="0"></a></td>';


			      echo '<td>'.date("F d, Y", strtotime($r[vh_date])).'</td>';	
			     echo '<td>'.str_pad($r[vh_number], 7, "0", STR_PAD_LEFT).'</td>';				
			      echo '<td>'.$r[po_header_id].'</td>';

			      $getDriver = mysql_query("select * from drivers where driverID='$r[driverID]'");
			      $rD = mysql_fetch_array($getDriver);

				echo '<td>'.$rD[driver_name].'</td>';

			      $getStock = mysql_query("select * from productmaster where stock_id='$r[stock_id]'");
			      $rS = mysql_fetch_array($getStock);

				echo '<td>'.$rS[stock].'</td>';


			      $getP = mysql_query("select * from vehicle_pass_purpose where vh_purpose_id='$r[vh_purpose_id]'");
			      $rP = mysql_fetch_array($getP);

			      echo '<td>'.$rP[vh_purpose_description].'</td>';
					
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("");
                ?>
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>