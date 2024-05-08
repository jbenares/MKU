<?php
	require_once 'reader.php';	
	$data = new Spreadsheet_Excel_Reader();	
	$data->setOutputEncoding('CP1251');	

	$b = $_REQUEST['b'];
	
	if($b=='Load DPRC Application') {	
		// Start Header Load
	
		//$xls_filename = "LoanMaster.xls";			
		$data->read("My_Uploads/Excel/$xls_filename");
			
		error_reporting(E_ALL ^ E_NOTICE);

		// $data->sheets[0]['cells'][a][b]; a -> Rows, b -> Cols
		//echo $data->sheets[0]['cells'][2][2];
			
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
			//}
			//echo "<br>";
								
			$id				= $data->sheets[0]['cells'][$i][2];				
			$customer_id 			= $data->sheets[0]['cells'][$i][1];				
			$application_date 		= extophp($data->sheets[0]['cells'][$i][3]);
			$resno		 		= $data->sheets[0]['cells'][$i][4];	
			$pkgcode 			= $data->sheets[0]['cells'][$i][5];
			$model_id 			= $data->sheets[0]['cells'][$i][7];
			$alot				= $data->sheets[0]['cells'][$i][8];
			$afloor			= $data->sheets[0]['cells'][$i][9];
			$phase				= $data->sheets[0]['cells'][$i][10];
			$blkno				= $data->sheets[0]['cells'][$i][11];
			$lotno				= $data->sheets[0]['cells'][$i][12];
			$payment_code			= $data->sheets[0]['cells'][$i][13];
			$dp_code			= $data->sheets[0]['cells'][$i][14];
			$loan_value			= $data->sheets[0]['cells'][$i][15];
			$dp_percent			= $data->sheets[0]['cells'][$i][16];
			$dp_disc_rate			= $data->sheets[0]['cells'][$i][29];
			$dp_amount			= $data->sheets[0]['cells'][$i][20];
			$dp_period			= $data->sheets[0]['cells'][$i][21];
			$net_loan			= $data->sheets[0]['cells'][$i][22];
			$date_due			= $data->sheets[0]['cells'][$i][23];
			$int_rate			= ($data->sheets[0]['cells'][$i][24])*100;
			$penalized			= $data->sheets[0]['cells'][$i][25];
			$pen_per_day			= $data->sheets[0]['cells'][$i][26];
			$amort				= $data->sheets[0]['cells'][$i][27];
			$approve_date			= extophp($data->sheets[0]['cells'][$i][28]);
			$cancel_date			= extophp($data->sheets[0]['cells'][$i][29]);
			$grace				= $data->sheets[0]['cells'][$i][30];
			$term				= $data->sheets[0]['cells'][$i][31];
			$outbal			= $data->sheets[0]['cells'][$i][35];
			$datebeg			= extophp($data->sheets[0]['cells'][$i][38]);
			$datecut			= extophp($data->sheets[0]['cells'][$i][43]);

			if($application_date=='1970-01-01') $application_date = "";
			if($approve_date=='1970-01-01') $approve_date = "";
			if($cancel_date=='1970-01-01') $cancel_date = "";
			if($datebeg=='1970-01-01') $datebeg = "";
			if($datecut=='1970-01-01') $datecut = "";			

			$sql = "insert into application set
						application_id='$id',	
						customer_id='$customer_id',					
						reservation_no='$resno',
						subd_id='1',
						model_id='$model_id',
						phase='$phase',
						block='$blkno',
						lot='$lotno',
						lot_area='$alot',
						floor_area='$afloor',
						payment_code='$payment_code',
						dp_code='$dp_code',
						loan_value='$loan_value',
						dp_percent='$dp_percent',
						loan_term='$term',
						disc_rate='$dp_disc_rate',
						interest_rate='$int_rate',
						dp_amount='$dp_amount',
						dp_period='$dp_period',
						outstanding_balance='$outbal',
						application_date='$application_date',
						net_loan='$net_loan',
						amortization='$amort',	
						date_due='$date_due',
						penalized='$penalized',
						penalty_per_day='$pen_per_day',
						grace_period='$grace',
						date_approved='$approve_date',
						date_cancelled='$cancel_date',
						package_type_id='$pkgcode',
						datebeg='$datebeg',
						datecut='$datecut'";

			echo "<p>".$sql."</p>";

			mysql_query($sql);

			echo mysql_error();
				
		}

		// End Header Load			
	}
	else if($b=='Load Inventory') {	
		// Start Header Load
	
		//$xls_filename = "Inventory.xls";				
		$data->read("My_Uploads/Excel/$xls_filename");
			
		error_reporting(E_ALL ^ E_NOTICE);

		// $data->sheets[0]['cells'][a][b]; a -> Rows, b -> Cols
		//echo $data->sheets[0]['cells'][2][2];
			
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
			//}
			//echo "<br>";
								
			$id				= $data->sheets[0]['cells'][$i][1];				
			$model 			= $data->sheets[0]['cells'][$i][3];				
			$inv_phase 			= $data->sheets[0]['cells'][$i][4];
			$inv_block	 		= $data->sheets[0]['cells'][$i][5];	
			$inv_lot 			= $data->sheets[0]['cells'][$i][6];
			$alot				= $data->sheets[0]['cells'][$i][7];
			$afloor			= $data->sheets[0]['cells'][$i][8];
			$application_id		= $data->sheets[0]['cells'][$i][10];	

			$sql = "insert into dprc_inventory set
						inv_id='$id',
						subd_id='1',	
						model_id='$model',					
						inv_phase='$inv_phase',
						inv_block='$inv_block',
						inv_lot='$inv_lot',
						inv_lot_area='$alot',
						inv_floor_area='$afloor',
						application_id='$application_id'";

			echo "<p>".$sql."</p>";

			mysql_query($sql);

			echo mysql_error();
				
		}

		// End Header Load			
	}
	else if($b=='Load DPRC Payments') {	
		//$xls_filename = "LoanPayment.xls";				
		$data->read("My_Uploads/Excel/$xls_filename");
			
		error_reporting(E_ALL ^ E_NOTICE);
			
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
			//}
			//echo "<br>";
								
			$id				= $data->sheets[0]['cells'][$i][1];								
			$application_id 		= $data->sheets[0]['cells'][$i][2];
			$or_date	 		= extophp($data->sheets[0]['cells'][$i][3]);	
			$or_number			= $data->sheets[0]['cells'][$i][4];
			$pmode 			= $data->sheets[0]['cells'][$i][5];
			$check_number			= $data->sheets[0]['cells'][$i][6];
			$amount			= $data->sheets[0]['cells'][$i][7];
			$edate				= extophp($data->sheets[0]['cells'][$i][8]);
			$pcode				= $data->sheets[0]['cells'][$i][9];
			$penalize			= $data->sheets[0]['cells'][$i][11];
			$remarks			= addslashes($data->sheets[0]['cells'][$i][17]);	

			$sql = "insert into dprc_payment set
						dprc_payment_id='$id',
						application_id='$application_id',	
						or_date='$or_date',					
						postcode='$pcode',
						pay_mode='$pmode',
						date_encoded='$edate',
						payment_amount='$amount',
						or_no='$or_number',
						penalize='$penalize',
						check_no='$check_number',
						remarks='$remarks'";

			echo "<p>".$sql."</p>";

			mysql_query($sql);

			echo mysql_error();
				
		}

		// End Header Load			
	}
	else if($b=='Load DPRC Ledger') {	
		//$xls_filename = "LoanLedger.xls";				
		$data->read("My_Uploads/Excel/$xls_filename");
			
		error_reporting(E_ALL ^ E_NOTICE);
			
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
			//}
			//echo "<br>";
																
			$pay_id 			= $data->sheets[0]['cells'][$i][2];
			$period	 		= $data->sheets[0]['cells'][$i][3];	
			$due_date			= extophp($data->sheets[0]['cells'][$i][4]);
			$principal 			= $data->sheets[0]['cells'][$i][5];
			$interest			= $data->sheets[0]['cells'][$i][6];
			$days				= $data->sheets[0]['cells'][$i][7];
			$penalty			= $data->sheets[0]['cells'][$i][8];
			$outbal			= $data->sheets[0]['cells'][$i][9];

			$sql = "insert into dprc_ledger set
						dprc_payment_id='$pay_id',
						period='$period',					
						principal='$principal',
						interest='$interest',
						due_date='$due_date',
						late_days='$days',
						penalty='$penalty',
						outbal='$outbal'";


			echo "<p>".$sql."</p>";

			mysql_query($sql);

			echo mysql_error();				
		}

		// End Header Load			
	}
	else if($b=='Load DP') {	
		$xls_filename = "DPLedger.xls";				
		$data->read("My_Uploads/Excel/$xls_filename");
			
		error_reporting(E_ALL ^ E_NOTICE);
			
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
			//}
			//echo "<br>";
																
			$application_id 		= $data->sheets[0]['cells'][$i][1];
			$pay_id	 		= $data->sheets[0]['cells'][$i][2];	
			$principal 			= $data->sheets[0]['cells'][$i][3];
			$days				= $data->sheets[0]['cells'][$i][4];
			$penalty			= $data->sheets[0]['cells'][$i][5];
			$outbal			= $data->sheets[0]['cells'][$i][6];
			$remarks			= addslashes($data->sheets[0]['cells'][$i][9]);
			$discount			= $data->sheets[0]['cells'][$i][10];

			$sql = "insert into dprc_dp set
						application_id='$application_id',
						dprc_payment_id='$pay_id',				
						dp_principal='$principal',
						dp_days='$days',
						dp_penalty='$penalty',
						dp_outbal='$outbal',
						remarks='$remarks',
						discount='$discount'";


			echo "<p>".$sql."</p>";

			mysql_query($sql);

			echo mysql_error();				
		}

		// End Header Load			
	}
	else if($b=='Delete Selected') {
		$checkList = $_REQUEST['checkList'];

		foreach($checkList as $c) {
			unlink("../My_Uploads/Excel/$c");
		}
	}
	
	function extophp($dataD) {
		if(is_numeric($dataD)) {		
			$ts = mktime(0,0,0,1,$dataD-1,1900);			
			return date("Y-m-d",$ts);	
		}
		else {
			return date("Y-m-d", strtotime($dataD)-1);
		}
	}

