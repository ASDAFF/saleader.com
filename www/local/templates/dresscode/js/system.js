var appOpen;
var timeOutID;
var intervalID;
var flushTimeout;
var appBasketChangeTimeout;
var skuLoading = false;
var fastBuyOpen = false;
var fastViewOpen = false;
var fastViewStoresOpen = false;
var priceVariantOpen = false;

var flushCart = function(id, q) {
	$.getJSON(ajaxPath + "?act=upd&id=" + id + "&q=" + q, function(data) {
		if(data["success"] == "true"){
			cartReload();
		}else{
			$basketQty = $("#appBasket .qty").addClass("error");
			if(data["error"] == "quantityError"){
				
				$basketQty.val(data["currentQuantityValue"]);
				
				//calc
				var $appBasket = $("#appBasket");
				var $price = $appBasket.find(".price");
				var $sum = $appBasket.find(".allSum");
				var gStrSum = $sum.html().replace(/\d\.\d/g, '').replace(/[0-9]/g, '');

				$sum.html(
					formatPrice(
						$price.data("price") * $basketQty.val()
					) + gStrSum
				);

				if($price.data("discount") > 0){

					var $sumDiscount = $sum.find(".discount");
					var gstrSumDiscount = $sumDiscount.html().replace(/\d\.\d/g, '').replace(/[0-9]/g, '');

					$sumDiscount.html(
						formatPrice(
							$price.data("discount") * $basketQty.val()
						) + gstrSumDiscount	
					);
				}

			}
			console.error("flushCart: " + data["error"]);
		}
		
	});
}

var cartReload = function() {
	$.get(ajaxPath + "?act=flushCart", function(data) {
		var $items = $(data).find(".dl");
		$("#flushTopCart").html($items.eq(0).html());
		$("#flushFooterCart").html($items.eq(1).html());
		$("#flushTopwishlist").html($items.eq(2).html());
		$("#flushTopCompare").html($items.eq(3).html());
	});
}

$(function(){

	if($("#footerTabs .tab").size() == 0){
		$("#footerTabs, #footerTabsCaption").remove();
	}else{
		$("#footerTabsCaption .item").eq(0).find("a").addClass("selected");
		$("#footerTabs .tab").eq(0).addClass("selected");
	}	

	if($("#infoTabs .tab").size() == 0){
		$("#infoTabs, #infoTabsCaption").remove();
	}else{
		$("#infoTabsCaption .item").eq(0).find("a").addClass("selected");
		$("#infoTabs .tab").eq(0).addClass("selected");
	}	

});

$(function(){
	var $upButton = $("#upButton");

	$(window).on("ready scroll", function(event){
		var curScrollValueY = (event.currentTarget.scrollY) ? event.currentTarget.scrollY : $(window).scrollTop()
		if(curScrollValueY > 0){
			$upButton.addClass("enb");
		}else{
			$upButton.removeClass("enb");
		}

	});

	$upButton.on("click", function(event){

		$("html,body").animate({
			scrollTop: 0
		}, 250);

		return event.preventDefault();

	});
});

