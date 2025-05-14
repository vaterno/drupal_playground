<?php

namespace Drupal\vaterno_comments\Helper;

class TimeHelper
{
    public static function getCurrentTime(): int
    {
        return time();
    }
}
