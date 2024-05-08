<script type="text/javascript">
	<!--
	var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	tsRegister();
	// -->
</script>
<?php

	$b = $_REQUEST['b'];
	$checkList = $_REQUEST['checkList'];
	$keyword = $_REQUEST['keyword'];
	
	
	if($b=='Delete Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	
			mysql_query("delete from formulation_header where formulation_id='$ch'");
		}
	  }
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

<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div>
        	Search in
            <div style="display:inline-block;">
				<?php
				 	$formulationfilter='formulationcode';
				
                    //echo $options->getFormulationFilterOptions($formulationfilter);
					echo "<b>&nbsp;Formulation Code</b>";
                ?>
                 &nbsp; having &nbsp;
            	<div id="keywordfield" style="display:inline-block;">
	                 <?php

					 	
					 	if($formulationfilter=='customername'){
							echo $options->getAccountFormulationOptions($keyword); 
						}elseif($formulationfilter=='formulationcode'){
							echo "<input type='text' name='keyword' class='textbox3' value='$keyword'>";
						}else{
							echo $options->getAllCategoryFilterOptions($keyword);	
						}
					 ?>
                     
                     &nbsp;in&nbsp;
                     <?php echo $options->getAllCategoryOptions('','categorykeyword');?>
                     
           		</div>
                 <input type="submit" name="b" value="Search Formulation" class="buttons" />
           	</div>
      	</div>
        
        <br />
        <input type="submit" name="b" value="Delete Selected" onclick="return approve_confirm();" class="buttons" />
        <input type="button" value="Print Formulation" onclick="printIframe('formulationframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "select
						  *
					 from
					 	  formulation_header
					 where
					 	  $formulationfilter like '%$keyword%'";
						  
			if(!empty($categorykeyword)){
				
				$sql.="	 and
							  category = '$categorykeyword' 
							";
			}
			
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
          <td width="20"></td>
          <td width="20"></td>
          <td width="20"></td>         
          <td><b>Formulation Code</b></td>
          <td><b>Formulation Date</b></td>
          <td><b>Category</b></td>
          <td><b>Description</b></td>
          <td><b>Customer Name</b></td>
          <td><b>Price per Kilo</b></td>
        </tr>  
        </thead>      
		<?php								
			while($r=mysql_fetch_assoc($rs)) {
				
				echo '<tr bgcolor="'.$transac->row_color($i++).'">';
				echo '<td width="20">'.$i.'</td>';
				echo '<td><input type="checkbox" name="checkList[]" value="'.$r[formulation_id].'" onclick="document._form.checkAll.checked=false"></td>';
				echo '<td width="15"><a href="admin.php?view=a2bcefb19927cacb4c3a&formulation_id='.$r[formulation_id].'" onclick="xajax_show_formulation(\''.$r[formulation_id].'\');toggleBox(\'demodiv\',1);" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td width="15"><a href="#" onclick="xajax_duplicateformulationform(\''.$r[formulation_id].'\');toggleBox(\'demodiv\',1);" title="Duplicate"><img src="images/duplicate.png" border="0"></a></td>';
				echo '<td width="15"><a href="#" onclick="xajax_print_formulation(\''.$r[formulation_id].'\'); toggleBox(\'demodiv\',1);" title="Print Formulation"><img src="images/action_print.gif" border="0"></a></td>';
				echo '<td>'.$r[formulationcode].'</td>';	
				echo '<td>'.$r[formulationdate].'</td>';	
				echo '<td>'.$options->getCategoryNameWithLevel($r[category]).'</td>';	
				echo '<td>'.$r[description].'</td>';	
				echo '<td>'.$options->getAccountName($r[customername]).'</td>';	
				echo '<td><div align="right">'.$options->getPricePerKiloFromFormulationId($r[formulation_id]).'</div></td>';	
				echo '</tr>';
			}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav($view);
                ?>                
            </td>
      	</tr>
    </table>
    </div>
</div>
</form>