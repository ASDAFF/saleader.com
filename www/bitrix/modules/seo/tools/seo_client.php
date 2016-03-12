<?php
/*
This is callback page for Dropbox OAuth 2.0 authentication.
Dropbox redirects only to specific back url set in the OAuth application.
The page opens in popup window after user authorized on Dropbox.
*/
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

if(CModule::IncludeModule("socialservices") && CSocServAuthManager::CheckUniqueKey())
{
	if(isset($_REQUEST["authresult"]))
	{
		$engine = $_REQUEST["engine"];

		\Bitrix\Seo\Service::clearAuth($engine, true);
?>
<script type="text/javascript">
	opener.location.reload();
	window.close();
</script>
<?
	}
	else
	{
		$result = array();

		$state = $_REQUEST["state"];
		if(isset($_REQUEST["code"]) && $_REQUEST["code"] <> '')
		{
			$engine = \Bitrix\Seo\Service::getEngine();
			$engine->getInterface()->setCode($_REQUEST["code"]);

			if($engine->getInterface()->GetAccessToken() !== false)
			{
				$engine->setAuthSettings($engine->getInterface()->getResult());

				$result["result"] = "ok";
			}
			else
			{
				$result = $engine->getInterface()->getError();
			}
		}
		else
		{
			$result["error"] = "Wrong request";
		}

		Header("Content-Type: application/json");
		echo \Bitrix\Main\Web\Json::encode($result);
	}
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");