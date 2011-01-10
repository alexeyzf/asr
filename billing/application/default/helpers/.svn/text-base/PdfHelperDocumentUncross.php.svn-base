<?php
require_once ('Zend/tcpdf/tcpdf.php');

class PdfHelperDocumentUncross
{
	public function replaceData($dataR, $pdf, $letterID)
	{

		$template = file_get_contents(realpath("../application/default/views/templates/mass_uncross.txt"));

		$now = date("d.m.Y");

		// Тут замена
		$template = str_replace('{#number}', $letterID, $template);
		$template = str_replace('{#phone_hub_case_name}', $dataR[0]['case_name'], $template);
		$template = str_replace('{#phone_hub_case_director}', $dataR[0]['case_director'], $template);
		$template = str_replace('{#phone_hub_director}', $dataR[0]['director'], $template);
		$template = str_replace('{#pcross}', $dataR[0]['pcross'], $template);
		$template = str_replace('{#frame_number}', $dataR[0]['frame_number'], $template);
		$template = str_replace('{#line_number1}', $dataR[0]['line_number1'], $template);
		$template = str_replace('{#line_number2}', $dataR[0]['line_number2'], $template);
		$template = str_replace('{#ats_name}', $dataR[0]['ats_name'], $template);
		$template = str_replace('{#datetime}', $now, $template);


        $pdf->writeHTML($template, true, 0, true, 0);
        $pdf->AddPage();
        $template = "";
	}
}