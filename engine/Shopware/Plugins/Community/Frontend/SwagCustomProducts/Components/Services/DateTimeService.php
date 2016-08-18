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

use DateTime;

class DateTimeService
{
    /** DateTime format */
    const YMD_HIS = "Y-m-d H:i:s";
    const GERMAN = "d.m.Y H:i:s";
    const UNIX_TIMESTAMP = "U";

    /** NOW string */
    const NOW = "NOW";

    /**
     * Get the formatted DateTime string
     *
     * @param string $format
     * @return string
     */
    public function getNowString($format = self::UNIX_TIMESTAMP)
    {
        $dateTime = $this->getDateTime();

        return $dateTime->format($format);
    }

    /**
     * Change a formatted dateTime string
     *
     * @param $dateTimeString
     * @param $newFormat
     * @return string
     */
    public function changeFormatString($dateTimeString, $newFormat)
    {
        $dateTime = $this->getDateTime($dateTimeString);

        return $dateTime->format($newFormat);
    }

    /**
     * @param string $dateTimeString
     * @return DateTime
     */
    public function getDateTime($dateTimeString = self::NOW)
    {
        return new DateTime($dateTimeString);
    }
}
