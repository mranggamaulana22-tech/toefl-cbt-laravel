module.exports = {
    extends: ['stylelint-config-standard'],
    ignoreFiles: ['public/build/**', 'vendor/**', 'node_modules/**'],
    rules: {
        'alpha-value-notation': null,
        'color-function-notation': null,
        'at-rule-no-unknown': [
            true,
            {
                ignoreAtRules: ['tailwind', 'apply', 'screen', 'responsive', 'variants'],
            },
        ],
        'import-notation': null,
        'media-feature-range-notation': null,
        'rule-empty-line-before': null,
        'selector-class-pattern': null,
    },
};
