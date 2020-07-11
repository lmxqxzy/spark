<?php

namespace Spark\Support\Regions;

use Spark\Support\Arr;

class ChinaRegions
{
    /**
     * 直辖市
     */
    const DIRECT_CITIES = ['北京市', '天津市', '重庆市', '上海市'];

    /**
     * 解析原始数据成单数组
     *
     * @param string $file_path 原始数据文件路径
     * @param string $save_path 数组文件保存路径
     *
     * @return int|false|null
     */
    public static function parseOriginalDataToArray(
        string $file_path = null,
        string $save_path = null
    ) {
        if (is_null($file_path)) {
            $file_path = static::defaultDataPath();
        }
        if (is_null($save_path)) {
            $save_path = static::defaultDataArrayPath();
        }
        $content = file_get_contents($file_path);
        if (is_string($content)) {
            $content = str_replace(["\t"], '', $content);
            $first_line_end_index = strpos($content, "\n");
            $first_line = substr($content, 0, $first_line_end_index);
            $body = substr($content, $first_line_end_index + 1);
            $lines = explode("\n", $body);
            $data = [];
            if (is_array($lines)) {
                foreach ($lines as $item) {
                    $str = trim($item);
                    $code = substr($str, 0, 6);
                    $name = trim(substr($str, 6));
                    $data[(string) $code] = $name;
                }
            }
            $str = Arr::export($data, true);
            $contents = [
                "<?php\r\n// ",
                $first_line,
                "\r\nreturn ",
                $str . ";"
            ];
            return file_put_contents($save_path, $contents, LOCK_EX);
        }
    }

