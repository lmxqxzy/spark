<?php

namespace Spark\Support;

class Annotation
{
    const PROCESSOR_TYPE_LONG = 1;

    const PROCESSOR_TYPE_SHORT = 2;

    const PROCESSOR_SEQUENCE_END = "\r\n";

    const PROCESSOR_ARGUMENT_PLACEHOLDER = '{{{}}}';

    protected $language;

    protected $content;

    protected $processor_long = [];

    protected $processor_short = [];

    protected $processor_front_space = 0;

    protected $current_processor;

    public function __construct(string $language = null)
    {
        $this->loadDefaultProcessor();
        $language && $this->setLanguage($language);
    }

    /**
     * 设置注释语言，以应用不同格式的注释格式
     *
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage(string $language)
    {
        if ($language) {
            $language =  strtolower($language);
            $languages = ['html', 'javascript', 'css', 'php'];
            if (in_array($language, $languages)) {
                $this->lang = $language;
            }
        }
        return $this;
    }

    /**
     * 设置需要被注释的内容
     *
     * @param string[] ...$content 需要被注释的内容，可重复添加
     *
     * @return $this
     */
    public function setContent(string ...$content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 设置前置空格数
     *
     * @param int $num
     *
     * @return $this
     */
    public function setFrontSpace(int $num = 0)
    {
        $this->processor_front_space = $num;
        return $this;
    }

    /**
     * 生成注释
     *
     * @param int $type 注释格式 长/短
     * @param string[] ...$content 需要被注释的内容，可重复添加
     *
     * @return string|null
     */
    public function create(int $type, string ...$content)
    {
        call_user_func_array([$this, 'setContent'], $content);
        switch ($type) {
            case static::PROCESSOR_TYPE_LONG:
                return $this->toLong();
            case static::PROCESSOR_TYPE_SHORT:
            default:
                return $this->toShort();
        }
    }

    /**
     * 输出长注释
     *
     * @param string $default 默认
     *
     * @return string
     */
    protected function toLong(string $default = 'default')
    {
        $processor = $this->getProcessor(
            static::PROCESSOR_TYPE_LONG,
            $default
        );
        if (!$processor) {
            return null;
        }
        $placeholder = $this->getPlaceholder();
        $sequence_end = $this->getSequenceEnd();
        $start = $processor['start'];
        $inner = $processor['inner'];
        $end = $processor['end'];
        $str = '';
        $str .= $start;
        $str .= $sequence_end;
        foreach ($this->content as $c) {
            $str .= str_replace(
                $placeholder,
                $this->getFrontSpaces() . $c,
                $inner
            );
            $str .= $sequence_end;
        }
        $str .= $end;
        return $str;
    }

    /**
     * 输出短注释
     *
     * @param string $default 默认
     *
     * @return string
     */
    public function toShort(string $default = 'default')
    {
        $processor = $this->getProcessor(
            static::PROCESSOR_TYPE_SHORT,
            $default
        );
        if (!$processor) {
            return null;
        }
        $placeholder = $this->getPlaceholder();
        $str = '';
        foreach ($this->content as $c) {
            $str .= str_replace(
                $placeholder,
                $this->getFrontSpaces() . $c,
                $processor
            );
            break;
        }
        return $str;
    }

    /**
     * 获取变量占位字符串
     *
     * @return string
     */
    protected function getPlaceholder()
    {
        return static::PROCESSOR_ARGUMENT_PLACEHOLDER;
    }

    /**
     * 获取行结束换行处理字符串
     *
     * @return string
     */
    protected function getSequenceEnd()
    {
        return static::PROCESSOR_SEQUENCE_END;
    }

    /**
     * 获取设定的前置空格字符串
     *
     * @return string
     */
    protected function getFrontSpaces()
    {
        return str_repeat(' ', $this->processor_front_space);
    }

    /**
     * 获取注释解析模式数组
     *
     * @param int $type 长/短注释选择，非长注释转换为短注释
     * @param string $default 当找不到时使用的默认解析语言
     *
     * @return mixed|null
     */
    protected function getProcessor(int $type, string $default)
    {
        $processors = $this->processor_short;
        if ($type == static::PROCESSOR_TYPE_LONG) {
            $processors = $this->processor_long;
        }
        if (isset($processors[$this->language])) {
            return $processors[$this->language];
        } else {
            if (isset($processors[$default])) {
                return $processors[$default];
            }
        }
        return $processors['default'] ?? null;
    }

    /**
     * 加载注释解析模式数组
     *
     * @return $this
     */
    protected function loadDefaultProcessor()
    {
        $placeholder = static::PROCESSOR_ARGUMENT_PLACEHOLDER;
        $this->processor_long = [
            'default' => [
                'start' => '/**',
                'inner' => ' *' . $placeholder,
                'end' => ' */'
            ]
        ];
        $this->processor_short = [
            'default' => '//' . $placeholder
        ];
        return $this;
    }

    /**
     * 添加新语言的注释处理器
     *
     * @param string $language 语言标识
     * @param mixed $processor_long 长注释处理格式
     * @param mixed $processor_short 短注释处理格式
     * @param bool $set_language 是否同时切换到当前语言
     *
     * @return $this
     */
    public function addProcessor(
        string $language,
        $processor_long = null,
        $processor_short = null,
        bool $set_language = false
    ) {
        if (!is_null($processor_long)) {
            $this->addLongProcessor($language, $processor_long);
        }
        if (!is_null($processor_short)) {
            $this->addShortProcessor($language, $processor_short);
        }
        if ($set_language) {
            $this->setLanguage($language);
        }
        return $this;
    }

    /**
     * 添加新的长注释处理器
     *
     * @param string $language 语言标识
     * @param mixed $processor 处理格式
     * @param bool $set_language 是否同时切换到当前语言
     *
     * @return $this
     */
    public function addLongProcessor(
        $language,
        $processor,
        bool $set_language = false
    ) {
        if ($language != 'default') {
            $this->processor_long[$language] = $processor;
        }
        if ($set_language) {
            $this->setLanguage($language);
        }
        return $this;
    }

    /**
     * 添加新的短注释处理器
     *
     * @param string $language 语言标识
     * @param mixed $processor 处理格式
     * @param bool $set_language 是否同时切换到当前语言
     *
     * @return $this
     */
    public function addShortProcessor(
        string $language,
        $processor,
        bool
        $set_language = false
    ) {
        if ($language != 'default') {
            $this->processor_short[$language] = $processor;
        }
        if ($set_language) {
            $this->setLanguage($language);
        }
        return $this;
    }
}
