import '../css/global.scss';

const $ = require('jquery');

const alphabeticPattern = '^[a-zA-Z\b]+$';
const alphanumericPattern = '^[a-zA-Z0-9\b]+$';
const disableCharsOnKeypress = (event, pattern) => {
    const regex = new RegExp(pattern);
    if (!regex.test(event.key)) return false;
};

$('.only-alphabetic-chars').on('keypress', (event) => disableCharsOnKeypress(event, alphabeticPattern));

$('.only-alphanumeric-chars').on('keypress', (event) => disableCharsOnKeypress(event, alphanumericPattern));