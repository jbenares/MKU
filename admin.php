<?php
	error_reporting(0);
	include_once("conf/ucs.conf.php");

	if($_SESSION['HKL324lew23Kdafdmliun849IP']!='true') header("location: index.php");

	#DPRC
	//include_once("dprc/dprc_options.class.php");
	//include_once("dprc/dprc_application.class.php");

	require_once("xajax_core/xajax.inc.php");

	include_once("my_Classes/upload.class.php");
	include_once("my_Classes/files.class.php");
	include_once("my_Classes/query.class.php");
	include_once("my_Classes/users.class.php");
	include_once("my_Classes/groups.class.php");
	include_once("my_Classes/wall.class.php");
	include_once("my_Classes/options.class.php");
	include_once("my_Classes/pm.class.php");
	include_once("my_Classes/menu.class.php");
	include_once("my_Classes/privileges.class.php");
	include_once("my_Classes/folders.class.php");
	include_once("my_Classes/ppiquestions.class.php");
	include_once("my_Classes/clients.class.php");
	include_once("my_Classes/ps_pagination.php");
	include_once("my_Classes/productmaster.class.php");
	include_once("my_Classes/categories.class.php");
	include_once("my_Classes/account.class.php");
	include_once("my_Classes/account_type.class.php");
	include_once("my_Classes/supplier.class.php");
	include_once("my_Classes/location.class.php");
	include_once("my_Classes/purchaseheader.class.php");
	include_once("my_Classes/brand.class.php");
	include_once("my_Classes/formulation.class.php");
	include_once("my_Classes/joborder.class.php");
	include_once("my_Classes/package.class.php");
	include_once("my_Classes/delivery.class.php");
	include_once("my_Classes/rr.class.php");
	include_once("my_Classes/service_rr.class.php");
	include_once("my_Classes/stockstransfer.class.php");
	include_once("my_Classes/productconversion.class.php");
	include_once("my_Classes/stockcard.class.php");
	include_once("my_Classes/po.class.php");
	include_once("my_Classes/equipment.class.php");
	include_once("my_Classes/catague.class.php");
	include_once("my_Classes/jobs.class.php");

	include_once("my_Classes/order.class.php");
	include_once("my_Classes/stockreturns.class.php");
	include_once("my_Classes/jo.class.php");
	include_once("my_Classes/production.class.php");
	include_once("my_Classes/preturns.class.php");
	include_once("my_Classes/invadjust.class.php");
	include_once("my_Classes/purchaserequest.class.php");

	include_once("my_Classes/projects.class.php");
	include_once("my_Classes/budget.class.php");
	include_once("my_Classes/work_category.class.php");
	include_once("my_Classes/issuance.class.php");
	include_once("my_Classes/contractor.class.php");
	include_once("my_Classes/ap.class.php");
	include_once("my_Classes/ar.class.php");
	include_once("my_Classes/employee.class.php");
	include_once("my_Classes/sales_invoice.class.php");
	include_once("my_Classes/spo.class.php");
	include_once("my_Classes/cr.class.php");

	include_once("my_Classes/cv.class.php");
	require_once("eur/classes/eur.include.php");


	#Roljhon Classes
	include_once("items/classes/items.class.php");
	include_once("po_labor/classes/polabor.class.php");
	include_once("my_Classes/contract.class.php");
	include_once("my_Classes/project_type.class.php");
	include_once("my_Classes/violations.class.php");
	#End Roljhon Classes
	
	//2017 Classes - Martin
	include_once("my_Classes/employee_type.class.php");
	include_once("my_Classes/dtr.class.php");
	include_once("my_Classes/dtr_detail.class.php");

	/*******************
	ACCTG CLASSES
	*******************/

	include_once("my_Classes/chartofaccounts.class.php");
	include_once("my_Classes/journal.class.php");
	include_once("my_Classes/gltransac.class.php");

	$xajax = new xajax();
	$options = new options();
	$transac = new query();
	$upload = new upload();

	#$xajax->setFlag('debug',true);

	$view = $_REQUEST[view];
	$function_list = $transac->include_XajaxF($view);

	if(!empty($function_list)) {
		foreach($function_list as $f) {
			$xajax->registerFunction($f);
		}

		$xajax->registerFunction("notifications");
	}

	$xajax->registerFunction("notifications");
	$xajax->processRequest();

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title><?=$title;?></title>
	<link href="images/logo_icon.png" rel="SHORTCUT ICON" >
    <?php $xajax->printJavascript(); ?>
    <script language=JavaScript src="menu/stuHover.js" type="text/javascript"></script>
    <script language=JavaScript fptype=dynamicanimation src="xajax_js/div_show_hide.js"></script>
    <script language=JavaScript fptype=dynamicanimation src="scripts/animate.js"></script>
    <script language=JavaScript src="scripts/checkall.js"></script>
    <script language=JavaScript src="scripts/prototype.js"></script>
    <script language=JavaScript src="scripts/lightbox_effect.js"></script>
    <link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
    <script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
    <script type="text/javascript" src="scripts/numformats.js"></script>

    <?php
	/****************************
	FOR JAVASCRIPT, CSS IMPORT LIBRARIES
	*****************************/
    ?>
    <script type="text/javascript" src="scripts/gs_sortable.js"></script>
    <script type="text/javascript" src="scripts/jquery.js"></script>
    <script type="text/javascript" src="scripts/jquery.validate.js"></script>
    <script type="text/javascript" src="scripts/jquery-ui-1.8.15.custom.js"></script>
    <link rel="stylesheet" type="text/css" href="scripts/jquery-ui-1.8.18.custom.css">
		<link rel="stylesheet" type="text/css" href="scripts/codebase/dhtmlx.css"/>
		<script src="scripts/codebase/dhtmlx.js"></script>

    <style type="text/css">
		input[type="submit"], input[type="button"], input[type="reset"]{
			font-size:11px;
		}
    </style>
		<style type="text/css">
			.dhtmlx-myCss{
			    font-weight:bold !important;
			    color:white !important;
			    background-color:red !important;
			}
	 </style>
    <script type="text/javascript">
		var j=jQuery.noConflict();
	</script>
	<script type="text/javascript" src="scripts/hinderSubmit.js"></script>
    <script>
        function approve_confirm() {
          doyou = confirm("Do you want to confirm?");

          if(doyou == true) {
            return true;
          }
          else if(doyou == false) {
            return false;
          }
        }
    </script>

    <link rel="stylesheet" type="text/css" href="menu/pro_dropdown_2.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/stylemain.css" />
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/demo2.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/lightbox_effect.css" />

