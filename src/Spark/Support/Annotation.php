<?php

namespace Spark\Support;

class Annotation
{
    const TYPE_LONG = 1;

    const TYPE_SHORT = 2;

    const BLOCK_SEQUENCE_END = "\r\n";

    const BLOCK_PLACEHOLDER = '{{{}}}';

    protected $language;

    protected $content;

    protected $blocker_long = [];

    protected $blocker_short = [];

    protected $block_front_space = 0;

    public function __construct(string $language = null)
    {
        $this->loadBlocker();
        $language && $this->setLanguage($language);
    }

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

    public function setContent(string ...$content)
    {
        $this->content = $content;
        return $this;
    }

    public function setFrontSpace(int $num = 0)
    {
        $this->block_front_space = $num;
        return $this;
    }

    public function create(int $type, string ...$content)
    {
        call_user_func_array([$this, 'setContent'], $content);
        switch ($type) {
            case static::TYPE_LONG:
                return $this->toLong();
            case static::TYPE_SHORT:
            default:
                return $this->toShort();
        }
    }

    protected function toLong(string $default = 'default')
    {
        $blocker = $this->getBlocker(static::TYPE_LONG, $default);
        if (!$blocker) {
            return null;
        }
        $placeholder = $this->getPlaceholder();
        $sequence_end = $this->getSequenceEnd();
        $start = $blocker['start'];
        $inner = $blocker['inner'];
        $end = $blocker['end'];
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

    protected function toShort(string $default = 'default')
    {
        $blocker = $this->getBlocker(static::TYPE_SHORT, $default);
        if (!$blocker) {
            return null;
        }
        $placeholder = $this->getPlaceholder();
        $str = '';
        foreach ($this->content as $c) {
            $str .= str_replace(
                $placeholder,
                $this->getFrontSpaces() . $c,
                $blocker
            );
            break;
        }
        return $str;
    }

    protected function getPlaceholder()
    {
        return static::BLOCK_PLACEHOLDER;
    }

    protected function getSequenceEnd()
    {
        return static::BLOCK_SEQUENCE_END;
    }

    protected function getFrontSpaces()
    {
        return str_repeat(' ', $this->block_front_space);
    }

    protected function getBlocker(int $type, string $default)
    {
        $blockers = $this->blocker_short;
        if ($type == static::TYPE_LONG) {
            $blockers = $this->blocker_long;
        }
        if (isset($blockers[$this->language])) {
            return $blockers[$this->language];
        } else {
            if (isset($blockers[$default])) {
                return $blockers[$default];
            }
        }
        return $blockers['default'] ?? null;
    }

    protected function loadBlocker()
    {
        $this->blocker_long = [
            'default' => [
                'start' => '/**',
                'inner' => ' *' . static::BLOCK_PLACEHOLDER,
                'end' => ' */'
            ]
        ];
        $this->blocker_short = [
            'default' => '//' . static::BLOCK_PLACEHOLDER
        ];
    }
}
