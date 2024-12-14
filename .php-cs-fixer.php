<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

$finder = \PhpCsFixer\Finder::create()
    ->name('*.php')
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
;

$header = <<<EOF
    Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
    See LICENSE.txt for license details.

    Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
    EOF;

$config = new \PhpCsFixer\Config();
return $config
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP81Migration' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'octal_notation' => false,
        'trim_array_spaces' => true,
        'phpdoc_order' => true,
        'ordered_imports' => true,
        'new_with_parentheses' => true,
        'method_chaining_indentation' => true,
        'no_unused_imports' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'blank_line_after_opening_tag' => false,
        'header_comment' => [
            'header' => $header,
            'comment_type' => 'PHPDoc',
            'location' => 'after_open',
            'separate' => 'bottom',
        ],
    ]);
