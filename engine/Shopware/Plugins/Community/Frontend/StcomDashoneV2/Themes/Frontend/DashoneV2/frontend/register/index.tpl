{extends file="parent:frontend/register/index.tpl"}

{block name='frontend_index_navigation_categories_top' append}
    {assign var='sBreadcrumb' value=[['name'=>"{s name='RegisterTitle'}Melden Sie sich als Kunde an{/s}", 'link' =>{url action='index'}]]}
    <nav class="content--breadcrumb under--navigation">
        <div class="container">
            {block name='frontend_index_breadcrumb_inner'}
                {include file='frontend/index/breadcrumb.tpl'}
            {/block}
        </div>
    </nav>
{/block}
