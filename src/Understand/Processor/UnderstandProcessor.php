<?php namespace Understand\Processor;

class UnderstandProcessor
{

    /**
     * Field providers
     *
     * @var array
     */
    protected $callbacks;

    /**
     * @param array $callbacks
     */
    public function __construct(array $callbacks = [])
    {
        $this->callbacks = $callbacks;
    }

    /**
     * Adds additional data
     *
     * @param array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        foreach ($this->callbacks as $fieldName => $caller)
        {
            if ( ! is_array($caller))
            {
                $caller = [$caller];
            }

            $callback = $caller[0];
            $args = isset($caller[1]) ? $caller[1] : [];

            $value = call_user_func_array($callback, $args);

            $record[$fieldName] = $value;
        }

        return $record;
    }
}