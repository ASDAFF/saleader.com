<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
//test_dump($arResult);

?>
<?if(!empty($arResult)):?>
	<div class="bx_page" class="contactList"  itemscope itemtype="http://schema.org/Organization">
		<h2>IPTV <span itemprop="name">Интернет-магазин IP видеонаблюдения</span></h2>
		<ul>
			<li>
				<table>
					<tbody>
					<tr>
						<td>
							<img alt="cont1.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont1.png" title="cont1.png">
						</td>
						<td>
							<a href="tel:<?=$arResult['PROPERTIES']['TEL']['VALUE']?>" title=""
							   rel="nofollow" itemprop="telephone"><?=$arResult['PROPERTIES']['TEL']['VALUE']?></a>
						</td>
					</tr>
					</tbody>
				</table>
			</li>
			<li>
				<table>
					<tbody>
					<tr>
						<td>
							<img alt="cont2.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont2.png" title="cont2.png">
						</td>
						<td>
							<a href="mailto:<?=$arResult['PROPERTIES']['EMAIL']['VALUE']?>" title="Электронная почта интернет-магазина Лидер Продаж"
							   rel="nofollow" itemprop="email"><?=$arResult['PROPERTIES']['EMAIL']['VALUE']?></a>
						</td>
					</tr>
					</tbody>
				</table>
			</li>
			<li>
				<table>
					<tbody>
					<tr>
						<td>
							<img alt="cont3.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont3.png" title="cont3.png">
						</td>
						<td itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                            <span  itemprop="addressLocality">
                                <?=$arResult['PROPERTIES']['LOCATION']['VALUE']?>
                                </span>
							<span itemprop="postalCode">
								<?=$arResult['PROPERTIES']['ZIP']['VALUE']?>
                            </span>
							<br>
							<span  itemprop="streetAddress">
                                  <?=$arResult['PROPERTIES']['ADDRESS']['~VALUE']?>
                            </span>
						</td>
					</tr>
					</tbody>
				</table>
			</li>
			<li>
				<table>
					<tbody>
					<tr>
						<td>
							<img alt="cont4.png" src="<?= SITE_TEMPLATE_PATH ?>/images/cont4.png" title="cont4.png">
						</td>
						<td>
							   <?=$arResult['PROPERTIES']['SCHEDULE']['~VALUE']?>
						</td>
					</tr>
					</tbody>
				</table>
			</li>
		</ul>

	<? $APPLICATION->IncludeComponent(
		"bitrix:map.google.view",
		".default",
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"CONTROLS" => array(
				0 => "SMALL_ZOOM_CONTROL",
				1 => "TYPECONTROL",
				2 => "SCALELINE",
			),
			"INIT_MAP_TYPE" => "ROADMAP",
			"MAP_DATA" => "a:4:{s:10:\"google_lat\";d:".$arResult['PROPERTIES']['X']['VALUE'].";s:10:\"google_lon\";d:".$arResult['PROPERTIES']['Y']['VALUE'].";s:12:\"google_scale\";i:17;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:12:\"ipvi.store\";s:3:\"LON\";d:".$arResult['PROPERTIES']['Y']['VALUE'].";s:3:\"LAT\";d:".$arResult['PROPERTIES']['X']['VALUE'].";}}}",
			"MAP_HEIGHT" => "500",
			"MAP_ID" => "",
			"MAP_WIDTH" => "100%",
			"OPTIONS" => array(
				0 => "ENABLE_DBLCLICK_ZOOM",
				1 => "ENABLE_DRAGGING",
				2 => "ENABLE_KEYBOARD",
			)
		),
		false
	); ?><br>
	<noindex>
		<small><a
				href="https://www.google.com/maps/place/%D0%A2%D0%A0%D0%90%D0%9D%D0%A1%D0%A1%D0%9D%D0%90%D0%91,+%D1%82%D1%80%D0%B0%D0%BD%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%BD%D0%BE-%D0%BB%D0%BE%D0%B3%D0%B8%D1%81%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B0%D1%8F+%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D1%8F/@55.890684,37.712885,17z/data=!4m5!3m4!1s0x0:0xdd6f1834b1496f56!8m2!3d55.890607!4d37.712763?hl=ru"
				class="blackLink" target="_blank" rel="nofollow">Просмотреть увеличенную карту</a></small>
	</noindex>
	<h2 class="bold">ЗАДАТЬ ВОПРОС</h2>
	</div>
	<? $APPLICATION->IncludeComponent(
		"bitrix:main.feedback",
		".default",
		Array(
			"AJAX_MODE" => "Y",
			"COMPONENT_TEMPLATE" => ".default",
			"EMAIL_TO" => "em00s8@mail.ru",
			"EVENT_MESSAGE_ID" => array(0 => "7",),
			"OK_TEXT" => "Спасибо, ваше сообщение принято.",
			"REQUIRED_FIELDS" => array(),
			"USE_CAPTCHA" => "Y"
		)
	); ?>
	<h2 class="personalMenu bold">Реквизиты интернет-магазина</h2>
		<div class="description">
			 <?=$arResult['PROPERTIES']['REQ']['~VALUE']?>
		</div>
<?endif;?>