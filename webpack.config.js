// webpack.config.js
const path = require('path');
const webpack = require('webpack');

const PATHS = {
    source: path.join(__dirname, 'resources/js'),
    build: path.join(__dirname, 'web')
};

const {VueLoaderPlugin} = require('vue-loader');

module.exports = (env, argv) => {
    let config = {
        production: argv.mode === 'production'
    };

    return {
        mode: 'development',
        entry: [
            './resources/js/app.js'
        ],
        output: {
            path: PATHS.build,
            filename: config.production ? 'assets/inertia/js/app.min.js' : 'assets/inertia/js/app.js'
        },
        resolve: {
            extensions: ['.js', '.vue', '.json'],
            alias: {
                '@': '/' + path.resolve(__dirname, 'resources/js')
            }
        },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    use: 'vue-loader'
                },
                {
                    test: /\.css$/,
                    use: ['style-loader', 'css-loader']
                },
                {
                    test: /\.sass$/,
                    use:  ['style-loader', 'css-loader', 'sass-loader']
                },
                {
                    test: /\.svg$/,
                    loader: 'vue-loader',
                },
            ]
        },
        plugins: [
            new VueLoaderPlugin(),
            // Define Bundler Build Feature Flags
            new webpack.DefinePlugin({
                // Drop Options API from bundle
                // __VUE_OPTIONS_API__: false,
                // Enable Vue devtools on production
                __VUE_PROD_DEVTOOLS__: true,
            }),
        ]
    };
};