?>
<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/user.png'> LOAD EXCEL</div>
    <div class="module_actions">
	<!--
        <input type="file" name="xls" class="textbox" />
        <input type="submit" name="b" value="Load Excel File" class="buttons" onclick="return approve_confirm();" />
	-->
	<input type="submit" name="b" value="Load DPRC Application" class="buttons" onclick="return approve_confirm();" />
	<input type="submit" name="b" value="Load Inventory" class="buttons" onclick="return approve_confirm();" />
	<input type="submit" name="b" value="Load DPRC Ledger" class="buttons" onclick="return approve_confirm();" />
	<input type="submit" name="b" value="Load DPRC Payments" class="buttons" onclick="return approve_confirm();" />
	<input type="submit" name="b" value="Load DP" class="buttons" onclick="return approve_confirm();" />
       <input type="submit" name="b" value="Delete Selected" class="buttons" onclick="return approve_confirm();" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table cellspacing="2" cellpadding="5" width="100%" align="center" class="search_table">
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
          <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
		  <td><b>Filename</b></td>
        </tr> 
		<?php
            if ($handle = opendir('My_Uploads/Excel')) {
				$i = 0;
                while (false !== ($file = readdir($handle))) {
					if($file=='.' || $file=='..') continue;
				
					echo '<tr bgcolor="'.$transac->row_color($i++).'">';
					
					echo '<td width="20">'.$i.'</td>';				
					echo '<td><input type="checkbox" name="checkList[]" value="'.$file.'" onclick="document._form.checkAll.checked=false"></td>';				
                    echo "<td>$file</td>";
					
					echo '</tr>';
                }
            
                closedir($handle);
            }
        ?>
    </table>
    </div>
</div>
</form>