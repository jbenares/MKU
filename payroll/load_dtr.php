<?php

	require_once 'Excel/reader.php';	
	$data = new Spreadsheet_Excel_Reader();	
	$data->setOutputEncoding('CP1251');

	ini_set('max_execution_time', 1000);

	$b = $_REQUEST['b'];
	$xls = $_FILES['xls'];
	$brand = $_REQUEST['brand'];
	$checkList = $_REQUEST['checkList'];
	//print_r($xls);
	
	if($b=='Load File') {
		/* Tag as processed all old biometric records */
		$tagold = mysql_query("update biometric_entries set old='1'");

		$filename =  $upload->upload_img($xls[size], $xls[type], $xls[tmp_name], $xls[name], "Excel", "");

		//$my_file = 'My_Uploads/Excel/FLOOD112514.txt';
		$my_file = $filename;
		$handle = fopen($my_file, 'r');
		$data = fread($handle,filesize($my_file));
		
		$line = explode("\n", $data);		

		if($brand==1) {
			foreach($line as $key => $l) {
				$new_data = explode("\t", $l);
				//print_r($new_data);
				//echo '<br>';

				if($new_data[0]!='No') {
					$empID = $new_data[2];
					$b_dt = explode(" ", $new_data[6]);
					$b_date = date("Y-m-d", strtotime($b_dt[0]));
					$b_time = $b_dt[1];

					if($b_date!='1970-01-01') {
						$check_if_saved = mysql_query("select biometric_ID from biometric_entries where
										empID='$empID' and
										b_date='$b_date' and
										b_time='$b_time'");

						if(mysql_num_rows($check_if_saved)==0) {

							$query = mysql_query("insert into biometric_entries set
										empID='$empID',
										No='$new_data[0]',
										b_date='$b_date',
										b_time='$b_time',
									   brand='1'");

							//echo $b_date.' '.$b_time.' '.$empID.'<br>';

							echo mysql_error();
						}	
					}
				}
			}
		}
		else if($brand==2) {
			foreach($line as $key => $l) {
				$new_data = explode("\t", $l);
				//print_r($new_data);
				//echo '<br>';

				if(count($new_data)==6) {
					if(!empty($new_data[0])) {
						$empID = ltrim(substr($new_data[0], -4), '0');					     
						
						$b_dt = explode(" ", $new_data[1]);
						$b_date = date("Y-m-d", strtotime($b_dt[0]));
						$b_time = $b_dt[1];

						if($b_date!='1970-01-01') {
							$check_if_saved = mysql_query("select biometric_ID from biometric_entries where
											empID='$empID' and
											b_date='$b_date' and
											b_time='$b_time'");

							if(mysql_num_rows($check_if_saved)==0) {

								$query = mysql_query("insert into biometric_entries set
											empID='$empID',
											No='',
											b_date='$b_date',
											b_time='$b_time',
										   brand='2'");

								//echo $b_date.' '.$b_time.' '.$empID.'<br>';

								echo mysql_error();
							}	
						}
					}
				}
			}
		}

		$one_month_before = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
		echo $one_month_before;
		//print_r($line);

		$get_emps = mysql_query("select
							empID
						from
							biometric_entries
						where
							old='0'
						group by
							empID
						order by
							empID asc");
			
		echo "<p>".mysql_error()."</p>";

		while($re=mysql_fetch_array($get_emps)) {
			if($re[empID]=='0') continue;

			echo "<p>".mysql_error()."</p>";
			
			$get_dates = mysql_query("select
								biometric_ID,
								empID,
								b_date,
								min(b_time) as minb_time,
								max(b_time) as maxb_time
							from
								biometric_entries
							where
								empID='$re[empID]' and
								b_date > '$one_month_before' and
								old='0'
							group by
								b_date
							order by
								b_date asc");
			
			while($rd=mysql_fetch_array($get_dates)) {
				$to_time = strtotime($rd[maxb_time]);
				$from_time = strtotime($rd[minb_time]);

				$inam = strtotime($rd[minb_time]);
				$outpm = strtotime($rd[maxb_time]);

				$get_time_in = mysql_query("select emp_time_in from employee where employeeID='$rd[empID]'");
				$rti = mysql_fetch_array($get_time_in);
				
				if($inam<=strtotime($rti[emp_time_in])) $inam = strtotime($rti[emp_time_in]);

				if($rti[emp_time_in]=="07:30:00" && $outpm>=strtotime("16:30")) $outpm = strtotime("16:30");
				else if($rti[emp_time_in]=="08:00:00" && $outpm>=strtotime("17:00")) { $outpm = strtotime("17:00"); }

				$hrs_worked = round(abs( ($outpm - $inam) )/3600,2) - 1; // Less (1) Hour Break

				$hrs_late = 8 - $hrs_worked; // Late in hours			
				$mins_late = $hrs_late * 60; // Late in mins

				if($mins_late < 6) $late = 0;
				if($mins_late >= 6 && $mins_late <16) $late = 0.5;
				if($mins_late >= 16 && $mins_late <= 30) $late = 1;
				if($mins_late > 30) $late = 4;

				$hrs_worked = 8 - $late;

				$get_all_time = mysql_query("select biometric_ID from biometric_entries where b_date='$rd[b_date]' and employeeID='$rd[empID]'");
				if(mysql_num_rows($get_all_time)>=4 && strtotime($rd[maxb_time])>=$outpm) $hrs_worked = 0;

				//echo $rd[empID].' '.$rd[b_date].' '.$rd[minb_time].' '.$rd[maxb_time].' '.$hrs_worked.' '.$hrs_ot.'<br>';			

				$allowance_rate = $options->getAttribute('employee','employeeID',$rd[empID],'allowance');

				$employee_statusID = $options->getAttribute('employee','employeeID',$rd[empID],'employee_statusID');
			
				if($employee_statusID == 1) { // Monthly					
					$saved_rate = $options->getAttribute('employee','employeeID',$rd[empID],'base_rate')/26;
				}
				else if( $employee_statusID == 2 ) { // Daily
					$saved_rate = $options->getAttribute('employee','employeeID',$rd[empID],'base_rate');
				}

				$actual_rate = ($saved_rate*$hrs_worked)/8;

				$check_if_inDTR = mysql_query("select dtrID from dtr where employeeID='$rd[empID]' and dtr_date='$rd[b_date]' and dtr_void='0'");

				//echo mysql_num_rows($check_if_inDTR)."<br>";
				
				if(mysql_num_rows($check_if_inDTR)==0) {
					
					$save_dtr = mysql_query("insert into dtr set
									employeeID='$rd[empID]',
									dtr_date='$rd[b_date]',
									employee_statusID='$employee_statusID',
									saved_rate='$saved_rate',
									work_value='$hrs_worked',
									hrs_ot='$hrs_ot',
									saved_allowance_rate='$allowance_rate',
									actual_rate='$actual_rate',
							    		time_in='$rd[minb_time]'") or die(mysql_error());
					
					echo "<p>DTR Saved!</p>";
					

				}
				
				mysql_query("update biometric_entries set processed='1' where b_date='$rd[b_date]'");
			}

		}
		
	}
	else if($b=='Delete Selected') {
		foreach($checkList as $c) {
			unlink("My_Uploads/Excel/$c");
		}
	}
	else if($b=='Populate Time In') {
		$get_dtr = mysql_query("select * from dtr where time_in='00:00:00' order by dtr_date desc limit 5000") or die(mysql_error());

		while($r_dtr=mysql_fetch_array($get_dtr)) {	

			//echo $r_dtr[time_in];

			$get_dates = mysql_query("select
							min(b_time) as minb_time
						from
							biometric_entries
						where
							empID='$r_dtr[employeeID]' and
							b_date='$r_dtr[dtr_date]'") or die(mysql_error());

			$r_dates = mysql_fetch_array($get_dates);

			//echo $r_dates[minb_time].'<br>';

			mysql_query("update dtr set time_in='$r_dates[minb_time]' where dtrID='$r_dtr[dtrID]'") or die(mysql_error());

		}
	}

?>
<form name="_form" id="_form" action="" method="post" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/user.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
        <input type="file" name="xls" class="textbox" />
	 <select name=brand class=select>
		<option value=1>Mustard Seed</option>
	  	<option value=2>Spectramind</option>
	 </select>
        <input type="submit" name="b" value="Load File" class="buttons" onclick="return approve_confirm();" />
        <input type="submit" name="b" value="Delete Selected" class="buttons" onclick="return approve_confirm();" />
	<input type="submit" name="b" value="Populate Time In" class="buttons" onclick="return approve_confirm();" />
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

			if($i==20) break;
                }
            
                closedir($handle);
            }
        ?>
    </table>
    </div>
</div>
</form>
<?php
	/*
		if($xls[size]>0) {
			$xls_filename =  $upload->upload_img($xls[size], $xls[type], $xls[tmp_name], $xls[name], "Excel", "");

			//echo $xls_filename;
			//exit;

			//ini_set('memory_limit','128M');
						
			$data->read("$xls_filename");

			error_reporting(E_ALL ^ E_NOTICE);		
						
			// $data->sheets[0]['cells'][a][b]; a -> Rows, b -> Cols
			//echo $data->sheets[0]['cells'][2][2];
				
			for ($i = 12; $i <= $data->sheets[0]['numRows']; $i++) {
				//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
					//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
				//}
				
				$employee = strstr($data->sheets[0]['cells'][$i][1], "Employee: ");
				//echo $employee."<br>";
				if(!empty($employee)) {
					$exploded = explode('(', $employee);
					$idnumber = ltrim(substr($exploded[1], 0, -1),0
);				
					//echo $idnumber."<br>";
				}
				
				if($data->sheets[0]['cells'][$i][1]=='Date') {
					$start_dtr_records = true;
					continue;
				}
				
				if($data->sheets[0]['cells'][$i][1]=='Days Present:' || $data->sheets[0]['cells'][$i][1]=='Total:') {
				   
					$start_dtr_records = false;
					continue;
				}
				
				if($start_dtr_records && !empty($data->sheets[0]['cells'][$i][1])) {
					$dtr_date		= $data->sheets[0]['cells'][$i][1];
					$in1			= $data->sheets[0]['cells'][$i][3];
					$out1			= $data->sheets[0]['cells'][$i][4];
					$in2			= $data->sheets[0]['cells'][$i][5];
					$out2			= $data->sheets[0]['cells'][$i][6];
					$hrs_required		= $data->sheets[0]['cells'][$i][15];
					$break			= $data->sheets[0]['cells'][$i][16];
					$hrs_worked		= $data->sheets[0]['cells'][$i][17];
					$hrs_ot			= $data->sheets[0]['cells'][$i][18];
					$hrs_ut			= $data->sheets[0]['cells'][$i][19];

					//if($hrs_worked==0 || empty($hrs_worked)) continue;
					
					//echo ($dtr_date-25569)." - ".gmdate("Y-m-d", ($dtr_date-25569)*86400)."<br>";
				
					$dtr_date = gmdate("Y-m-d", ($dtr_date-25569)*86400);

					$saved_rate = $options->getAttribute('employee','employeeID',$employee,'base_rate');

					$sql = mysql_query("insert into dtr set
								employeeID='$idnumber',
								dtr_date='$dtr_date',
								saved_rate='$saved_rate',
								in1='$in1',
								out1='$out1',
								in2='$in2',
								out2='$out2',
								hrs_required='$hrs_required',
								work_value='$hrs_worked',
								break='$break',
								hrs_ot='$hrs_ot',
								hrs_ut='$hrs_ut'");
					echo mysql_error();

					error_reporting(E_ALL ^ E_NOTICE);		

				}	
			}

		}			
		
		echo $msg;
	*/
?>
