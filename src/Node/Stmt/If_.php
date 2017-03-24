<?php

namespace PhpTestBed\Node\Stmt;

use PhpTestBed\I18n;
use PhpTestBed\Stylizer;

class If_ extends \PhpTestBed\Node\ResolverAbstract
{

    private $condition;
    private $elseRun;

    public function __construct(\PhpParser\Node\Stmt\If_ $node)
    {
        $this->elseRun = true;
        parent::__construct($node);
    }

    protected function printEnterMessage()
    {
        parent::__printEnterMessage('code.if-enter');
    }

    protected function printExitMessage()
    {
        parent::__printExitMessage('code.if-exit');
    }

    private function printIfCond()
    {
        $this->printMessage(
                I18n::getInstance()->get('code.if-cond') . ' ' .
                $this->condition->message()
        );
    }

    private function printElseCond()
    {
        $this->printMessage(
                Stylizer::systemMessage(
                        I18n::getInstance()->get('code.else-cond')
                )
                , $this->node->else->getLine()
        );
    }

    protected function resolve()
    {
        $scriptCrawler = \PhpTestBed\ScriptCrawler::getInstance();
        $this->condition = \PhpTestBed\Node\ResolverCondition::choose($this->node->cond);
        $scriptCrawler->addLevel();
        $this->printIfCond();
        $this->resolveIf();
        $this->resolveElse();
        $scriptCrawler->removeLevel();
    }

    private function resolveIf()
    {
        $scriptCrawler = \PhpTestBed\ScriptCrawler::getInstance();
        if ($this->condition->getResult()) {
            $scriptCrawler->crawl($this->node->stmts);
            $this->elseRun = false;
        } elseif (!empty($this->node->elseifs) && $this->elseRun) {
            foreach ($this->node->elseifs as $elseif) {
                if ($this->elseRun) {
                    $elseIf = new ElseIf_($elseif);
                    $this->elseRun = !$elseIf->getResolveState();
                }
            }
        }
    }

    private function resolveElse()
    {
        $scriptCrawler = \PhpTestBed\ScriptCrawler::getInstance();
        if (!empty($this->node->else) && $this->elseRun) {
            $this->printElseCond();
            $scriptCrawler->crawl($this->node->else->stmts);
        }
    }

}
