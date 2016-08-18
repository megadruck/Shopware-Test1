{namespace name="frontend/detail/option"}

{literal}
    <script id="overview-template" type="text/x-handlebars-template">
        <div class="panel has--border custom-products--surcharges">
            <div class="panel--title is--underline">{/literal}{s name="detail/overview/surcharge_price"}{/s}{literal}</div>
            <div class="panel--body">
                <ul class="custom-products--overview-list custom-products--list-surcharges">
                    <li class="custom-products--overview-base">
                        {/literal}&nbsp;&nbsp;{s name="detail/overview/base_price"}{/s}{literal}
                        <span class="custom-products--overview-price">{{formatPrice basePrice}}</span>
                    </li>
                    {{#surcharges}}
                        <li>
                            {{#if hasParent}}
                                &emsp;
                            {{/if}}
                            <span class="custom-products--overview-name">{{name}}</span>
                            <span class="custom-products--overview-price">{{formatPrice price}}</span>
                        </li>
                    {{/surcharges}}

                    <li class="custom-products--overview-total">
                        {/literal}{s name="detail/overview/total_surcharges"}{/s}{literal}
                        <span class="custom-products--overview-price">{{formatPrice totalUnitPrice}}</span>
                    </li>
                </ul>
            </div>

            <div class="panel--title is--underline">{/literal}{s name="detail/overview/once_price"}{/s}{literal}</div>
            <div class="panel--body">
                <ul class="custom-products--overview-list custom-products--list-once">
                    {{#onceprices}}
                        <li>
                            {{#if hasParent}}
                                &emsp;
                            {{/if}}
                            <span class="custom-products--overview-name">{{name}}</span>
                            <span class="custom-products--overview-price">{{formatPrice price}}</span>
                        </li>
                    {{/onceprices}}

                    <li class="custom-products--overview-total custom-products--overview-once">
                        {/literal}{s name="detail/overview/total_once"}{/s}{literal}
                        <span class="custom-products--overview-price">{{formatPrice totalPriceOnce}}</span>
                    </li>
                </ul>
            </div>

            <div class="panel--title is--underline">{/literal}{s name="detail/overview/price_total"}{/s}{literal}</div>
            <div class="panel--body">
                <ul class="custom-products--overview-list custom-products--list-once">
                    <li class="custom-products--overview-total custom-products--overview-once">
                        {/literal}{s name="detail/overview/total_price"}{/s}{literal}
                        <span class="custom-products--overview-price">{{formatPrice total}}</span>
                    </li>
                </ul>
            </div>
        </div>
    </script>
{/literal}
