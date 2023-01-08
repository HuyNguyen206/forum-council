<?php

namespace App\SpamRules;

class InvalidKeywords
{
    const INVALID_KEY_WORDS = ['yahoo customer support'];

    public function detect($body)
    {
        foreach (self::INVALID_KEY_WORDS as $word) {
            if (stripos($body, $word) !== false) {
                return true;
            }
        }

        return false;
    }
}
