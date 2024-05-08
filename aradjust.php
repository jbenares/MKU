<script type="text/javascript" src="scripts/jquery.validate.js"></script>
<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
.search_table tr:hover td{
	background-color:#B5E2FE;
}
.search_table tr:nth-child(even){
	background-color:#FFFFCC;	
}
.search_table tr:nth-child(odd){
	background-color:#EEE8AA;	
}
.search_table tr:nth-child(1){
	background-color:#C0C0C0;	
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

.header-table
{
	width:100%;
}
</style>

<?php

	$b						= $_REQUEST['b'];
	$aradjust_header_id 	=$_REQUEST['aradjust_header_id'];
	
	$date					= $_REQUEST['date'];
	$remarks				= $_REQUEST['remarks'];
	$user_id				= $_SESSION['userID'];
	$username				= $options->getUserName($user_id);
	
	$remarks_detail			= $_REQUEST['remarks_detail'];
	$amount					= $_REQUEST['amount'];
	
	$aradjust_detail_ids	= $_REQUEST['aradjust_detail_id'];
	$datetoday				= date("Y-m-d H:i:s");
	
	$details_display=FALSE;
	//$status="S";
	if($b=="Submit"){
		$details_display=TRUE;
		$status="S";
		
		$audit="Added by: $username on $datetoday, ";
		
		mysql_query("
			insert into
				aradjust_header
			set
				date='$date',
				remarks='$remarks',
				audit='$audit',
				status='$status',
				user_id='$user_id'
		") or die(mysql_error());	
		
		$aradjust_header_id=mysql_insert_id();

	}else if($b=="Update"){
		$details_display=TRUE;
		$status="S";
		
		$audit=$options->getDataFromARAdjust($aradjust_header_id,"audit");
		$audit.="Edit by: $username on $datetoday, ";
			
		mysql_query("
			update
				aradjust_header
			set
				date='$date',
				remarks='$remarks',
				audit='$audit',
				status='$status',
				user_id='$user_id'
			where
				aradjust_header_id='$aradjust_header_id'
		") or die(mysql_error());	
		
	}else if($b=="Finish"){
		$details_display=TRUE;
		$finish = TRUE;
		$status="F";
		
		$audit=$options->getDataFromARAdjust($aradjust_header_id,"audit");
		$audit.="Edit by: $username on $datetoday, ";
			
		mysql_query("
			update
				aradjust_header
			set
				date='$date',
				remarks='$remarks',
				audit='$audit',
				status='$status',
				user_id='$user_id'
			where
				aradjust_header_id='$aradjust_header_id'
		") or die(mysql_error());	
		
		
		$journal_id=$options->getJournalID("JV");
		$generalreference=$options->generateJournalReference($journal_id);
		
		$result=mysql_query("
			insert into
				gltran_header
			set
				date='$date',
				generalreference='$generalreference',
				journal_id='$journal_id',
				status='S'
		") or die(mysql_error());	
		
		$last_id=mysql_insert_id();
		$options->insertToGLARAdjust($last_id,$aradjust_header_id);
		
	}else if($b=="Add"){
		$status="S";
		$details_display=TRUE;
		
		mysql_query("
			insert into
				aradjust_details
			set
				aradjust_header_id='$aradjust_header_id',
				amount='$amount',
				remarks='$remarks_detail'
		") or die(mysql_error());
	
	}else if($b=="Delete"){
		$details_display=TRUE;
		$status="S";
		if($aradjust_detail_ids){
			foreach($aradjust_detail_ids as $id){
				mysql_query("
					delete from
						aradjust_details
					where
						aradjust_detail_id='$id'
				") or die(mysql_error());
			}
		}
		
	}
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
        
        <input type="hidden" name="aradjust_header_id" value="<?=$aradjust_header_id;?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        <div class="inline">
            Date<br />
            <input type="text" class="required textbox3" title="Please enter date" id='date' name="date" onclick=fPopCalendar("date"); readonly='readonly'  value="<?=$date?>">
        </div>    
        <?php if($status){ ?>
        <div class="inline">
        	Status<br />
			<input type="text" class="textbox3" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly" />
        </div>
        <?php } ?>
        <br />
        <div>
            Remarks<br />
            <input type="text" class="textbox2" name="remarks" value="<?=$remarks?>" />
        </div>       
                              
   	</div>
    <div class="module_actions">
        <?php if(empty($status) || $status=="S"){ ?>
			<?php
            if(empty($status)){
            ?>
                <input type="submit" name="b" class="buttons" value="Submit" />
            <?php }else{ ?>
                <input type="submit" name="b" class="buttons" value="Update" />
                <input type="submit" name="b" class="buttons" value="Finish" />
            <?php } ?>
      	<?php } ?>
  	</div>
	<?php
    if($details_display && $status=="S"):
    ?>
 
    <div style="background-color:#000; color:#FFF; font-weight:bolder; padding:5px; margin:5px 0px; text-align:left;">	
        AR ADJUSTMENT ENTRY DETAILS
    </div>
    <div style="background:#fff; text-align:left;">
      	<div class="inline">
            Amount:<br />
            <input type="text" class="textbox3" name="amount" />
        </div>
        <div class="inline">
            Remarks:<br />
            <input type="text" class="textbox4" name="remarks_detail" />
        </div>
        <input type="submit" name="b" value="Add"  class="buttons" />
        <input type="submit" name="b" value="Delete" class="buttons" />
    </div><!--End of Transaction Details-->
	<?php
    endif;
    ?>
    	
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="float:left; z-index:10000; width:100%; text-align:center;" id="table_container" >
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
              <th width="20"><b>#</b></th>
              <th width="20" align="center"></th>
              <th>Amount</th>
              <th>Remarks</th>
            </tr>   	
            
            <?php
            $result=mysql_query("
				select
					*
				from
					aradjust_details
				where
					aradjust_header_id='$aradjust_header_id'
			") or die(mysql_error());
			$i=1;
			while($r=mysql_fetch_assoc($result)){
				$aradjust_detail_id		= $r[aradjust_detail_id];
				$amount					= $r[amount];
				$remarks				= $r[remarks];
			?>
            <tr>
            	<td><?=$i++?></td>
                <td><input type="checkbox" name="aradjust_detail_id[]" value="<?=$aradjust_detail_id?>"  /></td>
                <td style="text-align:right;"><?=number_format($amount,2,'.',',')?></td>
                <td><?=$remarks?></td>
            	
            </tr>
            <?php } ?>
        </table>
    </div>
   
</div>
</form>
