<template>
  <q-card class="person-card" flat bordered>
    <q-card-section horizontal>
      <q-card-section class="col-8">
        <div class="text-bold">
          <p class="ellipsis q-mb-xs text-grey-7 text-uppercase">{{ person.last_name }}</p>
          <p class="ellipsis">{{ person.first_name }} {{ person.middle_name }}</p>
        </div>
        <div v-if="person.phone" class="ellipsis">
          <a :href="`tel:${person.phone}`">
            <q-icon class="q-mr-xs" name="call" />
            {{ person.phone }}
          </a>
        </div>
        <div v-if="person.email" class="ellipsis">
          <a :href="`mailto:${person.email}`">
            <q-icon class="q-mr-xs" name="alternate_email" />
            {{ person.email }}
          </a>
        </div>
      </q-card-section>
      <q-img
        class="col-4 person-image"
        :src="person.photo ? `/uploads/${person.photo}` : '/img/blank-person.svg'"
      />
    </q-card-section>
  </q-card>
</template>

<script>
export default {
  props: {
    person: Object
  }
}
</script>

<style scoped>
.person-card, .person-image {
  height: 136px;
  max-height: 136px;
}
</style>