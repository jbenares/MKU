<?php
include_once("../conf/ucs.conf.php");
require_once("../my_Classes/options.class.php");
include_once("../library/lib.php");
$options = new options();
$id  = $_REQUEST['id'];

$query="
	select
		co.*,
		e.tin,e.address,
		concat(employee_fname,' ',employee_mname,' ',employee_lname) as name
	from 
	    contracts_oncall as co left join employee as e on co.employeeID = e.employeeID				
	where
		co.oncall_id = '$id'
";

$result=mysql_query($query);
$aTrans=mysql_fetch_assoc($result);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>On-Call COE</title>
<style type="text/css">
.header2 {
	font-size: 44pt;
	font-style: normal;
	font-weight: bolder;
	color: #069;
	text-align: center;
	font-family: Tahoma, Geneva, sans-serif;
	letter-spacing: 2px;
	margin-left: 110px;
}
.header3 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 40pt;
	text-align: center;
	color: #069;
	font-weight: bolder;
	height: 100px;
	margin-left: 110px;
}
.header1 {
	font-size: 32pt;
	font-style: normal;
	font-weight: bold;
	color: #069;
	text-align: right;
	margin-right: 3%;
	font-family: Arial, Helvetica, sans-serif;
}
.container {
	font-size: 33pt;
	margin-top: 5%;
	margin-right: 8%;
	margin-bottom: 5%;
	margin-left: 0%;
	font-family: Arial, Helvetica, sans-serif;
}
.page-break {
	page-break-after: always;
}
</style>
</head>
<body>
       <div>
         <img src="../images/heading2.png" id="heading2" style="width:2000px; height:250px;"/>
     </div>
       <div class="container"><br>             
  			     <div class="header1">
                  OC- COS No. &nbsp; <?=str_pad($aTrans['oncall_id'],6,0,STR_PAD_LEFT)?>
           </div><br>	
             <div class="header2">
                  CONTRACT OF SERVICE
            </div><br>	
                <p style="margin-left:120px; text-align:justify; text-indent:50px">
                   This Contract entered into this &nbsp;_____________________&nbsp;
                   at Bacolod City,Philippines, by and between:       
              </p>                                    
                <p style="margin-left:120px; text-align:center">                                   
                  <b>DYNAMIC BUILDERS & CONSTRUCTION CO. (PHIL.), INC.,</b> a domestic 
                    corporation duly registered and existing under the laws of the Republic of the Philippines,
                    with business address at Bacolod-Murcia Road, Brgy. Alijis,  Bacolod City,
                    represented in this act by its General Manager,  ENGR. JOSE EDUARDO T. CRUZ,
                    herein referred to as the “FIRST PARTY”<br><br>	 - and - <br><br>	
                  <b><?=$aTrans['name']?></b> of legal age, with address <br>
                    at  &nbsp; <b><?=$aTrans['address']?></b> &nbsp; hereinafter referred<br> 
                    to as the “SECOND PARTY”.<br><br>
                    WITNESSETH THAT:
              </p>                                        
 							  <p style="margin-left:120px; text-align:justify">
                    HEREAS, the FIRST PARTY is a corporation engaged in construction and other related
                    business;<br><br>WHEREAS, the FIRST PARTY is willing to engage the services of the
                    SECOND PARTY and the SECOND PARTY is desirous of working with the FIRST PARTY as&nbsp;               
    								<b><?=$aTrans['position']?></b>&nbsp;for
   									<b><?=$options->getAttribute('projects','project_id',$aTrans['project_id'],'project_name')?></b>
                    Project subject to the following terms and conditions, to wit:
              </p>  
                <p style="margin-left:150px; text-align:justify"> 
                    1.	The SECOND PARTY’s scope of work is outlined in the approved
    								    <b>ROM No.:&nbsp;<?=$aTrans['rom_no']?></b>;<br><br>
                    2.	The period of employment of the SECOND PARTY shall be for a fixed period of
  								      <b><?=$aTrans['no_of_days']?></b>, such that after the said period expires, this contract
                        of employment shall automatically terminate and shall be rendered without any force and effect;<br><br>                      
                    3.	During the period of employment of the SECOND PARTY, he/she shall be paid the amount
                        of <b><?=number_format($aTrans['salary'],2)?></b> per day from
                        Monday to Saturday at eight hours per day. It is understood that if he/she does not report
                        for work, he/she shall not be entitled to his/her daily pay under the principle of “no work, no pay”;<br><br>
                    4.	The SECOND PARTY shall not be entitled to service incentive leave benefits since the
                        period of his/her employment is only for <b><?=$aTrans['no_of_days']?></b>;<br><br>
                    5.	The FIRST PARTY pays overtime compensation for work rendered in excess of regular
                        working hours ONLY if with approved prior written request to render overtime work;<br><br>
                    6.	The FIRST PARTY may deduct and retain from the compensation of the SECOND PARTY
             </p>  
        </div>                             
						<div>
							<img src="../images/footer.png" id="footer" style="vertical-align:middle; width:2000px; height:250px; margin-top:110px"/>
			  </div>											  
              <div class="page-break"></div>
            <div>
					    <img src="../images/heading2.png" id="heading2" style="vertical-align:middle; width:2000px; height:250px;"/>
		    </div><br>
            <div class="container">
                 <p style="margin-left:160px; text-align:justify">
                		the allowable deductions imposed by law and other obligations of the SECOND PARTY with the FIRST PARTY;<p>  
                 <p style="margin-left:120px; text-align:justify">      
                    7.	The SECOND PARTY shall abide with all the rules and regulations which shall be imposed 
                        by the FIRST PARTY and he is restricted from accepting any other work or contract during 
                        the tenure of this contract;<br><br>
                    8.	At the end of the&nbsp;<b><?=$aTrans['no_of_days']?></b>&nbsp; period or the termination
                        of this contract, the same ceases to have any force and effect;<br><br>
                    9.	The SECOND PARTY hereby attests that he understands the content of this contract and
                        he was not forced or coerced to sign this contract.
				      </p>
					       <p style="margin-left:110px; text-align:justify; text-indent:50px">
					           <b>IN WITNESS WHEREOF,</b> the parties have executed this document as of the date and place
                        first mentioned.<br><br>
              </p>
                  							<table style="width:88%; margin-left:120px">
                  										<tr>
                  											 <td><b><u>FIRST PARTY</u></td>
                  											 <td align="center"><b><u>SECOND PARTY</b></u></td>
                  									</tr>
                  						</table>
                  									<div style="margin-left:110px">
                  											<b>DYNAMIC BUILDERS<br> 
                  												 CONSTRUCTION CO. (PHIL.), INC.</b><br><br>
                  												 By:
                  								</div><br><br>											
                  							<table style="margin-left:80%; margin-left:120px;">
                  										<tr>
                  												<td width="50%">(Engr.) JOSE EDUARDO T. CRUZ	</td>
                  												<td width="30%" align="center"> <b><?=($aTrans['name']);?></b></td>
                  									</tr>
                  										<tr>
                  												<td>General Manager</td>
                  												<td align="center">Employee</td>
                  									</tr>
                  						</table><br><br>
                                    <div align="center">SIGNED IN THE PRESENCE OF:</div><br><br>
                                <table width="100%">
                                       <tr>
                                           <td align="center">_______________________	</td>
                                           <td align="center"> _______________________</td>
                                    </tr>
                              </table>  
                                  </div>
        <div>
           <img src="../images/footer.png" id="footer" style="vertical-align:middle; width:2000px; height:250px; margin-top:1000px"/>
      </div>  
             <div class="page-break"></div>
        <div>
           <img src="../images/heading2.png" id="heading2" style="vertical-align:middle; width:2000px; height:250px; margin-bottom:100px"/>
      </div>
					
   <div class="container">
				<div class="header3">
                            ACKNOWLEDGMENT
      </div><br><br>
                <p style="margin-left:120px; text-align:justify">                 
                            REPUBLIC OF THE PHILIPPINES)<br>
                            City of Bacolod&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)  SS.<br>
                            x----------------------------------------------------x <br><br>
                            
                            BEFORE ME, a Notary Public for and in the City of Bacolod, this _____________________, 
                            personally came and appeared the following persons with their corresponding valid IDs: <br><br> 
							</p>
                                  <table border="1" style="margin-left:200px;border-collapse:collapse;width:80%">
                                          <tr style="height:30px">
                                              <td width="40%">&nbsp;&nbsp;<i>Names</i></td>	
                                              <td width="40%">&nbsp;&nbsp;<i>Valid ID</i></td>
                                        </tr>
                                          <tr style="height:70px">
                                              <td>&nbsp;&nbsp;JOSE EDUARDO T. CRUZ</td>	
                                              <td>&nbsp;&nbsp;TIN 910-236-480</td>
                                        </tr>
                                          <tr style="height:70px">
                                              <td>&nbsp;&nbsp;<?=$aTrans['name']?></td>	
                                              <td>&nbsp;&nbsp;TIN <?=$aTrans['tin']?></td>
                                       </tr>
                              </table>
							<div style="margin-left:120px; text-align:justify">
                            known to me and to me known to be the same persons who executed the foregoing instrument
                            and acknowledged before me that the same is their free and voluntary act and deed.<br><br>
                            WITNESS MY HAND AND NOTARIAL SEAL.<br><br>
                            </div>
                            <div style="text-align:right; margin-right:110px">
                            Notary Public
                            </div>
                            <div style="margin-left:120px">
                            Doc. No.:	__________<br>
                            Page No.:	__________<br>
                            Book No.:	__________<br>
                            </div>
			</div>
			 <div>
           <img src="../images/footer.png" id="footer" style="vertical-align:middle; width:2000px; height:250px; margin-top:1200px"/>
			</div>  

	</body>
</html>