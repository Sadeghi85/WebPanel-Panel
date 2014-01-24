<?php namespace Libraries\Sadeghi85\Domains;

// use AdminController;
// use View;

class Create {

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
	
	public static function formatOutput($output)
	{
		return trim(preg_replace(sprintf('#^\s*(?:%s)?\s*(.*?)(?:%s)?\s*$#is', preg_quote(self::$utilitiesBeginSignature), preg_quote(self::$utilitiesEndSignature)), '$1', implode("\n", $output)));
	}
	
	public static function create($params)
	{
		self::initialize();
		
		$domain = isset($params['domain']) ? $params['domain'] : 'example.com';
		$ipPort = isset($params['ipPort']) ? $params['ipPort'] : '127.0.0.1:80';
		$alias = isset($params['alias']) ? $params['alias'] : '[]';
		$activate = isset($params['activate']) ? $params['activate'] : 0;
		
		$domain = escapeshellcmd(strtolower($domain));
		
////////// Step 1: Check if domain directory already exists
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s/%s" 2>&1', self::$utilitiesPath, 'file_exists.sh', self::$basePath, $domain), $output, $returnVal))
		{
			// Domain directory doesn't exist. Will create it in the next step.
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				
			}
			// Problem. "file_exists.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "file_exists.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Domain directory exists.
		else
		{
			return array('status' => 1, 'line' => __LINE__, 'message' => 'Domain directory already exists. Move it and try again.', 'output' => self::formatOutput($output));
		}
		
		unset($output);
////////// \Step 1
		
		// PHP user
		$phpUser = 'u-'.substr(str_replace('.', '_', $domain), 0, 30);
		
////////// Step 2: Create the PHP user
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s/%s" "%s" "%s" 2>&1', self::$utilitiesPath, 'useradd.sh', self::$basePath, $domain, self::$userShell, $phpUser), $output, $returnVal))
		{
			// Can't create PHP user. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t create the PHP user for this domain.', 'output' => self::formatOutput($output));
			}
			// Problem. "useradd.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "useradd.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// PHP user is created successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 2

/////////// Step 3: Create the domain, web directory and index.php
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s/%s/%s" "%s" "%s" 2>&1', self::$utilitiesPath, 'mkweb.sh', self::$basePath, $domain, self::$webDir, $domain, $phpUser), $output, $returnVal))
		{
			// Can't create directory. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t create the web directory.', 'output' => self::formatOutput($output));
			}
			// Problem. "mkweb.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "mkweb.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Domain and web directory is created successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 3
		
////////// Step 4: Correcting permissions on the domain directory
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s/%s" 2>&1', self::$utilitiesPath, 'chpermission.sh', $phpUser, self::$basePath, $domain), $output, $returnVal))
		{
			// Can't change permissions. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t change permissions on the domain directory.', 'output' => self::formatOutput($output));
			}
			// Problem. "chpermission.sh" didn't execute to the last line.
			else
			{			
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "chpermission.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Permissions changed successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 4
		
/////////// Step 5: Creating PHP pool definition
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s" 2>&1', self::$utilitiesPath, 'mkphppool.sh', $domain, $phpUser), $output, $returnVal))
		{
			// Can't create pool. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t create the PHP pool.', 'output' => self::formatOutput($output));
			}
			// Problem. "mkphppool.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "mkphppool.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Pool created successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 5
		
		
/////////// Step 6: Creating Apache virtualhost definition
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" 2>&1', self::$utilitiesPath, 'mkapachevhost.sh', $domain), $output, $returnVal))
		{
			// Can't create virtualhost. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t create the Apache virtualhost.', 'output' => self::formatOutput($output));
			}
			// Problem. "mkapachevhost.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "mkapachevhost.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Virtualhost created successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 6

/////////// Step 7: Creating Nginx virtualhost definition
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s" 2>&1', self::$utilitiesPath, 'mknginxvhost.sh', $domain, $ipPort), $output, $returnVal))
		{
			// Can't create virtualhost. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t create the Nginx virtualhost.', 'output' => self::formatOutput($output));
			}
			// Problem. "mknginxvhost.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "mknginxvhost.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Virtualhost created successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 7

////////// Step 8: Setting the aliases
		$aliases = json_decode($alias);
		$alias = trim(implode(' ', $aliases));
		
		if ($alias)
		{
			if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" "%s" 2>&1', self::$utilitiesPath, 'set_alias.sh', $domain, $alias), $output, $returnVal))
			{
				// Can't set aliases. Why?
				if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
				{
					return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t set aliases.', 'output' => self::formatOutput($output));
				}
				// Problem. "set_alias.sh" didn't execute to the last line.
				else
				{
					return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "set_alias.sh" utility.', 'output' => self::formatOutput($output));
				}
			}
			// Aliases were set successfully.
			else
			{
			
			}
			
			unset($output);
		}
////////// \Step 8

/////////// Step 9: Creating Webalizer definition
		if (self::$utilitiesBeginSignature != exec(sprintf('sudo sh "%s/%s" "%s" 2>&1', self::$utilitiesPath, 'mkwebalizer.sh', $domain), $output, $returnVal))
		{
			// Can't create webalizer config. Why?
			if (preg_match(sprintf('#%s#', self::$utilitiesEndSignature), implode("\n", $output)))
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Couldn\'t create the Webalizer config.', 'output' => self::formatOutput($output));
			}
			// Problem. "mkwebalizer.sh" didn't execute to the last line.
			else
			{
				return array('status' => 1, 'line' => __LINE__, 'message' => 'Critical error in executing "mkwebalizer.sh" utility.', 'output' => self::formatOutput($output));
			}
		}
		// Webalizer config created successfully.
		else
		{
		
		}
		
		unset($output);
////////// \Step 9
		
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
