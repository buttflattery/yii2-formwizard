# Yii2-FormWizard (v1.0)

### What is this repository for? ###

A Yii2 plugin used for creating stepped form or form wizard using `yii\widgets\ActiveForm` and `\yii\db\ActiveRecord`, it uses [smart wizard library](https://github.com/mstratman/jQuery-Smart-Wizard) for creating the form interface that uses 3 builtin and 2 extra themes, moreover you can also create your own customized theme too.

_Note : It uses limited features of the jquery plugin SmartWizard that suite the needs of the ActiveForm validation so not all options in the javascript plugin library are allowed to be changed or customized from within this plugin._


### External Libraries Used ###
* [Smart Wizard](https://github.com/mstratman/jQuery-Smart-Wizard).
* [jQuery v2.2.4](https://jquery.com/download/)
* [Bootstrap v3.3.7](https://getbootstrap.com/docs/3.3/)

### How do I get set up? ###

use composer to install the extension 

```
php composer.phar require  buttflattery/yii2-formwizard "@dev" 
```

or add into the `composer.json` file under `require` section

```composer
"buttflattery/yii2-formwizard":"@dev"
```

### Usage 1 Carousel Mode ###
```php
<?php
        echo Videowall::widget([
            'videoTagOptions' => [
                'height' => "500",
            ],
            'wallType' => Videowall::TYPE_CAROUSEL,
            'videos' => [
                [
                    "src" => "/PATH/TO/VIDEO.MP4",
                    "mime" => 'video/mime',
                    "poster" => "/PATH/TO/POSTER.JPG",
                    "title" => "Sweet Sexy Savage",
                ], [
                    "src" => '/PATH/TO/VIDEO.MP4',
                    'poster' => '/PATH/TO/POSTER.JPG',
                    'mime' => 'video/mime',
                    'title' => 'Video 2',
                ],
            ]
        ]);
```

### Usage 2 Thumbnail Mode ###

Thumbnail mode for compact display of the video files along with filter option using the select2 dropdown.

```php
<?php
        echo Videowall::widget([
            'videoTagOptions' => [
                'height' => "500",
            ],
            'wallType' => Videowall::TYPE_THUMB,
            'videos' => [
                [
                    "src" => "/PATH/TO/VIDEO.MP4",
                    "mime" => 'video/mime',
                    "poster" => "/PATH/TO/POSTER.JPG",
                    "title" => "Sweet Sexy Savage",
                ], [
                    "src" => '/PATH/TO/VIDEO.MP4',
                    'poster' => '/PATH/TO/POSTER.JPG',
                    'mime' => 'video/mime',
                    'title' => 'Video 2',
                ],
            ]
        ]);
```

### Usage 3 Playlist Mode ##
Bonus Feature for disaplying th playlists inside the video wall. 

```php 
<?php
        echo Videowall::widget([
            'wallType' => Videowall::TYPE_PLAYLIST,
            'videoTagOptions' => [
                'width' => "800",
                'height' => "600",
            ],
            'playlists' => [
                [
                    'name' => 'Sweet Sexy Savage',
                    'cover' => '/PATH/TO/POSTER.JPG',
                    'videos' => [
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                    ],
                ],
                [
                    'name' => 'Hope',
                    'cover' => '/PATH/TO/POSTER.JPG',
                    'videos' => [
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ],
                        [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ], [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ], [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ], [
                            'src' => '/PATH/TO/VIDEO.MP4',
                            'poster' => '/PATH/TO/POSTER.JPG',
                            'mime' => 'video/mime',
                            'title' => 'Video title',
                        ]],
                ],
            ]
        ]);
```

### Available Options ###

- `videoTagOptions (array)`: Attributes for the the default video tag used by videoJs to initialize the player. You can pass the following options for the video tag.

    - `class` : Html class for the the video tag.
    - `width` : Width for the video tag.
    - `height` : Height for the video tag.
    - `setupOptions` : The setup options for the video tag used inside the `data-setup` attribute, below are few of the options commonly used by the videoJS player.
        - `controls` : Default value `true`.
        - `autoplay` : Default value `true`.
        - `preload` : Default value `auto`.
    - `poster (path)` : Path for the default poster for the video tag, use path relative to the web directory.
- `wallType (string)` : 
    - `Videowall::TYPE_CAROUSEL` (carousel)
    - `Videowall::TYPE_THUMBNAIL` (thumbnail)
    - `Videowall::TYPE_PLAYLIST` (playlist)
- `slideShowDelay (milliseconds)` : integer, default value 2000 ms.
- `videoWallContainer (string)`: container class name for the video wall, default class `video-wall-container`.
- `containerId (string)` : container id for the video wall slides, default id prefix `video-wall-slides`.
- `containerClass (string)` : container class name for the video wall  slides, default class `slides-container`.
- `helpImproveVideoJs (boolean)` : `true` or `false` used by the videoJS player.
- `thumbPageSize (int)` : page size for the thumbnails mode, default value `15`.
- `playlistPageSize (int)` : page size for the playlist model, default value `8`.
- `loadBootstrapAssets (boolean)` : select if plugin should load the bootstrap assets or use the globally registered yii bootstrap assets, by default this option is `false` and Yii2 default assets bundle is used.
- `bootstrapCssSource (url)` : url to the bootstrap css file for the plugin to load, this option is effective when you have `"loadBootstrapAssets"=>true`.
- `bootstrapJsSource (url)` : url to the boootstrap js file for the plugin to load, this option is effective when you have `"loadBootstrapAssets"=>true`.
- `select2Defaults (array)` : default options used for rendring the kartik-v\yii2-select2 plugin, you override them and add you own too look into documentation for the options
    - `allowClear`: default value `true`.
    - `theme` : default value `default`.
    - `width` : default value `100%`.
    - `placeholder` : default value `Search Videos`.
    - `minimumInputlength` : default value `2`.
    - `dropdownCssClass` : default value `bigdrop`.
- `openOnStart (boolean)` : Select if the video wall is open when player is initialized, default value is `true`.
- `callback (function)`: a callback function called by the plugin after initialized.
- `clientEvents (array)` : an array of client events supported by the plugin, you can see the plugin [documentation](https://github.com/buttflattery/idows-videojs-videowall) for the supported events, you can use them like below
```
"pluginEvents"=>[
    'onBeforeNext' => 'function(event,dataObj){console.log(event);}',
]
```

### Who do I talk to? ###

* buttflattery@hotmail.com
* omeraslam@idowstech.com