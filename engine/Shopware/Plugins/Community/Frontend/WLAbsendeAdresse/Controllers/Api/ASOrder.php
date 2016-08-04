<?php

use Doctrine\ORM\Query;
/**
 *
 * @author svg <s.vgroenheim@wistundlaumann.de>
 */
class Shopware_Controllers_Api_ASOrder extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\Order
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('ASOrder');
    }

    /**
     * Get list of orders
     *
     * GET /api/asorder/
     */
    public function indexAction()
    {
        $limit = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $result = $this->resource->getList($offset, $limit, $filter, $sort);
        foreach ($result['data'] as &$order) {
            $order['senderaddress'] = $this->getDataById($order['id']);
            //$result['data'][$key]['senderaddress'] = $this->getDataById($result['data'][$key]['id']);
        }

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    /**
     * Get one order
     *
     * GET /api/asorder/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        $useNumberAsId = (boolean) $this->Request()->getParam('useNumberAsId', 0);

        if ($useNumberAsId) {
            $order = $this->resource->getOneByNumber($id);
        } else {
            $order = $this->resource->getOne($id);
        }
        $order['senderaddress'] = $this->getDataById($order['id']);

        $this->View()->assign('data', $order);
        $this->View()->assign('success', true);
    }

    /**
     * Update order
     *
     * PUT /api/asorder/{id}
     */
    public function putAction()
    {
        $id = $this->Request()->getParam('id');
        $useNumberAsId = (boolean) $this->Request()->getParam('useNumberAsId', 0);
        $params = $this->Request()->getPost();

        if ($useNumberAsId) {
            $order = $this->resource->updateByNumber($id, $params);
        } else {
            $order = $this->resource->update($id, $params);
        }

        $location = $this->apiBaseUrl . 'orders/' . $order->getId();
        $data = array(
            'id'       => $order->getId(),
            'location' => $location
        );

        $this->View()->assign(array('success' => true, 'data' => $data));
    }

    public function getDataById($orderId){
        $em = Shopware()->Models();
        $em->getClassMetadata('Shopware\CustomModels\Order\OrderSenderAddress');
        $sql = "SELECT address, ord, state, country FROM Shopware\CustomModels\Order\OrderSenderAddress address ";
        $sql .= " LEFT JOIN address.order ord ";
        $sql .= " LEFT JOIN address.state state ";
        $sql .= " LEFT JOIN address.country country ";
        $sql .= " WHERE address.order = " . $orderId;
        $query = $em->createQuery($sql);
        return $query->getResult ( Query::HYDRATE_ARRAY )[0];
    }
}
