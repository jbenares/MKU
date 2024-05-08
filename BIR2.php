<?php
require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
	
$options=new options();	

$supplier_id	= $_REQUEST['supplier_id'];
$quarter 	= $_REQUEST['quarter'];
$year		= $_REQUEST['year'];
$cleared			= ($_REQUEST['cleared']) ? 1  : 0 ;
#$attr_date 	 	= ($cleared) ? "date_cleared" : "cv_date";
$attr_date 	 	= ($cleared) ? "date_cleared" : "check_date";
$cv_no			= $_REQUEST['cv_no'];


$supplier = $options->getAttribute('supplier','account_id',$supplier_id,'account');
$supplier_tin = $options->getAttribute('supplier','account_id',$supplier_id,'tin');
$supplier_address = $options->getAttribute('supplier','account_id',$supplier_id,'address');


$aTin = explode("-",$supplier_tin);


$tin_1 = $aTin[0];
$tin_2 = $aTin[1];
$tin_3 = $aTin[2];
$tin_4 = $aTin[3];

switch($quarter){
	case 1:
		$first_month 	= date("$year-01-01");
		$end_of_first_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($first_month)));
		
		$second_month 	= date("$year-02-01");
		$end_of_second_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($second_month)));
		
		$third_moth 	= date("$year-03-01");
		$end_of_third_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($third_moth)));
	
		break;
		
	case 2:
	
		$first_month 	= date("$year-04-01");
		$end_of_first_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($first_month)));
		
		$second_month 	= date("$year-05-01");
		$end_of_second_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($second_month)));
		
		$third_moth 	= date("$year-06-01");
		$end_of_third_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($third_moth)));
	
		break;
		
	case 3:
	
		$first_month 	= date("$year-07-01");
		$end_of_first_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($first_month)));
		
		$second_month 	= date("$year-08-01");
		$end_of_second_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($second_month)));
	
		$third_moth 	= date("$year-09-01");
		$end_of_third_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($third_moth)));
		
		break;
		
	case 4:
	
		$first_month 	= date("$year-10-01");
		$end_of_first_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($first_month)));
		
		$second_month 	= date("$year-11-01");
		$end_of_second_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($second_month)));
		
		$third_moth 	= date("$year-12-01");
		$end_of_third_month	= date("Y-m-d",strtotime("+1 month -1 day",strtotime($third_moth)));
		
		break;			
}

$a = $options->getBIRWitholdingTaxAttributes($supplier_id,$first_month,$end_of_first_month,$attr_date,NULL,$cv_no);
$first_month_vat = $a['vatable'];
$first_month_tax = $a['tax'];

$a = $options->getBIRWitholdingTaxAttributes($supplier_id,$second_month,$end_of_second_month,$attr_date,NULL,$cv_no);
$second_month_vat = $a['vatable'];
$second_month_tax = $a['tax'];


$a = $options->getBIRWitholdingTaxAttributes($supplier_id,$third_moth,$end_of_third_month,$attr_date,NULL,$cv_no);
$third_month_vat = $a['vatable'];
$third_month_tax = $a['tax'];

// 1st month
$a = $options->getBIR($supplier_id,$first_month,$end_of_first_month,$attr_date,NULL,$cv_no,1);
$first_month_vat_1 = $a['vatable'];
$first_month_tax_1 = $a['tax'];

$a = $options->getBIR($supplier_id,$first_month,$end_of_first_month,$attr_date,NULL,$cv_no,2);
$first_month_vat_2 = $a['vatable'];
$first_month_tax_2 = $a['tax'];

$a = $options->getBIR($supplier_id,$first_month,$end_of_first_month,$attr_date,NULL,$cv_no,5);
$first_month_vat_5 = $a['vatable'];
$first_month_tax_5 = $a['tax'];

$a = $options->getBIR($supplier_id,$first_month,$end_of_first_month,$attr_date,NULL,$cv_no,10);
$first_month_vat_10 = $a['vatable'];
$first_month_tax_10 = $a['tax'];
// end 1st month

// 2nd month
$a = $options->getBIR($supplier_id,$second_month,$end_of_second_month,$attr_date,NULL,$cv_no,1);
$second_month_vat_1 = $a['vatable'];
$second_month_tax_1 = $a['tax'];

$a = $options->getBIR($supplier_id,$second_month,$end_of_second_month,$attr_date,NULL,$cv_no,2);
$second_month_vat_2 = $a['vatable'];
$second_month_tax_2 = $a['tax'];

