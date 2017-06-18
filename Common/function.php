<?php
/**
 * Created by PhpStorm.
 * User: 郝飞
 * Date: 2017/4/5
 * Time: 15:05
 */

/**
 * 生成指定位数的随机数
 * @param $length 需要的字符长度
 * @return int
 */
function randNum($length){
	$min = pow(10 , ($length - 1));
	$max = pow(10, $length) - 1;
	return rand($min, $max);
}