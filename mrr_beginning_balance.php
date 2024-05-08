<?php	
require_once("my_Classes/depreciation.class.php");

#echo Depreciation::getAccumulatedDepreciation("2007-05-31","2012-07-12",11000000,240);
#echo Depreciation::getNetBookValue("2007-05-31","2012-12-27",11000000,240);
$stock_name 	= $_REQUEST['stock_name'];
$stock_id 		= ($stock_name)?$_REQUEST['stock_id']:"";

function getDetails($rr_header_id){
    $sql = "
        select 
            rr_detail_id,stock,asset_code,details,date_acquired,d.cost,estimated_life
        from 
            rr_detail as d, productmaster as p 
        where
            d.stock_id = p.stock_id
        and
            rr_header_id = '$rr_header_id'
    ";
    return lib::getArrayDetails($sql);
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
.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}
</style>
<?php
	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];
	
	#SEARCH 
	$search_rr_header_id	= $_REQUEST['search_rr_header_id'];
	
	#HEADER
	$rr_header_id		= $_REQUEST['rr_header_id'];
	$date				= $_REQUEST['date'];
	$supplier_id		= $_REQUEST['supplier_id'];
	$rr_in				= "W"; #WAREHOUSE
	$rr_type			= "A"; #ASSET
	$ppe_gchart_id		= $_REQUEST['ppe_gchart_id'];
	
	#DETAILS
	$rr_detail_id		= $_REQUEST['rr_detail_id'];
	$stock_id			= $_REQUEST['stock_id'];
	$cost				= $_REQUEST['cost'];
	$asset_code			= $_REQUEST['asset_code'];
	$details			= $_REQUEST['details'];
	$date_acquired		= $_REQUEST['date_acquired'];
	$estimated_life		= $_REQUEST['estimated_life'];
	
	#DETAIL QUERIES
	if($b == 'D'){ 
		mysql_query("
			delete from
				rr_detail
			where
				rr_detail_id = '$rr_detail_id'
		") or die(mysql_error());
		
		$msg = "ITEM DELETED";
	}else if( $b == "ADD PPE" ){
		mysql_query("
			insert into
				rr_detail
			set
				rr_header_id 	= '$rr_header_id',
				stock_id 		= '$stock_id',
				cost 			= '$cost',
				asset_code 		= '$asset_code',
				details 		= '$details',
				date_acquired 	= '$date_acquired',
				estimated_life 	= '$estimated_life',
				quantity 		= '1',
				amount			= '$cost'
		") or die(mysql_error());
		$msg = "ITEM ADDED";
	}
	
	#HEADER QUERIES
	if($b=="Submit"){
		$query="
			insert into 
				rr_header
			set
				date 		= '$date',
				supplier_id = '$supplier_id',
				rr_in 		= '$rr_in',
				rr_type 	= '$rr_type',
				status 		= 'S',
				user_id 	= '$user_id',
				ppe_gchart_id = '$ppe_gchart_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		$rr_header_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		$query="
			update
				rr_header
			set
				date 		= '$date',
				supplier_id = '$supplier_id',
				status 		= 'S'
			where
				rr_header_id = '$rr_header_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	if( !empty($rr_header_id) ){
	$query="
		select
			*
		from
			rr_header 
		where
			rr_header_id = '$rr_header_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	}
	
	$date				= $r['date'];
	$supplier_id		= $r['supplier_id'];
	$user_id			= $r['user_id'];
	$ppe_gchart_id		= $r['ppe_gchart_id'];
	$status				= $r['status'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        MRR # : <br />  
        <input type="text" class="textbox"  name="search_rr_header_id" value="<?=$search_rr_header_id?>"  onclick="this.select();"  autocomplete="off" />
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
            rr_detail_id,stock,asset_code,details,date_acquired,d.cost,estimated_life, h.*
        from 
            rr_header as h 
            inner join rr_detail as d on h.rr_header_id = d.rr_header_id
            inner join productmaster as p on d.stock_id = p.stock_id            
        where                            
            h.rr_type = 'A'
        and h.po_header_id = '0'
    ";
        
    if(!empty($search_rr_header_id)) $sql.=" and  h.rr_header_id = '$search_rr_header_id' ";
    
	
	$sql.=" order by h.date desc ";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_rr_header_id=$search_rr_header_id");
    ?>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
        <th width="20">#</th>
        <th width="20"></th>
        <th>MRR #</th>
        <th>ASSET</th>
        <th>ASSET CODE</th>
        <th>DESCRIPTION</th>
        <th>DATE ACQUISITION</th>
        <th>DATE</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&rr_header_id='.$r['rr_header_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.str_pad($r['rr_header_id'],7,0,STR_PAD_LEFT).'</td>';	      

        echo "<td>$r[stock]</td>";
        echo "<td>$r[asset_code]</td>";
        echo "<td>$r[details]</td>";
        echo '<td>'.date("m/d/Y",strtotime($r['date_acquired'])).'</td>';    


		echo '<td>'.date("m/d/Y",strtotime($r['date'])).'</td>';	
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
        <div class="module_title"><img src='images/user_orange.png'>MRR ASSET BEGINNING BALANCE</div>
        <div class="module_actions">
            <input type="hidden" name="rr_header_id" value="<?=$rr_header_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <table class="table-form">
            	<tr>
                	<td>
                    	DATE:
                   	</td>
                    <td>
                        <input type="text" class="textbox datepicker" name="date" value="<?=$date?>"  />
                    </td>
               	</tr>
                <tr>
                    <td>
                    	SUPPLIER:
                   	</td>
                    <td>
                        <input type="text" class="textbox supplier" value="<?=$options->getAttribute('supplier','account_id',$supplier_id,'account')?>" />
                        <input type="hidden" name="supplier_id" value="<?=$supplier_id?>" />
                    </td>
				</tr>
                <tr>
                    <td>
                    	PPE ACCOUNT:
                   	</td>
                    <td>
                        <?=$options->getTableAssoc($r['ppe_gchart_id'],'ppe_gchart_id','Select PPE Account',"select * from gchart where parent_gchart_id = '943' order by gchart asc",'gchart_id','gchart');?>
                    </td>
                </tr>
            </table>
            <?php if(!empty($rr_header_id)){ ?>
           	<table class="table-form">
            	<tr>
                	<td>MRR #:</td>
                    <td><input type="text" class="textbox" value="<?=str_pad($rr_header_id,7,0,STR_PAD_LEFT)?>" readonly="readonly" /></td>
               	</tr>
                <tr>
                	<td>STATUS:</td>
                    <td><input type="text" class="textbox" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" /></td>
               	</tr>
                <tr>
                	<td>ENCODED BY:</td>
                    <td><input type="text" class="textbox" value="<?=$options->getUserName($user_id)?>" readonly="readonly" /></td>
               	</tr>
          	</table>
            <?php } ?>
        </div>
        <?php if(!empty($rr_header_id)){ ?>
        <div class="module_actions">
			
        </div>
        <?php } ?>
        <div class="module_actions">
            <?php
            if(!empty($rr_header_id)){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <?php
            }else{
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php
            }
            ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
            <?php
			if(!empty($rr_header_id)){
            ?>
            <a href="admin.php?view=<?=$view?>&rr_header_id=<?=$rr_header_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
            <?php
			}
            ?>
        </div>
        <?php if(!empty($rr_header_id)){ ?>
        <div style="background:#000; color:#FFF; font-weight:bolder; padding:4px; font-size:11px;">
        	PPE ENTRY
        </div>
        <div class="module_actions">
        	<table class="table-form">
            	<tr>
                	<td>ITEM :</td>
                    <td>
                    	<input type="text" class="textbox" id="stock_name" name="stock_name" value="<?=$stock_name?>"  onclick="this.select();" />
						<input type="hidden" name="stock_id" id="stock_id" value="<?=$stock_id?>" />
                    </td>        
                </tr>
                <tr>
                	<td>ACQUISITION COST : </td>
                    <td><input type="text" class="textbox hinder-submit" name="cost" id="acquisition_cost"   /></td>
                </tr>
                <tr>
                	<td>ASSET CODE :</td>
                    <td><input type="text" class="textbox hinder-submit"  name="asset_code"  /></td>
                </tr>
                <tr>
                	<td>ASSET DESCRIPTION:</td>
                    <td><input type="text" class="textbox hinder-submit" name="details"  /></td>
                </tr>
            </table>
            <table class="table-form">
            	<tr>
                	<td>DATE ACQUIRED:</td>
                    <td>
                    	<input type="textbox" class="textbox datepicker hinder-submit" name="date_acquired" />
                    </td>        
                </tr>
                <tr>
                	<td>ESTIMATED USEFUL LIFE IN MONTHS: </td>
                    <td><input type="text" class="textbox hinder-submit" name="estimated_life" id="estimated_life" /></td>
                </tr>
                <tr>
                	<td>ACCUMULATED DEPRECIATION PER MONTH :</td>
                    <td><input type="text" class="textbox hinder-submit" id="depr_per_month"  readonly="readonly"/></td>
                </tr>
            </table>
       	</div>
		<div class="module_actions">
            <input type="submit" name="b" value="ADD PPE" />
        </div>
        <table class="display_table" style="width:100%;">
        	<?php
			$result = mysql_query("
				select 
					rr_detail_id,stock,asset_code,details,date_acquired,d.cost,estimated_life
				from 
					rr_detail as d, productmaster as p 
				where
					d.stock_id = p.stock_id
				and
					rr_header_id = '$rr_header_id'
			") or die(mysql_error());
            ?>
        	<tr>
            	<th width="20"></th>
            	<th>ASSET</th>
                <th>ASSET CODE</th>
                <th>ASSET DESCRIPTION</th>
                <th>DATE ACQUIRED</th>
                <th>ACQUISITION COST</th>
                <th>ESTIMATED USEFUL LIFE IN MONTHS</th>
            </tr>
            <?php
			while($r = mysql_fetch_assoc($result)){
            ?>
            <tr>
            	<td><a href="admin.php?view=<?=$view?>&b=D&rr_detail_id=<?=$r['rr_detail_id']?>&rr_header_id=<?=$rr_header_id?>"><img src="images/trash.gif" /></a></td>
            	<td><?=$r['stock']?></td>
                <td><?=$r['asset_code']?></td>
                <td><?=$r['details']?></td>
                <td><?=date("m/d/Y",strtotime($r['date_acquired']))?></td>
                <td><?=number_format($r['cost'],2,'.',',')?></td>
                <td><?=$r['estimated_life']?> MONTHS</td>
            </tr>
            <?php } ?>
        </table>
        <?php } #END OF TABLE ?>
    </div>
    
<?php
}
?>
<?php
/*if($b == "Print Preview" && $customer){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$customer' width='100%' height='500'>
			</iframe>";
}
*/
			
?>
</form>
<script type="text/javascript">
j(function(){	
	j("#estimated_life,#acquisition_cost").change(function(){
		//alert(j("#acquisition_cost").val());
		//alert(j("#estimated_life").val());
		var monthly_depr = j("#acquisition_cost").val() / j("#estimated_life").val();
		j("#depr_per_month").val(monthly_depr);
	});
});
</script>
	