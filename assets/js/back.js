import '../css/global.scss';
import '../css/back.css';

const $ = require('jquery');
const getSlug = require('speakingurl');

import 'bootstrap';
import '../css/vendor/cropper.min.css';
import '../../public/bundles/prestaimage/css/cropper.css';
import './vendor/cropper.min.js';
import Cropper from '../../public/bundles/prestaimage/js/cropper.js';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import '@ckeditor/ckeditor5-build-classic/build/translations/es.js';

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
});
