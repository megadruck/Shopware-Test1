<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ArticleStockHelper
 *
 * @author Stefan
 */
class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_ArticleStockHelper {

    /**
     * Restores the Stock for every item for the given order
     *
     * @param int $orderId
     * @return boolean
     */
    public function restoreArticleStock($orderId)
    {
        $result = false;
        foreach($this->getOrderBasket($orderId) as $article){
            if(!is_array($article) || !isset($article['quantity']) || !isset($article['quantity'])){
                continue;
            }
            try {
                Shopware()->Db()->query("UPDATE `s_articles_details` SET `instock` = `instock`+? WHERE `ordernumber`=?", array(
                    $article['quantity'],
                    $article['articleordernumber']
                ));
                $result = true;
            } catch (Exception $exception) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Returns the BasketItems for the given order
     *
     * @param int $orderId
     * @return array
     */
    private function getOrderBasket($orderId){
        $select = Shopware()->Db()->select()
            ->from('s_order_details', array(
                'articleordernumber', 'quantity'
            ))
            ->where('`modus` = 0')
            ->where('`articleID` != 0')
            ->where('`orderID` = ?', $orderId);
        return Shopware()->Db()->fetchAll($select);
    }
}
