<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace ShopwarePlugins\SwagCustomProducts\Components\Inquiry\Strategy;

class SelectedValueStrategy implements StrategyInterface
{
    /**
     * @var \sArticles
     */
    private $sArticles;

    /**
     * @var \Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @var string
     */
    private $currency;

    /**
     * @param \sArticles $sArticles
     * @param \Shopware_Components_Snippet_Manager $snippetManager
     * @param string $currency
     */
    public function __construct(
        \sArticles $sArticles,
        \Shopware_Components_Snippet_Manager $snippetManager,
        $currency
    ) {
        $this->sArticles = $sArticles;
        $this->snippetManager = $snippetManager;
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function generateMessage(array $data)
    {
        $text = "\n" . self::TABULATOR . $data['name'];
        $text .= $this->getSurchargeText($data) . ':';

        $text .= "\n" . self::TABULATOR . self::TABULATOR . self::TABULATOR . $data['selectedValue'][0];
        return $text;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getSurchargeText(array $data)
    {
        if ($data['surcharge'] && $data['is_once_surcharge']) {
            return ' ' . $this->getOncePriceSnippet($this->formatPrice($data['surcharge']));
        }

        if ($data['surcharge'] && !$data['is_once_surcharge']) {
            return ' ' . $this->getSurchargeSnippet($this->formatPrice($data['surcharge']));
        }
        return '';
    }

    /**
     * @param float $price
     * @return string
     */
    private function getOncePriceSnippet($price)
    {
        $snippet = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE)->get('inquiry/once_surcharge');
        $price .= ' ' . $this->currency;

        return sprintf($snippet, $price);
    }

    /**
     * @param float $price
     * @return string
     */
    private function getSurchargeSnippet($price)
    {
        $snippet = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE)->get('inquiry/surcharge');
        $price .= ' ' . $this->currency;

        return sprintf($snippet, $price);
    }

    /**
     * @param float|string $price
     * @return float
     */
    private function formatPrice($price)
    {
        return $this->sArticles->sFormatPrice($price);
    }
}
