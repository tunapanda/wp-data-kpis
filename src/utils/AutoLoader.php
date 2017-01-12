<?php

namespace datakpi;

class AutoLoader {

	private $namespace;
	private $sourcePaths;

	public function __construct($namespace=NULL) {
		$this->namespace=NULL;
		if ($namespace)
			$this->setNamespace($namespace);

		$this->sourcePaths=array();
	}

	public function setNamespace($namespace) {
		$this->namespace=$namespace;
	}

	public function addSourcePath($sourcePath) {
		$this->sourcePaths[]=$sourcePath;
	}

	public function register() {
		spl_autoload_register(array($this,"autoloader"));
	}

	public function autoloader($fullClassName) {
		if (!$this->namespace)
			throw new Exception("Namespace not set");

		$namespacePart=$this->namespace."\\";
		if (substr($fullClassName,0,strlen($namespacePart))!=$namespacePart)
			return;

		$className=substr($fullClassName,strlen($namespacePart));

		foreach ($this->sourcePaths as $sourcePath) {
			$classFileName=$sourcePath."/".$className.".php";
			if (file_exists($classFileName))
				require_once $classFileName;
		}
	}
}