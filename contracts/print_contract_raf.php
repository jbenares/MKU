<?php
include_once("../conf/ucs.conf.php");
require_once("../my_Classes/options.class.php");
require_once('../my_Classes/numtowords.class.php');
include_once("../library/lib.php");

$options = new options();
$id  = $_REQUEST['id'];
$separation = $_REQUEST['separation'];

$query="
	select
		cr.*,
		e.employee_statusID,e.tin, e.address,
		concat(employee_fname,' ',employee_mname,' ',employee_lname) as name

	from 
	    contracts_raf as cr left join employee as e on cr.employeeID = e.employeeID	
		
	where
		cr.raf_id = '$id'
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
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rank And File</title>
<style type="text/css">

.container {
	margin-top: 1%;
	margin-right: 10%;
	margin-bottom: 0%;
	font-size: 30pt;
	font-family: Arial, Helvetica, sans-serif;
}
.header1 {
	font-size: 32pt;
	color: #069;
	text-align: right;
	margin-right:0%;
	font-weight: bold;
	font-family: "Times New Roman", Times, serif;
}
.header2 {
	font-size: 46pt;
	color: #069;
	text-align: center;
	word-spacing: 2px;
	font-weight: bolder;
	margin-left: 120px;
	font-family: "Times New Roman", Times, serif;
}
.header3 {
	font-family: "Times New Roman", Times, serif;
	font-size: 42pt;
	color: #06C;
	letter-spacing: 2px;
	text-align: center;
	margin-left: 170px;
}
.page-break {
	page-break-after: always;
}
.header4 {
	font-size: 40pt;
	font-style: italic;
	font-weight: bold;
	text-align: center;
	margin-left: 170px;
    margin-top: 50px;
}
</style>
</head>
<body>
      <div>
         <img src="../images/heading2.png" id="heading2" style="width:1900px;height:250px;"/>
  </div>
           <div class="container">
				<div class="header1">
                            RAF-COE No. &nbsp; <?=str_pad($aTrans['raf_id'],6,0,STR_PAD_LEFT)?>
            </div><p>

                <div class="header2">
                            CONTRACT OF EMPLOYMENT
			</div><p>

                <div style="margin-left:120px; text-indent:50px; text-align:justify">
                            This Agreement entered into this 
                             &nbsp;_________________&nbsp; 
                             at Bacolod City,Philippines, by and between:
            </div>
                
                <p style="margin-left:120px; text-align:center">
                          <b>DYNAMIC BUILDERS & CONSTRUCTION CO. (PHIL.) INC.,</b> a domestic <br>
                             corporation duly registered and existing under the laws of the Republic of the Philippines, with
                             business address at Bacolod-Murcia Road, Brgy. Alijis, Bacolod City, represented in 
                             this act by its General Manager,  ENGR. JOSE EDUARDO T. CRUZ, 
                             hereinafter referred to as the “EMPLOYER”<br><br> - and -<br><br>
                             <b><?=$aTrans['name']?></b>,&nbsp; of legal age, with address at<br>  &nbsp; 
                             <b><?=$aTrans['address']?></b>,&nbsp; hereinafter referred to as the<br> “EMPLOYEE”.
            </p>

                <div align="center">              
                             <b>WITNESSETH THAT:</b>         
        </div>
                 <p style="margin-left:120px; text-align:justify; text-indent:70px">   
                            WHEREAS, the EMPLOYER is a corporation engaged in construction and other related business;
            </p>
                 <p style="margin-left:120px; text-align:justify; text-indent:70px">
                            WHEREAS, the EMPLOYER is interested in engaging the services of the EMPLOYEE
                            as &nbsp;<b><?=$aTrans['position']?></b>;
            </p>
                <p style="margin-left:120px; text-align:justify">
                             NOW, THEREFORE, for and in consideration of the foregoing premises, the parties hereby agree as follows:
            </p>                   
                <p style="margin-left:170px; text-align:justify">		
                             1.	The EMPLOYER agrees to employ EMPLOYEE and EMPLOYEE agrees to remain 
                                in the employ of EMPLOYER under the terms and conditions hereinafter set forth.<br><br>
    
                             2.	The EMPLOYEE’s employment is as &nbsp; <b><?=$aTrans['position']?></b>&nbsp;under the
                                <b><?=$options->getAttribute('projects','project_id',$aTrans['project_id'],'project_name')?></b>
                                &nbsp; effective <b><?=date("F d, Y",strtotime($aTrans['effectivity_date']))?></b>. 
                      	        A more specific job description of the EMPLOYEE’s duties and responsibilities is outlined in
                                Annex “A” and made an integral part of this contract.<br><br>
    
                             3.	The EMPLOYEE’s work performance shall be regularly evaluated in writing by his/her
                                immediate superior or such other representative appointed by the EMPLOYER. The
                                EMPLOYEE further agrees that it is the prerogative of the EMPLOYER to evaluate
                                his/her performance.<br><br> 
    
                                The salary of the EMPLOYEE is payable in 
                                two parts, every 15th and end of the month from which shall be deducted the social security 
                                contributions, withholding taxes, other government mandated deductions and other
                                obligations of the EMPLOYEE with the EMPLOYER. At
            </p>
        </div>            
     </div>           
        <div>
           <img src="../images/footer.png" id="footer" style="width:1900px; height:250px; margin-top:450px;"/>
     </div>  
                <div class="page-break"></div>
        <div>
           <img src="../images/heading2.png" id="heading2" style="vertical-align:middle; width:1900px; height:220px;"/>
	</div>
                        <div class="container">
                        <p style="margin-left:170px; text-align:justify">                  
                        this juncture, the EMPLOYEE gives the EMPLOYER the authority to make the deduction
                        from the salary of the EMPLOYEE. <br><br>
    
                        Notwithstanding incidents when the EMPLOYER granted benefits, bonuses or allowance
                        other than those defined in this contract, such incidents are not to be considered as an 
                        established practice or precedent and shall not form part of the benefits, bonuses and 
                        allowances due and demandable under this Contract of Employment.<br><br>
    
                        4.	The EMPLOYEE shall work for a period of eight (8) hours per day from Monday to
                        Saturday. Management prescribes the work schedule, and it reserves the right to
                        change the schedule as it may deem necessary to meet operational requirements.<br><br>
    
                        5.	The EMPLOYEE recognizes EMPLOYER’s right and prerogative to, without limitation,
                        assign and re-assign him/her to perform such other tasks within EMPLOYER’s 
                        organization,  in other departments/offices/projects,as may be deemed  necessary or in the 
                        interest of the service. Endorsements to this effect are likewise made part of this contract.<br><br>
    
                        6.	The EMPLOYEE consents and agrees, upon request from the EMPLOYER, to undergo,
                        at a government accredited institute to be nominated by the EMPLOYER,
                        medical/drugtests at the expense of the EMPLOYEE at anytime without prior notice. This
                        is to be carried out for purposes of determining the physical and mental fitness of the
                        EMPLOYEE to perform the functions of the job. <br><br>
    
                        In the event the EMPLOYEE fails his medical/drug tests, the same shall be a valid cause 
                        for his dismissal.<br><br>
                        
                        7.	All existing as well as future rules and regulations, policies and memoranda issued by 
                        the EMPLOYER are hereby deemed incorporated with this Contract.  The EMPLOYEE 
                        recognizes that he/she shall be bound by all such rules and regulationsand to such
                        policies which the EMPLOYER may issue from, time to time. The EMPLOYEE
                        acknowledges his/her duty and responsibility to be aware of the EMPLOYER’s rules and
                        regulations regarding his/her employment and to fully comply with these in good faith.<br><br>
    
                        8.	The EMPLOYEE hereby recognizes the EMPLOYER’s right to impose disciplinary
                         measures or sanctions, which may include, but are not limited to, termination of 
                         employment, suspensions, fines, salary deductions, withdrawal of benefits, loss of
                         privileges, for any and all infraction, act or omission, irrespective of whether such
                         infraction, act or omission constitutes a ground for termination. <br><br>
    
                        9.	The EMPLOYEE agrees to terminate all other business relationships or concerns that
                         he/she may be personally involved with that are in the same line of business as that of the EMPLOYER. <br><br> 
    
                            The EMPLOYEE acknowledges being aware of the code of discipline mandated by the
                             EMPLOYER and all the rules and regulations issued by the EMPLOYER concerning the
                             employment of the EMPLOYEE with the EMPLOYER.  <br><br> 
                             
                        10.	Aside from the just and authorized causes for the termination of employment 
                        enumerated in the Labor Code, the following acts and/or omissions shall, without
                        limitation, similarly constitute just and authorized grounds for the termination of
                        employmentby the EMPLOYER: 
                         </p>
                               <p style="margin-left:300px; text-align:justify">
                                        a.	Intentional or unintentional violation of the EMPLOYER’s policies, rules, and 
                                        regulations as embodied in the Company Rules and Regulations; 
                                        </p>  
                                       </div>
                                        <div>
                     <img src="../images/footer.png" id="footer" style= "width:1900px; height:220px"/>
                                </div>  
                                <div class="page-break"></div>
                           <div>
							 <img src="../images/heading2.png" id="heading2" style="width:1900px; height:250px;"/>
						</div>
                        <p style="margin-left:300px; text-align:justify">
