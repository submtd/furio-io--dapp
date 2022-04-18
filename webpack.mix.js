const mix = require("laravel-mix");
const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");

mix.webpackConfig({
    plugins: [
        new NodePolyfillPlugin({
            excludeAliases: ['console']
        })
    ]
});

mix.js("resources/js/app.js", "public/js")
    .postCss("resources/css/app.css", "public/css", [
        require("tailwindcss"),
    ])
    .vue()
    .version();
