<?php
/**
 * Controller for tech letter register pages
 *
 * @author marat
 */

require_once('BaseController.php');

class TechLetterRegisterController extends BaseController
{
    public function indexAction()
    {
        if ($this->_request->isPost())
        {
            $startDate = $this->_request->getPost('start_date');
            $endDate = $this->_request->getPost('end_date');
            
            $this->view->startDate = $startDate;
            $this->view->endDate = $endDate;
            
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));
            
            $isStatisticalCalculation = $this->_request->getPost('is_statistical_calculation');
            $this->view->isStatisticalCalculation = $isStatisticalCalculation;
            
            $letterRegisterModel = new LettersToAts();
            $letters = $letterRegisterModel->getLetters($startDate, $endDate);

            foreach ($letters as $key => $letter)
            {
                if ($letter['kind'] == LettersToAts::LETTER_KIND_CROSS)
                {
                    $letters[$key]['is_cross'] = 1;
                    $letters[$key]['is_uncross'] = 0;
                }
                elseif ($letter['kind'] == LettersToAts::LETTER_KIND_UNCROSS)
                {
                    $letters[$key]['is_cross'] = 0;
                    $letters[$key]['is_uncross'] = 1;
                }
                else // LettersToAts::LETTER_KIND_RECROSS
                {
                    $letters[$key]['is_cross'] = 1;
                    $letters[$key]['is_uncross'] = 1;
                }
            }
            
            $this->view->letters = $letters;
            
            if ($isStatisticalCalculation)
            {
                $hubInfo = array();
                
                foreach ($letters as $letter)
                {                    
                    $hubInfo[$letter['phone_hub_name']]['cross'] += $letter['is_cross'];
                    $hubInfo[$letter['phone_hub_name']]['uncross'] += $letter['is_uncross'];
                    $hubInfo[$letter['phone_hub_name']]['total'] += $letter['is_cross'] + $letter['is_uncross'];
                }
                
                $this->view->hubLetters = $hubInfo;
            }
        }
    }
}