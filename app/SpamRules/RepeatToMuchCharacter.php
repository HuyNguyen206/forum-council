<?php

namespace App\SpamRules;

class RepeatToMuchCharacter
{
    public function detect($body)
    {
        return preg_match('/(.)\1{4,}/', $body);
    }
}
