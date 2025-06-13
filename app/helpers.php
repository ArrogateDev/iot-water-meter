<?php

if (!function_exists('validate_cn_phone')) {
    /**
     * 验证是否是中国验证码.
     *
     * @param string $number
     * @return bool
     */
    function validate_cn_phone(string $number)
    {
        return (bool)preg_match('/^1[3-9]\d{9}$/', $number);
    }
}

if (!function_exists('make_order_no')) {
    /**
     * 生成订单号.
     *
     * @return string
     */
    function make_order_no()
    {
        list($msec, $sec) = explode(' ', microtime());
        return random_int(1000, 9999) . (int)sprintf('%.0f', ((float)$msec + (float)$sec) * 1000);
    }
}

if (!function_exists('generateRandomLetters')) {
    /**
     * @param int $length
     * @return string
     */
    function generateRandomLetters(int $length = 2)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

}

if (!function_exists('def')) {
    /**
     * [defer description]
     * @Author    https://github.com/php-defer/php-defer
     * @param \SplStack &$context [description]
     * @param callable $callback [description]
     * @return    void [description]
     */
    function def(?SplStack &$context, callable $callback): void
    {
        $context ??= new class() extends SplStack {
            public function __destruct()
            {
                while ($this->count() > 0) {
                    \call_user_func($this->pop());
                }
            }
        };

        $context->push($callback);
    }
}

if (!function_exists('limit_page')) {
    /**
     * 分页每页数据量
     *
     * @return mixed
     */
    function limit_page()
    {
        return min(max((int)request()->input('limit', 20), 1), 30);
    }
}

if (!function_exists('current_page')) {
    /**
     * 分页当前页
     *
     * @return mixed
     */
    function current_page()
    {
        return max((int)request()->input('offset', 0), 0);
    }
}


if (!function_exists('list_to_tree')) {

    /**
     * @param array $array
     * @param int $pid
     * @param string $children_key
     * @param string $primary_key
     * @param string $relation_key
     * @return array
     */
    function list_to_tree(array $array, int $pid = 0, string $children_key = 'children', string $primary_key = 'id', string $relation_key = 'pid')
    {
        $tree = [];
        foreach ($array as $key => $value) {
            if ($value[$relation_key] == $pid) {
                unset($array[$key]);
                $value[$primary_key] !== $value[$relation_key] && $value[$children_key] = list_to_tree($array, $value[$primary_key], $children_key, $primary_key, $relation_key);
                $tree[] = $value;
            }
        }
        return $tree;
    }
}

if (!function_exists('web_resource_url')) {
    function web_resource_url($file)
    {
        return asset('storage/' . $file) . ('?v=' . (env('APP_ENV') === 'production' ? env('ENV_VERSION', '1.0.0') : time()));
    }
}

if (!function_exists('percentage')) {
    function percentage($number, $decimals = 2)
    {
        return number_format(round($number, $decimals), $decimals, '.', '');
    }
}
