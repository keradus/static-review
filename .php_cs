<?php

$config = Symfony\CS\Config\Config::create();

$config->finder(Symfony\CS\Finder\DefaultFinder::create()->in(__DIR__))
       ->fixers([
           'psr0',
           'encoding',
           'short_tag',
           'braces',
           'elseif',
           'eof_ending',
           'function_declaration',
           'indentation',
           'linefeed',
           'lowercase_constants',
           'lowercase_keywords',
           'php_closing_tag',
           'trailing_spaces',
           'visibility',
           'extra_empty_lines',
           'new_with_braces',
           'object_operator',
           'operators_spaces',
           'phpdoc_params',
           'return',
           'spaces_cast',
           'standardize_not_equal',
           'ternary_spaces',
           'unused_use',
           'whitespacy_lines',
           'concat_with_spaces',
           'ordered_use',
           'short_array_syntax',
           'strict',
        ]);

return $config;
