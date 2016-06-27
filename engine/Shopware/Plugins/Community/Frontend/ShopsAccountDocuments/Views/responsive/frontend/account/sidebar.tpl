{extends file='parent:frontend/account/sidebar.tpl'}

{block name="frontend_account_menu_link_orders" append}
    <li class="navigation--entry">
        <a href="{url controller='ShopsAccountDocuments'}" title="{s name="AccountLinkDocuments"}Meine Dokumente{/s}"
           class="navigation--link{if $Controller == 'ShopsAccountDocuments'} is--active{/if}">
            {s name="AccountLinkDocuments"}Meine Dokumente{/s}
        </a>
    </li>
{/block}
