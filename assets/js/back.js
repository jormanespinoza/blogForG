import '../css/global.scss';
import '../css/back.css';

const $ = require('jquery');
const getSlug = require('speakingurl');
const alphabeticPattern = '^[a-zA-Z\b]+$';
const alphanumericPattern = '^[a-zA-Z0-9\b]+$';
const disableCharsOnKeypress = (event, pattern) => {
    const regex = new RegExp(pattern);
    if (!regex.test(event.key)) return false;
};

import 'bootstrap';
import '../css/vendor/cropper.min.css';
import '../../public/bundles/prestaimage/css/cropper.css';
import './utils/user_form.js';
import './vendor/cropper.min.js';
import Cropper from '../../public/bundles/prestaimage/js/cropper.js';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import '@ckeditor/ckeditor5-build-classic/build/translations/es.js';

$('.only-alphabetic-chars').on('keypress', (event) => disableCharsOnKeypress(event, alphabeticPattern));

$('.only-alphanumeric-chars').on('keypress', (event) => disableCharsOnKeypress(event, alphanumericPattern));

const generateCKEditor = (textarea) => {
    ClassicEditor.create(document.querySelector(textarea), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'blockQuote', 'bulletedList', 'numberedList', 'undo', 'redo'],
        language: 'es'
    })
        .then((editor) => {
            window.editor = editor;
        })
        .catch((err) => {
            console.error(err.stack);
        });
};

$(() => {
    // Speakingurl
    $('#post_title').on('keyup blur', (event) => {
        const slug = getSlug($(event.currentTarget).val());
        $('#post_url').val(slug);
    });

    // Cropper JS
    $('.cropper').each((index, element) => new Cropper($(element)));

    // Cropper Event
    $('.cropper-local button').on('click', (event) => {
        event.preventDefault();
        const maxWidth = $(event.currentTarget).parent().parent().parent().data('max-width');
        const maxHeight = $(event.currentTarget).parent().parent().parent().data('max-height');
        const aspectRatio = maxWidth / maxHeight;

        $.fn.cropper.setDefaults({
            aspectRatio: aspectRatio,
            zoomable: false,
            cropBoxResizable: false,
            movable: false,
            dragCrop: false,
            zoomable: false,
            dragMode: 'none'
        });
    });

    // CKEditor
    $('.applyCKEditor').each((index, element) => {
        generateCKEditor(`#${$(element).attr('id')}`);
    });

    // Collaspe
    $('.collapse').collapse();
});
