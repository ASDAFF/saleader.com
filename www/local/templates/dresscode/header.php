<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
use Bitrix\Main\Page\Asset;

?>
<?
	$TEMPLATE_BACKGROUND_NAME = "white";
	$TEMPLATE_THEME_NAME = "default";
?>
<?
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
	<head>
		<meta charset="<?=SITE_CHARSET?>">
		<META NAME="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/apple-touch-icon-57x57.png" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon-precomposed" sizes="60x60" href="/apple-touch-icon-60x60.png" />
		<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/apple-touch-icon-120x120.png" />
		<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/apple-touch-icon-76x76.png" />
		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/apple-touch-icon-152x152.png" />
		<link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196" />
		<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" />
		<link rel="icon" type="image/png" href="/favicon-128.png" sizes="128x128" />
		<meta name="application-name" content=" "/>
		<meta name="msapplication-TileColor" content="#FFFFFF" />
		<meta name="msapplication-TileImage" content="/mstile-144x144.png" />
		<meta name="msapplication-square70x70logo" content="/mstile-70x70.png" />
		<meta name="msapplication-square150x150logo" content="/mstile-150x150.png" />
		<meta name="msapplication-wide310x150logo" content="/mstile-310x150.png" />
		<meta name="msapplication-square310x310logo" content="/mstile-310x310.png" />
		<?$APPLICATION->ShowLink("canonical", null, true);?>
		<?$APPLICATION->ShowMeta("keywords")?>
		<?$APPLICATION->ShowMeta("description")?>
		<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/fonts/roboto/roboto.css");?>
		<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/themes/".$TEMPLATE_BACKGROUND_NAME."/".$TEMPLATE_THEME_NAME."/style.css");?>
		<?$APPLICATION->ShowCSS(true, false);?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.11.0.min.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.easing.1.3.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/rangeSlider.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/system.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/topMenu.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/topSearch.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/dwCarousel.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/dwSlider.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/dwZoomer.js");?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/colorSwitcher.js");?>
		<?$APPLICATION->ShowHeadStrings();?>
		<?$APPLICATION->ShowHeadScripts();?>
		<title><?$APPLICATION->ShowTitle();?> < IPVI.STORE</title>
	</head>
<body class="loading <?if (INDEX_PAGE == "Y"):?>index<?endif;?>">
<? $APPLICATION->ShowViewContent('ProductScopeOpen') ?>

	<div id="panel">
		<?$APPLICATION->ShowPanel();?>
	</div>
	<div id="foundation">
		<div id="topHeader">
			<div class="limiter">
				<?$APPLICATION->IncludeComponent("bitrix:menu", "topMenu", Array(
					"ROOT_MENU_TYPE" => "top",
						"MENU_CACHE_TYPE" => "N",
						"MENU_CACHE_TIME" => "3600000",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => "",
						"MAX_LEVEL" => "1",
						"CHILD_MENU_TYPE" => "top",
						"USE_EXT" => "N",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N",
						"CACHE_SELECTED_ITEMS" => "N"
					),
					false
				);?>
				<ul id="topService">
					<?$APPLICATION->IncludeComponent("dresscode:sale.geo.positiion", "", array(),
						false,
						array(
						"ACTIVE_COMPONENT" => "Y"
						)
					);?>
					<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "top", Array(
						"REGISTER_URL" => "",
							"FORGOT_PASSWORD_URL" => "",
							"PROFILE_URL" => "",
							"SHOW_ERRORS" => "N",
						),
						false
					);?>
				</ul>
			</div>
		</div>
		<div id="subHeader">
			<div class="limiter">
				<div id="logo">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						".default",
						array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "logo",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div id="topHeading">
					<div class="vertical">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							".default",
							array(
								"AREA_FILE_SHOW" => "sect",
								"AREA_FILE_SUFFIX" => "heading",
								"AREA_FILE_RECURSIVE" => "Y",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</div>
				</div>
				<div id="headerTools">
					<ul class="tools">
						<li class="search">
							<div class="wrap">
								<a href="#" class="icon" id="openSearch"></a>
							</div>
						</li>
						<li class="telephone">
							<div class="wrap">
								<a href="<?=SITE_DIR?>callback/" class="icon callBack"></a>
								<div class="nf">
									<?$APPLICATION->IncludeComponent(
										"bitrix:main.include",
										".default",
										array(
											"AREA_FILE_SHOW" => "sect",
											"AREA_FILE_SUFFIX" => "phone",
											"AREA_FILE_RECURSIVE" => "Y",
											"EDIT_TEMPLATE" => ""
										),
										false
									);?>
								</div>
							</div>
						</li>
						<li class="wishlist">
							<div id="flushTopwishlist">
								<?$APPLICATION->IncludeComponent("dresscode:favorite.line", ".default", Array(
									),
									false
								);?>
							</div>
						</li>
						<li class="compare">
							<div id="flushTopCompare">
								<?$APPLICATION->IncludeComponent("dresscode:compare.line", ".default", Array(
									),
									false
								);?>
							</div>
						</li>
	             	 	<li class="cart"><div id="flushTopCart"><?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",
	"topCart",
	array(
		"HIDE_ON_BASKET_PAGES" => "N",
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/",
		"PATH_TO_REGISTER" => SITE_DIR."login/",
		"POSITION_FIXED" => "N",
		"SHOW_AUTHOR" => "N",
		"SHOW_EMPTY_VALUES" => "Y",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_PERSONAL_LINK" => "N",
		"SHOW_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"COMPONENT_TEMPLATE" => "topCart",
		"SHOW_DELAY" => "N",
		"SHOW_NOTAVAIL" => "N",
		"SHOW_SUBSCRIBE" => "N",
		"SHOW_IMAGE" => "Y",
		"SHOW_PRICE" => "Y",
		"SHOW_SUMMARY" => "Y"
	),
	false
);?></div></li>
					</ul>
				</div>
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							".default",
							array(
								"AREA_FILE_SHOW" => "sect",
								"AREA_FILE_SUFFIX" => "searchLine",
								"AREA_FILE_RECURSIVE" => "Y",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>

			</div>
		</div>
		<div id="main">
			<div class="limiter">
				<div class="compliter">
					<?if(ERROR_404 != "Y"):?>
					<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	".default",
	array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "leftBlock",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => ""
	),
	false
);?>
<?endif;?>
					<div id="right">

						<?if (INDEX_PAGE != "Y" && ERROR_404 != "Y"):?>
							<?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb", 
	"seocontext_dresscode", 
	array(
		"START_FROM" => "0",
		"PATH" => "",
		"SITE_ID" => "s1",
		"COMPONENT_TEMPLATE" => "seocontext_dresscode"
	),
	false
); ?>
<? endif; ?>