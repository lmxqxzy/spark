<?php

namespace Spark\Support\Regions;

class ChinaRegions
{
    public static function parseOriginalDataToArray(
        string $file_path = null,
        string $save_path = null
    ) {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__;
        if (is_null($file_path)) {
            $file_path = $dir . $ds . 'data' . $ds . 'ChinaRegionsData';
        }
        if (is_null($save_path)) {
            $save_path = $file_path . '.php';
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
            $str = str_replace("array (", '[', var_export($data, true));
            $str = str_replace(')', ']', $str);
            $str = substr_replace($str, '', strrpos($str, ','), 1);
            $save = file_put_contents(
                $save_path,
                "<?php\r\n// "
                    . $first_line
                    . "\r\nreturn "
                    . $str . ";"
            );
        }
    }

    public static function parseDataTree(
        string $data_path = null,
        string $save_path = null
    ) {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__;
        $data_dir = $dir . $ds . 'data' . $ds;
        $name = 'ChinaRegionsData';
        if (is_null($data_path)) {
            $data_path = $data_dir . $name . '.php';
        }
        if (is_null($save_path)) {
            $save_path = $data_dir . 'Tree' . $ds . $name . 'Tree.php';
        }

        $data = include $data_path;
        $country = [];
        $province = [];
        $city = [];
        $district = [];
        $dis_cities = ['北京市', '天津市', '重庆市', '上海市'];
        array_walk_recursive($data, function ($value, $key) use (&$country, &$province) {
            if (preg_match("/[1-9]{1,2}0{4}/", $key) == 1) {
                $country[$key]['name'] = $value;
                $province[$key] = $value;
            }
        });

        $except_province = array_diff($data, $province);

        foreach ($province as $pkey => $pvalue) {
            array_walk_recursive($except_province, function ($value, $key) use ($pkey, &$country, &$city) {
                $prefix = substr($pkey, 0, 2);
                if (preg_match('/^' . $prefix . '[0-9]{2}0{2}$/', $key) == 1) {
                    $country[$pkey]['items'][$key]['name'] = $value;
                    $city[$key] = $value;
                }
            });
        }

        $district = array_diff($except_province, $city);

        foreach ($country as $pkey => $province_array) {
            if (!isset($province_array['items'])) {
                array_walk_recursive($district, function ($value, $key) use ($pkey, $province_array, $dis_cities, &$country) {
                    $prefix = substr($pkey, 0, 2);
                    if (preg_match('/^' . $prefix . '0{1}[0-9]{3}$/', $key)) {

                        if (in_array($province_array['name'], $dis_cities)) {
                            $city_key = substr($key, 0, 4) . '00';
                            if (isset($country[$pkey]['items'][$city_key]['items'])) {
                                $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                            } else {
                                if (isset($country[$pkey]['items'][$city_key])) {
                                    $country[$pkey]['items'][$city_key]['items'] = [];
                                    $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                } else {
                                    $country[$pkey]['items'][$city_key] = ['name' => $province_array['name']];
                                    $country[$pkey]['items'][$city_key]['items'] = [];
                                    $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                }
                            }
                        } else {
                            // $country[$pkey]['items'][$key] = $value;
                            $country[$pkey]['items'][$key] = ['name' => $value];
                        }
                    }
                });
            } else {
                if (is_array($province_array['items'])) {
                    foreach ($province_array['items'] as $ckey => $city_array) {
                        array_walk_recursive($district, function ($value, $key) use ($pkey, $province_array, $dis_cities, $ckey, &$country) {
                            $prefix = substr($ckey, 0, 4);
                            if (preg_match('/^' . $prefix . '[0-9]{2}$/', $key)) {

                                if (in_array($province_array['name'], $dis_cities)) {
                                    $city_key = substr($key, 0, 4) . '00';
                                    if (isset($country[$pkey]['items'][$city_key]['items'])) {
                                        $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                    } else {
                                        if (isset($country[$pkey]['items'][$city_key])) {
                                            $country[$pkey]['items'][$city_key]['items'] = [];
                                            $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                        } else {
                                            $country[$pkey]['items'][$city_key] = ['name' => $province_array['name']];
                                            $country[$pkey]['items'][$city_key]['items'] = [];
                                            $country[$pkey]['items'][$city_key]['items'][$key] = ['name' => $value];
                                        }
                                    }
                                } else {
                                    // $country[$pkey]['items'][$ckey]['items'][$key] = $value;
                                    $country[$pkey]['items'][$ckey]['items'][$key] = ['name' => $value];
                                }
                            }
                        });
                    }
                }
            }
        }

        static::saveArrayToFile($country, $save_path);
        return $country;
    }

