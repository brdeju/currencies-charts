<?php

namespace Brdeju\Bundle\CurrenciesChartsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use SoapClient;
use SoapHeader;
use DateTime;

class CurrenciesController extends Controller {
    const historical_rates_link = 'http://globalcurrencies.xignite.com/xGlobalCurrencies.json/GetHistoricalRatesRanges';
    
    /**
     * @Route("/currencies/{currency}", name="currencies", 
     *      defaults={"currency"=NULL}, requirements={"currency": "usd|pln|eur|gbp"})
     * @Template()
     */
    public function currenciesAction(Request $request, $currency = null) {
        $currencies = array(
            'usd', 'pln', 'eur', 'gbp'
        );
        
        return array(
            'currencies' => $currencies,
            'active_item' => $currency
        );
    }

    /**
     * @Route("/currencies/{currency}/chart", name="currency_chart", 
     *      defaults={"currency"="pln"}, requirements={"currency": "usd|pln|eur|gbp"})
     * @Template("CurrenciesChartsBundle:Currencies:currency_chart.html.twig")
     */
    public function currencyChartAction(Request $request, $currency = 'pln') {
        $currencies = array(
            'usd', 'pln', 'eur', 'gbp'
        );
        
        return array(
            'currency' => $currency
        );
    }

    /**
     * @Route("/currencies/{currency}/data", name="currency_data", 
     *      defaults={"currency"="pln"}, requirements={"currency": "usd|pln|eur|gbp"})
     */
    public function currencyDataAction(Request $request, $currency = 'pln') {
        $translator = $this->container->get("translator");
        $get = $request->query->all();
        $response = array();
        $xignite_api_key = $this->container->getParameter('xignite.api_key');
        $xignite_soap_client_url = $this->container->getParameter('xignite.soap_client_url');
        $xignite_authenticate_url = $this->container->getParameter('xignite.authenticate_url');
        
        $params = $this->parseQueryStringToRatesRequest($currency, $get);                
        $client = new SoapClient($xignite_soap_client_url);
        $xignite_header = new SoapHeader($xignite_authenticate_url, "Header", array("Username" => $xignite_api_key));
        $client->__setSoapHeaders(array($xignite_header));
        $result = $client->GetHistoricalRatesRanges($params);
        
        $response['tickInterval'] = $params['PeriodType'];
        $response['title'] = $translator->trans( 'currencies.rates.'.$params['Symbols']);
        if(is_soap_fault($result) || (!is_array($result->GetHistoricalRatesRangesResult->HistoricalRate) && $result->GetHistoricalRatesRangesResult->HistoricalRate->Outcome === "RequestError") ) {
            $response['result'] = $result;
            $response['error'] = true;
        } else {
            $response['result'] = $this->parseHistoricalRates($result);
            $response['error'] = false;
        }
        
        return new JsonResponse($response);
    }

    
    private function parseQueryStringToRatesRequest($currency, $get) {
        $params = array();
        if( isset($get['base']) && !empty($get['base']) ) {
            $params['Symbols'] = $get['base'].strtoupper($currency);
        } else {
            $params['Symbols'] = 'PLN'.strtoupper($currency);
        }
        if( isset($get['start_date']) && !empty($get['start_date']) ) {
            $params['StartDate'] = $get['start_date'];
        } else {
            $date = strtotime("-7 day", time());
            $params['StartDate'] = date('m/d/Y', $date);
        }
        if( isset($get['end_date']) && !empty($get['end_date']) ) {
            $params['EndDate'] = $get['end_date'];
        } else {
            $params['EndDate'] = date('m/d/Y', time());
        }
        if( isset($get['fixing_time']) ) {
            $params['FixingTime'] = $get['fixing_time'];
        } else {
            $params['FixingTime'] = "00:00";
        }
        if( isset($get['period_type']) 
                && in_array($get['period_type'],array('Daily', 'Weekly', 'Monthly', 'Quarterly', 'Yearly')) ) {
            $params['PeriodType'] = $get['period_type'];
        } else {
            $params['PeriodType'] = 'Daily'; 
        }
        if( isset($get['price_type']) ) {
            $params['PriceType'] = $get['price_type'];
        } else {
            $params['PriceType'] = 'Mid';
        }
        return $params;
    }
    
    private function parseHistoricalRates($xignite_response) {
        $historical_rates = $xignite_response->GetHistoricalRatesRangesResult->HistoricalRate;
        $rates_data = array();
        if(is_array($historical_rates) ) {
            for($i=0,$len=count($historical_rates);$i<$len;$i++) {
                $this->parseRow($rates_data, $historical_rates[$i]);
            }
        } else{
            $this->parseRow($rates_data, $historical_rates);
        }
        return $rates_data;
    }
    private function parseRow(&$rates_data, $row) {
        $base_currency = $row->BaseCurrency;
        $quote_currency = $row->QuoteCurrency;
        $start_date = $row->StartDate;
        $end_date = $row->EndDate;
        $first_rate = $row->Open;
        $last_rate = $row->Close;
        $high_rate = $row->High;
        $low_rate = $row->Low;
        $average_rate = $row->Average;
        $start_date = DateTime::createFromFormat('m/d/Y', $start_date)->getTimestamp();
        $end_date = DateTime::createFromFormat('m/d/Y', $end_date)->getTimestamp();

        if( !isset($rates_data[$quote_currency]) ) {
            $rates_data[$quote_currency] = array();
        }
        
        $rates_data[$quote_currency][$end_date] = $rates_data[$quote_currency][$start_date] = array(
            'first_rate' => $first_rate,
            'last_rate' => $last_rate,
            'high_rate' => $high_rate,
            'low_rate' => $low_rate,
            'average_rate' => $average_rate,
        );
        
        return $rates_data;
    }
}
