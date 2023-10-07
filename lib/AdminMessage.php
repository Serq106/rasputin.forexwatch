<?php

namespace Rasputin\Forexwatch;

class AdminMessage
{
	private static $var_sess = 'rasputin.forexwatch';

	public static function show($msg = NULL)
	{
		if($msg != NULL) static::addMsg($msg, 'true');

		$arMsgs = $_SESSION[static::$var_sess];

		if(!empty($arMsgs) && is_array($arMsgs))
		{
			foreach($arMsgs as $type => $msgs)
			{
				$msg = implode("<br>", $msgs);
				switch($type)
				{
					case "true":
						$admin_type = "OK";
						break;
					case "false":
						$admin_type = "ERROR";
						break;
					case "info":
						$admin_type = "PROGRESS";
						break;
					default:
						$admin_type = "OK";
						break;
				}

				\CAdminMessage::ShowMessage(["MESSAGE" => $msg, "TYPE" => $admin_type, "HTML" => true]);
				unset($arMsgs[$type]);
			}
		}

		$_SESSION[static::$var_sess] = $arMsgs;
	}

	public static function addMsg($msg, $type = 'true')
	{
		$arMsgs = is_array($_SESSION[static::$var_sess]) ? $_SESSION[static::$var_sess]:[];

		if($msg != NULL && in_array($type, ['info', 'true', 'false']))
		{
			$arMsgs[$type][] = $msg;
		}

		$_SESSION[static::$var_sess] = $arMsgs;
	}
}