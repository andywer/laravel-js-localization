
var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');

gulp.task('minify', function() {
  return gulp.src('resources/js/*.js')
    .pipe(concat('public/js/combined.js'))
    .pipe(uglify())
    .pipe(rename('localization.min.js'))
    .pipe(gulp.dest('public/js'));
});

gulp.task('default', ['minify']);
