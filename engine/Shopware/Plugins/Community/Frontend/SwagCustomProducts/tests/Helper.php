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

namespace ShopwarePlugins\SwagCustomProducts\tests\phpUnit;

use Shopware\CustomModels\SwagCustomProducts\Template;

/**
 * Class Helper
 * @package ShopwarePlugins\SwagCustomProducts\tests\phpUnit
 */
class Helper
{
    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var array
     */
    private $createdEntities = [];

    public function __construct()
    {
        $this->db = Shopware()->Db();
    }

    /**
     * @param array $data
     * @return Template
     */
    public function getTemplateDemoData(array $data = [])
    {
        $default = [
            'internalName' => 'test_internal_name',
            'displayName' => 'Test Configuration',
            'stepByStepConfiguration' => false,
            'created' => new \DateTime(),
            'articles' => [],
            'options' => [
                $this->getOptionDemoData('textfield'),
                $this->getOptionDemoData('textarea')
            ]
        ];

        $template = new Template();
        $data = array_merge($default, $data);
        $template->fromArray($data);

        Shopware()->Models()->persist($template);
        Shopware()->Models()->flush($template);

        $this->createdEntities[] = $template;

        return $template;
    }

    /**
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public function getOptionDemoData($type)
    {
        switch ($type) {
            case 'textfield':
                return [
                    'name' => 'test_textfield',
                    'typeId' => 1,
                    'placeholder' => 'test placeholder',
                    'defaultValue' => 'test default value',
                    'position' => 1,
                    'required' => true
                ];
                break;
            case 'checkbox':
                return [
                    'name' => 'test_checkbox',
                    'typeId' => 2,
                    'position' => 2
                ];
                break;
            case 'textarea':
                return [
                    'name' => 'test_textarea',
                    'typeId' => 3,
                    'placeholder' => 'test placeholder for a textarea',
                    'defaultValue' => 'default value textarea',
                    'position' => 3
                ];
                break;
            default:
                throw new \Exception('No valid option type was passed');
        }
    }

    public function cleanUp()
    {
        foreach ($this->createdEntities as $entity) {
            Shopware()->Models()->remove($entity);
            Shopware()->Models()->flush($entity);
        }
    }
}
