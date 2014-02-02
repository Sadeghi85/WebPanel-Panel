<?php namespace Libraries\Sadeghi85\Sites;

use \Illuminate\Support\Facades\Config as Config;

class Create {
	
	private static $webpanelRoot;
	private static $sitesHome;
	private static $webDir;
	private static $userShell;

    private static $initialized = false;

    private static function initialize()
    {
    	if (self::$initialized)
    		return;

		self::$webpanelRoot = Config::get('webpanel.webpanelRoot');
        self::$sitesHome = Config::get('webpanel.sitesHome');
		self::$webDir = Config::get('webpanel.webDir');
		self::$userShell = Config::get('webpanel.userShell');
		
    	self::$initialized = true;
    }
	
	public static function formatOutput($output)
	{
		return trim(preg_replace(sprintf('#^\s*(?:%s)?\s*(.*?)(?:%s)?\s*$#is', preg_quote(self::$utilitiesBeginSignature), preg_quote(self::$utilitiesEndSignature)), '$1', implode("\n", $output)));
	}
	
	public static function create($params)
	{
		self::initialize();
		
		$siteServerName = isset($params['siteServerName']) ? $params['siteServerName'] : 'example.com:80';
		$sitePort = isset($params['sitePort']) ? $params['sitePort'] : '80';
		$siteAliases = isset($params['siteAliases']) ? $params['siteAliases'] : array('example.com');
		$siteTag = isset($params['siteTag']) ? $params['siteTag'] : 'web001';
		$siteActivate = isset($params['siteActivate']) ? $params['siteActivate'] : 0;
		
		
		
////////// Step 1: Create the user
		if ( ! \Libraries\Sadeghi85\Sites\Shell::createUser($errorMessage, $siteTag, $siteAliases))
		{
			return array('status' => 1, 'message' => $errorMessage);
		}
////////// \Step 1

////////// Step 2: Create the site and web directory and index.php
		if ( ! \Libraries\Sadeghi85\Sites\Shell::createSiteDir($errorMessage, $siteTag, $siteServerName))
		{
			return array('status' => 1, 'message' => $errorMessage);
		}
////////// \Step 2

////////// Step 3: Creating PHP pool definition
		if ( ! \Libraries\Sadeghi85\Sites\Shell::CreatePHPPool($errorMessage, $siteTag, $siteServerName))
		{
			return array('status' => 1, 'message' => $errorMessage);
		}
////////// \Step 3

////////// Step 4: Creating Apache virtualhost definition
		if ( ! \Libraries\Sadeghi85\Sites\Shell::CreateApacheVhost($errorMessage, $siteTag, $siteServerName, $siteAliases, $sitePort))
		{
			return array('status' => 1, 'message' => $errorMessage);
		}
////////// \Step 4

////////// Step 5: Creating Nginx virtualhost definition
		if ( ! \Libraries\Sadeghi85\Sites\Shell::CreateNginxVhost($errorMessage, $siteTag, $siteServerName, $siteAliases, $sitePort))
		{
			return array('status' => 1, 'message' => $errorMessage);
		}
////////// \Step 5

////////// Step 6: Creating Webalizer definition
		if ( ! \Libraries\Sadeghi85\Sites\Shell::CreateWebalizerConfig($errorMessage, $siteTag, $siteServerName))
		{
			return array('status' => 1, 'message' => $errorMessage);
		}
////////// \Step 6

		dd($siteTag);

				
/////////// Step 10: Activate or deactivate?
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s" 2>&1', self::$utilitiesPath, 'chstatus.sh', $domain, $activate), $output, $returnVal))
		{
			// Can't change status. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t '.($activate ? 'activate' : 'diactivate').' this domain.', 'output' => self::formatOutput($output));
			}
			// Problem. "chstatus.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "chstatus.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Status changed successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 10
		
		
		return array('status' => 0, 'line' => __LINE__, 'message' => '', 'output' => '');
	}


}
