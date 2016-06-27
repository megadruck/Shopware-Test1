{if $customizingGroup}
    <script type="text/javascript">
        var jsCustomizingObject = {ldelim}
            'customizingEditorStylePath': decodeURIComponent('{{link file='frontend/_public/src/css/editor.css'}|urlencode}'),
            'customizingSavePath': decodeURIComponent('{{url controller=customizing action=save groupId=$customizingGroup.id articleId=$sArticle.articleID articleTax=$sArticle.tax forceSecure}|urlencode}'),
            'customizingUploadUrl': decodeURIComponent('{{url controller=customizing action=upload groupId=$customizingGroup.id articleId=$sArticle.articleID forceSecure}|urlencode}'),
            'customizingPrice': '{if $sArticle.priceStartingFrom}{$sArticle.priceStartingFrom|replace:',': '.'}{else}{$sArticle.price|replace:',':'.'}{/if}',
            'customizingResetPath': decodeURIComponent('{{url controller=customizing action=reset groupId=$customizingGroup.id forceSecure}|urlencode}')
            {rdelim};

        jQuery.datePickerSettings = {ldelim}
            closeText: "{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerCloseText'}{/s}",
            prevText: "{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerPrevText'}{/s}",
            nextText: "{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerNextText'}{/s}",
            currentText: "{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerCurrentText'}{/s}",
            monthNames: [{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerMonthNames'}{/s}],
            monthNamesShort: [{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerMonthShortNames'}{/s}],
            dayNames: [{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerDayNames'}{/s}],
            dayNamesShort: [{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerDayShortNames'}{/s}],
            dayNamesMin: [{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerDayMinNames'}{/s}],
            weekHeader: "{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerWeekHeader'}{/s}",
            dateFormat: "{s namespace='frontend/account/partner_statistic' name='PartnerDatePickerDateFormat'}{/s}",
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: "",
            showOn: "button",
            buttonText: ""
            {rdelim};
    </script>
{/if}
