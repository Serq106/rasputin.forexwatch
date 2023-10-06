<?php
namespace Rasputin\Forexwatch;

class ParserCurrency
{
    public static function agentLaunchingParser()
    {
        self::getCurrency();

        return __METHOD__."();";
    }

    private static function getCurrency(){
        $date = date('d/m/Y'); // Текущая дата

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.cbr.ru/scripts/XML_daily.asp?date_req='.$date);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $out = curl_exec($ch);

        curl_close($ch);


        $content_currency = simplexml_load_string($out);

        foreach ($content_currency->Valute as $valute) {
            $arFields['CODE'] = (string)$valute->CharCode[0];
            $arFields['DATE'] = (string)$content_currency['Date'];
            $arFields['COURSE'] = (string)$valute->Value[0];

            $currency = \Rasputin\Forexwatch\ForexwatchTable::getList(
                [
                    "filter" => [
                        'CODE' => $arFields['CODE'],
                        'DATE' => $arFields['DATE'],
                    ],
                ]
            )->Fetch();

            if(!empty($currency['ID'])){
                \Rasputin\Forexwatch\ForexwatchTable::update($currency['ID'], $arFields);
            } else {
                \Rasputin\Forexwatch\ForexwatchTable::add($arFields);
            }

        }

    }
}