<input type="hidden" name="mdevrecaptcha_gtoken" value="">

<script src="https://www.google.com/recaptcha/api.js?render={$recaptcha_token}"></script>
<script>
{literal}
function mdevrecaptcha_submitWidget(e, obj, action = 'submit') {
    e.preventDefault();
    grecaptcha.ready(function() {{/literal}
        grecaptcha.execute('{$recaptcha_token}'{literal}, {action: 'login'}).then(function(token) {
            $('input[name="mdevrecaptcha_gtoken"]').val(token);
            
            if(action == 'click') {
                obj.click();
            }
            else {
                obj.submit();
            }
        });
    });
}{/literal}
</script>