$(window).on("ready", function(event){

	var $body = $("body").removeClass("loading"); // cache body

	if($("div").is(".global-block-container") && $("div").is(".global-information-block") && $("div").is(".global-information-block-cn")){

		var $globalBlockContainer = $body.find(".global-block-container");
		var $globalInformationBlock = $globalBlockContainer.find(".global-information-block");
		var $globalInformationBlockCntr = $globalInformationBlock.find(".global-information-block-cn");

		if(!$globalInformationBlock.hasClass("no-fixed")){
			var informBlockOffset = $globalInformationBlock.offset();
			var maxScrollHeight = $globalBlockContainer.height() + informBlockOffset.top - ($globalInformationBlockCntr.height() + 24); //24 padding top
		}

		var gbScrollCtr = function(event){

			var $this = $(this);
			var currentScrollValue = $this.scrollTop();

			if(currentScrollValue >= informBlockOffset.top){
				if(currentScrollValue >= maxScrollHeight){
					$globalInformationBlock.addClass("max-scroll");
				}else{
					$globalInformationBlock.removeClass("max-scroll");
				}
				$globalInformationBlock.addClass("fixed");
			}else{
				$globalInformationBlock.removeClass("fixed");
			}

		};

		var reCalcGbParams = function(){
			informBlockOffset = $globalInformationBlock.offset();
			maxScrollHeight = $globalBlockContainer.height() + informBlockOffset.top - ($globalInformationBlockCntr.height() + 24); //24 padding top
		}

		$(window).on("scroll", gbScrollCtr);
		$(window).on("resize", reCalcGbParams);

	}

	var getFastView = function(event){

		var $this = $(this).addClass("loading");
		var productID = $this.data("id");

		if(productID){
			$.ajax({
				url: ajaxPath + "?act=getFastView&product_id=" + productID, 
				success: function(http){

					//clear carousel cache vars
					delete fastViewInitPictureCarousel;
					delete fastViewInitPictureSlider;
					delete initFastViewApp;
					delete createFastView;

					//remove fastview window
					$("#appFastView").remove();

					//append to body
					$body.append(http);
					$this.removeClass("loading");

					//unbind last events
					$(document).off("click", "#appFastView .appFastViewExit");
					$(document).off("click", "#appFastView .appFastViewPictureCarouselItem");
					$(document).off("click", "#appFastView .appFastViewPictureCarouselLeftButton");
					$(document).off("click", "#appFastView .appFastViewPictureCarouselRightButton");
					$(document).off("mousemove", "#appFastView .appFastViewPictureSliderItemLink");
					$(document).off("mouseover", "#appFastView .appFastViewPictureSliderItemLink");
					$(document).off("mouseleave", "#appFastView .appFastViewPictureSliderItemLink");

					//start fastView scrips
					initFastViewApp();

				},
				cache: false,
				async: false
			});
			fastViewOpen = true;
		}

		return event.preventDefault();
	}

	var getStoresWindow = function(event){
	
		var $this = $(this).addClass("loading");
		var productID = $this.data("id");

		if(productID){
			$.get(ajaxPath + "?act=getAvailableWindow&product_id=" + productID, function(http){
				$("#fastViewStores").remove();
				$body.append(http);
				$this.removeClass("loading");
				fastViewStoresOpen = true;
			});
		}
	
		return event.preventDefault();

	};

	var closeStoresWindow = function(event){
		$("#fastViewStores").remove();
		fastViewStoresOpen = false;
		return event.preventDefault();
	};

	var getPricesWindow = function(event){

		var $this = $(this).addClass("loading");
		var productID = $this.data("id");

		if(productID){
			$.get(ajaxPath + "?act=getPricesWindow&product_id=" + productID, function(http){

				$("#appProductPriceVariant").remove();
				$this.removeClass("loading");
				$body.append(http);

				var thisOffsetLeft = $this.offset().left;
				var thisOffsetTop = $this.offset().top;

				if(thisOffsetLeft + 320 > $(window).width()){
					thisOffsetLeft = $(window).width() - 334;
				}

				if($this.data("fixed") == "Y"){
					$("#appProductPriceVariant").css({
						left: thisOffsetLeft,
						top: thisOffsetTop - $(window).scrollTop(),
						position: "fixed"
					});
				}else{
					$("#appProductPriceVariant").css({
						left: thisOffsetLeft,
						top: thisOffsetTop
					});
				}
				priceVariantOpen = true;
			});
		}

		return event.preventDefault();

	};

	var closePricesWindow = function(event){
		$("#appProductPriceVariant, .priceVariantStyles").remove();
		priceVariantOpen = false;
		return event.preventDefault();
	};

	var fastViewSku = function($product, http){
		var $namer = $product.find(".appFastViewProductHeadingLink");
		var $elPicture = $product.find(".picture");

		$namer.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]).html(http[0]["PRODUCT"]["NAME"]);

		$elPicture.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]);
		$elPicture.html($("<img/>").attr("src", http[0]["PRODUCT"]["PICTURE"]));

		$product.find(".addCart, .fastBack, .addCompare").data("id", http[0]["PRODUCT"]["ID"]);
		$product.find(".price").html(http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]).removeClass("getPricesWindow").removeAttr("href");

		if(http[0]["PRODUCT"]["RESULT_PROPERTIES"]){
			$product.find(".changeProperties").html(http[0]["PRODUCT"]["RESULT_PROPERTIES"]);
		}

		var $changeCart = $product.find(".addCart");
		
		$changeCart.find("img").remove();
		$changeCart.removeClass("added").removeClass("disabled")
			.html(LANG["ADD_BASKET_DEFAULT_LABEL"])
			.prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}))
			.attr("href", "#");

		if($product.data("hide-measure") != "Y" && http[0]["PRODUCT"]["MEASURE"] != undefined && http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] != ""){
			$product.find(".price").append(
				$("<span/>").addClass("measure").html(
					" / " + http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] + " "
				)
			);
		}

		if(http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0){
			$product.find(".price").append(
				$("<span/>").addClass("oldPriceLabel").html(CATALOG_LANG["FAST_VIEW_OLD_PRICE_LABEL"]).append(
					$("<s/>").addClass("discount").html(
						http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
					)
				)
			);
		}

		if(http[0]["PRODUCT"]["COUNT_PRICES"] > 1){
			$product.find(".price").addClass("getPricesWindow").data("id", http[0]["PRODUCT"]["ID"]).prepend($("<span/>", {class: "priceIcon"})).attr("href", "#");
		}else{
			$product.find(".price").find(".priceIcon").remove();
		}

		var $changeAvailable = $product.find(".changeAvailable");

		$changeAvailable.removeClass("getStoresWindow");
		$changeAvailable.removeClass("outOfStock");
		$changeAvailable.removeClass("onOrder");
		$changeAvailable.removeClass("inStock");
		$changeAvailable.removeAttr("href");


		if(http[0]["PRODUCT"]["CATALOG_QUANTITY"] > 0){
			if(http[0]["PRODUCT"]["STORES_COUNT"] > 1){
				$changeAvailable.html($("<span/>").html(LANG["CATALOG_AVAILABLE"])).addClass("inStock").attr("href", "#").addClass("getStoresWindow").data("id", http[0]["PRODUCT"]["ID"]);
				$changeAvailable.prepend(
					$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
				);
			}else{
				$changeAvailable.html(LANG["CATALOG_AVAILABLE"]).addClass("inStock");
				$changeAvailable.prepend(
					$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
				);
			}
		}else{
			if(http[0]["PRODUCT"]["CAN_BUY"] != "Y"){
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

		//article

		if(typeof(http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]) != "undefined"){
			if(typeof(http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) != "undefined" && http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"] !=""){
				$product.find(".changeArticle").html(http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]).parents(".article").removeClass("hidden");
			}else{
				if($product.find(".changeArticle").data("first-value")){
					$product.find(".changeArticle").html($product.find(".changeArticle").data("first-value"));
				}else{
					$product.find(".changeArticle").parents(".article").addClass("hidden");
				}
			}
		}

		if(http[0]["PRODUCT"]["IMAGES"]){
			
			var $appFastViewPictureSliderItems = $product.find(".appFastViewPictureSliderItems").css({left: 0});
			var $appFastViewPictureCarouselItems = $product.find(".appFastViewPictureCarouselItems").css({left: 0});
			
			$appFastViewPictureSliderItems.empty();
			$appFastViewPictureCarouselItems.empty();

			$.each(http[0]["PRODUCT"]["IMAGES"], function(i, nextElement){
				$appFastViewPictureSliderItems.append(
					$("<div />", {class: "appFastViewPictureSliderItem"}).append(
						$("<div />", {class: "appFastViewPictureSliderItemLayout"}).append(
							$("<a />", {class: "appFastViewPictureSliderItemLink", href: http[0]["PRODUCT"]["DETAIL_PAGE_URL"]}).data("loupe-picture", nextElement["SUPER_LARGE_PICTURE"]["src"]).append(
								$("<img />", {class: "appFastViewPictureSliderItemPicture", src: nextElement["LARGE_PICTURE"]["src"]})
							)
						)
					)
				);

				$appFastViewPictureCarouselItems.append(
					$("<div />", {class: "appFastViewPictureCarouselItem"}).append(
						$("<a />", {class: "appFastViewPictureCarouselItemLink", href: "#"}).append(
							$("<img />", {class: "appFastViewPictureCarouselItemPicture", src: nextElement["SMALL_PICTURE"]["src"]})
						)
					)
				);

			});

			fastViewInitPictureSlider();
			fastViewInitPictureCarousel();

		}
	};

	var selectSku = function(event){

		if(skuLoading == true){
			return false;
		}

		var _params = "";
		var _props = "";
		var _highload= "";
		var _product_width = 220;
		var _product_height = 200;

		var $_this = $(this);
		var $_mProduct = $_this.parents(".sku");
		var $_parentProp = $_this.parents(".skuProperty");
		var $_propList = $_mProduct.find(".skuProperty");
		var $_clickedProp = $_this.parents(".skuPropertyValue");

		var _level = $_parentProp.data("level");

		$_this.parents(".skuPropertyList").find("li").removeClass("selected");
		$_clickedProp.addClass("selected loading");

		skuLoading = true; //block

		// set product image paramets
		if($_mProduct.data("product-width") != undefined){
			_product_width = $_mProduct.data("product-width");
		}

		if($_mProduct.data("product-height") != undefined){
			_product_height = $_mProduct.data("product-height");
		}

		$_propList.each(function(i, prop){

			var $_nextProp  = $(prop);
			var $_nextPropList = $_nextProp.find("li");

			var propName = $_nextProp.data("name");
			var _used = false;

			if($_nextProp.data("highload") == "Y"){
				_highload = _highload + propName + ";"
			}

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

		$.getJSON(ajaxPath + "?act=selectSku&props=" + encodeURI(_props) + "&params=" + encodeURI(_params) + "&level=" + _level + "&iblock_id=" + $_mProduct.data("iblock-id") + "&prop_id=" + $_mProduct.data("prop-id") + "&product_id=" + $_mProduct.data("product-id") + "&highload=" + encodeURI(_highload) + "&product_width=" + _product_width + "&product_height=" + _product_height + "&product-change-prop=" + $_mProduct.data("change-prop") + "&product-more-pictures=" + $_mProduct.data("more-pictures"))
		  .done(function(http){
	  		$_propList.each(function(pI, pV){
	  			var $_sf = $(pV);
	  				$_sf.data("level") > _level && $_sf.find(".skuPropertyValue").removeClass("selected").addClass("disabled");
	  		});
			$.each(http[1]["PROPERTIES"], function(name, val){
			  	var $_gPropList = $_propList.filter(function(){ return ($(this).data("name") == name); });
			  	var $_gPropListValues = $_gPropList.find(".skuPropertyValue");
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
			
			if($_mProduct.data("cast-func")){ 
				eval($_mProduct.data("castFunc"))($_mProduct,  http); // callback function for cast sku change.
			}else{

				var $namer = $_mProduct.find(".name");
				var $nameMiddler = $namer.find(".middle");
				var $elPicture = $_mProduct.find(".picture");

				if($nameMiddler){
					$namer.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]);
					$nameMiddler.html(http[0]["PRODUCT"]["NAME"]);
				}else{
					$namer.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]).html(http[0]["PRODUCT"]["NAME"]);
				}

				$elPicture.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]);
				$elPicture.html($("<img/>").attr("src", http[0]["PRODUCT"]["PICTURE"]));
				$elPicture.append($("<span />", {class: "getFastView"}).data("id", http[0]["PRODUCT"]["ID"]).html(LANG["FAST_VIEW_PRODUCT_LABEL"]));

				$_mProduct.find(".addCart, .fastBack, .addCompare").data("id", http[0]["PRODUCT"]["ID"]);
				$_mProduct.find(".price").html(http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"] + " ").removeClass("getPricesWindow");

				var $changeCart = $_mProduct.find(".addCart");
				
				$changeCart.find("img").remove();
				$changeCart.removeClass("added").removeClass("disabled")
					.html(LANG["ADD_BASKET_DEFAULT_LABEL"])
					.prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}))
					.attr("href", "#");

				if($_mProduct.data("hide-measure") != "Y" && http[0]["PRODUCT"]["MEASURE"] != undefined && http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] != ""){
					$_mProduct.find(".price").append(
						$("<span/>").addClass("measure").html(
							" / " + http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] + " "
						)
					);
				}
				
				if(http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0){
					$_mProduct.find(".price").append(
						$("<s/>").addClass("discount").html(
							http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
						)
					);
				}
				
				if(http[0]["PRODUCT"]["COUNT_PRICES"] > 1){
					$_mProduct.find(".price").addClass("getPricesWindow").data("id", http[0]["PRODUCT"]["ID"]).prepend($("<span/>", {class: "priceIcon"})).attr("href", "#");
				}else{
					$_mProduct.find(".price").find(".priceIcon").remove();
				}
				
				var $changeAvailable = $_mProduct.find(".changeAvailable");

				$changeAvailable.removeClass("getStoresWindow");
				$changeAvailable.removeClass("outOfStock");
				$changeAvailable.removeClass("onOrder");
				$changeAvailable.removeClass("inStock");
				$changeAvailable.removeAttr("href");


				if(http[0]["PRODUCT"]["CATALOG_QUANTITY"] > 0){
					if(http[0]["PRODUCT"]["STORES_COUNT"] > 1){
						$changeAvailable.html($("<span/>").html(LANG["CATALOG_AVAILABLE"])).addClass("inStock").attr("href", "#").addClass("getStoresWindow").data("id", http[0]["PRODUCT"]["ID"]);
						$changeAvailable.prepend(
							$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
						);
					}else{
						$changeAvailable.html(LANG["CATALOG_AVAILABLE"]).addClass("inStock");
						$changeAvailable.prepend(
							$("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
						);
					}
				}else{
					if(http[0]["PRODUCT"]["CAN_BUY"] != "Y"){
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
			}

			$_clickedProp.removeClass("loading");
			skuLoading = false;

		  }).fail(function(jqxhr, textStatus, error){
		  	$_clickedProp.removeClass("loading");
		  	skuLoading = false;
		    alert("Request Failed: " + textStatus + ", " + error);
		});

		event.preventDefault();

	}

	var addCart = function(event){
		
		var $this = $(this);
		var productID = $this.data("id");
		var quantity = $this.data("quantity");

		var _arID = [];

		if($this.attr("href") === "#"){

			if($this.hasClass("multi")){
				if($this.data("selector") != "" && $this.attr("href") === "#"){
					$this.addClass("loading").text(LANG["ADD_CART_LOADING"]);
					var $addElements = $($this.data("selector")).filter(":not(.disabled)");
					if($addElements.length > 0){
						$addElements.each(function(x, elx){
							var $elx = $(elx);
							if($elx.data("id") != ""){
								_arID[x] = $elx.data("id");
							}
						});

						if(_arID != ""){
							$.getJSON(ajaxPath + "?act=addCart&id=" + _arID.join(";") + "&q=1&multi=1&site_id=" + SITE_ID, function(data) {
								var $imageAfterLoad = $this.find("img");
								$this.text(LANG["ADDED_CART_SMALL"])
									.attr("href", SITE_DIR + "personal/cart/")
									.prepend($imageAfterLoad.attr("src", TEMPLATE_PATH + "/images/added.png"))
									.removeClass("loading")
									.addClass("added");
								cartReload();
							});
						}else{
							alert("error (5)");
						}
					}else{
						alert("error(6)");
					}
					event.preventDefault();
				}
			}else{

				if(parseInt(productID, 10) > 0){
					
					$this.addClass("loading");

					var gObj = {
						act: "addCart",
						id: productID,
						q: (quantity > 0 ? quantity : 1),
						site_id: SITE_ID
					};

					$.getJSON(ajaxPath, gObj).done(function(jData){
						
						var reloadCart = cartReload();
						var cartWindow = displayWindow(jData);
						var $imageAfterLoad = $this.find("img");

						$this.removeClass("loading")
							.addClass("added")
							.html(LANG["BASKET_ADDED"])
							.prepend($imageAfterLoad.attr("src", TEMPLATE_PATH + "/images/added.png"))
							.attr("href", SITE_DIR + "personal/cart/");
					
					}).fail(function(jqxhr, textStatus, error){
						
						$.get(ajaxPath, gObj).done(function(Data){
							console.log(Data)
						});

						$this.removeClass("loading")
									.addClass("error");
					   
					    console.error(
					    	"Request Failed: " + textStatus + ", " + error
					    );

					});

				}
			}

			return event.preventDefault();
		}
	}

	var displayWindow = function(jData){
		if($(window).width() > 700){
			var $appBasket = $("#appBasket").show();
			var $container = $appBasket.find(".container");
			var $foundation = $("#foundation").addClass("blurred");
			var $changeAvailable = $appBasket.find(".availability");
			var $moreLink = $appBasket.find(".moreLink").attr("href", jData["DETAIL_PAGE_URL"]);
			var $image = $appBasket.find(".image").attr("src", $appBasket.data("load"));
			var $wishlist = $appBasket.find(".addWishlist").data("id", jData["ID"]);
			var $compare = $appBasket.find(".addCompare").data("id", jData["ID"]);
			var $picture = $appBasket.find(".picture");
			var $delete = $appBasket.find(".delete").data("id", jData["CART_ID"]);
			var $price = $appBasket.find(".price").html(jData["PRICE"]).data({"price": jData["~PRICE"], "discount": jData["OLD_PRICE"]});
			var $allSum = $appBasket.find(".allSum").html(jData["SUM"]);
			var $name = $appBasket.find(".name").text(jData["NAME"])
			var $qty = $appBasket.find(".qty").val(jData["QUANTITY"]).data("id", jData["ID"]).data("ratio", jData["ADDBASKET_QUANTITY_RATIO"]).removeClass("error");
			var $minus = $appBasket.find(".minus").data("id", jData["ID"]);
			var $plus = $appBasket.find(".plus").data("id", jData["ID"]);

			$changeAvailable.removeClass("outOfStock");
			$changeAvailable.removeClass("onOrder");
			$changeAvailable.removeClass("inStock");


			if(jData["CATALOG_QUANTITY"] > 0){
				$changeAvailable.addClass("inStock");
			}else{
				if(jData["CAN_BUY"] != true){
					$changeAvailable.addClass("outOfStock");
				}else{
					$changeAvailable.addClass("onOrder");		
				}
			}

			if(jData["MEASURE_SYMBOL_RUS"] != undefined && jData["MEASURE_SYMBOL_RUS"] != ""){
				$price.append(
					$("<span/>").addClass("measure").html(
						" / " + jData["MEASURE_SYMBOL_RUS"] + " "
					)
				);
			}

			if(parseInt(jData["DISCOUNT_PRICE"], 10) > 0){
				$price.append(
					$("<s>")
						.addClass("discount")
							.html(jData["DISCOUNT_PRICE"])
				);
			}

			if(parseInt(jData["DISCOUNT_SUM"], 10) > 0){
				$allSum.append(
					$("<s>")
						.addClass("discount")
							.html(jData["DISCOUNT_SUM"])
				);
			}

			if(jData["RATING"] != undefined){
				
				$container.prepend(
					$("<div>").addClass("rating")
						.append(
							$("<i>")
								.addClass("m")
									.css("width", (jData["RATING"] * 100 / 5) + "%")
						)
							.append(
								$("<i>")
									.addClass("h")
							)

				);
			}

			$picture.find(".markerContainer")
										.remove();

			if(jData["MARKER"] != undefined){
				
				$picture.prepend(
					$("<div>").addClass("markerContainer")
						.append(
							jData["MARKER"]
						)

				);
			}

			loadingPictureControl(jData["DETAIL_PICTURE"], function(){
				$image.attr("src", jData["DETAIL_PICTURE"]);
			});

			appOpen = true;  //global flag
		}

	};

	var appBasketDelete = function(event){

		var $this = $(this)
						.addClass("loading");

		var gObj = {
			id: $this.data("id"),
			act: "del"
		};

		$.get(ajaxPath, gObj).done(function(hData){
			
			if(hData != ""){
				$("#appBasket .closeWindow").trigger("click");
				$this.removeClass("loading");
				cartReload();
			}else{
				$this.removeClass("loading")
							.addClass("error");
			}

		}).fail(function(jqxhr, textStatus, error){
			
			$this.removeClass("loading")
						.addClass("error");
		   
		    console.error(
		    	"Request Failed: " + textStatus + ", " + error
		    );

		});

		return event.preventDefault();
	};

	var appBasketClose = function(event){
		
		var $appBasket = $("#appBasket").hide();
		var $foundation = $("#foundation").removeClass("blurred");
		
		appOpen = false;  //global flag
		
		return event.preventDefault();
	};

	var appBasketMinus = function(event){
		var $this = $(event.currentTarget);
		var $qty = $this.siblings(".qty").removeClass("error");
		var gQuantity = Number($qty.val());
		var qtyRatio = Number($qty.data("ratio"));

		if(gQuantity > qtyRatio){
			$qty.val((gQuantity * 10 - qtyRatio * 10 ) / 10);
		}
		
		appBasketCalc($qty, $this.data("id"));
		return event.preventDefault();
	};

	var appBasketPlus = function(event){

		var $this = $(event.currentTarget);
		var $qty = $this.siblings(".qty").removeClass("error");
		var gQuantity = Number($qty.val());
		var qtyRatio = Number($qty.data("ratio"));

		$qty.val((gQuantity * 10 + qtyRatio * 10) / 10);
		appBasketCalc($qty, $this.data("id"));

		return event.preventDefault();
	};

	var appBasketChange = function(event){
		
		var $this = $(this);
		var gValue = $this.val();
		var qtyRatio = Number($this.data("ratio"));
		var wValue;

		if(gValue.replace(/[^\d]/gi, '') != gValue){
			wValue = qtyRatio;
		}else if(Number(gValue) > qtyRatio){
			wValue = Number(gValue); 
		}else{
			wValue = qtyRatio;
		}

		wValue = Math.ceil(wValue / qtyRatio) * qtyRatio;

		var tmpWValue = $this.val(wValue);
		var tmpWId = $this.data("id");
		
		clearTimeout(appBasketChangeTimeout);
		appBasketChangeTimeout = setTimeout(function() {
			appBasketCalc(tmpWValue, tmpWId);
		}, 600);

		return event.preventDefault();
	};

	var appBasketCalc = function($qty, productID){
		
		var $appBasket = $("#appBasket");
		var $price = $appBasket.find(".price");
		var $sum = $appBasket.find(".allSum");
		var gStrSum = $sum.html().replace(/\d\.\d/g, '').replace(/[0-9]/g, '');
		$qty.removeClass("error");

		var tmpPriceSum = $price.data("price") * $qty.val();

		$sum.html(
			formatPrice(
				tmpPriceSum.toFixed(2)
			) + gStrSum
		);
	
		if($price.data("discount") > 0){

			var $sumDiscount = $sum.find(".discount");
			var gstrSumDiscount = $sumDiscount.html().replace(/\d\.\d/g, '').replace(/[0-9]/g, '');

			$sumDiscount.html(
				formatPrice(
					$price.data("discount") * $qty.val()
				) + gstrSumDiscount	
			);
		}

		clearTimeout(flushTimeout);
		flushTimeout = setTimeout(function() {
			flushCart(productID, $qty.val())
		}, 500);

	};

	var appBasketPlusHold = function(event){
		intervalID = setInterval(function() {
	        appBasketPlus(event);
	    }, 150);
	};	

	var appBasketPlusHoldUp = function(){
		clearInterval(intervalID);
	};

	var appBasketMinusHold = function(event){
		intervalID = setInterval(function() {
	        appBasketMinus(event);
	    }, 150);
	};	

	var appBasketMinusHoldUp = function(){
		clearInterval(intervalID);
	};

	var addCompare = function(event){

		var $this = $(event.currentTarget);
		var $icon = $this.find("img");
		var productID = $this.data("id");

		if($this.attr("href") == "#"){
			if(parseInt(productID, 10) > 0 && !$this.hasClass("added")){
				
				$this.addClass("loading");

				var gObj = {
					id: productID,
					act: "addCompare"
				};

				$.get(ajaxPath, gObj).done(function(hData){
					if(hData != ""){
						var reloadCart = cartReload();
						if($this.data("no-label") == "Y"){
							$this.removeClass("loading")
										.addClass("added")
											.attr("href", SITE_DIR + "compare/");
						}else{
							$this.removeClass("loading")
										.addClass("added")
											.html(LANG["ADD_COMPARE_ADDED"])
												.prepend($icon)
													.attr("href", SITE_DIR + "compare/");
						}
					}else{
						$this.removeClass("loading")
								.addClass("error");
					}
				}).fail(function(jqxhr, textStatus, error){
					
					$this.removeClass("loading")
								.addClass("error");
				   
				    console.error(
				    	"Request Failed: " + textStatus + ", " + error
				    );

				});
			}

			return event.preventDefault();
		}
	};

	var addWishlist = function(event){
		
		var $this = $(event.currentTarget);
		var $icon = $this.find("img");
		var productID = $this.data("id");

		if($this.attr("href") == "#"){
			if(parseInt(productID, 10) > 0 && !$this.hasClass("added")){
				
				$this.addClass("loading");

				var gObj = {
					id: productID,
					act: "addWishlist"
				};

				$.get(ajaxPath, gObj).done(function(hData){
					if(hData != ""){
						var reloadCart = cartReload();
						if($this.data("no-label") == "Y"){
							$this.removeClass("loading")
										.addClass("added")
											.attr("href", SITE_DIR + "wishlist/");
						}else{
							$this.removeClass("loading")
										.addClass("added")
											.html(LANG["WISHLIST_ADDED"])
												.prepend($icon)
													.attr("href", SITE_DIR + "wishlist/");
						}
					}else{
						$this.removeClass("loading")
									.addClass("error");
					}
				}).fail(function(jqxhr, textStatus, error){
					
					$this.removeClass("loading")
								.addClass("error");
				   
				    console.error(
				    	"Request Failed: " + textStatus + ", " + error
				    );

				});
			}

			return event.preventDefault();
		}
	};

	var openFastBack = function(event){

		var $this = $(this);
		var $appFastBuy = $("#appFastBuy");
		var $foundation = $("#foundation").addClass("blurred");

		$("#fastBuyOpenContainer").show();
		$("#fastBuyResult").hide();

		$("#fastBuyForm").find('input[type="text"], textarea').val("");

		var productID = $this.data("id");
		
		$this.addClass("loading");

		var gObj = {
			id: productID,
			act: "getFastBuy"
		};

		$.getJSON(ajaxPath, gObj).done(function(jData){
			
			$this.removeClass("loading");
			$appFastBuy.find("#fastBuyPicture .url, #fastBuyName .url").attr("href", jData[0]["DETAIL_PAGE_URL"]);
			$appFastBuy.find("#fastBuyPicture .picture").attr("src", $appFastBuy.data("load"));
			$appFastBuy.find("#fastBuyPrice").html(jData[0]["PRICE"]["PRICE_FORMATED"]);
			$appFastBuy.find("#fastBuyName .middle").html(jData[0]["NAME"]);	
			$appFastBuy.find("#fastBuyFormId").val(jData[0]["ID"]);
			$appFastBuy.find(".markerContainer").remove();

			if(jData[0]["MARKER"] != undefined){
				
				$appFastBuy.find("#fastBuyPicture").prepend(
					$("<div>").addClass("markerContainer")
						.append(
							jData[0]["MARKER"]
						)

				);
			}

			$appFastBuy.show();	

			loadingPictureControl(jData[0]["PICTURE"]["src"], function(){
				$appFastBuy.find("#fastBuyPicture .picture").attr("src", jData[0]["PICTURE"]["src"]);
			});

		}).fail(function(jqxhr, textStatus, error){
			
			$.get(ajaxPath, gObj).done(function(Data){
				console.log(Data)
			});

			$this.removeClass("loading")
						.addClass("error");
		   
		    console.error(
		    	"Request Failed: " + textStatus + ", " + error
		    );

		});

		fastBuyOpen = true;

		return event.preventDefault();
	};

	var sendFastBack = function(event){
		
		var $this = $(this).addClass("loading");
		var $fastBuyForm = $("#fastBuyForm");
		var $fastBuyFormName = $fastBuyForm.find("#fastBuyFormName").removeClass("error");
		var $fastBuyFormTelephone = $fastBuyForm.find("#fastBuyFormTelephone").removeClass("error");

		if($fastBuyFormName.val() == ""){
			$fastBuyFormName.addClass("error");
		}

		if($fastBuyFormTelephone.val() == ""){
			$fastBuyFormTelephone.addClass("error");
		}

		if($fastBuyFormName.val() != "" && $fastBuyFormTelephone.val() !=""){

			$.getJSON(ajaxPath + "?" + $fastBuyForm.serialize()).done(function(jData){
				
				$("#fastBuyResultTitle").html(jData["heading"]);
				$("#fastBuyResultMessage").html(jData["message"]);

				$("#fastBuyOpenContainer").hide();
				$("#fastBuyResult").show();
				
				$this.removeClass("loading");

			}).fail(function(jqxhr, textStatus, error){
				
				$this.removeClass("loading").addClass("error");
			   
			    console.error(
			    	"Request Failed: " + textStatus + ", " + error
			    );

			});

		}else{
			$this.removeClass("loading");
		}

		return event.preventDefault();
	};

	var closeFastBack = function(event){
		var $appFastBuy = $("#appFastBuy").hide();
		var $foundation = $("#foundation").removeClass("blurred");
		return event.preventDefault();
	};

	var removeFromWishlist = function(event){
		
		var $this = $(this);
		var $wishlist = $("#wishlist");
		var $parentThis = $(this).parents(".item");
		var productID = $this.data("id");
				$this.addClass("loading");

		var gObj = {
			id: productID,
			act: "removeWishlist"
		};

		$.get(ajaxPath, gObj).done(function(hData){
			if(hData != ""){
				if($wishlist.find(".product").length == 1){
					window.location.reload();
				}else{
					reloadCart = cartReload();
					$parentThis.remove();
				}
			}else{
				$this.removeClass("loading")
							.addClass("error");
			}
		}).fail(function(jqxhr, textStatus, error){
			
			$this.removeClass("loading")
						.addClass("error");
		   
		    console.error(
		    	"Request Failed: " + textStatus + ", " + error
		    );

		});

		return event.preventDefault();
	
	};

    var slideCollapsedBlock = function(event){
    	var $collapsed =  $("#left").children(".collapsed");
		if(!$collapsed.is(":visible") || $collapsed.hasClass("toggled")){
	    	$collapsed.stop().slideToggle().addClass("toggled");
	    	return event.preventDefault();
	    }
    };

    var openSmartFiler = function(event){
    	$smartFilterForm = $("#smartFilterForm");
    	if($smartFilterForm.is(":visible")){
    		$smartFilterForm.stop().slideUp("fast");
    	}else{
    		$smartFilterForm.stop().slideDown("fast");
    	}
    };

    var openSmartSections = function(event){
    	$smartSections = $("#nextSection ul");
    	if($smartSections.is(":visible")){
    		$smartSections.stop().slideUp("fast");
    	}else{
    		$smartSections.stop().slideDown("fast");
    	}
    };

	var formatPrice = function(data) {
		var price = String(data).split('.');
		var strLen = price[0].length;
		var str = "";

		for (var i = strLen; i > 0; i--) {
			str = str + ((!(i % 3) ? " " : "") + price[0][strLen - i]);
		}

		return str + (price[1] != undefined ? "." + price[1] : "");
	}

	var closeElementsAfterClick = function(event){
		
		if(appOpen){
			appBasketClose(event);
		}

		if(fastBuyOpen){
			$("#appFastBuy").hide();
			$("#foundation").removeClass("blurred");
			fastBuyOpen = false;
		}

		if(fastViewOpen){
			$("#appFastView").remove();
			fastViewOpen = false;
		}

		if(fastViewStoresOpen){
			$("#fastViewStores").remove();
			fastViewStoresOpen = false;
		}

		if(priceVariantOpen){
			$("#appProductPriceVariant").remove();
			priceVariantOpen = false;
		}

	};

    var loadingPictureControl = function(imagePath, callBack){
    
        if(imagePath){
            var newImage = new Image();
            $(newImage).one("load", callBack);
            newImage.src = imagePath;
        }
   
    };

    $(document).on("click", "#appBasket .closeWindow", appBasketClose);
    $(document).on("click", "#appBasket .delete", appBasketDelete);
    $(document).on("click", "#appBasket .minus", appBasketMinus);
    $(document).on("click", "#appBasket .plus", appBasketPlus);
    $(document).on("keyup", "#appBasket .qty", appBasketChange);
	
	$(document).on("click", ".skuPropertyLink", selectSku);
	$(document).on("click", ".addCart", addCart);

	$(document).on("click", ".addWishlist", addWishlist);
	$(document).on("click", ".addCompare", addCompare);
	$(document).on("click", ".fastBack", openFastBack);
	$(document).on("click", "#fastBuyFormSubmit", sendFastBack);
	$(document).on("click", "#appFastBuy .closeWindow", closeFastBack);
	$(document).on("click", ".removeFromWishlist", removeFromWishlist);

	$(document).on("mouseout",  "#appBasket .plus", appBasketPlusHoldUp);
	$(document).on("mouseup",   "#appBasket .plus", appBasketPlusHoldUp);
	$(document).on("mousedown", "#appBasket .plus", appBasketPlusHold);

	$(document).on("mousedown", "#appBasket .minus", appBasketMinusHold);
	$(document).on("mouseout", "#appBasket .minus", appBasketMinusHoldUp);
	$(document).on("mouseup", "#appBasket .minus", appBasketMinusHoldUp);

	$(document).on("click", "#fastViewStores .fastViewStoresExit", closeStoresWindow);
	$(document).on("click", ".getStoresWindow", getStoresWindow);

	$(document).on("click", "#appProductPriceVariant .appPriceVariantExit", closePricesWindow);
	$(document).on("click", ".getPricesWindow", getPricesWindow);

	$(document).on("click", ".getFastView", getFastView);

    $(document).on("click", "#catalogMenuHeading", slideCollapsedBlock);
    $(document).on("click", "#smartFilter .heading", openSmartFiler);
    $(document).on("click", "#nextSection .title", openSmartSections);

    $(document).on("click", "#appBasketContainer, #appFastView .appFastViewContainer, #fastViewStores .fastViewStoresContainer, #appProductPriceVariant, #appFastBuyContainer, .getFastView, .getPricesWindow, .fastBack, .addCart", function(event){
    	return event.stopImmediatePropagation();
    });

    //close elements after document click
    $(document).on("click", closeElementsAfterClick);

	// ajax all error;

	$(document).ajaxError(function( event, request, settings ) {
		console.error("Error requesting page " + settings.url);
	});

	$(document).on("click", "#footerTabsCaption .item", function(event){
		$(this).find("a").addClass("selected");
		$(this).siblings(".item").find("a").removeClass("selected");
		$("#footerTabs").find(".tab").hide().eq($(this).index()).show();
		return event.preventDefault();
	});

	$(document).on("click", "#infoTabsCaption .item", function(event){
		$(this).find("a").addClass("selected");
		$(this).siblings(".item").find("a").removeClass("selected");
		$("#infoTabs").find(".tab").hide().eq($(this).index()).show();
		return event.preventDefault();
	});

});


var formatPrice = function(data) {
	var price = String(data).split('.');
	var strLen = price[0].length;
	var str = "";

	for (var i = strLen; i > 0; i--) {
		str = str + ((!(i % 3) ? " " : "") + price[0][strLen - i]);
	}

	return str + (price[1] != undefined ? "." + price[1] : "");
}
