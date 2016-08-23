{extends file='parent:frontend/account/menu.tpl'}

{namespace name='frontend/account/sidebar'}
<div class="account--menu is--rounded">
    {block name="frontend_account_menu"}

        {* Sidebar menu container *}
        <div class="account--menu-container">

            {* Sidebar navigation *}
            <ul class="sidebar--navigation navigation--list is--level0 show--active-items">

                {* Link to the account overview page *}
                {block name="frontend_account_menu_link_overview"}
                    <li class="navigation--entry">
                        <a href="{url controller='account'}" title="{s name="AccountLinkOverview"}{/s}"
                           class="navigation--link{if $sAction == 'index'} is--active{/if}"><i class="icon--list"></i>
                            {s name="AccountLinkOverview"}{/s}
                        </a>
                    </li>
                {/block}

                {* Link to the user orders *}
                {block name="frontend_account_menu_link_orders"}
                    <li class="navigation--entry">
                        <a href="{url controller='account' action='orders'}" title="{s name="AccountLinkPreviousOrders"}{/s}"
                           class="navigation--link{if $sAction == 'orders'} is--active{/if}"><i class="icon--archive"></i>
                            {s name="AccountLinkPreviousOrders"}{/s}
                        </a>
                    </li>
                {/block}

                {* Link to the user downloads *}
                {block name="frontend_account_menu_link_downloads"}{/block}

                   
                {* Link to the user Billing address settings *}
                {block name="frontend_account_menu_link_billing"}{/block}
                
                  {* Link to the user Sender address settings *}
                {block name="frontend_account_menu_link_sender"}
                    <li class="navigation--entry">
                        <a href="{url controller=address}" title="{s name="AccountLinkSenderAddress"}{/s}"
                           class="navigation--link{if $sAction == 'sender'} is--active{/if}"><i class="icon--text"></i>
                            {s name="AccountLinkSenderAddress"}{/s}
                        </a>
                    </li>
                {/block}

                {* Linkt to the user shipping address settings *}
                {block name="frontend_account_menu_link_shipping"}
                    <li class="navigation--entry">
                        <a href="{url controller=address}?shipping=true" title="{s name="AccountLinkShippingAddress"}{/s}"
                           class="navigation--link{if $sAction == 'shipping'} is--active{/if}"><i class="icon--truck"></i>
                            {s name="AccountLinkShippingAddress"}{/s}
                        </a>
                    </li>
                {/block}

                {* Link to the user payment method settings *}
                {block name="frontend_account_menu_link_payment"}
                    <li class="navigation--entry">
                        <a href="{url controller='account' action='payment'}" title="{s name="AccountLinkPayment"}{/s}"
                           class="navigation--link{if $sAction == 'payment'} is--active{/if}"><i class="icon--paypal"></i>
                            {s name="AccountLinkPayment"}{/s}
                        </a>
                    </li>
                {/block}

                {* Link to the user product notes *}
                {block name="frontend_account_menu_link_notes"}
                    <li class="navigation--entry">
                        <a href="{url controller='note'}" title="{s name="AccountLinkNotepad"}{/s}" class="navigation--link"><i class="icon--info"></i>
                            {s name="AccountLinkNotepad"}{/s}
                        </a>
                    </li>
                {/block}

                {*prepend block = buggy with 5.1 Logout action *}
                {block name="frontend_account_menu_link_logout"}
                    <li class="navigation--entry">
                        <a href="{url controller='account' action='logout'}" title="{s name="AccountLinkLogout2"}{/s}" class="navigation--link link--logout"><i class="icon--logout"></i>
                            {s name="AccountLinkLogout2"}{/s}
                        </a>
                    </li>
                {/block}

            </ul>
        </div>
    {/block}
</div>