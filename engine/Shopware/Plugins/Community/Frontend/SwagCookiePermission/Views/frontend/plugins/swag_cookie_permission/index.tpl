{block name="frontend_index_header_css_screen" append}
    <link type="text/css" media="all" rel="stylesheet"
          href="{link file='frontend/_resources/styles/cookie-bar.css'}"/>
{/block}

{* Cookie permission javascript *}
{block name='frontend_index_header_javascript_inline' prepend}
    var cookieForwardTo = '{$swagCookieBarConfig.forwardTo}';
    var shopId = '{$Shop->getId()}';
    var isRemoveCookies = '{$swagCookieBarConfig.isRemoveCookies}';
    var cookiePermissionUrl = '{url module=widgets controller=SwagCookiePermission action=isAffectedUser}';
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <script src="{link file='frontend/_resources/javascript/cookie-bar.js'}"></script>
{/block}

{block name='frontend_index_before_page' prepend}
    <div class="cookie-bar"
         style="display: none;{if $swagCookieBarConfig.backgroundColor} background-color:{$swagCookieBarConfig.backgroundColor}{/if}">
        <p>{s name='cookieBarMessage' namespace='frontend/swag_cookie_permission/main'}This page requires cookies. Do you agree with the usage of cookies?{/s}
            <a href="javascript: void(0);" class="cp-enable cp-btn">
                <span class="cp-yes-icon cp-icon"></span><span class="cp-btn-label">{s name='CookieBarYesBtn' namespace='frontend/swag_cookie_permission/main'}Yes{/s}</span></a>
            <a href="javascript: void(0);" class="cp-disable  cp-btn">
                <span class="cp-no-icon cp-icon"></span><span class="cp-btn-label">{s name='CookieBarNoBtn' namespace='frontend/swag_cookie_permission/main'}No{/s}</span>
            </a>
        </p>
    </div>
{/block}