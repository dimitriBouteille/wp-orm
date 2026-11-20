<template>
  <footer class="site-footer">
    <div class="container">
      <p class="license">
        Released under the MIT License · <span class="package">dbout/wp-orm</span>
      </p>
      <a
        class="author"
        href="https://dimitri-bouteille.fr/"
        aria-label="Made by Dimitri Bouteille, visit the portfolio"
      >
        <span>Made by</span>
        <svg width="14" height="17" viewBox="0 0 954 1152" fill="currentColor" aria-hidden="true" focusable="false">
          <polygon points="9,0 153,0 441,576 153,1152 9,1152 297,576" />
          <polygon points="513,0 657,0 945,288 657,576 945,864 657,1152 585,1152 441,864 513,720 657,1008 801,864 513,576 801,288" />
        </svg>
      </a>
    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  border-top: 1px solid var(--vp-c-divider);
}

.container {
  max-width: 1040px;
  margin: 0 auto;
  padding: 24px 24px 40px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px 12px;
  align-items: center;
  justify-content: space-between;
  font-size: 13px;
  color: var(--vp-c-text-3);
}

.license {
  margin: 0;
}

.package {
  font-family: var(--vp-font-family-mono);
}

.author {
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 44px;
  color: var(--vp-c-text-3);
  text-decoration: none;
  transition: color 0.25s;
}

.author:hover {
  color: var(--vp-c-text-1);
}

.author svg {
  color: #ffffff;
}

@media (max-width: 640px) {
  .container {
    flex-direction: column;
    text-align: center;
  }
}
</style>
