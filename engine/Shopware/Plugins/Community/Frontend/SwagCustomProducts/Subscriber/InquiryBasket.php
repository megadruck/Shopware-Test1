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

namespace ShopwarePlugins\SwagCustomProducts\Subscriber;

use Enlight\Event\SubscriberInterface;
use ShopwarePlugins\SwagCustomProducts\Components\Inquiry\InquiryService;
use ShopwarePlugins\SwagCustomProducts\Components\Types\TypeFactory;
use ShopwarePlugins\SwagCustomProducts\Components\Types\TypeInterface;
use ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManager;
use Shopware\Components\DependencyInjection\Container as DIContainer;

class InquiryBasket implements SubscriberInterface
{
    /**
     * @var DIContainer
     */
    private $container;

    /**
     * @var TypeInterface[]
     */
    private $types;

    /**
     * @var InquiryService
     */
    private $inquiryService;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Frontend_Forms::getContent::after' => 'onGetContent'
        ];
    }

    /**
     * @param DIContainer $container
     */
    public function __construct(DIContainer $container)
    {
        $this->container = $container;
    }

    /**
     * Hook which adds custom product options to the inquiry form.
     *
     * @param \Enlight_Hook_HookArgs $args
     * @return array
     */
    public function onGetContent(\Enlight_Hook_HookArgs $args)
    {
        /** @var TypeFactory $typeFactory */
        $typeFactory = $this->container->get('custom_products.type_factory');
        $this->types = $typeFactory->factory();

        $this->inquiryService = $this->container->get('custom_products.inquiry.inquiry_service');

        /** @var \Shopware_Controllers_Frontend_Forms $subject */
        $subject = $args->getSubject();
        $return = $args->getReturn();

        if ($subject->Request()->sInquiry != 'basket') {
            return $return;
        }

        $elements = $return['sElements'];
        $elementId = array_search('inquiry', array_column($elements, 'name', 'id'));
        $element = $elements[$elementId];

        $basket = Shopware()->Modules()->Basket()->sGetBasket();

        $text = $subject->get('snippets')->getNamespace('frontend/detail/comment')->get('InquiryTextBasket');

        foreach ($basket['content'] as $basketRow) {
            if (!empty($basketRow['modus'])) {
                continue;
            }

            if ($basketRow["customProductMode"] == 0 || $basketRow['customProductMode'] == BasketManager::MODE_PRODUCT) {
                $text .= "\n{$basketRow["quantity"]} x {$basketRow["articlename"]} ({$basketRow["ordernumber"]}) - {$basketRow["price"]} ";
                $text .= $this->container->get('shopware_storefront.context_service')->getShopContext()->getCurrency()->getCurrency();
            }

            if ($basketRow['customProductMode'] == BasketManager::MODE_PRODUCT) {
                $text .= $this->renderInquiryMessage($basketRow['customProductHash'], $basketRow);
            }

            if (!empty($text)) {
                $elements[$elementId]["value"] = $text;
                $element["value"] = $text;
            }
        }

        $return['sElements'][$elementId]['value'] = $element['value'];
        $return['sFields'][$elementId] = $this->_createInputElement($element, $subject->_postData[$elementId]);

        return $return;
    }


    /**
     * Renders the message for all options.
     *
     * @param string $customProductHash
     * @param array $basketPosition
     * @return string
     */
    private function renderInquiryMessage($customProductHash, array $basketPosition)
    {
        $text = '';
        if (empty($customProductHash)) {
            return '';
        }

        foreach ($basketPosition['custom_product_adds'] as $option) {
            $type = $this->types[$option['type']];
            $text .= $this->inquiryService->getMessage($option, $type->couldContainValues());
        }

        return $text;
    }

    /**
     * This method is copied due to extending issues.
     * @see: \Shopware_Controllers_Frontend_Forms::_createInputElement
     *
     * @param array $element
     * @param array $post
     * @return string
     */
    private function _createInputElement($element, $post = null)
    {
        if ($element['required'] == 1) {
            $requiredField = "is--required required";
            $requiredFieldSnippet = "%*%";
            $requiredFieldAria = 'required="required" aria-required="true"';
        } else {
            $requiredField = "";
            $requiredFieldSnippet = "";
            $requiredFieldAria = "";
        }

        $placeholder = "placeholder=\"{$element['label']}$requiredFieldSnippet\"";

        switch ($element['typ']) {
            case "password":
            case "email":
            case "text":
            case "textarea":
            case "file":
                if ((empty($post) && !empty($element["value"]))) {
                    $post = $element["value"];
                } elseif (!empty($post)) {
                    $post = '{literal}' . str_replace('{/literal}', '', $post) . '{/literal}';
                }
                break;
            case "text2":
                if (empty($post[0]) && !empty($element["value"][0])) {
                    $post[0] = $element["value"][0];
                } elseif (!empty($post[0])) {
                    $post[0] = "{literal}{$post[0]}{/literal}";
                }
                if (empty($post[1]) && !empty($element["value"][1])) {
                    $post[1] = $element["value"][1];
                } elseif (!empty($post[1])) {
                    $post[1] = "{literal}{$post[1]}{/literal}";
                }
                break;
            default:
                break;
        }

        $output = '';
        switch ($element['typ']) {
            case "password":
            case "email":
            case "text":
                $output .= "<input type=\"{$element['typ']}\" class=\"{$element['class']} $requiredField\" $requiredFieldAria value=\"{$post}\" id=\"{$element['name']}\" $placeholder name=\"{$element['name']}\"/>\r\n";
                break;
            case "checkbox":
                if ($post == $element['value']) {
                    $checked = " checked";
                } else {
                    $checked = "";
                }
                $output .= "<input type=\"{$element['typ']}\" class=\"{$element['class']} $requiredField\" $requiredFieldAria value=\"{$element['value']}\" id=\"{$element['name']}\" name=\"{$element['name']}\"$checked/>\r\n";
                break;
            case "file":
                $output .= "<input type=\"{$element['typ']}\" class=\"{$element['class']} $requiredField file\" $requiredFieldAria id=\"{$element['name']}\" $placeholder name=\"{$element['name']}\" maxlength=\"100000\" accept=\"{$element['value']}\"/>\r\n";
                break;
            case "text2":
                $element['class'] = explode(";", $element['class']);
                $element['name'] = explode(";", $element['name']);

                if (strpos($element['label'], ';') !== false) {
                    $placeholders = explode(";", $element['label']);
                    $placeholder0 = "placeholder=\"{$placeholders[0]}$requiredFieldSnippet\"";
                    $placeholder1 = "placeholder=\"{$placeholders[1]}$requiredFieldSnippet\"";
                } else {
                    $placeholder0 = $placeholder;
                    $placeholder1 = $placeholder;
                }

                $output .= "<input type=\"text\" class=\"{$element['class'][0]} $requiredField\" $requiredFieldAria value=\"{$post[0]}\" $placeholder0 id=\"{$element['name'][0]};{$element['name'][1]}\" name=\"{$element['name'][0]}\"/>\r\n";
                $output .= "<input type=\"text\" class=\"{$element['class'][1]} $requiredField\" $requiredFieldAria value=\"{$post[1]}\" $placeholder1 id=\"{$element['name'][0]};{$element['name'][1]}\" name=\"{$element['name'][1]}\"/>\r\n";
                break;
            case "textarea":
                if (empty($post) && $element["value"]) {
                    $post = $element["value"];
                }
                $output .= "<textarea class=\"{$element['class']} $requiredField\" $requiredFieldAria id=\"{$element['name']}\" $placeholder name=\"{$element['name']}\">{$post}</textarea>\r\n";
                break;
            case "select":
                $values = explode(";", $element['value']);
                $output .= "<select class=\"{$element['class']} $requiredField\" $requiredFieldAria id=\"{$element['name']}\" name=\"{$element['name']}\">\r\n\t";

                if (!empty($requiredField)) {
                    $requiredField = 'disabled="disabled"';
                }

                $label = $element['label'] . $requiredFieldSnippet;

                if (empty($post)) {
                    $output .= "<option selected=\"selected\" $requiredField value=\"\">$label</option>";
                } else {
                    $output .= "<option $requiredField value=\"\">$label</option>";
                }
                foreach ($values as $value) {
                    if ($value == $post) {
                        $output .= "<option selected>$value</option>";
                    } else {
                        $output .= "<option>$value</option>";
                    }
                }
                $output .= "</select>\r\n";
                break;
            case "radio":
                $values = explode(";", $element['value']);
                foreach ($values as $value) {
                    if ($value == $post) {
                        $checked = " checked";
                    } else {
                        $checked = "";
                    }
                    $output .= "<input type=\"radio\" class=\"{$element['class']} $requiredField\" value=\"$value\" id=\"{$element['name']}\" name=\"{$element['name']}\"$checked> $value ";
                }
                $output .= "\r\n";
                break;
        }
        return $output;
    }
}
