$(function(){
$("#demoForm").submit(function(e){
	e.preventDefault();
	
	var urlToSubmit = $(this).attr("action");
	var method = $(this).attr("method");
	var dataToSubmit = $(this).serialize();
	
	// set loader and make button disable.
	var btn = $(this).find("button[type='submit']");
	var originalText = btn.html();
	btn.html(btn.attr("data-loading"));
	btn.prop("disabled",true);
	
	$.ajax({
		url: urlToSubmit,
		type: method,
		data : dataToSubmit,
		dataType:"json",
		success: function(response){
			
			// clean up the form 
			$(".error").remove();
			$(".success").remove();
			$(".alert").remove();
			$(".has-error").removeClass("has-error");
			
			// reset the button 
			btn.html(originalText);
			btn.prop("disabled",false);
			
			console.log("Efforts",response.efforts);
			console.log(response);
			//console.log =function(){}
			// if response has redirect then redirect the url.
			if(response.redirect) {
				window.location.href = response.redirect;
			}
			
			if(response.status == "success")
			{
				if(response.msg) {
					$("#demoForm").prepend(response.msg);
				} else {
				
					$("#demoForm").prepend("<p class='success'>Your Form Was Successfully Submitted</p>");
				}
				
			} else {
					$("#demoForm").prepend("<div class='alert'>There is problem with submission, please check form carefully.");
			
				// process validation errors.
				for(var i in response.errors) {
					

					if (typeof response.errors[i] == "string")
						var errorMsg = "<p class='error'>" + response.errors[i] + "</p>";
					else {
					var errorMsgArray = [];
						
						for(var k in response.errors[i]) {
							errorMsgArray[k] ="<p class='error'>" + response.errors[i][k]+"</p>";
						}

					}
					
					var selector = $("input[name='" + i + "']");
					// if selector is input.
					if(selector.length) {
						console.log("input",selector);
							
						
					var type = selector.attr("type");
					// if selector is checkbox or radio.
					if(type == "checkbox" || type == "radio") {
					
							selector.parent().append(errorMsg);
							selector.parent().addClass("has-error");
					// if it is group validator
					} else if(selector.length > 1 && $("div[name='"+i+"']").length) {
						selector = $("div[name='"+i+"']");
						// if group level error
						if(typeof response.errors[i] == "string") {
							$("div[name='"+i+"']").append(errorMsg);
							$("div[name='"+i+"']").addClass("has-error");
						// if field level error.
						} else {
							childs = selector.find("input[name='"+i+"']");
							console.log("childs",childs);
							//window.customChild = childs;
							for (var j in childs) {
								if(errorMsgArray[j] !== undefined && errorMsgArray[j] )
								{
									console.log("child",childs[i]);
									$(childs[j]).after(errorMsgArray[j]);
									$(childs[j]).addClass("has-error");
								}
							}	
						}
					// normal input tag like text,email etc.						
					} else {
						selector.after(errorMsg);
						selector.addClass("has-error");
					}
						
						
					// if selector is select tag	
					} else if((selector = $("select[name='"+i+"']")) && selector.length) {
						
						if(selector.length > 1 && $("div[name='"+i+"']").length) {
							selector = $("div[name='"+i+"']");
							if(typeof response.errors[i] == "string") {
								$("div[name='"+i+"']").append(errorMsg);
								$("div[name='"+i+"']").addClass("has-error");
							} else {
								childs = selector.find("input[name='"+i+"']");
								console.log("childs",childs);
								//window.customChild = childs;
								for (var j in childs) {
									if(errorMsgArray[j] !== undefined && errorMsgArray[j] )
									{
										console.log("child",childs[i]);
										$(childs[j]).after(errorMsgArray[j]);
										$(childs[j]).addClass("has-error");
									}
								}	
							}

						
						} else {
							selector.addClass("has-error");
							console.log("select",selector);
							selector.after(errorMsg);
						}
						
						// if selector is textaread
					} else if((selector = $("textarea[name='"+i+"']")) && selector.length) {
						
						if(selector.length > 1 && $("div[name='"+i+"']").length) {
							selector = $("div[name='"+i+"']");
							if(typeof response.errors[i] == "string") {
								$("div[name='"+i+"']").append(errorMsg);
								$("div[name='"+i+"']").addClass("has-error");
							} else {
								childs = selector.find("input[name='"+i+"']");
								console.log("childs",childs);
								//window.customChild = childs;
								for (var j in childs) {
									if(errorMsgArray[j] !== undefined && errorMsgArray[j] )
									{
										console.log("child",childs[i]);
										$(childs[j]).after(errorMsgArray[j]);
										$(childs[j]).addClass("has-error");
									}
								}	
							}
						
						} else {
							selector.addClass("has-error");
							console.log("textarea",selector);
							selector.after(errorMsg);
						}
						
					}
				}


			}
			window.scrollTo(0,0);
		}
	});
	

});
});