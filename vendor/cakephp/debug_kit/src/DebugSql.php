<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 3.11.4
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit;

use Cake\Core\Configure;
use Cake\Database\Query;
use Cake\Error\Debugger;
use Doctrine\SqlFormatter\CliHighlighter;
use Doctrine\SqlFormatter\HtmlHighlighter;
use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;

/**
 * Contains methods for dumping well formatted SQL queries.
 */
class DebugSql
{
    /**
     * Template used for HTML output.
     *
     * @var string
     */
    private static string $templateHtml = <<<HTML
<div class="cake-debug-output">
%s
<pre class="cake-debug">
%s
</pre>
</div>
HTML;

    /**
     * Template used for CLI and text output.
     *
     * @var string
     */
    private static string $templateText = <<<TEXT
%s
########## DEBUG ##########
%s
###########################

TEXT;

    /**
     * Prints out the SQL statements generated by a Query object.
     *
     * This function returns the same variable that was passed.
     * Only runs if debug mode is enabled.
     *
     * @param \Cake\Database\Query $query Query to show SQL statements for.
     * @param bool $showValues Renders the SQL statement with bound variables.
     * @param bool|null $showHtml If set to true, the method prints the debug
     *    data in a browser-friendly way.
     * @param int $stackDepth Provides a hint as to which file in the call stack to reference.
     * @return \Cake\Database\Query
     */
    public static function sql(
        Query $query,
        bool $showValues = true,
        ?bool $showHtml = null,
        int $stackDepth = 0
    ): Query {
        if (!Configure::read('debug')) {
            return $query;
        }

        $sql = (string)$query;
        if ($showValues) {
            $sql = self::interpolate($sql, $query->getValueBinder()->bindings());
        }

        /** @var array $trace */
        $trace = Debugger::trace(['start' => 0, 'depth' => $stackDepth + 1, 'format' => 'array']);
        $file = isset($trace[$stackDepth]) ? $trace[$stackDepth]['file'] : 'n/a';
        $line = isset($trace[$stackDepth]) ? $trace[$stackDepth]['line'] : 0;
        $lineInfo = '';
        if ($file) {
            $search = [];
            if (defined('ROOT')) {
                $search = [ROOT];
            }
            if (defined('CAKE_CORE_INCLUDE_PATH')) {
                array_unshift($search, CAKE_CORE_INCLUDE_PATH);
            }
            /** @var string $file */
            $file = str_replace($search, '', $file);
        }

        $template = self::$templateHtml;
        if (static::isCli() || $showHtml === false) {
            $template = self::$templateText;
            if ($file && $line) {
                $lineInfo = sprintf('%s (line %s)', $file, $line);
            }
        }
        if ($showHtml === null && $template !== self::$templateText) {
            $showHtml = true;
        }

        if (static::isCli() && !$showHtml) {
            $highlighter = new CliHighlighter([
                CliHighlighter::HIGHLIGHT_QUOTE => "\x1b[33;1m",
                CliHighlighter::HIGHLIGHT_WORD => "\x1b[36;1m",
                CliHighlighter::HIGHLIGHT_VARIABLE => "\x1b[33;1m",
            ]);
        } elseif ($showHtml) {
            $highlighter = new HtmlHighlighter(
                [
                    HtmlHighlighter::HIGHLIGHT_QUOTE => 'style="color: #004d40;"',
                    HtmlHighlighter::HIGHLIGHT_BACKTICK_QUOTE => 'style="color: #26a69a;"',
                    HtmlHighlighter::HIGHLIGHT_NUMBER => 'style="color: #ec407a;"',
                    HtmlHighlighter::HIGHLIGHT_WORD => 'style="color: #9c27b0;"',
                    HtmlHighlighter::HIGHLIGHT_PRE => 'style="color: #222; background-color: transparent;"',
                ],
                false
            );
        } else {
            $highlighter = new NullHighlighter();
        }

        $var = (new SqlFormatter($highlighter))->format($sql);
        $var = trim($var);

        if ($showHtml) {
            $template = self::$templateHtml;
            if ($file && $line) {
                $lineInfo = sprintf('<span><strong>%s</strong> (line <strong>%s</strong>)</span>', $file, $line);
            }
        }

        printf($template, $lineInfo, $var);

        return $query;
    }

    /**
     * Prints out the SQL statements generated by a Query object and dies.
     *
     * Only runs if debug mode is enabled.
     * It will otherwise just continue code execution and ignore this function.
     *
     * @param \Cake\Database\Query $query Query to show SQL statements for.
     * @param bool $showValues Renders the SQL statement with bound variables.
     * @param bool|null $showHtml If set to true, the method prints the debug
     *    data in a browser-friendly way.
     * @param int $stackDepth Provides a hint as to which file in the call stack to reference.
     * @return void
     */
    public static function sqld(
        Query $query,
        bool $showValues = true,
        ?bool $showHtml = null,
        int $stackDepth = 1
    ): void {
        static::sql($query, $showValues, $showHtml, $stackDepth);
        die(1);
    }

    /**
     * Checks whether the current environment is CLI based.
     *
     * @return bool
     */
    protected static function isCli(): bool
    {
        return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
    }

    /**
     * Helper function used to replace query placeholders by the real
     * params used to execute the query.
     *
     * @param string $sql The SQL statement
     * @param array $bindings The Query bindings
     * @return string
     */
    private static function interpolate(string $sql, array $bindings): string
    {
        $params = array_map(function ($binding) {
            $p = $binding['value'];

            if ($p === null) {
                return 'NULL';
            }
            if (is_bool($p)) {
                return $p ? '1' : '0';
            }

            if (is_string($p)) {
                $replacements = [
                    '$' => '\\$',
                    '\\' => '\\\\\\\\',
                    "'" => "''",
                ];

                $p = strtr($p, $replacements);

                return "'$p'";
            }

            return $p;
        }, $bindings);

        $keys = [];
        $limit = is_int(key($params)) ? 1 : -1;
        foreach ($params as $key => $param) {
            $keys[] = is_string($key) ? "/$key\b/" : '/[?]/';
        }

        return preg_replace($keys, $params, $sql, $limit);
    }
}
