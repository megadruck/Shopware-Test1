{block name='frontend_detail_data_customizing_wrapper'}
    <div class="customizing--data-wrapper">
        {block name='frontend_detail_data_customizing_wrapper_form'}
            <form name="customizingOptions" method="post">
                {block name='frontend_detail_data_customizing_wrapper_form_panel'}
                    {if $recalculateProductPrice}
                        <input type="hidden" name="price" value="{$sArticle.price}">
                        <input type="hidden" name="currentQuantity" value="1">
                    {/if}
                    <div class="customizing--panel panel has--border">
                        {block name='frontend_detail_data_customizing_form_panel_title'}
                            {if $customizingGroup.showName}
                                <h2 class="customizing--panel-title panel--title is--underline">{$customizingGroup.name}</h2>
                            {/if}
                        {/block}

                        {block name="frontend_detail_data_customizing_form_panel_img"}
                            {if $customizingGroup.showGroupImage}
                                <img class="customizing--panel-img" src="{$customizingGroup.imagePath}"/>
                            {/if}
                        {/block}

                        {block name='frontend_detail_data_customizing_form_panel_body'}
                            <div class="customizing--panel-body panel--body is--wide">
                                {if $customizingGroup.description && $customizingGroup.showDescription}
                                    {block name='frontend_detail_data_customizing_form_description'}
                                        <p class="customizing--description">{$customizingGroup.description}</p>
                                    {/block}
                                {/if}

                                {block name='frontend_detail_data_customizing_form_fields'}
                                    {include file='frontend/swag_customizing/fields.tpl'}
                                {/block}
                            </div>
                        {/block}
                    </div>
                {/block}
            </form>
        {/block}
    </div>
{/block}