$a = $options->getBIR($supplier_id,$second_month,$end_of_second_month,$attr_date,NULL,$cv_no,5);
$second_month_vat_5 = $a['vatable'];
$second_month_tax_5 = $a['tax'];

$a = $options->getBIR($supplier_id,$second_month,$end_of_second_month,$attr_date,NULL,$cv_no,10);
$second_month_vat_10 = $a['vatable'];
$second_month_tax_10 = $a['tax'];
// end 2nd month

// 3rd month
$a = $options->getBIR($supplier_id,$third_moth,$end_of_third_month,$attr_date,NULL,$cv_no,1);
$third_month_vat_1 = $a['vatable'];
$third_month_tax_1 = $a['tax'];

$a = $options->getBIR($supplier_id,$third_moth,$end_of_third_month,$attr_date,NULL,$cv_no,2);
$third_month_vat_2 = $a['vatable'];
$third_month_tax_2 = $a['tax'];

$a = $options->getBIR($supplier_id,$third_moth,$end_of_third_month,$attr_date,NULL,$cv_no,5);
$third_month_vat_5 = $a['vatable'];
$third_month_tax_5 = $a['tax'];

$a = $options->getBIR($supplier_id,$third_moth,$end_of_third_month,$attr_date,NULL,$cv_no,10);
$third_month_vat_10 = $a['vatable'];
$third_month_tax_10 = $a['tax'];
// end 3rd month

//$total_vat = $first_month_vat + $second_month_vat + $third_month_vat;
$total_tax = $first_month_tax + $second_month_tax + $third_month_tax;

//total 1st month vat
$first_month_vat_total = round($first_month_vat_1,2) + round($first_month_vat_2,2) + round($first_month_vat_5,2) + round($first_month_vat_10,2);
//total 2nd month vat
$second_month_vat_total = round($second_month_vat_1,2) + round($second_month_vat_2,2) + round($second_month_vat_5,2) + round($second_month_vat_10,2);
//total 3rd month vat
$third_month_vat_total = round($third_month_vat_1,2) + round($third_month_vat_2,2) + round($third_month_vat_5,2) + round($third_month_vat_10,2);

//total months
$total_vat = $first_month_vat_total + $second_month_vat_total + $third_month_vat_total;


//total 1% tax
$one_percent_total = round($first_month_vat_1,2) + round($second_month_vat_1,2) + round($third_month_vat_1,2) ;
//total 2% tax
$two_percent_total = round($first_month_vat_2,2) + round($second_month_vat_2,2) + round($third_month_vat_2,2);
//total 5% tax
$five_percent_total = round($first_month_vat_5,2) + round($second_month_vat_5,2) + round($third_month_vat_5,2);
//total 10% tax
$ten_percent_total = round($first_month_vat_10,2) + round($second_month_vat_10,2) + round($third_month_vat_10,2);


//tax withheld
$withheld_one	= round($first_month_tax_1,2) + round($second_month_tax_1,2) + round($third_month_tax_1,2);
$withheld_two	= round($first_month_tax_2,2) + round($second_month_tax_2,2) + round($third_month_tax_2,2);
$withheld_five	= round($first_month_tax_5,2) + round($second_month_tax_5,2) + round($third_month_tax_5,2);
$withheld_ten	= round($first_month_tax_10,2) + round($second_month_tax_10,2) + round($third_month_tax_10,2);

