<?php
/**
 * Helper for generate pdf for card contract
 *
 * @author marat
 */

require_once ('PdfHelper.php');

class CardContractPdfHelper extends PdfHelper
{
    public function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }

    public function startPrint($contractNumber, $contractDate, $discounts)
    {

        $this->_pdf->SetFontSize(8);
        $this->_pdf->SetTitle("Договор № {$contractNumber} клиента {$this->_clientInfo['client_name']}");
        $template = $this->getTemplate('card_contract');
        $content = $this->replaceCompanyRekvizits($template);
        $content = $this->replaceClientRekvizits($content);

        $dateAgree = new Zend_Date($contractDate);
        $dateAgree = $dateAgree->toString('"d" MMMM Y года');
        $content = str_replace('{$contractDate}', $dateAgree, $content);
        $content = str_replace('{$contractNumber1}', $contractNumber, $content);

        $discountString = $this->getDiscountString($discounts);
        $content = str_replace('{$discountText}', $discountString, $content);

        $this->_pdf->writeHTML($content);
        $this->sentResponce("card_contract_{$contractNumber}");
    }

    private function getDiscountString($discounts)
    {
        $str = '<table>';

        for ($i = 0 ; $i < count($discounts); $i++)
        {
            if ($i + 1 < count($discounts))
            {
                $nextAmount = 'до ' . ($discounts[$i + 1]['amount'] - 1);
            }
            else
            {
                $nextAmount = 'и выше';
            }

            $str .= "
                <tr>
                    <td>от {$discounts[$i]['amount']} сум</td>
                    <td>{$nextAmount}</td>
                    <td>{$discounts[$i]['discount']}%</td>
                </tr>
            ";
        }

        $str .= '</table>';
        return $str;
    }
}