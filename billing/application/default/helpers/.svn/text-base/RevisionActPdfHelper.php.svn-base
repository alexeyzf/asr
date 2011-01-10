<?php
/**
 * Helper
 */
require_once ('PdfHelper.php');
require_once ('CurrencyToWord.php');

class RevisionActPdfHelper extends PdfHelper
{
    public function __construct($companyRekvizits)
    {
        parent::__construct($companyRekvizits);
    }

    public function getPdf($year, $ballanceUsd, $ballanceUzs, $transactions, $invoices, $startMonth)
    {
    	$this->_pdf->setPrintFooter(false);
    	$this->_pdf->SetFontSize(7);
    	$html = $this->getHtml($year, $ballanceUsd, $ballanceUzs, $transactions, $invoices, $startMonth);
    	$this->_pdf->writeHtml($html);
    	$this->sentResponce("revision-act");
    }

    public function getHtml($year, $ballanceUsd, $ballanceUzs, $transactions, $invoices, $startMonth)
    {
    	$template = $this->getTemplate('revision_act');
    	$content = $this->replaceClientRekvizits($template);
    	$content = $this->replaceCompanyRekvizits($content);
    	$content = str_replace('{$currentDate}', date('d.m.Y'), $content);
    	$html = $content;
    	$html .= $this->getHead($year, $ballanceUsd, $ballanceUzs);
    	$html .= $this->getBody($year, $transactions, $invoices);
    	$html .= $this->getBottom($year, $ballanceUsd, $ballanceUzs, $startMonth);

    	$html .= '
    	<table style="font-size:26px">
    		<tr>
    			<td width="50px" align="center">
    			</td>
    			<td width="150px" align="center">
    				<b style="font-family:arialbd">ООО "' . $this->_companyRekvizits->company_name . '"</b>
    				<br />
    				______________________
    			</td>
    			<td width="100px" align="center">
    			</td>
    			<td width="150px" align="center">
    				<b style="font-family:arialbd">' . $this->_clientInfo['client_name'] . '</b>
    				<br />
    				______________________
    			</td>
    		</tr>
    	</table>
    	';
    	return $html;
    }

    private function getHead($year, $ballanceUsd, $ballanceUzs)
    {
    	if ($ballanceUsd < 0)
    	{
    		$this->ourTotalDebitUsd = -$ballanceUsd;
    		$this->ourTotalCreditUsd = 0;
    		$this->clientTotalDebitUsd = 0;
    		$this->clientTotalCreditUsd = -$ballanceUsd;

    		$this->ourTotalDebitUzs = -$ballanceUzs;
    		$this->ourTotalCreditUzs = 0;
    		$this->clientTotalDebitUzs = 0;
    		$this->clientTotalCreditUzs = -$ballanceUzs;
    	}
    	else
    	{
    		$this->ourTotalDebitUsd = 0;
    		$this->ourTotalCreditUsd = $ballanceUsd;
    		$this->clientTotalDebitUsd = $ballanceUsd;
    		$this->clientTotalCreditUsd = 0;

    		$this->ourTotalDebitUzs = 0;
    		$this->ourTotalCreditUzs = $ballanceUzs;
    		$this->clientTotalDebitUzs = $ballanceUzs;
    		$this->clientTotalCreditUzs = 0;
    	}

    	$html = '
    		<table border="1" width="100%" style="font-family: arial" cellpadding="2">
            	<tr>
            		<td rowspan="2" align="center" width="50px">Дата</td>
            		<td rowspan="2" align="center" width="175px">Операции</td>
            		<td colspan="2" width="150px">' . $this->_companyRekvizits->company_name . '</td>
            		<td colspan="2" width="150px">' . $this->_clientInfo['client_name'] . '</td>
            	</tr>
            	<tr>
            		<td align="center" width="75px">Дебет</td>
            		<td align="center" width="75px">Кредит</td>
            		<td align="center" width="75px">Дебет</td>
            		<td align="center" width="75px">Кредит</td>
            	</tr>
            	<tr>
            		<td colspan="2" align="right" width="225px">Сальдо на 01.01.' . $year . '</td>
            		<td align="right" width="75px">' . number_format($this->ourTotalDebitUzs, 2, ',', ' ') . '</td>
            		<td align="right" width="75px">' . number_format($this->ourTotalCreditUzs, 2, ',', ' ') . '</td>
            		<td align="right" width="75px">' . number_format($this->clientTotalDebitUzs, 2, ',', ' ') . '</td>
            		<td align="right" width="75px">' . number_format($this->clientTotalCreditUzs, 2, ',', ' ') . '</td>
            	</tr>
            	<tr>
                        <td colspan="2" align="right" width="225px">В валюте USD</td>
            		<td align="right" width="75px">' . number_format($this->ourTotalDebitUsd, 2, ',', ' ') . '</td>
            		<td align="right" width="75px">' . number_format($this->ourTotalCreditUsd, 2, ',', ' ') . '</td>
            		<td align="right" width="75px">' . number_format($this->clientTotalDebitUsd, 2, ',', ' ') . '</td>
            		<td align="right" width="75px">' . number_format($this->clientTotalCreditUsd, 2, ',', ' ') . '</td>
            	</tr>
    	';

    	$this->ourDUsd = $this->ourTotalDebitUsd;
    	$this->ourCUsd = $this->ourTotalCreditUsd;
    	$this->clientDUsd = $this->clientTotalDebitUsd;
    	$this->clientCUsd = $this->clientTotalCreditUsd;

		$this->ourDUzs = $this->ourTotalDebitUzs;
		$this->ourCUzs = $this->ourTotalCreditUzs;
		$this->clientDUzs = $this->clientTotalDebitUzs;
		$this->clientCUzs = $this->clientTotalCreditUzs;
		//////////////////////////////////////////////////////////////

		$this->ourTotalDebitUsd = 0;
		$this->ourTotalCreditUsd = 0;
		$this->clientTotalDebitUsd = 0;
		$this->clientTotalCreditUsd = 0;

	    $this->ourTotalDebitUzs = 0;
		$this->ourTotalCreditUzs = 0;
		$this->clientTotalDebitUzs = 0;
		$this->clientTotalCreditUzs = 0;
    	return $html;
    }

