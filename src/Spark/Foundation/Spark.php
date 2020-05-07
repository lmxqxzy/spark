<?php

namespace Spark\Foundation;

class Spark
{
    const VERSION = '0.0.1.0 alpha';

    public static function getLongVersion()
    {
        return sprintf('Spark <info>%s</info>', self::VERSION);
    }
}