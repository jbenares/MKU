<style type="text/css">
	.ui-widget-header{
		padding:6px;
		margin-top:0px;
		margin-bottom:0px;
	}
	.ui-widget-header h3{
		padding:0px;
		margin:0px;	
	}
	.ui-widget-content{
		padding:0px;	
	}
	.ui-widget-content ul{
		margin-left:20px;
	}
</style>

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
<?php	
	$b = $_REQUEST['b'];
	$achId	= $_REQUEST['headerid'];
	$user_id = $_SESSION['userID'];	
		
	if($b=="Return"){
	$stock_id	= $_REQUEST['itemid'];
	$dater	= $_REQUEST['date'];
	$qtyr	= $_REQUEST['qtyr'];
	
		# Check if qty out is equal to qty in
		// IN
		$chk_i = "SELECT SUM(quantity) as qtyin FROM asset_circulation_detail WHERE ach_id = '$achId' AND stock_id = '$stock_id' AND status = 'I'";
		$rs_chk_i = mysql_query($chk_i);
		$rw_chk_i = mysql_fetch_assoc($rs_chk_i);
		$qty_in = $rw_chk_i['qtyin'];
		// OUT
		$chk_o = "SELECT SUM(quantity) as qtyout FROM asset_circulation_detail WHERE ach_id = '$achId' AND stock_id = '$stock_id' AND status = 'O'";
		$rs_chk_o = mysql_query($chk_o);
		$rw_chk_o = mysql_fetch_assoc($rs_chk_o);
		$qty_out = $rw_chk_o['qtyout'];
		$total_qtyleft = $qty_in - $qty_out; // Remaining qty left
		// Validation
		if($qtyr > $total_qtyleft){
			$msg = "Total quantity IN is " . $total_qtyleft . '. You have entered more than the total quantity. Returned of item FAILED';
		}else{
		
		$query="
			insert into
				asset_circulation_detail
			set
				ach_id 	= '$achId',
				stock_id 	= '$stock_id',
				quantity	= '$qtyr',
				status = 'O',
				date_returned	= '$dater',
				date_added		= NOW()
		";	
		mysql_query($query);
		$msg = "Item Returned";
		} // End If Validation
	}else{}
	

	$query="
		select
					*
				from
					projects s, employee e, asset_circulation_header ac, productmaster p, asset_circulation_detail ad
				where
					(p.stock like '%$keyword%') AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id AND ac.is_deleted != '1'
				AND
					ac.ach_id = ad.ach_id AND ad.stock_id = p.stock_id AND ad.status = 'I' AND ac.ach_id = '$achId'
				order by 
					ac.ach_id DESC
	";
	
	$result=mysql_query($query);
	
	$queryopt="
		select
					*
				from
					projects s, employee e, asset_circulation_header ac, productmaster p, asset_circulation_detail ad
				where
					(p.stock like '%$keyword%') AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id AND ac.is_deleted != '1'
				AND
					ac.ach_id = ad.ach_id AND ad.stock_id = p.stock_id AND ad.status = 'I' AND ac.ach_id = '$achId'
				group by
					ad.stock_id
				order by 
					ac.ach_id DESC
	";
	
