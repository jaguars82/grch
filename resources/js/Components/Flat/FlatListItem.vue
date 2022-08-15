<template>
  <q-card class="my-card" flat bordered>
    <q-card-section horizontal>
      <q-img
        class="col-5"
        :src="`/uploads/${flat.layout}`"
      />
      <q-card-section>
        {{ flat.id }}
      </q-card-section>
    </q-card-section>
  </q-card>
</template>

<script>

export default ({
  props: {
    flat: Array
  },
  setup() {
    
  },
})
</script>