$withheld_total = $withheld_one + $withheld_two + $withheld_five + $withheld_ten;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>

    <style type="text/css">
	
	table{
		border-collapse:collapse;	
	}

    .pageWrapper
    {
        margin:auto;    
    }
    .contentWrapper
    {
	margin: auto;
	width: auto;
	border-top-width: medium;
	border-right-width: medium;
	border-bottom-width: medium;
	border-left-width: medium;
	border-top-style: solid;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	border-top-color: #000;
	border-right-color: #000;
	border-bottom-color: #000;
	border-left-color: #000;
    }
    .headerWrapper
    {
        width:auto;
        height:2cm;
        border-bottom-width: medium;
        border-bottom-style: solid;
        border-bottom-color: #000;      
    }
    .logoHeaderWrapper
    {
        float:left;
        width:2.5in;
        height:2cm; 
    }
    .centerHeaderWrapper
    {   
        float:left;
        width:3.0in;
        height:1.8cm;       
        text-align:center;
        font-size:25px;
        padding-top:6px;
        font-family:Arial, Helvetica, sans-serif;
    }
    .labelHeaderWrapper
    {
        float:left;
        height:2cm; 
    }
    .labelWrapper
    {
        height:2cm;
        margin-left:3cm;    
    }
    .logo
    {
        float:left;
        height:2cm;
        width:2cm   
    }
    .logo img
    {
        margin-top:7px; 
        margin-left:3px;
    }
    .logoLabel
    {
        float:left;
        font-size:11px; 
        width:4cm;
        padding:1px;

    }
    .logoContentWrapper
    {
        margin-top:10px;    
    }
    .periodWrapper
    {
        height:0.375in;


    }
    .forThePeriodLabel
    {
        float:left;
        width:0.875in;  
        height:auto;
        font-family:Arial, Helvetica, sans-serif;
        font-size:8px;
        padding-top:4px;
    }
    .forTheTaxpayerLabel
    {
        float:left;
        width:1.25in;   
        height:auto;
        font-family:Arial, Helvetica, sans-serif;
        font-size:8px;
        padding-top:4px;
    }
    .forThePayeeLabel
    {
        float:left;
        width:1.25in;   
        height:15px;
        font-family:Arial, Helvetica, sans-serif;
        font-size:8px;
        padding-top:20px;
    }
    .periodFromMonthWrapper
    {
        width:1.475in;  
        height:0.375in;     
        float:left;
    }
    .taxpayerWrapper
    {
        width:4in;  
        height:0.375in;     
        float:left;
    }
    .payeeWraper
    {
        width:8in;  
        height:0.375in;     
        float:left;
    }
    .payeeAddressWraper
    {
    width:8.4in;
    height:0.375in;
    float:left;
    clear:both;
    margin-top:10px     

    }
    .payee2AddressWraper
    {
        width:8.5in;
        height:0.375in;
        clear:both;
        z-index: 100;   
        position:relative;
        top:100px;
    }
    .periodDateField
    {
    float:left;
    width:1.05cm;
    height:29px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-right-style: solid;
    border-bottom-style: solid;
    border-left-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    border-bottom-width: 1px;

    }
    .taxpayerField
    {
    float:left;
    width:1.5cm;
    height:29px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-right-style: solid;
    border-bottom-style: solid;
    border-left-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    border-bottom-width: 1px;
    }
    .zipcodeField
    {
    float:left;
    width:2cm;
    height:29px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-right-style: solid;
    border-bottom-style: solid;
    border-left-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    border-bottom-width: 1px;
    background-color: #FFF;
    }
    .taxpayerFieldDesign
    {
    width:18px;
    float:left;
    height:8px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-right-style: solid;
    border-bottom-style: none;
    border-left-style: none;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    }
    .periodDateFieldDesign
    {
    width:18px;
    float:left;
    height:8px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-right-style: solid;
    border-bottom-style: none;
    border-left-style: none;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    }
    .partOneLabelWrapper
    {
    width:auto;
    height:10px;
    clear:both;
    font-family:Arial, Helvetica, sans-serif;
    font-size:8px;
    border-top-width: medium;
    border-right-width: medium;
    border-bottom-width: medium;
    border-left-width: medium;
    border-top-style: solid;
    border-right-style: none;
    border-bottom-style: solid;
    border-left-style: none;
    }
    .spacer
    {
    width:8px; float:left; height:10px; padding-top:25px; font-size:8px;    
    }
	.spacerWithBG
    {
	width:8px;
	float:left;
	height:10px;
	padding-top:25px;
	font-size:8px;
	background-image: url(images/arrow.png);
	background-repeat: no-repeat;
	background-position: 0px 10px;
    }
    .payeeNameWrapper
    {
    float:left;
    width:5in;
    height:31px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-bottom-style: solid;
    border-left-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: bolder;
    text-transform: uppercase;
    color: #000;
    padding-top: 1px;
    }
    .payorNameWrapper
    {
    float:left;
    width:6.5in;
    height:31px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: bolder;
    text-transform: uppercase;
    color: #000;
    padding-top: 1px;
    border: 1px solid #000;
    }

    .payeeNameWrapperWithNoRightBorder
    {
    float:left;
    width:5in;
    height:31px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-bottom-style: solid;
    border-left-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: bolder;
    text-transform: uppercase;
    color: #000;
    padding-top: 1px;
    border-right-style: none;
    }

    .payeeAddressFieldWrapper
    {
    float:left;
    width:4.6in;
    height:31px;
    border-top-width: 1px;
    border-right-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-bottom-style: solid;
    border-left-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    font-weight: bolder;
    text-transform: uppercase;
    color: #000;
    padding-top: 1px;
    background-color: #FFF;
    border-right-style: solid;
    }

    .forTheForeignAddressLabel
    {
        float:left;
        width:1.5in;    
        height:15px;
        font-family:Arial, Helvetica, sans-serif;
        font-size:8px;
        padding-top:20px;
    }
    .tableWrapper
    {
        width:100%; 
    }
    .tableWrapper table
    {
        width:100%; 
        font-family:Arial, Helvetica, sans-serif;
        font-size:10px;
    }
    .tableWrapper table tr td
    {
    font-family:Arial, Helvetica, sans-serif;
    font-size:12px;

    }
    .notifyWrapper{
        font-family:Arial, Helvetica, sans-serif; font-size:8px;    
    }

    .signatoryFieldWrapper{
	display:inline-block;
    width:200px;
    height:50px;
    margin-right: 5px;
    margin-left: 5px;
	vertical-align:top;
    }
    .confirmFieldWrapper{
	display:inline-block;
	vertical-align:top;
    width:140px;
    height:50px;
    margin-right: 5px;
    margin-left: 5px;
    }
    .signatoryPosition{
    font-family:Arial, Helvetica, sans-serif;
    font-size:8px;
    text-align:center;
    border-top-width: 1px;
    border-right-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-top-style: solid;
    border-top-color: #000;
    border-right-color: #000;
    border-bottom-color: #000;
    border-left-color: #000;
    }
    .signatoryName{
    font-family:Arial, Helvetica, sans-serif;
    font-size:14px;
    text-align:center;
    font-weight:bolder;
    height: 20px;
    }

    .confirm{
        font-family:Arial, Helvetica, sans-serif;
        font-size:9px;      

    }

    .pageWrapper .contentWrapper .payeeAddressWraper .zipcodeField div strong {
	font-family: Arial, Helvetica, sans-serif;
}
    </style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>BIR Form 2307</title>
