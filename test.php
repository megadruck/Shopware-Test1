<?php

$obj = (object) array(
    'foo' => 'bar',
    '_position'=>array('_position2'=>array(array('name'=>'test1','nummer'=>'test2','titel'=>'test3'),array('name'=>'test1','nummer'=>'test2','titel'=>'test3')),array('name'=>'test1','nummer'=>'test2','titel'=>'test3'),array('name'=>'test1','nummer'=>'test2','titel'=>'test3')),

    'property' => 'value'

);

foreach ($obj->_position['_position2'] as &$key)
{

    echo $obj->_position['_position2']$key['barcode'];
}
