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
.search_table tr td input[type="text"]
{
	color:#5e6977;
	background:none;
	border:none;	
	font-size:11px;
	text-align:right;
	width:100%;
}
label.error { float: none; display:block; color: red; padding-left: .5em; vertical-align: top; }
.search_table tr:hover td{
	background-color:#B5E2FE;
}
.alignLeft
{
	text-align:left;	
}
#messageError{
	padding:0px 5px;
}

#messageError ul{
	list-style:square;	
	margin-left:20px;
}
</style>

<?php
	
	$id=$_REQUEST[po_header_id];
	$payroll_id=$_REQUEST[payroll_id];
	$amount = $_REQUEST[amount];
	$overtime = $_REQUEST[overtime];
	$date	  = $_REQUEST[date];
	$project_id = $_REQUEST[project_id];
	$supplier_id  = $_REQUEST[supplier_id];
	$payroll_header_details = $_REQUEST[payroll_header_details];
	
	if($_REQUEST[b]=="Submit"){
		/*Insert into Header*/	
		
		$query="
			insert into
				po_header_payroll
			set
				payroll_header_id = '$payroll_id',
				overtime = '$_REQUEST[overtime]',
				amount = '$_REQUEST[payroll]',
				date_from = '$_REQUEST[date_from]',
				date_to = '$_REQUEST[date_to]'
		";
		mysql_query($query) or die(mysql_error());
		
		header('location: admin.php?view='.$view.'&po_header_id='.$id.'&payroll_id='.$payroll_id.'&msg=Payroll Added');
	}else if($_REQUEST[b]=="Update"){
		$query="
			update
				po_header_payroll
			set
				amount = '$_REQUEST[payroll]',
				overtime = '$_REQUEST[overtime]',
				date_from = '$_REQUEST[date_from]',
				date_to = '$_REQUEST[date_to]'
			where
				payroll_header_details='$_REQUEST[payroll_header_details]'
		";	
		mysql_query($query) or die(mysql_error());
		
		header('location: admin.php?view='.$view.'&po_header_id='.$id.'&payroll_id='.$payroll_id.'&msg=Payroll Updated');
	}
	
	if($_REQUEST[act]=="Update" && !isset($_REQUEST[b])){
			$sql="select * from po_header_payroll where payroll_header_details='$payroll_header_details'";
			$q=mysql_query($sql);
			$ra=mysql_fetch_assoc($q);
			
			$date_from = $ra[date_from];
			$date_to = $ra[date_to];
			$amount = $ra[amount];
			$overtime = $ra[overtime];
	}else if($_REQUEST[act]=="Delete" && !isset($_REQUEST[b])){
		mysql_query("delete from po_header_payroll where payroll_header_details='$_REQUEST[payroll_header_details]'");
		
		header('location: admin.php?view='.$view.'&po_header_id='.$id.'&payroll_id='.$payroll_id.'&msg=Payroll Deleted');
	}
					$sql="
						select
							  *
						from
							  po_header as h, supplier as s, projects p
						where
							h.supplier_id = s.account_id
						and
							(h.po_type = 'L' or h.po_type = 'S')
						and
							p.project_id = h.project_id
						and
							h.po_header_id='$id'
						";
		$rs = mysql_query($sql);
		$rw = mysql_fetch_assoc($rs);
		$project_name = $rw[project_name];
		$account = $rw[account];
		
		/*$project_name	= $options->attr_Project($project_id,'project_name');
		$supplier_name	= (!empty($supplier_id))?$options->getSupplierName($supplier_id):"";*/
		
	/*if($_REQUEST[b]=="Submit"){
		
		$id = $_REQUEST[id];
	}else{
		$id=$_REQUEST[id];
		$payroll_id=$_REQUEST[payroll_id];
	}*/
?>

