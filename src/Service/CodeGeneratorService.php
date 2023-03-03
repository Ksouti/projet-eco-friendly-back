<?php

namespace App\Service;

class CodeGeneratorService
{
    /**
     * @return string a unique code to be used with a non unique nickname
     */
    public function codeGen(): string
    {
        $digits = 4;
        return '#' . str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
    }
}
