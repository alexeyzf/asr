<?php
/**
 * Helper for generate pdf for card-order 
 * 
 * @author marat
 */

require_once ('PdfHelper.php');

class CardOrderPdfHelper extends PdfHelper
{
    public function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }
    
    public function startPrint($contractNumber, $contractDate, $orderNumber, $orderDiscount, $orderDetails)
    {
        $this->_pdf->SetFontSize(8);
        $this->_pdf->SetTitle("Заказ №{$orderNumber} клиента {$this->_clientInfo['client_name']}");

        $template = $this->getTemplate('card_order');

        $content = $this->replaceCompanyRekvizits($template);

        $contractDate = new Zend_Date($contractDate);
        $contractDate = $contractDate->toString('d MMMM Y г.');
        $currentDate = new Zend_Date();
        $currentDate = $currentDate->toString('d MMMM Y г.');

        $content = str_replace('{$currentDate}', $currentDate, $content);
        $content = str_replace('{$contractDate}', $contractDate, $content);
        $content = str_replace('{$contractNumber}', $contractNumber, $content);
        $content = str_replace('{$clientName}', $this->_clientInfo['client_name'], $content);
        $content = str_replace('{$contactName}', $this->_clientInfo['contact_name'], $content);
        $content = str_replace('{$orderNumber}', $orderNumber, $content);
        $this->_pdf->writeHTML($content);

        $detailsContent = $this->getCardOrderDetailsString($orderDiscount, $orderDetails);
        $this->_pdf->SetFont('arial');
        $this->_pdf->writeHTML($detailsContent);

        $footerContent = $this->getCardOrderFooterString();
        $this->_pdf->writeHTML($footerContent);

        $this->_pdf->writeHTML("<hr>");

        $this->_pdf->writeHTML($content);
        $this->_pdf->writeHTML($detailsContent);
        $this->_pdf->writeHTML($footerContent);
        
        $this->sentResponce("order_{$orderNumber}");
    }

    private function getCardOrderDetailsString($discount, $details)
    {
        $str = '
        <table border="1" width="100%" style="font-family: arial" cellpadding="2">
            <tr>
                <th width="20">N</th>
                <th width="250">Наименование</th>
                <th width="20">Ед.<br />изм.</th>
                <th width="40">Кол-во</th>
                <th width="40">Цена</th>
                <th width="50">Стоимость</th>
                <th width="40">НДС 20%</th>
                <th width="40">Сумма</th>
            </tr>
            ';

        $counter = 1;
        $totalAmount = 0;

        foreach ($details as $detail)
        {
            $price = $detail['face_value'] * 1000;
            $amount = $price * $detail['count'] * (100 - $discount) / 100;
            $totalAmount += $amount;

            if ($detail['card_type'] == 1)
            {
                $cardName = "Единая карта оплаты SIGMA - {$detail['face_value']} единиц";
            }
            elseif ($detail['card_type'] == 2)
            {
                $cardName = "Карты оплаты Smile.uz - {$price} Сум";
            }

            $str .= "
                <tr>
                    <td width=\"20\">{$counter}</td>
                    <td width=\"250\">{$cardName}</td>
                    <td width=\"20\">шт.</td>
                    <td width=\"40\" align=\"right\">{$detail['count']}</td>
                    <td width=\"40\" align=\"right\">{$price}</td>
                    <td width=\"50\" align=\"right\">{$amount}</td>
                    <td width=\"40\"></td>
                    <td width=\"40\"></td>
                </tr>
            ";
            
            $counter++;
        }

        $totalName = 'Итого к оплате';
        $total1Name = 'Всего к оплате';
        $discountName = '';
        if ($discount)
        {
            $totalName .= ' c учётом скидки';
            $total1Name .= ' c учётом скидки';
            $discountName =  "Скидка: {$discount}%";
        }
        $totalName .= ':';
        $total1Name .= ':';

        $str .= "
            <tr>
                <td width=\"20\"></td>
                <td width=\"250\">{$totalName}</td>
                <td width=\"20\"></td>
                <td width=\"40\"></td>
                <td width=\"40\"></td>
                <td width=\"50\" align=\"right\">{$totalAmount}</td>
                <td width=\"40\"></td>
                <td width=\"40\"></td>
            </tr>
        </table>
        <br />
        <table width=\"100%\">
            <tr>
                <td>{$total1Name} {$totalAmount} сум</td>
                <td>{$discountName}</td>
            </tr>
        </table>
        <br />";

        return $str;
    }

    private function getCardOrderFooterString()
    {
        $clientName = $this->_clientInfo['client_name'];
        $companyName = $this->_companyRekvizits->company_name;
        $companyBoss = $this->_companyRekvizits->gen_name_main;

        $str = "
            <table width=\"100%\" cellpadding=10>
                <tr>
                    <td align=left>
                        Исполнитель<br />
                        Генеральный директор<br />
                        {$companyName}<br />
                        ___________________{$companyBoss}

                    </td>
                    <td align=left>
                        Заказчик<br />
                        {$clientName}<br />
                        ___________________{$contactName}
                    </td>
                </tr>
            </table>
        ";

        return $str;
    }
}