<div class=form_layout>
	<?php if(isset($_REQUEST[msg])) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$_REQUEST[msg].'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>ADD PAYROLL DETAILS</div>
    <div class="module_actions">
    <form name="header_form" id="header_form" action="" method="post">
    <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$id;?>" />
	<input type="hidden" name="payroll_id" id="payroll_id" value="<?=$payroll_id;?>" />
    <div id="messageError">
    	<ul>
        </ul>
    </div>
    	<?php
		if($id):
			$id_pad=str_pad($id,8,"0",STR_PAD_LEFT);
		?>
            <div class="inline">
                PO #: <br />
                <input type="text" class="textbox3" value="<?=$id_pad?>" readonly=readonly/>
            </div>
        <?php
		endif;
		?>
		<div class='inline'>
        	<div>Project : </div>        
            <div>
               <input type="text" class="textbox" value="<?=$project_name?>" readonly=readonly />
            </div>
        </div>  
		
		<div class='inline'>
        	<div>Supplier: </div>        
            <div>
                <input type="text" class="textbox" value="<?=$account?>" readonly=readonly />
         	</div>
        </div> 
		<br/>
		<br/>
		<div class='inline'>
        	<div>From Date: </div>        
            <div>
            	<input type="text" class="required textbox3 datepicker" title="Please enter date" id='date' name="date_from" value="<?=$date_from?>" readonly='readonly' >
         	</div>
        </div>
		<div class='inline'>
        	<div>To Date: </div>        
            <div>
            	<input type="text" class="required textbox3 datepicker" title="Please enter date" id='date1' name="date_to" value="<?=$date_to?>" readonly='readonly' >
         	</div>
        </div>    	  
        <br/>
		<br/>		
        <div class='inline'>
        	<div>Payroll Amount : </div>        
            <div>
				<input type='text' class='textbox3' name='payroll' id='payroll' value="<?=$amount?>" />
            </div>
        </div>

		<div class='inline'>
        	<div>Overtime Amount : </div>        
            <div>
				<input type='text' class='textbox3' name='overtime' id='overtime' value="<?=$overtime?>" />
            </div>
        </div>
		<?phpif($_REQUEST[act]!="Update"){?>
			<input type="submit" name="b" id="b" value="Submit" />
		<?php}else{?>
			<input type="submit" name="b" id="b" value="Update" />
		<?php}?>
		 <?php if($b=="Print Preview"){ ?>	
            <input type="button" value="Print" onclick="printIframe('JOframe');" />
        <?php } ?>
    </div>
    
    <div style="float:left; width:100%; text-align:center;" id="table_container">
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table" id="search_table">
            <tr bgcolor="#C0C0C0">				
              <th width="20"><b>#</b></th>
              <th width="50" align="center"></th>
              <th><b>From</b></th>
              <th><b>To</b></th>
			  <th><b>Overtime Amount</b></th>
			  <th><b>Payroll Amount</b></th>
            </tr>   	
		<?php
			$query=mysql_query("SELECT 
									* 
								FROM
									po_header_payroll as d
								WHERE
									d.payroll_header_id = '$payroll_id'
								");
			$i=1;
			while($r=mysql_fetch_assoc($query)){
				extract($r);
				echo '<tr>';
				echo '<td style="text-align:center;" width="20">'.$i++.'</td>';
				echo '<td style="text-align:center;" width="50">
						<a  href="admin.php?view='.$view.'&act=Update&payroll_header_details='.$payroll_header_details.'&po_header_id='.$id.'&payroll_id='.$payroll_id.'" ><img src="images/edit.gif" /></a>
						<a  href="admin.php?view='.$view.'&act=Delete&payroll_header_details='.$payroll_header_details.'&po_header_id='.$id.'&payroll_id='.$payroll_id.'" onclick="return approve_confirm();"><img src="images/trash.gif" /></a>
					  </td>';
				echo '<td style="text-align:center;">'.date("F d,Y",strtotime($date_from)).'</td>';
				echo '<td style="text-align:center;">'.date("F d,Y",strtotime($date_to)).'</td>';
				echo '<td style="text-align:center;">P '.number_format($overtime,2).'</td>';
				echo '<td style="text-align:center;">P '.number_format($amount,2).'</td>';
				echo '</tr>';
			}
		?>
		</table>
    </div>
    </form>
</div>
