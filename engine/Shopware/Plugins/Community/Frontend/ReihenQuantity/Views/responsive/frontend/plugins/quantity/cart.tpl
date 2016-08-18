{* Article amount *}
{if $sArticle.attr6 == 1}
{block name='frontend_checkout_cart_item_quantity'}
	{*! Article amount *}
    {if $sBasketItem.modus == 0}
    <div class="panel--td column--quantity is--align-right">
		<form name="basket_change_quantity{$sBasketItem.id}" method="POST" action="{url action='changeQuantity' sTargetAction=$sTargetAction}">
			<input class="sQuantity" name="sQuantity" type="text" value="{$sBasketItem.quantity}">
			<button type="submit" class="quantitySubmit btn is--primary" title="{s name="reihenQuantityReload"}Aktualisieren{/s}"><i class="icon--cycle"></i></button>
			<div class="packunit">
				{$sBasketItem.packunit}
			</div>
			<input type="hidden" name="sArticle" value="{$sBasketItem.id}" />
			<input type="hidden" name="sArticleMin" value="{$sBasketItem.minpurchase}" />
			<input type="hidden" name="sArticleMax" value="{$sBasketItem.maxpurchase}" />
		</form>
    </div> 
	{else}
		&nbsp;
	{/if}
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <script type="text/javascript">
        (function($) {
            $(".sQuantity").ForceNumbersOnly();

            $('.sQuantity').focusout('input', function() {
            	var val = $(this).val();
            	var id = $(':input:eq(' + ($(':input').index(this) + 2) + ')').val();
            	var min = $(':input:eq(' + ($(':input').index(this) + 3) + ')').val();
            	var max = $(':input:eq(' + ($(':input').index(this) + 4) + ')').val();

            	if(parseInt(val) < parseInt(min)){
					alert("{$ReihenQuantityConfig->minText}");  
					$(this).val(min);   		
				}
            	if(parseInt(val) > parseInt(max)){
					alert("{$ReihenQuantityConfig->maxText}");   
					$(this).val(max);   		
				}
                $("form[name='basket_change_quantity"+id+"']").submit();
            });
        })(jQuery);

    </script>
{/block}
{/if}