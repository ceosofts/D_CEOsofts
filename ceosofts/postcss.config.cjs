// Keep the original content but ensure it's using module.exports syntax
module.exports = {
  // Your existing PostCSS configuration
  plugins: [
    require('postcss-import'),
    require('tailwindcss'),
    require('autoprefixer'),
  ],
};
