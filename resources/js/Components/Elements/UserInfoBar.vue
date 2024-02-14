<template>
  <div class="flex items-center" :class="{ 'justify-end': $q.screen.xs, 'justify-between': $q.screen.gt.xs }">
    <div v-if="user.organization" class="gt-xs user-organization-info">
      <img class="organization-logo" v-if="user.organization.logo" :src="`/uploads/${user.organization.logo}`" />
    </div>
    <div class="user-personal-info">
      <div>
        <p class="q-mb-xs">
          <span v-if="user.last_name"><strong>{{ user.last_name }}&nbsp;</strong></span>
          <span v-if="user.first_name"><strong>{{ user.first_name }}</strong></span>
          <span v-if="user.organization">, <span class="text-grey-7">{{ user.organization.name }}</span></span>
        </p>
        <div class="contact-item" v-if="user.phone">
          <q-icon class="q-pr-xs" name="call" />
          <span>{{ user.phone }}</span>
        </div>
        <div class="contact-item" v-if="user.email">
          <a :href="`mailto:${user.email}`">
            <q-icon class="q-pr-xs" name="mail" />
            <span>{{ user.email }}</span>
          </a>
        </div>
      </div>
      <div class="q-px-xs">
        <q-avatar size="70px">
          <img :src="user.photo ? `/uploads/${user.photo}` : '/img/user-nofoto.jpg'" />
        </q-avatar>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    user: Array
  }
}
</script>

<style scoped>
  /*.user-info-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }*/

  .user-personal-info {
    display: flex;
    align-items: center;
  }

  .organization-logo {
    height: 50px;
  }

  .contact-item {
    display: flex;
    align-items: center;
  }
</style>
