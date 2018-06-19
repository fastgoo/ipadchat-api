<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/11
 * Time: 下午11:18
 */

namespace PadChat;

class Util
{
    /**
     * 请求数据签名
     * @param $params
     * @param string $secret
     * @return string
     * @throws RequestException
     */
    public static function makeSign($params, $secret = '')
    {
        if (!is_array($params)) {
            throw new RequestException('[签名] 请求参数必须传入数组参数');
        }
        /** 排序数组 */
        ksort($params);
        reset($params);
        /** 拼装签名字符串 */
        $arg = '';
        foreach ($params as $key => $val) {
            if (is_array($val)) {
                $val = json_encode($val, JSON_UNESCAPED_UNICODE);
            }
            $arg .= $key . '=' . $val . '&';
        }
        $arg && $arg = substr($arg, 0, -1);
        /** sha256 签名 */
        $sign = hash('sha256', "$arg&{$secret}");
        return $sign;
    }


}