<?php

return PhpCsFixer\Config::create()
	->setRules([
        '@PSR2' => true,
        //'@Symfony' => true,
        'indentation_type' => true,
        'no_unneeded_curly_braces' => true,
       // 'class_attributes_separation' => true,
        'concat_space' => ['spacing' => 'one'],
        'class_definition' => [
        			'multiLineExtendsEachSingleLine' => true,
        			'singleItemSingleLine' => false,
        			'singleLine' => false
        			],

        	'no_leading_import_slash' => true,

    ])
    ->setIndent("\t")
    ->setLineEnding("\n")
;
