$(document).ready(function () {
    reload_login();
});

$(window).on( "resize", function() {
    reload_login();
});

function reload_login() {
    var w=$('body').innerWidth();
    var f=600;/*ширина формы*/
    var p=70*2;/*отступ формы*/
    if(w<=1650){
        p=28*2;
    }
    var s=100;
    s=(w-f-p)/2;
    $('.login-page__logo').css('flex', '0 0 '+s+'px');
}

$(".phone-mask").mask("+7 (999) 999-99-99");

$(document).on('click','.js-send-sms',function(e){
    let tel_auth = $('#phone').val();
    let form_error = document.querySelector('.form-login__error');

    if (tel_auth!='') {
        e.target.disabled = true;
        setTimeout(function() {
            e.target.disabled = false;
            e.target.value = 'Получать SMS код еще раз...'
        }, 10000);

        $.ajax({
            type: "POST",
            url: "/ajax/sms.php",
            data: JSON.stringify({number_login: tel_auth, action: 'get_code'}),
            success: function(responce){
                data = JSON.parse(responce)
                //console.log(data);
                if(data.res == 'OK'){
                    form_error.innerHTML = '<span class="blue-not">Вам отправлен СМС код</span>';
                }
                else if(data.res == 'no-user'){
                    form_error.innerHTML = '<span class="blue-not">Регистрация нового пользователя...<br>Вам выслан код СМС</span>';
                }
                else if(data.res == 'NO' && data.status_code == 204){
                    form_error.innerHTML = 'Данный оператор не обслуживается';
                }
                else{
                    form_error.innerHTML = '<span class="red-not">Ошибка отправки СМС</span>';
                }
                form_error.style.display = 'block';
                setTimeout(function() {
                    e.target.disabled = false;
                    form_error.innerHTML = '';
                    form_error.style.display = 'none';
                }, 3000);
            }
        });

    } else {
        form_error.innerHTML = '<span class="red-not">Введите номер телефона</span>';
        form_error.style.display = 'block';
        setTimeout(function() {
            form_error.innerHTML = '';
            form_error.style.display = 'none';
        }, 3000);
    }
})

$(document).on('click','.js-send-auth',function(e){
    let tel_auth = $('#phone').val();
    let code_auth = $('#code').val();
    let form_error = document.querySelector('.form-login__error');
    //console.log(code_auth, form_error);
    if (code_auth != '') {
        $.ajax({
            type: "POST",
            url: "/ajax/sms.php",
            data: JSON.stringify({number_login: tel_auth, code_auth: code_auth, action: 'code_auth'}),
            success: function(responce){
                console.log(responce.res);
                data = JSON.parse(responce);
                console.log(data);
                if(data.res == 'OK'){
                    document.location.href = data.link;
                }
                else if(data.res == 'NO'){
                    form_error.innerHTML = 'Проверочный код не совпал.<br>Попробуйте еще раз...';
                }
                else if(data.res == 'AUTH'){
                    form_error.innerHTML = '<span class="blue-not">Вы авторизованы</span>';
                    setTimeout(function() {
                        document.location.href = data.link;
                    }, 2000);
                }
                else if(data.res == 'NEW'){
                    form_error.innerHTML = '<span class="blue-not">Вы зарегистрированы!</span>';
                    setTimeout(function() {
                        document.location.href = data.link;
                    }, 2000);
                }
                else if(data.res == 'NOREG'){
                    form_error.innerHTML = '<span class="reg-not">Ошибка регистрации нового пользователя,<br>>попробуйте еще раз!</span>';
                    setTimeout(function() {
                        document.location.href = data.link;
                    }, 2000);
                }
                else{
                    form_error.innerHTML = '<span class="red-not">Ошибка отправки кода</span>';
                }
                form_error.style.display = 'block';
                setTimeout(function() {
                    e.target.disabled = false;
                    form_error.innerHTML = '';
                    form_error.style.display = 'none';
                }, 3000);
            }
        });

    } else {
        form_error.innerHTML = '<span class="red-not">Введите код из СМС или пароль</span>';
        form_error.style.display = 'block';
        setTimeout(function() {
            form_error.innerHTML = '';
            form_error.style.display = 'none';
        }, 3000);
    }
})
