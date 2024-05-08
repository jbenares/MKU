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
		
		$date=explode("/",$_REQUEST['date']);
		$date="$date[2]-$date[0]-$date[1]";		

		$query="
			insert into
				stockcard
			set
				date='$date',
				typeoftransaction='$_REQUEST[typeoftransaction]',
				qtyin='$_REQUEST[qtyin]',
				qtyout='$_REQUEST[qtyout]',
				balance='$_REQUEST[balance]'
		";	
		
		mysql_query($query) or die(mysql_error());		
	}
?>

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    <form name="header_form" id="header_form" action="" method="post">
    <div id="messageError">
    	<ul>
        </ul>
    </div>
    <div class='inline'>
        <div>Date: </div>        
        <div>
            <input type="text" name="date" id="date" class="textbox3 required" readonly="readonly"  title="Please Enter Date"/>
                <script language='JavaScript'>
                    new tcal ({
                        // form name
                        'formname': 'header_form',
                        // input name
                        'controlname': 'date'
                    });
                </script>			
        </div>
    </div>    
    <div class='inline'>
        <div>Type of Transaction : </div>        
        <div>
		<?php
            echo $options->getTypeOfTransactionOptions();
        ?> 
            
        </div>
    </div>   
    
    <div class='inline'>
        <div>Quantity In: </div>        
        <div>
			<input type="text" name="qtyin" id='qtyin' class="textbox3" />
        </div>
    </div>   
    
    <div class='inline'>
        <div>Quantity Out: </div>        
        <div>
			<input type="text" name="qtyout" id='qtyout' class="textbox3" />
        </div>
    </div> 
    
    <div class='inline'>
        <div>Balance: </div>        
        <div>
			<input type="text" name="balance" id='balance' class="textbox3" />
        </div>
    </div>   
    
    <input type="submit" name="b" id="b" value="Submit" />
                     
   <!--<div class='inline'>
        <div>Status : </div>        
        <div>
            <input type="text" readonly="readonly" value="<?=$options->getTransactionStatusName($r[status]);?>" />
        </div>
    </div> -->
        
    </form>
</div>

<script type="text/javascript">
j("#header_form").validate({
	errorContainer: "#messageError",
	errorLabelContainer: "#messageError ul",
	wrapper: "li"
});
</script>
