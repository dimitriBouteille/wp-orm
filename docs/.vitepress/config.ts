import { defineConfig } from 'vitepress'

const repository = 'https://github.com/dimitriBouteille/wp-orm'

export default defineConfig({
  title: 'WP ORM',
  description: 'The Eloquent ORM, inside WordPress: models for every WordPress table, meta filters and casting, custom post types and migrations.',
  base: '/wp-orm/',
  cleanUrls: true,
  lastUpdated: true,

  // The site only has a dark theme.
  appearance: 'force-dark',

  // README.md is the table of contents for GitHub, the site uses index.md and the sidebar.
  srcExclude: ['README.md'],

  head: [
    ['meta', { name: 'theme-color', content: '#0c0d0f' }],
    ['link', { rel: 'preconnect', href: 'https://fonts.googleapis.com' }],
    ['link', { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' }],
    ['link', { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&family=Geist+Mono:wght@400;500&display=swap' }],
  ],

  themeConfig: {
    siteTitle: 'wp-orm',

    nav: [
      { text: 'Docs', link: '/getting-started/installation', activeMatch: '^/(getting-started|models|advanced)/|^/database/connection' },
      { text: 'Migrations', link: '/database/migrations' },
    ],

    sidebar: [
      {
        text: 'Getting started',
        items: [
          { text: 'Installation', link: '/getting-started/installation' },
          { text: 'Quick start', link: '/getting-started/quick-start' },
        ],
      },
      {
        text: 'Models',
        items: [
          { text: 'Built-in models', link: '/models/builtin-models' },
          { text: 'Querying models', link: '/models/querying' },
          { text: 'Metas', link: '/models/metas' },
          { text: 'Custom models', link: '/models/custom-models' },
          { text: 'Events', link: '/models/events' },
        ],
      },
      {
        text: 'Database',
        items: [
          { text: 'Database connection', link: '/database/connection' },
          { text: 'Migrations', link: '/database/migrations' },
        ],
      },
      {
        text: 'Advanced',
        items: [
          { text: 'Security', link: '/advanced/security' },
          { text: 'Limitations', link: '/advanced/limitations' },
        ],
      },
    ],

    outline: [2, 3],

    search: {
      provider: 'local',
    },

    editLink: {
      // Pull requests target the develop branch.
      pattern: `${repository}/edit/develop/docs/:path`,
      text: 'Edit this page on GitHub',
    },

    socialLinks: [
      { icon: 'github', link: repository },
    ],
  },
})
