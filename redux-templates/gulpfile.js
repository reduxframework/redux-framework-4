const {src, dest, series} = require('gulp');
const gulp = require('gulp');
const zip = require('gulp-zip');
const replace = require('gulp-replace');
const clean = require('gulp-clean');
const minifyCSS = require('gulp-csso');
const minifyJS = require('gulp-minify');
const concatCss = require('gulp-concat-css');
const merge = require('merge-stream');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const package_data = require('./package.json');


function cleanBuild() {
	return src('./build', {read: false, allowEmpty: true})
		.pipe(clean());
}

function makeBuild() {
	return src([
		'./**/*.*',
        '!./assets/js/*.dev.*',
		'!./node_modules/**/*.*',
		'!./src/**/*.*',
        '!./.wordpress-org/**/*.*',
        '!./.github/**/*.*',
		'!./build/**/*.zip',
		'!./gulpfile.js',
		'!./yarn.lock',
		'!./yarn-error.log',
		'!.babelrc',
		'!./languages/**/*',
		'!.eslintrc',
		'!./package-lock.json',
        '!./composer-lock.json',
        '!./composer.lock',
		'!./webpack.*.js',
        '!./jest.config.js',
        '!./babel.config.js',
        '!./jsconfig.json',
        '!vendor/composer/installers/**/*',
        '!vendor/composer/LICENSE',
        '!vendor/composer/installed.json',
	]).pipe(dest('build/redux-templates/'));
}

function productionMode() {
	// const replacement_string = '\n\t\t\twp_enqueue_style(\'redux-templates-bundle\', REDUXTEMPLATES_DIR_URL . \'assets/css/admin.min.css\', false, REDUXTEMPLATES_VERSION);\n\t\t\t';
	return src(['./build/redux-templates/core/Init.php'])
	// .pipe(replace(/(?<=#START_REPLACE)([^]*?)(?=#END_REPLACE)/g, replacement_string))
		.pipe(replace(/redux-templates\.dev/g, 'redux-templates.min'))
        .pipe(replace(/vendor\.dev/g, 'vendor.min'))
		.pipe(replace(/map\.js/g, 'map.min.js'))
		.pipe(dest('./build/redux-templates/core/'));
}

function debug() {
	var stream = arguments[0];

	// put your desired debugging code here
	console.log('hello');
	console.log(arguments);

	return stream;
}

function versionUpdate() {
	return src(['./redux_templates.php'])
		.pipe(replace(/\* Version:.*/g, '\* Version: \t\t  ' + package_data.version))
		.pipe(replace(/\'REDUXTEMPLATES_VERSION\', \'.*\'/g, '\'REDUXTEMPLATES_VERSION\', \'' + package_data.version + '\''))
		// .pipe(replace(/common-script\.js/g, 'common-script.min.js'))
		.pipe(dest('./'));
}


function admin_css() {
	return src(['./src/scss/*.scss'])
		.pipe(sass())
		.pipe(autoprefixer({
			cascade: false
		}))
		.pipe(minifyCSS())
		.pipe(concat('admin.min.css'))
		.pipe(dest('assets/css/'))
}


function minify_js() {
	const commonjs = src(['./build/redux-templates/assets/js/*.js'])
		.pipe(minifyJS({
			ext: {
				src: '.js',
				min: '.min.js'
			},
			exclude: ['tasks'],
			ignoreFiles: ['redux-templates.min.js', '*-min.js', '*.min.js']
		}))
		.pipe(dest(['./build/redux-templates/assets/js/']));

	return commonjs;
}


function makeZip() {
	return src('./build/**/*.*')
		.pipe(zip('./build/redux-templates.zip'))
		.pipe(dest('./'))
}

exports.makeBuild = makeBuild;
exports.productionMode = productionMode;
exports.admin_css = admin_css;
exports.minify_js = minify_js;
exports.cleanBuild = cleanBuild;
exports.versionUpdate = versionUpdate;


exports.makeZip = makeZip;
exports.default = series(
	versionUpdate,
	cleanBuild,
	makeBuild,
	productionMode,
	admin_css,
	minify_js,
	makeZip
);

gulp.task('watch_admin_css', function () {
	gulp.watch('src/scss/*.scss', gulp.series('admin_css'));
})
