<?php


class ParamsSet extends BaseContract implements JsonSerializable
{

    protected $input;

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    public function getExpectedOutput()
    {
        return $this->expectedOutput;
    }

    /**
     * @param mixed $expectedOutput
     */
    public function setExpectedOutput($expectedOutput)
    {
        $this->expectedOutput = $expectedOutput;
    }

    protected $expectedOutput;


    function jsonSerialize()
    {
        return array(
            'input' => $this->input,
            'expectedOutput' => $this->expectedOutput);
    }

}