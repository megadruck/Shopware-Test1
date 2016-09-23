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
 * Date: 22.09.2016
 * Time: 15:14
 */

$str="CMYK-Farbwerteatlas / CMYK Color Manual PDF/EPS keine E-Mail";
$additional =" PDF/EPS keine E-Mail";

$res=str_replace($additional, '', $str);
echo $res;