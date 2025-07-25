import typescriptEslint from "@typescript-eslint/eslint-plugin";
import globals from "globals";
import tsParser from "@typescript-eslint/parser";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all,
});

export default [
    ...compat.extends(
        "eslint:recommended",
        "plugin:@typescript-eslint/recommended",
        "prettier",
    ),
    {
        plugins: {
            "@typescript-eslint": typescriptEslint,
        },

        languageOptions: {
            globals: {
                ...globals.browser,
            },

            parser: tsParser,
            ecmaVersion: 12,
            sourceType: "module",
        },

        rules: {},
    },
    {
        files: ["resources/ts/**/*.ts", "resources/ts/**/*.tsx"],
        rules: {},
    },
    {
        ignores: [
            "resources/ts/**/*.d.ts",
            "resources/ts/**/*.test.ts",
            "resources/ts/**/*.test.tsx",
            "resources/ts/**/*.spec.ts",
            "resources/ts/**/*.spec.tsx",
            "node_modules/",
            "dist/",
            "build/",
            "public/",
            "vendor/",
        ],
    },
];
