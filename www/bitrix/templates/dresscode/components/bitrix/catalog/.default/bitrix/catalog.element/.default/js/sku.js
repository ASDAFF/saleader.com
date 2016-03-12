$(function(){
	var elementSelectSku = function(event){

		var _params = "";
		var _props = "";

		var $_this = $(this);

		if($_this.parent().hasClass("selected")){
			return false;
		}

		var $_mProduct = $_this.parents(".elementSku");
		var $_parentProp = $_this.parents(".elementSkuProperty");
		var $_propList = $_mProduct.find(".elementSkuProperty");
		var $_clickedProp = $_this.parents(".elementSkuPropertyValue");

		var _level = $_parentProp.data("level");

		$_this.parents(".elementSkuPropertyList").find("li").removeClass("selected");
		$_clickedProp.addClass("selected loading");

		$_propList.each(function(i, prop){

			var $_nextProp  = $(prop);
			var $_nextPropList = $_nextProp.find("li");

			var propName = $_nextProp.data("name");
			var _used = false;

			$_nextPropList.each(function(io, obj){
				var $_currentObj = $(obj);
				_props = _props + propName + ":" + $_currentObj.data("value") + ";";
				if($_currentObj.hasClass("selected")){
					_params = _params + propName + ":" + $_currentObj.data("value") + ";";
					return _used = true;
				}
			});

			if(!_used){
				_params = _params + propName + ":-forse;";
			}

		});

		$.getJSON(elementAjaxPath + "?act=selectSku&props=" + encodeURI(_props) + "&params=" + encodeURI(_params) + "&level=" + _level + "&iblock_id=" + $_mProduct.data("iblock-id") + "&prop_id=" + $_mProduct.data("prop-id") + "&product_id=" + $_mProduct.data("product-id"))
		  .done(function(http){
	  		$_propList.each(function(pI, pV){
	  			var $_sf = $(pV);
	  				$_sf.data("level") > _level && $_sf.find(".elementSkuPropertyValue").removeClass("selected").addClass("disabled");
	  		});
			$.each(http[1]["PROPERTIES"], function(name, val){
			  	var $_gPropList = $_propList.filter(function(){ return ($(this).data("name") == name); });
			  	var $_gPropListValues = $_gPropList.find(".elementSkuPropertyValue");
				$_gPropListValues.each(function(il, element){
					var $nextElement = $(element);
					$.each(val, function(pVal, _selected){
						if(pVal == $nextElement.data("value") && _selected != "D"){
							(_selected == "Y") ? $nextElement.addClass("selected").removeClass("disabled") : $nextElement.removeClass("disabled");
							return false;
						}
					});
				});
			});
			
			$(".changeName").html(http[0]["PRODUCT"]["NAME"]);
			
			$_mProduct.find(".changeID").data("id", http[0]["PRODUCT"]["ID"]);
			$_mProduct.find(".changePrice").html(http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]);
			$_mProduct.find(".changePicture").html($("<img/>").attr("src", http[0]["PRODUCT"]["IMAGES"][0]["MEDIUM_IMAGE"]["SRC"]));

			var $changeCart = $_mProduct.find(".changeCart");
			
			$changeCart.find("img").remove();
			$changeCart.removeClass("added").removeClass("disabled")
				.html(LANG["ADD_BASKET_DEFAULT_LABEL"])
				.prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}))
				.attr("href", "#");

			//AVAILABLE
			
			var $changeAvailable = $_mProduct.find(".changeAvailable");

			$changeAvailable.removeClass("outOfStock");
			$changeAvailable.removeClass("onOrder");
			$changeAvailable.removeClass("inStock");


			if(http[0]["PRODUCT"]["CATALOG_QUANTITY"] > 0){
				$changeAvailable.html(LANG["CATALOG_AVAILABLE"]).addClass("inStock");
				$changeAvailable.prepend(
					$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
				);
			}else{
				if(http[0]["PRODUCT"]["CAN_BUY"] != true){
					$changeAvailable.html(LANG["CATALOG_NO_AVAILABLE"]).addClass("outOfStock");
					$changeCart.addClass("disabled");
					$changeAvailable.prepend(
						$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/outOfStock.png")
					);
				}else{
					$changeAvailable.html(LANG["CATALOG_ON_ORDER"]).addClass("onOrder");
					$changeAvailable.prepend(
						$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/onOrder.png")
					);				
				}
			}
			// pictures

			if(http[0]["PRODUCT"]["IMAGES"]){

				// big slider vars
				var $pictureSlider = $("#pictureContainer .pictureSlider").empty();
				
				// small pictures slider
				var $moreImagesCarouselSlideBox = $("#moreImagesCarousel .slideBox");
				$moreImagesCarouselSlideBox.find(".item").remove();

				$.each(http[0]["PRODUCT"]["IMAGES"], function(i, nextElement){
					
					//big slider
					$pictureSlider.append(
						$("<div />", {class: "item"}).append(
							$("<a/>", {class: "zoom", href: nextElement["LARGE_IMAGE"]["SRC"]}).data("large-picture", nextElement["LARGE_IMAGE"]["SRC"]).append(
								$("<img />", {src: nextElement["MEDIUM_IMAGE"]["SRC"]})
							)
						)
					)

					//small slider
					$moreImagesCarouselSlideBox.append(
						$("<div />", {class: "item"}).append(
							$("<a/>", {class: "zoom", href: nextElement["LARGE_IMAGE"]["SRC"]}).data("large-picture", nextElement["LARGE_IMAGE"]["SRC"]).append(
								$("<img />", {src: nextElement["SMALL_IMAGE"]["SRC"]})
							)
						)
					)
				
				});

				startPictureElementSlider();
				startMorePicturesElementCarousel();
				createZoomer();

			}

			//short description

			if(http[0]["PRODUCT"]["PREVIEW_TEXT"]){
				$_mProduct.find(".changeShortDescription").html(http[0]["PRODUCT"]["PREVIEW_TEXT"]);
			}else{
				if($_mProduct.find(".changeShortDescription").data("first-value")){
					$_mProduct.find(".changeShortDescription").html($_mProduct.find(".changeShortDescription").data("first-value"));
				}
			}

			// full description

			if(http[0]["PRODUCT"]["DETAIL_TEXT"]){
				$_mProduct.find(".changeDescription").html(http[0]["PRODUCT"]["DETAIL_TEXT"]);
			}else{
				if($_mProduct.find(".changeDescription").data("first-value")){
					$_mProduct.find(".changeDescription").html($_mProduct.find(".changeDescription").data("first-value"));
				}
			}

			//article

			if(http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]){
				$_mProduct.find(".changeArticle").html(http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]);
			}else{
				if($_mProduct.find(".changeArticle").data("first-value")){
					$_mProduct.find(".changeArticle").html($_mProduct.find(".changeArticle").data("first-value"));
				}
			}

			if(http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0){
				$_mProduct.find(".changePrice").append(
					$("<s/>").addClass("discount").html(
						http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
					)
				);
			}

			$_clickedProp.removeClass("loading");
			
		  }).fail(function(jqxhr, textStatus, error){
		  	$_clickedProp.removeClass("loading");
		    alert("Request Failed: " + textStatus + ", " + error);
		});

		event.preventDefault();

	}

	$(document).on("click", ".elementSkuPropertyLink", elementSelectSku);

});