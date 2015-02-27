<?php namespace UnitTesting\FunctionSpy;

class Method {

	protected $calls = array();

	protected $result = null;

	public function addCall(array $args)
	{
		$this->calls[] = $args;
		return $this;
	}

	public function getCalls()
	{
		return $this->calls;
	}

	public function wasCalled()
	{
		return !empty($this->calls);
	}

	public function wasCalledWith(array $args)
	{
		return in_array($args, $this->calls);
	}

	public function wasLastCalledWith(array $args)
	{
		return end($this->calls) == $args;
	}

	public function setResult($result)
	{
		$this->result = $result;
	}

	public function getResult()
	{
		return $this->result;
	}

}
