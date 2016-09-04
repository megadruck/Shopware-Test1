
<nav class="top-bar--login block{if $dashoneUser} is--loggedin{/if}" role="menubar">
    {if !$dashoneUser || $Action == 'logout'}
        <form name="sLogin" method="post" action="{url controller='account' action='login' sTarget='account' sTargetAction='index'}">
            <input name="sTarget" type="hidden" value="account">

            <div class="register--login-description">{s namespace='frontend/index/checkout_actions' name='IndexTopBarLogin'}Login{/s}</div>
            <div class="register--login-email">
                <input name="email" placeholder="Ihre E-Mail-Adresse" type="email" tabindex="1" value="" id="email" class="register--login-field">
            </div>
            <div class="register--login-password">
                <input name="password" placeholder="Ihr Passwort" type="password" tabindex="2" id="passwort" class="register--login-field">
            </div>
            <div class="register--login-lostpassword">
                <a href="{url controller='account' action='password'}"
                   title="{s namespace='frontend/index/checkout_actions' name='IndexTopBarLoginPassword'}Passwort vergessen?{/s}">
                    {s namespace='frontend/index/checkout_actions' name='IndexTopBarLoginPassword'}Passwort vergessen?{/s}
                </a>
                <br>
                <a href="{url controller='register'}" title="{s namespace='frontend/index/checkout_actions' name='IndexTopBarRegister'}Registrieren{/s}">
                    {s namespace='frontend/index/checkout_actions' name='IndexTopBarRegister'}Registrieren{/s}
                </a>
            </div>
            <div class="register--login-action">
                <button type="submit" class="register--login-btn btn is--primary is--large is--icon-right" name="Submit">
                        <span>
                            {s namespace='frontend/index/checkout_actions' name='IndexTopBarLoginBtn'}Anmelden{/s}
                        </span>
                    <i class="icon--arrow-right"></i></button>
            </div>
        </form>
    {/if}
</nav>
