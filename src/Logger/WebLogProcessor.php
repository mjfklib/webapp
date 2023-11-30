<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\ProcessorInterface;
use Monolog\Processor\WebProcessor;

class WebLogProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[] $processors
     */
    protected array $processors = [];


    public function __construct()
    {
        $this->processors[] = new ProcessIdProcessor();
        $this->processors[] = new WebProcessor();
    }


    /**
     * @inheritdoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        foreach ($this->processors as $processor) {
            $record = $processor->__invoke($record);
        }
        return $record;
    }
}
