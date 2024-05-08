<?php
$user_id	= $_SESSION['userID'];

$b = $_REQUEST['b'];
$separation = $_REQUEST['separation'];
$_REQUEST['search_employeeID']  = ($_REQUEST['search_employee']) ? $_REQUEST['search_employeeID'] : "";

$aRequest	= array('b','raf_id','keyword');
$aHeader 	= 	array(
					'employeeID','con_date','effectivity_date','project_id','base_rate','allowance','others','position','separation'
				);

$aVal = array();
if(count($aRequest) > 0){
	foreach($aRequest as $request){
		$aVal[$request] = $_REQUEST[$request];
	}
}
if(count($aHeader) > 0){
	foreach($aHeader as $header){
		$aVal[$header] = $_REQUEST[$header];
	}
}

if($aVal['b'] == "Submit"){
	
	if(empty($aVal['raf_id'])){
		if(count($aHeader) > 0){
			$sql = 
				"insert into 
				    contracts_raf 
				set
			";
			foreach($aHeader as $header){
			$sql.="$header = '$aVal[$header]',";	
			}
			$sql = rtrim($sql,",");
		}
		$query = mysql_query($sql) or die(mysql_error());	
		$aVal['raf_id'] = mysql_insert_id();
		mysql_query("
			update contracts_raf set user_id = '$user_id' where raf_id = '$aVal[raf_id]'
		") or die(mysql_error());
		$msg = "Transaction Saved";
	}else{
		if(count($aHeader) > 0){
			$sql = 
				"update
					contracts_raf
				set
			";
			foreach($aHeader as $header){
			$sql.="$header = '$aVal[$header]',";	
			}
			$sql = rtrim($sql,",");
			$sql.= "where raf_id = '$aVal[raf_id]'";
			$query = mysql_query($sql) or die(mysql_error());	
			$msg = "Transaction Updated";
			mysql_query("
				update contracts_raf set user_id = '$user_id' where raf_id = '$aVal[raf_id]'
			") or die(mysql_error());
		}	
	}
	
}


if($aVal['raf_id'] && $aVal['b'] != "Search"){
	$result = mysql_query("
		select * from contracts_raf where raf_id = '$aVal[raf_id]'
	") or die(mysql_error());
	$aVal = mysql_fetch_assoc($result);
}
?>

<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.print();
    return false;
}
</script>


<style type="text/css">
.table-form{
	display:inline-table;	
}
.table-form tr td:nth-child(1){
	font-weight:bold;
	text-align:left;
}
</style>
<form enctype='multipart/form-data' method="post" action="" id="newareaform" >
	<div class="module_actions">
    	<div style="display:inline-block;">
            Search Employee <br />    	
            <input type="text" class="textbox ac-employee" name="search_employee" value="<?=$_REQUEST['search_employee']?>"/>
            <input type="hidden" name="search_employeeID" value="<?=$_REQUEST['search_employeeID']?>" />
       	</div>
        
       	<div style="display:inline-block;">
        	Search Contract Date: <br />
        	<input type="text" class="textbox datepicker" name="search_date" value="<?=$_REQUEST['search_con_date']?>" />
        </div>
                
        <input type="submit" name="b" value="Search" />
		
		<a href="?view=<?=$view?>"><input type="button" name="b" value="New" /></a>
</div>
		
    
      
	<?php if($aVal['b'] == "Search"): ?>
		<?php
        $page = $_REQUEST['page'];
        if(empty($page)) $page = 1;
         
        $limitvalue = $page * $limit - ($limit);
        
        $sql = "
            select
				concat(e.employee_lname,', ',e.employee_fname) as employee, raf_id, effectivity_date
			from
				contracts_raf as cr left join employee as e on cr.employeeID = e.employeeID 
			where
				1=1
		";
		if( $_REQUEST['search_con_date'] ) 	$sql .= " and date = '$_REQUEST[search_con_date]'";
		if( $_REQUEST['search_employeeID'] ) 	$sql .= " and cr.employeeID = '$_REQUEST[search_employeeID]'";
		
		//echo $sql;
		
		
		$sql .= "
			order by raf_id asc
        ";
              
        $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                
        $i=$limitvalue;
        $rs = $pager->paginate();
        
        $pagination	= $pager->renderFullNav("$view&b=Search&keyword=$aVal[keyword]&search_employeeID=$_REQUEST[search_employeeID]&search_employee=$_REQUEST[search_employee]&search_date=$_REQUEST[search_con_date]");
        ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
       <table width="100%" align="left" style="text-align:left;" class="display_table">
        	<!--<thead> -->
                <tr>				
                    <td width="20">#</td>
                    <td width="20"></td>
                    <td style="text-align:left;">RAF-COE #</td>
                    <td style="text-align:left;">EFFECTIVITY DATE</td>
                    <td style="text-align:left;">EMPLOYEE NAME</td>
                    <!--<td style="text-align:left;">FROM PROJECT</td>
                    <td style="text-align:left;">TO PROJECT</td>
                    <td style="text-align:left;">NOTES</td>-->
                </tr>  
            <!--</thead>
            <tbody> -->
			<?php								
            while($r=mysql_fetch_assoc($rs)) {
                
                echo '<tr>';
                echo '<td width="20">'.++$i.'</td>';
                echo '<td width="15"><a href="admin.php?view='.$view.'&raf_id='.$r['raf_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
				echo '<td>'.str_pad($r['raf_id'],5,0,STR_PAD_LEFT).'</td>';	
				echo "<td>".date("m/d/Y",strtotime($r['effectivity_date']))."</td>";
                echo '<td>'."$r[employee]".'</td>';	
				/*echo '<td>'."$r[from_project]".'</td>';	
				echo '<td>'."$r[to_project]".'</td>';	
				echo '<td>'."$r[notes]".'</td>';*/	
                echo '</tr>';
            }
            ?>
            <!--</tbody> -->
        </table>
        <div class="pagination">
             <?=$pagination?>
        </div>
    <?php else: #end else?>
    <div class=form_layout_productmaster>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'><?=strtoupper($transac->getMname($view))?></div>
        <div class="module_actions">
            <table class="table-form">
                <input type="hidden" name="raf_id" value="<?=$aVal['raf_id']?>" />
                
                <tr>
                    <td>Effectivity Date:</td>
                    <td><input type="text" class="textbox datepicker"  name="effectivity_date" value="<?=$aVal['effectivity_date']?>" onkeypress="if(event.keyCode==13){ return false; }"/></td>
                    <td>Base Rate:</td>
                    <td><input type="text" class="textbox" name="base_rate" value="<?=$aVal['base_rate']?>" /></td>
                </tr>
                <tr>
                    <td>Employee:</td>
                    <td>
                        <input type="text" class="textbox ac-employee" value="<?php if(!empty($aVal['employeeID'])) echo $options->getAttribute('employee','employeeID',$aVal['employeeID'],'employee_lname').', '.$options->getAttribute('employee','employeeID',$aVal['employeeID'],'employee_fname')?>" onkeypress="if(event.keyCode==13){ jQuery('#submit_button').focus(); return false; }"/>
                        <input type="hidden" name="employeeID" value="<?=$aVal['employeeID']?>" />
                    </td>
                    <td>Allowance:</td>
                    <td><input type="text" class="textbox" name="allowance" value="<?=$aVal['allowance']?>" /></td>
                </tr>
                
                <tr>
                    <td>Project:</td>
                    <td>
                        <input type="text" class="textbox project" value="<?=$options->getAttribute('projects','project_id',$aVal['project_id'],'project_name')?>" onkeypress="if(event.keyCode==13){ return false; }"/>
                        <input type="hidden" name="project_id" value="<?=$aVal['project_id']?>" />
                    </td>
                    <td>Others:</td>
                    <td> <input type="text" class="textbox" name="others" value="<?=$aVal['others']?>" /></td>
                </tr>
                <tr>
                    <!--<td>Contract Date:</td>
                    <td><input type="text" class="textbox datepicker"  name="con_date" value="<?=$aVal['con_date']?>" onkeypress="if(event.keyCode==13){ return false; }"/></td>-->
                    <td>Position:</td>
                    <td> <input type="text" class="textbox" name="position" value="<?=$aVal['position']?>" /></td>				
                    <td colspan="3"> <input type="checkbox" name="separation" value="1" <?phpif (isset($separation)){?>checked="checked" <?php}?> />Please Check if Separation Pay is Applicable</td>
					
                </tr>
                <!--<tr>
                    <td>Position:</td>
                    <td> <input type="text" class="textbox" name="position" value="<?=$aVal['position']?>" /></td>
                </tr>-->
                <!-- <tr>
                    <td>Salary:</td>
                    <td>
                        <input type="text" class="textbox project" value="<?=$options->getAttribute('projects','project_id',$aVal['to_project_id'],'project_name')?>" onkeypress="if(event.keyCode==13){ return false; }"/>
                        <input type="text" class="textbox" name="salary" value="<?=$aVal['salary']?>" />
                    </td>
                </tr>-->
            </table>
       	</div>
        <?php if($aVal['status']): ?>
        <div class="module_actions">
        	<div style="display:inline-block;">
            	<em>Status:</em> <br />
            	<span style="font-weight:bold;"><?=$options->getTransactionStatusName($aVal['status'])?></span>
            </div>
            
            <div style="display:inline-block; margin-left:10px;">
            	<em>Encoded by:</em> <br />
            	<span style="font-weight:bold;"><?=$options->getUserName($aVal['user_id'])?></span>
            </div>
        </div>
        <?php endif; ?>
       	<div class="module_actions">
		    
            <input type='submit' name='b' value='Submit' class='buttons' id="submit_button">
            <input type='reset' value='Clear Form' class='buttons'>
            <?php if($b!="Print Preview" && !empty($aVal['raf_id'])){ ?>
			<input type="submit" name="b" id="b" value="Print Preview" />
			<?php } else if($b=="Print Preview"){ ?>	
			<input type="button" value="Print" onclick="printIframe('JOframe');" />	
			<?php } ?>
        </div>
        <?php 
		if($b == "Print Preview" && $aVal['raf_id']){ 
			echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='contracts/print_contract_raf.php?id=$aVal[raf_id]&separation=$separation' width='100%' height='500'>
			</iframe>";    
		} 
		?>
    </div>
    
    <?php endif; #end if ?>
</form>
<script type="text/javascript">
jQuery(function(){
	jQuery(".ac-employee").autocomplete({
		source: "autocomplete/employees.php",
		minLength: 2,
		select: function(event, ui) {
			jQuery(this).val(ui.item.value);
			jQuery(this).next().val(ui.item.id);
		}
	});
});
</script>