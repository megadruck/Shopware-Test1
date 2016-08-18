<?php

namespace Tests\Shopware\ImportExport;

class ExpressionEvaluatorTest extends ImportExportTestHelper
{
    public function testPhpEvaluator()
    {
        $variables = array(
            'title' => 'Product',
            'active' => true,
            'status' => 2,
        );

        $expression1 = '$active ? false : true';
        $expression2 = '$title . \'-Test\'';

        $transformersFactory = $this->Plugin()->getDataTransformerFactory();

        $phpEval = $transformersFactory->createValueConvertor('phpEvaluator');

        $evalVariable1 = $phpEval->evaluate($expression1, $variables);
        $evalVariable2 = $phpEval->evaluate($expression2, $variables);

        $this->assertEquals($evalVariable1, false);
        $this->assertEquals($evalVariable2, 'Product-Test');
    }

    public function testSmartyEvaluator()
    {
        $variables = array(
            'title' => 'Product',
            'active' => true,
            'status' => 2,
        );

        $expression1 = '{if $active} false {else} true {/if}';
        $expression2 = '{if $title == \'Product\'} {$title}-Test {/if}';

        $transformersFactory = $this->Plugin()->getDataTransformerFactory();

        $smartyEval = $transformersFactory->createValueConvertor('smartyEvaluator');

        $evalVariable1 = $smartyEval->evaluate($expression1, $variables);
        $evalVariable2 = $smartyEval->evaluate($expression2, $variables);

        $this->assertEquals($evalVariable1, 'false');
        $this->assertEquals($evalVariable2, 'Product-Test');
    }
}
