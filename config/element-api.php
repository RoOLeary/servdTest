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
                    foreach ($entry->pageBlocks->all() as $block) {
                        switch ($block->type->handle) {
                            case 'hero':
                                $bodyBlocks[] = [
                                    'blockType' => 'hero',
                                    'uid' => $block->uid,
                                    'eyebrow' => $block->eyebrow,
                                    'heading' => $block->heading,
                                    'subHeading' => $block->subHeading
                                ];
                                break;
                            case 'header':
                            $bodyBlocks[] = [
                                'uid' => $block->uid,
                                'blockType' => 'header',
                                'headline' => $block->headline,
                                ];
                                break;
                            case 'faq':
                                $faqRows = [];
                                foreach($block->faqs->all() as $row){
                                    $faqRows[] = [
                                        'question' => $row->question,
                                        'answer' => $row->answer,
                                    ];
                                }
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'faq',
                                    'faqHeading' => $block->faqHeading,
                                    'faqLeadtext' => $block->faqLeadtext,
                                    'faqs' => $faqRows
                                ];
                                break;
                            
                            // //     $bodyBlocks[] = [
                            // //         'heading' => $block->heading,
                            // //         'speakersIntro' => $block->speakersIntro,
                            // //         // 'speakers' => $selectedSpeakers
                            // //     ];
                            // //     break;
                            case 'video':
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'video',
                                    'videoTitle' => $block->videoTitle,
                                    'videoEmbedCode' => $block->videoEmbedCode,
                                ];
                                break;
                            case 'imageSlider':
                                $SuperTableRows = [];
                                foreach ($block->sliderMatrix->all() as $row){
                                    $SuperTableRows[] = [
                                        'textSub' => $row->textSub,
                                        'textHeading' => $row->textHeading,
                                        'textBackground' => $row->textBackground,
                                        'slideImage' => $row->slideImage,
                                        'slideColor' => $row->slideColor->value,
                                    ];
                                }
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'imageSlider',
                                    'sliderTitle' => $block->sliderTitle,
                                    'sliderMatrix' => $SuperTableRows,
                                ];
                                break;
                            case 'text':
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'text',
                                    'headline' => $block->headline,
                                    'articleBody' => $block->articleBody,
                                ];
                                break;
                            case 'textVisual':

                                $TVButtons = [];
                                foreach ($block->textVisualButtons->all() as $row){
                                    $TVButtons[] = [
                                        'linkId' => $row->linkId,
                                        'linkText' => $row->linkText,
                                        'linkUrl' => $row->linkUrl,
                                        'isExternal' => $row->target
                                    ];
                                }

                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'textVisual',
                                    'title' => $block->textVisualTitle,
                                    'articleBody' => $block->textVisualContent,
                                    'image' => $block->textVisualImage,
                                    'buttons' => $TVButtons,
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
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'articles'],
                'elementsPerPage' => 10,
                'transformer' => function(Entry $entry) {

                    $articleCategory = $entry->category->one()->id;
                    $relatedArticles = Entry::find()
                        ->section('articles')
                        ->relatedTo($articleCategory)
                        ->limit(10)
                        ->all();

                    return [
                        'title' => $entry->headline,
                        'category' => $relatedArticles,
                        'catId' => $articleCategory,
                        'jsonUrl' => UrlHelper::url("/api/articles/{$entry->slug}.json"),
                    
                    ];
                },
            ];
        },
        'api/recipes.json' => function() {
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'recipes'],
                'elementsPerPage' => 10,
                'transformer' => function(Entry $entry) {

                    return [
                        'slug' => $entry->slug,
                        'title' => $entry->headline,
                        'articleBody' => $entry->articleBody,
                        'jsonUrl' => UrlHelper::url("/api/recipes.json"),
                    
                    ];
                },
            ];
        },

        'api/recipes/<slug:{slug}>.json' => function($slug) {
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'articles', 'relatedTo' => [ 'targetElement' => $slug ] ],
                'elementsPerPage' => 10,
                'transformer' => function(Entry $entry) {

                    return [
                        'slug' => $entry->slug,
                        'title' => $entry->headline,
                        'articleBody' => $entry->articleBody,
                        'jsonUrl' => UrlHelper::url("/api/recipes/{$entry->slug}.json"),
                    
                    ];
                },
            ];
        },

        'api/category/tech.json' => function() {
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'articles', 'relatedTo' => 280],
                'elementsPerPage' => 10,
                'transformer' => function(Entry $entry) {
                    
                    $articleCategory = $entry->category->one()->id;
                    $relatedArticles = Entry::find()
                        ->section('articles')
                        ->relatedTo($articleCategory)
                        ->limit(10)
                        ->all();

                    return [
                        'slug' => $entry->slug,
                        'title' => $entry->headline,
                        'subHeadline' => $entry->subHeadline,
                        'body' => $entry->articleBody,
                        'category' => $relatedArticles,
                        'catId' => $articleCategory,
                        'jsonUrl' => UrlHelper::url("/api/category/{$entry->slug}.json"),
                    
                    ];
                },
            ];
        },

        'api/category/<slug:{slug}>.json' => function($slug) {
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            $slugQuery = 83;
            // s$categoryId = $slugQuery->catId;

            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'articles', 'relatedTo' => [ 'targetElement' => $slug ] ],
                'elementsPerPage' => 10,
                'transformer' => function(Entry $entry) {

                    $articleCategory = $entry->category->one()->id;
                    $relatedArticles = Entry::find()
                        ->section('articles')
                        ->relatedTo($articleCategory)
                        ->limit(10)
                        ->all();

                    return [
                        'slug' => $entry->slug,
                        'title' => $entry->headline,
                        'subHeadline' => $entry->subHeadline,
                        'body' => $entry->articleBody,
                        'category' => $relatedArticles,
                        'catId' => $articleCategory,
                        'jsonUrl' => UrlHelper::url("/api/category/{$entry->slug}.json"),
                    
                    ];
                },
            ];
        },

        'api/articles/<slug:{slug}>.json' => function($slug) {
            return [
                'elementType' => Entry::class,
                'criteria' => ['slug' => $slug],
                'one' => true,
                'transformer' => function(Entry $entry) {
                    
                    $bodyBlocks = [];
                    foreach ($entry->pageBlocks->all() as $block) {
                        switch ($block->type->handle) {
                            case 'hero':
                                $bodyBlocks[] = [
                                    'blockType' => 'hero',
                                    'uid' => $block->uid,
                                    'eyebrow' => $block->eyebrow,
                                    'heading' => $block->heading,
                                    'subHeading' => $block->subHeading
                                ];
                                break;
                            case 'header':
                            $bodyBlocks[] = [
                                'uid' => $block->uid,
                                'blockType' => 'header',
                                'headline' => $block->headline,
                                ];
                                break;
                            case 'faq':
                                $faqRows = [];
                                foreach($block->faqs->all() as $row){
                                    $faqRows[] = [
                                        'question' => $row->question,
                                        'answer' => $row->answer,
                                    ];
                                }
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'faq',
                                    'faqHeading' => $block->faqHeading,
                                    'faqLeadtext' => $block->faqLeadtext,
                                    'faqs' => $faqRows
                                ];
                                break;
                            
                            // // case 'speakers':
                            // //     // $selectedSpeakers = [];
                            // //     $relatedCat = $block->speakerCategory->one()->id;
                                
                            // //     // $blockSpeakers = Entry::find()
                            // //     //     ->section('speakers')
                            // //     //     ->relatedTo($relatedCat)
                            // //     //     ->limit(10)
                            // //     //     ->all();

                                
                            // //     // $selectedSpeakers = [];
                            // //     //     foreach($blockSpeakers as $spkr){
                            // //     //     $selectedSpeakers[] = [
                            // //     //         'speakerName' => $spkr->speakerName
                            // //     //     ];
                            // //     // }

                            // //     $bodyBlocks[] = [
                            // //         'heading' => $block->heading,
                            // //         'speakersIntro' => $block->speakersIntro,
                            // //         // 'speakers' => $selectedSpeakers
                            // //     ];
                            // //     break;
                            case 'video':
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'video',
                                    'videoTitle' => $block->videoTitle,
                                    'videoEmbedCode' => $block->videoEmbedCode,
                                ];
                                break;
                            case 'imageSlider':
                                $SuperTableRows = [];
                                foreach ($block->sliderMatrix->all() as $row){
                                    $SuperTableRows[] = [
                                        'textSub' => $row->textSub,
                                        'textHeading' => $row->textHeading,
                                        'textBackground' => $row->textBackground,
                                        'slideImage' => $row->slideImage,
                                        'slideColor' => $row->slideColor->value,
                                    ];
                                }
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'imageSlider',
                                    'sliderTitle' => $block->sliderTitle,
                                    'sliderMatrix' => $SuperTableRows,
                                ];
                                break;
                            // case 'text':
                            //     $bodyBlocks[] = [
                            //         'uid' => $block->uid,
                            //         'blockType' => 'text',
                            //         'headline' => $block->headline,
                            //         'articleBody' => $block->articleBody,
                            //     ];
                            //     break;
                            // case 'textVisual':

                            //     $TVButtons = [];
                            //     foreach ($block->textVisualButtons->all() as $row){
                            //         $TVButtons[] = [
                            //             'linkId' => $row->linkId,
                            //             'linkText' => $row->linkText,
                            //             'linkUrl' => $row->linkUrl,
                            //             'isExternal' => $row->target
                            //         ];
                            //     }

                            //     $bodyBlocks[] = [
                            //         'uid' => $block->uid,
                            //         'blockType' => 'textVisual',
                            //         'title' => $block->textVisualTitle,
                            //         'articleBody' => $block->textVisualContent,
                            //         'image' => $block->textVisualImage,
                            //         'buttons' => $TVButtons,
                            //     ];
                            //     break;
                        }
                    }

                    return [
                        'title' => $entry->title,
                        'headline' => $entry->headline,
                        'subHeadline' => $entry->subHeadline,
                        'body' => $entry->articleBody,
                        'category' => $entry->category->one()->slug,
                        'related' => $entry->manualRelatedEntries->one()->title ? $entry->manualRelatedEntries->one()->title : [],
                        'blocks' => $bodyBlocks,
                        'jsonUrl' => UrlHelper::url("/api/articles/{$entry->slug}.json"),
                       
                    ];
                },
            ];
        },

        'api/pages.json' => function() {
            \Craft::$app->response->headers->set('Access-Control-Allow-Origin', '*');
            return [
                'elementType' => Entry::class,
                'criteria' => ['section' => 'pages'],
                'elementsPerPage' => 20,
                'transformer' => function(Entry $entry) {

                    return [
                        'title' => $entry->headline,
                        'jsonUrl' => UrlHelper::url("api/pages/{$entry->slug}.json"),
                    ];
                },
            ];
        },

        'api/pages/<slug:{slug}>.json' => function($slug) {
            return [
                'elementType' => Entry::class,
                'criteria' => ['slug' => $slug],
                'one' => true,
                'transformer' => function(Entry $entry) {
                    
                    $bodyBlocks = [];
                    foreach ($entry->pageBlocks->all() as $block) {
                        switch ($block->type->handle) {
                            case 'hero':
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'hero',
                                    'eyebrow' => $block->eyebrow,
                                    'heading' => $block->heading,
                                    'subHeading' => $block->subHeading
                                ];
                            break;
                            case 'header':
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'header',
                                    'headline' => $block->headline,
                                ];
                            break;
                            case 'faq':
                                $faqRows = [];
                                foreach($block->faqs->all() as $row){
                                    $faqRows[] = [
                                        'question' => $row->question,
                                        'answer' => $row->answer,
                                    ];
                                }
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'faq',
                                    'faqHeading' => $block->faqHeading,
                                    'faqLeadtext' => $block->faqLeadtext,
                                    'faqs' => $faqRows
                                ];
                            break;
                            case 'projects':
                                // $relatedCat = $block->speakerCategory->one()->id;
                                
                                $blockProjects = Entry::find()
                                    ->section('projects')
                                    // ->relatedTo($relatedCat)
                                    ->limit(10)
                                    ->all();

                                
                                $selectedProjects = [];
                                    foreach($blockProjects as $proj){
                                    $selectedProjects[] = [
                                        'projectName' => $proj->projectName,
                                        'projectDescription' => $proj->projectDescription,
                                        'projectImage' => $proj->projectImage,
                                    ];
                                }

                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'projects',
                                    'projectsHeading' => $block->projectsHeading,
                                    'projectsLeadText' => $block->projectsLeadText,
                                    'projects' => $selectedProjects
                                    
                                ];
                                break;
                            // case 'speakers':
                            //     // $selectedSpeakers = [];
                            //     $relatedCat = $block->speakerCategory->one()->id;
                                
                            //     // $blockSpeakers = Entry::find()
                            //     //     ->section('speakers')
                            //     //     ->relatedTo($relatedCat)
                            //     //     ->limit(10)
                            //     //     ->all();

                                
                            //     // $selectedSpeakers = [];
                            //     //     foreach($blockSpeakers as $spkr){
                            //     //     $selectedSpeakers[] = [
                            //     //         'speakerName' => $spkr->speakerName
                            //     //     ];
                            //     // }

                            //     $bodyBlocks[] = [
                            //         'heading' => $block->heading,
                            //         'speakersIntro' => $block->speakersIntro,
                            //         // 'speakers' => $selectedSpeakers
                            //     ];
                            //     break;
                            case 'video':
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'video',
                                    'videoTitle' => $block->videoTitle,
                                    'videoEmbedCode' => $block->videoEmbedCode,
                                ];
                            break;
                            case 'imageSlider':
                                $SuperTableRows = [];
                                foreach ($block->sliderMatrix->all() as $row){
                                    $SuperTableRows[] = [
                                        'textSub' => $row->textSub,
                                        'textHeading' => $row->textHeading,
                                        'textBackground' => $row->textBackground,
                                        'slideImage' => $row->slideImage,
                                        'slideColor' => $row->slideColor->value,
                                    ];
                                }
                                $bodyBlocks[] = [
                                    'uid' => $block->uid,
                                    'blockType' => 'imageSlider',
                                    'sliderTitle' => $block->sliderTitle,
                                    'sliderMatrix' => $SuperTableRows,
                                ];
                            break;
                            // case 'text':
                            //     $bodyBlocks[] = [
                            //         'uid' => $block->uid,
                            //         'blockType' => 'text',
                            //         'headline' => $block->headline,
                            //         'articleBody' => $block->articleBody,
                            //     ];
                            // break;
                            // case 'textVisual':

                            //     $TVButtons = [];
                            //     foreach ($block->textVisualButtons->all() as $row){
                            //         $TVButtons[] = [
                            //             'linkId' => $row->linkId,
                            //             'linkText' => $row->linkText,
                            //             'linkUrl' => $row->linkUrl,
                            //             'isExternal' => $row->target
                            //         ];
                            //     }
                            //     $bodyBlocks[] = [
                            //         'uid' => $block->uid,
                            //         'blockType' => 'textVisual',
                            //         'title' => $block->textVisualTitle,
                            //         'articleBody' => $block->textVisualContent,
                            //         'image' => $block->textVisualImage,
                            //         'buttons' => $TVButtons,
                            //     ];
                            // break;
                        }
                    }

                    return [
                        'title' => $entry->title,
                        'pageBlocks' => $bodyBlocks, 
                        'jsonUrl' => UrlHelper::url("api/pages/{$entry->slug}.json"),           
                    ];
                },
            ];
        },
    ]
];