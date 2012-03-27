<?php
class Class_Base
{
	/**
	 * Construct a table model to read database
	 * @param String $modelName
	 * @return Zend_Db_Table_Abstract
	 */
    
    static public function _($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model list class');
		}
		$className = 'Class_Model_'.$modelName.'_Tb';
	    $args = func_get_args();
		array_shift($args);
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args);
		} else {
		    return new $className;
		}
	}
	
	/**
	 * 
	 */
	
	static public function _row($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model, no model name given!');
		}
		$className = 'Class_Model_'.$modelName;
		$args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args); 
		} else {
		    return new $className;
		}
	}
	
	/**
	 * Construct a rowset model to read database
	 * @param String $modelName
	 */
    
    static public function _set($modelName)
	{
		if(empty($modelName)) {
			throw new Exception('could not find model, no model name given!');
		}
		$className = 'Class_Model_'.$modelName.'_Rowset';
		$args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args); 
		} else {
		    return new $className;
		}
	}
	
	/**
	 * Construct a table model to read database
	 * @param String $modelName
	 * @throws Exception
	 * @return Zend_Db_Table_Abstract
	 */
	static public function _tb($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model list class');
		}
		$className = 'Class_Model_'.$modelName.'_Tb';
	    $args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args);
		} else {
		    return new $className;
		}
	}
	
	static public function _query($select)
	{
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
	}
	
    static function fireLog($message)
    {
        $loger = self::_getLogger('firebug');
		$loger->info($message);
    }
    
    static function log($message, $type = null)
    {
        switch($type) {
            case 'info':
                $loger = self::_getLogger('file', 'info.log');
                $loger->info($message);
                break;
            case 'error':
                $loger = self::_getLogger('file', 'error.log');
                $loger->info($message);
                break;
            default:
                $loger = self::_getLogger('file', $type.'.log');
                $loger->crit($message);
                break;
        }
    }
    
    protected static function _getLogger($loggerType, $filename = null)
    {
        switch($loggerType) {
            case 'file':
                return new Zend_Log(new Zend_Log_Writer_Stream(APP_PATH.'/'.$filename));
                break;
            case 'firebug':
                return new Zend_Log(new Zend_Log_Writer_Firebug());
                break;
            default:
                throw new Exception('logger type is not defined!');
        }
    }
    
    public static function siteInfo($field)
    {
//        $siteObj = Class_TableFactory::getRow('site', array('name' => 'site'), 1);
//        $row = $siteObj->toArray();
//        if(array_key_exists($field, $row)) {
//            return $row[$field];
//        }
//        return null;
    }
    
    public static function _cache(Array $reset = array())
    {
        $frontendName = 'Core';
        $backendName = 'File';
        $frontendOptions = array('lifetime' => 10800, 'automatic_serialization' =>  true);
        $backendOptions = array('cache_dir' => CACHE_PATH, 'hashed_directory_level' => 1);
        
        foreach($reset as $key => $value) {
            switch($key) {
                case 'frontendName':
                    $frontendName = $value;
                    break;
                case 'backendName':
                    $backendName = $value;
                    break;
                case 'caching':
                    $frontendOptions['caching'] = $value;
                    break;
                case 'lifetime':
                    $frontendOptions['lifetime'] = $value;
                    break;
            }
        }
        
        $cache = Zend_Cache::factory(
             $frontendName,
             $backendName,
             $frontendOptions,
             $backendOptions
        );
        return $cache;
    }
    
    public static function copyDir($source, $destination)
    {
    	$dir = opendir($source);
    	if(!is_dir($destination)) {
			$oldumask = umask(0); 
			mkdir($destination, 01777);
			umask($oldumask);
		}
	    while(false !== ($file = readdir($dir))) {
	        if(($file != '.') && ($file != '..') && ($file != '.svn')) {
	            if(is_dir($source.'/'.$file)) {
	                self::copyDir($source.'/'.$file, $destination.'/'.$file);
	            } else {
	                copy($source.'/'.$file, $destination.'/'.$file);
	            }
	        }
	    }
	    closedir($dir);
    }
    
    public static function removeDir($directory, $empty = false)
    {
	    if(substr($directory,-1) == "/") {
	        $directory = substr($directory,0,-1);
	    }
		
	    if(!file_exists($directory) || !is_dir($directory)) {
	        return false;
	    } elseif(!is_readable($directory)) {
	        return false;
	    } else {
	        $directoryHandle = opendir($directory);
	        while ($contents = readdir($directoryHandle)) {
	            if($contents != '.' && $contents != '..') {
	                $path = $directory . "/" . $contents;
	                if(is_dir($path)) {
	                    self::removeDir($path);
	                } else {
	                    unlink($path);
	                }
	            }
	        }
	        closedir($directoryHandle);
	        if($empty == false) {
	            if(!rmdir($directory)) {
	                return false;
	            }
	        }
	       	
	        return true;
	    }
    }
}