{extends file='parent:frontend/account/sidebar.tpl'}


{* Link to the user addresses *}
{block name="frontend_account_menu_link_addresses"}
    <li class="navigation--entry">
        <a href="{url controller='address'}" title="{s name="AccountLinkAddresses"}{/s}"
           class="navigation--link" data-address-selection="true" data-setDefaultSenderAddress="1">{s name="AccountLinkAddresses"}{/s}
        </a>
    </li>
{/block}