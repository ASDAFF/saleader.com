<?
function check_mobile(){
	$arAgents = array(
		"iPhone",
		"Android",
		"webOS",
		"iPod",
		"HTC",
		"LG",
		"Lenovo",
		"NOKIA",
		"Symbian",
		"Windows Mobile",
		"SymbOS",
		"SAMSUNG",
		"SonyEricsson",
		"Alcatel",
		"BlackBerry",
		"WindowsMobile"
	);

	if(isset($_GET["FullVersion"])){
		setcookie("elMobile", N, time() + 3600000);
		return false;
	}

	if(isset($_GET["MobileVersion"])){
		setcookie("elMobile", Y, time() + 3600000);
		return true;
	}

	if(!empty($_COOKIE["elMobile"]) && $_COOKIE["elMobile"] == N){
		return false;
	}

	if(!empty($_COOKIE["elMobile"]) && $_COOKIE["elMobile"] == Y){
		return true;
	}

	foreach ($arAgents as $agent) {
		if(stripos($_SERVER["HTTP_USER_AGENT"], $agent)){
			setcookie("elMobile", Y, time() + 3600000);
			return true;
		}
	}

	return false;

}
?>