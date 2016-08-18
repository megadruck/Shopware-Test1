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

namespace ShopwarePlugins\SwagCustomProducts\Components\Services;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\CustomModels\SwagCustomProducts\Option;
use Shopware\CustomModels\SwagCustomProducts\Template;
use Shopware\CustomModels\SwagCustomProducts\Value;
use Shopware_Components_Translation as Translator;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;

class TranslationService
{
    const TEMPLATE = 'customProductTemplateTranslations';
    const OPTIONS = 'customProductOptionTranslations';
    const VALUES = 'customProductValueTranslations';

    /** @var ContextServiceInterface $contextService */
    private $contextService;

    /** @var Connection $connection */
    private $connection;

    /** @var Translator $translator */
    private $translator;

    /**
     * @param ContextService $contextService
     * @param Connection $connection
     */
    public function __construct(ContextService $contextService, Connection $connection)
    {
        $this->contextService = $contextService;
        $this->connection = $connection;
        $this->translator = new Translator();
    }

    /**
     * @param array $template
     * @return array
     */
    public function translateTemplate(array $template)
    {
        // get the Translations
        $customProductTemplate = array_merge(
            $template,
            $this->getTranslation(
                TranslationService::TEMPLATE,
                $template['id']
            )
        );

        $customProductTemplate['options'] = $this->translateOptions($customProductTemplate['options']);

        return $customProductTemplate;
    }

    /**
     * @param string $translationType
     * @param int $objectId
     * @return array
     */
    private function getTranslation($translationType, $objectId)
    {
        $shopId = $this->contextService->getShopContext()->getShop()->getId();

        $translation = $this->translator->read(
            $shopId,
            $translationType,
            $objectId
        );

        if (empty($translation)) {
            return [];
        }

        $translation['display_name'] = $translation['displayName'];
        $translation['default_value'] = $translation['defaultValue'];
        unset($translation['displayName']);
        unset($translation['defaultValue']);

        return $translation;
    }

    /**
     * @param Template $oldTemplate
     * @param Template $newTemplate
     */
    public function cloneTranslations($oldTemplate, $newTemplate)
    {
        $templateTranslations = $this->getRawTranslations(self::TEMPLATE, $oldTemplate->getId());
        $this->saveTranslation($templateTranslations, $newTemplate->getId());

        $this->cloneOptionTranslations($oldTemplate, $newTemplate);
    }

    /**
     * @param string $objectType
     * @param int $objectId
     * @return array
     */
    private function getRawTranslations($objectType, $objectId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $translations = $queryBuilder->select('*')
            ->from('s_core_translations')
            ->where('objecttype = :objectType')
            ->andWhere('objectkey = :objectKey')
            ->setParameter('objectType', $objectType)
            ->setParameter('objectKey', $objectId)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return array_shift($translations);
    }

    /**
     * @param array $options
     * @return array
     */
    private function translateOptions(array $options)
    {
        $translatedOptions = [];

        foreach ($options as &$option) {
            $translatedOption = array_merge(
                $option,
                $this->getTranslation(
                    TranslationService::OPTIONS,
                    $option['id']
                )
            );

            if (!((int)$option['could_contain_values'])) {
                $translatedOptions[] = $translatedOption;
                continue;
            }

            $translatedOption['values'] = $this->translateValues($option['values']);
            $translatedOptions[] = $translatedOption;
        }

        return $translatedOptions;
    }

    /**
     * @param array $values
     * @return array
     */
    private function translateValues(array $values)
    {
        $translatedValues = [];

        foreach ($values as &$value) {
            $translatedValues[] = array_merge(
                $value,
                $this->getTranslation(
                    TranslationService::VALUES,
                    $value['id']
                )
            );
        }

        return $translatedValues;
    }

    /**
     * @param array $translation
     * @param int $newObjectId
     * @return string | boolean
     */
    private function saveTranslation($translation, $newObjectId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        if (empty($translation) || !$newObjectId) {
            return false;
        }

        $translation = $this->prepareTranslationForSave($translation, $newObjectId);

        $queryBuilder->insert('s_core_translations')
            ->values([
                'objecttype' => '?',
                'objectdata' => '?',
                'objectkey' => '?',
                'objectlanguage' => '?',
                'dirty' => '?'
            ])
            ->setParameters($translation)
            ->execute();

        return $this->connection->lastInsertId();
    }

    /**
     * @param Template $oldTemplate
     * @param Template $newTemplate
     */
    private function cloneOptionTranslations($oldTemplate, $newTemplate)
    {
        foreach ($oldTemplate->getOptions() as $option) {
            $optionTranslation = $this->getRawTranslations(self::OPTIONS, $option->getId());

            if (empty($optionTranslation)) {
                continue;
            }

            $newOption = $this->getNewOption($option, $newTemplate);

            if (!$newOption) {
                continue;
            }

            $this->saveTranslation($optionTranslation, $newOption->getId());

            if ($option->getCouldContainValues()) {
                $this->cloneValueTranslation($option, $newOption);
            }
        }
    }

    /**
     * @param Option $oldOption
     * @param Option $newOption
     */
    private function cloneValueTranslation($oldOption, $newOption)
    {
        foreach ($oldOption->getValues() as $value) {
            $valueTranslation = $this->getRawTranslations(self::VALUES, $value->getId());

            if (empty($valueTranslation)) {
                continue;
            }

            $newValue = $this->getNewValue($value, $newOption);

            if (!$newValue) {
                continue;
            }

            $this->saveTranslation($valueTranslation, $newValue->getId());
        }
    }

    /**
     * @param Option $oldOption
     * @param Template $newTemplate
     * @return null|Option
     */
    private function getNewOption(Option $oldOption, Template $newTemplate)
    {
        foreach ($newTemplate->getOptions() as $option) {
            if ($option->getPosition() == $oldOption->getPosition()) {
                return $option;
            }
        }

        return null;
    }

    /**
     * @param Value $oldValue
     * @param Option $newOption
     * @return null|Value
     */
    private function getNewValue(Value $oldValue, Option $newOption)
    {
        foreach ($newOption->getValues() as $value) {
            if ($value->getPosition() == $oldValue->getPosition()) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array $translation
     * @param int $newObjectId
     * @return array
     */
    private function prepareTranslationForSave(array $translation, $newObjectId)
    {
        return [
            $translation['objecttype'],
            $translation['objectdata'],
            $newObjectId,
            $translation['objectlanguage'],
            $translation['dirty']
        ];
    }
}
