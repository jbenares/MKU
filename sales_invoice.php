
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

</style>
<?php
#DO NOT REMOVE
$b					= $_REQUEST['b'];
$user_id			= $_SESSION['userID'];

#FOR SEARCH
$search_project	= $_REQUEST['search_project'];	
$search_project_id	= ($search_project) ? $_REQUEST['search_project_id'] : "";
$search_invoice_no	= $_REQUEST['search_invoice_no'];

#HEADER
$sales_invoice_id		= $_REQUEST['sales_invoice_id'];
$date					= $_REQUEST['date'];
$project_id				= $_REQUEST['project_id'];
$amount					= $_REQUEST['amount'];
$invoice_no				= $_REQUEST['invoice_no'];	
$ar_gchart_id			= $_REQUEST['ar_gchart_id'];
$sales_gchart_id		= $_REQUEST['sales_gchart_id'];
$date_received			= $_REQUEST['date_received'];

if($b=="Submit"){
	$query="
		insert into 
			sales_invoice
		set
			date = '$date',
			date_received = '$date_received',
			project_id = '$project_id',
			amount = '$amount',
			invoice_no = '$invoice_no',
			ar_gchart_id = '$ar_gchart_id',
			sales_gchart_id = '$sales_gchart_id',
			user_id	 = '$user_id'
	";	
	
	mysql_query($query) or die(mysql_error());
	$sales_invoice_id = mysql_insert_id();
	$msg="Transaction Saved";
	
}else if($b=="Update"){
	$query="
		update
			sales_invoice
		set
			date = '$date',
			date_received = '$date_received',
			project_id = '$project_id',
			amount = '$amount',
			invoice_no = '$invoice_no',
			ar_gchart_id = '$ar_gchart_id',
			sales_gchart_id = '$sales_gchart_id',
			user_id	 = '$user_id'
		where
			sales_invoice_id = '$sales_invoice_id'
	";	
	
	mysql_query($query) or die(mysql_error());
	
	$msg = "Transaction Updated";
}else if($b=="Cancel"){
	$query="
		update
			sales_invoice
		set
			status='C'
		where
			sales_invoice_id = '$sales_invoice_id'
	";	
	mysql_query($query);
	$msg = "Transaction Cancelled";
	$options->cancelGL($sales_invoice_id,'sales_invoice_id','SJ');
	
}else if($b=="Finish"){
	$query="
		update
			sales_invoice
		set
			status='F'
		where
			sales_invoice_id = '$sales_invoice_id'
	";	
	
	mysql_query($query);
	$msg = "Transaction Finished";
	$options->postSalesInvoice($sales_invoice_id);
}else if($b == "Unfinish"){
    $query="
		update
			sales_invoice
		set
			status='S'
		where
			sales_invoice_id = '$sales_invoice_id'
	";	
	mysql_query($query);
	$msg = "Transaction Unfinished";
	$options->cancelGL($sales_invoice_id,'sales_invoice_id','SJ');
}else if($b == "Add"){
    $amount = $_REQUEST[qty]*$_REQUEST[unit_price];
    $query="
        insert into 
            sales_invoice_detail
        set
            sales_invoice_id = '$sales_invoice_id',
            qty = '$_REQUEST[qty]',
            unit = '$_REQUEST[unit]',
            description = '$_REQUEST[description]',
            unit_price = '$_REQUEST[unit_price]',
            amount = '$amount'
    ";  
    
    mysql_query($query) or die(mysql_error());
    $msg="Details Added";
    
}else if($b == "D"){
    $query="
        delete from
            sales_invoice_detail
        where
            sales_invoice_detail_id = '$_REQUEST[sales_invoice_detail_id]'
    ";  
    mysql_query($query);
    $msg = "Item Deleted";
    header('location: admin.php?view='.$_REQUEST[view].'&sales_invoice_id='.$_REQUEST[sales_invoice_id]);
    //$options->cancelGL($sales_invoice_id,'sales_invoice_id','SJ');
}
$query="
	select
		*
	from
		sales_invoice
	where
		sales_invoice_id ='$sales_invoice_id'
";

