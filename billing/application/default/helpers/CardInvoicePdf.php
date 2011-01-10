<?php
/**
 * Helper for generate pdf for card invoice
 *
 * @author marat
 */

require_once ('PdfHelper.php');

class CardInvoicePdfHelper extends PdfHelper
{
    function __construct ($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }

    public function startPrint($contractNumber, $contractDate, $invoiceNumber, $orderDiscount, $orderDetails, $order_date)
    {

        $this->_pdf->SetTitle("Счет-факутра-накладная №{$invoiceNumber} клиента {$this->_clientInfo['client_name']}");
        $this->_pdf->SetFontSize(8);
        $template = $this->getTemplate('card_invoice');
        $content = $this->replaceCompanyRekvizits($template);

        $dateAgree = new Zend_Date($contractDate);
        $dateAgree = $dateAgree->toString('dd.MM.Y года');


        $content = str_replace('{$contractDate}', $dateAgree, $content);
        $content = str_replace('{$contractNumber}', $contractNumber, $content);
        $content = str_replace('{$invoiceNumber}', $invoiceNumber, $content);

        //$currentDate = new Zend_Date();
        //$currentDate = $currentDate->toString('dd.M.Y года');

        //$currentDate = date('d.m.Y');
        $content = str_replace('{$currentDate}', date('d.m.Y', strtotime($order_date)), $content);

        $content = $this->replaceClientRekvizits($content);
        $this->_pdf->writeHTML($content);

        $detailsContent = $this->getCardInvoiceDetailsString($orderDiscount, $orderDetails);
        $this->_pdf->writeHTML($detailsContent);

        $footerContent = $this->getCardInvoiceFooterString();
        $this->_pdf->writeHTML($footerContent);

        $this->_pdf->writeHTML('<hr>');
        $this->_pdf->writeHTML($content);
        $this->_pdf->writeHTML($detailsContent);
        $this->_pdf->writeHTML($footerContent);

        $this->_pdf->AddPage();
        $this->_pdf->writeHTML($content);
        $this->_pdf->writeHTML($detailsContent);
        $this->_pdf->writeHTML($footerContent);

        $this->sentResponce("invoice_{$invoiceNumber}");
    }

    private function getCardInvoiceDetailsString($discount, $details)
    {
        $str = '
        <table border="1" width="100%" style="font-family: arial" cellpadding=2>
            <tr>
                <th width="160" rowspan=2>Наименование товаров ( работ,услуг )</th>
                <th width="20" rowspan=2>Ед.<br />изм.</th>
                <th width="40" rowspan=2>Кол-во</th>
                <th width="40" rowspan=2>Цена</th>
                <th width="50" rowspan=2>Стоимость</th>
                <th width="70" colspan=2>Акцизный налог</th>
                <th width="70" colspan=2>НДС</th>
                <th width="80" rowspan=2>Стоимость поставки c учётом НДС</th>
            </tr>
            <tr>
                <th width="35">Ставка</th>
                <th width="35">Сумма</th>
                <th width="35">Ставка</th>
                <th width="35">Сумма</th>
            </tr>
            ';

        $counter = 1;
        $totalAmount = 0;

        foreach ($details as $detail)
        {
            $price 			   = $detail['face_value'] * 1000;
            $amount 		   = $price * $detail['count'] * (100 - $discount) / 100;
            $priceWithDiscount = $price * 1 * (100 - $discount) / 100;
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
                    <td width=\"160\">{$cardName}</td>
                    <td width=\"20\">шт.</td>
                    <td width=\"40\">{$detail['count']}</td>
                    <td width=\"40\">{$priceWithDiscount}</td>
                    <td width=\"50\">{$amount}</td>
                    <td width=\"70\" colspan=2>Без акцизного</td>
                    <td width=\"70\" colspan=2>Без НДС</td>
                    <td width=\"80\"></td>
                </tr>
            ";
        }
		$wordAmount = CurrencyToWord::num2str($totalAmount, 'UZS');

        $str .= "
            <tr>
                <td width=\"160\">Всего к оплате:</td>
                <td width=\"20\"></td>
                <td width=\"40\"></td>
                <td width=\"40\"></td>
                <td width=\"50\">{$totalAmount}</td>
                <td width=\"70\" colspan=2></td>
                <td width=\"70\" colspan=2></td>
                <td width=\"80\"></td>
            </tr>
        </table>
        <table width=\"100%\">
            <tr>
                <td>Всего отпущено на сумму: {$wordAmount} </td>
            </tr>
        </table>";

        return $str;
    }

    private function getCardInvoiceFooterString()
    {
        $clientName = $this->_clientInfo['client_name'];
        $companyName = $this->_companyRekvizits->company_name;
        $companyBoss = $this->_companyRekvizits->gen_name_main;
        $companyHeadAcc = $this->_companyRekvizits->head_accounting_department;

        $str = "
            <table width=\"100%\">
                <tr>
                    <td align=\"left\">
                        Руководитель:_______________________{$companyBoss}<br /><br />
                        Главный бухгалтер:__________________{$companyHeadAcc}<br /><br />
                        М.П.<br /><br />
                        Отпустил:___________________________<br />
                        <sup>(подпись ответственного лица от Поставщика)</sup>
                    </td>
                    <td align=\"left\">
                        Получил:_______________________________________________<br />
                        <sup>(подпись покупателя или уполномоченного представителя)</sup><br /><br />
                        По доверенности<br />
                        N ___________ от \"___________\" 20___г.<br /><br />
                        ________________________________________________________<br />
                        <sup>(Ф.И.О. получателя)</sup>
                    </td>
                </tr>
            </table>
        ";

        return $str;
    }
}