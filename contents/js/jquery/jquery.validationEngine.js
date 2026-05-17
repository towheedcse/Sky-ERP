/*
 * Inline Form Validation Engine, jQuery plugin
 * 
 * Copyright(c) 2009, Cedric Dugas
 * http://www.position-relative.net
 *	
 * Form validation engine witch allow custom regex rules to be added.
 * Licenced under the MIT Licence
 */

J(document).ready(function() {

	// SUCCESS AJAX CALL, replace "success: false," by:     success : function() { callSuccessFunction() }, 
	J("[class^=validate]").validationEngine({
		success :  false,
		failure : function() {}
	})
});

jQuery.fn.validationEngine = function(settings) {
	if(J.validationEngineLanguage){					// IS THERE A LANGUAGE LOCALISATION ?
		allRules = J.validationEngineLanguage.allRules
	}else{
		allRules = {"required":{    			  // Add your regex rules here, you can take telephone as an example
							"regex":"none",
							"alertText":"* This field is required",
							"alertTextCheckboxMultiple":"* Please select an option",
							"alertTextCheckboxe":"* This checkbox is required"},
						"length":{
							"regex":"none",
							"alertText":"*Between ",
							"alertText2":" and ",
							"alertText3": " characters allowed"},
						"minCheckbox":{
							"regex":"none",
							"alertText":"* Checks allowed Exceeded"},	
						"confirm":{
							"regex":"none",
							"alertText":"* Your field is not matching"},		
						"telephone":{
							"regex":"/^[0-9\-\(\)\ ]+$/",
							"alertText":"* Invalid age"},	
						"email":{
							"regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",
							"alertText":"* Invalid email address"},	
						"date":{
                             "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}J/",
                             "alertText":"* Invalid date, must be in YYYY-MM-DD format"},
						"onlyNumber":{
							"regex":"/^[0-9\ ]+J/",
							"alertText":"* Numbers only"},	
						"noSpecialCaracters":{
							"regex":"/^[0-9a-zA-Z]+J/",
							"alertText":"* No special caracters allowed"},	
						"onlyLetter":{
							"regex":"/^[a-zA-Z\ \']+J/",
							"alertText":"* Letters only"}
					}	
	}

 	settings = jQuery.extend({
		allrules:allRules,
		inlineValidation: true,
		success : false,
		failure : function() {}
	}, settings);	

	J("form").bind("submit", function(caller){   // ON FORM SUBMIT, CONTROL AJAX FUNCTION IF SPECIFIED ON DOCUMENT READY
		if(submitValidation(this) == false){
			if (settings.success){
				settings.success && settings.success(); 
				return false;
			}
		}else{
			settings.failure && settings.failure(); 
			return false;
		}
	})
	if(settings.inlineValidation == true){ 		// Validating Inline ?
		
		J(this).not("[type=checkbox]").bind("blur", function(caller){loadValidation(this)})
		J(this+"[type=checkbox]").bind("click", function(caller){loadValidation(this)})
	}
	var buildPrompt = function(caller,promptText,showTriangle) {			// ERROR PROMPT CREATION AND DISPLAY WHEN AN ERROR OCCUR
		var divFormError = document.createElement('div')
		var formErrorContent = document.createElement('div')
		var arrow = document.createElement('div')
		
		
		J(divFormError).addClass("formError")
		J(divFormError).addClass(J(caller).attr("id"))
		J(formErrorContent).addClass("formErrorContent")
		J(arrow).addClass("formErrorArrow")

		J("body").append(divFormError)
		J(divFormError).append(arrow)
		J(divFormError).append(formErrorContent)
		
		if(showTriangle == true){		// NO TRIANGLE ON MAX CHECKBOX AND RADIO
			J(arrow).html('<div class="line10"></div><div class="line9"></div><div class="line8"></div><div class="line7"></div><div class="line6"></div><div class="line5"></div><div class="line4"></div><div class="line3"></div><div class="line2"></div><div class="line1"></div>');
		}
		J(formErrorContent).html(promptText)
	
		callerTopPosition = J(caller).offset().top;
		callerleftPosition = J(caller).offset().left;
		callerWidth =  J(caller).width()
		callerHeight =  J(caller).height()
		inputHeight = J(divFormError).height()

		callerleftPosition = callerleftPosition + callerWidth -30
		callerTopPosition = callerTopPosition  -inputHeight -10
	
		J(divFormError).css({
			top:callerTopPosition,
			left:callerleftPosition,
			opacity:0
		})
		J(divFormError).fadeTo("fast",0.8);
	};
	var updatePromptText = function(caller,promptText) {	// UPDATE TEXT ERROR IF AN ERROR IS ALREADY DISPLAYED
		updateThisPrompt =  J(caller).attr("id")
		J("."+updateThisPrompt).find(".formErrorContent").html(promptText)
		
		callerTopPosition  = J(caller).offset().top;
		inputHeight = J("."+updateThisPrompt).height()
		
		callerTopPosition = callerTopPosition  -inputHeight -10
		J("."+updateThisPrompt).animate({
			top:callerTopPosition
		});
	}
	var loadValidation = function(caller) {		// GET VALIDATIONS TO BE EXECUTED
		
		rulesParsing = J(caller).attr('class');
		rulesRegExp = /\[(.*)\]/;
		getRules = rulesRegExp.exec(rulesParsing);
		str = getRules[1]
		pattern = /\W+/;
		result= str.split(pattern);	
		
		var validateCalll = validateCall(caller,result)
		return validateCalll
		
	};
	var validateCall = function(caller,rules) {	// EXECUTE VALIDATION REQUIRED BY THE USER FOR THIS FIELD
		var promptText =""	
		var prompt = J(caller).attr("id");
		var caller = caller;
		var callerName = J(caller).attr("name");
		isError = false;
		callerType = J(caller).attr("type");
		
		for (i=0; i<rules.length;i++){
			switch (rules[i]){
			case "optional": 
				if(!J(caller).val()){
					closePrompt(caller)
					return isError
				}
			break;
			case "required": 
				_required(caller,rules);
			break;
			case "custom": 
				 _customRegex(caller,rules,i);
			break;
			case "length": 
				 _length(caller,rules,i);
			break;
			case "minCheckbox": 
				 _minCheckbox(caller,rules,i);
			break;
			case "confirm": 
				 _confirm(caller,rules,i);
			break;
			default :;
			};
		};
		if (isError == true){
			var showTriangle = true
			if(J("input[name="+callerName+"]").size()> 1 && callerType == "radio") {		// Hack for radio group button, the validation go the first radio
				caller = J("input[name="+callerName+"]:first")
				showTriangle = false
				var callerId ="."+ J(caller).attr("id")
				if(J(callerId).size()==0){ isError = true }else{ isError = false}
			}
			if(J("input[name="+callerName+"]").size()> 1 && callerType == "checkbox") {		// Hack for radio group button, the validation go the first radio
				caller = J("input[name="+callerName+"]:first")
				showTriangle = false
				var callerId ="div."+ J(caller).attr("id")
				if(J(callerId).size()==0){ isError = true }else{ isError = false}
			}
			if (isError == true){ // show only one
				(J("div."+prompt).size() ==0) ? buildPrompt(caller,promptText,showTriangle)	: updatePromptText(caller,promptText)
			}
		}else{
			if(J("input[name="+callerName+"]").size()> 1 && callerType == "radio") {		// Hack for radio group button, the validation go the first radio
				caller = J("input[name="+callerName+"]:first")
			}
			if(J("input[name="+callerName+"]").size()> 1 && callerType == "checkbox") {		// Hack for radio group button, the validation go the first radio
				caller = J("input[name="+callerName+"]:first")
			}
			closePrompt(caller)
		}		
		
		/* VALIDATION FUNCTIONS */
		function _required(caller,rules){   // VALIDATE BLANK FIELD
			callerType = J(caller).attr("type")
			
			if (callerType == "text" || callerType == "password" || callerType == "textarea"){
				
				if(!J(caller).val()){
					isError = true
					promptText += settings.allrules[rules[i]].alertText+"<br />"
				}	
			}
			if (callerType == "radio" || callerType == "checkbox" ){
				callerName = J(caller).attr("name")
		
				if(J("input[name="+callerName+"]:checked").size() == 0) {
					isError = true
					if(J("input[name="+callerName+"]").size() ==1) {
						promptText += settings.allrules[rules[i]].alertTextCheckboxe+"<br />" 
					}else{
						 promptText += settings.allrules[rules[i]].alertTextCheckboxMultiple+"<br />"
					}	
				}
			}	
			if (callerType == "select-one") { // added by paul@kinetek.net for select boxes, Thank you
					callerName = J(caller).attr("id");
				
				if(!J("select[name="+callerName+"]").val()) {
					isError = true;
					promptText += settings.allrules[rules[i]].alertText+"<br />";
				}
			}
			if (callerType == "select-multiple") { // added by paul@kinetek.net for select boxes, Thank you
					callerName = J(caller).attr("id");
				
				if(!J("#"+callerName).val()) {
					isError = true;
					promptText += settings.allrules[rules[i]].alertText+"<br />";
				}
			}
		}
		function _customRegex(caller,rules,position){		 // VALIDATE REGEX RULES
			customRule = rules[position+1]
			pattern = eval(settings.allrules[customRule].regex)
			
			if(!pattern.test(J(caller).attr('value'))){
				isError = true
				promptText += settings.allrules[customRule].alertText+"<br />"
			}
		}
		function _confirm(caller,rules,position){		 // VALIDATE FIELD MATCH
			confirmField = rules[position+1]
			
			if(J(caller).attr('value') != J("#"+confirmField).attr('value')){
				isError = true
				promptText += settings.allrules["confirm"].alertText+"<br />"
			}
		}
		function _length(caller,rules,position){    // VALIDATE LENGTH
		
			startLength = eval(rules[position+1])
			endLength = eval(rules[position+2])
			feildLength = J(caller).attr('value').length

			if(feildLength<startLength || feildLength>endLength){
				isError = true
				promptText += settings.allrules["length"].alertText+startLength+settings.allrules["length"].alertText2+endLength+settings.allrules["length"].alertText3+"<br />"
			}
		}
		function _minCheckbox(caller,rules,position){    // VALIDATE CHECKBOX NUMBER
		
			nbCheck = eval(rules[position+1])
			groupname = J(caller).attr("name")
			groupSize = J("input[name="+groupname+"]:checked").size()
			
			if(groupSize > nbCheck){	
				isError = true
				promptText += settings.allrules["minCheckbox"].alertText+"<br />"
			}
		}
		return(isError) ? isError : false;
	};
	var closePrompt = function(caller) {	// CLOSE PROMPT WHEN ERROR CORRECTED
		closingPrompt = J(caller).attr("id")

		J("."+closingPrompt).fadeTo("fast",0,function(){
			J("."+closingPrompt).remove()
		});
	};
	var submitValidation = function(caller) {	// FORM SUBMIT VALIDATION LOOPING INLINE VALIDATION
		var stopForm = false
		J(caller).find(".formError").remove()
		var toValidateSize = J(caller).find("[class^=validate]").size()
		
		J(caller).find("[class^=validate]").each(function(){
			var validationPass = loadValidation(this)
			return(validationPass) ? stopForm = true : "";	
		});
		if(stopForm){							// GET IF THERE IS AN ERROR OR NOT FROM THIS VALIDATION FUNCTIONS
			destination = J(".formError:first").offset().top;
			J("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination}, 1100)
			return true;
		}else{
			return false
		}
	};
};