<div class="container">
                                <p style="margin-left:300px; text-align:justify">
                                        b.	Loss of trust and confidence reposed by the EMPLOYER on the EMPLOYEE;  <br><br> 
                                        c.	When the  EMPLOYEE is declared physically or mentally incapacitated to 
                                            perform his/her duties by the EMPLOYER’s appointed doctor; <br><br>     
                                        d.	Failure of the EMPLOYEE to successfully pass the EMPLOYER’s
                                         evaluation; <br><br>
                                        e.	Other similar acts, omissions, and/or event. 
                                        </p>
               <p style="margin-left:170px; text-align:justify">
    
                        The EMPLOYEE may terminate this contract on any of the ground provided by law. It is 
                        understood, however that he/she shall not be entitled to any pay if the termination is at  
                        his/her instance. Moreover, in the event that the EMPLOYEE willterminate this Contract, 
                        a 30-day written notice shall be served in advance to the EMPLOYER through the  
                        HRD/Admin/Legal Department, pursuant to Article 285 of the Labor Code. Failure to 
                        comply with the mandatory 30-day written notice shall hold EMPLOYEE liable for 
                        liquidated damages amounting to One thousand Pesos (Php1, 000.00) per day short of 
                        the 30-day period, in lieu of presentation of proof of actual damages, which liquidated  
                        damages shall be deducted from whatever amount that may still be due the  
                        EMPLOYEE, if any, without prejudice to the collection of said amount through whatever 
                        legal means should there be no such amount still owing in his/her favor. <br><br>  
    
                        Upon termination of this employment, the EMPLOYEE shall promptly account for, return, 
                        and deliver to the EMPLOYER at the EMPLOYER’s main office, his/her I.D. Card and all 
                        the EMPLOYER’s property, which may have been assigned or entrusted to his/her care 
                        or custody. Likewise, EMPLOYEE must officially turnover the properties, things 
                        andwhatever intellectualknowledge he acquired by virtue of his employment with the  
                        EMPLOYER. <br><br> 
    
                        
						<?php
						if ($separation ==1){
							echo "Where EMPLOYEE is entitled to separation pay, the same shall be equivalent to one half (1/2) month pay for every year of service. ";
							
						}else{
							echo "";
						}
						?> 
                         <br><br> 
    
                        11.	The EMPLOYEE agrees that all amounts due to him/her as entitlements, e.g., wages,
                            bonuses or other similar monetary benefits from the EMPLOYER at the time of the 
                            EMPLOYEE’s separation, resignation or dismissal from employment, shall first be applied
                            to any and all outstanding obligations that the EMPLOYEE may have with the EMPLOYER
                            without prejudice to other recourses of the EMPLOYER should the amount due to him/her s
                            be less than his/her outstanding obligation. <br><br> 
    
                        12.	EMPLOYEE undertakes the responsibility not to disclose confidential information he 
                        acquired relative to his employment with the EMPLOYER. In the event he made such
                        disclosure, deliberately or negligently, the same is a valid ground for his termination. 
                        Moreover, in the event his employment is severed, he continues to undertake not to divulge 
                        company secrets or any information acquired by him otherwise, he shall be liable for 
                        damages.Confidential information is any information belonging to EMPLOYER or its 
                        clients that could be used by people outside the company to the detriment of the
                        EMPLOYER or its clients. <br><br>  
    
                        13.	If any provision of this document shall be construed to be invalid, it shall not affect the
                        legality, validity, and enforceability of the other provisions of this document; the invalid
                     </p>
                     </div>   
   <div>
        <img src="../images/footer.png" id="footer" style=" margin-top:200px; width:1900px; height:250px"/>
