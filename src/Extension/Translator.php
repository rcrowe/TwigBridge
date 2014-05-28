<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 * @copyright Barry vd. Heuvel <barryvdh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use  Illuminate\Translation\Translator as LaravelTranslator;


class Translator extends \Twig_Extension
{
    protected $translator;

    public function __construct(LaravelTranslator $translator){
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_Translator';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        return array(
            new Twig_SimpleFunction('trans', array($this->translator, 'trans'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('trans_choice', array($this->translator, 'transChoice'), array('is_safe' => array('html'))),
        );
    }

}
