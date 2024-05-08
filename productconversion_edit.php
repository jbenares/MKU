<script type="text/javascript" src="scripts/jquery.validate.js"></script>
<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
.search_table tr:hover td{
	background-color:#B5E2FE;
}
label.error { float: none; display:block; color: red; padding-left: .5em; vertical-align: top; }
.alignLeft
{
	text-align:left;	
}
#messageError{
	padding:0px 5px;
}

ul{
	list-style:square;	
	margin-left:20px;
}
</style>

<?php
$b					= $_REQUEST['b'];
$date				= $_REQUEST['date'];
$locale_id			= $_REQUEST['locale_id'];

$finishedproduct_id	= $_REQUEST['finishedproduct_id'];
$type				= $options->getStockType($finishedproduct_id);

$packagetype		= $_REQUEST['packagetype'];
$packqty			= $_REQUEST['packqty'];

$quantity			= $_REQUEST['quantity'];

$stock_id			= $_REQUEST['stock_id'];
$qty				= $_REQUEST['qty'];

$user_id			= $_SESSION['userID'];
$user_name			= $options->getUserName($_SESSION[userID]);

$product_convert_id	= $_REQUEST['product_convert_id'];

if($b){
	if($type=="FP"){
		$balance=$options->getInventoryBalanceOfFinishedProductForJO($_REQUEST[finishedproduct_id],$_REQUEST[packagetype],$date,$_REQUEST[locale_id]);
	}else{
		$balance=$options->getCurrentBalanceOfStock($finishedproduct_id,$date,$locale_id);	
	}
	
	
	if($balance>0){
		if($b=="Submit"){
			$status="S";
			
			
			$query="
				insert into
					product_convert
				set
					date='$date',
					locale_id='$locale_id',
					finishedproduct_id='$finishedproduct_id',
					packagetype='$packagetype',
					stock_id='$stock_id',
					packqty='$packqty',
					qty='$qty',
					audit='Added by ".$user_name."',
					status='$status',
					user_id='$user_id',
					quantity='$quantity'
			";	
			
			mysql_query($query);
			$product_convert_id = mysql_insert_id();
			
			$options->solveWeightedAvg($product_convert_id,$date,$quantity);
		}else if($b=="Finish"){
			$status="F";
			
			
			$audit=$options->getAuditOfProductConversion($_REQUEST[product_convert_id]);
			$audt.=" Updated by $user_name on ".date("Y-m-d H:i:s");
			$query="
				update
					product_convert
				set
					date='$date',
					locale_id='$locale_id',
					finishedproduct_id='$finishedproduct_id',
					packagetype='$packagetype',
					stock_id='$stock_id',
					packqty='$packqty',
					qty='$qty',
					audit='$audit',
					status='$status',
					user_id='$user_id',
					quantity='$quantity'
				where
					product_convert_id='$product_convert_id'
			";	
			
			mysql_query($query);
			$options->solveWeightedAvg($product_convert_id,$date,$quantity,TRUE);
			
		}else if($b=="Update"){
			$status="S";
			
			
			$audit=$options->getAuditOfProductConversion($_REQUEST[product_convert_id]);
			$audt.=" Updated by $user_name on ".date("Y-m-d H:i:s");
			$query="
				update
					product_convert
				set
					date='$date',
					locale_id='$locale_id',
					finishedproduct_id='$finishedproduct_id',
					packagetype='$packagetype',
					stock_id='$stock_id',
					packqty='$packqty',
					qty='$qty',
					audit='$audit',
					status='$status',
					user_id='$user_id',
					quantity='$quantity'
				where
					product_convert_id='$product_convert_id'
			";	
			
			mysql_query($query);
			
			$options->solveWeightedAvg($product_convert_id,$date,$quantity);
		}
		
	}else{
		echo '
			<script type="text/javascript">
				alert("Unable to Convert. Stock has inventory \n Balance of : '.$balance.'");
			</script>
		';
	}
		
}
	
$result=mysql_query("
	select
		*
	from
		product_convert
	where
		product_convert_id='$product_convert_id'
");

$r=mysql_fetch_assoc($result);

$date				= $r['date'];
$locale_id			= $r['locale_id'];

$finishedproduct_id	= $r['finishedproduct_id'];
$type				= $options->getStockType($finishedproduct_id);

$packagetype		= $r['packagetype'];
$packqty			= $r['packqty'];

$quantity			= $r['quantity'];

$stock_id			= $r['stock_id'];
$qty				= $r['qty'];
$status				= $r['status'];


?>

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'>EDIT PRODUCT CONVERSION</div>
    <div class="module_actions">
    <form name="header_form" id="header_form" action="" method="post">
    <input type="hidden" name="product_convert_id" value="<?=$product_convert_id?>"  /> 
    <div id="messageError">
    	<ul>
        </ul>
    </div>
    <div class='inline'>
        <div>Date: </div>        
        <div>
            <input type="text" class="required textbox3" title="Please enter date" id='date' name="date" onclick=fPopCalendar("date"); readonly='readonly'  value="<?=$date?>">
        </div>
    </div>    	
    <div class='inline'>
        <div>Location : </div>        
        <div>
		<?php
            echo $options->getAllLocationOptions($locale_id,'locale_id');
        ?> 
            
        </div>
    </div>   
    <div class='inline'>
        <div>Finished Product: </div>        
        <div>
		<?=$options->getAllMaterialOptions($finishedproduct_id,'finishedproduct_id',"PM");?>
        </div>
    </div>   
    
    <div class="inline" id="qty_div">
     
    </div>
    
    <div class='inline'>
        <div>Stock: </div>        
        <div>
       	<?php
			echo $options->getRawMaterials($stock_id,'stock_id');
		?>
        </div>
    </div> 
    
    <div class='inline'>
        <div>Quantity: </div>        
        <div>
        	<input type="text" name="qty" id="qty" readonly="readonly" value="<?=$qty?>" class="textbox3" />
        </div>
    </div> 
   	<?php if($status){ ?>
    <div class='inline'>
        <div>Status : </div>        
        <div>
            <input type="text" readonly="readonly" value="<?=$options->getTransactionStatusName($status);?>" />
        </div>
    </div>
    <?php } ?>
    
   	<?php if($status!="F"){ ?> 
    	<?php if(empty($status)){ ?>
        <input type="submit" name="b" id="b" value="Submit" class="buttons"/>
       	<?php }else{ ?>
        <input type="submit" name="b" id="b" value="Update" class="buttons"/>
        <input type="submit" name="b" id="b" value="Finish" class="buttons"/>
        <?php } ?>
   	<?php } ?>
          
   
        
    </form>
</div>

<script type="text/javascript">
j(function(){
	xajax_displayProductConvertQty(xajax.getFormValues('header_form'));	
	j("#finishedproduct_id").change(function(){
		xajax_displayProductConvertQty(xajax.getFormValues('header_form'));	
	});
});
</script>
