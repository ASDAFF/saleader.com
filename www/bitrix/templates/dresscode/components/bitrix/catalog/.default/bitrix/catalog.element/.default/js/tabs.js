	$(function(){

		var $window  = $(window);
		$(window).on("load", function(){

			function getElementOffset(getElement){
				return $(getElement).offset().top;
			}
			
			var $tabs = $("#catalogElement .tabs");
			var $tabElements = $tabs.find(".tab");
			var $mainScrollObj = $("html, body");

			var arCoordinates = {};

			$tabElements.each(function(i, nextElement){
				var $nextElement = $(nextElement);
				if($nextElement.data("id")){
					arCoordinates[$nextElement.data("id")] = getElementOffset("#" + $nextElement.data("id"));
				}
			});

			var scrollToElement = function(event){
				var $this = $(this).parents(".tab");
				var toElementID = $this.data("id");

				if(toElementID){

					$tabElements.removeClass("active");

					$mainScrollObj.stop().animate({
						scrollTop: getElementOffset("#" + toElementID) + "px"
					}, 250);

					return event.preventDefault($this.addClass("active"));
				}
			};

			var calcCloseElement = function(coordinate){
				var copyCoordinate = [];

				$.each(arCoordinates, function(i, val) {
					if(coordinate <= val){
						copyCoordinate.push({
							id: i, value: val
						})
					}
				});

				copyCoordinate.sort(function(obj1, obj2) {
				  if (obj1.value < obj2.value) return -1;
				  if (obj1.value > obj2.value) return 1;
				  return 0;
				});

				return copyCoordinate[0];
			};

			var scrollControl = function(event){
				var curScrollValueY = (event.currentTarget.scrollY) ? event.currentTarget.scrollY : $(window).scrollTop()
				var arCurrentTab = calcCloseElement(curScrollValueY);
				if(arCurrentTab != undefined){
					$tabElements.removeClass("active");
					$tabElements.filter('[data-id="' + arCurrentTab["id"] + '"]').addClass("active");
				}
			};

			$(document).on("click", "#elementNavigation .tab a, #elementSmallNavigation .tab a", scrollToElement);
			$(window).on("scroll", scrollControl);
		});
	});
