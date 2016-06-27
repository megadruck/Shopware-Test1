<span id = "sofort_payment_template_su" >
{if $sofortSofortbankingIsRecommended}
    <p id = "sofort_payment_text_sofortbanking" >{$sofortSofortbankingRecommendedText}</p >
{else}
    <br >
{/if}
    {if $sofortSofortbankingIsShowingBanner}
        {if $sofortSofortbankingIsCustomerProtectionEnabled}
            <a href = "{$sofortSofortBankingLinkBannerCustomerProtection}" target = "_blank" >
                <img src = '{$sofortSofortbankingBannerCp}' alt = '{$sofortSofortbankingAlt}' />
            </a>
        {else}
            <a href = "{$sofortSofortBankingLinkBannerLogo}" target = "_blank" >
                <img src = '{$sofortSofortbankingBanner}' alt = '{$sofortSofortbankingAlt}' />
            </a>
        {/if}
    {/if}
    {if $sofortSofortbankingIsShowingLogo}
        <a href = "{$sofortSofortBankingLinkBannerLogo}" target = "_blank" >
            <img src = '{$sofortSofortbankingLogo}'>
        </a>
        {if $sofortSofortbankingIsCustomerProtectionEnabled}
            <p class = "sofort_payment_text" >{$sofortSofortbankingLogoTextCp}</p>
        {/if}
        <p class = "sofort_payment_text" >{$sofortSofortbankingLogoText}</p>
    {/if}
</span >