<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];	
	#$date_option	= $_REQUEST['date_option'];
	$cash_gchart_id	= $_REQUEST['cash_gchart_id'];
	$project_id		= $_REQUEST['project_id'];
	$supplier_id	= $_REQUEST['supplier_id'];
	
	$date_def = ($date_option) ? 'check_date' : 'cv_date';
	
	function getProjects($cv_header_id){
		$result = mysql_query("
			select
				*
			from
				cv_header as h, cv_detail as d, projects as p
			where
				h.cv_header_id = d.cv_header_id
			and
				d.project_id = p.project_id
			and
				h.cv_header_id = '$cv_header_id'
			group by d.project_id
		") or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r['project_name'];
		}
		
		return implode(",",$a);		
	}
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
<style type="text/css">
td,th{
	vertical-align:top;
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
			DYNAMIC BUILDERS & CONSTRUCTION CO. (PHILS.), INC.</br>
        	BANK RECONCILATION - CLEARED CHECKS <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?><br />
            <?=(!empty($cash_gchart_id)) ? $options->getAttribute("gchart","gchart_id",$cash_gchart_id,"gchart") : "ALL BANK ACCOUNTS" ?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th style="text-align:left;">VOUCHER DATE</th>
                    <th style="text-align:left;">CV#</th>
                    <th style="text-align:left;">SUPPLIER</th>
                    <th style="text-align:left;">PROJECTS</th>
                    <th style="text-align:left;">DATE CLEARED</th>
                    <th style="text-align:left;">CHECK NO</th>
                    <th style="text-align:left;">CHECK DATE</th>
                    <th style="text-align:left;">PARTICULARS</th>
                    <th style="text-align:right;">CASH AMOUNT</th>
                </tr>	
                
             	<?php
					$query="
						select
							*
						from
							cv_header as h, cv_detail as d, supplier as s
						where
							h.cv_header_id = d.cv_header_id
						and h.supplier_id = s.account_id
						and status != 'C'
						and cleared = '1'
					";

					if( !empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) ){
						$query .= "
							and $date_def between '$from_date' and '$to_date'
						";
					}

					if( !empty($_REQUEST['from_cleared_date']) && !empty($_REQUEST['to_cleared_date']) ){
						$query .= " and date_cleared between '$_REQUEST[from_cleared_date]' and '$_REQUEST[to_cleared_date]'";
					}
					
					if(!empty($supplier_id)){
					$query .= "and supplier_id = '$supplier_id'";
					}
					
					if(!empty($cash_gchart_id)){
					$query.="
						and cash_gchart_id = '$cash_gchart_id'
					";	
						
					}
					
					if(!empty($project_id)){
					$query.="
						and project_id in ($project_id)
					";	
					}
					
					
					$query.="
						group by h.cv_header_id
						
						order by
							$date_def asc, check_no asc
					";
					#echo $query;

					$result=mysql_query($query) or die(mysql_error());
					$total_amount = 0;
					while($r=mysql_fetch_assoc($result)){
						$cv_header_id		    = $r['cv_header_id'];
						$cv_header_id_pad		= str_pad($cv_header_id,7,0,STR_PAD_LEFT);
						$cv_no				    = $r['cv_no'];
						$cv_date				= $r['cv_date'];
						$check_date				= $r['check_date'];
						$check_no				= $r['check_no'];
						$supplier_id			= $r['supplier_id'];
						$particulars			= $r['particulars'];
						$supplier 				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
						
						$cash_amount = $options->getCashAmount($cv_header_id);
						
						$total_amount += $cash_amount;
				?>	
                        <tr>
                        	<td><?=date("m/d/Y", strtotime($cv_date))?></td>		
                            <td><?=$cv_no?></td>	
                            <td><?=$supplier?></td>
                            <td><?=getProjects($cv_header_id)?></td>
                            <td><?=$r['date_cleared']?></td>
                            <td><?=$check_no?></td>	
                            <td><?=date("m/d/Y", strtotime($check_date))?></td>		
                            <td><?=$particulars?></td>
                            <td style="text-align:right;"><?=number_format($cash_amount,2,'.',',')?></td>
                      	</tr>
				<?php } ?>
                <tr>
                	<td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><span style="border-bottom:3px double #000;"><?=number_format($total_amount,2,'.',',')?></span></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>