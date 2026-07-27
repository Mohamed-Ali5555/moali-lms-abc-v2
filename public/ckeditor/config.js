/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};




CKEDITOR.editorConfig = function(config) {
    config.extraPlugins = 'mathjax';
    config.mathJaxLib = 'https://cdn.jsdelivr.net/npm/mathjax@2/MathJax.js?config=TeX-AMS_HTML';
    config.allowedContent = true;

};
