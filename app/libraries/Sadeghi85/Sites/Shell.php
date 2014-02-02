<?php namespace Libraries\Sadeghi85\Sites;

use \Illuminate\Support\Facades\Config as Config;

class Shell {
	
	private static $webpanelRoot;
	private static $sitesHome;
	private static $webDir;
	private static $tagStartsWith;
	private static $userShell;

    private static $initialized = false;

    private static function initialize()
    {
    	if (self::$initialized)
    		return;
		
		self::$webpanelRoot = Config::get('webpanel.webpanelRoot');
        self::$sitesHome = Config::get('webpanel.sitesHome');
		self::$webDir = Config::get('webpanel.webDir');
		self::$tagStartsWith = Config::get('webpanel.tagStartsWith');
		self::$userShell = Config::get('webpanel.userShell');
		
    	self::$initialized = true;
    }
	
	public static function CreateWebalizerConfig(&$errorMessage, $siteTag, $siteServerName)
	{
		self::initialize();
		
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/webalizer.d/sites-available'));
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/webalizer.d/sites-available-for-humans'));
		
		// Step1: Move possible existing config file
		$handle1 = popen(sprintf('sudo \mv -T -f "/etc/webalizer.d/sites-available/%s.conf" "/etc/webalizer.d/sites-available/%s.%s" 2>&1', $siteTag, $siteTag, date('Y-m-d-H-i-s')), 'r');
		if ( ! is_resource($handle1))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output1 = stream_get_contents($handle1);
		$return_value1 = pclose($handle1);
		
		// Step2: Copy Webalizer config file from template
		$handle2 = popen(sprintf('sudo \cp -T -f "%s/cmd/templates/webalizer/example.com.conf" "/etc/webalizer.d/sites-available/%s.conf" 2>&1', self::$webpanelRoot, $siteTag), 'r');
		if ( ! is_resource($handle2))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output2 = stream_get_contents($handle2);
		$return_value2 = pclose($handle2);
		
		if ($return_value2 !== 0)
		{
			$errorMessage = sprintf("Couldn't copy Webalizer config file from template.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output2));
			return false;
		}
		
		// Step3: Replace example site name with site tag
		$handle3 = popen(sprintf('sudo sed -i -e"s/example\.com/%s/g" "/etc/webalizer.d/sites-available/%s.conf" 2>&1', $siteTag, $siteTag), 'r');
		if ( ! is_resource($handle3))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output3 = stream_get_contents($handle3);
		$return_value3 = pclose($handle3);
		
		if ($return_value3 !== 0)
		{
			$errorMessage = sprintf("Couldn't edit Webalizer config file.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output3));
			return false;
		}
		
		// Step4: Create symlink
		$handle4 = popen(sprintf('sudo ln -T -f -s "%s" "%s" 2>&1', sprintf('../sites-available/%s.conf', $siteTag), sprintf('/etc/webalizer.d/sites-available-for-humans/%s.conf', $siteServerName)), 'r');
		if ( ! is_resource($handle4))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output4 = stream_get_contents($handle4);
		$return_value4 = pclose($handle4);
		
		return true;
	}
	
	public static function CreateNginxVhost(&$errorMessage, $siteTag, $siteServerName, $siteAliases, $sitePort)
	{
		self::initialize();
		
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/nginx/sites-available'));
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/nginx/sites-available-for-humans'));
		
		$nginxAliases = implode(' ', $siteAliases);
		
		// Step1: Move possible existing config file
		$handle1 = popen(sprintf('sudo \mv -T -f "/etc/nginx/sites-available/%s.conf" "/etc/nginx/sites-available/%s.%s" 2>&1', $siteTag, $siteTag, date('Y-m-d-H-i-s')), 'r');
		if ( ! is_resource($handle1))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output1 = stream_get_contents($handle1);
		$return_value1 = pclose($handle1);
		
		// Step2: Copy Nginx config file from template
		$handle2 = popen(sprintf('sudo \cp -T -f "%s/cmd/templates/nginx/example.com.conf" "/etc/nginx/sites-available/%s.conf" 2>&1', self::$webpanelRoot, $siteTag), 'r');
		if ( ! is_resource($handle2))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output2 = stream_get_contents($handle2);
		$return_value2 = pclose($handle2);
		
		if ($return_value2 !== 0)
		{
			$errorMessage = sprintf("Couldn't copy Nginx config file from template.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output2));
			return false;
		}
		
		// Step3: Add aliases
		$handle3 = popen(sprintf('sudo sed -i -e"s#^\(\s*\)root.*#\1root \"%s\";#I" -e"s#^\(\s*\)server_name.*#\1server_name %s;#I" -e"s#^\(\s*\)listen.*#\1listen %s;#I" "/etc/nginx/sites-available/%s.conf" 2>&1', sprintf('%s/sites-available/%s/%s', self::$sitesHome, $siteTag, self::$webDir), $nginxAliases, $sitePort, $siteTag), 'r');
		if ( ! is_resource($handle3))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output3 = stream_get_contents($handle3);
		$return_value3 = pclose($handle3);
		
		if ($return_value3 !== 0)
		{
			$errorMessage = sprintf("Couldn't edit Nginx config file.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output3));
			return false;
		}
		
		// Step4: Create symlink
		$handle4 = popen(sprintf('sudo ln -T -f -s "%s" "%s" 2>&1', sprintf('../sites-available/%s.conf', $siteTag), sprintf('/etc/nginx/sites-available-for-humans/%s.conf', $siteServerName)), 'r');
		if ( ! is_resource($handle4))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output4 = stream_get_contents($handle4);
		$return_value4 = pclose($handle4);
		
		return true;
	}
	
	public static function CreateApacheVhost(&$errorMessage, $siteTag, $siteServerName, $siteAliases, $sitePort)
	{
		self::initialize();
		
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/httpd/sites-available'));
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/httpd/sites-available-for-humans'));
		
		$apacheAliases = implode(' ', array_map(function ($alias) use ($sitePort) {
			return $sitePort.'.'.$alias;
		}, $siteAliases));
		
		// Step1: Move possible existing config file
		$handle1 = popen(sprintf('sudo \mv -T -f "/etc/httpd/sites-available/%s.conf" "/etc/httpd/sites-available/%s.%s" 2>&1', $siteTag, $siteTag, date('Y-m-d-H-i-s')), 'r');
		if ( ! is_resource($handle1))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output1 = stream_get_contents($handle1);
		$return_value1 = pclose($handle1);
		
		// Step2: Copy Apache config file from template
		$handle2 = popen(sprintf('sudo \cp -T -f "%s/cmd/templates/apache/example.com.conf" "/etc/httpd/sites-available/%s.conf" 2>&1', self::$webpanelRoot, $siteTag), 'r');
		if ( ! is_resource($handle2))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output2 = stream_get_contents($handle2);
		$return_value2 = pclose($handle2);
		
		if ($return_value2 !== 0)
		{
			$errorMessage = sprintf("Couldn't copy Apache config file from template.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output2));
			return false;
		}
		
		// Step3: Replace example site name with siteServerName and add aliases
		$handle3 = popen(sprintf('sudo sed -i -e"s#example\.com#%s#g" -e"s#^\(\s*\)DocumentRoot.*#\1DocumentRoot \"%s\"#I" -e"s#^\(\s*\)<Directory.*#\1<Directory \"%s\">#I" -e"s#^\(\s*\)ServerName.*#\1ServerName %s#I" -e"s#^\(\s*\)ServerAlias.*#\1ServerAlias %s#I" -e"s#^\(\s*\)ServerAdmin.*#\1ServerAdmin postmaster@%s#I" "/etc/httpd/sites-available/%s.conf" 2>&1', $siteTag, sprintf('%s/sites-available/%s/%s', self::$sitesHome, $siteTag, self::$webDir), sprintf('%s/sites-available/%s/%s', self::$sitesHome, $siteTag, self::$webDir), $siteServerName, $apacheAliases, $siteServerName, $siteTag), 'r');
		if ( ! is_resource($handle3))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output3 = stream_get_contents($handle3);
		$return_value3 = pclose($handle3);
		
		if ($return_value3 !== 0)
		{
			$errorMessage = sprintf("Couldn't edit Apache config file.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output3));
			return false;
		}
		
		// Step4: Create symlink
		$handle4 = popen(sprintf('sudo ln -T -f -s "%s" "%s" 2>&1', sprintf('../sites-available/%s.conf', $siteTag), sprintf('/etc/httpd/sites-available-for-humans/%s.conf', $siteServerName)), 'r');
		if ( ! is_resource($handle4))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output4 = stream_get_contents($handle4);
		$return_value4 = pclose($handle4);
		
		return true;
	}
	
	public static function CreatePHPPool(&$errorMessage, $siteTag, $siteServerName)
	{
		self::initialize();
		
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/php-fpm.d/sites-available'));
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', '/etc/php-fpm.d/sites-available-for-humans'));
		
		// Step1: Move possible existing config file
		$handle1 = popen(sprintf('sudo \mv -T -f "/etc/php-fpm.d/sites-available/%s.conf" "/etc/php-fpm.d/sites-available/%s.%s" 2>&1', $siteTag, $siteTag, date('Y-m-d-H-i-s')), 'r');
		if ( ! is_resource($handle1))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output1 = stream_get_contents($handle1);
		$return_value1 = pclose($handle1);
		
		// Step2: Copy PHP Pool config file from template
		$handle2 = popen(sprintf('sudo \cp -T -f "%s/cmd/templates/php-fpm/example.com.conf" "/etc/php-fpm.d/sites-available/%s.conf" 2>&1', self::$webpanelRoot, $siteTag), 'r');
		if ( ! is_resource($handle2))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output2 = stream_get_contents($handle2);
		$return_value2 = pclose($handle2);
		
		if ($return_value2 !== 0)
		{
			$errorMessage = sprintf("Couldn't copy PHP Pool config file from template.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output2));
			return false;
		}
		
		// Step3: Replace example pool name with site's tag
		$handle3 = popen(sprintf('sudo sed -i -e"s#example\.com#%s#g" -e"s#^user\s\+=.*#user = %s#" -e"s#^group\s\+=.*#group = %s#" -e"s#^php_admin_value\[open_basedir\]\s\+=.*#php_admin_value[open_basedir] = \"/usr/share/pear:/tmp:%s\"#" "/etc/php-fpm.d/sites-available/%s.conf" 2>&1', $siteTag, $siteTag, $siteTag, sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag), $siteTag), 'r');
		if ( ! is_resource($handle3))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output3 = stream_get_contents($handle3);
		$return_value3 = pclose($handle3);
		
		if ($return_value3 !== 0)
		{
			$errorMessage = sprintf("Couldn't edit PHP Pool config file.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output3));
			return false;
		}
		
		// Step4: Create symlink
		$handle4 = popen(sprintf('sudo ln -T -f -s "%s" "%s" 2>&1', sprintf('../sites-available/%s.conf', $siteTag), sprintf('/etc/php-fpm.d/sites-available-for-humans/%s.conf', $siteServerName)), 'r');
		if ( ! is_resource($handle4))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output4 = stream_get_contents($handle4);
		$return_value4 = pclose($handle4);
		
		return true;
	}
	
	public static function CreateSiteDir(&$errorMessage, $siteTag, $siteServerName)
	{
		self::initialize();
		
		// Step1: Create site's web directory
		$handle1 = popen(sprintf('sudo mkdir -p "%s" 2>&1', sprintf('%s/sites-available/%s/%s', self::$sitesHome, $siteTag, self::$webDir)), 'r');
		if ( ! is_resource($handle1))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output1 = stream_get_contents($handle1);
		$return_value1 = pclose($handle1);
		
		if ($return_value1 !== 0)
		{
			$errorMessage = sprintf("Couldn't create the site's directory.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output1));
			return false;
		}
		
		// Step2: Copy 'index.php' from template
		$handle2 = popen(sprintf('sudo \cp -T -f "%s/cmd/templates/web/index.php" "%s/index.php" 2>&1', self::$webpanelRoot, sprintf('%s/sites-available/%s/%s', self::$sitesHome, $siteTag, self::$webDir)), 'r');
		if ( ! is_resource($handle2))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output2 = stream_get_contents($handle2);
		$return_value2 = pclose($handle2);
		
		// Step3: Replace example site name with actual site name
		$handle3 = popen(sprintf('sudo sed -i -e"s/example\.com/%s/g" "%s/index.php" 2>&1', $siteServerName, sprintf('%s/sites-available/%s/%s', self::$sitesHome, $siteTag, self::$webDir)), 'r');
		if ( ! is_resource($handle3))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output3 = stream_get_contents($handle3);
		$return_value3 = pclose($handle3);
		
		// Step4: Change ownership on site's directory
		$handle4 = popen(sprintf('sudo chown -R "%s:%s" "%s" 2>&1', $siteTag, $siteTag, sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag)), 'r');
		if ( ! is_resource($handle4))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output4 = stream_get_contents($handle4);
		$return_value4 = pclose($handle4);
		
		// Step5: Change permissions to 644
		$handle5 = popen(sprintf('sudo chmod -R 644 "%s" 2>&1', sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag)), 'r');
		if ( ! is_resource($handle5))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output5 = stream_get_contents($handle5);
		$return_value5 = pclose($handle5);
		
		// Step6: Change directory permissions to 755
		$handle6 = popen(sprintf('sudo chmod -R +X "%s" 2>&1', sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag)), 'r');
		if ( ! is_resource($handle6))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output6 = stream_get_contents($handle6);
		$return_value6 = pclose($handle6);
		
		// Step7: Create symlink
		$handle7 = popen(sprintf('sudo ln -T -f -s "%s" "%s" 2>&1', sprintf('../sites-available/%s', $siteTag), sprintf('%s/sites-available-for-humans/%s', self::$sitesHome, $siteServerName)), 'r');
		if ( ! is_resource($handle7))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output7 = stream_get_contents($handle7);
		$return_value7 = pclose($handle7);
		
		return true;
	}
	
	public static function CreateUser(&$errorMessage, $siteTag, $siteAliases)
	{
		self::initialize();
		
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag)));
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', sprintf('%s/sites-available-for-humans', self::$sitesHome)));
		shell_exec(sprintf('sudo ln -fs "%s" "%s" 2>&1', sprintf('../sites-available/%s', $siteTag), sprintf('%s/sites-available-for-humans/%s', self::$sitesHome, $siteTag)));
		
		$userComment = implode(' ', $siteAliases);
		
		$handle1 = popen(sprintf('sudo groupadd "%s" 2>&1', $siteTag), 'r');
		if ( ! is_resource($handle1))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output1 = stream_get_contents($handle1);
		$return_value1 = pclose($handle1);
		
		$handle2 = popen(sprintf('sudo id "%s" 2>&1', $siteTag), 'r');
		if ( ! is_resource($handle2))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		$output2 = stream_get_contents($handle2);
		$return_value2 = pclose($handle2);
		
		// User already exists
		if ($return_value2 === 0)
		{
			// Edit user
			shell_exec(sprintf('sudo killall -u %s', $siteTag));
			$handle3 = popen(sprintf('sudo usermod --home "%s" --shell "%s" --gid "%s" -G "%s" --comment "%s" "%s" 2>&1', sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag), self::$userShell, $siteTag, $siteTag, $userComment, $siteTag), 'r');
			if ( ! is_resource($handle3))
			{
				$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
				return false;
			}
			$output3 = stream_get_contents($handle3);
			$return_value3 = pclose($handle3);
			
			// if ($return_value3 !== 0)
			// {
				// $errorMessage = sprintf("Couldn't edit user.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output3));
				// return false;
			// }
		}
		// User doesn't exist
		else
		{
			// Create user
			$handle3 = popen(sprintf('sudo useradd --home "%s" --shell "%s" --gid "%s" -G "%s" --comment "%s" "%s" 2>&1', sprintf('%s/sites-available/%s', self::$sitesHome, $siteTag), self::$userShell, $siteTag, $siteTag, $userComment, $siteTag), 'r');
			if ( ! is_resource($handle3))
			{
				$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
				return false;
			}
			$output3 = stream_get_contents($handle3);
			$return_value3 = pclose($handle3);
			
			if ($return_value3 !== 0)
			{
				$errorMessage = sprintf("Couldn't create user.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s</pre>", __FILE__, __LINE__, e($output3));
				return false;
			}
		}
		
		return true;
	}
	
	public static function FindNextTag(&$errorMessage, &$siteTag)
	{
		self::initialize();
		
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', sprintf('%s/sites-available', self::$sitesHome)));
		shell_exec(sprintf('sudo mkdir -p "%s" 2>&1', sprintf('%s/sites-available-for-humans', self::$sitesHome)));
		
		$output = '';
		$error = '';
		
		$descriptorspec = array(
			//0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w") // stderr is a pipe that the child will write to
		);

		$handle = proc_open(sprintf('sudo ls %s/sites-available/', self::$sitesHome), $descriptorspec, $pipes);

		if ( ! is_resource($handle))
		{
			$errorMessage = sprintf("Couldn't open process.<br><br>File: %s<br>Method: %s<br>Line: %s<br>", __FILE__, __LINE__, __METHOD__);
			return false;
		}
		
		$output = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		$error = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		// It is important that you close any pipes before calling
		// proc_close in order to avoid a deadlock
		$return_value = proc_close($handle);
		
		if ($return_value !== 0)
		{
			$errorMessage = sprintf("Couldn't get list of directory.<br><br>File: %s<br>Line: %s<br><br>Details:<br><pre>%s\r\n%s</pre>", __FILE__, __LINE__, e($output), e($error));
			return false;
		}
		
		$array = array_filter(array_map('trim', explode("\n", $output)), function ($str) {
			return preg_match(sprintf('#^%s\d+$#', self::$tagStartsWith), $str);
		});
		
		sort($array);
		
		$tag = end($array);
		
		preg_match(sprintf('#^%s(\d+)$#', self::$tagStartsWith), $tag, $m);
		if (isset($m[1]))
		{
			$tag = (self::$tagStartsWith).(str_pad($m[1]+1, 3, '0', STR_PAD_LEFT));
		}
		else
		{
			$tag = (self::$tagStartsWith).'001';
		}
		
		$siteTag = $tag;
		return true;
	}
}