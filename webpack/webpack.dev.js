const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");

module.exports = merge(common, {
    mode: "development",
    devtool: "source-map",
    optimization: {
        minimize: false,
    },

    watchOptions: {
        poll: 500,           // wichtig: polling statt inotify
        aggregateTimeout: 200,
        ignored: /node_modules/,
    },
});
