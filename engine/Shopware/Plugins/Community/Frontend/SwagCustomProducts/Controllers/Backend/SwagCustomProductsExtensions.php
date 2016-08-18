<?php

use Doctrine\DBAL\Connection;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\ColorSelectType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\DateType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\FileUploadType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\ImageSelectType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\ImageUploadType;

class Shopware_Controllers_Backend_SwagCustomProductsExtensions extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * Provides an action which can be triggered using an AJAX request to get the basic
     * custom product information.
     *
     * We're using it in the product module to check if a product has an associated custom product template.
     */
    public function getTemplateByProductIdAction()
    {
        $productId = (int)$this->Request()->getParam('productId');

        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $query = $connection->createQueryBuilder();

        $data = $query->select('template.*')
            ->from('s_plugin_custom_products_template', 'template')
            ->join('template', 's_plugin_custom_products_template_product_relation', 'product', 'product.template_id = template.id')
            ->where('product.article_id = :productId')
            ->setParameter('productId', $productId)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $this->view->assign(['success' => true, 'data' => $data]);
    }

    /**
     * Get the productConfiguration by hash
     */
    public function getConfigurationByHashAction()
    {
        $hash = (string) $this->Request()->getParam('hash');
        $this->View()->assign('data', $this->getOrderedCustomProduct($hash));
    }

    /**
     * @param string $hash
     * @return array
     */
    private function getOrderedCustomProduct($hash)
    {
        $data = $this->getCustomProduct($hash);
        $config = json_decode($data['configuration'], true);
        $options = json_decode($data['template'], true);

        $result = [];
        foreach ($config as $optionId => $optionValue) {
            $option = $this->getElementById($optionId, $options);
            if ($option === null) {
                continue;
            }

            if ($option['could_contain_values']) {
                $result[] = [
                    'label' => $option['name'],
                    'type' => $option['type'],
                    'multi' => true,
                    'value' => $this->iterateValues($optionValue, $option)
                ];
            } else {
                $result[] = [
                    'label' => $option['name'],
                    'type' => $option['type'],
                    'multi' => false,
                    'value' => $this->getOptionValue($option, $optionValue)
                ];
            }
        }
        return $result;
    }

    /**
     * @param string $hash
     * @return array
     */
    private function getCustomProduct($hash)
    {
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        $query->select(['configuration', 'template']);
        $query->from('s_plugin_custom_products_configuration_hash', 'hash_table');
        $query->where('hash_table.hash = :hash');
        $query->setParameter(':hash', $hash);
        $data = $query->execute()->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * @param array $option
     * @param array $optionValue
     * @return string
     */
    private function getOptionValue($option, $optionValue)
    {
        switch ($option['type']) {
            case DateType::TYPE:
                return implode(' ', $optionValue);
            default:
                return array_shift($optionValue);
        }
    }

    /**
     * @param int $id
     * @param array $elements
     * @return null|array
     */
    private function getElementById($id, $elements)
    {
        foreach ($elements as $element) {
            if ($element['id'] == $id) {
                return $element;
            }
        }
        return null;
    }

    /**
     * @param array $values
     * @param array $option
     * @return array
     */
    private function iterateValues($values, $option)
    {
        $result = [];
        foreach ($values as $data) {
            $valueData = $this->getElementById($data, $option['values']);

            $label = $this->getLabelOfValue($option['type'], $option['name'], $valueData['name']);
            $value = $this->getDataOfValue($option['type'], $valueData['value'], $data, $valueData);

            $result[] = [
                'label' => $label,
                'type' => $option['type'],
                'value' => $value
            ];
        }
        return $result;
    }

    /**
     * @param string $type
     * @param string $optionName
     * @param string $valueName
     * @return string
     */
    private function getLabelOfValue($type, $optionName, $valueName)
    {
        switch ($type) {
            case FileUploadType::TYPE:
            case ImageUploadType::TYPE:
                return $optionName;

            default:
                return $valueName;
        }
    }

    /**
     * @param string $type
     * @param string $value
     * @param string|array $data
     * @param array $valueData
     * @return array|string
     */
    private function getDataOfValue($type, $value, $data, $valueData)
    {
        switch ($type) {
            case ImageSelectType::TYPE:
                return $valueData['image']['file'];
            case FileUploadType::TYPE:
            case ImageUploadType::TYPE:
                return json_decode($data, true);
            case ColorSelectType::TYPE:
                return $value;
            default:
                return $data;
        }
    }
}