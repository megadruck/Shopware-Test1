{extends file='parent:frontend/index/topbar-navigation.tpl'}
{block name="frontend_index_checkout_actions_service_menu" prepend}
    <div class="navigation--entry entry--account" role="menuitem">
            <a href="{url controller='account'}" class="entry--link js--account">
                <i class="icon--cog"></i> {s namespace='frontend/index/checkout_actions' name='IndexLinkMyAccount'}Mein Konto{/s}
            </a>
    </div>
    <div class="navigation--entry entry--note" role="menuitem">
        <a href="{url controller='note'}" class="entry--link">
            {s namespace='frontend/index/checkout_actions' name='IndexLinkNotePad'}Merkzettel{/s}
        </a>
        {if $sNotes|@count > 0}
            <span class="badge notes--quantity">{$sNotes|@count}</span>
        {/if}
    </div>
    {action module=widgets controller=StcomDashone action=index}
{/block}

{* Service / Support drop down *}
{block name="frontend_index_checkout_actions_service_menu"}
    <div class="navigation--entry entry--service has--drop-down" role="menuitem" aria-haspopup="true"
         data-drop-down-menu="true">
        {s namespace='frontend/index/checkout_actions' name='IndexLinkService'}{/s}

        {* Include of the widget *}
        {block name="frontend_index_checkout_actions_service_menu_include"}
            {action module=widgets controller=index action=menu group=gLeft}
        {/block}
    </div>
{/block}


