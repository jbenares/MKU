<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>

<?php

	$b = $_REQUEST['b'];
	
	$issuance_header_id		= $_REQUEST['issuance_header_id'];
	$issuance_header_id_pad	= str_pad($issuance_header_id,7,0,STR_PAD_LEFT);
	
	#$project_id			= $_REQUEST['project_id'];
	#$project_name		= $options->attr_Project($project_id,'project_name');
	#$project_code		= $options->attr_Project($project_id,'project_code');
	#$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	$field		= $_REQUEST['field'];
	
	#ISSUANCE HEADER/DETAILS
	$date					= $_REQUEST['date'];
	$project_id				= $_REQUEST['project_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	
	$reference				= $_REQUEST['reference'];
	$driverID				= $_REQUEST['driverID'];
	$equipment_id			= $_REQUEST['equipment_id'];
	$stock_id				= $_REQUEST['stock_id'];
	$quantity				= $_REQUEST['quantity'];
	$price					= $_REQUEST['price'];
	$amount					= $_REQUEST['amount'];
	$user_id				= $_SESSION['userID'];
	
	if($b == "Finish Selected"){
		$ids = $_REQUEST['ids'];	
		$status	= $_REQUEST['status'];
		
		$x = 0;
		
		#print_r($status);
		#echo "<br>";
		#print_r($ids);
		foreach($status as $s){
			if($s == "F"){
				#echo $ids[$x] . "<br>";
				
				$issuance_header_id = $ids[$x];
				
				$query="
					update
						issuance_header
					set
						status='F'
					where
						issuance_header_id = '$issuance_header_id'
				";	
				mysql_query($query) or die(mysql_error());
				
				$options->insertAudit($issuance_header_id,'issuance_header_id','F');
				
				$options->postIssuance($issuance_header_id);
			}
			$x++;		
		}
		$msg = "RISs Finished";
	}
	
	
	if($b == "ISSUE"){
		mysql_query("
			insert into
				issuance_header
			set
				date = '$date',
				project_id = '$project_id',
				status = 'S',
				user_id = '$user_id',
				work_category_id = '$work_category_id',
				sub_work_category_id = '$sub_work_category_id'
		") or die(mysql_error());
		
		$issuance_header_id = mysql_insert_id();
		
		mysql_query("
			insert into
				issuance_detail
			set
				issuance_header_id = '$issuance_header_id',
				stock_id = '$stock_id',
				quantity = '$quantity',
				price = '$price',
				amount = '$amount',
				equipment_id = '$equipment_id',
				driverID = '$driverID',
				_reference = '$reference'
		") or die(mysql_error());
		
		$msg = "ISSUED";
	}	
	
	
	if($b=='Cancel') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {	

			$query="
				update
					issuance_header
				set
					status='C'
				where
					issuance_header_id = '$ch'
			";
			mysql_query($query);
			$options->insertAudit($ch,'issuance_header_id','C');
			
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
        <div style="display:inline-block;">
        	<div class="inline">
	            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
           	</div>
            <select class="select" name="field">
                	<option value="issuance_header_id" <?php if($field=="issuance_header_id") echo "selected='selected'"; ?>>Issuance #</option>
                    <option value='h.project_id' <?php if($field=="h.project_id") echo "selected='selected'"; ?>>Project #</option>
                    <option value='project_name' <?php if($field=="project_name") echo "selected='selected'"; ?>>Project Name</option>
	        </select>
            
            <input type="submit" name="b" value="Search" />
            <input type="submit" name="b" value="Finish Selected"  />
            <!--<input type="submit" name="b" value="Cancel" onclick="return approve_confirm();" />-->
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="button"  value="QUICK ISSUE" onclick="j('#_dialog').dialog('open');" />                        
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php
	if($b!="Print"){
    ?>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<?php
			$page = $_REQUEST['page'];
			if(empty($page)) $page = 1;
			 
			$limitvalue = $page * $limit - ($limit);
		
			$sql = "
				select
					*
				from
					issuance_header as h, projects as p
				where
					h.project_id = p.project_id
			";
			
			if(!empty($field)){
			$sql.="
			and
				$field like '%$keyword%'	
				
			";	
			}
			
			$sql.="
				order by date desc
			";
						  
				
			$pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                    
			$i=$limitvalue;
			$rs = $pager->paginate();
		?>
        <thead>
    	<tr bgcolor="#C0C0C0">				
            <th width="20">#</th>
            <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
            <th width="20"></th>
            <th width="20"></th>    
            <th>Issuance #</th>
            <th>Date</th>
            <th>Project</th>
            <th>Work Category</th>
            <th>Sub Work Category</th>
            <th width="20">Status</th>
             <th width="20">Select Status</th>
        </tr>  
        </thead>      
		<?php		
		$i=1;						
		while($r=mysql_fetch_assoc($rs)) {
			$issuance_header_id	= $r['issuance_header_id'];
			$issuance_header_id_pad	= str_pad($issuance_header_id,7,0,STR_PAD_LEFT);
			$project_id			= $r['project_id'];
			$project_name		= $options->attr_Project($project_id,'project_name');
			$project_code		= $options->attr_Project($project_id,'project_code');
			$project_name_code	= ($project_id)?"$project_name - $project_code":"";
			$date				= $r['date'];
			
			$work_category_id 	= $r['work_category_id'];
			$work_category  = $options->attr_workcategory($work_category_id,'work');	
			$sub_work_category_id = $r['sub_work_category_id'];
			$sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
			$status	= $r['status'];
		?>
            <tr bgcolor="<?=$transac->row_color($i)?>">
            <td width="20"><?=$i++?></td>
            <td><input type="checkbox" name="checkList[]" value="<?=$issuance_header_id?>" onclick="document._form.checkAll.checked=false"></td>
            <td width="15"><a href="admin.php?view=02bb738f4e1ab460dd47&issuance_header_id=<?=$issuance_header_id?>" title="Show Details"><img src="images/edit.gif" border="0"></a></td>
            <td width="15"><a href="admin.php?view=<?=$view?>&issuance_header_id=<?=$issuance_header_id?>&b=Print" title="Print Preview"><img src="images/action_print.gif" border="0"></a></td>
            <td><?=$issuance_header_id_pad?></td>
            <td><?=date("F j, Y",strtotime($date))?></td>
          	<td><?=$project_name_code?></td>	
            <td><?=$work_category?></td>	
            <td><?=$sub_work_category?></td>		
            <td><?=$options->getTransactionStatusName($status)?></td>	
            <td>
				 <?php if($status == "S"){ ?>
                <select name="status[]" >
                    <option value="">Select Status:</option>
                    <option value="F">Finish</option>
                </select>
                <input type="hidden" name="ids[]" value="<?=$issuance_header_id?>" />
                <?php } ?>
            </td>
            </tr>
       	<?php
		}
        ?>
        </table>
        <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
        <tr>
            <td colspan="5" align="left">
                <?php
                    echo $pager->renderFullNav("$view&field=$field&keyword=$keyword");
                ?>                
            </td>
      	</tr>
    	</table>
    <?php
	}else{	
    ?>
    	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_issuance.php?id=<?=$issuance_header_id?>' width='100%' height='500'>
       	</iframe>
    <?php
	}
    ?>
    </div>
</div>
</form>
<div id="_dialog">
    <div id="_dialog_content">
	   	<table style="display:inline-table;">
        	<tr>
            	<td>DATE</td>
                <td><input type="text" name="date" class="textbox datepicker hinder-submit" autocomplete="off" readonly="readonly"  /></td>
            </tr>
            <tr>
            	<td>LOCATION</td>
                <td>
                	<input type="text" class="textbox project hinder-submit"  onclick="this.select();"  />
	                <input type="hidden" name="project_id" title="Select Project" />
                </td>
            </tr>
            <tr>
            	<td>WORK CATEGORY</td>
                <td><?=$options->option_workcategory('','work_category_id','Select Work Category')?></td>
            </tr>
            <tr>
            	<td>SUB WORK CATEGORY</td>
                <td id="subworkcategory">
                	<select>
                    	<option value=''>Select Sub Work Category:</option>
                    </select>
                </td>
            </tr>
        </table>
        <table style="display:inline-table;">
        	<tr>
            	<td>REFERENCE</td>
                <td><input type="text" name="reference" class="textbox hinder-submit" /></td>
            </tr>
            <tr>
            	<td>DRIVER</td>
                <td><?=$options->getTableAssoc(NULL,'driverID',"Select Driver","select * from drivers order by driver_name asc","driverID","driver_name")?></td>
            </tr>
            <tr>
            	<td>EQUIPMENT</td>
                <td>
                	<input type='textbox' class='equipment_name textbox hinder-submit' onclick="this.select();">
					<input type='hidden' name='equipment_id'>
                </td>
            </tr>
            <tr>
            	<td>ITEM</td>
                <td>
                	<input type="text" class="textbox stock_name hinder-submit" onclick="this.select();" />
                    <input type="hidden" name="stock_id" class="textbox"  />
                </td>
            </tr>
            <tr>
            	<td>QUANTITY</td>
                <td><input type="text" name="quantity" id="quantity" class="textbox hinder-submit" autocomplete="off" /></td>
            </tr>
            <tr>
            	<td>PRICE</td>
                <td><input type="text" name="price" id="price" class="textbox hinder-submit" autocomplete="off" /></td>
            </tr>
            <tr>
            	<td>AMOUNT</td>
                <td><input type="text" name="amount" id="amount" class="textbox hinder-submit" autocomplete="off" /></td>
            </tr>
        </table>
    </div>
    <div>
    	<input type="submit" name="b" value="ISSUE" />
    </div>
</div>
<script type="text/javascript">
	j(function(){
		var dlg = j("#_dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600, title : "Check Voucher Details"});
		dlg.parent().appendTo(jQuery("form:first"));
		
		j("#work_category_id").change(function(){
			xajax_display_subworkcategory(this.value);
		});
		
		j("#quantity,#price").keyup(function(e) {
            solveIssueAmount();
        });
	});
	
	function solveIssueAmount(){
		var quantity = j('#quantity').val();
		var price = j('#price').val();
		
		var amount = quantity * price ; 
		
		j('#amount').val(amount);
	}
	
</script>