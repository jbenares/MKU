<?php
include_once("../conf/ucs.conf.php");
require_once("../my_Classes/options.class.php");
require_once('../my_Classes/numtowords.class.php');
include_once("../library/lib.php");

$options = new options();
$id  = $_REQUEST['id'];

$query="
	select
		ct.*,
		e.address,e.employee_statusID,e.tin,
		concat(employee_fname,' ',employee_mname,' ',employee_lname) as name

	from 
	    contracts_tsp as ct left join employee as e on ct.employeeID = e.employeeID
		
		
	where
		ct.tsp_id = '$id'
";

$result=mysql_query($query);
$aTrans=mysql_fetch_assoc($result);
$base_rate = $aTrans['base_rate'];
$allowance = $aTrans['allowance'];
$others = $aTrans['others'];
$tPackage = $base_rate + $allowance + $others;
$convert = new num2words();
$convert->setNumber($tPackage);
$words = strtoupper($convert->getCurrency());

	
#echo $query;
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<style type="text/css">
.container {
	margin-right: 7%;
	margin-left: 2%;
	font-size: 32pt;
	font-family: Arial, Helvetica, sans-serif;	
}
.header1 {
	font-size: 32pt;
	color: #069;
	text-align: right;
	margin-right:2%;
	line-height: normal;
	font-weight: bold;
	font-family: "Times New Roman", Times, serif;
}
.header2 {
	font-size: 46pt;
	color: #069;
	text-align: center;
	word-spacing: 1px;
	font-weight: bolder;
	font-family: Georgia, "Times New Roman", Times, serif;
}
.header3 {
	font-family: "Times New Roman", Times, serif;
	font-size: 40pt;
	color: #069;
	text-align: center;
	margin-left: 150px;
	margin-top:50px;
}
.header4 {
	font-size: 42pt;
	font-style: italic;
	font-weight: bold;
	text-align: center;
	margin-left: 150px;
	margin-top:50px;
}
.page-break {
	page-break-after: always;
}

