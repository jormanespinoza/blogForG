import '../css/global.scss';

const $ = require('jquery');
const alphabeticPattern = '^[a-zA-Z\b]+$';
const alphanumericPattern = '^[a-zA-Z0-9\b]+$';
const disableCharsOnKeyup = (event, pattern) => {
    const regex = new RegExp(pattern);
    if (!regex.test(event.key)) return false;
};

$('.only-alphabetic-chars').on('keyup', (event) => disableCharsOnKeyup(event, alphabeticPattern));

$('.only-alphanumeric-chars').on('keyup', (event) => disableCharsOnKeyup(event, alphanumericPattern));
