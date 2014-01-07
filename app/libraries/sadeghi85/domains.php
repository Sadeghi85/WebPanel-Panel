<?php namespace Libraries\Sadeghi85;

// use AdminController;
// use View;

class Domains {

	private static $utilitiesPath = '/opt/webpanel/cmd/utilities';
	private static $utilitiesBeginSignature = '*webpanel*';
	private static $utilitiesEndSignature = '@webpanel@';
	
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
		
		$domain = escapeshellcmd(strtolower($domain));
		
		// Check if domain directory already exists
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s/%s"', self::$utilitiesPath, 'file_exists.sh', self::$basePath, $domain), $output, $returnVal))
		{
			if ( ! preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "file_exists.sh" utility.');
			}
		}
		
		if ($returnVal === 0)
		{
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Domain directory already exists. Move it and try again.');
		}
		
		unset($output);
		//
		
		// Create the domain directory
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s/%s"', self::$utilitiesPath, 'mkdir.sh', self::$basePath, $domain), $output, $returnVal))
		{
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t create the Domain directory.');
			}
		
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "mkdir.sh" utility.');
		}
		
		unset($output);
		//
		
		// Create the web directory
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s/%s/%s"', self::$utilitiesPath, 'mkdir.sh', self::$basePath, $domain, self::$webDir), $output, $returnVal))
		{
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t create the web directory.');
			}
			
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "mkdir.sh" utility.');
		}
		
		unset($output);
		//
		
		// PHP user
		$phpUser = 'u-'.substr(str_replace('.', '_', $domain), 0, 30);
		
		// Check if PHP user already exists
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s"', self::$utilitiesPath, 'user_exists.sh', $phpUser), $output, $returnVal))
		{
			// PHP user for this domain doesn't exists
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				unset($output);
				
				// Create the PHP user
				if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s/%s" "%s"', self::$utilitiesPath, 'useradd.sh', $phpUser, self::$basePath, $domain, self::$userShell), $output, $returnVal))
				{
					if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
					{
						return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t create the PHP user for this domain.');
					}
					
					return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "useradd.sh" utility.');
				}
				
				unset($output);
				//
			}
			else
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "user_exists.sh" utility.');
			}
		}
		
		unset($output);
		//
		
		// Correcting permissions on the domain directory
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s" "%s/%s"', self::$utilitiesPath, 'chown.sh', '-R', $phpUser, self::$basePath, $domain), $output, $returnVal))
		{
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t change permissions on the domain directory.');
			}
			
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "chown.sh" utility.');
		}
		
		unset($output);
		
		// 644 for files (initially 644 for all, then 755 for dirs)
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s/%s"', self::$utilitiesPath, 'chmod.sh', '-R 644', self::$basePath, $domain), $output, $returnVal))
		{
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t change permissions on the domain directory.');
			}
			
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "chmod.sh" utility.');
		}
		
		unset($output);
		
		// to give search bit to all directories, effectively 755 for dirs
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s/%s"', self::$utilitiesPath, 'chmod.sh', '-R +X', self::$basePath, $domain), $output, $returnVal))
		{
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t change permissions on the domain directory.');
			}
			
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "chmod.sh" utility.');
		}
		
		unset($output);
		//
		
		// Creating pool definition
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "/etc/php-fpm.d/sites-available/%s.conf"', self::$utilitiesPath, 'file_exists.sh', $domain), $output, $returnVal))
		{
			// Pool definition doesn't exists
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				unset($output);
				
				// Copy pool definition from template
				if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "templates/php-fpm/example.com.conf" "/etc/php-fpm.d/sites-available/%s.conf"', self::$utilitiesPath, 'cp.sh', $domain), $output, $returnVal))
				{
					if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
					{
						return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t copy the pool definition from template.');
					}
					
					return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "cp.sh" utility.');
				}
				
				unset($output);
				
				// Regex replace
				if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "s/example\\\.com/%s/g" "/etc/php-fpm.d/sites-available/%s.conf"', self::$utilitiesPath, 'regex_replace.sh', $domain, $domain), $output, $returnVal))
				{
					if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
					{
						return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t edit the pool definition.');
					}
					
					return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "regex_replace.sh" utility.');
				}
				
				unset($output);
				
				if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "s/^user\\\s\\\+=.*/user = %s/" "/etc/php-fpm.d/sites-available/%s.conf"', self::$utilitiesPath, 'regex_replace.sh', $phpUser, $domain), $output, $returnVal))
				{
					if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
					{
						return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t edit the pool definition.');
					}
					
					return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "regex_replace.sh" utility.');
				}
				
				unset($output);
				
				if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "s/^group\\\s\\\+=.*/group = %s/" "/etc/php-fpm.d/sites-available/%s.conf"', self::$utilitiesPath, 'regex_replace.sh', $phpUser, $domain), $output, $returnVal))
				{
					if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
					{
						return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t edit the pool definition.');
					}
					
					return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "regex_replace.sh" utility.');
				}
				
				unset($output);
				
				if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "../sites-available/%s.conf" "/etc/php-fpm.d/sites-enabled/%s.conf"', self::$utilitiesPath, 'ln.sh', $domain, $domain), $output, $returnVal))
				{
					if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
					{
						return array('status' => '1', 'message' => 'Line '.__LINE__.': Couldn\'t enable the pool.');
					}
					
					return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "ln.sh" utility.');
				}
				
				unset($output);
			}
			
			return array('status' => '1', 'message' => 'Line '.__LINE__.': Critical error in executing "file_exists.sh" utility.');
		}
		//
		
		
		
		
	}


}
