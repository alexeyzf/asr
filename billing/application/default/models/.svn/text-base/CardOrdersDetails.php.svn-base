<?php
/**
 * Model for card_orders_details table
 *
 * @author marat
 */

require_once ('BaseModel.php');

class CardOrdersDetails extends BaseModel
{
    protected $_name = 'card_orders_details';
    protected $_sequence = 'card_orders_details_seq';

    public function addDetail($orderID, $cardType, $faceValue, $count)
    {
        $orderID = intval($orderID);
        $cardType = intval($cardType);
        $faceValue = intval($faceValue);
        $count = intval($count);

        $data['order_id'] = $orderID;
        $data['card_type'] = $cardType;
        $data['face_value'] = $faceValue;
        $data['count'] = $count;
        $data['amount'] = $faceValue * 1000 * $count;

        $this->insert($data);
    }

    public function getOrderAmount($orderID)
    {
        $orderID = intval($orderID);

        if ( ! $orderID )
        {
            return;
        }

        $sql = "
            SELECT
                SUM(amount)
            FROM
                card_orders_details
            WHERE
                order_id = {$orderID}
        ";

        return $this->_db->fetchOne($sql);
    }

    public function getOrderDetails($orderID)
    {
        $orderID = intval($orderID);

        if ( ! $orderID )
        {
            return array();
        }

        $details = $this->fetchAll("order_id = {$orderID}")->toArray();

        $result = array();

        foreach ($details as $detail)
        {
            $result[$detail['face_value']] = $detail;
        }

        return $result;
    }
}