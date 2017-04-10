var gulp = require('gulp');

/*
	COMPILING & PROCESSING ______________________________________________________________________
*/
var autoprefixer = require('gulp-autoprefixer');
var sass = require('gulp-sass');
var cleanCSS = require('gulp-clean-css');

var jshint = require('gulp-jshint');
var jshintstylish = require('jshint-stylish');

var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

var htmlmin = require('gulp-htmlmin');
var imagemin = require('gulp-imagemin');

// var print = require('gulp-print');

var src_dir = "src/";
var lib_dir = 'lib/';

var build_dir = "E:/Dropbox/DESARROLLO/Wordpress/TEST/wp-content/plugins/weatherconditions/";
var dist_dir = "dist/";


var tasks = {
	once: [],
	watch: [],
};

/* JAVASCRIPT ____________________________________________________________________________*/
var scripts_src = 'src/js/**';


gulp.task('script_err', function() {
	gulp.src(scripts_src)
		.pipe(jshint())
		.pipe(jshint.reporter(jshintstylish));
});

gulp.task('script', function() {
	
	var scripts = [
		'!'+src_dir+'js/*Admin*',
		src_dir+'js/**',
	];
	// gulp.src(scripts).pipe(print());
	gulp.src(scripts)
		.pipe(concat('bc_weatherconditions.js'))
			.pipe(gulp.dest(build_dir+'js'))
		.pipe(rename({suffix: '.min'}))
		.pipe(uglify())
			.pipe(gulp.dest(build_dir+'js'))
	;

	scripts = [
		src_dir+'js/*Admin*',
	];
	gulp.src(scripts)
		.pipe(concat('bc_weatherconditions_admin.js'))
			.pipe(gulp.dest(build_dir+'js'))
		.pipe(rename({suffix: '.min'}))
		.pipe(uglify())
			.pipe(gulp.dest(build_dir+'js'))
	;
});
gulp.task('script_w', function(){gulp.watch(scripts_src,['script']);});
gulp.task('script_watch',['script','script_w']);
tasks.once.push('script');
tasks.watch.push('script_w');


/* IMAGES ____________________________________________________________________________*/
var image_min_src = 'src/img/**';
gulp.task('image_min', function(){
	gulp.src(image_min_src)
		.pipe(imagemin())
		.pipe(gulp.dest(build_dir+'img/'))
	;
});
gulp.task('image_min_w', function(){gulp.watch(image_min_src,['image_min']);});
gulp.task('image_min_watch',['image_min','image_min_w']);
tasks.once.push('image_min');
tasks.watch.push('image_min_w');


/* SCSS ____________________________________________________________________________*/
var sass_compile_src = 'src/css/*.scss';
gulp.task('sass_compile',function(){
	gulp.src(sass_compile_src)
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer({
			browsers: ['last 3 versions','safari 5', 'ie 6', 'ie 7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'],
			cascade: false
		}))
		.pipe(cleanCSS({compatibility: 'ie9'}))
		.pipe(gulp.dest(build_dir+'css'))
	;
});
gulp.task('sass_compile_w',function(){gulp.watch(sass_compile_src,['sass_compile']);});
gulp.task('sass_compile_watch',['sass_compile','sass_compile_w']);
tasks.once.push('sass_compile');
tasks.watch.push('sass_compile_w');


/* HTML ____________________________________________________________________________*/
var html_min_src = 'src/**/*.html';
gulp.task('html_min',function(){
	gulp.src(html_min_src)
		.pipe(htmlmin({collapseWhitespace: true}))
		.pipe(gulp.dest(build_dir))
	;
});
gulp.task('html_min_w',function(){gulp.watch(html_min_src,['html_min']);});
gulp.task('html_min_watch',['html_min','html_min_w']);
tasks.once.push('html_min');
tasks.watch.push('html_min_w');

/* PHP ____________________________________________________________________________*/
var copyphp_src = 'src/**/*.php';
gulp.task('copyphp', function() {
   gulp.src(copyphp_src,{base:'src/'})
   .pipe(gulp.dest(build_dir));
});
gulp.task('copyphp_w', function() { gulp.watch(copyphp_src,['copyphp']);});
gulp.task('copyphp_watch',['copyphp','copyphp_w']);
tasks.once.push('copyphp');
tasks.watch.push('copyphp_w');

/* FONTS ____________________________________________________________________________*/
var copyfonts_src = 'src/fonts/**';
gulp.task('copyfonts', function() {
   gulp.src(copyfonts_src)
   .pipe(gulp.dest(build_dir+'fonts'));
});
gulp.task('copyfonts_w', function() { gulp.watch(copyfonts_src,['copyfonts']);});
gulp.task('copyfonts_watch',['copyfonts','copyfonts_w']);
tasks.once.push('copyfonts');
tasks.watch.push('copyfonts_w');


/* GENERAL ____________________________________________________________________________*/
gulp.task('once',tasks.once);
gulp.task('watch',tasks.watch);
gulp.task('default',['once','watch']);

/*
	VERSIONING & RELASES ______________________________________________________________________
*/

// var git = require('gulp-git');