</head>

<body>
<style type="text/css">
.dhtmlx-myCss{
	font-weight:bold !important;
	color:#000 !important;
	background-color:#fff !important;
	border: 2px solid #eee;

}
</style>
	<script type="text/javascript">
	//showNotif("Testing!");
	function showNotif(msg){
		var message = msg;
			dhtmlx.message({
				title:"Notification",
				type: "myCss",
				text: message,
				expire: 2000
			})
	}

	function poll(){
       jQuery.ajax({
           type: "POST",
           url: "ajax/checkWall.php?data="+jQuery('#Wallcount').val(),
           success: function(data){
						  var x = JSON.parse(data)
							var audio = new Audio('sounds/notif.mp3');
              // do something with data
							if(x[0] != 0){
								dhtmlx.message({
									title: "Notification",
									text: x[0],
									expire: 10000
								})
								  audio.play();
									jQuery('#Wallcount').val(x[1]);
							}
							//alert(data);
           }
       });
   };
   setInterval(poll, 1000);
	</script>
		<?php
			$sql = mysql_query("select count(*) as c from wall");
			$r=mysql_fetch_assoc($sql);
		?>
		<input type="hidden" id="Wallcount" value="<?=$r['c']?>">
		<script type="text/javascript" src="scripts/wz_tooltip.js"></script>

    <div id="overlay" onClick="hideBox()" style="display:none"></div>
    <div id="box" style="display:none">
        <img id="close" src="images/close.gif" onClick="hideBox()" alt="Close" title="Close this window" />
        <div id="Rdiv"></div>
    </div>

    <div id="demodiv" class="demo" style="display:none;"><img src="images/109_.gif"><br>Please Wait While Processing Query</div>

    <div id="header">
        <span class="title"><?=$title;?></span>
        <div style="font-size:11px; width:100%;text-align:right; right:5px; margin-top:8px;position:absolute;">
            <span>
            	<a href="logout.php"><img src="images/key_go.png" onmouseover="Tip('Logout')"></a>
							<a href="#" onclick="xajax_notifications('<?=$today;?>');toggleBox('demodiv',1);"><img src="images/comment_blue.gif" onmouseover="Tip('Notifications')"></a>
                <img src="images/user.png" /> <b><?=$_SESSION[user_lname].', '.$_SESSION[user_fname].' '.$_SESSION[user_mname][0].'. ('.$_SESSION['access'].')';?></b>
            </span>
            <span><img src="images/date.png" /> <b><?=date("l, dS \of F Y");?></b></span>
        </div>
    </div>

    <div style="top:30px;left:0;position:fixed;width:100%;z-index:900;"><?php include_once("menu.php"); ?></div>

    <div style="width:95%;text-align:left;margin:auto;top:68px;position:relative;background:#EEEEEE;">
        <div class="form_layout" style="height:90px; background-color: #a1d0fc;"><img src="images/logo_main.png" alt=" "  style="width: 500px; margin-top: 12px;"/></div>
    </div>

    <div style="width:95%;margin:auto;position:relative;top:68px;">
        <?php include_once($transac->include_files($registered_access, $view)); ?>
    </div>
    <div style="font-size:10px;font-family:Arial;color:#5e6977;margin:auto;width:300px;top:100px;position:relative; z-index:0; clear:both; text-align:center;">
        <?=$title;?><br /><br />
        <img src="images/ciss_small_2.jpg" /> <br /><br />
        <?='Copyright &#169 '?>2011<?=" - ".date("Y")?>
    </div>

    <?php
	/**********
	DO NOT REMOVE - SCRIPTS FOR AUTOCOMPLETE, DATEPICKER, AND DIALOGS
	**********/
	include_once("admin.salvio.php");

	?>

</body>
</html>
