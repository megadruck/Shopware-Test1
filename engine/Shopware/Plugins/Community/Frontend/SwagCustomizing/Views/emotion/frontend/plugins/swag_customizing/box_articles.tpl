{block name='frontend_listing_box_article_actions_buy_now'}
    {if !$sArticle.customizingWithRequiredOptions || $sArticle.customizingWithRequiredOptions == 0}
        {$smarty.block.parent}
    {/if}
{/block}
