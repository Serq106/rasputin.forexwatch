<?
namespace Rasputin\Forexwatch;

class Tools{

	public static function GetAdminElementEditLink($ELEMENT_ID, $arParams = array(), $strAdd = "")
    {
		$url = "rasputin_forexwatch_edit.php";

		if($ELEMENT_ID !== null){
            $url.= "?ID=".intval($ELEMENT_ID);
        } else {
            return false;
        }

		$url.= "&lang=".urlencode(LANGUAGE_ID);

		foreach ($arParams as $name => $value){
            if (isset($value)){
                $url.= "&".urlencode($name)."=".urlencode($value);
            }
        }
		
		return $url.$strAdd;
    }

}