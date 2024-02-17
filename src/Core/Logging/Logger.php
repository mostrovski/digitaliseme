<?php

namespace Digitaliseme\Core\Logging;

use BadMethodCallException;

/**
 * @method void trace(string $message)
 * @method void debug(string $message)
 * @method void info(string $message)
 * @method void warning(string $message)
 * @method void error(string $message)
 * @method void fatal(string $message)
 */
class Logger
{
    protected array $levels = ['trace', 'debug', 'info', 'warning', 'error', 'fatal'];
    protected string $level = 'debug';
    protected string $message = '';

    protected function write(): void
    {
        $log = '['.date('Y-m-d H:i:s.u').'] ['.strtoupper($this->level).'] '.$this->message.PHP_EOL;
        file_put_contents(
            logs_path($this->fileName()),
            $log,
            FILE_APPEND
        );
    }

    protected function fileName(): string
    {
        return 'digitaliseme_'.date('Y-m-d').'_'.$this->level.'.log';
    }

    public function __call(string $name, array $arguments)
    {
        if (! in_array($name, $this->levels, true)) {
            throw new BadMethodCallException('Method '.$name.' does not exist.');
        }

        $this->level = $name;
        $this->message = (string) current($arguments);
        $this->write();
    }
}
