<?php
/**
 * Model for card_orders talbe
 *
 * @author marat
 */

require_once ('BaseModel.php');

class CardOrders extends BaseModel
{
    protected $_name = 'card_orders';
    protected $_sequence = 'card_orders_seq';

    public function getClientOrders($clientID)
    {
        $clientID = intval($clientID);

        if ( ! $clientID )
        {
            return array();
        }

        $sql = "
            SELECT
                *,
                (SELECT
                    SUM(amount)
                FROM
                    card_orders_details
                WHERE order_id = card_orders.id) * (100-discount)/100 As amount
            FROM
                card_orders
            WHERE
                is_deleted = false
                AND client_id = {$clientID}
            ";

        return $this->_db->fetchAll($sql);
    }

    public function setPosted($id)
    {
        $id = intval($id);

        if ( ! $id )
        {
            return;
        }

        $data['is_posted'] = true;
        $this->update($data, "id = {$id}");
    }

    public function setDiscount($orderID, $discount)
    {
        $orderID = intval($orderID);
        
        if ( ! $orderID || ! $discount )
        {
            return;
        }

        $data['discount'] = $discount;

        $this->update($data, "id = {$orderID}");
    }

    public function getMaxInvoiceNumber()
    {
        $sql = "
            select (max(invoice_number)::integer + 1)  as max_in from card_orders limit 1
        ";
        return $this->_db->fetchOne($sql);
    }

    public function getNextOrderNumber($client_id)
    {
        $sql = "
            select (max(number)::integer + 1) as max_number from card_orders where client_id = {$client_id}
        ";
        return $this->_db->fetchOne($sql);
    }
}
