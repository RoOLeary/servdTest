<?php 

use craft\elements\Entry;
use craft\helpers\UrlHelper;

return [
    'endpoints' => [
        'api/homepage.json' => function() {
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
                        'blocks' => $bodyBlocks,
                        'jsonUrl' => UrlHelper::url("homepage.json"),
                   ];
                },
            ];
        },
        'api/articles.json' => function() {
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'articles'],
                'transformer' => function(Entry $entry) {
                    return [
                        'title' => $entry->title,
                        'url' => $entry->url,
                        'jsonUrl' => UrlHelper::url("articles/{$entry->slug}.json"),
                        'summary' => $entry->summary,
                    ];
                },
            ];
        },
        'api/articles/<slug:\d+>.json' => function($slug) {
            return [
                'elementType' => Entry::class,
                'criteria' => ['slug' => $slug],
                'one' => true,
                'transformer' => function(Entry $entry) {
                    return [
                        'title' => $entry->title,
                        'url' => $entry->url,
                        // 'summary' => $entry->summary,
                        // 'body' => $entry->body,
                    ];
                },
            ];
        },
    ]
];