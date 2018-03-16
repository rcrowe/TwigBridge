<?php

namespace TwigBridge\Twig\Normalizers;

interface Normalizer
{
    /**
     * Remove the extension from the given file name.
     *
     * @param string $name
     *
     * @return string
     */
    public function normalize(string $name): string;
}
