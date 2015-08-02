
var gulp = require('gulp');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');

gulp.task('minify', function() {
  return gulp.src('resources/js/localization.js')
    .pipe(uglify())
    .pipe(rename('localization.min.js'))
    .pipe(gulp.dest('public/js'));
});

gulp.task('default', ['minify']);
