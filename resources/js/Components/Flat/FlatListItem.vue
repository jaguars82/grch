<template>
  <q-card class="my-card" flat bordered>
    <q-card-section horizontal>
      <q-img
        class="col-5"
        src="https://cdn.quasar.dev/img/parallax2.jpg"
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
