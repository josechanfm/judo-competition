import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        // 只產生 `.form-input` 這類 class，不要對所有 input 上 base 樣式。
        // base 策略的 `[type='text']` 選擇器權重與 Ant Design 的 `.ant-input` 相同，
        // 在 CSS 順序上會蓋掉 Ant 的邊框/圓角（表單看起來就像沒套到樣式）。
        forms({ strategy: 'class' }),
    ],
};
