
<?php

	include_once("../conf/ucs.conf.php");
	
	
	$from = $_REQUEST['from_date'];
	$to = $_REQUEST['to_date'];

	
	$m=explode("-",$from);
	$m_=explode("-",$to);
	
	
	$f=$m[2];
	//display from and to word format
	
	$disf = date("M d, Y",strtotime($from));
	$dist = date("M d, Y",strtotime($to));
	//$sql = "SELECT * ";
	
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
	body
	{
		size: legal portrait;
		padding:0px;
		font-family:Arial, Helvetica, sans-serif;
		font-size:8px;
	}

	.clearfix:after {
		content: ".";
		display: block;
		clear: both;	
		visibility: hidden;
		line-height: 0;
		height: 0;
	}

	.clearfix {
		display: inline-block;
	}

	html[xmlns] .clearfix {
		display: block;
	}
	 
	* html .clearfix {
		height: 1%;
	}

	table{ border-collapse:collapse; width:100%; }
	table td{ padding:3px; text-align:center;}
	table thead td{ font-weight:bold;  border-top:1px solid #000; border-bottom:1px solid #000; }
	table tfoot td{
		border-top:1px solid #000;
		font-weight:bold;	
	}
	@media print{
		table { page-break-inside:auto }
		tr{ page-break-inside:avoid; page-break-after:auto }
		thead { display:table-header-group }
		tfoot { display:table-footer-group }
		.pb { page-break-after:always }
	}
	
</style>
</head>
<body>
	<h2>KILOMETER RUN</h2>
	<p>For the period from <?=$disf?> to <?=$dist?></p>
	<!--<div class="divFooter">
		<h2>KILOMETER RUN</h2>
		<p>For the period from <?=$disf?> to <?=$dist?></p>
	</div>!-->
	<table border='1' cellpadding="1" cellspacing='0' width='100%'>
		<tr>
			<td>Unit#</td>
			<td width="20">TruckClass</td>
			<td>EngModel</td>
			<?php
				while($f<=$m_[2]){
					?>
						<td><?=$f++?></td>
					<?php
				}
			?>
			<td>TOTAL</td>
		</tr>
		<?php
			$i=0;
			$l=1;
				
			while($i<sizeof($list)){
				$sql="SELECT
						*
					FROM
						productmaster
					WHERE 
						stock
					LIKE
						'".$list[$i]."%'
					AND
						categ_id1='25'	
					AND
					      e_status='1'
					ORDER by stock ASC
					";
				//echo $sql;
				$qu=mysql_query($sql);
				$totalt=0;
				while($fe=mysql_fetch_assoc($qu)){
					//$km=$fe['km'];
					$stock=$fe['stock'];
					$engmodel=explode("-",$fe['barcode']);
					$sid=$fe['stock_id'];
					if($i==0){
						$te=explode(" ",$stock);
						if($te[1]=='TRUCK'){
							$te=explode("#",$stock);
						}
					}else{
						$te=explode("#",$stock);
					}
					
					
					?>
					<tr>
									<td width="20"><?=$l++?></td>
									<td style="text-align:left;" width="40">
									<?php
										if($i==0){
											echo $stock;
										}else if($i==1){
											echo $stock;
										}else if($i==2){
											echo $stock;
										}else if($i==3){
											echo $stock;
										}else if($i==4){
											echo $stock;
										}else if($i==5){
											echo $stock;
										}else if($i==6){
											echo $stock;
										}else if($i==7){
											echo 'T-MOUNTED';
										}
										
									?></td>
									<td width="20"><?=$engmodel[0]?></td>
					<?php
							$ge="SELECT
										*,sum(ed.km) as k
									FROM
										eur_header as eh,eur_detail as ed
									WHERE
										eh.stock_id='$sid'
									AND
										eh.eur_header_id=ed.eur_header_id
									AND
										ed.eur_void !='1'
									AND
										ed.released_date
									BETWEEN
										'$from'
									AND
										'$to'
									GROUP BY 
										ed.released_date
									ORDER BY
										ed.released_date ASC
										";
							$gee=mysql_query($ge);
						
							if(mysql_num_rows($gee)==0){
								$f=$m[2];
								$kmt=0;
								while($f<=$m_[2]){
									?>
										<td width="20">&nbsp;</td>
									<?php
									$f++;
								}
							}else{
								$kmt=0;
								$date="";
								$km=0;
								//$d="";
								$f=$m[2];
								while($fet=mysql_fetch_assoc($gee)){
										//$eurid=$fet['eur_header_id'];
										$d=explode("-",$fet['released_date']);
										$km=round($fet['k'],0);
										
									while($f<=$m_[2]){
											//echo $m[2].' - '.$d[2].'&nbsp';
											if($f==$d[2]){
												$kmt=$km+$kmt;
												?>
													<td width="20"><?=round($km,0)?></td>
												<?php
												break;
											}else{
												?>
													<td width="20">&nbsp;</td>
												<?php
											}
										$f++;
									}
									$f++;
								}
							}
						$f--;
						if($f!=0){
							while($f<$m_[2]){
								?>
									<td width="20">&nbsp;</td>
								<?php
								$f++;
							}
						}
						/*else if(mysql_num_rows($gee)==0){
							$f=$m[2];
							while($f=<$m_[2]){
								?>
									<td width="20">&nbsp;</td>
								<?php
							}
							$f++;
						}*/
						?>
								<td style="text-align:right;" width="20"><?=$kmt?></td>
						</tr>
					<?php
					$totalt=$kmt+$totalt;
				}
				$i++;
				?>
					<tr>
						<td colspan="2"><b>Sub-Total # of Units</b></td>
						<td><b><?php echo $u=mysql_num_rows($qu); ?></b></td>
					
					<?php
						$f=$m[2];
						while($f<=$m_[2]){
							?>
								<td>&nbsp;</td>
							<?php
							
							$f++;
						}
					?>
								<td><b><?=$totalt?></b></td>
					</tr>
					<tr><td colspan="<?=$m_[2]?>"></td></tr>
				<?php
			}
		?>
	<!--
	</table>
	
	<table border='1' cellpadding="1" cellspacing='0' width='100%'>
	-->
	<!-- HEAVE EQUIPMENT SECOND TABLE !-->
		<tr><td colspan="<?=$m_[2]+6?>"><b>HEAVY EQUIPMENT UNITS (hrs)</b></td></tr>
		<?php
			$i=0;
			//$l=1;
			while($i<sizeof($list2)){
				//echo $list2[$i];
				$sql2="SELECT
						*
						FROM
							productmaster
						WHERE 
							stock
						LIKE
							'".$list2[$i]."%'
						AND
							categ_id1='25'
						AND
							e_status ='1'
						AND
							unit='UNIT'
						ORDER by stock ASC
							";

				$qu2=mysql_query($sql2);
				$totalt=0;
				$prevt="";
				while($fe2=mysql_fetch_assoc($qu2)){
					//$km=$fe['km'];
					$stock2=$fe2['stock'];
					$engmodel2=explode("-",$fe2['barcode']);
					$sid2=$fe2['stock_id'];
					$te2=explode(" ",$stock2);
					
					?>
					<tr>
									<td width="20"><?=$l++?></td>
									<td style="text-align:left;" width="40">
									<?php
										if($i==0){
											echo $stock2;
										}else if($i==1){
											echo $stock2;
										}else if($i==2){
											if($stock2 != 'PLATE COMPACTOR'){
												echo $stock2;
											}
										}else if($i==3){
											echo $stock2;
										}else if($i==4){
											echo $stock2;
										}else if($i==5){
											echo $stock2;
										}else if($i==6){
											echo $stock2;
										}
										
									?>
									</td>
									<td width="20"><?=$engmodel2[0]?></td>
					<?php
									$ge2="SELECT
										*,sum(ed.computed_time) as k
									FROM
										eur_header as eh,eur_detail as ed
									WHERE
										eh.stock_id='$sid2'
									AND
										eh.eur_header_id=ed.eur_header_id
									AND
										ed.eur_void !='1'
									AND
										ed.released_date
									BETWEEN
										'$from'
									AND
										'$to'
									GROUP BY 
										ed.released_date
									ORDER BY
										ed.released_date ASC
										";
							$gee2=mysql_query($ge2);
							if(mysql_num_rows($gee2)==0){
								$f=$m[2];
								$kmt2=0;
								while($f<=$m_[2]){	
									?>
										<td width="20">&nbsp;</td>
									<?php
									$f++;
								}
							}else{
							
								
								$kmt2=0;
								$date="";
								$km2=0;
								//$d="";
								$f=$m[2];
							while($fet2=mysql_fetch_assoc($gee2)){
									//$eurid=$fet['eur_header_id'];
									$d=explode("-",$fet2['released_date']);
									$km2=round($fet2['k'],0);
									
								while($f<=$m_[2]){
										//echo $m[2].' - '.$d[2].'&nbsp';
										if($f==$d[2]){
											$kmt2=$km2+$kmt2;
											?>
												<td width="20"><?=round($km2,0)?></td>
											<?php
											
											break;
										}else{
											?>
												<td width="20">&nbsp;</td>
											<?php
										}
									$f++;
								}
								$f++;
							}
						}
						$f--;
						if($f!=0){
							while($f<$m_[2]){
								?>
									<td width="20">&nbsp;</td>
								<?php
								$f++;
							}
						}
					?>
								<td style="text-align:right;" width="20"><?=$kmt2?></td>
						</tr>
					<?php
					$totalt=$kmt2+$totalt;
				}
				$i++;
				?>
					<tr>
						<td colspan="2"><b>Sub-Total # of Units</b></td>
						<td><b><?php echo $p=mysql_num_rows($qu2); ?></b></td>
					
					<?php
						//$i=0;
						$f=$m[2];
						while($f<=$m_[2]){
							?>
								<td>&nbsp;</td>
							<?php
							
							$f++;
						}
					?>
								<td><b><?=$totalt?></b></td>
					</tr>
				<?php
			}
		?>
	</table>
</body>
</html>