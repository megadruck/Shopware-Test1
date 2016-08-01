<?php

namespace Shopware\Themes\Megadruck;

use Shopware\Components\Form as Form;

class Theme extends \Shopware\Components\Theme
{
    protected $extend = 'DashoneV2';

    protected $name = <<<'SHOPWARE_EOD'
Megadrucktemplate
SHOPWARE_EOD;

    protected $description = <<<'SHOPWARE_EOD'
Megadrucktemplate
SHOPWARE_EOD;

    protected $author = <<<'SHOPWARE_EOD'

SHOPWARE_EOD;

    protected $license = <<<'SHOPWARE_EOD'

SHOPWARE_EOD;

    public function createConfig(Form\Container\TabContainer $container)
    {
    }
}