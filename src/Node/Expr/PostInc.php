<?php

namespace PhpTestBed\Node\Expr;

use PhpTestBed\I18n;
use PhpTestBed\Stylizer;

class PostInc extends \PhpTestBed\Node\ResolverAbstract
{

    private $value;

    public function __construct(\PhpParser\Node\Expr\PostInc $node)
    {
        parent::__construct($node);
    }

    protected function resolve()
    {
        $this->value = \PhpTestBed\Repository::getInstance()->get($this->node->var->name);
        $mVar = [
            'var' => Stylizer::variable("\${$this->node->var->name}"),
            'value' => Stylizer::value( ++$this->value)
        ];
        $this->printMessage(I18n::getInstance()->get('code.post-inc', $mVar));
        \PhpTestBed\Repository::getInstance()
                ->set($this->node->var->name, $this->value);
    }

    public function getValue()
    {
        return $this->value;
    }

}
