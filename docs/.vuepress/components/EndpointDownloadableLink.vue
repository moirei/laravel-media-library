<template>
  <endpoint
    name="Get downloadable link"
    method="GET"
    endpoint="downloadable-link/{file-id}"
    v-bind="data"
  >
    <slot></slot>
  </endpoint>
</template>

<script>
import Endpoint from './Endpoint'
import data from './endpoints/data/downloadable-link'

export default {
  name: 'EndpointDownloadableLink',
  components: { Endpoint },
  data: () => ({ data }),
}
</script>