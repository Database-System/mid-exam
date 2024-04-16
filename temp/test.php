<?php

$str = "(二)09 资电103 (五)06-07 (四)11-12 资电103 刘维正";
// $str = "(二)09 资电103 刘维正";
// $str = "(四)01-04 忠516(建製圖室) (四)06-08 忠516(建製圖室) (五)01-04 忠804 朱伯晟,趙又嬋,饒珮齡,黃馨慧,張子浩,黃宏毓,何柏奇,詹仁普,黃郁惠,林佳平,鄭孟鴻,蕭元淞";
preg_match_all('/\([一二三四五六日]\)\d{2}(-\d{2})?/u', $str, $matches);
$results = [];
if (!empty($matches[0])) {
    foreach ($matches[0] as $match) {
        $clean = str_replace(['(', ')'], '', $match);
        if (strpos($clean, '-') !== false) {
            list($firstPart, $secondPart) = explode('-', $clean);
            $weekDay = mb_substr($firstPart, 0, 1, "UTF-8");
            $firstNumber = mb_substr($firstPart, 1, null, "UTF-8");
            $results[] = $weekDay . $firstNumber;
            $results[] = $weekDay . $secondPart;
        } else {
            $results[] = $clean;
        }
    }
    var_dump($results);
} else {
    echo "No match found";
}