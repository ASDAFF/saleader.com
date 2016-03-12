<?php
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global bool $bNeedAuth
 * @global array $currentUser
 * @global Bitrix\Seo\Engine\YandexDirect $engine
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Converter;
use Bitrix\Seo\Service;

$authAction = "";
$request = Context::getCurrent()->getRequest();

echo BeginNote();
if(!Service::isRegistered())
{
	$authAction = "registerClient();";
?>
	<input type=button onclick="<?=$authAction?>" value="<?=Loc::getMessage('SEO_YANDEX_REGISTER')?>"
 id="seo_authorize_btn" />
<?
}
elseif(!Service::isAuthorized())
{
	$authAction = "authorizeClient();";
?>
	<input type=button onclick="<?=$authAction?>" value="<?=Loc::getMessage('SEO_AUTH_YANDEX')?>"
 id="seo_authorize_btn" />
<?
}
else
{
	$authInfo = Service::getAuth($engine->getCode());
	if(!$authInfo)
	{
		$authorizeUrl = Service::getAuthorizeLink($engine->getCode());
?>
		<input type=button onclick="authorizeUser('<?= $authorizeUrl ?>')" value="<?= Loc::getMessage('SEO_AUTH_YANDEX') ?>" id="seo_authorize_btn"/>
<?
	}
	else
	{
		$currentUser = $authInfo['user'];
?>
<div id="auth_result" class="seo-auth-result">
	<b><?=Loc::getMessage('SEO_AUTH_CURRENT')?>:</b><div style="width: 300px; padding: 10px 0 0 0;">
		<?=Converter::getHtmlConverter()->encode($currentUser['real_name'].' ('.$currentUser['display_name'].')')?><br />
		<a href="javascript:void(0)" onclick="makeNewAuth()"><?=Loc::getMessage('SEO_AUTH_CANCEL')?></a>
		<div style="clear: both;"></div>
	</div>
</div>
<?
	}
}
echo EndNote();
?>

<script type="text/javascript">
	function makeNewAuth()
	{
		BX.showWait(BX('auth_result'));
		BX.ajax.loadJSON('/bitrix/tools/seo_yandex_direct.php?action=nullify_auth&sessid=' + BX.bitrix_sessid(), function(){
			window.location.reload();
		});
	}

	function registerClient()
	{
		BX('seo_authorize_btn').disabled = true;

		BX('seo_authorize_btn').value = '<?=CUtil::JSEscape(Loc::getMessage("SEO_YANDEX_REGISTER_RPOGRESS"))?>';

		BX.ajax.loadJSON('/bitrix/tools/seo_yandex_direct.php?action=register&sessid=' + BX.bitrix_sessid(), function(result)
		{
			if(result['result'])
			{
				authorizeClient();
			}
			else if(result["error"])
			{
				alert('<?=CUtil::JSEscape(Loc::getMessage("SEO_ERROR"))?> : ' + result['error']['message']);
				BX('seo_authorize_btn').value = '<?=CUtil::JSEscape(Loc::getMessage('SEO_YANDEX_REGISTER'))?>';
			}
		});
	}

	function authorizeClient()
	{
		BX('seo_authorize_btn').value = '<?=CUtil::JSEscape(Loc::getMessage("SEO_YANDEX_AUTH_RPOGRESS"))?>';

		BX.ajax.loadJSON('/bitrix/tools/seo_yandex_direct.php?action=authorize&sessid=' + BX.bitrix_sessid(), function(result)
		{
			if(result["location"])
			{
//				BX('seo_authorize_btn').value = '<?=CUtil::JSEscape(Loc::getMessage("SEO_YANDEX_AUTH_CONFIRM_RPOGRESS"))?>';

				BX.ajax.loadJSON(result["location"], function(r)
				{
					if(r['result'] == "ok")
					{
						BX.reload();
					}
					else if(r['error'])
					{
						alert('<?=CUtil::JSEscape(Loc::getMessage("SEO_ERROR"))?> ' + r['error'] + ': ' + r['error_description']);
						BX('seo_authorize_btn').value = '<?=CUtil::JSEscape(Loc::getMessage('SEO_AUTH_YANDEX'))?>';
					}
				})
			}
		});
	}

	function authorizeUser(url)
	{
		BX.util.popup(url, 680, 600);
	}

<?
if($request["auth"] && $authAction != "")
{
	echo $authAction;
}
?>
</script>