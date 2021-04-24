module.exports = {
  title: 'Laravel Media Library',
  description: 'Manage media library with a directory system and associate files with Eloquent models.',
  base: '/laravel-media-library/',
  themeConfig: {
    logo: '/logo.png',
    repo: 'moirei/laravel-media-library',
    repoLabel: 'Github',
    docsRepo: 'moirei/laravel-media-library',
    docsDir: 'docs',
    docsBranch: 'master',
    sidebar: [
      '/',
      {
        title: 'Get started',
        collapsable: false,
        sidebarDepth: 1,    // optional, defaults to 1
        children: [
          '/features',
          '/installation/',
          '/installation/prepare-models',
          '/configuration',
          ['/installation/note', 'Concepts'],
        ],
      },
      {
        title: 'File Upload',
        path: '/guide/upload/',
        collapsable: false,
        children: [
          '/guide/upload/endpoints',
          '/guide/upload/manual-uploads',
          '/guide/upload/manual-attachments',
        ],
      },
      {
        title: 'Usage',
        collapsable: false,
        children: [
          '/guide/usage/data-casts',
          '/guide/usage/generating-urls',
          '/guide/usage/file-sharing',
          '/guide/usage/api',
        ],
      },
      {
        title: 'Frontend',
        collapsable: false,
        sidebarDepth: 1,
        children: [
          '/js-client',
        ],
      },
      // 'packages'
    ],
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/upload/' },
      // { text: 'External', link: 'https://moirei.com', target:'_self', rel:false },
    ]
  },
  head: [
    ['link', { rel: 'icon', href: '/logo.png' }],
    // ['link', { rel: 'manifest', href: '/manifest.json' }],
    ['meta', { name: 'theme-color', content: '#3eaf7c' }],
    ['meta', { name: 'apple-mobile-web-app-capable', content: 'yes' }],
    ['meta', { name: 'apple-mobile-web-app-status-bar-style', content: 'black' }],
    ['link', { rel: 'apple-touch-icon', href: '/icons/apple-touch-icon-152x152.png' }],
    // ['link', { rel: 'mask-icon', href: '/icons/safari-pinned-tab.svg', color: '#3eaf7c' }],
    ['meta', { name: 'msapplication-TileImage', content: '/icons/msapplication-icon-144x144.png' }],
    ['meta', { name: 'msapplication-TileColor', content: '#000000' }]
  ],
  plugins: [
    '@vuepress/register-components',
    '@vuepress/active-header-links',
    '@vuepress/pwa',
    ['@vuepress/search', {
      searchMaxSuggestions: 10
    }],
    'seo',
  ],
}