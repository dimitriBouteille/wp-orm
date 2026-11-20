<script setup lang="ts">
import DefaultTheme from 'vitepress/theme-without-fonts'
import { useData } from 'vitepress'
import SiteFooter from './components/SiteFooter.vue'

const { frontmatter } = useData()
</script>

<template>
  <DefaultTheme.Layout>
    <template #layout-bottom>
      <SiteFooter v-if="frontmatter.layout === 'home'" />
    </template>
  </DefaultTheme.Layout>
</template>
