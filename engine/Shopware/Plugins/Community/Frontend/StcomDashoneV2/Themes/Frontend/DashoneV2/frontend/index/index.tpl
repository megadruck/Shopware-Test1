{extends file='parent:frontend/index/index.tpl'}

{* Breadcrumb *}
{block name='frontend_index_breadcrumb'}
{/block}

{* Maincategories navigation top *}
{block name='frontend_index_navigation_categories_top' append}
    {if count($sBreadcrumb)}
        <nav class="content--breadcrumb under--navigation">
            <div class="container">
                {block name='frontend_index_breadcrumb_inner'}
                    {include file='frontend/index/breadcrumb.tpl'}
                {/block}
            </div>
        </nav>
    {/if}
    {if $Controller == 'index'}
        <nav class="content--service under--navigation">
            <div class="container">
                {block name='frontend_index_service_inner'}
                    {include file='frontend/index/service-navigation.tpl'}
                {/block}
            </div>
        </nav>
    {/if}
{/block}