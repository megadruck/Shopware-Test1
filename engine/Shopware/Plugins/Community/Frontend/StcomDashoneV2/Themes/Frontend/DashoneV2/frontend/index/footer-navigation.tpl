{extends file='parent:frontend/index/footer-navigation.tpl'}

{namespace name="frontend/index/menu_footer"}

{block name="frontend_index_footer_column_newsletter"}
    <div class="footer--column column--newsletter is--last block">
        <h4 class="column--headline"><i class="icon--mail"></i>{s name="sFooterNewsletterHead"}{/s}</h4>

        {block name="frontend_index_footer_column_newsletter_content"}
            <div class="column--content">
                <p class="column--desc">
                    {s name="sFooterNewsletter"}{/s}
                </p>

                {block name="frontend_index_footer_column_newsletter_form"}
                    <form class="newsletter--form" action="{url controller='newsletter'}" method="post">
                        <input type="hidden" value="1" name="subscribeToNewsletter" />

                        {block name="frontend_index_footer_column_newsletter_form_field"}
                            <input type="email" name="newsletter" class="newsletter--field" placeholder="{s name="IndexFooterNewsletterValue"}{/s}" />
                        {/block}

                        {block name="frontend_index_footer_column_newsletter_form_submit"}
                            <button type="submit" class="newsletter--button btn">
                                <span class="newsletter--submit">{s name='IndexFooterNewsletterSubmitButton'}Go{/s}</span>
                            </button>
                        {/block}
                    </form>
                {/block}
            </div>
        {/block}
    </div>
{/block}