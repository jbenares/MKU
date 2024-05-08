<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
	.alert{background-color:#FFA893;}
	
</style>

<?php
	set_time_limit(800);
	$b	 			= $_REQUEST['b'];
	//$keyword		= $_REQUEST['keyword'];
	$checkList		= $_REQUEST['checkList'];
	$checkList2		= $_REQUEST['checkList2'];
	
	if($b=='Process Selected') {
	  if(!empty($checkList)) {
		foreach($checkList as $ch) {
			foreach($checkList2 as $ch2){
				mysql_query("update maintenance set date_fix='".date("Y-m-d")."' WHERE stock_id='$ch' AND job_id='$ch2'");
				//$options->insertAudit($ch,'petty_cash_id','D');
				
				//redirect to job order process
				header("location: admin.php?view=7b39524250cb38f48e8e&maintenance=true&eqpid=".$ch."&jobid=".$ch2);
			}
		}
	  }
	}
?>
<!--<div id="loading">
	<img id="loading-image" src="img/713.gif" alt="Loading..." />
	<h3 id="loading-text">Loading...Please Wait.</h3>
</div> !-->
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
	<div class="module_actions">
    	<!--<input type="text" name="keyword" class="textbox" value="<?=$keyword;?>" />!-->
        <!--<input type="submit" name="b" value="Search" class="buttons" />!-->
		<input type="submit" name="b" value="Process Selected" class="buttons" />
	</div>
    <table cellspacing="2" cellpadding="3" width="100%" align="left" class="search_table">
    	<tr bgcolor="#C0C0C0">				
          <td width="20"><b>#</b></td>
		  <td width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></td>
		  <td>Name</td>
		  <td width="20">KM Run</td>
		  <td width="20">Hours Operate</td>
		  <td>Job Needed</td>  
         </tr>        
		<?php	
			//list of equipments
			$list=array(
						'DT',
						'TM',
						'BT',
						'SL',
						'WT',
						'PM',
						'SV',
						'TRUCK MOUNTED',
						);
			$list2=array(
						'BH',
						'RG',
						'PL',
						'RR',
						'BD',
						'TC',
						'BC',
						);
			$ii=0;
			$i=1;
			while($ii<sizeof($list)){
				$rs="
						SELECT * FROM
							productmaster
						WHERE
							stock";
				if($ii==0){
					$rs.=" LIKE
							'".$list[$ii]."%'
						   AND
							categ_id1='25'
						   AND
							e_status='1'
					 	   ORDER by stock ASC
						  ";
				}else{
					$rs.=" LIKE
							'".$list[$ii]."%'
						   AND
							categ_id1='25'
						   AND
							e_status='1'
						   ORDER by stock ASC
						   ";
				}
				//echo $rs;	
				$qry2 = mysql_query($rs);				
				$km=0;
				$now=date("Y-m-d");
				$diff="";
				$curd="";
				while($r=mysql_fetch_assoc($qry2)){
					extract($r);
					//get eur_detail
						$qry="
								SELECT 
									*,sum(ed.km) as s_km
								FROM 
									eur_header as eh,eur_detail as ed
								WHERE
									eh.stock_id='$stock_id'
								AND
									eh.eur_header_id=ed.eur_header_id
								AND
									ed.eur_void!='1'
									";
							$stat="";
							$km_r=0;
							$heq_d=0;
							$truck_d=0;
							
							//get all jobIds
							$qrry=mysql_query("select * from dynamic.jobs where is_alert='1'");
							while($e=mysql_fetch_assoc($qrry)){
									$jobId=$e['job_id'];
									$job=$e['job'];
									$km_r=$e['km_run'];
									//$heq_d=$e['heq_d'];
									$truck_d=$e['truck_d'];
								
									
									//check stock id if already been fixed or existed
								
									$chck=mysql_query("select * from maintenance where stock_id='$stock_id' and job_id='$jobId'");
									if(mysql_num_rows($chck)){
										$fetch=mysql_fetch_assoc($chck);
										extract($fetch);
									
										//get diff from last fixed until now
										$year   = gmdate('Y');
										$month  = gmdate('m');
										$day    = gmdate('d');
										
										$f=explode("-",$date_fix);
										 //seconds in a day = 86400
										$days_in_between = (mktime(0,0,0,$month,$day,$year) - mktime(0,0,0,$f[1],$f[2],$f[0]))/86400;
										$dif=($days_in_between / 365.242199)*12;
										$diff = round($dif,2); //with leap year
										
										$qry.="AND
											ed.released_date>='$date_fix'";
										
										$qry_w=mysql_query($qry) or die( mysql_error() );
										$fet=mysql_fetch_assoc($qry_w);
										$km=$fet['s_km'];
										
										if(($diff>=$truck_d && $km!=0) || $km>=$km_r){
														$stat.="<input type='checkbox' name='checkList2[]' value='".$jobId."' onclick='document._form.checkAll.checked=false'>".$job." / ";
										}
										
									}else{
										$qry.="AND
												ed.released_date>='$now'";
										//insert into maintenance if not existed
										mysql_query("insert into maintenance(stock_id,date_fix,job_id) values('$stock_id',now(),'$jobId')");
										$curd=$now;
										//get diff from last fixed until now
										$year   = gmdate('Y');
										$month  = gmdate('m');
										$day    = gmdate('d');
										$f=explode("-",$now);
										 //seconds in a day = 86400
										$days_in_between = (mktime(0,0,0,$month,$day,$year) - mktime(0,0,0,$f[1],$f[2],$f[0]))/86400;
										$diff = round(($days_in_between / 365.242199)*12,2); //with leap year
									}			
						}
					echo '<tr bgcolor="'.$transac->row_color($i).'">';
				?>
							<td width="20"><?=$i++?></td>  
							<td><input type="checkbox" name="checkList[]" value="<?=$stock_id?>" onclick="document._form.checkAll.checked=false"></td>
							<td><?=$stock?></td>
							<td width="20"><?=round($km,0)?></td>
							<td width="20"><?=round($diff,0)?></td>
							<td style="color:red;">
							<?php	
								
									//DISPLAY STATUS
									if(empty($stat)){
										echo '<font color="green">None</font>';
									}else{
										echo $stat;
									}
									
							?>									
						</tr>
				<?php
				}
			$ii++;
		}
		
		//HEAVY EQUIP
		$ii=0;
		while($ii<sizeof($list2)){
				$rs_=mysql_query("
								SELECT * FROM
									productmaster
								WHERE
									stock
								LIKE
									'".$list2[$ii]."%'
								AND
									categ_id1='25'
								AND
									e_status='1'
								AND
									unit='UNIT'
								ORDER by stock ASC
								");
						
				$km=0;
				$now=date("Y-m-d");
				$diff="";
				$curd="";
				while($r_=mysql_fetch_assoc($rs_)){
					extract($r_);
					//get eur_detail
						$qry="
								SELECT 
									*,sum(ed.computed_time) as com_r
								FROM 
									eur_header as eh,eur_detail as ed
								WHERE
									eh.stock_id='$stock_id'
								AND
									eh.eur_header_id=ed.eur_header_id
								AND
									ed.eur_void!='1'
									";
							$stat="";
							$km_r=0;
							$heq_d=0;
							$truck_d=0;
							
							//get all jobIds
							$qrry=mysql_query("select * from dynamic.jobs where is_alert='1'");
							while($e=mysql_fetch_assoc($qrry)){
									$jobId=$e['job_id'];
									$job=$e['job'];
									$km_r=$e['c_time'];
									//$heq_d=$e['heq_d'];
									$truck_d=$e['heq_d'];
								//check stock id if already been fixed or existed
								$chck=mysql_query("select * from maintenance where stock_id='$stock_id' and job_id='$jobId'");
								if(mysql_num_rows($chck)){
									$fetch=mysql_fetch_assoc($chck);
									extract($fetch);
									$qry.="AND
											ed.released_date>='$date_fix'";
									$curd=$date_fix;
									//get diff from last fixed until now
									$year   = gmdate('Y');
									$month  = gmdate('m');
									$day    = gmdate('d');
									$f=explode("-",$date_fix);
										 //seconds in a day = 86400
										$days_in_between = (mktime(0,0,0,$month,$day,$year) - mktime(0,0,0,$f[1],$f[2],$f[0]))/86400;
										$dif=($days_in_between / 365.242199)*12;
										$diff = round($dif,2); //with leap year
										
										$qry.="AND
											ed.released_date>='$date_fix'";
										
										$qry_w=mysql_query($qry);
										$fet=mysql_fetch_assoc($qry_w);
										$km=$fet['com_r'];
										
										
										if(($diff>=$truck_d && $km!=0) || $km>=$km_r){
														$stat.="<input type='checkbox' name='checkList2[]' value='".$jobId."' onclick='document._form.checkAll.checked=false'>".$job." / ";
										}
								}else{
									$qry.="AND
											ed.released_date>='$now'";
									//insert into maintenance if not existed
									mysql_query("insert into maintenance(stock_id,date_fix,job_id) values('$stock_id',now(),'$jobId')");
									$curd=$now;
									//get diff from last fixed until now
									$year   = gmdate('Y');
									$month  = gmdate('m');
									$day    = gmdate('d');
									$f=explode("-",$now);
									 //seconds in a day = 86400
									$days_in_between = (mktime(0,0,0,$month,$day,$year) - mktime(0,0,0,$f[1],$f[2],$f[0]))/86400;
									$dif=($days_in_between / 365.242199)*12;
									$diff = round($dif,2); //with leap year
								}
						}
					
					echo '<tr bgcolor="'.$transac->row_color($i).'">';
				?>
							<td width="20"><?=$i++?></td>  
							<td><input type="checkbox" name="checkList[]" value="<?=$stock_id?>" onclick="document._form.checkAll.checked=false"></td>
							<td><?=$stock?></td>
							<td width="20"><?=round($km,0)?></td>
							<td width="20"><?=round($diff,0)?></td>
							<td style="color:red;">
							<?php
									//DISPLAY STATUS
									if(empty($stat)){
										echo '<font color="green">None</font>';
									}else{
										echo $stat;
									}
							?></td>			
						</tr>
				<?php
				}
			$ii++;
		}
        ?>
    </table>
</div>
</form>
<script language="javascript" type="text/javascript">
  $(window).load(function() {
    $('#loading').hide();
  });
</script>