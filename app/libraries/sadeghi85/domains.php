<?php namespace Libraries\Sadeghi85;

// use AdminController;
// use View;

class Domains {

	private static $utilitiesPath = '/opt/webpanel/cmd/utilities';
	private static $utilitiesSignature = '*webpanel*';
	
	private static $basePath = '/var/www/WebPanel';
	private static $webDir = 'web';
	private static $userShell = '/sbin/nologin';
	
	
	
    private static $initialized = false;

    private static function initialize()
    {
    	if (self::$initialized)
    		return;

        //self::$memInfo = `sudo cat /proc/meminfo`;
    	self::$initialized = true;
    }
	
	public static function create($domain)
	{
		self::initialize();
		
		$domain = escapeshellarg(strtolower($domain));
		
		// Check if domain directory already exists
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s/%s', self::$utilitiesPath, 'dir_exists.sh', self::$basePath, $domain), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "dir_exists.sh" utility.');
		}
		
		if ($returnVal === 0)
		{
			return array('status' => '1', 'message' => 'Domain directory already exists. Move it and try again.');
		}
		//
		
		// Create the domain directory
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s/%s', self::$utilitiesPath, 'mkdir.sh', self::$basePath, $domain), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "mkdir.sh" utility.');
		}
		
		if ($returnVal === 1)
		{
			return array('status' => '1', 'message' => 'Couldn\'t create the Domain directory.');
		}
		//
		
		// Create the web directory
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s/%s/%s', self::$utilitiesPath, 'mkdir.sh', self::$basePath, $domain, $webDir), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "mkdir.sh" utility.');
		}
		
		if ($returnVal === 1)
		{
			return array('status' => '1', 'message' => 'Couldn\'t create the web directory.');
		}
		//
		
		// PHP user
		$phpUser = 'u-'.substr(str_replace('.', '_', $domain), 0, 30);
		
		// Check if PHP user already exists
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s', self::$utilitiesPath, 'user_exists.sh', $phpUser), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "user_exists.sh" utility.');
		}
		
		// PHP user for this domain doesn't exists
		if ($returnVal === 1)
		{
			// Create the PHP user
			if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s %s/%s %s', self::$utilitiesPath, 'useradd.sh', $phpUser, self::$basePath, $domain, self::$userShell), $output, $returnVal))
			{
				return array('status' => '1', 'message' => 'Critical error in executing "useradd.sh" utility.');
			}
			
			if ($returnVal === 1)
			{
				return array('status' => '1', 'message' => 'Couldn\'t create the PHP user for this domain.');
			}
			//
		}
		//
		
		// Correcting permissions on the domain directory
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s %s %s/%s', self::$utilitiesPath, 'chown.sh', '-R', $phpUser, self::$basePath, $domain), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "chown.sh" utility.');
		}
		
		if ($returnVal === 1)
		{
			return array('status' => '1', 'message' => 'Couldn\'t change permissions on the domain directory.');
		}
		
		// 644 for files (initially 644 for all, then 755 for dirs)
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s %s/%s', self::$utilitiesPath, 'chmod.sh', '-R 644', self::$basePath, $domain), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "chmod.sh" utility.');
		}
		
		if ($returnVal === 1)
		{
			return array('status' => '1', 'message' => 'Couldn\'t change permissions on the domain directory.');
		}
		
		// to give search bit to all directories, effectively 755 for dirs
		if (self::$utilitiesSignature != exec(sprintf('sudo sh %s/%s %s %s/%s', self::$utilitiesPath, 'chmod.sh', '-R +X', self::$basePath, $domain), $output, $returnVal))
		{
			return array('status' => '1', 'message' => 'Critical error in executing "chmod.sh" utility.');
		}
		
		if ($returnVal === 1)
		{
			return array('status' => '1', 'message' => 'Couldn\'t change permissions on the domain directory.');
		}
		//
		
		
		
		
		
		
	}


}
