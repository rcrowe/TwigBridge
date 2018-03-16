<?php

namespace TwigBridge\Twig\Normalizers;

class DefaultNormalizer implements Normalizer
{
    /**
     * @var array
     */
    protected $extensions;

    /**
     * @param array $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(string $name): string
    {
        foreach ($this->extensions as $extension) {
            if (!str_contains($name, $extension)) {
                continue;
            }

            return str_before($name, $this->extension($extension));
        }

        return $name;
    }

    /**
     * @param string $extension
     *
     * @return string
     */
    protected function extension(string $extension): string
    {
        return str_start($extension, '.');
    }
}
