$(function(){
	$("#colorSwitcher .items .item a").on("click", function(){
		$("#colorSwitcher .item").removeClass("selected").find("a").removeClass("loading");
		var $this = $(this).addClass("loading");
		$this.parent().addClass("selected");
		var date = new Date(new Date().getTime() + 600 * 1000);
		document.cookie = "TEMPLATE_THEME_NAME=" + $this.data("color-name") + "; path=/; expires=" + date.toUTCString();
		location.reload();
		$(".colorSwitcherLoader").fadeIn("slow").addClass("visible");
		return event.preventDefault();
	});

	$("#colorSwitcher .backgroundItems .item a").on("click", function(){
		$("#colorSwitcher .backgroundItems .item").removeClass("selected").find("a").removeClass("loading");
		var $this = $(this).addClass("loading");
		$this.parent().addClass("selected");
		var date = new Date(new Date().getTime() + 600 * 1000);
		document.cookie = "TEMPLATE_BACKGROUND_NAME=" + $this.data("color-name") + "; path=/; expires=" + date.toUTCString();
		location.reload();
		$(".colorSwitcherLoader").fadeIn("slow").addClass("visible");
		return event.preventDefault();
	});

});