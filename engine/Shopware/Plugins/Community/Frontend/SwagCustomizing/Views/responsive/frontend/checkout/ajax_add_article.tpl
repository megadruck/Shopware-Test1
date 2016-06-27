{extends file="parent:frontend/checkout/ajax_add_article.tpl"}

{block name="checkout_ajax_add_information" append}
    {block name="checkout_ajax_customizing_add_information"}
        {include file="frontend/swag_customizing/checkout/ajax_add_article.tpl"}
    {/block}
{/block}
