<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SluggerService
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param string $string the string to slugify
     * @return string the slugified string
     */
    public function slugify(string $string): string
    {
        return $this->slugger->slug($string, '-')->lower();
    }
}
