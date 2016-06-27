{extends file='frontend/forms/index.tpl'}


{* Sidebar right *}
{block name='frontend_index_content_right'}
<div id="right_account" class="grid_4 last">
	{include file="frontend/ticket/navigation.tpl"}
</div>
{/block}


{* Empty sidebar left *}
{block name='frontend_index_content_left'}{/block}

{block name='frontend_index_content'}
<div class="account_forms">

    <div id="center" class="grid_16 supportrequest">
        <h2 class="headingbox_dark largesize">{$sSupport.name}</h2>
        <div class="inner_container">
            {if $sSupport.sElements}
                <div class="col_center_custom">
                    {eval var=$sSupport.text}
                </div>
                <div class="space">&nbsp;</div>
                {block name='frontend_forms_index_elements'}
                    {include file="frontend/forms/elements.tpl"}
					{if $email}
                    <script type="text/javascript">
						var nameField = $("#name"),
							vornameField = $("#vorname"),
							nachnameField = $("#nachname"),
							emailField = $("#email");

                        if(nameField.val() != undefined)
                        {
							nameField.val("{$userName}");
                        }
                        if(vornameField.val() != undefined)
                        {
							vornameField.val("{$firstName}");
                        }
                        if(nachnameField.val() != undefined)
                        {
							nachnameField.val("{$lastName}");
                        }
                        if(emailField.val() != undefined)
                        {
							emailField.val("{$email}");
                        }
                    </script>
					{/if}
                {/block}
                {elseif $sSupport.text2}
                <div class="success center bold">
                    {eval var=$sSupport.text2}
                </div>
                <div class="space">&nbsp;</div>
                <a href="{url controller='index'}" class="button-left large">{s name='FormsLinkBack'}{/s}</a>
                {else}
                <div class="col_center_container">
                    <p>{s name='FormsTextContact'}{/s}</p>
                    <a href="{url controller='index'}" class="button-left large">{s name='FormsLinkBack'}{/s}</a>
                </div>
            {/if}
        </div>
        <div class="doublespace">&nbsp;</div>
    </div>
</div>
{/block}