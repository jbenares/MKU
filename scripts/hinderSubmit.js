// JavaScript Document
j(function(){
	j(".hinder-submit").keypress(function(e){
		if(e.keyCode == 13){
			return false;	
		}
	});
});