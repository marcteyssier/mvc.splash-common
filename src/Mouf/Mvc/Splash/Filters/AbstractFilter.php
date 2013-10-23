<?php
namespace Mouf\Mvc\Splash\Filters;

use Mouf\Reflection\MoufReflectionMethod;

use Mouf\Mvc\Splash\Controllers\Controller;


/**
 * Class to be extended to add a filter to an action.
 */
abstract class AbstractFilter
{
	/**
	 * The controller this filter is applied to
	 * @var Controller
	 */
	protected $controller;

	/**
	 * The method this filter is applied to
	 * @var MoufReflectionMethod
	 */
	protected $refMethod;
	
	/**
	 * Used in serialization
	 * @var string
	 */
	protected $serializeInstanceName;

	/**
	 * Used in serialization
	 * @var string
	 */
	protected $serializeMethodName;

	/*public function getAnnotationTarget()
    {
        return stubAnnotation::TARGET_METHOD+stubAnnotation::TARGET_CLASS;
    }*/

    /**
	 * Returns the priority. The higher the priority, the earlier the beforeAction will be executed and the later the afterAction will be executed.
	 * Default priority is 50.
	 * @return int The priority.
	 */
	protected function getPriority() {
		return 50;
	}


	/**
	 * Function to be called before the action.
	 */
	abstract public function beforeAction();

	/**
	 * Function to be called after the action.
	 */
	abstract public function afterAction();

	/**
	 * Sets the metadata for this annotation.
	 */
	public function setMetaData(Controller $controller, MoufReflectionMethod $refMethod) {
		$this->controller = $controller;
		$this->refMethod = $refMethod;
	}

	public static function compare($filter1, $filter2) {
		return ($filter1->getPriority() - $filter2->getPriority());
	}
        
        /**
        * TODO: se demander est-ce que les pointeurs sont nécessaires ou pas. Dans l'idéal, la classe pourrait ne pas contenir de pointeurs.
        * @return array
        */
		public function __sleep() {
           $moufManager = \Mouf\MoufManager::getMoufManager();
           $this->serializeInstanceName = $moufManager->findInstanceName($this->controller);
           $this->serializeMethodName = $this->refMethod->getName();
           return array("serializeInstanceName", "serializeMethodName");
       	}

       public function __wakeup() {
           $moufManager = \Mouf\MoufManager::getMoufManager();

           $this->controller = $moufManager->getInstance($this->serializeInstanceName);
           $this->refMethod = new MoufReflectionMethod(new \Mouf\Reflection\MoufReflectionClass(get_class($this->controller)), $this->serializeMethodName);
       }
}
?>