</div>  
        <div class="page-break"></div>
   <div>
        <img src="../images/heading2.png" id="heading2" style="vertical-align:middle; width:1900px; height:250px;"/>
</div>
                        <div class="container">
                        <p style="margin-left:250px; text-align:justify">
                        provision shall be deleted from this document and no longer incorporated herein but all
                         other provisions of this document shall continue. 
     					</p>
    					<p style="margin-left:170px; text-align:justify">
                        14.	This Contract represents the entire agreement between the EMPLOYER and the
                         EMPLOYEE and supersedes all previous oral and written communications, 
                         representations or agreements between the parties.     
            			</p>
						<p style="margin-left:170px; text-align:justify">
						15.	In the event EMPLOYEE resigns, is terminated, dismissed or seperated from his employment with EMPLOYER, EMPLOYEE
							agrees that for one (1) year from said resignation, termination, dismissal or seperation from employment, he/she is restricted 
							from working in a company in competition with whom the EMPLOYER has or had a business undertaking. Failure to observe the one-year
							restriction period shall render EMPLOYEE liable for damages.
						</p>
                            <p style=" margin-left:170px; text-align:justify">
                                <b>IN WITNESS WHEREOF</b>, the parties have executed this document as of the
                                date and place first mentioned.<br><br>
					       
                            <b>DYNAMIC BUILDERS & <br>
                            CONSTRUCTION CO. (PHIL.) INC.</b> <br><br> 
                            
                            By:  
                           </p> <br><br>
                                   <table  style="margin-left:170px; width:90%">
                                         <tr>
                                           <td>(Engr.)JOSE EDUARDO T. CRUZ	<td>       
                                           <td><b><?=$aTrans['name']?></b></td>
                                       </tr>
                                         <tr>
                                           <td>General Manager<td>	                        
                                           <td> Employee</td>
                                       </tr>
                                </table>
                            
                                        	  <p style=" margin-left:150px; text-align:center">
                                       			 SIGNED IN THE PRESENCE OF:
                                        	</p>
                                                 <br><br>                    
                              <table style="margin-left:170px;width:90%; font-family:'Times New Roman', Times, serif">
                                 <tr>
                                    <td><b> SILVESTRE G. LAREZA</b>	</td>
                                    <td align="center"><b>SELWYN ROSS T. MONTES</b></td>
                                </tr>
                            </table>
						</p>
                        
                                           		 <div class="header3">
                                            		ACKNOWLEDGEMENT
                                            	</div><br><br> 
