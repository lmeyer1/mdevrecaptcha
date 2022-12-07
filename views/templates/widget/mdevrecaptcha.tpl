<input type="hidden" name="mdevrecaptcha_gtoken" value="">

<script src="https://www.google.com/recaptcha/api.js?render={$recaptcha_token}"></script>
<script>
{literal}
function mdevrecaptcha_submitWidget(e, obj, action = 'submit') {
    e.preventDefault();
    grecaptcha.ready(function() {{/literal}
        let field = $('input[name="mdevrecaptcha_gtoken"]'),
            action = field.closest('form').attr('id') || 'widget'
        grecaptcha.execute('{$recaptcha_token}'{literal}, {action: action}).then(function(token) {
            field.val(token);
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
