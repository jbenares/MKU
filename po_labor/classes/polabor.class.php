<?php
function add_payroll($id,$payroll_id){
		$objResponse = new xajaxResponse();	
		$options	 = new options();
			
		
	/*$sql = "select
			  *
		from
			  po_header as h, supplier as s, projects p
		where
			h.supplier_id = s.account_id
		and
			h.po_type = 'L'
		and
			p.project_id = h.project_id
		and
			h.po_header_id='$id'
	";
	$rs = mysql_query($sql);
	$rw = mysql_fetch_assoc($rs);
	extract($rw);
			
	$content = "
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				PO # : <br>
				$id
			</div>
			
			<div class='form-div'>
				Project : <br>
				$project_name
			</div>
			
			<div class='form-div'>
				Supplier: <br>
				$account
			</div>
			
			<div class='form-div'>
				From : <br/>
				".$options->monthsList()." - ".$options->daysList()." - ".$options->yearList()."
				<br />To : <br />
				".$options->monthsList2()." - ".$options->daysList2()." - ".$options->yearList2()."
			</div>
			
			<div class='form-div'>
				Payroll Amount : <br>
				<input type='text' class='textbox' name='payroll' id='payroll' value='' />
			</div>
			
			<div class='form-div'>
				<input type='hidden' name='po_header_id' value='$id' >
				<input type='hidden' name='payroll_id' value='$payroll_id' >
				<input type='button' class='button' name='submit' value='Submit' onclick=xajax_add_payrollform(xajax.getFormValues('dialog_form')); >
			</div>
		</div>		
	";
	<div class='inline'>
				From : <br />
				<input type=\"text\" name=\"from\" id=\"date\" title=\"Please Enter Date\" /><br />
				To : <br />
				<input type=\"text\" name=\"to\" id=\"date\" title=\"Please Enter Date\" />
	</div>
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PAYROLL' );");	
	$objResponse->script('openDialog();');
	*/
	$objResponse->redirect('admin.php?view=e57f21aa474aa28b4753&po_header_id='.$id.'&payroll_id='.$payroll_id);
	return $objResponse;
}


function add_payrollform($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$po_header_id		= $form_data['po_header_id'];
	$payroll_header_id	= $form_data['payroll_id'];	
	$amt				= $form_data['payroll'];
	$from				 = $form_data['year']."-".$form_data['month']."-".$form_data['day'];
	$to					 = $form_data['year2']."-".$form_data['month2']."-".$form_data['day2'];
	
	//get latest payroll_det
	/*$sel = mysql_query("select * from po_header_payroll_det where po_header_id='$po_header_id'");
	$fe=mysql_fetch_assoc($sel);
	$payroll_det = $fe['payroll_det'];
	*/
	//sum up
	//$amt +=$payroll_det;
	
	mysql_query("
		insert into
			po_header_payroll
		set	
			payroll_header_id = '$payroll_header_id',
			amount	= '$amt',
			date_from = '$from',
			date_to = '$to'
	") or $objResponse->alert(mysql_error());
	
	
		$objResponse->alert("Successfully Added.");	
		$objResponse->redirect("admin.php?view=f374ef0a421b1630d56e");
	
	return $objResponse;
}

?>