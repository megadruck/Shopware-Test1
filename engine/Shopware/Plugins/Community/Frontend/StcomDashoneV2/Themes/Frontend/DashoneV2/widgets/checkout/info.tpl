{extends file='parent:widgets/checkout/info.tpl'}

{* Cart entry *}
{block name="frontend_index_checkout_actions_cart"}
    <li class="navigation--entry entry--cart" role="menuitem">
        <a class="btn is--icon-left cart--link" href="{url controller='checkout' action='cart'}"
           title="{"{s namespace='frontend/index/checkout_actions' name='IndexLinkCart'}{/s}"|escape}">
            <div class="cart--background"></div>
			<span class="cart--display">
                {s namespace='frontend/index/checkout_actions' name='IndexLinkCart'}{/s}
			</span>

            <span class="badge is--primary is--minimal cart--quantity{if $sBasketQuantity < 1} is--hidden{/if}">{$sBasketQuantity}</span>

            <i class="icon--basket"></i>

            <span class="cart--amount">
				{$sBasketAmount|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
                <span class="cart--quantity-article is--tablet">
				     - {$sBasketQuantity} {s namespace='frontend/index/checkout_actions' name='IndexLinkCartArticle'}Artikel{/s}
			</span>
			</span>
        </a>

        <div class="ajax-loader">&nbsp;</div>
    </li>
{/block}
