<script type="text/javascript" src="scripts/jquery.validate.js"></script>
<script language="JavaScript" src="scripts/calendar/calendar_us.js"></script>
<link rel="stylesheet" href="scripts/calendar/calendar.css"></link>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<style type="text/css">
.search_table tr td input[type="text"]
{
	color:#5e6977;
	background:none;
	border:none;	
	font-size:11px;
	text-align:right;
	width:100%;
}
label.error { float: none; display:block; color: red; padding-left: .5em; vertical-align: top; }
.search_table tr:hover td{
	background-color:#B5E2FE;
}
.alignLeft
{
	text-align:left;	
}
#messageError{
	padding:0px 5px;
}

#messageError ul{
	list-style:square;	
	margin-left:20px;
}
</style>

<?php
$po_header_id=$_REQUEST[po_header_id];
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    PO # : 
    <?=$options->getPOOptions($po_header_id)?><input type="submit" name="b" value="Search" />
    </div>
    <div>
    <?=$options->getUndeliveredItems($po_header_id)?>
    </div>
</div>
</form>