</style>
</head>
<body>
	   <div>
		  <img src="../images/heading2.png" id="heading2" style="vertical-align:middle; width:2000px; height:250px;"/>
	</div>
				
			<div class="header1">
				 TSP- COE No. <?=str_pad($aTrans['tsp_id'],6,0,STR_PAD_LEFT)?>
		</div><br><br>
			
			<div class="header2">
				<b>CONTRACT OF EMPLOYMENT</b>
		</div><br>
			<div class="container" style=" margin-left:150px; text-indent:70px; text-align:justify">
				This Agreement entered into this &nbsp;____________________&nbsp; at Bacolod City,Philippines, by and between:
		</div>
			<div class="container">
				<p style="margin-left:150px; text-align:center">
					<b>DYNAMIC BUILDERS & CONSTRUCTION CO. (PHIL.), INC.,</b> a domestic<br>
					   corporation duly registered and existing under the laws of the Republic of the Philippines, 
					   with business address at Bacolod-Murcia Road, Brgy. Alijis,  Bacolod City, 
					   represented in this act by its General Manager,  ENGR. JOSE EDUARDO T. CRUZ,  
					   herein referred to as the “EMPLOYER”<br><br>
								 - and -<br><br>
																				 
						<b><?=$aTrans['name']?></b>,&nbsp; of legal age, with address <br>at &nbsp; <b><?=$aTrans['address']?></b>																				
						hereinafter <br>referred to as the “EMPLOYEE”. <br><br>
						WITNESSETH THAT:                                           
			</p>																	
				<p style="margin-left:150px; text-align:justify">
						WHEREAS, the EMPLOYER is a corporation engaged in construction and other related 
						business;<br><br>WHEREAS, the EMPLOYER has a construction project:
						<b><?=$options->getAttribute('projects','project_id',$aTrans['project_id'],'project_name')?></b>;                                                 <br><br>
																				
						WHEREAS, the EMPLOYER engages the services of the EMPLOYEE for the said project, 
						subject to the following terms and conditions, to wit:  <br><br>
																								
						NOW, THEREFORE, for and in consideration of the foregoing premises, the parties hereby agree as follows:<br><br>
                                              
						<b>I.&nbspJOB TITLE & DESCRIPTION</b><br><br>
                              
							The EMPLOYEE is hired as <b><?=$aTrans['position']?></b> 
							of the said project, effective <b><?=date("F d, Y",strtotime($aTrans['effectivity_date']))?></b>. 
							The job description of the EMPLOYEE’s duties and
							responsibilities is outlined in Annex “A” and made an integral part of this  contract. <br><br>
																				
							The EMPLOYEE shall promote and support the plans,  programs and policies imposed  by 
							the Management,as well as protect the interests and abide with the decisions of the EMPLOYER.<br><br>
																				
							<b>II.&nbsp;COMPENSATION AND OTHER BENEFITS</b><br><br>
								The EMPLOYEE shall be paid a monthly salary of <b><?=$words?>       
								(Php <?=number_format(($tPackage),2)?>)</b>,
								payable in two parts, every 15th and end of the
								month from which shall be deducted   the  social security contributions, withholding taxes,
								other government  mandated deductions and other obligations of the EMPLOYEE with the
								EMPLOYER.At this juncture, the EMPLOYEE gives the EMPLOYER the authority to make the deduction from
			</p>
		</div>  

       <div>
			<img src="../images/footer.png" id="footer" style="width:2000px; height:250px; margin-top:400px;"/>
    </div>  
		   <div class="page-break"></div>
	   <div>
			<img src="../images/heading2.png" id="heading2" style="width:2000px; height:250px;"/>
	</div>
			<div class="container">
				<p style="margin-left:150px; text-align:justify">
								the salary of  the EMPLOYEE. It is understood that the monthly pay 
								includes all the legal benefits due the EMPLOYEE as mandated by law.<br><br>
																					 
								Notwithstanding incidents when the EMPLOYER granted benefits, bonuses or allowances 
								other than those defined in this contract, such incidents are not to be considered as an 
								established practice or precedent and shall not form part of the benefits, bonuses and 
								allowances due and demandable under this Contract of Employment. <br><br>
                                  
							<b>III.&nbsp;EMPLOYMENT STATUS</b> <br><br>
								EMPLOYEE’s employment is until the completion of the project and/or until his services are
								no longer needed in the aforesaid project, i.e. when the phase of the project wherein he was 
								employed was completed, and/or his services are needed in another project.<br><br>
																		
							    It is understood therefore that the EMPLOYEE is not entitled to separation pay since his/her
								employment contract terminates at the end of the project or phase thereof wherein
								his service is no longer needed.<br><br>
                
							<b>IV.&nbsp;WORK HOURS</b><br>
							    Management prescribes the working hours and reserves the right to schedule as it may
								deem necessary to meet operational requirements. It is understood that the workdays shall 
								be from Monday to Saturday and EMPLOYEE must observe the working period as directed by Management.<br><br> 
                                   
							<b>V.&nbsp;MEDICAL/DRUG TESTS</b><br><br>
								The EMPLOYEE consents and agrees, upon request from the EMPLOYER, to undergo, at a 
							    government accredited institute to be nominated by the EMPLOYER, medical/drug tests at
								the expense of the EMPLOYEE at any time without prior notice.  This is to be carried out for
								purposes of determining the physical and mental fitness of the EMPLOYEE to perform 
								the functions of the job.<br><br>
								In the event the EMPLOYEE fails his medical/drug tests, the same shall be a valid cause for his 					                                    dismissal.<br><br>
                    
						    <b>VI.&nbsp;COMPANY RULES AND REGULATIONS</b><br><br>
							    All existing as well as future rules and regulations, policies and memoranda issued by 
								EMPLOYER are hereby deemed incorporated with this Contract.  The EMPLOYEE
								recognizes that he/she shall be bound by all such rules and regulations which EMPLOYER
								may issue from time to time.  The EMPLOYEE acknowledges his/her duty and responsibility
							    to be aware of the rules and regulations of the EMPLOYER regarding his/her employment
								and to fully comply with this in good faith.<br><br>
																	   
							<b>VII.&nbsp;DISCIPLINARY MEASURES</b><br><br>
								The EMPLOYEE hereby recognizes the right of the EMPLOYER to impose disciplinary
								measures or sanctions,  which may include, but are not limited to,  termination of 
							    employment,  suspensions, fines, salary deductions, withdrawal of benefits, loss of
								privileges, for any and all infraction, act or omission, irrespective of whether such infraction,
							    act or omission constitutes a ground for termination.
			</p>
	    </div> 

	   <div>
			<img src="../images/footer.png" id="footer" style="width:2000px; height:250px; margin-top:200px;"/>
	</div>  
		    <div class="page-break"></div>
	   <div>
			<img src="../images/heading2.png" id="heading2" style="width:2000px; height:250px;"/>
    </div>
						
	    <div class="container">
				<p style="margin-left:150px; text-align:justify">
							<b>VIII.&nbsp;BUSINESS CODE OF CONDUCT</b><br><br>
								The EMPLOYEE agrees to terminate all other business relationships or concerns that 
								he/she may be personally involved with that are in the same line of business as that 
								of the EMPLOYER. <br><br>
																			 
								The EMPLOYEE acknowledges being aware of the code of discipline mandated by the
								EMPLOYER and all the rules and regulations issued by the EMPLOYER concerning 
								the employment of the EMPLOYEE with the EMPLOYER.<br><br>
																			  
						    <b>IX.&nbsp;PRE-TERMINATION OF CONTRACTUAL EMPLOYMENT</b><br><br>
								It is understood that the employment of the EMPLOYEE terminates upon the completion of 
								the project, when his services is no longer needed and/or he is transferred to another
							    project. This is however without prejudice to the right of the EMPLOYER to terminate the
								services of the EMPLOYEE based on just and/or authorized cause imposed by law, including the following:
			</p>       
                                  
					 <p style="margin-left:250px; text-align:justify">
								1.	Intentional or unintentional violation of the EMPLOYER’s policies, rules, and regulations  as 			                                        embodied in the Company Rules and Regulations;<br><br>
								2.  Loss of trust and confidence reposed by the EMPLOYER on the EMPLOYEE;<br><br> 
								3.	When the EMPLOYEE is declared physically or mentally incapacitated
								    to perform his/her duties by the EMPLOYER’s appointed doctor;<br><br>
								4.	Other similar acts, omissions, and/or event.
																		 
				</p>
			<p style="margin-left:150px; text-align:justify">
							    The EMPLOYEE may terminate this contract on any of the ground provided by law. It is
							    understood, however that he/she shall not be entitled to any pay if the termination is at
								his/her instance. Moreover, in the event that the EMPLOYEE will pre-terminate this
								Contract, a 30-day written notice shall be served in advance to the EMPLOYER through the            
								HRD/Admin/Legal Department, pursuant to Article 285 of the Labor Code. Failure to comply 
								with the mandatory 30-day written notice shall hold EMPLOYEE liable for liquidated
								damages amounting to One thousand Pesos (Php1, 000.00) per day short of the 30-day
								period, in lieu of presentation of proof of actual damages, which liquidated damages shall be
								deducted from whatever amount that may still be due the EMPLOYEE, if any, without
								prejudice to the collection of said amount through whatever legal means should there be no
								such amount still owing in his/her favor. <br><br>
                                       
								The EMPLOYEE shall promptly account for, return, and deliver to the EMPLOYER at the
								EMPLOYER’s main office, his/her company I.D. Card and all the EMPLOYER’s property,
								which may have been assigned or entrusted to his/her care or custody. Likewise,
							    EMPLOYEE must officially turnover the properties, things and whatever intellectual
							    knowledge he acquired by virtue of his employment with the EMPLOYER. <br><br>
                                     
							<b>X.&nbsp;FINAL PAY</b><br><br>
							    The EMPLOYEE agrees that all amounts due to him/her as entitlements, e.g., wages,
								bonuses or other similar monetary benefits from the EMPLOYER at the time of the
							    EMPLOYEE’s separation,resignation or dismissal from employment, shall first be applied to
							    any and all outstanding obligations  that  the  EMPLOYEE may  have  with  the  EMPLOYER 
								without  prejudice to other recourses  of the EMPLOYER should the amount due to him/her
								be less than his/her outstanding obligation.
			</p>
		</div>                                      
                                       
			<div>
				<img src="../images/footer.png" id="footer" style="vertical-align:middle; width:2000px; height:250px; margin-top:50px;"/>
		</div>  
				<div class="page-break"></div>
			<div>
				<img src="../images/heading2.png" id="heading2" style="vertical-align:middle; width:2000px; height:250px;"/>
		</div>

			<div class="container">
			<p style="margin-left:150px; text-align:justify">
							<b>XI.&nbsp;CONFIDENTIALITY</b><br><br>
								EMPLOYEE undertakes the responsibility not to disclose confidential information he
							    acquired relative to his employment with the EMPLOYER. In the event he made suc 
							    disclosure,eliberately or negligently, the same is a valid ground for his termination.
								Moreover, in the event his employment is severed, he continues to undertake not to divulge
							    company secrets or any information acquired by him otherwise, he shall be liable for damages.<br><br> 
								Confidential information is any information belonging to EMPLOYER that could be used by
								people outside the company to the detriment of EMPLOYER.  Appropriate steps should be 
								taken by the EMPLOYEE in handling all the business information of the EMPLOYER in
								order to minimize the possibility of unauthorized disclosure.<br><br>
																									 
							<b>XII.&nbsp;SEPARABILITY CLAUSE</b><br><br>
								If any provision of this document shall be construed to be illegal or invalid, they shall not
								affect the legality, validity, and enforceability of the other provisions of this document; the 
								illegal or invalid provision shall be deleted from this document and no longer incorporated
								herein but all other provisions of this document shall continue.<br><br>
																																 
							<b>XIII.&nbsp;ENTIRE AGREEMENT</b><br><br>
								This contract represents the entire agreement between the EMPLOYER and the EMPLOYEE and
								supersedes all previous oral or written communications, representations,
						        or agreements between the parties.<br><br>
																																											
									<b>IN WITNESS WHEREOF</b>, the parties have executed this document as of the date and place 	
										first mentioned.<br><br>
		 
									<b>DYNAMIC BUILDERS <br>CONSTRUCTION CO. (PHIL.), INC.</b><br><br>					
											By:<br><br>
		</p>                       
																														
										<table style="width:85%; margin:170px">
												<tr>
													<td>(Engr.) JOSE EDUARDO T. CRUZ</td>
													<td><b><?=$aTrans['name']?></b></td>
											</tr>
												<tr>
													<td>General Manager</td>
													<td>Employee</td>
											</tr>
									</table><br>                      	

				<div style="margin-left:170px; text-align:center">  
								SIGNED IN THE PRESENCE OF:
			</div><br><br>
										<table width="100%" border="0">
												<tr>
													<td align="center"><b>SILVESTRE G. LAREZA</td>
													<td align="center"><b>SELWYN ROSS T. MONTES</td>
											</tr>
									</table>
																						  
																	 </div> 
										<div>
											<img src="../images/footer.png" id="footer" style="width:2000px; height:250px; margin-top:400px"/>
								   </div>  
											<div class="page-break"></div>
										<div>
											<img src="../images/heading2.png" id="heading2" style="width:2000px; height:250px;"/>
								  </div>
						
             <div class="container">
                        
                    <div class="header3">           
                                ACKNOWLEDGMENT       
                 </div>
		   <p style="margin-left:150px; text-align:justify">
                                REPUBLIC OF THE PHILIPPINES)<br>
                                City of Bacolod&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp; SS.<br>
                                x----------------------------------------------------x <br><br> 

                                BEFORE ME, a Notary Public for and in the City of Bacolod, this ________________,
                                personally came and appeared the following persons with their corresponding valid IDs:
        </p> 
                                        <table border="1" style="margin-left:150px; width:90%;border-collapse:collapse;">
                                                    <tr>
                                                      <td width="35%">Names</td>
                                                      <td width="45%">Valid ID</td>
                                               </tr>
                                                    <tr>
                                                      <td>JOSE EDUARDO T. CRUZ</td>
                                                      <td>TIN 910-236-480</td>
                                               </tr>
                                                    <tr>
                                                      <td><?=$aTrans['name']?></td>
                                                      <td>TIN <?=$aTrans['tin']?></td>
                                               </tr>
                                    </table>
            <div style="margin-left:150px; text-align:justify">            	
                                known to me and to me known to be the same persons who executed the foregoing instrument 
                                and acknowledged before me that the same is their free and voluntary act and deed. <br><br> 
                                    
							    WITNESS MY HAND AND NOTARIAL SEAL. 
        </div>
           <p style=" margin-left:80%">Notary Public </p>
            <div style="margin-left:150px; text-align:justify">
						        Doc. No.:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________ <br> 
						        Page No.:&nbsp;&nbsp;&nbsp;&nbsp;__________ <br> 
                                Book No.:&nbsp;&nbsp;&nbsp;&nbsp;__________ <br> 
                                Series of &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________<br><br>
       </div>                        
           <div>
               <img src="../images/footer.png" id="footer" style="width:2000px; height:250px; margin-top:1550px"/>
        </div>  
               <div class="page-break"></div>
           <div>
				<img src="../images/heading2.png" id="heading2" style="width:2000px; height:250px;"/>
	    </div>                                   
               <div class="container">

                          <table style ="margin-left:170px">
                                    <tr>
                                        <td><b>Designation:</b></td>
                                        <td><b><?=$aTrans['position']?></b></td>  
                                </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>  
                                </tr>
                                    <tr>
                                        <td><b>Project:</b></td>
                                        <td><b><?=$options->getAttribute('projects','project_id',$aTrans['project_id'],'project_name')?></b></td>  
                                </tr>
                        </table>         
                         <div class="header4"> Annex “B”</div>
                             <div style="margin-left:150px; text-align:center"><br>
                                 <b> <u>SALARY PACKAGE</u><br>
                                        Php <u><?=number_format(($tPackage),2)?></u> </b><br><br> 
						    </div>
                                              
                                   <table style="margin-left:350px; width:90%;">
                                             <tr>
                                                 <td align="left">Base Rate</td>
                                                 <td>&nbsp;</td>
                                                 <td width="40%" align="left">Php <u><?=number_format(($base_rate),2)?></u></td>
                                        </tr>
                                             <tr>
                                                 <td>&nbsp;</td>  
                                        </tr>
                                             <tr>
                                                 <td align="left">Allowance</td>
                                                 <td>&nbsp;</td>
                                                 <td align="left">Php  <u><?=number_format(($allowance),2)?></u></td>
                                        </tr>
                                             <tr>
                                                 <td>&nbsp;</td>  
                                        </tr>
                                             <tr>
                                                 <td align="left">Others	</td>
                                                 <td>&nbsp;</td>
                                                 <td align="left">Php  <u><?=number_format(($others),2)?></u></td>
                                        </tr>
                              </table> <br><br>
            <p style="margin-left:150px; text-align:justify;">
						       *Note: <b><?=$options->getAttribute('employee_status','employee_statusID',$aTrans['employee_statusID'],'employee_status')?></b> - Paid EMPLOYEE<br><br>
	    </p>
            <p style="margin-left:150px; text-align:justify; text-indent:80px"> 
                                Additional allowances and benefits may be granted and approved by the<br> Management.
        </p> <br><br>
                        
           <div style="margin-left:1000px">
                                With my conformity:<br><br>
                                 <u><?=$aTrans['name']?></b></u>
	   </div>
          </div>
            <div>
            	<img src="../images/footer.png" id="footer" style="width:2000px; height:250px; margin-top:1550px"/>
         </div>  
</div>			  
</body>
</html>