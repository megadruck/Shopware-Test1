{block name="frontend_index_header_css_screen" append}
    {if $customizingGroup}
        <link rel="stylesheet" href="{link file='frontend/_resources/styles/swag_customizing.css'}"/>
        <link rel="stylesheet" href="{link file='frontend/_resources/styles/jquery.wysiwyg.css'}"/>
        <link rel="stylesheet" href="{link file='frontend/_resources/styles/jquery.minicolors.css'}"/>
    {/if}
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    {if $customizingGroup}
        <script type="text/javascript" src="{link file='frontend/_resources/javascript/jquery.swag_customizing.js'}"></script>
        <script type="text/javascript" src="{link file='frontend/_resources/javascript/fileupload/jquery.ui.widget.js'}"></script>
        <script type="text/javascript" src="{link file='frontend/_resources/javascript/fileupload/jquery.fileupload.js'}"></script>
        <script type="text/javascript" src="{link file='frontend/_resources/javascript/fileupload/jquery.iframe-transport.js'}"></script>
        <script type="text/javascript" src="{link file='frontend/_resources/javascript/minicolors/jquery.minicolors.js'}"></script>
        <script type="text/javascript" src="{link file='frontend/_resources/javascript/wysiwyg/jquery.wysiwyg.js'}"></script>
        <script type="text/javascript">
            var customizingEditorStylePath = "{link file='frontend/_resources/styles/editor.css'}",
                customizingSavePath = decodeURIComponent("{{url controller=customizing action=save groupId=$customizingGroup.id articleId=$sArticle.articleID articleTax=$sArticle.tax forceSecure}|urlencode}"),
                customizingResetPath = decodeURIComponent("{{url controller=customizing action=reset groupId=$customizingGroup.id forceSecure}|urlencode}"),
                customizingUploadUrl = decodeURIComponent("{{url controller=customizing action=upload groupId=$customizingGroup.id articleId=$sArticle.articleID forceSecure}|urlencode}"),
                customizingPrice = "{if $sArticle.priceStartingFrom}{$sArticle.priceStartingFrom}{else}{$sArticle.price}{/if}",
                locationUrl = window.location.href,
                customizingBlockPrices = {$sArticle.sBlockPrices|json_encode};

            customizingPrice = customizingPrice.replace(',', '.') * 1;
            customizingSavePath = normalizeAjaxUrl(locationUrl, customizingSavePath);
            customizingResetPath = normalizeAjaxUrl(locationUrl, customizingResetPath);
            customizingUploadUrl = normalizeAjaxUrl(locationUrl, customizingUploadUrl);

            function normalizeAjaxUrl(locationUrl, ajaxUrl) {
                if (!containsHTTPS(locationUrl) && containsHTTPS(ajaxUrl)) {
                    return ajaxUrl.replace('https://', 'http://');
                }
                return ajaxUrl;
            }

            function containsHTTPS(url) {
                return url.indexOf("https://") >= 0;
            }

        </script>
    {/if}
{/block}
{block name="frontend_detail_buy_quantity" append}
    <input type="hidden" name="customizingGroupId" id="customizingGroupId" value="{$customizingGroup.id}"/>
{/block}

{block name="frontend_detail_buy_laststock"}
    {if $sCustomLicense}
        <div class="error bold center">
            {s name='DetailBuyInfoNotAvailable' namespace='frontend/detail/buy'}{/s}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_detail_buy_button"}
    {if !$sCustomLicense}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_detail_buy_quantity"}
    {if !$sCustomLicense}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_widgets_delivery_infos"}
    {if !$sCustomLicense}
        {$smarty.block.parent}
    {/if}
{/block}
