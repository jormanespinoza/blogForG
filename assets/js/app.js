import '../css/global.scss';
import '../css/app.css';

const $ = require('jquery');

import 'bootstrap';
import './vendor/jquery.validate.js';

import 'font-awesome/css/font-awesome.min.css';

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
                $('#comment-frm button').text('Enviando...').addClass('disabled');
                $.ajax({
                    url: '/add-new-comment',
                    type: 'POST',
                    data: $('#comment-frm').serialize(),
                    success: (response) => {
                        // Empty form comment field
                        $('#comment').val('');
                        // Update comments
                        $('#post-comments').html(response);
                        // Update comment amount indicator
                        countComments($('#post').val());
                        $('#comment-frm button').text('Enviar').removeClass('disabled');
                    }
                });
            }
        });
    }
};
const likedPost = (element, post, liked) => {
    element.addClass('disabled');

    $.ajax({
        url: '/like-post',
        type: 'POST',
        data: `post=${post}&liked=${liked}`,
        success: (response) => {
            $('.post-likes-count').text(response);
            element.data('liked', liked);
            element.toggleClass('text-danger');
            element.siblings('.fa-heart').toggleClass('fa-heart-o text-danger');
            element.removeClass('disabled');
        }
    });
};

$(window).on('load', () => initCommentFrm());

$(() => {
    $('.toggle-like').on('click', function () {
        const liked = !$(this).data('liked');

        likedPost($(this), $(this).data('post'), liked);
    });
});
