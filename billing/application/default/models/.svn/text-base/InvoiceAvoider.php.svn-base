<?php
/**
 * Model of invoices_avoiders table
 */

require_once('BaseModel.php');

class InvoiceAvoiderModel extends BaseModel
{
    protected $_name = 'invoices_avoiders';
    protected $_sequence = 'invoice_avoid_seq';

    public function getInvoicesAvoiders()
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        $sql = "
            SELECT
                c.client_id, c.client_name, ia.startdate as avoiding_startdate
            FROM
                invoices i
                INNER JOIN (select * from clients union all select * from clients_arhiv) AS c
                    ON i.client_id = c.client_id
                LEFT JOIN invoice_details id
                        ON id.invoice_id = i.invoice_id
                LEFT OUTER JOIN invoices_avoiders ia
                    ON c.client_id = ia.client_id
            WHERE
                c.client_type_id = 0
 				AND i.lastdate between '{$startDate}' AND '{$endDate}'
                AND id.total != 0
 				AND c.vip = false
 				AND c.is_employee = false
 				AND c.is_donate = false
            GROUP BY
 				c.client_id,
 				i.schetfnum,
 				c.client_name,
 				c.address,
 				c.phone,
                ia.startdate
            ORDER BY c.client_id
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getInvoicesAvoidersIds()
    {
        $sql = "
            SELECT
                c.client_id
            FROM
                clients AS c
                INNER JOIN invoices_avoiders ia
                    ON c.client_id = ia.client_id
            WHERE
                c.client_type_id = 0
            ORDER BY c.client_id
        ";
        
        return $this->_db->fetchAll($sql);
    }

    private function separate($currentIds, $modifiedIds)
    {
        $result = array('insert' => array(), 'delete' => array());
        foreach ($currentIds as $row)
        {
            $dbIds[] = $row['client_id'];
        }
        foreach ($dbIds as $id)
        {
            if (!in_array($id, $modifiedIds))
            {
                $result['delete'][] = $id;
            }
        }
        foreach ($modifiedIds as $id)
        {
            if (!in_array($id, $dbIds))
            {
                $result['insert'][] = $id;
            }
        }
        return $result;
    }

    public function updateState($ids)
    {
        $separatedIds = $this->separate($this->getInvoicesAvoidersIds(), $ids);
        $this->startTransaction();
        try
        {
            foreach ($separatedIds['delete'] as $deleted)
            {
                $this->delete("client_id={$deleted}");
            }

            foreach ($separatedIds['insert'] as $inserted)
            {
                $this->insert(array('client_id' => $inserted));
            }
            $this->commitTransaction();
        }
        catch (Exception $ex)
        {
            $this->rollbackTransaction();
            echo $ex;
            exit;
        }
    }
}
