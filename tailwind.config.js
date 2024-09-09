/** @type {import('tailwindcss').Config} */
export default {
  content: ["./views/**/*.twig", "./scripts/**/*.js", "./**/*.php"],
  theme: {
    extend: {},
  },
  plugins: [require("@tailwindcss/forms")],
};
