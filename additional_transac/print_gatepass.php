<?php	
require_once(dirname(__FILE__).'/../library/lib.php');
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');

$gatepass_id = $_REQUEST['id'];

$aStatus = array(
    'F' => 'Finished',
    'S' => 'Saved',
    'C' => 'Cancelled'
);


$sql = "
	select
        h.*, concat(employee_lname,', ',employee_fname) as name, project_name
    from
        gatepass as h
        left join employee as e on h.employee_id = e.employeeID         
        left join projects as p on h.project_id = p.project_id
    where gatepass_id = '$gatepass_id'
";

$aTrans = lib::getTableAttributes($sql);

function displayDetails($id){


    /*RAW MATERIALS DISPLAY*/
    $sql = "
        select
            d.*, stock, unit
        from 
            gatepass_detail as d 
        left join productmaster as p on d.stock_id = p.stock_id
        where
            d.gatepass_id = '$id'
        and gatepass_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
   /* echo "
        <table class='table-css' style='vertical-align:top; width:100%; display:inline-table;' >
            
            <thead>
                <tr>
                    <td style='width:10px;'>#</td>
                    <td style='text-align:center; width:15%;'>QUANTITY</td>                    
                    <td style='text-align:center; width:10%;'>UNIT</td>                    
                    <td>ITEM</td>                    
                    <!--<td style='text-align:right; width:15%;'>COST</td>                    
                    <td style='text-align:right; width:15%;'>AMOUNT</td>  -->                 
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity += $r['quantity'];            
            $t_amount   += $r['amount'];            

           echo "
                <tr>
                    <td>$i</td>
                    <td style='text-align:center;'>".number_format($r['quantity'],2)."</td>
                    <td style='text-align:center'>$r[unit]</td>                    
                    <td>$r[stock]</td>                    
                    <!--<td style='text-align:center;'>".number_format($r['cost'],2)."</td>
                    <td style='text-align:right;'>".number_format($r['amount'],2)."</td>  -->                  
                </tr>
            ";

            $i++;
        }
    }

    echo "            
            </tbody>
            <tfoot>
                <tr>
                    <td></td>                    
                    <td style='text-align:center;'><span>".number_format($t_quantity,2)."</td>
                    <td></td>                    
                    <td></td>                    
                    <!--<td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_amount,2)."</td>-->
                </tr>
            </tfoot>
        </table>
    ";*/

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
<style type="text/css">
body
{		
	padding:0px;	
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
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

.table-header tbody td:nth-child(odd){
    padding-right: 20px;
}

.table-header tbody td:nth-child(even){
    padding-right: 40px;
}
.table-css{
    border-collapse: collapse;
    margin-top: 10px;
}

.table-css thead td{
    font-weight: bold;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
}
.table-css tfoot tr:first-child td{
    border-top: 1px solid #000;
    font-weight: bold;
}

.table-css caption{ font-weight: bold;}

.waste-cut td:nth-child(1){
    font-weight: bold;
    text-align: left;
    padding-right: 10px;
}
.waste-cut td:nth-child(2){
    text-align: right;
    padding-right: 10px;
}

.line_bottom {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:150px;
    font-size: 11px;
    text-align: center;
}
.line_bottom2 {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:380px;
    font-size: 11px;
    text-align: center;
}
.line_bottom3 {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:80px;
    font-size: 11px;
    text-align: center;
}

@media screen {
    div.divFooter {
        display: none;
    }
}
@media print {
    div.divFooter {
        position: fixed;
        bottom: 0;

        font-family: "Times New Roman";
        font-size: 11px;
    }
}
</style>
</head>
<body>
<div class="container">	    
        <p style="text-align:center; font-size:14px; font-weight:bold;">
            <?=$title?> <br>
            GATEPASS
        </p>     

        <div class="header" style="">
        	<table class='table-header'>
                <tbody>
                    <tr>
                        <td>Date:</td>
                        <td><?=lib::ymd2mdy($aTrans['date'])?></td> <!--<?=$aTrans['time']?>-->
                        <td>GP #: </td>
                        <td><?=str_pad($aTrans['gatepass_id'], 7,0,STR_PAD_LEFT)?></td>
						 <td></td>
                        <td></td>
                    </tr>                            
                    <tr>
                        <td>Employee:</td>
                        <td><?=lib::getEmployeeName($aTrans['employee_id'])?></td>                        

                        <td>Supplier:</td>
                        <td><?=lib::getAttribute('supplier','account_id',$aTrans['supplier_id'],'account')?></td>
						
                        <td>Visitor:</td>
                        <td><?=strtoupper($aTrans['visitor'])?></td>
                    </tr> 
                      <tr>
                        <td>Requesting Party:</td>
                        <td><?=$aTrans['project_name']?><?=lib::getAttribute('supplier','account_id',$aTrans['supplier_id'],'account')?><?=strtoupper($aTrans['visitor'])?></td>                        
						
                       <!-- <td>Visitor:</td>
                        <td></td>-->
                    </tr>     					
                                    
                </tbody>
                
            </table>
     	</div><!--End of header-->
        

        <p>
            Please allow <u><?=strtoupper($aTrans['visitor'])?></u> <u><?=lib::getEmployeeName($aTrans['employee_id'])?></u> <u><?=lib::getAttribute('supplier','account_id',$aTrans['supplier_id'],'account')?></u> to pass the gate:
		</p>
		<table style="width:100%">
		<tr>
	     <td>( <?php if( $aTrans['items_check'] ) echo "X"; ?> )With items listed under &nbsp&nbsp <u><?=$aTrans['reference']?></u></td>
			</tr>
			<tr></tr>
			<tr>
         <td>( <?php if( $aTrans['vehicle_check'] ) echo "X"; ?> )With equipment/vehicle&nbsp&nbsp<u><?=lib::getAttribute('productmaster','stock_id',$aTrans['stock_id'],'stock')?></u>&nbsp EUR#&nbsp<u><?=$aTrans['eur_reference']?></u></td>
			</tr>
     <!--<?=displayDetails($gatepass_id);?>-->

        <p>
            <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
                <tr>
                    <!--<td>Requesting Party:<p>
                       <input type="text" value= "<?=$aTrans['project_name']?>"class="line_bottom2" /><br>&nbsp;</p></td>
                    <td>Checked By:<p>
                        <input type="text" class="line_bottom" /><br>Guard on Duty</p></td>-->                    
                </tr>
            </table>
        </p>

        <p>
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td>Purpose:</td>
                        <td> ( <?php if( $aTrans['check_for_repair'] ) echo "X"; ?> )For Repair/Sample</td>
                        <td> ( <?php if( $aTrans['check_personal_items'] ) echo "X"; ?> )Personal Items/Use</td>                        
                    </tr>
                    <tr>
                        <td></td>
                        <td> ( <?php if( $aTrans['check_for_return'] ) echo "X"; ?> )For Return/Exchange</td>
                        <td> ( <?php if( $aTrans['check_chargeable_items'] ) echo "X"; ?> )Chargeable/Purchased Items</td>                        
                    </tr>
                    <tr>
                        <td></td>
                        <td> ( <?php if( $aTrans['check_for_project_use'] ) echo "X"; ?> )For Project Use</td>  
						<td> ( <?php if( $aTrans['check_borrowed_items'] ) echo "X"; ?> ) Borrowed Items</td>
                    </tr>
					<tr>
                        <td></td>
                        <td> ( <?php if( $aTrans['check_for_official_use'] ) echo "X"; ?> )Official Use</td>  
						<td> ( <?php if( $aTrans['check_purchase'] ) echo "X"; ?> ) Emission</td>
                    </tr>
					<tr>
                        <td></td>
                        <td> ( <?php if( $aTrans['check_for_hauling'] ) echo "X"; ?> )For Hauling/Pick-up</td>  
						<td> ( <?php if( $aTrans['check_for_rescue'] ) echo "X"; ?> ) For Rescue</td>
                    </tr>
					<tr>
                        <td></td>
                        <td> ( <?php if( $aTrans['check_for_pouring'] ) echo "X"; ?> )Pouring</td>  
						<td></td>
                    </tr>
                </tbody>
            </table>

        </p>

       <!-- <p>
            Remarks : <?=$aTrans['remarks']?>
        </p>-->

        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:50px;" class="summary">
            <tr>
                <td style="text-align:left;">Prepared and Approved by:<p>
                    <input type="text" class="line_bottom" /><br>HRD/Admin & Legal Department/Warehouse In-Charge</p></td>
               <td><p>
                    </p></td>
               <!-- <td>Approved By:<p>
                    <input type="text" class="line_bottom" /><br>G.M / O.M. / H.R.D./ RJR</p></td>-->
            </tr><p>
			
			<tr>
                <td style="text-align:left;">Checked by:<p>
                    <input type="text" class="line_bottom" />&nbsp&nbsp<br>P.M./P.I.C./Guard On Duty</p></td>
                <td style="text-align:left;">Time Out:
				    <input type="text" class="line_bottom3" /></td>
                <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br></p></td>
				<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br></p></td>
				<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br></p></td>
				<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br></p></td>
				<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br></p></td>
				<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<br></p></td>
				
            </tr>
			
        </table>
</div>
<div class="divFooter">
    F-WHS-010<br>
    Rev. 3 03/12/16
</div>
</body>
</html>
<script>
    printPage();
</script>