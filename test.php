<?php
/**
 * Copyright (c) 2016
 * Megadruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 *
 * Eichendorffstrasse 34b
 * 26655 Westerstede
 * Tel. 04488 52540-25
 * Fax. 04488 52540-14
 *
 * www.megadruck.de
 */

/**
 * Created by PhpStorm.
 * User: jfrintrop
 * Date: 10.10.2016
 * Time: 15:08
 */

$UserId="123";
$path="media/DruckDaten/{YEAR}/{DAY}/{CUSTOM}";

$path = preg_replace('/{YEAR}/',date("Y"),$path);
$path = preg_replace('/{MONTH}/',date("m"),$path);
$path = preg_replace('/{DAY}/',date("d"),$path);
$path = preg_replace('/{CUSTOM}/',$UserId,$path);

if( !is_dir($_SERVER["DOCUMENT_ROOT"].$path) && !mkdir($_SERVER["DOCUMENT_ROOT"].$path, 511,true) )
{
    echo "Error: Ordner ".$path." kann nicht erstellt werden.";
}
