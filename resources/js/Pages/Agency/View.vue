<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: true } }">

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <RegularContentContainer class="q-mx-md">
        <template v-slot:content>
          <OrganizationInfo
            :id="agency.id"
            :name="agency.name"
            :address="agency.address"
            :logo="agency.logo"
            noLogoImg="office.png"
            :url="agency.url"
            :phone="agency.phone"
            :detail="agency.detail"
          />
        </template>
      </RegularContentContainer>
      
      <!-- Map -->
      <RegularContentContainer v-if="agency.latitude && agency.longitude" class="q-mt-md q-mx-md">
        <template v-slot:content>
          <ObjectOnMap
            :address="agency.address"
            :latitude="agency.latitude"
            :longitude="agency.longitude"
            :markers="[{ latitude: agency.latitude, longitude: agency.longitude }]"
            :reverseCoords="true"
            :reverseMarkerCoords="true"
          />
        </template>
      </RegularContentContainer>

      <RegularContentContainer v-if="agentDataProvider.length" class="q-mt-md q-mx-md q-pl-md" title="Агенты">
        <template v-slot:content>
          <div class="row q-mt-md q-col-gutter-x-xs q-col-gutter-y-md">
            <template v-for="agent of agentDataProvider">
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                <PersonContactCard :person="agent" />
              </div>
            </template>
          </div>
        </template>
      </RegularContentContainer>
    </template>

    <template v-slot:right-drawer>
      <div class="q-pa-md">
        <p class="q-mb-xs text-h4 text-center" v-if="agency.address">Адрес</p>
        <div class="q-py-md rounded-borders bg-grey-2 text-center" v-if="agency.address">
          <q-icon size="md" class="q-mr-xs" name="location_on" />
          <span>{{ agency.address }}</span>
        </div>
        <p class="q-mb-xs q-mt-md text-h4 text-center" v-if="managerDataProvider.length">Администраторы агентства</p>
        <template v-if="managerDataProvider.length">
          <PersonContactCard class="q-mb-sm" v-for="manager of managerDataProvider" :person="manager" />
        </template>
      </div>
    </template>

  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from '@/Components/Elements/Loading.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import ObjectOnMap from '@/Components/Map/ObjectOnMap.vue'
import OrganizationInfo from '@/Components/Organization/OrganizationInfo.vue'
import PersonContactCard from '@/Components/Person/PersonContactCard.vue'

export default {
  components: {
    MainLayout, Breadcrumbs, Loading, RegularContentContainer, ObjectOnMap, OrganizationInfo, PersonContactCard
  },
  props: {
    agency: Object,
    contactDataProvider: Array,
    managerDataProvider: Array,
    agentDataProvider: Array,
  },
  setup(props) {
    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: false
      },
      {
        id: 2,
        label: 'Контакты',
        icon: 'real_estate_agent',
        url: '/agency',
        data: false,
        options: false
      },
      {
        id: 3,
        label: props.agency.name,
        icon: 'work',
        url: `/agency/view?id=${props.agency.id}`,
        data: false,
        options: false
      },
    ])

    return { breadcrumbs }
  }
}
</script>