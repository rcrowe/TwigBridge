<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @license MIT
 */

namespace TwigBridge\Command;

if (!defined('JSON_PRETTY_PRINT')) {
    define('JSON_PRETTY_PRINT', 128);
}

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Twig_Error_Loader;
use Twig_Error;
use RuntimeException;
use InvalidArgumentException;

/**
 * Lint check Twig templates.
 *
 * Taken from the Symfony TwigBundle:
 * https://github.com/symfony/TwigBundle/blob/master/Command/LintCommand.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Lint extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'twig:lint';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Lints Twig templates';

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        $format = $this->option('format');

        // Check STDIN for the template
        if (ftell(STDIN) === 0) {
            // Read template in
            $template = '';

            while (!feof(STDIN)) {
                $template .= fread(STDIN, 1024);
            }

            return $this->display(array($this->validate($template)), $format);
        }

        $files   = $this->getFiles($this->argument('filename'), $this->option('check'));
        $details = array();

        foreach ($files as $file) {

            try {
                $template = $this->getContents($file);
            } catch (Twig_Error_Loader $e) {
                throw new RuntimeException(sprintf('File or directory "%s" is not readable', $file));
            }

            $details[] = $this->validate($template, $file);
        }

        return $this->display($details, $format);
    }

    protected function getFiles($filename, array $check = array())
    {
        // Get files from passed in options
        $search = $check;

        if (!empty($filename)) {
            $search[] = $filename;
        }

        // If no files passed, use the view paths
        if (empty($search)) {
            $finder = new Finder;
            $paths  = $this->laravel['view']->getFinder()->getPaths();

            $finder->files()->in($paths)->name('*.'.$this->laravel['twig.bridge']->getExtension());

            foreach ($finder as $file) {
                $search[] = $file->getRealPath();
            }
        }

        return $search;
    }

    protected function getContents($file)
    {
        return $this->laravel['twig.loader']->getSource($file);
    }

    protected function validate($template, $file = null)
    {
        $twig = $this->laravel['twig'];

        try {
            $twig->parse($twig->tokenize($template, $file));
        } catch (Twig_Error $e) {
            return array(
                'template'  => $template,
                'file'      => $file,
                'valid'     => false,
                'exception' => $e,
            );
        }

        return array(
            'template'  => $template,
            'file'      => $file,
            'valid'     => true,
        );
    }

    protected function display(array $details, $format = 'text')
    {
        $verbose = $this->getOutput()->isVerbose();

        switch ($format) {
            case 'text':
                return $this->displayText($details, $verbose);

            case 'json':
                return $this->displayJson($details, $verbose);

            default:
                throw new InvalidArgumentException(sprintf('The format "%s" is not supported.', $format));
        }
    }

    protected function displayText(array $details, $verbose = false)
    {
        $errors = 0;

        foreach ($details as $info) {
            if ($info['valid'] && $verbose) {
                $file = ($info['file']) ? ' in '.$info['file'] : '';
                $this->line('<info>OK</info>'.$file);
            } elseif (!$info['valid']) {
                $errors++;
                $this->renderException($info);
            }
        }

        // Output total number of successful files
        $success = count($details) - $errors;
        $total   = count($details);

        $this->comment(sprintf('%d/%d valid files', $success, $total));

        return min($errors, 1);
    }

    protected function displayJson(array $details)
    {
        $errors = 0;

        array_walk(
            $details,
            function (&$info) use (&$errors) {
                $info['file'] = (string) $info['file'];
                unset($info['template']);

                if (!$info['valid']) {
                    $info['message'] = $info['exception']->getMessage();
                    unset($info['exception']);
                    $errors++;
                }
            }
        );

        $this->line(json_encode($details, JSON_PRETTY_PRINT));

        return min($errors, 1);
    }

    protected function renderException(array $info)
    {
        $file      = $info['file'];
        $exception = $info['exception'];

        $line  = $exception->getTemplateLine();
        $lines = $this->getContext($info['template'], $line);

        if ($file) {
            $this->line(sprintf('<error>Fail</error> in %s (line %s)', $file, $line));
        } else {
            $this->line(sprintf('<error>Fail</error> (line %s)', $line));
        }

        foreach ($lines as $no => $code) {
            $this->line(
                sprintf(
                    "%s %-6s %s",
                    $no == $line ? '<error>>></error>' : '  ',
                    $no,
                    $code
                )
            );

            if ($no == $line) {
                $this->line(sprintf('<error>>> %s</error> ', $exception->getRawMessage()));
            }
        }
    }

    /**
     * Grabs the surrounding lines around the exception.
     *
     * @param string     $template Contents of Twig template.
     * @param string|int $line     Line where the exception occurred.
     * @param int        $context  Number of lines around the line where the exception occurred.
     *
     * @return array
     */
    protected function getContext($template, $line, $context = 3)
    {
        $lines    = explode("\n", $template);
        $position = max(0, $line - $context);
        $max      = min(count($lines), $line - 1 + $context);

        $result = array();

        while ($position < $max) {
            $result[$position + 1] = $lines[$position];
            $position++;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments()
    {
        return [
            [
                'filename',
                InputArgument::OPTIONAL,
                'Filename or directory to lint. If none supplied, all views will be checked.',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions()
    {
        return [
            [
                'check',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Lint multiple files or directories',
            ],
            [
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Format to ouput the result in. Supports `text` or `json`.',
                'text',
            ],
        ];
    }
}