?>
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>RETURN ITEM</div>    
    
   	<div style="width:50%; float:left;">
	<form name="header_form" id="header_form" action="" method="post">
        <div class="module_actions">
            <input type="hidden" name="headerid" id="headerid" value="<?=$achId?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>
            <div class="inline">
                Date Return: <br />
                <input type="text" name="date" class="textbox3 datepicker" required  />
            </div>
            
            <div class='inline'>
                Item Name : <br />  
				<select name="itemid">
                <?php
					$res = mysql_query($queryopt);
					while($row = mysql_fetch_assoc($res))
					{
							$ach_id	= $row['ach_id'];							
							$stock_id = $row['stock_id'];
							# Check if qty out is equal to qty in
							// IN
							$chk_i = "SELECT SUM(quantity) as qtyin FROM asset_circulation_detail WHERE ach_id = '$ach_id' AND stock_id = '$stock_id' AND status = 'I'";
							$rs_chk_i = mysql_query($chk_i);
							$rw_chk_i = mysql_fetch_assoc($rs_chk_i);
							$qty_in = $rw_chk_i['qtyin'];
							// OUT
							$chk_o = "SELECT SUM(quantity) as qtyout FROM asset_circulation_detail WHERE ach_id = '$ach_id' AND stock_id = '$stock_id' AND status = 'O'";
							$rs_chk_o = mysql_query($chk_o);
							$rw_chk_o = mysql_fetch_assoc($rs_chk_o);
							$qty_out = $rw_chk_o['qtyout'];
							if($qty_in == $qty_out) # Do not display if there are no IN items
							{}else{								
							$total_qtyleft = $qty_in - $qty_out;
				?>
								<option value="<?php echo $row['stock_id']; ?>"><?php echo $row['stock']; ?> - <?php echo $total_qtyleft; ?>qty</option>
				<?php
							} // End If
					} // End While
				?>
				</select>
            </div>
			
			<div class="inline">
                Quantity Returned: <br />
                <input type="text" name="qtyr" id="qtyr" required  />
            </div>
			
            <br />
        </div>
        <div class="module_actions">    
			<input type="button" name="b" value="Go Back" onclick="window.location.href='admin.php?view=6607e797fc74266fa964'" class="buttons" />
			<?php 
				if($qty_in == $qty_out) # Do not display if there are no IN items
				{}else{
			?>
					&nbsp; <input type="submit" name="b" id="b" value="Return" />
			<?php
				}
			?>			
        </div>
    </form>   
		<div id="accordion">
			<h3><a href="#">LIST OF IN ITEMS</a></h3>
			<div>				
				<table cellspacing="2" cellpadding="5" width="100%" align="left" class="search_table">
					<tr bgcolor="#C0C0C0">				
					  <td width="20"><b>#</b></td>
					  <td>Item Name</td>
					  <td>Quantity</td>
					  <td>Status</td>		 
					  <td>Date IN</td>
					  <td>From Project</td>
					  <td>To Project</td>		  
					 </tr>        
					<?php					
						$i=1;			
						while($r=mysql_fetch_assoc($result)) {
							$ach_id		= $r['ach_id'];
							$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
							$from_project_name		= $r['project_name'];								
							$to_project_id		= $r['to_project_id'];
							$stock_name		= $r['stock'];
							$stock_id		= $r['stock_id'];
							$quantity		= $r['quantity'];
							$datereceived = date("M d, Y",strtotime($r['date_received']));
							
							$pto = "SELECT * FROM projects WHERE project_id = '$to_project_id'";
							$rs_pto = mysql_query($pto);
							$num_pto = mysql_num_rows($rs_pto);
							if($num_pto > 0)
							{
								$rw_pto = mysql_fetch_assoc($rs_pto);
								$to_project_name = $rw_pto['project_name'];
							}else{
								$to_project_name = '--';
							}
							
							# Check if qty out is equal to qty in
							// IN
							$chk_i = "SELECT SUM(quantity) as qtyin FROM asset_circulation_detail WHERE ach_id = '$ach_id' AND stock_id = '$stock_id' AND status = 'I'";
							$rs_chk_i = mysql_query($chk_i);
							$rw_chk_i = mysql_fetch_assoc($rs_chk_i);
							$qty_in = $rw_chk_i['qtyin'];
							// OUT
							$chk_o = "SELECT SUM(quantity) as qtyout FROM asset_circulation_detail WHERE ach_id = '$ach_id' AND stock_id = '$stock_id' AND status = 'O'";
							$rs_chk_o = mysql_query($chk_o);
							$rw_chk_o = mysql_fetch_assoc($rs_chk_o);
							$qty_out = $rw_chk_o['qtyout'];
														
							//if($qty_in == $qty_out) # Do not display if there are no IN items
							//{}else{
							
							echo '<tr bgcolor="'.$transac->row_color($i).'">';
					?>
								<td width="20"><?=$i++?></td>
								<td><?=$stock_name?></td>
								<td><?=$quantity?></td>
								<td>IN</td>
								<td><?=$datereceived?></td>
								<td><?=$from_project_name?></td>
								<td><?=$to_project_name?></td>
							</tr>
					<?php
							//} // End If
						} // End While
					?>
				</table>
			</div>			
		</div>
	</div>
		
		<div style="float:right;width:50%;" >
			<div class="accordion">
				<div class="ui-widget-header head">
                <h3><img src="images/cart.png" style="margin-right:15px;" /> LIST OF OUT ITEMS</h3>
            </div>
            <div style="width:100%; overflow:auto;" >
			<form name="_form" action="" method="post">
				<input type="text" name="keyword" class="textbox" />
				<input type="submit" name="search" value="Search Item" class="buttons" />
			</form>
                <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
                    <tr bgcolor="#C0C0C0">				
					  <td width="20"><b>#</b></td>
					  <td>Item Name</td>
					  <td>Quantity</td>
					  <td>Status</td>		 
					  <td>Date OUT</td>
					  <td>From Project</td>
					  <td>To Project</td>		  
					</tr>
                   <?php
						$search = $_REQUEST['search'];
						if($search=="Search Item")
						{
							$keyword = $_REQUEST['keyword'];
						}else{
							$keyword = '';
						}
						$i=1;	
						$queryo="
							select
										*
									from
										projects s, employee e, asset_circulation_header ac, productmaster p, asset_circulation_detail ad
									where
										(p.stock like '%$keyword%') AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id AND ac.is_deleted != '1'
									AND
										ac.ach_id = ad.ach_id AND ad.stock_id = p.stock_id AND ad.status = 'O' AND ac.ach_id = '$achId'
									order by 
										ac.ach_id DESC
						";
						
						$rs_queryo=mysql_query($queryo);
						while($rw_queryo=mysql_fetch_assoc($rs_queryo)) {
							$ach_id		= $rw_queryo['ach_id'];
							$employee = $rw_queryo['employee_lname'] . ',&nbsp;' . $rw_queryo['employee_fname'];
							$from_project_name		= $rw_queryo['project_name'];								
							$to_project_id		= $rw_queryo['to_project_id'];
							$stock_name		= $rw_queryo['stock'];
							$stock_id		= $rw_queryo['stock_id'];
							$quantity		= $rw_queryo['quantity'];
							$datereturned = date("M d, Y",strtotime($rw_queryo['date_returned']));
							
							$pto = "SELECT * FROM projects WHERE project_id = '$to_project_id'";
							$rs_pto = mysql_query($pto);
							$num_pto = mysql_num_rows($rs_pto);
							if($num_pto > 0)
							{
								$rw_pto = mysql_fetch_assoc($rs_pto);
								$to_project_name = $rw_pto['project_name'];
							}else{
								$to_project_name = '--';
							}
							
							
							echo '<tr bgcolor="'.$transac->row_color($i).'">';
					?>
								<td width="20"><?=$i++?></td>
								<td><?=$stock_name?></td>
								<td><?=$quantity?></td>
								<td>OUT</td>
								<td><?=$datereturned?></td>								
								<td><?=$to_project_name?></td>
								<td><?=$from_project_name?></td>
							</tr>
					<?php
							
						} // End While
					?>
                </table> 
           	</div>
			</div>
		</div>    
</div>
<script type="text/javascript">
j(function(){
	
	<?php
		if($b == "Add Details"){
			$active_state = 0;	
		}else if($b == "Add Service Details"){
			$active_state = 1;
		}else if($b == "Add Equipment Details"){
			$active_state = 2;	
		}else{
			$active_state = 3;	
		}
	?>
	
	j("#accordion").accordion({active : <?=$active_state?> , collapsible : true, autoHeight: false});
	
	j('.accordion .head').click(function() {
		j(this).next().toggle('slow');
		return false;
	});
		
	j("#work_category_id").change(function(){
		xajax_display_subworkcategory(this.value);
	});
	
	<?php
	if(!empty($status)){
	?>
		xajax_display_subworkcategory('<?=$work_category_id?>','<?=$sub_work_category_id?>');
	<?php
	}
	?>
});

</script>
	