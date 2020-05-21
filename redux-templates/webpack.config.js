const rules = require('./rules')
const plugins = require('./plugins')
var path = require('path');

module.exports = [{
    mode: 'development',

    entry: [
        path.join(__dirname, 'src/assets/js/index.js')
    ],
    output: {
        path: path.join(__dirname, 'assets/js'),
        filename: '[name].dev.js',
        library: '[name]', // assigns this module to the global (window) object
        libraryTarget: 'commonjs2'
    },
    // Permit importing @wordpress/* packages.
    externals: {
        react: 'commonjs react',
       'react-dom': 'commonjs react-dom',
    },

    optimization: {
        splitChunks: {
            cacheGroups: {
                vendor: {
                    test: /node_modules/,
                    chunks: 'initial',
                    name: 'editor_vendor',
                    priority: 10,
                    enforce: true
                }
            }
        },
    },
    resolve: {
        alias: {
            '~reduxtemplates': path.resolve(__dirname, '../src/')
        }
    },
    // Clean up build output
    stats: {
        all: false,
        assets: true,
        colors: true,
        errors: true,
        performance: true,
        timings: true,
        warnings: true,
    },
    module: {
        strictExportPresence: true,
        rules
        // rules: [
        //     {
        //         test: /\.js$/,
        //         exclude: /(node_modules|bower_components)/,
        //         use: { loader: 'babel-loader' }
        //     },
        //     {
        //         test: /\.scss$/,
        //         use: [ 'style-loader', 'css-loader', 'sass-loader' ],
        //     }
        // ]
    },
    devtool: 'source-map',
    plugins,
}];
