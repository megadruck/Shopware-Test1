{namespace name="frontend/detail/option"}

{* Field *}
{block name="frontend_detail_swag_custom_products_options_upload_field"}
    <div class="alert is--error is--rounded is--hidden">
        <div class="alert--content">
            {s name="upload/general_error"}{/s}
        </div>
    </div>

    {block name="frontend_detail_swag_custom_products_options_upload_wrapper"}
        <div class="custom-products--upload-wrapper"
             data-swag-custom-products-upload="true"
             data-uploadURL="{url module="widgets" controller="SwagCustomProducts" action="upload"}"
             data-templateId="{$swagCustomProductsTemplate['id']}"
             data-maxFiles="{$option['max_files']}"
             data-maxFileSize="{$option['max_file_size']}"
             data-mode="image"
             data-optionId="{$option['id']}"
             data-allowedMimeTypes="{$option['allowed_mime_types']|escapeHtml}"
        >

            {block name="frontend_detail_swag_custom_products_options_upload_field"}
                <input type="file" name="custom-option-id--{$option['id']}[]"
                       class="upload--field"
                       {if $option['max_files'] > 1} multiple="multiple"{/if}
                       id="custom-products-option-{$key}"
                       data-field="true"
                       {if $option['required']}
                           data-validate="true"
                           data-validate-message-required="{s name="detail/validate/upload_required"}{/s}"
                       {/if}
                />
            {/block}

            {block name="frontend_detail_swag_custom_products_options_upload_label"}
                <label for="custom-products-option-{$key}" class="custom-products--upload-label">
                    {s name="upload/drag_drop_label"}{/s}
                    <strong class="upload--divider">{s name="upload/drag_drop_divider_label"}{/s}</strong>
                    <span class="btn btn-secondary upload-btn--select">{s name="upload/btn_label"}{/s}</span>
                </label>
            {/block}

            {block name="frontend_detail_swag_custom_products_options_upload_information"}
                <div class="custom-products--uploading-information uploading--panel">
                    <div class="information--loading-indicator">
                    </div>

                    <p class="uploading--text">{s name="upload/currently_uploading"}{/s}</p>
                </div>
            {/block}

            {block name="frontend_detail_swag_custom_products_options_upload_failure"}
                <div class="custom-products--uploading-failure uploading--panel">
                    <i class="icon--cross uploading--icon"></i>

                    <p class="uploading--text too-much-files">{s name="upload/too_much_files"}{/s}</p>
                    <p class="uploading--text max-file-size">{s name="upload/max_file_size"}{/s}</p>
                    <p class="uploading--text duplicate-name">{s name="upload/duplicate_name"}{/s}</p>
                    <p class="uploading--text not-allowed-mime-type">{s name="upload/not_allowed_mime_type"}{/s}</p>
                </div>
            {/block}

            {block name="frontend_detail_swag_custom_products_options_upload_success"}
                <div class="custom-products--uploading-success uploading--panel">
                    <i class="icon--check uploading--icon"></i>

                    <p class="uploading--text">{s name="upload/added_successfully"}{/s}</p>
                </div>
            {/block}
        </div>
    {/block}

    {block name="frontend_detail_swag_custom_products_options_upload_config"}
        {if $option['max_files']}
            <span class="uploading--is-required">{s name="upload/maximum_allowed"}{/s}</span>
        {/if}
    {/block}

    {* Will be filled using javascript *}
    {block name="frontend_detail_swag_custom_products_options_upload_list"}
        <div class="custom-products--upload-list"></div>
    {/block}

    {block name="frontend_detail_swag_custom_products_options_upload_btn"}
        <button class="btn btn-primary upload-btn--send-request">
            {s name="upload/btn_upload_label"}{/s} <div class="js--loading"></div>
        </button>
    {/block}
{/block}
