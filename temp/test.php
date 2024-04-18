<?php

// $str = "(二)09 资电103 (五)06-07 (四)11-13 资电103 刘维正";
// // $str = "(二)09 资电103 刘维正";
// // $str = "(四)01-04 忠516(建製圖室) (四)06-08 忠516(建製圖室) (五)01-04 忠804 朱伯晟,趙又嬋,饒珮齡,黃馨慧,張子浩,黃宏毓,何柏奇,詹仁普,黃郁惠,林佳平,鄭孟鴻,蕭元淞";
// preg_match_all('/\([一二三四五六日]\)\d{2}(-\d{2})?/u', $str, $matches);
// // 初始化结果数组
// $results = [];
// if (!empty($matches[0])) {
//     foreach ($matches[0] as $match) {
//         $clean = str_replace(['(', ')'], '', $match);
//         if (strpos($clean, '-') !== false) {
//             list($firstPart, $secondPart) = explode('-', $clean);
//             $weekDay = mb_substr($firstPart, 0, 1, "UTF-8");
//             $firstNumber = mb_substr($firstPart, 1, null, "UTF-8");
//             $results[] = $weekDay . str_pad($firstNumber, 2, "0", STR_PAD_LEFT);
//             $results[] = $weekDay . str_pad($secondPart, 2, "0", STR_PAD_LEFT);
//         } else {
//             $results[] = $clean;
//         }
//     }

//     // 调用 fillMissingNumbers 函数并输出结果
//     $filled = fillMissingNumbers($results);
//     foreach ($filled as $item) {
//         echo $item . "\n";
//     }
// } else {
//     echo "No match found";
// }

// // 处理数据并填充缺失的编号
// function fillMissingNumbers($array)
// {
//     $results = [];
//     $lastIndex = count($array) - 1;

//     for ($i = 0; $i < $lastIndex; $i++) {
//         // 当前元素和下一个元素
//         $current = $array[$i];
//         $next = $array[$i + 1];

//         // 添加当前元素到结果
//         $results[] = $current;

//         // 解析出当前和下一个编号
//         $currentNumber = intval(substr($current, 1));
//         $nextNumber = intval(substr($next, 1));

//         // 如果两者之间有间隔
//         while ($nextNumber > $currentNumber + 1) {
//             $currentNumber++;
//             $results[] = $current[0] . str_pad($currentNumber, 2, "0", STR_PAD_LEFT);
//         }
//     }

//     // 添加最后一个元素
//     $results[] = $array[$lastIndex];

//     return $results;
// }

// // 调用函数并输出结果

function chineseToNumber($chinese) {
    $numbers = [
        '一' => 1, '二' => 2, '三' => 3, '四' => 4,
        '五' => 5, '六' => 6, '日' => 7
    ];

    return isset($numbers[$chinese]) ? $numbers[$chinese] : 'Unknown';
}
$str = "(二)09 资电103 (五)06-07 (四)11-13 资电103 刘维正";
preg_match_all('/\([一二三四五六日]\)\d{2}(-\d{2})?/u', $str, $matches);
// $results = [];
if (!empty($matches[0])) {
    foreach ($matches[0] as $match) {
        $clean = str_replace(['(', ')'], '', $match);
        if (strpos($clean, '-') !== false) {
            list($firstPart, $secondPart) = explode('-', $clean);
            $weekDay = mb_substr($firstPart, 0, 1, "UTF-8");
            $firstNumber = mb_substr($firstPart, 1);
            $secondNumber = mb_substr($secondPart, 0);
            $weekDayNumber = chineseToNumber($weekDay);
            for ($i = intval($firstNumber); $i <= intval($secondNumber); $i++) {
                $results[] = $weekDayNumber . str_pad($i, 2, "0", STR_PAD_LEFT);
            }
        } else {
            $weekDay = mb_substr($clean, 0, 1, "UTF-8");
            $number = mb_substr($clean, 1);
            $weekDayNumber = chineseToNumber($weekDay);
            $results[] = $weekDayNumber . str_pad($number, 2, "0", STR_PAD_LEFT);
        }
    }
    foreach ($results as $item) {
        echo $item . "\n";
    }
} else {
    echo "No match found";
}