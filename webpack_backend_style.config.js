const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const extractCSS = new ExtractTextPlugin('../../css/backend/style.min.css');

module.exports = {
    entry: {
        styles: [
            './public/js/backend/style.js',
        ],
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test:/\.css$/,
                use: extractCSS.extract([
                    'css-loader'
                ])
            },
            {
                test: /\.(jpg|jpeg|png|woff|woff2|eot|ttf|svg)$/,
                use: {
                    loader: 'url-loader',
                    options: {
                        limit: 100000,
                        name: "assets/[hash].[ext]"
                    }
                }
            }
        ],
    },
    plugins: [
        extractCSS,
        new OptimizeCssAssetsPlugin()
    ],
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, './public/js/backend/')
    },
    externals: {
        jquery: 'jQuery',
    }
};