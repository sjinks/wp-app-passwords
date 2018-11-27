'use strict';

const gulp         = require('gulp');
const del          = require('del');
const pump         = require('pump');
const prune        = require('gulp-prune');
const newer        = require('gulp-newer');
const sourcemaps   = require('gulp-sourcemaps');
const sass         = require('gulp-sass');
const autoprefixer = require('autoprefixer');
const cssnano      = require('cssnano');
const postcss      = require('gulp-postcss');
const rename       = require('gulp-rename');
const uglify       = require('gulp-uglify');

const config = {
    css: {
        src:  ['assets-dev/**.scss'],
        dest: 'assets',
        sass: {
            errLogToConsole: true,
            outputStyle:     'expanded',
            precision:       5
        },
        autoprefixer: {
            browsers : '> 5%'
        },
        cssnano: {}
    },
    js: {
        src:  ['assets-dev/**.js'],
        dest: 'assets'
    }
};

gulp.task('clean:css', function() {
    return del(config.css.dest + '/**.css', config.css.dest + '/**.css.map');
});

gulp.task('clean:js', function() {
    return del(config.css.dest + '/**.js', config.css.dest + '/**.js.map');
});

gulp.task('css', function(cb) {
    pump([
        gulp.src(config.css.src),
        prune({
            dest: config.css.dest,
            ext:  ['.min.css.map', '.min.css']
        }),
        newer({
            dest: config.css.dest,
            ext:  '.min.css'
        }),
        sourcemaps.init(),
        sass(config.css.sass),
        postcss([autoprefixer(config.css.autoprefixer)]),
        gulp.dest(config.css.dest),
        postcss([cssnano(config.css.cssnano)]),
        rename({
            suffix: '.min'
        }),
        sourcemaps.write('.', { includeContent: false }),
        gulp.dest(config.css.dest)
    ], cb);
});

gulp.task('js', function(cb) {
    pump([
        gulp.src(config.js.src),
        prune({
            dest: config.js.dest,
            ext:  ['.min.js.map', '.min.js']
        }),
        newer({
            dest: config.js.dest,
            ext:  '.min.js'
        }),
        sourcemaps.init(),
        uglify(),
        rename({
            suffix : '.min'
        }),
        sourcemaps.write('.', { includeContent: false }),
        gulp.dest(config.js.dest)
    ], cb);
});
