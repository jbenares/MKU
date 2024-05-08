j(document).ready(function(){
		computeExpectedOutput();
		
		j("#finishedproduct,#typeofpackage").change(function(){			
			/*On finishedproduct change , get excess of specific finished prodcut*/
			queryExcess();
			queryTypeOfPackage();
			queryInventoryBalance();
		});
				
		j("#header_form").validate({
			errorContainer: "#messageError",
			errorLabelContainer: "#messageError ul",
			wrapper: "li"
		});
		
		/*JOB ORDER EDIT*/
		j("#actualoutput,#excessused,#numberofbatches").keyup(function(){
			computeExpectedOutput();
		});
		
		j("#inventorybalance").change(function(){
			computeExpectedOutput();
		});
							
		j("#excessused").keyup(function(){
			//validateExcess();
		});
				
	});
	
	/************************************
	Query Inventory Balance
	************************************/
	
	function queryInventoryBalance(){
		var form_data={
			query: "queryInventoryBalance",
			finishedproduct: j("#finishedproduct").val(),
			typeofpackage: j("#typeofpackage").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					//alert(msg);
					j("#availqty-main").val(msg);
				}
		});	
		
	}
	
	function queryTypeOfPackage(){
		var form_data={
			query: "queryTypeOfPackage",
			typeofpackage: j("#typeofpackage").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					j("#typeofpackage-main").val(msg);
					/*Query Other Packages*/
					queryTypeOfPackage_1();
					queryTypeOfPackage_2();
					queryTypeOfPackage_3();
				}
		});	
	}
	
	/*******************************
	INVENTORY BALANCE FOR PACKAGE 1
	*******************************/
	function queryTypeOfPackage_1(){
		var form_data={
			query: "queryTypeOfPackage_1",
			typeofpackage: j("#typeofpackage").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(package_id){
					var package=queryPackageName_1(package_id);
					j("#package_id_1").val(package);
					queryInventoryBalance_1(package_id);
				}
		});	
	}
	
	function queryPackageName_1(package_id){
		var form_data={
			query: "queryPackageName_1",
			typeofpackage: package_id
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					j("#typeofpackage-1").val(msg);
				}
		});	
	}
	
	function queryInventoryBalance_1(package_id){
		var form_data={
			query: "queryInventoryBalance",
			finishedproduct: j("#finishedproduct").val(),
			typeofpackage: package_id
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					//alert(msg);
					j("#availqty-1").val(msg);
				}
		});	
		
	}
	
	/*******************************
	INVENTORY BALANCE FOR PACKAGE 2
	*******************************/
	function queryTypeOfPackage_2(){
		var form_data={
			query: "queryTypeOfPackage_2",
			typeofpackage: j("#typeofpackage").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(package_id){
					var package=queryPackageName_2(package_id);
					j("#package_id_2").val(package);
					queryInventoryBalance_2(package_id);
				}
		});	
	}
	
	function queryPackageName_2(package_id){
		var form_data={
			query: "queryPackageName_2",
			typeofpackage: package_id
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					j("#typeofpackage-2").val(msg);
				}
		});	
	}
	
	function queryInventoryBalance_2(package_id){
		var form_data={
			query: "queryInventoryBalance",
			finishedproduct: j("#finishedproduct").val(),
			typeofpackage: package_id
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					//alert(msg);
					j("#availqty-2").val(msg);
				}
		});	
		
	}
	
	
	
	/*******************************
	INVENTORY BALANCE FOR PACKAGE 3
	*******************************/
	function queryTypeOfPackage_3(){
		var form_data={
			query: "queryTypeOfPackage_3",
			typeofpackage: j("#typeofpackage").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(package_id){
					var package=queryPackageName_3(package_id);
					j("#package_id_3").val(package);
					queryInventoryBalance_3(package_id);
				}
		});	
	}
	
	function queryPackageName_3(package_id){
		var form_data={
			query: "queryPackageName_3",
			typeofpackage: package_id
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					j("#typeofpackage-3").val(msg);
				}
		});	
	}
	
	function queryInventoryBalance_3(package_id){
		var form_data={
			query: "queryInventoryBalance",
			finishedproduct: j("#finishedproduct").val(),
			typeofpackage: package_id
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					//alert(msg);
					j("#availqty-3").val(msg);
				}
		});	
	}
	
	
	
	/************************************
	Query Excess
	************************************/
		
		
	function queryExcess(){
		var form_data={
			query: "queryExcess",
			finishedproduct: j("#finishedproduct").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					//alert(msg);
					j("#inventoryexcess").val(msg);
					j("#excessused").val(msg);
					
				}
		});
	}
	
	/************************************
	Query Excess For Job Order Edit
	************************************/
		
	function queryExcessForJOEdit(){
		var form_data={
			query: "queryExcessForJOEdit",
			finishedproduct: j("#finishedproduct").val(),
			jobdate: j("#jobdate").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(msg){
					//alert(msg);
					j("#inventoryexcess").val(msg);
					//j("#excessused").val(msg);
					
				}
		});
	}
	
	/******************
	EXPECTED OUTPUT
	******************/
	function computeExpectedOutput(){
		/*Total Quantity*/
		var totalqty=j("#totalqty").val();
		
		/*Number of Batches*/
		var numberofbatches=j("#numberofbatches").val();
		
		/*Actutal Output*/
		var actualoutput=j("#actualoutput").val();
		
		var expectedoutput=(totalqty * numberofbatches);
		
		/*Expected Output += Added from Inventory*/
		//alert(j("#inventorybalance").val());
		
		/*Add to inventory Exxcess Used*/
		if(j("#excessused").val()!=""){
			expectedoutput=expectedoutput+parseFloat(j("#excessused").val());	
		}
		
		var form_data={
			query: "computeBalance",
			inventorybalance: j("#excessused").val(),
			expected : expectedoutput,
			typeofpackage : j("#typeofpackage").val()
		};
		
		j.ajax({
			url:"ajax.php",
			type: 'POST',
			data: form_data,
			success:
				function(bal){
					//alert(bal);
					j("#expectedoutput").val(bal);
					
					var form_data={
						query: "computeVariance",
						expected: bal,
						actual : actualoutput
					};
					
					j.ajax({
						url:"ajax.php",
						type: 'POST',
						data: form_data,
						success:
							function(bal){
								//alert(bal);
								j("#div-variance").html(bal);
								//j("#expectedoutput").val(bal);
							}
					});
				}
		});
						
	}
	
	/******************
	VALIDATE EXCESS USED
	******************/
	function validateExcess(){
		var inventoryexcess = j("#inventoryexcess").val();
		var excessused = j("#excessused").val();
		
		if(parseFloat(excessused) > parseFloat(inventoryexcess)){
			alert("Invalid Input");	
		}
		
	}
	
	