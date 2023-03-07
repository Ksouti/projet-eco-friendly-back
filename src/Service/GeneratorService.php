<?php

namespace App\Service;

use App\Repository\UserRepository;

class GeneratorService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return string a unique code to be used with a non unique nickname
     */
    public function codeGen(): string
    {
        $users = $this->userRepository->findAll();
        $codes = [];
        foreach ($users as $user) {
            $codes[] = $user->getCode();
        }
        $digits = 4;
        $code = '#' . str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
        while (in_array($code, $codes)) {
            $code = '#' . str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
        }
        return $code;
    }

    /**
     * @return string a temporary password
     */
    public function passwordGen(): string
    {
        // generates a 10 characters long password with at least one uppercase letter, one lowercase letter, one number and one special character
        $password = '';
        $length = 10;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
        $count = mb_strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $password .= mb_substr($chars, $index, 1);
        }
        while (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/", $password)) {
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $index = rand(0, $count - 1);
                $password .= mb_substr($chars, $index, 1);
            }
        }
        return $password;
    }
}
