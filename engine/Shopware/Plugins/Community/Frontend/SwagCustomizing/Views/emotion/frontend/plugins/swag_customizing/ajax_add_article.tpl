<div class="heading">
    <h2>
        {if !$sBasketInfo}
            {s name='AjaxAddHeader' namespace='frontend/checkout/ajax_add_article'}{/s}
        {else}
            {s name='AjaxAddHeaderError' namespace='frontend/checkout/ajax_add_article'}{/s}
        {/if}
    </h2>

    {* Close button *}
    <a href="#" class="modal_close" title="{s name='LoginActionClose' namespace='frontend/checkout/ajax_add_article'}{/s}">
        {s name='LoginActionClose' namespace='frontend/checkout/ajax_add_article'}{/s}
    </a>
</div>

{if $sBasketInfo}
    <div class="error_container">
        <p class="text">
            {$sBasketInfo}
        </p>
        <div class="clear">&nbsp;</div>
    </div>
{/if}

<div class="ajax_add_article">
    {block name='frontend_checkout_ajax_add_article_middle'}
        <div class="middle">
            {if $sArticle}
                <div class="article_box" style="height:auto;overflow:auto;">

                    {* Thumbnail *}
                    <div class="thumbnail">
                        <a href="{$sArticle.linkDetails}" title="{$sArticle.articleName}" class="artbox_thumb"
                            {if $sArticle.image.src}
                            style="background: url({$sArticle.image.src.1}) no-repeat center center"
                            {/if}>
                            {if !$sArticle.image.src}
                                <img src="{link file='frontend/_resources/images/no_picture.jpg'}" alt="{s name='ListingBoxNoPicture' namespace='frontend/checkout/ajax_add_article'}{/s}"/>
                            {/if}
                        </a>
                    </div>

                    {* Title *}
                    <strong class="title">{$sArticleName|truncate:37|strip_tags}</strong>

                    {* Ordernumber *}
                    <span class="ordernumber">
                        {s name='AjaxAddLabelOrdernumber' namespace='frontend/checkout/ajax_add_article'}{/s}: {$sArticle.ordernumber}
                    </span><br/>

                    {assign var="showHeader" value=false}
                    {if count($articleCustomizing)>0}
                        {foreach $articleCustomizing as $ac}
                            {if $ac.amount > 0.001}
                                {assign var="showHeader" value=true}
                            {/if}
                        {/foreach}
                        {if $showHeader}
                            <span class="customizing-ajax-modal-box-heading">
                                {s name='AjaxAddLabelCustomizingOptions' namespace='frontend/checkout/ajax_add_article'}{/s}
                            </span>
                            <br/>
                        {/if}
                    {/if}

                    {$i = 100}
                    {foreach $articleCustomizing as $ac}
                        {if $ac.amount > 0.001}
                            <div class="customizing-ajax-modal-box-row">
                                <div class="customizing-ajax-modal-box-container">
                                    <div>
                                        <b>
                                            {* Option name *}
                                            {$ac.customizing.name|truncate:37|strip_tags}:
                                        </b>
                                    </div>
                                    <div>
                                        {* Assign the customizing value to the variable with the name 'value' to work with itin the value.tpl *}
                                        {assign var="value" value=$ac.customizing}
                                        {* Option value *}
                                        {include file='frontend/plugins/swag_customizing/value.tpl'}
                                    </div>
                                    {* Surcharge price *}
                                    {if $ac.amount > 0.001}
                                        <div class="customizing-ajax-modal-box-price">
                                            <strong>{$ac.amount|currency}</strong>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        {/if}
                        {$i = $i + 20}
                    {/foreach}

                    {* Price *}
                    <strong class="price">{$sArticle.price|currency}</strong>

                    {* Quantity *}
                    <span class="quantity">
                        {s name='AjaxAddLabelQuantity' namespace='frontend/checkout/ajax_add_article'}{/s}: {$sArticle.quantity}
                    </span>
                </div>
            {/if}

            {* Actions *}
            <div class="actions">
                {block name='frontend_checkout_ajax_add_article_action_buttons'}
                    <a title="{s name='AjaxAddLinkBack' namespace='frontend/checkout/ajax_add_article'}{/s}" class="button-middle large modal_close">
                        {s name='AjaxAddLinkBack' namespace='frontend/checkout/ajax_add_article'}{/s}
                    </a>
                    <a href="{url action='cart'}" class="button-right large right" title="{s name='AjaxAddLinkCart' namespace='frontend/checkout/ajax_add_article'}{/s}">
                        {s name='AjaxAddLinkCart' namespace='frontend/checkout/ajax_add_article'}{/s}
                    </a>
                    <div class="clear">&nbsp;</div>
                {/block}
            </div>
            <div class="space">&nbsp;</div>
        </div>
    {/block}

    <div class="bottom">
        {block name='frontend_checkout_ajax_add_article_cross_selling'}
            {if $sCrossSimilarShown|@count || $sCrossBoughtToo|@count}
                <h2>{s name='AjaxAddHeaderCrossSelling' namespace='frontend/checkout/ajax_add_article'}{/s}</h2>
                <div class="slider_modal">
                    {$sCrossSellingArticles = $sCrossBoughtToo}
                    {if $sCrossSimilarShown && $sCrossBoughtToo|count < 1}
                        {$sCrossSellingArticles = $sCrossSimilarShown}
                    {/if}
                    {foreach from=$sCrossSellingArticles item=article}
                        {if $article@index % 3 == 0}
                            <div class="slide">
                        {/if}

                        {assign var=image value=$article.image.src.2}
                        <div class="article_box">
                            {if $image}
                                <a style="background: url({$image}) no-repeat scroll center center transparent;" class="artbox_thumb" title="{$article.articleName|escape}" href="{$article.linkDetails}">
                                </a>
                            {else}
                                <a class="artbox_thumb no_picture" title="{$article.articleName|escape}" href="{$article.linkDetails}">
                                </a>
                            {/if}
                            <a title="{$article.articleName}" class="title" href="{$article.linkDetails}">
                                {$article.articleName|truncate:28}
                            </a>
                            {if $article.purchaseunit}
                                <div class="article_price_unit">
                                    <p>
                                        <strong>{s name='SlideArticleInfoContent' namespace='frontend/plugins/recommendation/slide_articles'}{/s}:</strong> {$article.purchaseunit} {$article.sUnit.description}
                                    </p>
                                    {if $article.purchaseunit != $article.referenceunit}
                                        <p>
                                            {if $article.referenceunit}
                                                <strong class="baseprice">{s name='SlideArticleInfoBaseprice' namespace='frontend/plugins/recommendation/slide_articles'}{/s}:</strong>
                                                {$article.referenceunit} {$article.sUnit.description} = {$article.referenceprice|currency} {s name='Star' namespace='frontend/listing/box_article'}{/s}
                                            {/if}
                                        </p>
                                    {/if}
                                </div>
                            {/if}
                            <p class="price">
                                <span class="price">
                                    {if $article.priceStartingFrom && !$article.liveshoppingData}{s name='ListingBoxArticleStartsAt' namespace='frontend/plugins/recommendation/slide_articles'}{/s}{/if}{$article.price|currency}*
                                </span>
                            </p>
                        </div>
                        {if $article@index % 3 == 2 || $article@last}
                            </div>
                        {/if}
                    {/foreach}
                </div>
            {/if}
        {/block}
    </div>
</div>
