<?php namespace UnitTesting\FunctionSpy;

class Registry {
	protected $methods = array();

	protected function resolveMethod($method)
	{
		if (!$instance = $this->getSpiedMethod($method))
		{
			$this->methods[$method] = $instance = new Method();
		}
		return $instance;
	}

	public function setMethodResult($method, $result)
	{
		$instance = $this->resolveMethod($method);
		$instance->setResult($result);
	}

	public function spyMethodCall($method, array $args)
	{
		$instance = $this->resolveMethod($method);

		$instance->addCall($args);

		return $instance->getResult();
	}

	public function getAllSpiedMethods()
	{
		return $this->methods;
	}

	public function getSpiedMethod($method)
	{
		$result = null;
		if (isset($this->methods[$method]))
		{
			$result = $this->methods[$method];
		}
		return $result;
	}

	public function flushSpiedMethods()
	{
		$this->methods = array();
	}

	public function __call($method, array $args)
	{
		return $this->spyMethodCall($method, $args);
	}
}
