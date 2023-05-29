<?php

//decode by http://www.yunlu99.com/
namespace utils\yly;

spl_autoload_register("\\App\\Autoloader::loadByNamespace");
class Autoloader
{
	public static function loadByNamespace($name)
	{
		$class_path = str_replace("\\", DIRECTORY_SEPARATOR, $name);
		if (strpos($name, "App\\") === 0) {
			$class_file = __DIR__ . substr($class_path, strlen("App")) . ".php";
		} else {
			if (empty($class_file) || !is_file($class_file)) {
				$class_file = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . $class_path . ".php";
			}
		}
		if (is_file($class_file)) {
			require_once $class_file;
			if (class_exists($name, false)) {
				return true;
			}
		}
		return false;
	}
}