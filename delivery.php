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


	if($_REQUEST[b]=="Submit"){
			
		/*Insert into Header*/	
		
		// format jobdate
			
		$drnum=$options->generateDRNumber();
		
		$query="
			insert into
				dr_header
			set
				drnum='$drnum',
				date='$_REQUEST[date]',
				account_id='$_REQUEST[account_id]',
				locale_id='$_REQUEST[locale_id]',
				grossamount='$_REQUEST[grossamount]',
				netamount='$_REQUEST[netamount]',
				discounttotal='$_REQUEST[discount_header]',
				tax='$_REQUEST[tax]',
				paytype='$_REQUEST[paytype]',
				user_id='$_SESSION[userID]',
				status='S',
				remarks='$_REQUEST[remarks]'
		";	
		mysql_query($query);
		
		$last_id=mysql_insert_id();
		
	}
?>

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    <form name="header_form" id="header_form" action="" method="post">
    <input type="hidden" name="dr_header_id" id="dr_header_id" value="<?=$last_id?>" />
    <div id="messageError">
    	<ul>
        </ul>
    </div>
		<div class='inline'>
        	<div>Date: </div>        
            <div>
            	<input type="text" class="textbox3 required" id='date' name="date" onclick='fPopCalendar("date");' readonly='readonly' value="<?=$_REQUEST['date']?>" >
         	</div>
        </div>    	
        <div class='inline'>
        	<div>Account : </div>        
            <div>
                <?php
					echo $options->getSpecificAccountOptions($_REQUEST[account_id]);
				?> 
            </div>
        </div>   
        
        <div class='inline'>
        	<div>Location: </div>        
            <div>
                <?php
					echo $options->getAllLocationOptions($_REQUEST[locale_id]);
				?> 
         	</div>
        </div>   
                
        <div class='inline'>
        	<div>Pay Type : </div>        
            <div>
			<?php
				echo $options->getPayTypeOptions($_REQUEST[paytype]);
			?>
            </div>
        </div> 
        
        <div>
        	<div>Remarks : </div>        
            <div>
            	<input type='text' name="remarks" id="remarks" class="textbox2" value="<?=$_REQUEST[remarks];?>"/>
          	</div>
        </div> 
        
		<?php
		if(empty($_REQUEST[b])):
		?>
       	<input type="submit" name="b" id="b" value="Submit" />
        <?php
		endif;
		?>        
            
        
        <br />
        <?php
		if(!empty($_REQUEST[b])):
		?>
        <hr />
        <input type="hidden" name="stocktype" id="stocktype" value="" />
                   
        <div style="display:inline-block; margin-right:20px;">
        	<div>Material : </div>        
            <div><?php echo $options->getAllMaterialOptions(NULL,'stock_id'); ?></div>
        </div>  
      
        <div style="display:inline-block; margin-right:20px;">
        	<div>Quantity : </div>        
            <div style="display:inline-block;"><input type="text" size="12" name="quantity" id="quantity" /></div>
        </div> 
        <div style="display:inline-block; margin-right:20px;">
        	<div>SRP : </div>        
            <div><input type="text" size="12" name="srp" id="srp" readonly="readonly"/></div>
        </div>
        <div style="display:inline-block; margin-right:20px;">
        	<div>Discount (%): </div>        
            <div><input type="text" size="12" name="discount_detail" id="discount_detail" /></div>
        </div> 
        <div style="display:inline-block; margin-right:20px;">
        	<div>Price : </div>        
            <div><input type="text" size="12" name="price" id="price" /></div>
        </div> 
        
        <div style="display:inline-block; margin-right:20px;">
        	<div>Amount : </div>        
            <div><input type="text" size="12" name="amount" id="amount" readonly="readonly"/></div>
        </div> 
  		
        <input type="button" name="addButton" id="addButton" value="Add"  />
        
        <div style="color:#F00;" id="currentbalance"></div>
        <?php
		endif;
		?>
    </form>
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="float:left; width:100%; text-align:center;" id="table_container">
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
              <th width="20"><b>#</b></th>
              <th width="20" align="center"></th>
              <th><b>Stock</b></th>
              <th><b>Quantity</b></th>
              <th><b>SRP</b></th>
              <th><b>Price</b></th>
              <th><b>Discount</b></th>
              <th><b>Amount</b></th>
            </tr>   	
            
        </table>
    </div>
   
</div>

<script type="text/javascript" src="scripts/script_delivery.js"></script>