    private $ourTotalDebitUzs;
    private $ourTotalCreditUzs;
    private $clientTotalDebitUzs;
    private $clientTotalCreditUzs;

    private $ourTotalDebitUsd;
    private $ourTotalCreditUsd;
    private $clientTotalDebitUsd;
    private $clientTotalCreditUsd;

    private $clientEndBallanceUsd;
    private $clientEndBallanceUzs;

    public function getClientEndBallanceUsd()
    {
    	return $this->clientEndBallanceUsd;
    }

	public function getClientEndBallanceUzs()
    {
    	return $this->clientEndBallanceUzs;
    }

    private function getBody($year, $transactions, $invoices)
    {
    	$html = '';
    	$month = 1;
    	foreach ($transactions as $transaction)
    	{

    		$curMonth = intval(date('m', strtotime($transaction['currenttime'])));

    		if ($curMonth > $month) // если пошел новый месяц -
    		//нужно вывести сумму по счет-фактуре за предыдущий месяц
    		{
    			$html .= '
    				<tr>
    					<td rowspan="2" width="50px">' . date('t.m.Y', strtotime("01.{$month}.{$year}")) . '</td>
    					<td width="175px">Оказание услуг ' . $invoices[$month]['number'] . '; Акт выполненных работ</td>
    					<td align="right" width="75px">' . number_format($invoices[$month]['amount_uzs'], 2, ',', ' ') . '</td>
    					<td align="right" width="75px">0.00</td>
    					<td align="right" width="75px">0.00</td>
    					<td align="right" width="75px">' . number_format($invoices[$month]['amount_uzs'], 2, ',', ' ') . '</td>
    				</tr>
    				<tr>
    					<td align="right" width="175px">В валюте USD</td>
    					<td align="right" width="75px">' . number_format($invoices[$month]['amount_usd'], 2, ',', ' ') . '</td>
    					<td align="right" width="75px">0.00</td>
    					<td align="right" width="75px">0.00</td>
    					<td align="right" width="75px">' . number_format($invoices[$month]['amount_usd'], 2, ',', ' ') . '</td>
    				</tr>
    			';

    			$this->ourTotalDebitUzs += $invoices[$month]['amount_uzs'];
    			$this->ourTotalDebitUsd += $invoices[$month]['amount_usd'];
    			$this->clientTotalCreditUzs += $invoices[$month]['amount_uzs'];
    			$this->clientTotalCreditUsd += $invoices[$month]['amount_usd'];

    			$month = $curMonth;
    		}

    		if ($transaction['trantype'] < 100) // если зачисления берем из транзакций
    		{
    			$html .= '
    				<tr>
    					<td rowspan="2" width="50px">' . date('d.m.Y', strtotime($transaction['currenttime'])) . '</td>
    					<td width="175px">Выписка за услуги</td>
    					<td align="right" width="75px">0.00</td>
    					<td align="right" width="75px">' . number_format($transaction['summas'], 2, ',', ' ') . '</td>
    					<td align="right" width="75px">' . number_format($transaction['summas'], 2, ',', ' ') . '</td>
    					<td align="right" width="75px">0.00</td>
    				</tr>
    				<tr>
    					<td align="right" width="175px">В валюте USD</td>
    					<td align="right" width="75px">0.00</td>
    					<td align="right" width="75px">' . number_format($transaction['summa'], 2, ',', ' ') . '</td>
    					<td align="right" width="75px">' . number_format($transaction['summa'], 2, ',', ' ') . '</td>
    					<td align="right" width="75px">0.00</td>
    				</tr>';

    			$this->ourTotalCreditUzs += $transaction['summas'];
    			$this->ourTotalCreditUsd += $transaction['summa'];
    			$this->clientTotalDebitUzs += $transaction['summas'];
    			$this->clientTotalDebitUsd += $transaction['summa'];
    		}
    	}

    	//сумма по счет-фактуре за последний месяц
    	$month = $curMonth;
    	$html .= '
    		<tr>
    			<td rowspan="2" width="50px">' . date('t.m.Y', strtotime("01.{$month}.{$year}")) . '</td>
    			<td width="175px">Оказание услуг ' . $invoices[$month]['number'] . '; Акт выполненных работ</td>
    			<td align="right" width="75px">' . number_format($invoices[$month]['amount_uzs'], 2, ',', ' ') . '</td>
    			<td align="right" width="75px">0.00</td>
    			<td align="right" width="75px">0.00</td>
    			<td align="right" width="75px">' . number_format($invoices[$month]['amount_uzs'], 2, ',', ' ') . '</td>
    		</tr>
    		<tr>
    			<td align="right" width="175px">В валюте USD</td>
    			<td align="right" width="75px">' . number_format($invoices[$month]['amount_usd'], 2, ',', ' ') . '</td>
    			<td align="right" width="75px">0.00</td>
    			<td align="right" width="75px">0.00</td>
    			<td align="right" width="75px">' . number_format($invoices[$month]['amount_usd'], 2, ',', ' ') . '</td>
    		</tr>
    	';

    	$this->ourTotalDebitUzs += $invoices[$month]['amount_uzs'];
    	$this->ourTotalDebitUsd += $invoices[$month]['amount_usd'];
    	$this->clientTotalCreditUzs += $invoices[$month]['amount_uzs'];
    	$this->clientTotalCreditUsd += $invoices[$month]['amount_usd'];

    	return $html;
    }

