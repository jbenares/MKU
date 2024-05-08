<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	STOCKS TRANSFER HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th>DATE</th>
                    <th>TRANSFER #</th>
                    <th>PROJECT</th>
                    <th>ITEM</th>
                    <th>QTY</th>
                    <th>UNIT</th>
                </tr>	
                
             	<?php
					$query="
						select
							*
						from
							transfer_header as h, transfer_detail as d, productmaster as p, projects as pr
						where
							h.transfer_header_id = d.transfer_header_id
						and
							d.stock_id = p.stock_id
						and
							h.project_id = pr.project_id
						and
							h.status != 'C'
						and
							h.date between '$from_date' and '$to_date'
						order by
							h.date asc, h.transfer_header_id asc
					";
					$result=mysql_query($query) or die(mysql_error());
					
					while($r=mysql_fetch_assoc($result)){
				?>	
                        <tr>
                            <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                            <td><?=str_pad($r['transfer_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                            <td><?=$r['project_name']?></td>                       
                            <td><?=$r['stock']?></td>                       
                            <td style="text-align:right;"><?=$r['quantity']?></td>                       
                            <td><?=$r['unit']?></td>                       
                      	</tr>
				<?php } ?>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>