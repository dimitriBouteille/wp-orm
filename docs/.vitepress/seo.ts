import type { HeadConfig, PageData } from 'vitepress'

/**
 * Public URL of the site, with a trailing slash.
 * Change it here if the site moves to a custom domain.
 */
export const siteUrl = 'https://dimitribouteille.github.io/wp-orm/'

export const siteName = 'wp-orm'

export const siteDescription = 'The Eloquent ORM, inside WordPress: models for every WordPress table, meta filters and casting, custom post types and migrations.'

/**
 * Description of each page, used for the search engines and the social previews.
 * They are kept here and not in a frontmatter, so that the Markdown files stay clean on GitHub.
 */
const descriptions: Record<string, string> = {
  'index.md': siteDescription,
  'getting-started/installation.md': 'Install WP ORM with Composer: requirements, autoloader and optional features. No database configuration, it uses the WordPress connection.',
  'getting-started/quick-start.md': 'The most common WP ORM patterns: retrieve, query, create and update WordPress models, work with metas and relations.',
  'models/builtin-models.md': 'The Eloquent models for the WordPress tables: Post, User, Comment, Option, Term, their columns, relations, casts and enums.',
  'models/querying.md': 'Query WordPress data with WP ORM: findOneBy methods, builder shortcuts, reusable taps and the Eloquent query builder.',
  'models/metas.md': 'Read, write and filter post and user metas with WP ORM, and cast them to native PHP types like Eloquent attributes.',
  'models/custom-models.md': 'Create Eloquent models for your custom tables, custom post types and custom comment types, with metas support.',
  'models/events.md': 'Use the Eloquent model events (saving, saved, deleted...) with WP ORM: how to configure the event dispatcher in WordPress.',
  'database/connection.md': 'Use the WordPress database connection with the Laravel query builder, transactions, the schema builder and the DB facade.',
  'database/migrations.md': 'Version your custom WordPress tables with Laravel migrations and run them with WP-CLI: wp orm migrate, rollback and status.',
  'advanced/security.md': 'Security notes for WP ORM: mass assignment, passwords, SQL injection and serialized options.',
  'advanced/limitations.md': 'The known limitations of WP ORM: WordPress hooks and cache, multisite, metas and the Eloquent features not available.',
}

/**
 * Canonical URL of a page, matching the clean URLs of the site (no ".html", no "index").
 */
function pageUrl(relativePath: string): string {
  return siteUrl + relativePath.replace(/(^|\/)index\.md$/, '$1').replace(/\.md$/, '')
}

/**
 * Add the description, the canonical URL and the social preview tags to each page.
 */
export function transformPageData(pageData: PageData): void {
  const description = pageData.frontmatter.description ?? descriptions[pageData.relativePath] ?? siteDescription
  const isHome = pageData.relativePath === 'index.md'
  const title = isHome ? `${siteName} | The Eloquent ORM, inside WordPress` : `${pageData.title} | ${siteName}`
  const url = pageUrl(pageData.relativePath)
  const image = `${siteUrl}og-image.png`

  pageData.description = description

  const head: HeadConfig[] = [
    ['link', { rel: 'canonical', href: url }],
    ['meta', { property: 'og:type', content: isHome ? 'website' : 'article' }],
    ['meta', { property: 'og:site_name', content: siteName }],
    ['meta', { property: 'og:title', content: title }],
    ['meta', { property: 'og:description', content: description }],
    ['meta', { property: 'og:url', content: url }],
    ['meta', { property: 'og:image', content: image }],
    ['meta', { property: 'og:image:width', content: '1200' }],
    ['meta', { property: 'og:image:height', content: '630' }],
    ['meta', { property: 'og:image:alt', content: 'wp-orm, the Eloquent ORM inside WordPress' }],
    ['meta', { name: 'twitter:card', content: 'summary_large_image' }],
    ['meta', { name: 'twitter:title', content: title }],
    ['meta', { name: 'twitter:description', content: description }],
    ['meta', { name: 'twitter:image', content: image }],
  ]

  if (isHome) {
    head.push(['script', { type: 'application/ld+json' }, JSON.stringify({
      '@context': 'https://schema.org',
      '@type': 'SoftwareSourceCode',
      name: 'wp-orm',
      alternateName: 'dbout/wp-orm',
      description: siteDescription,
      url: siteUrl,
      codeRepository: 'https://github.com/dimitriBouteille/wp-orm',
      programmingLanguage: 'PHP',
      runtimePlatform: 'WordPress',
      license: 'https://opensource.org/licenses/MIT',
      author: {
        '@type': 'Person',
        name: 'Dimitri Bouteille',
        url: 'https://dimitri-bouteille.fr/',
      },
    })])
  }

  pageData.frontmatter.head ??= []
  pageData.frontmatter.head.push(...head)
}
