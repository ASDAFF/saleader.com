$(function(){
	var $registerForm = $(".bx-register-form");

	var authFormSubmit = function(event){
		var $formFields = $registerForm.find("input").removeClass("error");
		var emptyFields = false;

		$formFields.each(function(i, nextElement){
			var $nextElement = $(nextElement);
			if($nextElement.data("required") == "required"){
				if($nextElement.val() == ""){
					$nextElement.addClass("error");
					emptyFields = true;
				}
			}
		});

		if(emptyFields){
			return event.preventDefault();
		}

	};

	$registerForm.on("submit", authFormSubmit);
});