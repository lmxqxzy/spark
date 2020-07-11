<?php

namespace Spark\Support;

class Arr
{
    /**
     * 格式输出数组
     *
     * @param array $expression
     * @param bool $is_return
     *
     * @return string|null
     */
    public static function export(
        array $expression,
        bool $is_return = false
    ) {
        $export = var_export($expression, true);
        $patterns = [
            "/array \(/"         => '[',
            "/^([ ]*)\)(,?)$/m"  => '$1]$2',
            "/=>[ ]?\n[ ]+\[/"   => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(
            array_keys($patterns),
            array_values($patterns),
            $export
        );
        if ($is_return) {
            return $export;
        } else {
            echo $export;
        }
    }
}
