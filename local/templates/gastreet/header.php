<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
CJSCore::Init();
CJSCore::Init(["jquery3"]);

$asset = \Bitrix\Main\Page\Asset::getInstance();

// css
$asset->addCss(SITE_TEMPLATE_PATH . '/template_styles.css');

// js
$asset->addJs(SITE_TEMPLATE_PATH. '/js/mask.js');
$asset->addJs(SITE_TEMPLATE_PATH. '/js/scripts.js');

?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
	<head>
		<meta charset="<?= SITE_CHARSET ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0" />
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />

		<?$APPLICATION->ShowHead();?>
		<title><?$APPLICATION->ShowTitle();?></title>
	</head>
	<body>
		<div id="panel">
			<?$APPLICATION->ShowPanel();?>
		</div>

