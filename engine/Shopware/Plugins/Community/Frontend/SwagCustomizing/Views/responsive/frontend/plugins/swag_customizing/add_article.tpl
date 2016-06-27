{namespace name='frontend/checkout/ajax_add_article'}

{block name="frontend_detail_customizing_error_popup"}
    <div class="customizing--error-container">
        {block name="frontend_detail_customizing_error_popup_headline"}
            <div class="error-container--headline">
                <h2>{s name='AjaxAddHeaderError'}Hinweis:{/s}</h2>
            </div>
        {/block}

        {block name="frontend_detail_customizing_error_popup_content"}
            <div class="error-container--content">
                {block name="frontend_detail_customizing_error_popup_error_headline"}
                    <div class="error-container--error-headline">
                        {s name='CustomizingAddAricleErrorText'}Bitte füllen Sie zuerst folgende Pflichtfelder aus:{/s}
                    </div>
                {/block}

                {foreach $customizingRequiredOption as $option}
                    {block name="frontend_detail_customizing_error_popup_field"}
                        <div class="error-container--error-field">- {$option.name}</div>
                    {/block}
                {/foreach}
            </div>
        {/block}
    </div>
{/block}