    public static function loadDataArray(string $data_path = null)
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__;
        $data_dir = $dir . $ds . 'data' . $ds;
        $name = 'ChinaRegionsData';
        if (is_null($data_path)) {
            $data_path = $data_dir . $name . '.php';
        }
        return require $data_path;
    }

    /**
     * 单数组解析成多层次的结构数组
     *
     * @param string $array_path 单数组文件路径
     * @param string $save_path
     *
     * @return array
     */
    public static function parseDataTree(
        string $array_path = null,
        string $save_path = null
    ) {
        if (is_null($array_path)) {
            $data_path = static::defaultArrayPath();
        }
        if (is_null($save_path)) {
            $save_path = static::defaultTreeSavePath();
        }

        $data = require $data_path;
        $country = [];
        $province = [];
        $city = [];
        $district = [];
        $dis_cities = static::DIRECT_CITIES;

        /** 省份解析，依据格式：2位不为0+4个0 */
        array_walk_recursive(
            $data,
            function ($value, $key)
            use (&$country, &$province) {
                if (preg_match("/[1-9]{1,2}0{4}/", $key) === 1) {
                    $country[$key]['name'] = $value;
                    $province[$key] = $value;
                }
            }
        );

        $except_province = array_diff($data, $province);

        /** 城市解析，依据格式：4位不为0+2个0 */
        foreach ($province as $pkey => $pvalue) {
            array_walk_recursive(
                $except_province,
                function ($value, $key)
                use ($pkey, &$country, &$city) {
                    $prefix = substr($pkey, 0, 2);
                    if (preg_match('/^' . $prefix . '[0-9]{2}0{2}$/', $key) === 1) {
                        $country[$pkey]['items'][$key]['name'] = $value;
                        $city[$key] = $value;
                    }
                }
            );
        }

        $district = array_diff($except_province, $city);

        /** 区县解析，直辖市的区县划分在普通城市层次 */
        foreach ($country as $pkey => $province_array) {
            if (!isset($province_array['items'])) {
                array_walk_recursive(
                    $district,
                    function ($value, $key)
                    use ($pkey, $province_array, $dis_cities, &$country) {
                        $prefix = substr($pkey, 0, 2);
                        if (preg_match('/^' . $prefix . '0{1}[0-9]{3}$/', $key) === 1) {
                            $item = ['name' => $value];
                            // 直辖市城市使用省份进行重复
                            // if (in_array($province_array['name'], $dis_cities)) {
                            //     $city_key = $pkey;
                            //     if (isset($country[$pkey]['items'][$city_key]['items'])) {
                            //         $country[$pkey]['items'][$city_key]['items'][$key] = $item;
                            //     } else {
                            //         if (isset($country[$pkey]['items'][$city_key])) {
                            //             $country[$pkey]['items'][$city_key]['items'] = [];
                            //             $country[$pkey]['items'][$city_key]['items'][$key] = $item;
                            //         } else {
                            //             $city_item = ['name' => $province_array['name']];
                            //             $country[$pkey]['items'][$city_key] = $city_item;
                            //             $country[$pkey]['items'][$city_key]['items'] = [];
                            //             $country[$pkey]['items'][$city_key]['items'][$key] = $item;
                            //         }
                            //     }
                            // } else {
                            //     $country[$pkey]['items'][$key] = ['name' => $value];
                            // }
                            // 一般处理
                            $country[$pkey]['items'][$key] = $item;
                        }
                    }
                );
            } else {
                if (is_array($province_array['items'])) {
                    foreach ($province_array['items'] as $ckey => $city_array) {
                        array_walk_recursive(
                            $district,
                            function ($value, $key)
                            use ($pkey, $province_array, $dis_cities, $ckey, &$country) {
                                $prefix = substr($ckey, 0, 4);
                                if (preg_match('/^' . $prefix . '[0-9]{2}$/', $key) === 1) {
                                    // 直辖市城市使用省份进行重复
                                    // if (in_array($province_array['name'], $dis_cities)) {
                                    //     $city_key = $pkey;
                                    //     if (isset($country[$pkey]['items'][$city_key]['items'])) {
                                    //         $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                    //     } else {
                                    //         if (isset($country[$pkey]['items'][$city_key])) {
                                    //             $country[$pkey]['items'][$city_key]['items'] = [];
                                    //             $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                    //         } else {
                                    //             $country[$pkey]['items'][$city_key] = ['name' => $province_array['name']];
                                    //             $country[$pkey]['items'][$city_key]['items'] = [];
                                    //             $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                    //         }
                                    //     }
                                    // } else {
                                    //     $country[$pkey]['items'][$ckey]['items'][$key] = ['name' => $value];
                                    // }
                                    // 一般处理
                                    $country[$pkey]['items'][$ckey]['items'][$key] = ['name' => $value];
                                }
                            }
                        );
                    }
                }
            }
        }

        static::saveArrayToFile($country, $save_path);
        return $country;
    }

    /**
     * Undocumented function
     *
     * @param array $arr
     * @param string $save_path
     * @param string $commit
     *
     * @return int|false
     */
    public static function saveArrayToFile(
        array $arr,
        string $save_path,
        string $commit = ''
    ) {
        $header = "<?php\r\n\r\n";
        $commit = $commit ? $commit . "\r\n" : '';

        $data = Arr::export($arr, true);
        $content = $header . $commit . 'return ' . $data . ';';
        return file_put_contents($save_path, $content);
    }

    public function save($data, string $save_path = null)
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__;
        $data_dir = $dir . $ds . 'data' . $ds;
        $name = 'ChinaRegionsData';
        if (is_null($save_path)) {
            $save_path = $data_dir . 'Tree' . $ds . $name . 'Tree.php';
        }
    }

    protected static function defaultCommit()
    { }

    /**
     * 获取默认数据路径
     *
     * @return string
     */
    protected static function defaultDataPath(): string
    {
        $dir = static::defaultDataDir();
        $name = static::defaultFileName();
        return $dir . $name;
    }

    /**
     * 获取默认数据数组路径
     *
     * @return string
     */
    protected static function defaultArrayPath(): string
    {
        $dir = static::defaultDataDir();
        $name = static::defaultFileName() . '.php';
        return $dir . $name;
    }

    /**
     * 获取默认数据树数组保存路径
     *
     * @return string
     */
    protected static function defaultTreeSavePath(): string
    {
        $dir = static::defaultDataTreeDir();
        $name = static::defaultFileName() . 'Tree.php';
        return $dir . $name;
    }

    /**
     * 获取默认数据目录
     *
     * @return string
     */
    protected static function defaultDataDir(): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__;
        return $dir . $ds . 'Data' . $ds;
    }

    /**
     * 获取默认数据树目录
     *
     * @return string
     */
    protected static function defaultDataTreeDir(): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $dir = static::defaultDataTreeDir();
        return $dir . 'Tree' . $ds;
    }

    /**
     * 获取默认文件名
     *
     * @return string
     */
    protected static function defaultFileName(): string
    {
        return 'ChinaRegionsData';
    }
}
