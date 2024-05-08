<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
 <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>


<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$tire_keyword = $_REQUEST['tire_keyword'];
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from clients where eqID='$ch'");
		}
	  }
	}
?>

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/table.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<input type="text" name="tire_keyword" class="textbox" value="<?=$tire_keyword;?>" />
        <input type="submit" name="b" value="Search Tires" class="buttons" />
      <!--  <input type="submit" name="b" value="Display All" class="buttons" />-->
        <input type="button" name="b" value="Add Tire" onclick="xajax_new_tire_form();toggleBox('demodiv',1);" class="buttons" />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
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
					 	  tires as t,
						  tire_type as tt,
						  equipment as e,
						  tire_size as ts
					
					 where
					 	  (t.branding_no like '%$tire_keyword%' or
						  t.brand_name like '%$tire_keyword%' or
						  ts.sizes like '%$tire_keyword%' or
						  tt.type_name like '$tire_keyword%'or
						  e.eq_name like '$tire_keyword%') and
						  t.eqID=e.eqID and
						  t.size_id=ts.size_id and
						  t.type_id=tt.type_id
						  
					   order by eq_name asc	  
						";  
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>
            </td>
        </tr>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td><b>Branding No.</b></td>
          <td><b>Tire Type</b></td>
		  <td><b>Tire Size</b></td>
          <td><b>Brand Name</b></td>
          <td><b>Date of Purchased</b></td>
		  <td><b>Date Installed</b></td>
		  <td><b>Equipment</b></td>
          <td><b>Remarks</b></td>
        </tr>        
		
		
		<?php			
				
			while($r=mysql_fetch_assoc($rs)) {
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[tire_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="#" onclick="xajax_edit_tire_form(\''.$r[tire_id].'\');" title="Edit Entry"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.$r[branding_no].'</td>';	
				echo '<td>'.$r[type_name].'</td>';	
				echo '<td>'.$r[sizes].'</td>';	
				echo '<td>'.$r[brand_name].'</td>';	
				echo '<td>'.$r[purchased_date].'</td>';	
				echo '<td>'.$r[installed_date].'</td>';	
				echo '<td>'.$r[eq_name].'</td>';	
				echo '<td>'.$r[remarks].'</td>';	
				echo '</tr>';
			}
        ?>
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view");
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>