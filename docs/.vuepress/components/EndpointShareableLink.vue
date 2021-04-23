<template>
  <endpoint
    name="Get file shareable link"
    method="POST"
    endpoint="share/{file-id}"
    v-bind="data"
  >
    <slot></slot>
  </endpoint>
</template>

<script>
import Endpoint from "./Endpoint";
import data from "./endpoints/data/share";

export default {
  name: "EndpointShareableLink",
  components: { Endpoint },
  data: () => ({ data }),
};
</script>
