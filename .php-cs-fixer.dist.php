<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config)
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
            ->exclude(['public', 'storage', 'vendor', 'views']),
    )
    ->setRules([
        '@PhpCsFixer' => true,
        'blank_line_before_statement' => [
            'statements' => ['foreach', 'if', 'return', 'switch', 'while', 'try', 'do', 'for'],
        ],
        'global_namespace_import' => [
            'import_constants' => false,
            'import_functions' => false,
            'import_classes' => true,
        ],
        'heredoc_indentation' => true,
        'multiline_whitespace_before_semicolons' => true,
        'new_with_parentheses' => [
            'named_class' => false,
        ],
        'operator_linebreak' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'method:__invoke',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'method_public',
                'method_protected',
                'method_private',
                'magic',
            ],
        ],
        'phpdoc_to_comment' => [
            'ignored_tags' => ['use', 'lang', 'var'],
        ],
        'single_line_empty_body' => true,
        'unary_operator_spaces' => [
            'only_dec_inc' => true,
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setUsingCache(false);
