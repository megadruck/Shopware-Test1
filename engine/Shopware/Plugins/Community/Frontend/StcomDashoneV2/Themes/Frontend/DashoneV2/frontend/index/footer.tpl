{extends file='parent:frontend/index/footer.tpl'}

{* Copyright in the footer *}
{block name='frontend_index_footer_copyright'}
	<div class="footer--bottom">

        {* Vat info *}
		{block name='frontend_index_footer_vatinfo'}{/block}

        {* Shopware footer *}
        {block name="frontend_index_shopware_footer"}

			{* Copyright *}
			{block name="frontend_index_shopware_footer_copyright"}
				<div class="footer--copyright">
					{s name="IndexCopyright"}{/s}
				</div>
			{/block}

			{* Logo *}
			{block name="frontend_index_shopware_footer_logo"}
				<div class="footer--logo">
					<a href="http://shop-templates.com" title="Responsive Templates" class="logoSt" target="_blank" rel="">{s name='FooterShopTemplatesCopyright' namespace="frontend/index/footer"}www.shop-templates.com{/s}</a>
				</div>
			{/block}
        {/block}
	</div>
{/block}
