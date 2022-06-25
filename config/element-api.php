<?php 

use craft\elements\Entry;
use craft\helpers\UrlHelper;

return [
    'endpoints' => [
        'homepage.json' => function() {
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'homepage'],
                'transformer' => function(Entry $entry) {
                    return [
                        'title' => $entry->title,
                        'homeIntro' => $entry->homeIntro,
                        'jsonUrl' => UrlHelper::url("news/{$entry->id}.json"),
                   ];
                },
            ];
        },
    ]
];