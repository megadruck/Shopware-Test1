{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_checkout_cart_item_details_inline' append}
    {if $sFinishUploads}
        <div id="upload_box_{$sBasketItem.id}">
            {foreach from=$sFinishUploads[$key] item=uploadedItem name="uploadedIndex" key=cid}
                <div>
                    <strong>{$cid}</strong><br />
                    {assign var=fileCounter value=0}
                    {foreach key=pageKey from=$uploadedItem['name'] item=files}
                        {$fileCounter=$fileCounter+1}

                        {if $files=='per_Post'}
                            per Post
                        {elseif $files=='per_e_Mail'}
                            per e-Mail
                        {else}
                            {$fileCounter}. <a href="{if $uploadedItem['linkType'][$pageKey] == 'internal'}http://{$smarty.server.HTTP_HOST}{/if}{$uploadedItem['link'][$pageKey]}" target="_blank" title="{$files}">{$files}</a><br />
                        {/if}
                    {/foreach}
                </div>
            {/foreach}
        </div>
    {/if}
{/block}
