<script setup lang="ts">
import { ref } from 'vue'
import { withBase } from 'vitepress'

const tab = ref<'query' | 'terminal'>('query')

const features = [
  { title: 'WordPress models', details: 'Post, User, Comment, Option, Term, with relations.', link: '/models/builtin-models' },
  { title: 'Metas', details: 'Filter on metas in a query, cast them to native types.', link: '/models/metas' },
  { title: 'Custom models', details: 'Custom post types, comment types and tables.', link: '/models/custom-models' },
  { title: 'Migrations', details: 'Versioned tables, applied with WP-CLI.', link: '/database/migrations' },
  { title: 'Zero configuration', details: 'Runs on $wpdb, with the WordPress table prefix.', link: '/getting-started/installation' },
  { title: 'All of Eloquent', details: 'Eager loading, scopes, collections, casts, events.', link: '/getting-started/quick-start' },
]
</script>

<template>
  <div class="home">
    <section class="hero">
      <h1 class="title">The Eloquent ORM,<br class="desktop-only"> inside WordPress.</h1>
      <p class="tagline">
        Models for every WordPress table, meta filters and casting, custom post types and migrations. Zero configuration.
      </p>
      <div class="actions">
        <a class="button brand" :href="withBase('/getting-started/installation')">Get started</a>
        <a class="button alt" href="https://github.com/dimitriBouteille/wp-orm">View on GitHub</a>
      </div>
    </section>

    <section class="showcase">
      <div class="panel">
        <div class="tabs" role="tablist" aria-label="Example">
          <button
            id="tab-query"
            type="button"
            role="tab"
            class="tab"
            :aria-selected="tab === 'query'"
            aria-controls="panel-query"
            @click="tab = 'query'"
          >query.php</button>
          <button
            id="tab-terminal"
            type="button"
            role="tab"
            class="tab"
            :aria-selected="tab === 'terminal'"
            aria-controls="panel-terminal"
            @click="tab = 'terminal'"
          >terminal</button>
        </div>

        <div v-show="tab === 'query'" id="panel-query" role="tabpanel" aria-labelledby="tab-query">
<pre class="code"><span class="comment">// Featured products, newest first</span>
$products = Product::query()
    -&gt;whereStatus(PostStatus::Publish)
    -&gt;addMetaToFilter(<span class="string">'featured'</span>, <span class="string">'yes'</span>)
    -&gt;orderBy(Product::DATE, <span class="string">'desc'</span>)
    -&gt;get();</pre>
          <div class="commands">
            <div><span class="prompt">$</span> composer require dbout/wp-orm</div>
            <div><span class="prompt">$</span> wp orm migrate</div>
          </div>
        </div>

        <div v-show="tab === 'terminal'" id="panel-terminal" role="tabpanel" aria-labelledby="tab-terminal">
<pre class="code"><span class="comment"># Install the package</span>
<span class="prompt">$</span> composer require dbout/wp-orm

<span class="comment"># Create and run a migration</span>
<span class="prompt">$</span> wp orm make:migration create_orders_table
<span class="prompt">$</span> wp orm migrate</pre>
        </div>
      </div>
    </section>

    <section class="features">
      <div class="grid">
        <a v-for="feature in features" :key="feature.title" class="feature" :href="withBase(feature.link)">
          <h2 class="feature-title">{{ feature.title }}</h2>
          <p class="feature-details">{{ feature.details }}</p>
        </a>
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero {
  max-width: 820px;
  margin: 0 auto;
  padding: 120px 24px 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 28px;
  text-align: center;
}

.title {
  margin: 0;
  font-size: 58px;
  line-height: 1.08;
  font-weight: 500;
  letter-spacing: -0.035em;
  color: var(--vp-c-text-1);
}

.tagline {
  margin: 0;
  max-width: 540px;
  font-size: 18px;
  line-height: 1.65;
  color: var(--vp-c-text-2);
}

.actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 12px;
}

.button {
  display: inline-block;
  padding: 12px 22px;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 500;
  text-decoration: none;
  transition: background-color 0.25s, border-color 0.25s;
}

.button.brand {
  background: var(--vp-c-brand-3);
  color: #ffffff;
}

.button.brand:hover {
  background: var(--vp-c-brand-2);
}

.button.alt {
  border: 1px solid var(--vp-c-border);
  color: var(--vp-c-text-1);
}

.button.alt:hover {
  border-color: var(--vp-c-text-3);
}

.showcase {
  max-width: 820px;
  margin: 0 auto;
  padding: 0 24px 120px;
}

.panel {
  border: 1px solid var(--vp-c-divider-strong);
  border-radius: 12px;
  background: var(--vp-c-bg-alt);
  overflow: hidden;
}

.tabs {
  display: flex;
  gap: 24px;
  padding: 0 24px;
  border-bottom: 1px solid var(--vp-c-divider-strong);
}

.tab {
  padding: 14px 0;
  font-family: var(--vp-font-family-mono);
  font-size: 13px;
  color: var(--vp-c-text-3);
  border-bottom: 1px solid transparent;
  margin-bottom: -1px;
  transition: color 0.25s;
}

.tab:hover {
  color: var(--vp-c-text-1);
}

.tab[aria-selected='true'] {
  color: var(--vp-c-text-1);
  border-bottom-color: var(--vp-c-brand-3);
}

.code {
  margin: 0;
  padding: 28px;
  font-family: var(--vp-font-family-mono);
  font-size: 14px;
  line-height: 1.8;
  color: var(--vp-c-text-1);
  overflow-x: auto;
}

.comment {
  color: var(--vp-c-text-3);
}

.string,
.prompt {
  color: var(--vp-c-brand-1);
}

.commands {
  border-top: 1px solid var(--vp-c-divider-strong);
  padding: 20px 28px;
  font-family: var(--vp-font-family-mono);
  font-size: 13px;
  line-height: 1.8;
  color: var(--vp-c-text-2);
}

.features {
  border-top: 1px solid var(--vp-c-divider);
}

.grid {
  max-width: 1040px;
  margin: 0 auto;
  padding: 88px 24px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  column-gap: 56px;
  row-gap: 44px;
}

.feature {
  display: flex;
  flex-direction: column;
  gap: 8px;
  text-decoration: none;
}

.feature-title {
  margin: 0;
  padding: 0;
  border: 0;
  font-size: 16px;
  font-weight: 500;
  line-height: 1.5;
  letter-spacing: normal;
  color: var(--vp-c-text-1);
  transition: color 0.25s;
}

.feature:hover .feature-title {
  color: var(--vp-c-brand-1);
}

.feature-details {
  margin: 0;
  font-size: 15px;
  line-height: 1.65;
  color: var(--vp-c-text-2);
}

@media (max-width: 640px) {
  .desktop-only {
    display: none;
  }

  .hero {
    padding: 72px 20px 48px;
    gap: 20px;
  }

  .title {
    font-size: 38px;
    line-height: 1.1;
  }

  .tagline {
    font-size: 16px;
    line-height: 1.6;
  }

  .actions {
    flex-direction: column;
    align-self: stretch;
    padding-top: 8px;
    gap: 10px;
  }

  .button {
    padding: 14px 22px;
    text-align: center;
  }

  .showcase {
    padding: 0 16px 72px;
  }

  .tabs {
    padding: 0 18px;
    gap: 20px;
  }

  .tab,
  .commands {
    font-size: 12px;
  }

  .code {
    padding: 20px 18px;
    font-size: 12px;
    line-height: 1.75;
  }

  .commands {
    padding: 16px 18px;
  }

  .grid {
    padding: 56px 20px;
    row-gap: 32px;
  }
}
</style>
