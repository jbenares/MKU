function putDec(passedvalue) {
	if(passedvalue=="")
		//return '0.00';
		return "";
	else {
		passedvalue = Number(removeCommas(passedvalue));
		return addCommas((parseFloat(passedvalue)).toFixed(2));
	}
}

function putDec_forTotal(passedvalue) {
	if(passedvalue=="")
		return '0.00';
	else {
		return addCommas((parseFloat(passedvalue)).toFixed(2));
	}
}

function calc_total_price(qty, price, index) {
	total_price = qty * Number(removeCommas(price));
	
	document.getElementById(index).value=putDec_forTotal(total_price);		
}

function addCommas(nStr) {
	nStr += '';
	
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function removeCommas(aNum) {
	//remove any commas
	aNum=aNum.replace(/,/g,"");	
	
	//remove any spaces	
	aNum=aNum.replace(/\s/g,"");
	
	return aNum;

}

function check_if_null() {
	if(document.journal_entry.ref.value=="") {
		alert("Please fill in reference number!");
		return false;
	}
	
	if(document.journal_entry.amount_total.value=="0.00") {
		alert("Please check the amounts!");
		return false;
	}
	
	if(document.journal_entry.cash_account_id.value==0) {
		alert("Please select cash account!");
		return false;
	}
	
	return approve_confirm();
}