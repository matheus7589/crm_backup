var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var cleanCSS = require('gulp-clean-css');
var concatCss = require('gulp-concat-css');


gulp.task('default', null, function() {

    mimifyjs('assets/js/*.js', 'assets/js/min');
    mimifyjs(['assets/themes/perfex/js/*.js', '!assets/themes/perfex/js/*.min.js'], 'assets/themes/perfex/js');
    component();
});




function mimifyjs(sourceJs, destinyJs) {
    gulp.src(sourceJs)
    .pipe(rename({
        suffix: ".min"
    })).pipe(uglify())
    .pipe(gulp.dest(destinyJs));
}


function component() {

    gulp.src("resource/css/components/*.css")
        .pipe(concatCss("assets/css/component.css"))
        .pipe(rename({
            suffix: ".min"
        })).pipe(cleanCSS())
        .pipe(gulp.dest(""));



}