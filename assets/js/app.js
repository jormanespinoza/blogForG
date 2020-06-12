import '../css/global.scss';
import '../css/app.css';

const $ = require('jquery');

import 'bootstrap';

import './vendor/jquery.validate.js';

const countComments = (postId) => {
    $.ajax({
        url: '/count-comments',
        type: 'POST',
        data: `post=${postId}`,
        success: (response) => {
            $('.post-comments-count').text(response);
        }
    });
};

const initCommentFrm = () => {
    if ($('#comment-frm')[0]) {
        $('#comment-frm').validate({
            rules: {
                comment: {
                    required: true
                }
            },
            errorClass: 'error',
            errorPlacement: (error, element) => {},
            success: (label, element) => {},
            submitHandler: () => {
                $.ajax({
                    url: '/add-new-comment',
                    type: 'POST',
                    data: $('#comment-frm').serialize(),
                    success: (response) => {
                        // Empty form comment field
                        $('#comment').val('');
                        // Update comments
                        $('#post-comments').html(response);
                        // Update amount indicator
                        countComments($('#post').val());
                    }
                });
            }
        });
    }
};

$(window).on('load', () => initCommentFrm());
