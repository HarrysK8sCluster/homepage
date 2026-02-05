const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { WebpackManifestPlugin } = require("webpack-manifest-plugin");

module.exports = {
    cache: {
        type: "filesystem",
    },

    entry: {
        layout: [
            path.resolve(__dirname, "../assets/scss/layout.scss")
        ],
        home: [
            path.resolve(__dirname, "../assets/scss/home.scss")
        ],
        law: [
            path.resolve(__dirname, "../assets/scss/law.scss")
        ],
    },

    output: {
        path: path.resolve(__dirname, "../html/assets"),
        publicPath: "/assets/",
        filename: "[name].[contenthash].js",
        assetModuleFilename: "media/[name].[contenthash][ext][query]",
        clean: true,
    },

    module: {
        rules: [
            {
                test: /\.m?js$/,
                exclude: /node_modules/,
                use: "babel-loader",
            },
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: {
                            url: {
                                filter: (url) => {
                                    if (url.startsWith("/font/") || url.startsWith("/fonts/")) return false; // nicht anfassen
                                    return true; // alles andere normal via webpack
                                },
                            },
                        },
                    },
                    "postcss-loader",
                    "sass-loader",
                ],
            },
            {
                test: /\.(png|jpe?g|gif|svg|webp|ico)$/i,
                type: "asset/resource",
            },
            {
                test: /\.(woff2?|eot|ttf|otf)$/i,
                type: "asset/resource",
            }
        ],
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].[contenthash].css",
        }),

        // manifest.json für Twig (Cache-Busting)
        new WebpackManifestPlugin({
            fileName: "manifest.json",
            publicPath: "/assets/",
            filter: (file) => !file.path.endsWith(".map"),
        }),
    ],
};
