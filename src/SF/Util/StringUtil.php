<?php
namespace SF\Util;


class StringUtil
{
    public static function nullOrEpmty($string)
    {
        return $string === null || $string === '';
    }

    public static function parseCSV($string)
    {
        $line  = array();
        $buf = '';
        $chars = str_split($string);
        for ($i = 0, $l = count($chars); $i < $l;  $i++) {
            $char = $chars[$i];
            if ($char === ' ' || $char === "\t") continue;
            if ($char === '"' || $char === "'") {
                $quote = $char;
                for ($i += 1; $i < $l; $i++) {
                    $char = $chars[$i];
                    if ($char === '\\') {
                        $next = $chars[++$i];
                        if ($next === 'n') {
                            $buf .= "\n";
                        } elseif ($next === 't') {
                            $buf .= "\t";
                        } else {
                            $buf .= $next;
                        }
                    } elseif ($quote === $char) {
                        $line[] = $buf;
                        $buf = '';
                        for ($i += 1; $i < $l; $i++) {
                            $char = $chars[$i];
                            if ($char === ',' || $char === "\n") break;
                        }
                        break;
                    } else {
                        $buf .= $char;
                    }
                }
            } elseif ($char === "\n") {
                $line[] = $buf;
                $buf = '';
                break;
            } elseif ($char === ',') {
                $line[] = $buf;
                $buf = '';
            } else {
                $buf .= $char;
            }
        }
        if ($buf !== '') {
            $line[] = $buf;
        }
        return $line;
    }
}