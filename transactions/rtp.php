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
.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
.table-form{
	display:inline-table;	
}

.table-form tr td:nth-child(1){
	text-align:right;
	font-weight:bold;
}
.display_table td{
	padding:3px 5px;	
}
.display_table tr:nth-child(1) td{
	font-weight:bold;	
}
</style>
<?php
	#DO NOT REMOVE
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];
	
	#SEARCH 
	$search_reference	= $_REQUEST['search_reference'];

	#HEADER
	$aList	= 	array(
					'rtp_header_id','project_id','work_category_id','sub_work_category_id','reference','date','status','user_id','remarks','date_needed',
					'rtp_detail_id','description','quantity','unit','mcd_qty','budget_qty','actual_qty','balance_qty',
					'_rtp_detail_id','_description','_quantity','_unit','_mcd_qty','_budget_qty','_actual_qty','_balance_qty', 'date_received'
				);
				
	$aTrans	= array();
	if($aList){
		foreach($aList as $key){
			$aTrans[$key] = $_REQUEST[$key];
		}
	}
	
	
	if( $b == "Add" ){
		if(!empty($aTrans['description'])){
			mysql_query("
				insert into
					rtp_detail
				set
					rtp_header_id = '$aTrans[rtp_header_id]',
					description   = '$aTrans[description]',
					quantity      = '$aTrans[quantity]',
					unit          = '$aTrans[unit]',
					mcd_qty       = '$aTrans[mcd_qty]',
					budget_qty    = '$aTrans[budget_qty]',
					actual_qty    = '$aTrans[actual_qty]',
					balance_qty   = '$aTrans[balance_qty]'					
			") or die(mysql_error());
			$msg = "Item added";
		}else{
			$msg = "Empty item supplied.";	
		}
	} else if($b == 'd'){ 
		mysql_query("
			update
				rtp_detail
			set
				rtp_void = '1'
			where
				rtp_detail_id = '$aTrans[rtp_detail_id]'
		") or die(mysql_error());
		$msg = "Item voided.";
	
	} else if($b=="Submit"){
		$query="
			insert into 
				rtp_header
			set
				project_id           = '$aTrans[project_id]',
				work_category_id     = '$aTrans[work_category_id]',
				sub_work_category_id = '$aTrans[sub_work_category_id]',
				reference            = '$aTrans[reference]',
				date                 = '$aTrans[date]',
				status               = 'S',
				user_id              = '$user_id',
				remarks              = '$aTrans[remarks]',
				date_needed          = '$aTrans[date_needed]',
				date_received        = '$aTrans[date_received]',
				type                 = '$_REQUEST[type]',
				datetime_encoded     = '".lib::now()."'
		";	
		mysql_query($query) or die(mysql_error());
		$aTrans['rtp_header_id'] = mysql_insert_id();
		
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		
		$query="
			update
				rtp_header
			set
				project_id           = '$aTrans[project_id]',
				work_category_id     = '$aTrans[work_category_id]',
				sub_work_category_id = '$aTrans[sub_work_category_id]',
				reference            = '$aTrans[reference]',
				date                 = '$aTrans[date]',
				status               = 'S',
				user_id              = '$user_id',
				remarks              = '$aTrans[remarks]',
				date_needed          = '$aTrans[date_needed]',
				date_received        = '$aTrans[date_received]',
				type                 = '$_REQUEST[type]'				
			where
				rtp_header_id = '$aTrans[rtp_header_id]'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		if(!empty($aTrans['_rtp_detail_id'])):
			$x = 0;
			foreach($aTrans['_rtp_detail_id'] as $id):
				mysql_query("
					update 
						rtp_detail
					set
						description = '".$aTrans['_description'][$x]."',
						quantity = '".$aTrans['_quantity'][$x]."',
						unit = '".$aTrans['_unit'][$x]."',
						mcd_qty = '".$aTrans['_mcd_qty'][$x]."',
						budget_qty = '".$aTrans['_budget_qty'][$x]."',
						actual_qty = '".$aTrans['_actual_qty'][$x]."',
						balance_qty = '".$aTrans['_balance_qty'][$x]."'
					where
						rtp_detail_id = '$id'
				") or die(mysql_error());
				$x++;
			endforeach;
		endif;
		
		$msg = "Transaction Updated";
	}else if($b=="Cancel"){
		$query="
			update
				rtp_header
			set
				status='C'
			where
				rtp_header_id = '$aTrans[rtp_header_id]'
		";	
		mysql_query($query);
		$msg = "Transaction Cancelled";	
	}else if($b=="Finish"){
		$query="
			update
				rtp_header
			set
				status='F'
			where
				rtp_header_id = '$aTrans[rtp_header_id]'
		";	
		mysql_query($query);
		
		$msg = "Transaction Finished";
	}else if($b=="Unfinish"){
		
		$query="
			update
				rtp_header
			set
				status='S'
			where
				rtp_header_id = '$aTrans[rtp_header_id]'
		";	
		mysql_query($query);
		
		$msg = "Transaction Unfinished";
		
	}
	
	$query="
		select
			*
		from
			rtp_header 
		where
			rtp_header_id = '$aTrans[rtp_header_id]'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r = $aVal =  mysql_fetch_assoc($result);
	
	$aTrans	= array();
	if($aList){
		foreach($aList as $key){
			$aTrans[$key] = $r[$key];
		}
	}
?>
<?php if($aTrans['status'] != "S") { ?>
<style type="text/css">
.trash{
	display:none;	
}
</style>
<?php } ?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        REFERENCE: <br />  
        <input type="text" class="textbox"  name="search_reference" value="<?=$search_reference?>"  onclick="this.select();"  autocomplete="off" placeholder="Search" />
    </div>   
    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
if($b == "Search"){
?>
	<?php
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "
		select
        	*
        from
			rtp_header as h, projects as p
		where 
			h.project_id = p.project_id
    ";
        
    if(!empty($search_reference)){
    $sql.="
		and reference like '$search_reference%'
    ";
    }
	
	$sql.="
		order by date desc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_reference=$search_reference");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th style="text-align:left;">REFERENCE</th>
        <th style="text-align:left;">DATE RECEIVED</th>
		<th style="text-align:left;">DATE ENCODED</th>
        <th style="text-align:left;">PROJECT</th>
        <th style="text-align:left;">SCOPE OF WORK</th>
        <th style="text-align:left;">TYPE</th>
        <th style="text-align:left;">STATUS</th>
        <th style="text-align:left;">ENCODED BY</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&rtp_header_id='.$r['rtp_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.$r['reference'].'</td>';	
		echo '<td>'.date("m/d/Y",strtotime($r['date'])).'</td>';
		echo '<td>'.date("m/d/Y h:i:s",strtotime($r['datetime_encoded'])).'</td>';		
		echo '<td>'.$r['project_name'].'</td>';	
		echo '<td>'.$options->getAttribute('work_category','work_category_id',$r['work_category_id'],'work')." ".$options->getAttribute('work_category','work_category_id',$r['sub_work_category_id'],'work').'</td>';	
		echo '<td>'.$r['type'].'</td>';	
		echo '<td>'.(($r['status'] == "S") ? "SAVED" : ($r['status'] == "F") ? "FINISHED" : "CANCELLED").'</td>';	
		echo '<td>'.$options->getUserName($r['user_id']).'</td>';	
        echo '</tr>';
    }
    ?>
    </table>
    <div class="pagination">
	   	 <?=$pagination?>
    </div>
<?php
}else{
?>
    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>RTP MANAGEMENT SYSTEM</div>
        <div class="module_actions">
            <input type="hidden" name="rtp_header_id" value="<?=$aTrans['rtp_header_id']?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form">
            	<tr>
                	<td>DATE REQUESTED:</td>
                    <?php $date = (!empty($aTrans['date'])) ? $aTrans['date'] : date("Y-m-d") ?>
                    <td><input type="text" class="textbox datepicker" name="date" value="<?=$date?>" /></td>
                </tr>
                
                <tr>
                	<td>DATE NEEDED:</td>
                    <?php $date_needed = (!empty($aTrans['date_needed'])) ? $aTrans['date_needed'] : date("Y-m-d") ?>
                    <td><input type="text" class="textbox datepicker" name="date_needed" value="<?=$date_needed?>" /></td>
                </tr>
                
                <tr>
                	<td>DATE RECEIVED:</td>
                    <td><input type="text" class="textbox datepicker" name="date_received" value="<?=$aTrans['date_received']?>" /></td>
                </tr>
                <tr>
                	<td>PROJECT: <br /></td>
                    <td>
                    	<input type="text" class="textbox project" value="<?=$options->getAttribute('projects','project_id',$aTrans['project_id'],'project_name')?>" onclick="this.select();" />
						<input type="hidden" name="project_id" value="<?=$aTrans['project_id']?>" />
                    </td>
               	</tr>
            </table>
            <table class="table-form">
                <tr>
                	<td>WORK CATEGORY: <br /></td>
                    <td>
                    	<?=$options->option_workcategory($aTrans['work_category_id'],'work_category_id','Select Work Category')?>
                    </td>
               	</tr>                
                <tr>
                	<td>SUB WORK CATEGORY: <br /></td>
                    <td>
                    	<div id="subworkcategory">
            	
			            </div>
                    </td>
               	</tr>
            </table>
          	<table class="table-form">
                <tr>
                	<td>REFERENCE: <br /></td>
                    <td>
                    	<input type="text" class="textbox" name="reference" value="<?=$aTrans['reference']?>" />
                    </td>
               	</tr>
                <tr>
                	<td style="vertical-align:top;">REMARKS</td>
                    <td>
                    	<textarea style="border:1px solid #c0c0c0; width:100%;" name="remarks"><?=$aTrans['remarks']?></textarea>
                    </td>
                </tr>
            </table>
        </div>
        <div class="module_actions" >
        	<?php

        	$aVal['type'] = ( empty($aVal['type']) ) ? "RTP" : $aVal['type'];
        	?>
        	<div class="inline">
        	    <input type="radio" name="type" id="RTP" value="RTP" <?php if( $aVal['type']  == "RTP") echo "checked" ?>  > <label for="RTP" >RTP</label>
        	    <input type="radio" name="type" id="TS" value="TS" <?php if( $aVal['type']  == "TS") echo "checked" ?> > <label for="TS" >TRANSMITTAL</label>
        	</div>
        </div>

        <?php if(!empty($aTrans['status'])){ ?>
        <div class="module_actions">
        	<!--<div style="display:inline-block;">
            	WR # <br />
                <input type="text" class="textbox" value="<?=str_pad($rtp_header_id,7,0,STR_PAD_LEFT)?>" readonly="readonly" />
            </div> -->
            
            <div style="display:inline-block;">
            	Status <br />
                <input type="text" class="textbox" value="<?=$options->getTransactionStatusName($aTrans['status'])?>" readonly="readonly" />
            </div>
            
            <div style="display:inline-block;">
            	Encoded by <br />
                <input type="text" class="textbox" value="<?=$options->getUserName($aTrans['user_id'])?>" readonly="readonly" />
            </div>
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php if($aTrans['status']=="S"){ ?>
			<input type="submit" name="b" id="b" value="Update" />
			<input type="submit" name="b" id="b" value="Finish" />
			<?php }else if($aTrans['status']!="F" && $aTrans['status']!="C"){ ?>
			<input type="submit" name="b" id="b" value="Submit" />
			<?php } if($b!="Print Preview" && !empty($aTrans['status'])){ ?>
			<input type="submit" name="b" id="b" value="Print Preview" />
			<?php } if($b=="Print Preview"){ ?>	
			<input type="button" value="Print" onclick="printIframe('JOframe');" />	
			<?php } if($aTrans['status']!="C" && !empty($aTrans['status'])){ ?>
			<input type="submit" name="b" id="b" value="Cancel" /> 
            <?php if($aTrans['status'] == "F"){ ?>
			<input type="submit" name="b" id="b" value="Unfinish" />
            <?php } ?>
            <?php  ?>
			<?php } ?>
			<a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
        </div>
        <?php if($aTrans['status'] == "S"){ ?>
        <div class="module_actions">
            <div style="display:inline-block;">
            	DESCRIPTION<br />
                <input type="text" class="textbox" name="description" style="width:400px;" onclick="this.select();" onkeypress="if(event.keyCode==13){ j('#quantity').focus(); return false; }"  autocomplete="off" autofocus="autofocus" />
            </div>
            
            <div style="display:inline-block;">
            	QUANTITY<br />
                <input type="text" id="quantity" name="quantity" class="textbox3" onkeypress="if(event.keyCode==13){ j('#price').focus(); return false; }"  autocomplete="off" />
            </div>
            
            <div style="display:inline-block;">
            	UNIT<br />
                <input type="text" id="unit" name="unit" class="textbox3" onkeypress="if(event.keyCode==13){ j('#price').focus(); return false; }"  autocomplete="off" />
            </div>
            
            <div style="display:inline-block;">
            	MCD QTY<br />
                <input type="text" id="mcd_qty" name="mcd_qty" class="textbox3" onkeypress="if(event.keyCode==13){ j('#price').focus(); return false; }"  autocomplete="off" />
            </div>
            
            <div style="display:inline-block;">
            	BUDGET QTY<br />
                <input type="text" id="budget_qty" name="budget_qty" class="textbox3" onkeypress="if(event.keyCode==13){ j('#price').focus(); return false; }"  autocomplete="off" />
            </div>
            
            <div style="display:inline-block;">
            	ACTUAL QTY<br />
                <input type="text" id="actual_qty" name="actual_qty" class="textbox3" onkeypress="if(event.keyCode==13){ j('#price').focus(); return false; }"  autocomplete="off" />
            </div>
            
            <div style="display:inline-block;">
            	BALANCE QTY<br />
                <input type="text" id="balance_qty" name="balance_qty" class="textbox3" onkeypress="if(event.keyCode==13){ j('#price').focus(); return false; }"  autocomplete="off" />
            </div>
            
            <input type="submit" name="b" value="Add" />
        </div>
        <?php } ?>
        <?php if(!empty($aTrans['status']) && $b != "Print Preview"){ ?>
        <table class="display_table" style="width:100%;">
        	<tr>
            	<th width="20"></th>
	        	<th width="40">Qty</th>
                <th width="40">Unit</th>
                <th>Item Description </th>
                <th width="40">c/o MCD</th>
                <th width="40" >In-House Budget</th>
                <th width="40">Acutal Received</th>
                <th width="40">Balance</th>
           	</tr>
            <?php
			$result = mysql_query("
				select * from rtp_detail where rtp_header_id = '$aTrans[rtp_header_id]' and rtp_void = '0'
			") or die(mysql_error());
			$i=1;
			while($r = mysql_fetch_assoc($result)){
				echo "
					<tr>
						<td><a href='admin.php?view=$view&rtp_header_id=$aTrans[rtp_header_id]&b=d&rtp_detail_id=$r[rtp_detail_id]'><img src='images/trash.gif' style='cursor:pointer;' onclick='return approve_confirm();'></a></td>
						<td style='text-align:right;'><input type='text' class='textbox3' name='_quantity[]' style=' text-align:right;' value='$r[quantity]'></td>
						<td style='text-align:left;'><input type='text' class='textbox3' name='_unit[]' style=' text-align:left;' value='$r[unit]'></td>
						<td><input type='text' class='textbox' style='width:100%;' name='_description[]' value='$r[description]'></td>
						
						<td style='text-align:right;'><input type='text' class='textbox3' name='_mcd_qty[]' 	style=' text-align:right;' value='$r[mcd_qty]'></td>
						<td style='text-align:right;'><input type='text' class='textbox3' name='_budget_qty[]' 	style=' text-align:right;' value='$r[budget_qty]'></td>
						<td style='text-align:right;'><input type='text' class='textbox3' name='_actual_qty[]' 	style=' text-align:right;' value='$r[actual_qty]'></td>
						<td style='text-align:right;'><input type='text' class='textbox3' name='_balance_qty[]' style=' text-align:right;' value='$r[balance_qty]'></td>
						
						<input type='hidden' name='_rtp_detail_id[]' value='$r[rtp_detail_id]'
					</tr>
				";
			}
			?>
        </table>
        <?php }else if($b == "Print Preview" && $aTrans['rtp_header_id']){ 
    		echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='transactions/print_rtp.php?id=$aTrans[rtp_header_id]' width='100%' height='500'>
			</iframe>";    
        } 
		?>
    </div>
    
<?php } ?>
<?php
/*if($b == "Print Preview" && $rtp_header){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$rtp_header' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
jQuery(function(){	
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php if(!empty($aTrans['status'])){ ?>
		xajax_display_subworkcategory('<?=$aTrans['work_category_id']?>','<?=$aTrans['sub_work_category_id']?>');
	<?php } ?>
});
</script>
	