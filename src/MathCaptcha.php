<?php

namespace ElicDev\MathCaptcha;

use Illuminate\Session\SessionManager;

class MathCaptcha
{
    /**
     * @var SessionManager
     */
    private $session;

    const OPERATION_ADDITION = '+';
    const OPERATION_SUBTRACTION = '-';
    const OPERATION_MULTIPLICATION = '*';

    /**
     *
     * @param SessionManager|null $session
     */
    public function __construct(SessionManager $session = null)
    {
        $this->session = $session;
    }

    /**
     * Returns the math question as string.
     *
     * @return string
     */
    public function label()
    {
        return sprintf("%d %s %d", $this->getMathFirstOperator(), $this->getMathOperation(), $this->getMathSecondOperator());
    }

    /**
     * Returns the math input field
     * @param  array  $attributes Additional HTML attributes
     * @return string the input field
     */
    public function input(array $attributes = [])
    {
        $attributes['type'] = 'text';
        $attributes['id'] = 'modernmathcaptcha';
        $attributes['name'] = 'modernmathcaptcha';
        $attributes['required'] = 'required';
        $attributes['value'] = old('modernmathcaptcha');

        $html = '<input ' . $this->buildAttributes($attributes) . ' />';

        return $html;
    }

    /**
     * Laravel input validation
     * @param  string $value
     * @return boolean
     */
    public function verify($value)
    {
        return $value == $this->getMathResult();
    }

    /**
     * Reset the math operators to regenerate a new question.
     *
     * @return void
     */
    public function reset()
    {
        $this->session->forget('modernmathcaptcha.first');
        $this->session->forget('modernmathcaptcha.second');
        $this->session->forget('modernmathcaptcha.operation');
    }

    /**
     * The first math operand.
     *
     * @return integer
     */
    protected function getMathFirstOperator()
    {
        if (!$this->session->get('modernmathcaptcha.first')) {
            $this->session->put('modernmathcaptcha.first', rand(5, 10));
        }

        return $this->session->get('modernmathcaptcha.first');
    }

    /**
     * The second math operand
     * @return integer
     */
    protected function getMathSecondOperator()
    {
        if (!$this->session->get('modernmathcaptcha.second')) {
            $this->session->put('modernmathcaptcha.second', rand(0, 5));
        }

        return $this->session->get('modernmathcaptcha.second');
    }

    /**
     * The math operation
     * @return mixed
     */
    protected function getMathOperation()
    {
        if (!$this->session->get('modernmathcaptcha.operation')) {
            $this->session->put('modernmathcaptcha.operation', array_random([
                self::OPERATION_ADDITION,
                self::OPERATION_MULTIPLICATION,
                self::OPERATION_SUBTRACTION,
            ]));
        }

        return $this->session->get('modernmathcaptcha.operation');
    }

    /**
     * The math result to be validated.
     * @return integer
     */
    protected function getMathResult()
    {
        $operation = $this->session->get('modernmathcaptcha.operation');
        if ($operation === self::OPERATION_ADDITION) {
            return $this->getMathFirstOperator() + $this->getMathSecondOperator();
        }
        elseif ($operation === self::OPERATION_SUBTRACTION) {
            return $this->getMathFirstOperator() - $this->getMathSecondOperator();
        }
        elseif ($operation === self::OPERATION_MULTIPLICATION) {
            return $this->getMathFirstOperator() * $this->getMathSecondOperator();
        }
    }

    /**
     * Build HTML attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    protected function buildAttributes(array $attributes)
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            $html[] = $key . '="' . $value . '"';
        }
        return count($html) ? ' ' . implode(' ', $html) : '';
    }

}
