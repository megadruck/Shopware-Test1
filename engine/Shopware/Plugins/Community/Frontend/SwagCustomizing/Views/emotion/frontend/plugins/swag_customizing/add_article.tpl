<div class="heading">
    <h2>{s name='AjaxAddHeaderError' namespace='frontend/checkout/ajax_add_article'}{/s}</h2>

    {* Close button *}
    <a href="#" class="modal_close" title="{s name='LoginActionClose' namespace='frontend/checkout/ajax_add_article'}{/s}">
        {s name='LoginActionClose' namespace='frontend/checkout/ajax_add_article'}{/s}
    </a>
</div>

<div class="error_container">
    <p class="text">
        {s name='CustomizingAddAricleErrorText' namespace='frontend/checkout/ajax_add_article'}{/s}<br>
        {foreach $customizingRequiredOption as $option}
            - {$option.name}<br>
        {/foreach}
    </p>

    <div class="clear">&nbsp;</div>
</div>

<div class="ajax_add_article">
    {block name='frontend_plugins_swag_customizing_add_article_middle'}
        <div class="middle">
            <div class="actions">
                {block name='frontend_plugins_swag_customizing_add_article_action_buttons'}
                    <a class="button-right large right modal_close" title="{s name='AjaxAddLinkBack' namespace='frontend/checkout/ajax_add_article'}{/s}">
                        {s name='AjaxAddLinkBack' namespace='frontend/checkout/ajax_add_article'}{/s}
                    </a>
                    <div class="clear">&nbsp;</div>
                {/block}
            </div>
            <div class="space">&nbsp;</div>
        </div>
    {/block}
</div>