    public static function saveArrayToFile(
        array $arr,
        string $save_path,
        string $commit = ''
    ) {
        $header = "<?php\r\n\r\n";
        $commit = $commit ? $commit . "\r\n" : '';
        // $data = var_export($arr, true);
        // // 替换数组符号
        // $data = str_replace("array (", '[', $data);
        // $data = str_replace(')', ']', $data);
        // // 去除最后一个元素末的逗号
        // $data = substr_replace($data, '', strrpos($data, ','), 1);
        $data = static::varexport($arr, true);
        $content = $header . $commit . 'return ' . $data . ';';
        return file_put_contents($save_path, $content);
    }

    protected static function varexport($expression, $return = false)
    {
        $export = var_export($expression, true);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ((bool) $return) return $export;
        else echo $export;
    }


    /**
     * 对country.php数组文件格式改进，输出json
     */
    // function json()
    // {
    //     $data = include('country.php');
    //     $country = [];
    //     $province = [];
    //     $city = [];
    //     $district = [];
    //     array_walk_recursive($data, function ($value, $key) use (&$country, &$province) {
    //         if (preg_match("/[1-9]{1,2}0{4}/", $key) == 1) {
    //             $country[$key]['name'] = $value;
    //             $province[$key] = $value;
    //         }
    //     });

    //     $except_province = array_diff($data, $province);

    //     foreach ($province as $pkey => $pvalue) {
    //         array_walk_recursive($except_province, function ($value, $key) use ($pkey, $pvalue, &$country, &$city) {
    //             $prefix = substr($pkey, 0, 2);
    //             if (preg_match('/^' . $prefix . '[0-9]{2}0{2}$/', $key) == 1) {
    //                 $country[$pkey]['items'][$key]['name'] = $value;
    //                 $city[$key] = $value;
    //             }
    //         });
    //     }

    //     $only_district = array_diff($except_province, $city);

    //     foreach ($country as $pkey => $province_array) {
    //         if (!isset($province_array['items'])) {
    //             array_walk_recursive($only_district, function ($value, $key) use ($pkey, &$country) {
    //                 $prefix = substr($pkey, 0, 2);
    //                 if (preg_match('/^' . $prefix . '0{1}[0-9]{3}$/', $key)) {
    //                     $country[$pkey]['items'][$key] = $value;
    //                 }
    //             });
    //         } else {
    //             if (is_array($province_array['items'])) {
    //                 foreach ($province_array['items'] as $ckey => $city_array) {
    //                     array_walk_recursive($only_district, function ($value, $key) use ($pkey, $ckey, &$country) {
    //                         $prefix = substr($ckey, 0, 4);
    //                         if (preg_match('/^' . $prefix . '[0-9]{2}$/', $key)) {
    //                             $country[$pkey]['items'][$ckey]['items'][$key] = $value;
    //                         }
    //                     });
    //                 }
    //             }
    //         }
    //     }
    //     header("Content-type:text/html;charset=utf-8");
    //     return json_encode($country, JSON_UNESCAPED_UNICODE);
    // }

    /**
     * 使用country文件读取并把结果写入到country.php
     */
    // function load()
    // {
    //     $file_name = __DIR__ . DIRECTORY_SEPARATOR . 'country';
    //     $data = [];
    //     if (file_exists($file_name)) {
    //         if ($file = fopen($file_name, 'r')) {
    //             while (!feof($file)) {
    //                 $t = trim(fgets($file));
    //                 $t = preg_replace('/\n{2,}/', '\n', $t);
    //                 $tt = explode(' ', $t);
    //                 if (count($tt) >= 2) {
    //                     $data[trim($tt[0])] = trim($tt[1]);
    //                 }
    //             }
    //             fclose($file);
    //         }
    //     }
    //     $new = str_replace("array (", '[', var_export($data, true));
    //     $new = str_replace(')', ']', $new);
    //     return file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'country.php', "<?php\r\nreturn " . $new . ";");
    // }
}
