<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form action="/account/" method="post" id="account_form">
    <div class="account__page">
        <div class="input-blocks">
            <div class="input-blocks__caption">
                Данные владельца личного кабинета
            </div>
            <div class="input-blocks__wrapper">
                <div class="input-blocks__items">
                    <div class="input-blocks__item">
                        <label for="LAST_NAME" class="input__title">
                            Фамилия<sup>*</sup>
                        </label>
                        <input name="LAST_NAME" id="LAST_NAME" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['LAST_NAME_VALUE']?>">
                    </div>
                    <div class="input-blocks__item">
                        <label for="FIRST_NAME" class="input__title">
                            Имя<sup>*</sup>
                        </label>
                        <input name="FIRST_NAME" id="FIRST_NAME" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['FIRST_NAME_VALUE']?>">
                    </div>
                    <div class="input-blocks__item">
                        <label for="PATRONYMIC" class="input__title">
                            Отчество
                        </label>
                        <input name="PATRONYMIC" id="PATRONYMIC" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['PATRONYMIC_VALUE']?>">
                    </div>
                </div>
                <div class="input-blocks__items">
                    <div class="input-blocks__item">
                        <label for="PHONE_NUMBER" class="input__title">
                            Номер телефона<sup>*</sup>
                        </label>
                        <input name="PHONE_NUMBER" id="PHONE_NUMBER" type="text" class="input phone-mask" placeholder="" value="<?=$arResult['ITEMS']['PHONE_NUMBER']?>">
                    </div>
                    <div class="input-blocks__item">
                        <label for="EMAIL" class="input__title">
                            Адрес электронной почты<sup>*</sup>
                        </label>
                        <input name="EMAIL" id="EMAIL" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['EMAIL']?>">
                    </div>
                    <div class="input-blocks__item input-blocks__item-empty">
                    </div>
                    <div class="input-blocks__item">
                        <label for="COUNTRY" class="input__title">
                            Страна
                        </label>
                        <select class="input" name="COUNTRY" id="COUNTRY">
                            <?foreach($arResult['ITEMS']['UF_COUNTRY_NAME'] as $item):?>
                                <option><?=$item?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                    <div class="input-blocks__item">
                        <label for="CITY" class="input__title">
                            Город
                        </label>
                        <input name="CITY" id="CITY" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['CITY_VALUE']?>">
                        <?/*
                        <select class="input" id="CITY">
                            <option>Город 1</option>
                            <option>Город 2</option>
                            <option>Город 3</option>
                            <option>Город 4</option>
                            <option>Город 5</option>
                        </select>
                        */?>
                    </div>
                </div>
            </div>
        </div>
        <div class="input-blocks">
            <div class="input-blocks__caption">
                Данные организации
            </div>
            <div class="block__alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M12 22C6.477 22 2 17.523 2 12C2 6.477 6.477 2 12 2C17.523 2 22 6.477 22 12C22 17.523 17.523 22 12 22ZM13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7C12.5523 7 13 7.44772 13 8ZM12 10C12.5523 10 13 10.4477 13 11V16C13 16.5523 12.5523 17 12 17C11.4477 17 11 16.5523 11 16V11C11 10.4477 11.4477 10 12 10Z"
                          fill="#1E1E1E"/>
                </svg>
                если ты представляешь организацию, обязательно заполни инн
            </div>
            <div class="input-blocks__wrapper">
                <div class="input-blocks__items">
                    <div class="input-blocks__item input-blocks__item-wbutt">
                        <div class="input-blocks__item-wbutt-wrapper">
                            <label for="INN" class="input__title">
                                ИНН
                            </label>
                            <input name="INN" id="INN" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['INN_VALUE']?>">
                        </div>
                        <input type="button" value="Найти" class="button" id="js-btn-inn">
                    </div>
                    <div class="input-blocks__item input-blocks__item-empty"></div>
                    <div class="input-blocks__item input-blocks__item-empty"></div>
                    <div class="input-blocks__item">
                        <label for="COMPANY_NAME" class="input__title">
                            Название компании
                        </label>
                        <textarea name="COMPANY_NAME" id="COMPANY_NAME" class="input" ><?=$arResult['ITEMS']['COMPANY_NAME_VALUE']?></textarea>
                    </div>
                    <div class="input-blocks__item">
                        <label for="COMPANY_ADDRESS" class="input__title">
                            Почтовый адрес
                        </label>
                        <textarea name="COMPANY_ADDRESS" id="COMPANY_ADDRESS" class="input" ><?=$arResult['ITEMS']['COMPANY_ADDRESS_VALUE']?></textarea>
                    </div>
                    <div class="input-blocks__item">
                        <label for="POSITION" class="input__title">
                            Должность
                        </label>
                        <select class="input js-input__select-position" name="POSITION" id="POSITION">
                            <option value="">-</option>
                            <?foreach($arResult['ITEMS']['UF_POSITION_NAME'] as $item):?>
                                <option value="<?=$item?>"><?=$item?></option>
                            <?endforeach;?>
                            <?if((array_search($arResult['ITEMS']['POSITION_VALUE'], $arResult['ITEMS']['UF_POSITION_NAME']) !== true) AND !empty($arResult['ITEMS']['POSITION_VALUE'])):?>
                                <option selected value="<?=$arResult['ITEMS']['POSITION_VALUE']?>"><?=$arResult['ITEMS']['POSITION_VALUE']?></option>
                            <?endif?>
                            <option value="custom_position">*Добавить должность*</option>
                        </select>
                        <div class="custom__position-wrapper">
                            <input name="POSITION2" id="POSITION2" type="text" class="input input-position" placeholder="" value="">
                            <div class="custom__position-close">
                                <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="50px" height="50px"><path d="M 25 2 C 12.309534 2 2 12.309534 2 25 C 2 37.690466 12.309534 48 25 48 C 37.690466 48 48 37.690466 48 25 C 48 12.309534 37.690466 2 25 2 z M 25 4 C 36.609534 4 46 13.390466 46 25 C 46 36.609534 36.609534 46 25 46 C 13.390466 46 4 36.609534 4 25 C 4 13.390466 13.390466 4 25 4 z M 32.990234 15.986328 A 1.0001 1.0001 0 0 0 32.292969 16.292969 L 25 23.585938 L 17.707031 16.292969 A 1.0001 1.0001 0 0 0 16.990234 15.990234 A 1.0001 1.0001 0 0 0 16.292969 17.707031 L 23.585938 25 L 16.292969 32.292969 A 1.0001 1.0001 0 1 0 17.707031 33.707031 L 25 26.414062 L 32.292969 33.707031 A 1.0001 1.0001 0 1 0 33.707031 32.292969 L 26.414062 25 L 33.707031 17.707031 A 1.0001 1.0001 0 0 0 32.990234 15.986328 z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="input-blocks__item">
                        <label for="COMPANY_BRAND" class="input__title">
                            Бренд, который вы представляете
                        </label>
                        <input name="COMPANY_BRAND" id="COMPANY_BRAND" type="text" class="input" placeholder="" value="<?=$arResult['ITEMS']['COMPANY_BRAND_VALUE']?>">
                    </div>
                </div>
                <div class="input-blocks__button">
                    <input name="account_form" type="submit" class="button js-btn-send" value="Сохранить изменения">
                </div>
            </div>
        </div>
    </div>
</form>