<div style="margin-left:170px; text-align:justify">
                                    REPUBLIC OF THE PHILIPPINES)<br>
                                    City of Bacolod &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbspSS.<br>
                                    x----------------------------------------------------x <br><br> 
                                    
                                    BEFORE ME, a Notary Public for and in the City of Bacolod, this _____________________, 
                                    personally came and appeared the following persons with their corresponding valid IDs:   <br><br> 
                                   
                                            <table border="1" style="width:90%;border-collapse:collapse;">
                                                  <tr>
                                                    <td width="35%">&nbsp;&nbsp;<i>Names</i></td>	
                                                    <td width="45%">&nbsp;&nbsp;<i>Valid ID</i></td>
                                               </tr>
                                                  <tr>
                                                    <td>&nbsp;&nbsp;JOSE EDUARDO T. CRUZ</td>					          
                                                    <td>&nbsp;&nbsp;TIN 910-236-480</td>
                                               </tr>
                                                  <tr>
                                                    <td>&nbsp;&nbsp;<?=$aTrans['name']?></b></td>					          
                                                    <td>&nbsp;&nbsp;TIN <?=$aTrans['tin']?></b> </td>
                                              </tr>
                                          </table>
									known to me and to me known to be the same persons who executed the foregoing instrument 
                                    and acknowledged before me that the same is their free and voluntary act and deed.<br>
                                    
			   						WITNESS MY HAND AND NOTARIAL SEAL. <br><br> 

                                    Notary Public<br>
									Doc. No.:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________<br>
									Page No.:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________<br>
									Book No.:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________<br>
									Series of &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; __________<br><br>
            </div>
                <div>
                    <img src="../images/footer.png" id="footer" style="width:1900px; height:250px; margin-top:100px"/>
            </div>  
                     <div class="page-break"></div>
                <div>
					<img src="../images/heading2.png" id="heading2" style="width:1900px; height:250px;"/>
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
                                        <div class="header4">Annex “B”</div><br>
                                            <div style="text-align:center; margin-left:170px">
                                            <b><u>SALARY PACKAGE</u></b><br>
                                            <b>Php <u> <?=number_format(($tPackage),2)?></u></b></div><br><br>

                                         <table style="margin-left:350px; width:90%">
                                              <tr>
                                                <td align="left">Base Rate</td>	
                                                    <td align="left">&nbsp;</td>				
                                                <td align="center">Php <u><?=number_format(($base_rate),2)?></u></td>
                                          </tr>
                                          <tr>
                                            <td>&nbsp; </td>				
                                          </tr>
                                             <tr>
                                               <td align="left">Allowance</td>
                                                   <td align="left">&nbsp;</td> 					
                                               <td align="center">Php <u><?=number_format(($allowance),2)?></u></td>
                                          </tr>
                                          <tr>
                                                    <td>&nbsp; </td>				
                                          </tr>
                                             <tr>
                                          <tr>
                                             <td align="left">Others</td>
                                                   <td align="left">&nbsp;</td>							
                                                   <td align="center">Php <u><?=number_format(($others),2)?></u> </td>
                                         </tr>
                                     <table>         
                                                
                                            <br><br>
                                            <p style=" margin-left:170px; text-align:justify">
                                            *Note:  <b><?=$options->getAttribute('employee_status','employee_statusID',$aTrans['employee_statusID'],'employee_status')?></b> - Paid EMPLOYEE
</p>
<div style="margin-left:170px; text-align:justify; text-indent:90px">
                                            Additional allowances and benefits may be granted and approved by the
                                            Management.
</div><br><br>
                                            
                                         <div style="margin-left:1000px;">
                                            With my conformity:<br><br>
                                   
										
                                            <u><?=$aTrans['name']?></u>
                                     
                                     </div>
<div>
                     <img src="../images/footer.png" id="footer" style="width:1900px; height:250px; margin-top:1400px;"/>
                                </div>  
</body>
</html>