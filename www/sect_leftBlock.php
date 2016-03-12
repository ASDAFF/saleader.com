<div id="left">
	<a href="<?=SITE_DIR?>catalog/" class="heading orange<?$APPLICATION->ShowViewContent("menuRollClass");?>" id="catalogMenuHeading"><?=GetMessage("DRESS_CATALOG")?><ins></ins></a>
	<div class="collapsed">
		<?$APPLICATION->IncludeComponent("bitrix:menu", "leftMenu", Array(
			"ROOT_MENU_TYPE" => "left",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"MAX_LEVEL" => "4",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "leftSubMenu", Array(
			"ROOT_MENU_TYPE" => "left2",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "left2",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
			),
			false
		);?>
	</div>
	<?$APPLICATION->ShowViewContent("smartFilter");?>
	<div class="<?$APPLICATION->ShowViewContent("hiddenZoneClass");?>">
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list", 
			"leftNews", 
			array(
				"IBLOCK_TYPE" => "info",
				"IBLOCK_ID" => "8",
				"NEWS_COUNT" => "3",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_ORDER1" => "DESC",
				"SORT_BY2" => "SORT",
				"SORT_ORDER2" => "ASC",
				"FILTER_NAME" => "",
				"FIELD_CODE" => array(
					0 => "",
					1 => "ID",
					2 => "CODE",
					3 => "XML_ID",
					4 => "NAME",
					5 => "TAGS",
					6 => "SORT",
					7 => "PREVIEW_TEXT",
					8 => "PREVIEW_PICTURE",
					9 => "DETAIL_TEXT",
					10 => "DETAIL_PICTURE",
					11 => "DATE_ACTIVE_FROM",
					12 => "ACTIVE_FROM",
					13 => "DATE_ACTIVE_TO",
					14 => "ACTIVE_TO",
					15 => "SHOW_COUNTER",
					16 => "SHOW_COUNTER_START",
					17 => "IBLOCK_TYPE_ID",
					18 => "IBLOCK_ID",
					19 => "IBLOCK_CODE",
					20 => "IBLOCK_NAME",
					21 => "IBLOCK_EXTERNAL_ID",
					22 => "DATE_CREATE",
					23 => "CREATED_BY",
					24 => "CREATED_USER_NAME",
					25 => "TIMESTAMP_X",
					26 => "MODIFIED_BY",
					27 => "USER_NAME",
					28 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "",
					1 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "N",
				"CACHE_GROUPS" => "Y",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "N",
				"SET_BROWSER_TITLE" => "N",
				"SET_META_KEYWORDS" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"DISPLAY_DATE" => "Y",
				"DISPLAY_NAME" => "Y",
				"DISPLAY_PICTURE" => "Y",
				"DISPLAY_PREVIEW_TEXT" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"AJAX_OPTION_ADDITIONAL" => ""
			),
			false
		);?>
		<div id="subscribe" class="sideBlock">
		    <div class="sideBlockContent">
			    <?$APPLICATION->IncludeFile(SITE_DIR."sect_subscribe.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("SECT_SUBSCRIBE"), "TEMPLATE" => "sect_subscribe.php"));?>
				<?$APPLICATION->IncludeComponent("bitrix:subscribe.form", ".default", Array(
					"USE_PERSONALIZATION" => "Y",
						"PAGE" => "#SITE_DIR#personal/subscribe/subscr_edit.php",
						"SHOW_HIDDEN" => "Y",
						"AJAX_MODE" => "Y",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "3600",
					),
					false
				);?>
			</div>
		</div>
		<div class="sideBlock banner">
			<?$APPLICATION->IncludeFile(SITE_DIR."sect_left_banner1.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("SECT_LEFT_BANNER_1"), "TEMPLATE" => "sect_left_banner1.php"));?>
		</div>
		
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list", 
			"leftCollection", 
			array(
				"IBLOCK_TYPE" => "info",
				"IBLOCK_ID" => "5",
				"NEWS_COUNT" => "3",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_ORDER1" => "DESC",
				"SORT_BY2" => "SORT",
				"SORT_ORDER2" => "ASC",
				"FILTER_NAME" => "",
				"FIELD_CODE" => array(
					0 => "ID",
					1 => "CODE",
					2 => "XML_ID",
					3 => "NAME",
					4 => "TAGS",
					5 => "SORT",
					6 => "PREVIEW_TEXT",
					7 => "PREVIEW_PICTURE",
					8 => "DETAIL_TEXT",
					9 => "DETAIL_PICTURE",
					10 => "DATE_ACTIVE_FROM",
					11 => "ACTIVE_FROM",
					12 => "DATE_ACTIVE_TO",
					13 => "ACTIVE_TO",
					14 => "SHOW_COUNTER",
					15 => "SHOW_COUNTER_START",
					16 => "IBLOCK_TYPE_ID",
					17 => "IBLOCK_ID",
					18 => "IBLOCK_CODE",
					19 => "IBLOCK_NAME",
					20 => "IBLOCK_EXTERNAL_ID",
					21 => "DATE_CREATE",
					22 => "CREATED_BY",
					23 => "CREATED_USER_NAME",
					24 => "TIMESTAMP_X",
					25 => "MODIFIED_BY",
					26 => "USER_NAME",
					27 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "",
					1 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "N",
				"CACHE_GROUPS" => "Y",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "N",
				"SET_BROWSER_TITLE" => "N",
				"SET_META_KEYWORDS" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"DISPLAY_DATE" => "Y",
				"DISPLAY_NAME" => "Y",
				"DISPLAY_PICTURE" => "Y",
				"DISPLAY_PREVIEW_TEXT" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Коллекции",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"AJAX_OPTION_ADDITIONAL" => ""
			),
			false
		);?>

		<div class="sideBlock banner">
			<?$APPLICATION->IncludeFile(SITE_DIR."sect_left_banner2.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("SECT_LEFT_BANNER_2"), "TEMPLATE" => "sect_left_banner2.php"));?>
		</div>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list", 
		"leftService", 
		array(
			"IBLOCK_TYPE" => "info",
			"IBLOCK_ID" => "6",
			"NEWS_COUNT" => "3",
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_ORDER1" => "DESC",
			"SORT_BY2" => "SORT",
			"SORT_ORDER2" => "ASC",
			"FILTER_NAME" => "",
			"FIELD_CODE" => array(
				0 => "ID",
				1 => "CODE",
				2 => "XML_ID",
				3 => "NAME",
				4 => "TAGS",
				5 => "SORT",
				6 => "PREVIEW_TEXT",
				7 => "PREVIEW_PICTURE",
				8 => "DETAIL_TEXT",
				9 => "DETAIL_PICTURE",
				10 => "DATE_ACTIVE_FROM",
				11 => "ACTIVE_FROM",
				12 => "DATE_ACTIVE_TO",
				13 => "ACTIVE_TO",
				14 => "SHOW_COUNTER",
				15 => "SHOW_COUNTER_START",
				16 => "IBLOCK_TYPE_ID",
				17 => "IBLOCK_ID",
				18 => "IBLOCK_CODE",
				19 => "IBLOCK_NAME",
				20 => "IBLOCK_EXTERNAL_ID",
				21 => "DATE_CREATE",
				22 => "CREATED_BY",
				23 => "CREATED_USER_NAME",
				24 => "TIMESTAMP_X",
				25 => "MODIFIED_BY",
				26 => "USER_NAME",
				27 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "",
				1 => "",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "Y",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"SET_TITLE" => "N",
			"SET_BROWSER_TITLE" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"PAGER_TEMPLATE" => ".default",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"PAGER_TITLE" => "Новости",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"COMPONENT_TEMPLATE" => "leftService",
			"SET_LAST_MODIFIED" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"SHOW_404" => "N",
			"MESSAGE_404" => ""
		),
		false
	);?>
	</div>
</div>