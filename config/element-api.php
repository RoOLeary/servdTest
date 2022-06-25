<?php 

use craft\elements\Entry;
use craft\helpers\UrlHelper;

return [
    'endpoints' => [
        'homepage.json' => function() {
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'homepage'],
                'transformer' => function(Entry $entry) {

                    $bodyBlocks = [];
                    foreach ($entry->blocks->all() as $block) {
                        switch ($block->type->handle) {
                            case 'componentOne':
                                $bodyBlocks[] = [
                                    'title' => $block->heading,
                                ];
                                break;
                             case 'componentTwo':
                                $bodyBlocks[] = [
                                    'title' => $block->heading,
                                ];
                                break;
                        }
                    }

                    return [
                        'title' => $entry->title,
                        'homeIntro' => $entry->homeIntro,
                        'balls' => 'sack',
                        'matrix' => $bodyBlocks,
                        'jsonUrl' => UrlHelper::url("homepage.json"),
                   ];
                },
            ];
        },
    ]
];