var path = require('path');
var webpack = require('webpack');

module.exports = {
    mode: 'production',
    entry: {
        'redux-templates': path.join( __dirname, 'src/assets/js/index.js')
    },
    output: {
        path: path.join( __dirname, 'assets/js'),
        filename: '[name].min.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: { loader: 'babel-loader' }
            },
            {
                test: /\.scss$/,
                use: [ 'style-loader', 'css-loader', 'sass-loader' ],
            }
        ]
    }
};
