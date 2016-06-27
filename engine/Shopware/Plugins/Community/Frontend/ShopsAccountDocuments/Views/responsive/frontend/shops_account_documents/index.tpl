{extends file='parent:frontend/account/index.tpl'}

{namespace name='frontend/account/index'}

{* Breadcrumb *}
{block name='frontend_index_start' append}
    {assign var='sBreadcrumb' value=[['name'=>"{s name='AccountTitle'}{/s}", 'link' =>{url controller='account' action='index'}]]}
    {$sBreadcrumb[] = ['name'=>"{s name='ShopsAccountDocumentsTitle'}Meine Dokumente{/s}", 'link'=>{url}]}
{/block}

{* Account Sidebar *}
{block name="frontend_index_left_categories"}
    {include file="frontend/account/sidebar.tpl"}
{/block}

{* Main content *}
{block name="frontend_index_content"}
    <div class="content account--content">

        {* Welcome text *}
        {block name="frontend_account_orders_welcome"}
            <div class="account--welcome panel">
                {block name="frontend_account_orders_welcome_headline"}
                    <h1 class="panel--title">{s name="ShopsAccountDocumentsTitle"}Meine Dokumente{/s}</h1>
                {/block}

                {block name="frontend_account_orders_welcome_content"}
                    <div class="panel--body is--wide">
                        <p>{s name="ShopsAccountDocumentsText"}Hier finden Sie alle Dokumente zu Ihren Bestellungen.{/s}</p>
                    </div>
                {/block}
            </div>
        {/block}

        {if !$shopsAccountDocuments}
            {block name="frontend_account_orders_info_empty"}
                <div class="account--no-orders-info">
                    {include file="frontend/_includes/messages.tpl" type="warning" content="{s name='ShopsAccountDocumentsWarning'}Aktuell sind für Sie keine Dokumente hinterlegt.{/s}"}
                </div>
            {/block}
        {else}
            {* Orders overview *}
            {block name="frontend_account_orders_overview"}
                <div class="account--orders-overview panel is--rounded">

                    {block name="frontend_account_orders_table"}
                        <div class="panel--table">
                            {block name="frontend_account_orders_table_head"}
                                <div class="orders--table-header panel--tr">

                                    {block name="frontend_account_orders_table_head_id"}
                                        <div class="panel--th column--order">{s name="ShopsAccountDocumentsTableHeadOrder"}Bestellnummer{/s}</div>
                                    {/block}

                                    {block name="frontend_account_orders_table_head_type"}
                                        <div class="panel--th column--type">{s name="ShopsAccountDocumentsTableHeadType"}Typ{/s}</div>
                                    {/block}

                                    {block name="frontend_account_orders_table_head_date"}
                                        <div class="panel--th column--date">{s name="ShopsAccountDocumentsTableHeadDate"}Datum{/s}</div>
                                    {/block}

                                    {block name="frontend_account_orders_table_head_price"}
                                        <div class="panel--th column--price">{s name="ShopsAccountDocumentsTableHeadPrice"}Preis{/s}</div>
                                    {/block}

                                    {block name="frontend_account_orders_table_head_actions"}
                                        <div class="panel--th column--actions">{s name="ShopsAccountDocumentsTableHeadActions"}Aktionen{/s}</div>
                                    {/block}
                                </div>
                            {/block}


                            {block name="frontend_account_order_item_overview"}
                                {foreach $shopsAccountDocuments as $item}
                                    {include file="frontend/shops_account_documents/item.tpl"}
                                {/foreach}
                            {/block}
                        </div>
                    {/block}

                    {block name="frontend_account_orders_actions_paging"}
                        <div class="account--paging panel--paging">
                            {if $sPages.previous}
                                <a href="{$sPages.previous}" class="btn paging--link paging--prev">
                                    <i class="icon--arrow-left"></i>
                                </a>
                            {/if}

                            {foreach $sPages.numbers as $page}
                                {if $page.markup}
                                    <a class="paging--link is--active">{$page.value}</a>
                                {else}
                                    <a href="{$page.link}" class="paging--link">{$page.value}</a>
                                {/if}
                            {/foreach}

                            {if $sPages.next}
                                <a href="{$sPages.next}" class="paging--link paging--next">
                                    <i class="icon--arrow-right"></i>
                                </a>
                            {/if}

                            {block name='frontend_account_orders_actions_paging_count'}
                                <div class="paging--display">
                                    {s name="ListingTextSite" namespace="frontend/listing/listing_actions"}{/s}
                                    <span class="is--bold">{if $sPage}{$sPage}{else}1{/if}</span>
                                    {s name="ListingTextFrom" namespace="frontend/listing/listing_actions"}{/s}
                                    <span class="is--bold">{$sNumberPages}</span>
                                </div>
                            {/block}
                        </div>
                    {/block}

                </div>
            {/block}
        {/if}
    </div>
{/block}