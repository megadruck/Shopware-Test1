<span id = "sofort_payment_template_ideal" >
{if $sofortIdealIsRecommended}
    <p id = "sofort_payment_text_ideal" >{$sofortIdealRecommendedText}</p >
{else}
    <br >
{/if}
{if $sofortIdealIsShowingBanner}
    <a href = "{$sofortIdealLink}" target = "_blank" >
        <img src = '{$sofortIdealBanner}' alt = '{$sofortIdealAlt}' />
    </a>
{/if}
{if $sofortIdealIsShowingLogo}
    <a href = "{$sofortIdealLink}" target = "_blank" >
        <img src = '{$sofortIdealLogo}' alt = '{$sofortIdealAlt}' />
    </a>
{/if}
</span >
<div class = "sofortIdealSelect" style = "text-align:center;" >
    <label for = "sofort_ideal_bank_select" >{$sofortIdealWelcomeMessage}</label >
    <select name = "sofort_ideal_bank_select" id = "sofort_ideal_bank_select" onchange="sofortPaymentiDealBankChange('{url controller='PaymentIdeal' action='saveBank'}');">
        {foreach item=item from=$sofortIdealBanks}
            <option value = "{$item['code']}" {if $sUserData.additional.user.sofortIdealBank == $item['code']}selected{/if}>{$item['name']}</option >
        {/foreach}
    </select>
</div>