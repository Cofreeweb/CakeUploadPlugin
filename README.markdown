# Upload Plugin

The Upload Plugin is an attempt to sanely upload files using techniques garnered packages such as [MeioUpload](http://github.com/jrbasso/MeioUpload) , [UploadPack](http://github.com/szajbus/uploadpack) and [PHP documentation](http://php.net/manual/en/features.file-upload.php).

## Background

Media Plugin is too complicated, and it was a PITA to merge the latest updates into MeioUpload, so here I am, building yet another upload plugin. I'll build another in a month and call it "YAUP".


### Enable plugin

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

	CakePlugin::load('Upload', array( 'routes' => true));

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Configure
En app/Config/boostrap.php

Configure::load( 'uploads');

En app/Config/uploads.php

Marcar los tipos de uploads de la siguiente manera

$config ['Upload'] = array(
    'logo' => array(
        'type' => 'image',
        'template' => 'logo',
        'thumbail' => 'med',
        'config' => array(
            'thumbnailMethod' => 'php',
    				'path' => '{ROOT}webroot{DS}files{DS}photos{DS}',
    				'pathMethod' => 'random',
    				'thumbnailQuality' => 100,
    				'thumbnailSizes' => array(
    				    'min' => '50w',
                		'thm' => '100w',
                		'med' => '140w',
            ),
        )
    )
);

## Usage



## Behavior options:

* `pathMethod`: The method to use for file paths. This is appended to the `path` option below
  * Default: (string) `primaryKey`
  * Options:
    * flat: Does not create a path for each record. Files are moved to the value of the 'path' option
    * primaryKey: Path based upon the record's primaryKey is generated. Persists across a record
    * random: Random path is generated for each file upload. Does not persist across a record.
* `path`: A path relative to the `APP_PATH`. Should end in `{DS}`
  * Default: (string) `'{ROOT}webroot{DS}files{DS}{model}{DS}{field}{DS}'`
  * Tokens:
    * {ROOT}: Replaced by a `rootDir` option
    * {DS}: Replaced by a `DIRECTORY_SEPARATOR`
    * {model}: Replaced by the Model Alias
    * {field}: Replaced by the field name
    * {size}: Replaced by a zero-length string (the empty string) when used for the regular file upload path
    * {geometry}: Replaced by a zero-length string (the empty string) when used for the regular file upload path
* `fields`: An array of fields to use when uploading files
  * Default: (array) `array('dir' => 'dir', 'type' => 'type', 'size' => 'size')`
  * Options:
    * dir: Field to use for storing the directory
    * type: Field to use for storing the filetype
    * size: Field to use for storing the filesize
* `rootDir`: Root directory for moving images. Auto-prepended to `path` and `thumbnailPath` where necessary
  * Default (string) `ROOT . DS . APP_DIR . DS`
* `mimetypes`: Array of mimetypes to use for validation
  * Default: (array) empty
* `extensions`: Array of extensions to use for validation
  * Default: (array) empty
* `maxSize`: Max filesize in bytes for validation
  * Default: (int) `2097152`
* `minSize`: Minimum filesize in bytes for validation
  * Default: (int) `8`
* `maxHeight`: Maximum image height for validation
  * Default: (int) `0`
* `minHeight`: Minimum image height for validation
  * Default: (int) `0`
* `maxWidth`: Maximum image width for validation
  * Default: (int) `0`
* `minWidth`: Minimum image width for validation
  * Default: (int) `0`
* `deleteOnUpdate`: Whether to delete files when uploading new versions (potentially dangerous due to naming conflicts)
  * Default: (boolean) `false`
* `thumbnails`: Whether to create thumbnails or not
  * Default: (boolean) `true`
* `thumbnailMethod`: The method to use for resizing thumbnails
  * Default: (string) `imagick`
  * Options:
    * imagick: Uses the PHP `imagick` extension to generate thumbnails
    * php: Uses the built-in PHP methods (`GD` extension) to generate thumbnails
* `thumbnailName`: Naming style for a thumbnail
  * Default: `NULL`
  * Note: The tokens `{size}` and `{filename}` are both valid for naming and will be auto-replaced with the actual terms.
  * Note: As well, the extension of the file will be automatically added.
  * Note: When left unspecified, will be set to `{size}_{filename}` or `{filename}_{size}` depending upon the value of `thumbnailPrefixStyle`
* `thumbnailPath`: A path relative to the `rootDir` where thumbnails will be saved. Should end in `{DS}`. If not set, thumbnails will be saved at `path`.
  * Default: `NULL`
  * Tokens:
    * {ROOT}: Replaced by a `rootDir` option
    * {DS}: Replaced by a `DIRECTORY_SEPARATOR`
    * {model}: Replaced by the Model Alias
    * {field}: Replaced by the field name
    * {size}: Replaced by the size key specified by a given `thumbnailSize`
    * {geometry}: Replaced by the geometry value specified by a given `thumbnailSize`
* `thumbnailPrefixStyle`: Whether to prefix or suffix the style onto thumbnails
  * Default: (boolean) `true` prefix the thumbnail
  * Note that this overrides `thumbnailName` when `thumbnailName` is not specified in your config
* `thumbnailQuality`: Quality of thumbnails that will be generated, on a scale of 0-100
  * Default: (int) `75`
* `thumbnailSizes`: Array of thumbnail sizes, with the size-name mapping to a geometry
  * Default: (array) empty
* `thumbnailType`: Override the type of the generated thumbnail
  * Default: (mixed) `false` or `png` when the upload is a Media file
  * Options:
    * Any valid image type
* `mediaThumbnailType`: Override the type of the generated thumbnail for a non-image media (`pdfs`). Overrides `thumbnailType`
  * Default: (mixed) `png`
  * Options:
    * Any valid image type
* `saveDir`: Can be used to turn off saving the directory
  * Default: (boolean) `true`
  * Note: Because of the way in which the directory is saved, if you are using a `pathMethod` other than flat and you set `saveDir` to false, you may end up in situations where the file is in a location that you cannot predict. This is more of an issue for a `pathMethod` of `random` than `primaryKey`, but keep this in mind when fiddling with this option

## Thumbnail Sizes and Styles

Styles are the definition of thumbnails that will be generated for original image. You can define as many as you want.

	<?php
	class User extends AppModel {
		public $name = 'User';
		public $actsAs = array(
			'Upload.Upload' => array(
				'photo' => array(
					'thumbnailSizes' => array(
						'big' => '200x200',
						'small' => '120x120'
						'thumb' => '80x80'
					)
				)
			)
		);
	}


Styles only apply to images of the following types:

* image/bmp
* image/gif
* image/jpeg
* image/pjpeg
* image/png
* image/vnd.microsoft.icon
* image/x-icon

You can specify any of the following resize modes for your sizes:

* `100x80` - resize for best fit into these dimensions, with overlapping edges trimmed if original aspect ratio differs
* `[100x80]` - resize to fit these dimensions, with white banding if original aspect ratio differs
* `100w` - maintain original aspect ratio, resize to 100 pixels wide
* `80h` - maintain original aspect ratio, resize to 80 pixels high
* `80l` - maintain original aspect ratio, resize so that longest side is 80 pixels

## Validation rules

By default, no validation rules are attached to the model. One must explicitly attach each rule if needed. Rules not referring to PHP upload errors are configurable but fallback to the behavior configuration.

#### isUnderPhpSizeLimit

Check that the file does not exceed the max file size specified by PHP

	public $validate = array(
		'photo' => array(
			'rule' => 'isUnderPhpSizeLimit',
			'message' => 'File exceeds upload filesize limit'
		)
	);

#### isUnderFormSizeLimit

Check that the file does not exceed the max file size specified in the HTML Form

	public $validate = array(
		'photo' => array(
			'rule' => 'isUnderFormSizeLimit',
			'message' => 'File exceeds form upload filesize limit'
		)
	);

#### isCompletedUpload

Check that the file was completely uploaded

	public $validate = array(
		'photo' => array(
			'rule' => 'isCompletedUpload',
			'message' => 'File was not successfully uploaded'
		)
	);

#### isFileUpload

Check that a file was uploaded

	public $validate = array(
		'photo' => array(
			'rule' => 'isFileUpload',
			'message' => 'File was missing from submission'
		)
	);

#### tempDirExists

Check that the PHP temporary directory is missing

	public $validate = array(
		'photo' => array(
			'rule' => 'tempDirExists',
			'message' => 'The system temporary directory is missing'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('tempDirExists', false),
			'message' => 'The system temporary directory is missing'
		)
	);

#### isSuccessfulWrite

Check that the file was successfully written to the server

	public $validate = array(
		'photo' => array(
			'rule' => 'isSuccessfulWrite',
			'message' => 'File was unsuccessfully written to the server'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isSuccessfulWrite', false),
			'message' => 'File was unsuccessfully written to the server'
		)
	);

#### noPhpExtensionErrors

Check that a PHP extension did not cause an error

	public $validate = array(
		'photo' => array(
			'rule' => 'noPhpExtensionErrors',
			'message' => 'File was not uploaded because of a faulty PHP extension'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('noPhpExtensionErrors', 1024, false),
			'message' => 'File was not uploaded because of a faulty PHP extension'
		)
	);

#### isValidMimeType

Check that the file is of a valid mimetype

	public $validate = array(
		'photo' => array(
			'rule' => array('isValidMimeType', array('valid/mimetypes', 'array/here')),
			'message' => 'File is of an invalid mimetype'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isValidMimeType', array('valid/mimetypes', 'array/here'), false),
			'message' => 'File is of an invalid mimetype'
		)
	);

#### isWritable

Check that the upload directory is writable

	public $validate = array(
		'photo' => array(
			'rule' => array('isWritable'),
			'message' => 'File upload directory was not writable'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isWritable', false),
			'message' => 'File upload directory was not writable'
		)
	);

#### isValidDir

Check that the upload directory exists

	public $validate = array(
		'photo' => array(
			'rule' => array('isValidDir'),
			'message' => 'File upload directory does not exist'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isValidDir', false),
			'message' => 'File upload directory does not exist'
		)
	);

#### isBelowMaxSize

Check that the file is below the maximum file upload size (checked in bytes)

	public $validate = array(
		'photo' => array(
			'rule' => array('isBelowMaxSize', 1024),
			'message' => 'File is larger than the maximum filesize'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isBelowMaxSize', 1024, false),
			'message' => 'File is larger than the maximum filesize'
		)
	);

#### isAboveMinSize

Check that the file is above the minimum file upload size (checked in bytes)

	public $validate = array(
		'photo' => array(
			'rule' => array('isAboveMinSize', 1024),
			'message' => 'File is below the mimimum filesize'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isAboveMinSize', 1024, false),
			'message' => 'File is below the mimimum filesize'
		)
	);

#### isValidExtension

Check that the file has a valid extension

	public $validate = array(
		'photo' => array(
			'rule' => array('isValidExtension', array('ext', 'array', 'here')),
			'message' => 'File has an invalid extension'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isValidExtension', array('ext', 'array', 'here'), false),
			'message' => 'File has an invalid extension'
		)
	);

#### isAboveMinHeight

Check that the file is above the minimum height requirement (checked in pixels)

	public $validate = array(
		'photo' => array(
			'rule' => array('isAboveMinHeight' 150),
			'message' => 'File is below the minimum height'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isAboveMinHeight', 150, false),
			'message' => 'File is below the minimum height'
		)
	);

#### isBelowMaxHeight

Check that the file is below the maximum height requirement (checked in pixels)

	public $validate = array(
		'photo' => array(
			'rule' => array('isBelowMaxHeight', 150),
			'message' => 'File is above the maximum height'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isBelowMaxHeight', 150, false),
			'message' => 'File is above the maximum height'
		)
	);

#### isAboveMinWidth

Check that the file is above the minimum width requirement (checked in pixels)

	public $validate = array(
		'photo' => array(
			'rule' => array('isAboveMinWidth', 150),
			'message' => 'File is below the minimum width'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isAboveMinWidth', 150, false),
			'message' => 'File is below the minimum width'
		)
	);

#### isBelowMaxWidth

Check that the file is below the maximum width requirement (checked in pixels)
	public $validate = array(
		'photo' => array(
			'rule' => array('isBelowMaxWidth', 150),
			'message' => 'File is above the maximum width'
		)
	);

If the argument `$requireUpload` is passed, we can skip this check when a file is not uploaded:

	public $validate = array(
		'photo' => array(
			'rule' => array('isBelowMaxWidth', 150, false),
			'message' => 'File is above the maximum width'
		)
	);

## License

Copyright (c) 2010-2012 Jose Diaz-Gonzalez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.