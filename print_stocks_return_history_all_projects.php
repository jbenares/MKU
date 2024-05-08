<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$stock_id		= $_REQUEST['stock_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
 a{
	color:#000;
	text-decoration:none; 
 }
 a:hover{
	text-decoration:underline; 
 }
</style>

<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	STOCKS RETURN HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<?php
			$result = mysql_query("select * from projects order by project_name asc") or die(mysql_error());
			$projects = array();
			while($r = mysql_fetch_assoc($result)){
				$projects[] = $r['project_id'];
			}
            ?>
        
			<?php
			$grand_total_amount = 0;
			//$grand_total_quantity = 0;
           //$g_kg = 0;
			foreach($projects as $project_id){
            ?>      
            	<?php
				$query="
					select
							*
						from
							return_header as h, return_detail as d, productmaster as p, projects as pr
						where
							h.return_header_id = d.return_header_id
						and
							d.stock_id = p.stock_id
						and
							h.project_id = pr.project_id
						and
							h.status != 'C'
						AND
							h.project_id = '$project_id'
						and
							h.date between '$from_date' and '$to_date'
				";
				
				if(!empty($stock_id)){
				$query.="
					and 
						d.stock_id = '$stock_id'
				";	
				}
				
				if($work_category_id){
				$query.="
					and
						work_category_id = '$work_category_id'
				";	
				}
				
				if($sub_work_category_id){
				$query.="
					and
						sub_work_category_id = '$sub_work_category_id'
				";	
				}
				
				$query.="
					order by
							h.date asc, h.return_header_id asc
				";
				$result=mysql_query($query) or die(mysql_error());
				if(mysql_num_rows($result) <= 0){
					continue;	
				}
                ?>
           		<table cellpadding="6">
                	<caption style="text-align:left; font-weight:bold;"><?=$options->getAttribute("projects","project_id",$project_id,"project_name");?></caption>
                    <tr>
                        <th style="text-align:left; width:5%;">DATE</th>
                        <th style="text-align:center; width:10%;">RETURN #</th>
                        <th style="text-align:left;width:15%">SCOPE OF WORK</th>
						<th></th>
					    <th></th>
	                    <!--<th style="width:10%;">REFERENCE</th>
                             <th>PROJECT</th> -->
                        <th style="text-align:left;">ITEM</th>
                        <th style="text-align:right; width:10%;">QTY</th>
                        <th style="text-align:left; width:10%;">UNIT</th>
                        
                       <!-- <th style="text-align:right; width:10%;">KG/PC</th>
                        <th style="text-align:right; width:10%;">TOTAL KG</th>
                        
                        <th style="text-align:right; width:10%;">PRICE</th>
                        <th style="text-align:right; width:10%;">AMOUNT</th>-->
                    </tr>	
                    
                    <?php
					$total_quantity = 0;
					//$total_amount = 0;
					//$t_kg = 0;
					while($r=mysql_fetch_assoc($result)){
						//$total_quantity += $r['quantity'];
                        //$total_amount += $r['amount'];
						//$t_kg			+= $r['quantity'] * $r['kg'];
						
                    ?>	
                    <tr>
                        <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                        <td style="text-align:center;"><?=str_pad($r['return_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                        <td><?=$options->getAttribute('work_category','work_category_id',$r['work_category_id'],'work')?></td>    
						<td></td>
						<td></td>
                        <!--<td><?=$r['reference']?></td>                       
                        <td><?=$r['project_name']?></td> -->                        
                        <td><?=htmlentities($r['stock'])?></td>                       
                        <td style="text-align:right;"><?=$r['quantity']?></td>                       
                        <td><?=$r['unit']?></td>                       
                        
                       <!-- <td style="text-align:right;"><?=number_format($r['kg'],2,'.',',')?></td>                       
                        <td style="text-align:right;"><?=number_format($r['kg'] * $r['quantity'],2,'.',',')?></td>                       
                        
                        <td style="text-align:right;"><?=number_format($r['price'],2,'.',',')?></td>                       
                        <td style="text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td>  -->                     
                    </tr>
                    <?php } ?>
                    <?php
					//$grand_total_amount += $total_amount;
					//$grand_total_quantity += $total_quantity;
				//$g_kg	+= $t_kg;
					?>
                 	<!--<tr>
                        
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
					    <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
                       <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"></td>
                        <td style="border-top:1px solid #000;"></td>
                        
                        <!--<td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($t_kg,3,'.',',')?></td>
                        
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($total_amount,3,'.',',')?></td>
                    </tr>-->
                </table>
       		<?php } ?>
           <!-- <table style="margin-top:5px;">
                <tr>
                    	<td style="border-top:1px solid #000; width:5%;"></td>
                        <td style="border-top:1px solid #000; width:10%;"></td>
                        <td style="border-top:1px solid #000; width:10%;"></td>
                        <td style="border-top:1px solid #000; width:10%;"></td>
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; width:10%; font-weight:bold; text-align:right;"></td>
                        <td style="border-top:1px solid #000; width:10%;"></td>
                        
                       <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($g_kg,3,'.',',')?></td>
                        
                        <td style="border-top:1px solid #000; width:10%;"></td>
                        <td style="border-top:1px solid #000; width:10%; font-weight:bold; text-align:right;"><?=number_format($grand_total_amount,3,'.',',')?></td>-->
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>