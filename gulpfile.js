var gulp = require('gulp'),
    tasks = require('gulp-load-plugins')(),
    rimraf = require('rimraf');



gulp.task('clean', function (callback) {
    rimraf.sync('public/assets/');
    callback();
});



gulp.task('css', function () {
    gulp.src(['app/assets/less/front/main.less', 'app/assets/less/admin.less'])
        .pipe(tasks.plumber())
        .pipe(tasks.less())
        .pipe(tasks.autoprefixer())
        .pipe(tasks.if(!!tasks.util.env.dist, tasks.csso()))
        .pipe(gulp.dest('public/assets/css/'))
        .pipe(tasks.if(!tasks.util.env.dist, tasks.livereload()));
});



gulp.task('js', function () {
    gulp.src('app/assets/js/main.js')
        .pipe(tasks.plumber())
        .pipe(tasks.jshint())
        .pipe(tasks.jshint.reporter('default'))
        .pipe(tasks.browserify())
        .pipe(tasks.if(!!tasks.util.env.dist, tasks.uglify()))
        .pipe(gulp.dest('public/assets/js/'))
        .pipe(tasks.if(!tasks.util.env.dist, tasks.livereload()));

    gulp.src('vendor/neemzy/patchwork-core/assets/js/nicEdit.js')
        .pipe(gulp.dest('public/assets/js/'));
});



gulp.task('img', function () {
    gulp.src('app/assets/img/**/*')
        .pipe(tasks.if(!!tasks.util.env.dist, tasks.imagemin({ interlaced: true, progressive: true })))
        .pipe(gulp.dest('public/assets/img/'))
        .pipe(tasks.if(!tasks.util.env.dist, tasks.livereload()));

    gulp.src('vendor/neemzy/patchwork-core/assets/img/**/*')
        .pipe(gulp.dest('public/assets/img/'));
});



gulp.task('font', function () {
    var from = 'app/assets/font/*.ttf',
        to = 'public/assets/font/',
        icons = 'node_modules/bootstrap/fonts/';

    gulp.src(from, { buffer: false })
        .pipe(tasks.ttf2woff())
        .pipe(gulp.dest(to));

    gulp.src(from, { buffer: false })
        .pipe(tasks.ttf2eot())
        .pipe(gulp.dest(to));

    gulp.src([icons + '*.woff', icons + '*.eot'], { buffer: false })
        .pipe(gulp.dest('public/assets/font/'));
});



gulp.task('workflow', function () {
    if (!tasks.util.env.dist) {
        gulp.src('gulpfile.js')
            .pipe(tasks.open('', { url: 'http://patch.work/' }));

        tasks.livereload.listen();
        gulp.watch('app/assets/less/**/*.less', ['css']);
        gulp.watch('app/assets/js/**/*.js', ['js']);
        gulp.watch('app/assets/img/**/*', ['img']);

        gulp.watch(['app/config/**/*.yml', 'app/views/**/*.twig'], function () {
            gulp.src('').pipe(tasks.livereload());
        });
    }
});



gulp.task('default', ['clean', 'css', 'js', 'img', 'font', 'workflow']);