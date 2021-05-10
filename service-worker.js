/**
 * Welcome to your Workbox-powered service worker!
 *
 * You'll need to register this file in your web app and you should
 * disable HTTP caching for this file too.
 * See https://goo.gl/nhQhGp
 *
 * The rest of the code is auto-generated. Please don't update this file
 * directly; instead, make changes to your Workbox build configuration
 * and re-run your build process.
 * See https://goo.gl/2aRDsh
 */

importScripts("https://storage.googleapis.com/workbox-cdn/releases/4.3.1/workbox-sw.js");

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

/**
 * The workboxSW.precacheAndRoute() method efficiently caches and responds to
 * requests for URLs in the manifest.
 * See https://goo.gl/S9QRab
 */
self.__precacheManifest = [
  {
    "url": "404.html",
    "revision": "43294256d4c33ca13c29d2adc89d071b"
  },
  {
    "url": "assets/css/0.styles.10ad2620.css",
    "revision": "b61a1af0f5381b678d585583bdaed0a8"
  },
  {
    "url": "assets/img/search.83621669.svg",
    "revision": "83621669651b9a3d4bf64d1a670ad856"
  },
  {
    "url": "assets/js/10.72ff0b14.js",
    "revision": "d27dcc5081d0f65b8170eb8c0a51beca"
  },
  {
    "url": "assets/js/11.25403c68.js",
    "revision": "97db78c8574085b431f2a830b4d5cf60"
  },
  {
    "url": "assets/js/12.dbb36bd9.js",
    "revision": "b8edee2563ab81bccdad7c0adf85d06e"
  },
  {
    "url": "assets/js/13.ae6e9079.js",
    "revision": "f443b10e547d770a2e3696539f5f5c6f"
  },
  {
    "url": "assets/js/14.a9258466.js",
    "revision": "d8494a9be30d7dc9874b199a69ad4897"
  },
  {
    "url": "assets/js/15.7fd8b7da.js",
    "revision": "b0d35d5ef2a3075aafb82b447bf68545"
  },
  {
    "url": "assets/js/16.9beffe51.js",
    "revision": "8aad39354c95ef029043ca3dbfd8ff55"
  },
  {
    "url": "assets/js/17.c9be9413.js",
    "revision": "3f0ddb836fac808ebabbce153e190cbc"
  },
  {
    "url": "assets/js/18.4d1fecda.js",
    "revision": "6e8ff6ff930c4281d034b1cfaadb2881"
  },
  {
    "url": "assets/js/19.bf242ca7.js",
    "revision": "79e99aba2dc7a8a83973d115bec687c8"
  },
  {
    "url": "assets/js/2.d0a3eaee.js",
    "revision": "1235b1778bda6fc0b542f6d5e27f760e"
  },
  {
    "url": "assets/js/20.d557b794.js",
    "revision": "52de465b56868485257f4c1def642d24"
  },
  {
    "url": "assets/js/21.fd7cb7ed.js",
    "revision": "e5369444b1b69a3f75fe277744c62b61"
  },
  {
    "url": "assets/js/22.ea89c297.js",
    "revision": "3e01eff17ec64c6a9dd85d365f84a052"
  },
  {
    "url": "assets/js/23.aab86637.js",
    "revision": "d683bc26bb3f66980879ae10381a6bcc"
  },
  {
    "url": "assets/js/24.526b565f.js",
    "revision": "5bad5fcf08c680b31486bcb0467b7f71"
  },
  {
    "url": "assets/js/25.bce1b16a.js",
    "revision": "2a4a480cb7209dbf2b3bcdfb6110c2f5"
  },
  {
    "url": "assets/js/26.67b31086.js",
    "revision": "808a5e6502ec181bdc1cfe25a86f994a"
  },
  {
    "url": "assets/js/27.8d46d2f8.js",
    "revision": "4145b870f2d52a08d07e8fe9c272cd49"
  },
  {
    "url": "assets/js/28.15b74cdc.js",
    "revision": "1e514b9d079b44a73cc9bfea6de1d23e"
  },
  {
    "url": "assets/js/29.5b2d5433.js",
    "revision": "78f8bbe6cb1255f89987cd2df8dc8bf7"
  },
  {
    "url": "assets/js/3.c72f329f.js",
    "revision": "66ac75b58ba9607392d3e694cd64066f"
  },
  {
    "url": "assets/js/30.be3e0cea.js",
    "revision": "6981ed7aae2b5907096460718464855d"
  },
  {
    "url": "assets/js/31.9987ead3.js",
    "revision": "3ceb6c49d66a5dca7283ce8b605191a3"
  },
  {
    "url": "assets/js/32.5d6378c0.js",
    "revision": "ab097a7eb1d34e795f4663866569e2e7"
  },
  {
    "url": "assets/js/33.c3da07ea.js",
    "revision": "c4479da78bcfc5582cd914b051cf5062"
  },
  {
    "url": "assets/js/34.8a2e552e.js",
    "revision": "2dc045fe2e9a0ee14a8cb0c78e8da1c5"
  },
  {
    "url": "assets/js/35.0a00b1b8.js",
    "revision": "9a07fc7cf252cf888346167873868d42"
  },
  {
    "url": "assets/js/36.e8fc6a69.js",
    "revision": "da7f09766cea5f20bf5a6a74ababa18c"
  },
  {
    "url": "assets/js/37.209e3863.js",
    "revision": "edc33011979eabddad195fc777ff508f"
  },
  {
    "url": "assets/js/38.63067ec3.js",
    "revision": "2536dbcb07b43dd25ba462a446a06586"
  },
  {
    "url": "assets/js/39.bf0484fa.js",
    "revision": "2f6d86c65391fdb4dd22ee49ce1373e1"
  },
  {
    "url": "assets/js/4.13fbe7b9.js",
    "revision": "5812b608479764c8acf5ac189bb68f9c"
  },
  {
    "url": "assets/js/40.666c10f4.js",
    "revision": "a100a6822372b11ebee946b140234752"
  },
  {
    "url": "assets/js/41.fa13d973.js",
    "revision": "7c9fa6c587204cbd8666f5a476955fff"
  },
  {
    "url": "assets/js/42.5249a193.js",
    "revision": "7448f69fe3d69db9c49a771beabbc486"
  },
  {
    "url": "assets/js/43.0dc74905.js",
    "revision": "5f1c6eb519d79f8ea3cf7f4b083ed551"
  },
  {
    "url": "assets/js/44.0dbddfce.js",
    "revision": "5f9bc91fee94e140fa8768877d6ed545"
  },
  {
    "url": "assets/js/45.e8f1920a.js",
    "revision": "b8ca7cc0a0eb8326e8059517452b485c"
  },
  {
    "url": "assets/js/46.513f65d8.js",
    "revision": "46ebb4def2c1bf0ba8385811b3411ea1"
  },
  {
    "url": "assets/js/47.fbb97e15.js",
    "revision": "643b7610540813f8600bed402b49dfcd"
  },
  {
    "url": "assets/js/48.7c0668eb.js",
    "revision": "cde3e14c510be6bd4d3ec660352841cb"
  },
  {
    "url": "assets/js/5.4c619b6d.js",
    "revision": "2ca42dcf13fab3bd2b6155e8864d4af7"
  },
  {
    "url": "assets/js/6.3460421d.js",
    "revision": "621797a3a70277e36603502e5536dfa2"
  },
  {
    "url": "assets/js/7.4d8dd4f7.js",
    "revision": "640b876b25f0ba7f3ec5e2a270f972f3"
  },
  {
    "url": "assets/js/8.88b6d28c.js",
    "revision": "a7b09543fa219cc542eb5937dbfdc57d"
  },
  {
    "url": "assets/js/9.0208709b.js",
    "revision": "54883ec89e25096298b767c94d74c442"
  },
  {
    "url": "assets/js/app.b505f47a.js",
    "revision": "6b775ffd28bebee1c841540e8f84bb54"
  },
  {
    "url": "configuration.html",
    "revision": "fb6b0816d85629eef4105ce7e022fbc3"
  },
  {
    "url": "data.html",
    "revision": "5b8a246e373307ae3f9cbfe2a721929b"
  },
  {
    "url": "features.html",
    "revision": "f20e079958010640c825cd7ab73b040f"
  },
  {
    "url": "guide/upload/endpoints.html",
    "revision": "91291f5b83913d06769bcc8009946546"
  },
  {
    "url": "guide/upload/index.html",
    "revision": "0620cde3fdbdeff8f9ecf5855a26cb7f"
  },
  {
    "url": "guide/upload/manual-attachments.html",
    "revision": "bfd2584e401d0e8d445ed8bb4abf4e7a"
  },
  {
    "url": "guide/upload/manual-uploads.html",
    "revision": "6331bc0d77540d09f48308b9179022fa"
  },
  {
    "url": "guide/usage/api.html",
    "revision": "c2769d5a375135bcd9ef08c131384f0a"
  },
  {
    "url": "guide/usage/data-casts.html",
    "revision": "2c2914e5cf359625f8c9cdae724962ce"
  },
  {
    "url": "guide/usage/file-sharing.html",
    "revision": "143f093f73a6b72ebede640a1a8d056e"
  },
  {
    "url": "guide/usage/generating-urls.html",
    "revision": "8346a04828ffad677d50dffd41e1de75"
  },
  {
    "url": "icons/apple-touch-icon-152x152.png",
    "revision": "bb5d8a25d314cab9fb7003293e262b7b"
  },
  {
    "url": "icons/msapplication-icon-144x144.png",
    "revision": "7b147426540b00bc662c63140819dac9"
  },
  {
    "url": "index.html",
    "revision": "a903d5a9e6773a8405d2346cfba10c3f"
  },
  {
    "url": "installation/index.html",
    "revision": "535459cf5f5d323cec2c00670d0778f9"
  },
  {
    "url": "installation/note.html",
    "revision": "1170b3efbe0ce751776475d5a297ea3e"
  },
  {
    "url": "installation/prepare-models.html",
    "revision": "a5a2ee43102c7f3cafd695800b2aeff3"
  },
  {
    "url": "js-client.html",
    "revision": "f0674a22636d3e887eb92ff5bc54a73c"
  },
  {
    "url": "logo.png",
    "revision": "a68c56ae1a0bc32fdcbf4d244b183aef"
  },
  {
    "url": "packages.html",
    "revision": "1f56650fe64bd4d37d5b0e755f15ac6f"
  },
  {
    "url": "routes.html",
    "revision": "3f1165a2cf85d65a879c02d33dc217c9"
  }
].concat(self.__precacheManifest || []);
workbox.precaching.precacheAndRoute(self.__precacheManifest, {});
addEventListener('message', event => {
  const replyPort = event.ports[0]
  const message = event.data
  if (replyPort && message && message.type === 'skip-waiting') {
    event.waitUntil(
      self.skipWaiting().then(
        () => replyPort.postMessage({ error: null }),
        error => replyPort.postMessage({ error })
      )
    )
  }
})