$result=mysql_query($query) or die(mysql_error());
$r=mysql_fetch_assoc($result);

$date					= $r['date'];
$date_received			= $r['date_received'];
$project_id				= $r['project_id'];
$amount					= $r['amount'];
$invoice_no				= $r['invoice_no'];	
$sales_gchart_id		= $r['sales_gchart_id'];	
$ar_gchart_id			= $r['ar_gchart_id'];
$status					= $r['status'];
$user_id				= $r['user_id'];
?>

<?php
if( $status=="F" || $status == "C" ){
?>
<style type="text/css">
.cp_table tr td:nth-child(1),.cp_table  tr th:nth-child(1){
	display:none;	
}
</style>
<?php } ?>
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Project : <br />  
        <input type="text" class="textbox project" name="search_project" value="<?=$search_project?>"  onclick="this.select();"/>
        <input type="hidden" name="search_project_id" value="<?=$search_project_id?>"  />
    </div>   
	<div class="inline">
    	Invoice # : <br />  
        <input type="text" class="textbox" name="search_invoice_no" value="<?=$search_invoice_no?>"  onclick="this.select();"/>
    </div>
    <input type="submit" name="b" value="Search" />
 	<a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php if($b == "Search"){ ?>
	<?php
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "select * from sales_invoice as s, projects as p where s.project_id = p.project_id";
	
	if(  $search_project_id ) $sql .= " and s.project_id = '$search_project_id'";
	if(  $search_invoice_no ) $sql .= " and invoice_no like '%$search_invoice_no%'";
        
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
    ?>
    <div class="pagination">
        <?=$pager->renderFullNav("$view&b=Search&search_project=$search_project&search_project_id=$search_project_id&search_invoice_no=$search_invoice_no")?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20"><b>#</b></th>
        <th width="20"></th>
        <th>S.I. # :</th>
        <th>Invoice # :</th>
        <th>Date</th>
        <th>Project</th>
        <th>Amount</th>
        <th>Status</th>
    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
        echo '<td width="15"><a href="admin.php?view='.$view.'&sales_invoice_id='.$r[sales_invoice_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
        echo '<td>'.str_pad($r['sales_invoice_id'],7,0,STR_PAD_LEFT).'</td>';
		echo '<td>'.$r['invoice_no'].'</td>';	
        echo '<td>'.date("F j, Y",strtotime($r['date'])).'</td>';	
        echo '<td>'.$r['project_name'].'</td>';	
        echo '<td>'.$r['amount'].'</td>';	
        echo '<td>'.$options->getTransactionStatusName($r['status']).'</td>';	
        echo '</tr>';
    }
    ?>
    </table>
    <div class="pagination">
	    <?=$pager->renderFullNav("$view&b=Search&search_project=$search_project&search_project_id=$search_project_id&search_invoice_no=$search_invoice_no")?>
    </div>
    <?php
}else{
?>
    <div class=form_layout>
        <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
        <div class="module_title"><img src='images/user_orange.png'>SALES INVOICE</div>
        
        <div class="module_actions">
            <input type="hidden" name="sales_invoice_id" id="sales_invoice_id" value="<?=$sales_invoice_id?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            
            <div class='inline'>
                <div>Date: </div>        
                <div>
                    <input type="text" class="datepicker textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>">
                </div>
            </div>   
            
            <div class='inline'>
                <div>Invoice #: </div>        
                <div>
                    <input type="text" class="textbox3"  name="invoice_no" value="<?=$invoice_no?>">
                </div>
            </div>    
            
            
           <div class='inline'>
                Project : <br />  
                <input type="text" class="textbox project" value="<?=$options->getAttribute('projects','project_id',$project_id,'project_name')?>" onclick="this.select();"  />
                <input type="hidden" name="project_id" value="<?=$project_id?>" title="Please select Project" />
            </div>
            
            <div class='inline'>
                Amount: <br />
                <input type="text" class="textbox" name="amount" value="<?=$amount?>" autocomplete="off">
            </div> 
			<div class='inline'>
                <div>Date Received: </div>        
                <div>
                    <input type="text" class="datepicker textbox3" title="Please enter date"  name="date_received" readonly='readonly'  value="<?=$date_received?>">
                </div>
            </div>   			
           
            <br />
        
            <div class="inline">
                A/R Account : <br />
                <?=$options->option_chart_of_accounts($ar_gchart_id,'ar_gchart_id')?>
            </div>
            
            <div class="inline">
                Sales Account : <br />
                <?=$options->option_chart_of_accounts($sales_gchart_id,'sales_gchart_id')?>
            </div>
            
            <?php
            if(!empty($status)){
            ?>
            <br />
            <div class='inline'>
                <div>Status : </div>        
                <div>
                    <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
                </div>
            </div> 
            
            <div class='inline'>
                <div>Encoded by : </div>        
                <div>
                    <input type='text' class="textbox" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
                </div>
            </div> 
            <?php
                
            }


            ?>
        </div>
        <div class="module_actions">
            <?php if($status=="S"){ ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish" />
            <?php }else if($status!="F" && $status!="C"){ ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
            <?php if($b!="Print Preview" && !empty($status)){ ?>
                <input type="submit" name="b" id="b" value="Print Preview" />
            <?php } ?>
            <?php if($b=="Print Preview"){ ?>	
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } ?>
            <?php if($status!="C" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Cancel" />
            <?php } ?>
            <?php if($status=="F" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Unfinish" />
            <?php } ?>
            <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>  
            <?php
            if($status=="S"){
                    ?>
                        <br/>
                         <br/>
                        <div class='inline'>
                            <div>Description: </div>        
                            <div>
                                <input type='text' class="textbox" name="description" />
                            </div>
                        </div> 
                        <div class='inline'>
                            <div>Qty: </div>        
                            <div>
                                <input type='text' class="textbox3" name="qty" />
                            </div>
                        </div> 
                         <div class='inline'>
                            <div>Unit: </div>        
                            <div>
                                <input type='text' class="textbox3" name="unit" />
                            </div>
                        </div>
                        <div class='inline'>
                            <div>Unit Price: </div>        
                            <div>
                                <input type='text' class="textbox3" name="unit_price" />
                            </div>
                        </div>
                        <div class='inline'>
                             
                            <div>
                                <input type="submit" name="b" value="Add" />
                            </div>
                        </div> 
                    <?php
                }
        ?>
        </div>
        <?php
            $sql="select * from sales_invoice_detail where sales_invoice_id = '$sales_invoice_id'";
            $query=mysql_query($sql);
            ?>
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table" style="border:1px solid #000;">
                    <tr>
                        <th width="20" align="center"></th>
                        <th>Description</th>
                        <th style="text-align:center;">Unit</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:center;">Unit Price</th>
                        <th style="text-align:center;">Amount</th>
                    </tr>
            <?php
            $total=0;
            while($r=mysql_fetch_assoc($query)){
                $total+=$r[amount];
                ?>
                    <tr>
                        <td><a href="admin.php?view=<?=$view?>&sales_invoice_detail_id=<?=$r['sales_invoice_detail_id']?>&b=D&sales_invoice_id=<?=$sales_invoice_id?>" ><img src="images/trash.gif" onclick="return approve_confirm();" /></a></td>
                        <td><?=$r['description']?></td>
                        <td style="text-align:center;"><?=$r['unit']?></td>
                        <td style="text-align:center;"><?=$r['qty']?></td>
                        <td style="text-align:right;"><?=number_format($r['unit_price'],2)?></td>
                        <td style="text-align:right;"><?=number_format($r['amount'],2)?></td>
                    </tr>
                <?php
            }
        ?> 
                <tr>
                    <td colspan="5" style="text-align:right;">Total Amount Due</td>
                    <td style="text-align:right;"><b><?=number_format($total,2)?></b></td>
                </tr>
                </table>
    </div>
	<?php
    if($b == "Print Preview" && $sales_invoice_id){
    
        echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_sales_invoice.php?id=$sales_invoice_id' width='100%' height='500'>
                </iframe>";
    }
	?>
<?php } ?>
</form>
<script type="text/javascript">
j(function(){	
});
</script>
	