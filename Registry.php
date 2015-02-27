<?php namespace UnitTesting\FunctionSpy;
use ArrayAccess;

class Registry implements ArrayAccess {

	protected $recorders = array();

	public function getRecorders()
	{
		return $this->recorders;
	}

	public function getRecorder($method)
	{
		$result = null;
		if (isset($this->recorders[$method]))
		{
			$result = $this->recorders[$method];
		}
		return $result;
	}

	public function flushRecorders()
	{
		$this->recorders = array();
	}

	public function setFunctionResult($method, $result)
	{
		$instance = $this->resolveRecorder($method);
		$instance->setResult($result);
	}

	protected function resolveRecorder($method)
	{
		if (!$instance = $this->getRecorder($method))
		{
			$this->recorders[$method] = $instance = new Recorder();
		}
		return $instance;
	}

	protected function recordFunctionCall($method, array $args)
	{
		$instance = $this->resolveRecorder($method);

		$instance->addCall($args);

		return $instance->getResult();
	}

	public function __call($method, array $args)
	{
		return $this->recordFunctionCall($method, $args);
	}

	public function offsetExists($key)
	{
		return $this->getRecorder($key) !== null;
	}

	public function offsetGet($key)
	{
		return $this->resolveRecorder($key);
	}

	public function offsetSet($key, $value)
	{
		$this->setFunctionresult($key, $value);
	}

	public function offsetUnset($key)
	{
		throw new \OverflowException('Cannot unset property');
	}
}
