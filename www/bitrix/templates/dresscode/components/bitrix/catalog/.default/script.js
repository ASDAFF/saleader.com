$(function(){

	// if("CATALOG_BANNER_ENABLED" in window && "CATALOG_BANNER_BLOCK_ID" in window){
	// 	var $catalogSectionContainer = $("#" + CATALOG_BANNER_BLOCK_ID);
	// 	var $catalogSectionItems = $catalogSectionContainer.find(".item");
	// 	var $catalogSectionBanners = $("#catalog-section-banners");
	// 	if($catalogSectionItems.length > 14){
	// 		$catalogSectionItems.eq(9).after($catalogSectionBanners.removeClass("hidden"));
	// 	}
	// }

	var changeSortParams = function(){
		window.location.href = $(this).val();
	};

	$("#selectSortParams, #selectCountElements").on("change", changeSortParams);
});