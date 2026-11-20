import type { Theme } from 'vitepress'
// Geist is loaded from Google Fonts, the Inter font bundled with the default theme is not needed.
import DefaultTheme from 'vitepress/theme-without-fonts'
import Layout from './Layout.vue'
import HomePage from './components/HomePage.vue'
import './custom.css'

export default {
  extends: DefaultTheme,
  Layout,
  enhanceApp({ app }) {
    app.component('HomePage', HomePage)
  },
} satisfies Theme
