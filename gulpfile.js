var gulp = require('gulp');
var concat = require('gulp-concat');
var minify = require('gulp-minify');
var cleanCss = require('gulp-clean-css');
var rev = require('gulp-rev');
var del = require('del');
var copyAssets = require('gulp-css-copy-assets');
//import copyAssets from 'gulp-css-copy-assets';

gulp.task('clean-js', function () {
    return del([
        'public/build/js/*.js'
    ]);
});

gulp.task('clean-css', function () {
    return del([
        'public/build/css/*.css',
        'public/build/fonts',
        'public/build/images'
    ]);
});

gulp.task('pack-js', ['clean-js'], function () {
    return gulp.src([
        'themes/automotive/js/jquery.min.js',
        'themes/automotive/js/bootstrap.min.js',
        'themes/automotive/js/jquery.themepunch.tools.min.js',
        'themes/automotive/js/jquery.themepunch.revolution.min.js',
        'themes/automotive/js/wow.min.js',
        'themes/automotive/js/retina.js',
        'themes/automotive/js/jquery.fancybox.js',
        'themes/automotive/js/modernizr.custom.js',
        'themes/automotive/js/jquery.flexslider.js',
        'themes/automotive/js/jquery.bxslider.js',
        'themes/automotive/js/jquery.selectbox-0.2.js',
        'themes/automotive/js/jquery.mousewheel.js',
        'themes/automotive/js/jquery.easing.js',
        'themes/automotive/js/selectivizr.js',
        'themes/automotive/js/main.js',
        'themes/automotive/js/jquery.bs.gdpr.cookies.js',
        'themes/automotive/js/custom.js'
    ])
            .pipe(concat('bundle.js'))
            .pipe(minify({
                ext: {
                    min: '.js'
                },
                noSource: true
            }))
            .pipe(rev())
            .pipe(gulp.dest('public/build/js'))
            .pipe(rev.manifest('public/build/rev-manifest.json', {
                merge: true
            }))
            .pipe(gulp.dest(''));
});

gulp.task('pack-css', ['clean-css', 'copy-fonts', 'copy-images'], function () {
    return gulp.src([
        'themes/automotive/css/theme_fonts.css',
        'themes/automotive/css/bootstrap.min.css',
        'themes/automotive/css/font-awesome.min.css',
        'themes/automotive/css/flexslider.css',
        'themes/automotive/css/jquery.bxslider.css',
        'themes/automotive/css/jquery.fancybox.css',
        'themes/automotive/css/jquery.selectbox.css',
        'themes/automotive/css/style.css',
        'themes/automotive/css/mobile.css',
        'themes/automotive/css/settings.css',
        'themes/automotive/css/animate.min.css',
        'themes/automotive/css/ts.css',
        'themes/automotive/css/scustomizer-base.css',
        'themes/automotive/css/scustomizer-responsive.css',
    ])
            .pipe(concat('stylesheet.css'))
            .pipe(cleanCss())
            .pipe(rev())
            .pipe(gulp.dest('public/build/css'))
            .pipe(rev.manifest('public/build/rev-manifest.json', {
                merge: true
            }))
            .pipe(gulp.dest(''));
});

gulp.task('copy-fonts', function () {
    return gulp.src([
        'themes/automotive/fonts/**/*'
    ])
            .pipe(gulp.dest('public/build/fonts/'));
});

gulp.task('copy-images', function () {
    return gulp.src([
        'themes/automotive/images/**/*'
    ])
            .pipe(gulp.dest('public/build/images/'));
});

gulp.task('watch', function () {
    gulp.watch('assets/js/**/*.js', ['pack-js']);
    gulp.watch('assets/css/**/*.css', ['pack-css']);
});

gulp.task('default', ['watch']);
