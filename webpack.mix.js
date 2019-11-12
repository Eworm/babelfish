const mix = require('laravel-mix');
require('dotenv').config();

// In an .env file, define the path to the Statamic site you want this addon to be copied to.
// eg. STATAMIC_PATH=/users/bob/sites/statamic
const statamic = process.env.STATAMIC_PATH;

// mix.copyDirectory('Babelfish', `${statamic}/site/addons/Babelfish`);

mix.js('Babelfish/resources/assets/src/js/fieldtype.js', 'Babelfish/resources/assets/js');
