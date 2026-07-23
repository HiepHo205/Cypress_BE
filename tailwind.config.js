import scrollbarHide from "tailwind-scrollbar-hide";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        scrollbarHide,
    ],
};