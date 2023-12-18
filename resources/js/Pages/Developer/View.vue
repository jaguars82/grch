<template>
  <MainLayout>

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>
      <RegularContentContainer class="q-mx-md">
        <template v-slot:content>
          <OrganizationInfo
            :id="developer.id"
            :name="developer.name"
            :address="developer.address"
            :logo="developer.logo"
            noLogoImg="developer.png"
            :url="developer.url"
            :phone="developer.phone"
            :detail="developer.detail"
          />
        </template>
      </RegularContentContainer>
      
      <!-- Map -->
      <RegularContentContainer v-if="developer.latitude && developer.longitude" class="q-mt-md q-mx-md">
        <template v-slot:content>
          <ObjectOnMap
            :address="developer.address"
            :latitude="developer.latitude"
            :longitude="developer.longitude"
            :markers="[{ latitude: developer.latitude, longitude: developer.longitude }]"
            :reverseCoords="true"
            :reverseMarkerCoords="true"
          />
        </template>
      </RegularContentContainer>

      <!-- News and Actions -->
      <RegularContentContainer v-if="newsDataProvider.length" class="q-mt-md q-mx-md q-pl-md" title="Новости / Акции">
        <template v-slot:content>
          <NewsItem class="q-mx-md" v-for="post of newsDataProvider" :item="post" />
        </template>
      </RegularContentContainer>

      <!-- Other newbuilding complexes of this developer section -->
      <RegularContentContainer v-if="newbuildingComplexDataProvider.length" class="q-mx-md  q-mt-md" title="Жилые комплексы">
        <template v-slot:content>
          <div class="row q-mt-sm">
            <template v-for="complex of newbuildingComplexDataProvider">
              <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <q-card
                  class="no-shadow full-height cursor-pointer"
                  @click="goToComplex(complex.id)"
                  @mouseenter="focusOn"
                  @mouseleave="focusOff"
                >
                  <q-card-section>
                    <q-img class="ocomplex-item-img" fit="scale-down" :src="complex.logo ? `/uploads/${complex.logo}` : `/img/newbuilding-complex.png`" :alt="complex.name" />
                  </q-card-section>
                  <q-card-section>
                    <p class="text-h5 text-center">{{ complex.name }}</p>
                  </q-card-section>
                </q-card>
              </div>
            </template>
          </div>
        </template>
      </RegularContentContainer>

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
    developer: Object,
    newbuildingComplexDataProvider: Array,
    newsDataProvider: Array,
    contactDataProvider: Array,
    officeDataProvider: Array,
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
        label: 'Зайстройщики',
        icon: 'engineering',
        url: '/developer',
        data: false,
        options: false
      },
      {
        id: 3,
        label: props.developer.name,
        icon: 'corporate_fare',
        url: `/developer/view?id=${props.developer.id}`,
        data: false,
        options: false
      },
    ])

    const focusOn = function (event) {
      event.target.classList.add('bg-grey-3')
    }

    const focusOff = function (event) {
      event.target.classList.remove('bg-grey-3')
    }

    const goToComplex = function (complexId) {
      Inertia.get('/newbuilding-complex/view', { id: complexId })
    }

    return { breadcrumbs, focusOn, focusOff, goToComplex }
  }
}
</script>

<style scoped>
  .complex-logo {
    max-height: 200px;
  }
  .ocomplex-item-img {
    height: 90px;
  }
</style>