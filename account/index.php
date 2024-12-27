<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Аккаунт");

global $USER;
if (!$USER->IsAuthorized()) {
    LocalRedirect("/auth/");
}
?>

<div class="section__caption-body">
    <a href="#" class="button button-light-grey">
        Выйти из аккаунта
    </a>
</div>
</div>

<? $APPLICATION->IncludeComponent(
    "firstbit:account.form",
    "",
    array(
        "TITLE" => ""
    )
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
