document.addEventListener("DOMContentLoaded", function(){

    // Показать input в Должности
    const positionInput = document.querySelector('.custom__position-wrapper');
    const positionOption = document.querySelector('.js-input__select-position');
    const positionCloseBtn = document.querySelector('.custom__position-close');
    const position2 = document.getElementById('POSITION2');

    positionOption.addEventListener('click', () =>{
        if (positionOption.value === "custom_position") {
            positionInput.classList.add("shown");
            positionOption.classList.add("hidden");
        } else {
            positionInput.classList.remove("shown");
            positionOption.classList.remove("hidden");
        }
    });

    positionCloseBtn.addEventListener('click', () => {
        positionInput.classList.remove("shown");
        positionOption.classList.remove("hidden");
        positionOption.options[0].selected = true;
        position2.value = '';
    })

    // Найти ИНН
    const btnINN = document.getElementById('js-btn-inn');
    const inputCompanyInn = document.getElementById('INN');
    const inputCompanyName = document.getElementById('COMPANY_NAME');
    const inputCompanyAddress = document.getElementById('COMPANY_ADDRESS');

    btnINN.addEventListener('click', (e) => {
        e.preventDefault();
        console.log(inputCompanyInn.value);

        $.ajax({
            type: "POST",
            url: "/ajax/fns.php",
            data: JSON.stringify({number_inn: inputCompanyInn.value, type: 'get_inn'}),
            success: function(responce){
                data = JSON.parse(responce)
                if(data.items[0].ЮЛ){
                    console.log("ЮЛ",data.items[0].ЮЛ.НаимПолнЮЛ);
                    inputCompanyName.value = data.items[0].ЮЛ.НаимПолнЮЛ;
                    inputCompanyAddress.value = data.items[0].ЮЛ.Адрес.АдресПолн;
                } else if(data.items[0].ИП){
                    console.log("ИП",data.items[0].ИП.ФИОПолн);
                    inputCompanyName.value = data.items[0].ИП.ФИОПолн;
                    inputCompanyAddress.value = data.items[0].ИП.Адрес.АдресПолн;
                } else{

                }
            }
        });
    })

    // Проверка заполнения обязательных полей
    const btnSend = document.querySelector('.js-btn-send');
    const messageBox = BX.UI.Dialogs.MessageBox.create(
        {
            message: "Заполните все обязательные поля",
            modal: true,
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
            onOk: function(messageBox)
            {
                console.log("onOk");
                messageBox.close();
            },
        }
    );

    btnSend.addEventListener('click', function(e) {
        const inputLastName = document.getElementById('LAST_NAME').value;
        const inputFirstName = document.getElementById('FIRST_NAME').value;
        const inputPhone = document.getElementById('PHONE_NUMBER').value;
        const inputEmail = document.getElementById('EMAIL').value;
        if (!inputLastName || !inputFirstName || !inputPhone || !inputEmail) {
            e.preventDefault();
            messageBox.show();
            setTimeout(() => {
                messageBox.close();
            }, 3000)
        }
    })




});
