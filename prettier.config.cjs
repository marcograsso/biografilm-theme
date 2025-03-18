module.exports = {
  singleQuote: false,
  twigSingleQuote: false,
  twigOutputEndblockName: true,
  twigAlwaysBreakObjects: false,
  twigMultiTags: [],
  plugins: [
    "@prettier/plugin-php",
    "@zackad/prettier-plugin-twig",
    "prettier-plugin-tailwindcss",
  ],
  overrides: [
    {
      files: ["*.php"],
      options: {
        parser: "php",
      },
    },
  ],
};
