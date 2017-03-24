<?php

namespace PhpTestBed\Node\Stmt;

use PhpTestBed\I18n;

class For_ extends \PhpTestBed\Node\ResolverAbstract
{

    public function __construct(\PhpParser\Node\Stmt\For_ $node)
    {
        parent::__construct($node);
    }

    private function testConditions()
    {
        foreach ($this->node->cond as $cond) {
            $binOp = \PhpTestBed\Node\ResolverCondition::choose($cond);
            $this->printMessage(
                    I18n::getInstance()->get('code.if-cond') . ' ' .
                    $binOp->message()
            );
            if ($binOp->getResult() === false) {
                return false;
            }
        }
        return true;
    }

    protected function printEnterMessage()
    {
        parent::__printEnterMessage('code.for-enter');
    }

    protected function printExitMessage()
    {
        parent::__printExitMessage('code.for-exit');
    }

    protected function resolve()
    {
        $scriptCrawler = \PhpTestBed\ScriptCrawler::getInstance();
        $scriptCrawler->addLevel();
        $scriptCrawler->crawl($this->node->init);
        while (!$scriptCrawler->getBreak() && !$scriptCrawler->getThrow() && $this->testConditions()) {
            if (!empty($this->node->stmts)) {
                $scriptCrawler->crawl($this->node->stmts);
            }
            if (!empty($this->node->loop)) {
                $scriptCrawler->crawl($this->node->loop);
            }
        }
        $scriptCrawler->removeLevel();
        $scriptCrawler->removeBreak();
    }

}