</head>
<body>
<div class="pageWrapper">
    <div class="contentWrapper">
        <div class="headerWrapper">
            <div class="logoHeaderWrapper">
                <div class="logo">
                    <img src="images/rp.jpg" width="60" height="60"/>

                </div>
                <div class="logoContentWrapper">
                    <div class="logoLabel">
                        Repubika ng Pilipinas
                    </div>
                    <div class="logoLabel">
                        Kagawaran ng Pananalapi
                    </div>
                    <div class="logoLabel">
                        Kawanihan ng Rentas Internas
                    </div>
                </div><!--End of logoContentWrapper-->
            </div><!--End of logoHeaderWrapper-->
            <div class="centerHeaderWrapper">
                <div>
                    Certificate of Creditable
                </div>
                <div>
                    Tax Withheld At Source
                </div>
            </div>
            <!--End of ceneterHeaderWrapper-->
            <div class="labelHeaderWrapper">
              <div class="labelWrapper">
                <div style="font-size:9px;margin-top:4px;margin-left:8px;">
                    BIR Form No.
                </div>
                    <div style="font-size:31px;">
                        2307
                    </div>
                    <div style="font-size:9px;margin-top:1px;margin-left:1px;">
                        September 2005(ENCS)
                    </div>
                </div><!--End of labelWrapper-->
            </div><!--End of labelHeaderWrapper-->
      </div><!--End of headerWrapper-->
        <div class="periodWrapper">
            <div class="forThePeriodLabel">
                <strong>1</strong> &nbsp;&nbsp;&nbsp;For the Period<br />
                &nbsp;&nbsp;&nbsp;<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            From
            </div><!--forThePeriodLabel-->
            <div class="periodFromMonthWrapper">
              <div class="periodDateField">
                  <div style="width:40px; height:21px;"><?=date("m",strtotime($first_month))?></div>
                    <div class="periodDateFieldDesign">
                    </div>
                </div>
                <div class="periodDateField">
                    <div style="width:40px; height:21px;">
                    	<?=date("d",strtotime($first_month))?>
                    </div>
                    <div class="periodDateFieldDesign">
                    </div>
                </div>
                <div class="periodDateField">
                    <div style="width:40px; height:21px;">
                    <?=date("Y",strtotime($first_month))?>
                    </div>
                    <div class="periodDateFieldDesign">
                    </div>
                </div>
            </div>
            <div style="width:4cm; float:left; height:10px; padding-top:25px; font-size:8px;">
                (MM/DD/YY) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To
            </div>
            <div class="periodDateField">
                <div style="width:40px; height:21px;">
                <?=date("m",strtotime($end_of_third_month))?>
                </div>
                <div class="periodDateFieldDesign">
                </div>
            </div>
            <div class="periodDateField">
                <div style="width:40px; height:21px;">
                <?=date("d",strtotime($end_of_third_month))?>
                </div>
                <div class="periodDateFieldDesign">
                </div>
            </div>
            <div class="periodDateField">
                <div style="width:40px; height:21px;">
                <?=date("Y",strtotime($end_of_third_month))?>
                </div>
                <div class="periodDateFieldDesign">
                </div>
            </div>
            <div style="width:4cm; float:left; height:10px; padding-top:25px; font-size:8px;">
                (MM/DD/YY)
            </div>

        </div><!--End of periodWrapper-->
        <!--2nd Row-->
        <div class="partOneLabelWrapper">
            <div style="float:left;">
                <strong>Part I</strong>
            </div>
            <center>
                <strong>Payee Information</strong>
            </center>
        </div>
        <!--3rd Row-->
        <div class="periodWrapper">
            <div class="forTheTaxpayerLabel">
                <strong>2</strong> &nbsp;&nbsp;&nbsp;Taxpayer<br />
                &nbsp;&nbsp;&nbsp;<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Identification Number 
            </div><!--forThePeriodLabel-->
			<div class="spacerWithBG"></div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;">
                	<?php
					if(!empty($tin_1)){
						echo $tin_1;
					}
                    ?>
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>
            <div class="spacer">
            </div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;">
             	  <?php
					if(!empty($tin_2)){
						echo $tin_2;
					}
                    ?>
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>
            <div class="spacer">
            </div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;">
                <?php
				if(!empty($tin_3)){
					echo $tin_3;
				}
				?>
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>
            <div class="spacer">
            </div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;">
                <?php
				if(!empty($tin_4)){
					echo $tin_4;
				}
				?>
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>


        </div>
        <!--4th Row-->
        <div style="clear:both; width:auto;">
            <div class="payeeWraper">
                <div class="forThePayeeLabel">
                    <strong>3</strong> &nbsp;&nbsp;&nbsp;Payee's Name<br />
                    &nbsp;&nbsp;&nbsp;<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                </div><!--forThePeriodLabel-->
				<div class="spacerWithBG"></div>
                <div class="payeeNameWrapperWithNoRightBorder">
                	<?=$supplier?>
                </div>
            </div>
        </div>
        <!--End of 4th Row-->
        <!--5th Row-->
        <div style="clear:both; width:auto; position:relative; top:10px; overflow:auto;">
            <div style="font-family:Arial, Helvetica, sans-serif; font-size:8px; margin-left:300px;">
                (Last Name, First Name, Middle Name for Individuals) (Registered Name for Non-Individuals)
            </div>
        </div>
        <!-- End of 5th Row-->
        <!--6th Row-->
        <div class="payeeInformationWrapper">
            <div class="payeeAddressWraper">
                <div class="forThePayeeLabel">
                    <strong>4</strong> &nbsp;&nbsp;&nbsp;Registered Address<br />
                    &nbsp;&nbsp;&nbsp;<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                </div><!--forThePeriodLabel-->
				<div class="spacerWithBG"></div>
                <div class="payeeNameWrapper">
                	<?=$supplier_address?>
              </div>
                <div style="float:left; width:80px; font-family:Arial, Helvetica, sans-serif; font-size:11px; padding-left:10px;">
                    ZipCode
                </div>
                <div class="spacerWithBG"></div>
                <div class="zipcodeField">
                    <div style="width:75px; height:21px;" contenteditable="true"></div>
                    <div class="taxpayerFieldDesign">
                    </div>
                    <div class="taxpayerFieldDesign">
                    </div>
                    <div class="taxpayerFieldDesign">
                    </div>

                </div>
            </div>
            <div style="clear:both; width:auto; position:relative; top:-7px; z-index:100;">
                <div class="payeeAddress2Wraper">
                
                    <div class="forTheForeignAddressLabel">
                        <strong>5</strong>&nbsp;&nbsp;&nbsp;Foreign Address<br />
                        &nbsp;&nbsp;&nbsp;<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    </div><!--forThePeriodLabel-->
					<div class="spacerWithBG"></div>
                    <div class="payeeAddressFieldWrapper"></div>
                    <div style="float:left; width:85px; font-family:Arial, Helvetica, sans-serif; font-size:11px; padding-left:26px;">
                        ZipCode
                    </div>
                    <div class="spacerWithBG"></div>
                    <div class="zipcodeField">
                        <div style="width:75px; height:21px;" contenteditable="true">
                        </div>
                        <div class="taxpayerFieldDesign">
                        </div>
                        <div class="taxpayerFieldDesign">
                        </div>
                        <div class="taxpayerFieldDesign">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--End of 6th ROW-->
        <!-- 7th Row-->
        <div class="partOneLabelWrapper">
            <center>
                <strong>Payor Information</strong>
            </center>
        </div>
        <!-- End of 7th ROW-->
        <div class="periodWrapper">
            <div class="forTheTaxpayerLabel">
                <strong>6</strong>&nbsp;&nbsp;&nbsp;Taxpayer<br />
                &nbsp;&nbsp;&nbsp;<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Identification Number 
            </div><!--forThePeriodLabel-->
