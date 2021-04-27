const path = require('path');
const webpack = require('webpack');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const extractCSS = new ExtractTextPlugin('../../css/backend/bundle.min.css');

module.exports = {
    entry: {
        vendor: [
            './public/js/backend/lib.js',
        ],
        app: [
            './public/js/backend/index.js',
        ]
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
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery"
        }),
        new UglifyJsPlugin({
            test: /\.js$/,
            sourceMap: process.env.NODE_ENV === "development"
        }),
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

let fs = require('fs');

let getFiles = function (dir) {
    // get all 'files' in this directory
    // filter directories
    return fs.readdirSync(dir).filter(file => {
        return fs.statSync(`${dir}/${file}`).isFile();
    });
};

let entry = {};
getFiles('./public/js/backend/autoload').forEach(function (paths) {
    let filename = paths.split('.').slice(0, -1).join('.');
    entry[filename] = './public/js/backend/autoload/' + paths;
});

module.exports = {
    entry: entry,
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
                test: /\.css$/,
                use: extractCSS.extract([
                    'css-loader'
                ])
            }
        ],
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery"
        }),
        new UglifyJsPlugin({
            test: /\.js$/,
            sourceMap: process.env.NODE_ENV === "development"
        }),
        extractCSS,
        new OptimizeCssAssetsPlugin()
    ],
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, './public/js/backend/autoload/min')
    },
    externals: {
        jquery: 'jQuery',
    }
};