<?php
	ob_start();
	session_start();

	require('fpdf.php');
	
	$report_id= $_REQUEST['report_id'];
	
	include_once("conf/sms.conf.php");
	
	$queryR = mysql_query("select
								r.name as  Rname,
								r.address,
								r.telno,
								r.mobileno,
								r.Rmessage,
								s.name as Sname,
								j.supervisor
							from
								reports as r,
								substations as s,
								job_orders as j
							where
								r.id='$report_id' and
								r.substation_id=s.id and
								r.id=j.report_id");
								
	$r = mysql_fetch_array($queryR);
	
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->Image('images/header.jpg',10,10,15,9);
	$pdf->Ln(10);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(40,10,"Name : $r[Rname]");
	$pdf->Ln(5);
	$pdf->Cell(40,10,"Address : $r[address]");
	$pdf->Ln(5);
	$pdf->Cell(40,10,"Tel No : $r[telno]");
	$pdf->Ln(5);
	$pdf->Cell(40,10,"Mobile No : $r[mobileno]");
	$pdf->Ln(5);
	$pdf->Cell(40,10,"Sub Office : $r[Sname]");
	$pdf->Ln(10);
	$pdf->Cell(40,10,"Message :");
	$pdf->Ln(5);
	$pdf->Cell(40,10,"$r[Rmessage]");
	$pdf->Ln(10);
	$pdf->Cell(40,10,"$r[Rmessage]");
	$pdf->Ln(10);
	$pdf->SetFont('Arial','U',10);
	$pdf->Cell(40,10,"$r[supervisor]");
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(40,10,"Team Supervisor");
	$pdf->Ln(10);
	$pdf->SetFont('Arial','U',10);
	$pdf->Cell(40,10,"                                               ");
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(40,10,"Conforme");
	$pdf->Ln(10);
	$pdf->Output();
?>