<div class="spacerWithBG"></div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;"><strong>005                </strong></div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>
            <div class="spacer">
            </div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;"><strong>077
                </strong></div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>
            <div class="spacer">
            </div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;"><strong>159</strong>
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>
            <div class="spacer">
            </div>
            <div class="taxpayerField">
                <div style="width:58px; height:21px;"><strong>000</strong>
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
            </div>


        </div>
        <div style="clear:both; width:auto;">
            <div class="payeeWraper">
                <div class="forThePayeeLabel">
                    <strong>7</strong>&nbsp;&nbsp;&nbsp;Payor's Name<br />
                    &nbsp;&nbsp;&nbsp;<br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                </div><!--forThePeriodLabel-->
				<div class="spacerWithBG"></div>
                <div class="payorNameWrapper">dynamic builders &amp; construction co. (phil), inc
                </div>
            </div>
        </div>
        <div style="clear:both; width:auto; position:relative; top:10px; overflow:auto;">
            <div style="font-family:Arial, Helvetica, sans-serif; font-size:8px; margin-left:300px;">
                (Last Name, First Name, Middle Name for Individuals) (Registered Name for Non-Individuals)
            </div>
        </div>
        <div class="payeeAddressWraper">
            <div class="forThePayeeLabel">
                <strong>8</strong>&nbsp;&nbsp;&nbsp;Registered Address<br />
                &nbsp;&nbsp;&nbsp;<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            </div><!--forThePeriodLabel-->
			<div class="spacerWithBG"></div>
            <div class="payeeNameWrapper">bacolod-murcia road, brgy alijis, bacolod city</div>
            <div style="float:left; width:80px; font-family:Arial, Helvetica, sans-serif; font-size:11px; padding-left:10px;">
                ZipCode
            </div>
            <div class="spacerWithBG"></div>
            <div class="zipcodeField">
                <div style="width:75px; height:21px;"><strong>6100</strong> </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>
                <div class="taxpayerFieldDesign">
                </div>

            </div>
        </div>
        <div class="partOneLabelWrapper">
            <div style="float:left;">
                <strong>Part II</strong>
            </div>
            <center>
                <strong>Details of Monthly Income Payments and Tax Withheld for the Quarter</strong>
            </center>
        </div>
        <!--START OF TABLE-->
        <div  class="tableWrapper">
            <table cellspacing="0" cellpadding="2" border="1">
                <tr>
                    <th width="30%" rowspan="2">Income Payments Subject To <br />
                        Expanded Withholding Tax</th>
                    <th width="5%" rowspan="2">ATC</th>
                    <th colspan="4">AMOUNT OF INCOME PAYMENTS</th>
                    <th width="16%" rowspan="2">Tax Withheld<br />
                        For the Quarter</th>

                </tr>
                <tr>
                    <th width="13%">1st Month of <br />
                        the Quarter</th>
                    <th width="13%">2nd Month of <br />
                        the Quarter</th>
                    <th width="12%">3rd Month of <br />
                        the Quarter</th>
                    <th width="11%">Total</th>

                </tr>
                <tr>
                    <td>Payment made by top 20,000 private</td>
                    <td>
                        <div align="center">

                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                      
                        </div>
                    </td>
                    <td>
                        <div align="right">
            
                        </div>
                    </td>
                    <td>
                        <div align="right">
						
                        </div>
                    </td>
                    <td>
                        <div align="right">
						</div>
                    </td>
                </tr>
                <tr>
                    <td> corporation to their local/resident</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>supplier of goods 1%</td>
                    <td>
                        <div align="center">
						WC158
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($first_month_vat_1,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($second_month_vat_1,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($third_month_vat_1,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($one_percent_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($withheld_one,2)?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>or services 2%</td>
                    <td>
                        <div align="center">
						WC120
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($first_month_vat_2,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($second_month_vat_2,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($third_month_vat_2,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($two_percent_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($withheld_two,2)?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>or professionals 5%</td>
                    <td>
                        <div align="center">
						W1010
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($first_month_vat_5,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($second_month_vat_5,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($third_month_vat_5,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($five_percent_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($withheld_five,2)?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>or rentals 10%</td>
                    <td>
                        <div align="center">
						WC100
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($first_month_vat_10,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($second_month_vat_10,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($third_month_vat_10,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($ten_percent_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
						<?=number_format($withheld_ten,2)?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	<?=number_format($first_month_vat_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	<?=number_format($second_month_vat_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	<?=number_format($third_month_vat_total,2)?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	<?=number_format($total_vat,2,'.',',')?>
                        </div>
                    </td>
                    <td>
                        <div align="right">
                            <?=number_format($withheld_total,2,'.',',')?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Money Payments Subject to Withholding<br />
                        of Business Tax (Government & Private)</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
	                        
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        	
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td>
                        <div align="center">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                        </div>
                    </td>
                    <td>
                        <div align="right">
                      </div>
                  </td>
                </tr>
            </table>
        </div>
        <!--  END OF TABLE-->
        <div class="notifyWrapper">
            We declare, under the penalties of perjury, that this certificate has been made 
            in good faith, verified by me, and to the best of my knowledge and belief, is 
            true and correct,<br />
            pursuant to the provisions of the National Internal Revenue Code, as amended, 
            and the regulations issued under auhority thereof.
        </div>
        <!--Start of Signatories-->
        <div style="padding:10px; overflow:auto; border-bottom:1px #000 solid;">
            <center>
<div class="signatoryFieldWrapper">
                <div class="signatoryName" contenteditable="true">
                	Silvestre G. Lareza
                </div>
                <div class="signatoryPosition">
                    Payor/Payor's Authorized Representative/Accredited Tax Agent
                    (Signature Over Printed Name)
                </div>
            </div>
            <div class="signatoryFieldWrapper">
                <div class="signatoryName" contenteditable="true">
                	927-997-711
                </div>
                <div class="signatoryPosition">
                    TIN of Signatory
                </div>
            </div>
            <div class="signatoryFieldWrapper">
                <div class="signatoryName" contenteditable="true">
                	Finance
                </div>
                <div class="signatoryPosition">
                    Title/Position of Signatory
                </div>
            </div>
            <br />
            <div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Tax Agent Accreditation No./Attorney's Roll No. (if applicable)
                </div>
            </div>
            <div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Date of Issuance
                </div>
            </div>
            <div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Date of Expiry
                </div>
            </div>
</center>
        </div>
        <div>
            <div class="confirm">
                Conforme:
            </div>
            <div>
            </div>
            <center>
<div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Payee/Payee's Authorized Representativ/Accredited Tax Agent
                    (Signature Over Printed Name)
                </div>
            </div>
            <div class="confirmFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    TIN of Signatory
                </div>
            </div>
            <div class="confirmFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Title/Position of Signatory
                </div>
            </div>
            <div class="confirmFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Date Signed
                </div>
            </div>
            <br />
            <div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Tax agent Accreditation No./Attorney's Roll No. (if applicable)
                </div>
            </div>
            <div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Date of Issuance
                </div>
            </div>
            <div class="signatoryFieldWrapper">
                <div class="signatoryName">
                </div>
                <div class="signatoryPosition">
                    Date of Expiry
                </div>
            </div>

</center>
      </div>
    </div><!--End of contentWrapper-->
</div><!--End of pageWrapper-->

</body>
</html>
