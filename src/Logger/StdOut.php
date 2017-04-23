<?php

namespace AmoCRM\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Class StdOut
 *
 * Стандартный PSR-3 совместимый логгер, печатающий все в STDOUT
 *
 * @package AmoCRM\Logger
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class StdOut extends AbstractLogger
{
    const NO_COLOR = 49;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_CYAN = 36;

    /**
     * @var array Список цветов соответвующих уровням
     */
    private $colors = [
        LogLevel::EMERGENCY => self::COLOR_RED,
        LogLevel::ALERT => self::COLOR_RED,
        LogLevel::CRITICAL => self::COLOR_RED,
        LogLevel::ERROR => self::COLOR_RED,
        LogLevel::WARNING => self::COLOR_YELLOW,
        LogLevel::NOTICE => self::COLOR_BLUE,
        LogLevel::INFO => self::COLOR_GREEN,
        LogLevel::DEBUG => self::COLOR_CYAN,
    ];

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = [])
    {
        if (posix_isatty(STDOUT)) {
            printf("\033[0;%sm%s\033[0m\n", $this->color($level), $this->format($level, $message, $context));
        } else {
            printf("%s\n", $this->format($level, $message, $context));
        }
    }

    /**
     * Возвращает цвет соотвествующий уровню
     *
     * @param string $level
     * @return int
     */
    private function color($level)
    {
        return isset($this->colors[$level]) ? $this->colors[$level] : self::NO_COLOR;
    }

    /**
     * Форматирование сообщения и контекста
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    private function format($level, $message, array $context = [])
    {
        $context = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        return sprintf("[%s][%s] %s %s", strtoupper($level), date('c'), $message, $context);
    }
}