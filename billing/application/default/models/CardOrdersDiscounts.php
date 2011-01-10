<?php
/**
 * Model for card_orders_discounts table
 * 
 * @author marat
 */

require_once ('BaseModel.php');

class CardOrdersDiscounts extends BaseModel
{
    protected $_name ='card_orders_discounts';
    protected $_sequence = 'card_orders_discounts_seq';
    
    public function getDiscountByAmount($amount)
    {
        if ( ! $amount )
        {
            return 0;
        }
        
        $sql = "
            SELECT
                MAX(discount)
            FROM
                card_orders_discounts
            WHERE 
                amount <= {$amount}
            ";
        
        return $this->_db->fetchOne($sql);
    }
    
    public function getAllDiscounts()
    {
        return $this->fetchAll(null, 'amount')->toArray();
    }
}