var gulp = require('gulp')
var coffee = require('gulp-coffee');
var less = require('gulp-less');
var include = require('gulp-include');

gulp.task('build-coffee', function() {
  gulp.src('./assets/coffee/app.coffee')
    .pipe(include())
    .pipe(coffee({bare: true}))
    .on('error', console.log)
    .pipe(gulp.dest('./public/dist/js'));

});

gulp.task('build-vendor', function() {
  gulp.src('./assets/plugins/vendor.js')
    .pipe(include())
    .on('error', console.log)
    .pipe(gulp.dest('./public/dist/js'));

});

gulp.task('build-less', function(){
    return gulp.src('./assets/styles/main.less')
        .pipe(less())
        .pipe(gulp.dest('./public/dist/styles'));
});

gulp.task('default', ['build-coffee', 'build-less', 'build-vendor'], function() {
  js_path = ['./assets/coffee/*.coffee'];
  gulp.watch(js_path, ['build-coffee']);

  vendor_path = ['./assets/plugins/*.js']
  gulp.watch(vendor_path, ['build-vendor']);

  css_path = ['./assets/styles/*.less'];
  gulp.watch(css_path, ['build-less']);
});