    private function getBottom($year, $ballanceUsd, $ballanceUzs, $startMonth)
    {
    	$this->clientEndBallanceUsd = $ballanceUsd = $this->clientTotalDebitUsd - $this->clientTotalCreditUsd;
    	$this->clientEndBallanceUzs = $ballanceUzs = $this->clientTotalDebitUzs - $this->clientTotalCreditUzs;



	if ($ballanceUzs < 0)
    	{
    		$ourDebitUzs = -$ballanceUzs;
    		$ourDebitUsd = -$ballanceUsd;
    	}
    	else
    	{
    		$ourCreditUzs = $ballanceUzs;
    		$ourCreditUsd = $ballanceUsd;
    	}




		$date = $year. "-". $startMonth. "-"."01";

    	$daysInMonth = date('t', strtotime($date));

    	$todayMonth = date(''.$daysInMonth.'.'.$startMonth.'.Y');

        $BcreditUZS = $this->ourCUzs + $this->ourTotalCreditUzs - $this->ourTotalDebitUzs;
        $BdebitUZS  = $this->ourDUzs + $this->ourTotalDebitUzs - $this->ourTotalDebitUzs;

        $BcreditUSD = $this->ourCUsd + $this->ourTotalCreditUsd - $this->ourTotalDebitUsd;
        $BdebitUSD  = $this->ourDUsd + $this->ourTotalDebitUsd - $this->ourTotalDebitUsd;

// Переоценка
        $ratesModel = new Rates();
        $rate = $ratesModel->getRateNow();

        $currencyRevaluationCredit = ($BcreditUSD * $rate) - $BcreditUZS;
        $currencyRevaluationDebit = ($BdebitUSD * $rate) - $BdebitUZS;
// END переоценка

		// переоценка валюты
		$html .= '
		<tr>
			<td rowspan="2" width="50px">' . $todayMonth . '</td>
			<td width="175px">Переоценка валюты</td>
			<td align="right" width="75px">'.number_format($currencyRevaluationDebit, 2, ',', ' ').'</td>
			<td align="right" width="75px">' .number_format($currencyRevaluationCredit, 2, ',', ' '). '</td>
			<td align="right" width="75px">' .number_format($currencyRevaluationCredit, 2, ',', ' '). '</td>
			<td align="right" width="75px">'.number_format($currencyRevaluationDebit, 2, ',', ' ').'</td>
		</tr>
		<tr>
			<td align="right" width="175px">В валюте USD</td>
			<td align="right" width="75px">0.00</td>
			<td align="right" width="75px">0.00</td>
			<td align="right" width="75px">0.00</td>
			<td align="right" width="75px">0.00</td>
		</tr>';


    	$html .= '
    		<tr>
    			<td colspan="2" align="right" width="225px">Обороты за период</td>
    			<td align="right" width="75px">' . number_format($this->ourTotalDebitUzs, 2, ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($this->ourTotalCreditUzs, 2, ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($this->clientTotalDebitUzs, 2 , ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($this->clientTotalCreditUzs, 2, ',', ' ') . '</td>
    		</tr>
    	';

        // Сальдо с учетом переоценки валюты
            $BdebitUZS  = $BdebitUZS + $currencyRevaluationDebit;
            $BcreditUZS = $BcreditUZS + $currencyRevaluationCredit;
        //

    	$html .= '
    		<tr>
    			<td colspan="2" align="right" width="225px">Сальдо на ' . $todayMonth . '</td>
    			<td align="right" width="75px">' . number_format($BdebitUZS, 2, ',', ' ')  .'</td>
    			<td align="right" width="75px">' . number_format($BcreditUZS, 2, ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($BcreditUZS, 2, ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($BdebitUZS, 2, ',', ' ')  .'</td>
    		</tr>
    		<tr>
    			<td colspan="2" align="right" width="225px">В валюте USD</td>
    			<td align="right" width="75px">' . number_format($BdebitUSD, 2, ',', ' ')  .'</td>
    			<td align="right" width="75px">' . number_format($BcreditUSD, 2, ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($BcreditUSD, 2, ',', ' ') . '</td>
    			<td align="right" width="75px">' . number_format($BdebitUSD, 2, ',', ' ')  .'</td>
    		</tr>
    	</table>
    	';

    	if ($BdebitUZS > $BcreditUZS)
    	{
    		$inFavor = $this->_companyRekvizits->company_name;
    		$amountUzs = $BdebitUZS - $BcreditUZS;
    		$amountUsd = $BdebitUSD - $BcreditUSD;
    	}
    	else
    	{
    		$inFavor = $this->_clientInfo['client_name'];
    		$amountUzs = $BcreditUZS - $BdebitUZS;
    		$amountUsd = $BcreditUSD - $BdebitUSD;
    	}



    	$amountUzs = round($amountUzs, 2);
    	$amountUsd = round($amountUsd, 2);

    	$wordUzs = CurrencyToWord::num2str($amountUzs, 'UZS');
    	$wordUsd = CurrencyToWord::num2str($amountUsd, 'USD');

    	$html .= "
	    	<br />
	    	В пользу {$inFavor} {$amountUzs} ({$wordUzs}) в валюте USD {$amountUsd} ({$wordUsd})
	    	<br />
	    	<br />
	    	<br />
	    	<br />
    	";

    	return $html;
    }
}