<?php
include_once("../conf/ucs.conf.php");
require_once("../my_Classes/options.class.php");
require_once('../my_Classes/numtowords.class.php');
include_once("../library/lib.php");
$options = new options();
$id  = $_REQUEST['id'];

$query="
	select
		ca.*,
		e.address,e.tin,
		concat(employee_fname,' ',employee_mname,' ',employee_lname) as name
	from 
	    contracts_alp as ca left join employee as e on ca.employeeID = e.employeeID		
	where
		ca.alp_id = '$id'
";

$result=mysql_query($query);
$aTrans=mysql_fetch_assoc($result);
$convert = new num2words();
$convert->setNumber($aTrans['salary']);
$words = strtoupper($convert->getCurrency());
	
#echo $query;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<style type="text/css">
.header2 {
	font-size: 40pt;
	font-style: normal;
	font-weight: bolder;
	color: #069;
	text-align: center;
	letter-spacing: 3px;
	font-family: Georgia, "Times New Roman", Times, serif;
}
.header3 {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 40pt;
	font-weight: normal;
	color: #069;
	text-align: center;
	margin-left: 110px;
	letter-spacing: 2px;
}
.header1 {
	font-size: 30pt;
	font-style: normal;
	font-weight: bolder;
	color: #069;
	text-align: right;
	margin-right: 8%;
	font-family: Georgia, "Times New Roman", Times, serif;
}
.container {
	font-size: 26pt;
	margin-top: 5%;
	margin-right: 5%;
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
				  <img src="../images/heading2.png" id="heading2" style="width:1600px; height:250px; margin-bottom:30px"/>
			</div>
          <div class="container"> 
              <div class="header1">
                          ALP- COS No.&nbsp; <?=str_pad($aTrans['alp_id'],7,0,STR_PAD_LEFT)?>
		    </div><br>
                                
			 <div class="header2">
                          CONTRACT OF SERVICE
            </div><br>
                                 
                  <p style="margin-left:120px; text-indent:70px; text-align:justify">
                           This Contract entered into this &nbsp;_____________________&nbsp;                               
                           at Bacolod City, Philippines, by and between
                </p>
                  <p style="text-align:center; margin-left:110px">          
                        <b>DYNAMIC BUILDERS & CONSTRUCTION CO. (PHIL.), INC.,</b>a domestic<br> 
                           corporation duly registered and existing under the laws of the Republic of the Philippines,<br> 
                           with business address at Bacolod-Murcia Road, Brgy. Alijis,  Bacolod City,<br> 
                           represented in this act by its General Manager,  ENGR. JOSE EDUARDO T. CRUZ,<br>  
                           herein referred to as the “FIRST PARTY”<br><br>- and -<br><br>           
                           <b><?=$aTrans['name']?></b>&nbsp;of legal age, with address <br>
                            at &nbsp;  <b><?=$aTrans['address']?></b><br><br>hereinafter referred to as the “SECOND PARTY”.<br><br><br>
                                                 WITNESSETH THAT:
                </p>
                  <p style="margin-left:110px; text-align:justify">  					 
                                WHEREAS, the FIRST PARTY is a corporation engaged in construction and other related business;<br><br>     
                                WHEREAS, the FIRST PARTY has a construction project
                                <b><?=$options->getAttribute('projects','project_id',$aTrans['project_id'],'project_name')?></b>;<br><br>
                                WHEREAS,  the  SECOND  PARTY   is   desirous   of  working   with    the  FIRST   PARTY   as 
                                <b><?=$aTrans['position']?></b>;<br><br>
                                WHEREAS, the FIRST PARTY is willing to engage the services of the SECOND PARTY for the
                                said project as &nbsp;<b><?=$aTrans['position']?></b> &nbsp;to commence on &nbsp;<b>
                                <?=date("F d, Y",strtotime($aTrans['start_date']))?></b>
                                subject to the following terms and conditions, to wit:<br><br>
                </p>              
                   <div style="text-align:justify; margin-left:110px">
                                1.	The first month shall be the evaluation period, during which the SECOND PARTY’s 
                                competence, work attitude and diligence will be evaluated;<br><br>
                                
                                2.Upon passing the evaluation, the period of employment of the SECOND PARTY shall be
                                 until the completion of the project/phase thereof and/or until his services are no longer
                                 needed in the aforesaid project and/or until his services are needed in another project.<br><br>
                                3.	Regardless of the length of time it takes the project to be finished, SECOND PARTY is not 
                                entitled to separation pay since his employment terminates at the end of the project / phase
                                thereof or where his services are no longer needed;<br><br>
   			    </div>
                       <div>
                          <img src="../images/footer.png" id="footer" style="width:1600px; height:250px; margin-top:100px"/>
                      </div>  
                          <div class="page-break"></div>
                       <div>
						  <img src="../images/heading2.png" id="heading2" style="width:1600px; height:250px;"/>
			          </div>
                        
               	  <p style="text-align:justify; margin-left:110px">
                                4.	At any time during the period of this contract, the FIRST PARTY may terminate the
                                    services of the SECOND PARTY under the following reasons:
				</p>
                  <p style="text-align:justify; margin-left:170px">
                                a.	Misdemeanor, misconduct or insubordination or commission of act/s prejudicial 
                                    to the FIRST PARTY; OR<br><br>
                                b.	Where the services of SECOND PARTY as &nbsp;<b> <?=$aTrans['position']?></b> 
                                    are deemed not necessary or no longer needed by the project.<br><br>
                                c.	Other cases provided for by law.
                </p>    
                  <p style="text-align:justify; margin-left:110px">  
                                5.	During the period of employment of the SECOND PARTY, he shall be paid the amount of 
                                 <b><?=$words?></b>             
                                (Php <?=number_format($aTrans['salary'],2)?>) per day from Monday to Saturday at eight hours per day. It is
                               	understood that if he does not report for work, he shall not be entitled to his 
                                daily pay under the principle of “no work, no pay”.<br><br>
								
                                6.	The FIRST PARTY pays overtime compensation for work rendered in excess of regular
                                working hours ONLY if with approved prior written request to render overtime work.<br><br>
                			
                                7.	The FIRST PARTY may deduct and retain from the compensation of the SECOND PARTY 
                                the allowable deductions imposed by law and other obligations of the SECOND PARTY
                                with the FIRST PARTY.<br><br>
                
                                8.	The SECOND PARTY shall abide with all the rules and regulations which shall be imposed
                                 by the FIRST PARTY and he is restricted from accepting any other work or contract during
                                 the tenure of this contract;<br><br>
                
                                9.	The SECOND PARTY hereby attests that he understands the content of this contract and
                                he was not forced or coerced to sign this contract.
                </p>
                 
                    <p style="text-indent:30px; text-align:justify; margin-left:110px">        
                             <b>IN WITNESS WHEREOF</b>, the parties have executed this document as of the date and place first mentioned.<br><br>    
                </p>
                                     		<table style="margin-left:110px; width:80%;">
                                              <tr>
                                                <td><b><u>FIRST PARTY</b></u>	</td>
                                                <td style="text-align:center"><b><u> SECOND PARTY </b></u><br></td>
                                              </tr>
                                            </table>
                                        <div style="margin-left:110px">
                                             <b>DYNAMIC BUILDERS & <br>
                                              CONSTRUCTION CO. (PHIL.), INC.</b><br><br>
							                 By:<br><br>	
						              </div>
                                           <table style="margin-left:110px; width:80%">
                                              <tr>
                                                <td>(Engr.) JOSE EDUARDO T. CRUZ  </td>
                                                 <td><b><?=($aTrans['name']);?></b></td>           
                                              <tr>
                                                <td>General Manager</td>
                                                <td>Employee</td>
                                              </tr>
                                        </table><br><br>
										
                                      <div align="center">
                                        SIGNED IN THE PRESENCE OF:
                                      </div><br><br>	
                                                <table width="100%" border="0">
                                                  <tr>
                                                    <td align="center">_______________________ </td>
                                                    <td>_______________________</td>
                                                  </tr>
                                             </table>
                       <div>
                           <img src="../images/footer.png" id="footer" style=" margin-top:150px;width:1600px; height:250px"/>
                     </div>  		
                           <div class="page-break"></div>
                       <div>
						   <img src="../images/heading2.png" id="heading2" style="width:1600px; height:250px; margin-bottom:150px"/>
				    </div>
                            <div class="header3">
                                ACKNOWLEDGMENT                              
                          </div></p>
                            				<p style="margin-left:110px">
                            							REPUBLIC OF THE PHILIPPINES)<br>
                            							City of Bacolod&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp)  SS.<br>
                            							x----------------------------------------------------x  <br><br>	
                            			  </p>
              				       <div style="margin-left:110px; text-align:justify">
              							BEFORE ME, a Notary Public for and in the City of Bacolod, this _____________________,
              							personally came and appeared the following persons with their corresponding valid IDs: 
              					</div><br>
                                       <table border="1" width="80%" style="margin-left:110px;border-collapse:collapse"> 
                                          		<tr>
                                                  <td>&nbsp;&nbsp;<i>Names</i></td>	
                                                  <td>&nbsp;&nbsp;<i>Valid ID</i></td>
                                          	</tr>
                                         	   <tr>
                                                <td>&nbsp;&nbsp;JOSE EDUARDO T. CRUZ</td>	
                                                <td>&nbsp;&nbsp;TIN 910-236-480</td>
                                        	</tr>
                                         	  <tr>
                                                <td>&nbsp;&nbsp;<?=($aTrans['name']);?></td>	
                                                <td>&nbsp;&nbsp;TIN <?=($aTrans['tin']);?></td>
                                          </tr>     
                                  </table>
              					   <div style="margin-left:110px; text-align:justify">
              							known to me and to me known to be the same persons who executed the foregoing instrument
              							and acknowledged before me that the same is their free and voluntary act and deed.<br><br>	
              								WITNESS MY HAND AND NOTARIAL SEAL.<br><br>	
              					</div>
					   <div style="text-align:right">
                           Notary Public<br>
                    </div>
                       <div style="margin-left:110px">
                           Doc. No.:	__________<br>
                           Page No.:	__________<br>
                           Book No.:	__________<br>
						   Series of:	__________<br>
                    </div>
             <div class="image">
	            <img src="../images/footer.png" id="heading2" style="width:1600px; height:200px; margin-top:1100px;"/>
	     </div>	
</div>
</body>
</html>