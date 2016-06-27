{foreach $sArticle.sConfigurator as $sConfigurator}
    {if $sConfigurator.user_selected}
        {block name='frontend_detail_configurator_reset_button'}
            <a class="btn is--icon-left reset--configuration-customizing" href="{url sArticle=$sArticle.articleID sCategory=$sArticle.categoryID}">
                <i class="icon--cross"></i>
                {s name="DetailConfiguratorReset" namespace="frontend/detail/index"}Reset selection{/s}
            </a>
        {/block}
        {break}
    {/if}
{/foreach}
