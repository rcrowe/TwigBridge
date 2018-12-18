<?php

namespace TwigBridge\Extension\Laravel;

use Illuminate\Http\Request;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels input class in your Twig templates.
 */
class Input extends AbstractExtension
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new input extension
     *
     * @param \Illuminate\Http\Request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Input';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('input_get', [$this->request, 'input']),
            new TwigFunction('input_old', [$this->request, 'old']),
            new TwigFunction('input_has', [$this->request, 'has']),
            new TwigFunction('old', [$this->request, 'old']),
        ];
    }
}
