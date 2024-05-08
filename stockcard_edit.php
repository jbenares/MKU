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
	$stockcard_id=$_REQUEST[stockcard_id];


	if($_REQUEST[b]=="Submit"){
			
		$date=explode("/",$_REQUEST['date']);
		$date="$date[2]-$date[0]-$date[1]";		
					
		$query="
			update
				stockcard
			set
				date='$date',
				typeoftransaction='$_REQUEST[typeoftransaction]',
				qtyin='$_REQUEST[qtyin]',
				qtyout='$_REQUEST[qtyout]',
				balance='$_REQUEST[balance]'
			where
				stockcard_id='$_REQUEST[stockcard_id]'
		";	
		
		mysql_query($query);
		
	}
	
	$query="
		select
			*
		from
			stockcard
		where
			stockcard_id='$stockcard_id'
	";
	
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$date=$r['date'];
	$date=explode("-",$date);
	$date="$date[1]/$date[2]/$date[0]";	
?>

<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'>EDIT STOCK CARD</div>
    <div class="module_actions">
        <form name="header_form" id="header_form" action="" method="post">
            <input type="hidden" name="stockcard_id" id="stockcard_id" value="<?=$r[stockcard_id]?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <div class='inline'>
                <div>Date: </div>        
                <div>
                    <input type="text" name="date" id="date" class="textbox3 required" readonly="readonly" value="<?=$date?>" title="Please Enter Date"/>
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
                    echo $options->getTypeOfTransactionOptions($r[typeoftransaction]);
                ?> 
                    
                </div>
            </div>   
            
            <div class='inline'>
                <div>Quantity In: </div>        
                <div>
                    <input type="text" name="qtyin" id='qtyin' class="textbox3" value="<?=$r[qtyin]?>" />
                </div>
            </div>   
            
            <div class='inline'>
                <div>Quantity Out: </div>        
                <div>
                    <input type="text" name="qtyout" id='qtyout' class="textbox3" value="<?=$r[qtyout]?>" />
                </div>
            </div> 
            
            <div class='inline'>
                <div>Balance: </div>        
                <div>
                    <input type="text" name="balance" id='balance' class="textbox3" value="<?=$r[balance]?>" />
                </div>
            </div>   
            
            <input type="submit" name="b" id="b" value="Submit" />
        </form>
    </div>
</div>