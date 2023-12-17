<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: true } }">

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>

      <!-- Newbuilding complex description section -->
      <RegularContentContainer class="q-mx-md ">
        <template v-slot:content>
          <!-- Complex title (name and location) -->
          <div class="row items-start justify-between">
            <div class="col-10 col-sm-12">
              <h1>{{ complex.name }}</h1>
              <p v-if="complex.address" class="q-mt-xs text-h4 text-grey">
                {{ complex.address }}
              </p>
            </div>
            <div class="col-2 lt-sm">
              <img :src="complex.logo ? `/uploads/${complex.logo}` : `/img/newbuilding-complex.png`" :alt="complex.name" />
            </div>
          </div>
          <!-- Prices by flat type and complex logo -->
          <div class="row justify-between items-center">
            <div class="col-12 col-sm-7">
              <ParamPair
                v-for="priceByFlat of complex.flats_by_room"
                :paramName="priceByFlat.label"
                :paramValue="priceByFlat.price"
                :link="priceByFlat.search_url"
              />
            </div>
            <div class="col-4 gt-xs">
              <q-img class="complex-logo" fit="scale-down" :src="complex.logo ? `/uploads/${complex.logo}` : `/img/newbuilding-complex.png`" :alt="complex.name" />
            </div>
          </div>
          <!-- Comples description and images -->
          <div v-if="complex.images.length || complex.detail" class="row q-mt-md q-col-gutter-y-md">
            <div v-if="complex.images.length" class="col-12 col-lg-7">
              <q-carousel
                :height="$q.screen.xs ? '300px' : $q.screen.gt.md ? '100%' : '400px'"
                swipeable
                animated
                v-model="slide"
                arrows
                thumbnails
                infinite
              >
                <q-carousel-slide
                  v-for="image of complex.images"
                  :key="image.id"
                  :name="image.id"
                  :img-src="`/uploads/${image.file}`"
                  @click="onImageClick(image)"
                />
              </q-carousel>
            </div>
            <div v-if="complex.detail" class="col-12 col-lg-5" v-html="complex.detail"></div>
          </div>
        </template>
      </RegularContentContainer>

      <!-- Chesses section -->
      <RegularContentContainer v-if="complex.newbuildings.length" class="q-mx-md q-mt-md" title="Шахматки/позиции">
        <template v-slot:content>
          <div v-for="building of complex.newbuildings" :key="building.id">
            <q-expansion-item
              class="q-my-sm"
              header-class="rounded-borders position-item"
            >
              <template v-slot:header>
                <div class="row items-center full-width">
                  <div class="col-4">{{ building.name }}</div>
                </div>
              </template>

              <template v-if="building.entrances.length">
                <q-expansion-item
                  v-for="entrance of building.entrances"
                >
                  <template v-slot:header>
                    <div class="row items-center full-width">
                      <div class="col-4">{{ entrance.name }}</div>
                    </div>
                  </template>

                  <div class="row" v-for="floor of Object.keys(entrance.flats).reverse()">
                    <div class="col-1">{{ floor }}</div>
                    <div class="col-11">
                      <div class="row">
                        <div class="column cursor-pointer"
                          v-for="flatId of Object.keys(entrance.flats[floor])"
                          @click="goToFlat(entrance.flats[floor][flatId].id)"
                          @mouseenter="focusOn"
                          @mouseleave="focusOff"
                        >
                          <div class="row justify-between">
                            <div>{{ entrance.flats[floor][flatId].rooms }}</div>
                            <div>{{ entrance.flats[floor][flatId].number }}</div>
                          </div>
                          <div>
                            <span v-if="entrance.flats[floor][flatId].has_discount">{{ entrance.flats[floor][flatId].price_range }}</span>
                            <span v-else>{{ asCurrency(entrance.flats[floor][flatId].price_cash) }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                  </div>
                  
                </q-expansion-item>
              </template>
            </q-expansion-item>
          </div>
        </template>
      </RegularContentContainer>

      <!-- Other newbuilding complexes of this developer section -->
      <RegularContentContainer v-if="otherNC.length" class="q-mx-md  q-mt-md" title="Другие ЖК этого застройщика">
        <template v-slot:content>
          <div class="row q-mt-sm">
            <template v-for="otherComplex of otherNC">
              <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <q-card
                  class="no-shadow full-height cursor-pointer"
                  @click="goToComplex(otherComplex.id)"
                  @mouseenter="focusOn"
                  @mouseleave="focusOff"
                >
                  <q-card-section>
                    <q-img class="ocomplex-item-img" fit="scale-down" :src="otherComplex.logo ? `/uploads/${otherComplex.logo}` : `/img/newbuilding-complex.png`" :alt="otherComplex.name" />
                  </q-card-section>
                  <q-card-section>
                    <p class="text-h5 text-center">{{ otherComplex.name }}</p>
                  </q-card-section>
                </q-card>
              </div>
            </template>
          </div>
        </template>
      </RegularContentContainer>
      <pre>{{ complex }}</pre>
    </template>

    <template v-slot:right-drawer>
      <div class="q-pa-md">
        <p class="q-mb-xs text-h4 text-center">Стоимость объектов</p>
        <div class="q-py-md rounded-borders bg-grey-2 relative-position">
          <q-badge v-if="complex.maxDiscount" color="orange" floating>
            Скидка до {{ complex.maxDiscount }}
          </q-badge>
          <p class="q-mb-xs text-h3 text-bold text-blue-8 text-center">
            {{ complex.priceRange }}
          </p>
        </div>

        <p class="q-mb-xs q-mt-md text-h4 text-center">Информация</p>
        <ParamPair
          paramName="Площадь"
          :paramValue="complex.areaRange"
        />
        <ParamPair
          v-if="complex.materials.length"
          paramName="Материал"
          :paramValue="complex.materials"
        />
        <ParamPair
          v-if="complex.furnishes.length"
          paramName="Отделка"
        >
          <template v-slot:customValue>
            <template v-for="(furnish, index) of complex.furnishes">
              <span class="text-blue-9 cursor-pointer" @click="showFurnishViewer(furnish.id)">{{ furnish.name }}</span>
              <span v-if="index < complex.furnishes.length - 1">, </span>
            </template>
          </template>
        </ParamPair>
        <!-- Furnish view fullscreen dialog window -->
        <q-dialog
          v-if="complex.furnishes.length"
          class="fitwindow"
          v-model="furnishViewer"
          :maximized="true"
          transition-show="scale"
          transition-hide="slide-down"
        >
          <q-card>
            <q-card-section class="q-pa-none full-height">

              <div class="column full-height">
                <div class="col-1">

                  <q-toolbar class="q-pt-sm q-pr-sm bg-white full-height">
                    <q-space />
                    <q-btn class="self-start" round dense flat icon="close" v-close-popup />
                  </q-toolbar>

                </div>
                <div class="col-11 q-px-md">
                  <FinishingCard :finishing="furnishViewerContent" :border="false" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </q-dialog>

        <ParamPair
          v-if="complex.freeFlats"
          paramName="Свободно"
          :paramValue="`${complex.freeFlats} квартир`"
        />

        <ParamPair
          v-if="complex.minYearlyRate"
          paramName="Ставка от"
          :paramValue="complex.minYearlyRate"
        />

        <ParamPair
          v-if="complex.nearestDeadline"
          paramName="Ближайшая сдача"
          :paramValue="complex.nearestDeadline"
        />

        <div class="q-mt-md">
          <AdvantagesBlock v-if="complex.advantages.length" :advantages="complex.advantages" :inColumn="true" />
        </div>

      </div>
    </template>

  </MainLayout>
</template>
  
<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea } from '@/helpers/formatter'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import ParamPair from '@/Components/Elements/ParamPair.vue'
import FinishingCard from '@/Components/FinishingCard.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'

export default {
  props: {
    complex: {
      type: Object,
      derfault: {}
    },
    otherNC: {
      type: Array,
      derfault: []
    },
  },
  components: {
    MainLayout, Breadcrumbs, Loading, ParamPair, FinishingCard, AdvantagesBlock, RegularContentContainer
  },
  setup(props) {
    const breadcrumbs = ref([
      {
        id: 1,
        label: 'Главная',
        icon: 'home',
        url: '/',
        data: false,
        options: 'native'
      },
      {
        id: 2,
        label: 'Жилые комплексы',
        icon: 'domain',
        url: '/newbuilding-complex',
        data: false,
        options: false
      },
      {
        id: 3,
        label: props.complex.name,
        icon: 'apartment',
        url: `/newbuilding-complex/view?id=${props.complex.id}`,
        data: false,
        options: false
      },
    ])

    const slide = props.complex.images.length ? ref(props.complex.images[0].id) : ref(false)

    const focusOn = function (event) {
      event.target.classList.add('bg-grey-3')
    }

    const focusOff = function (event) {
      event.target.classList.remove('bg-grey-3')
    }

    const goToComplex = function (complexId) {
      Inertia.get('/newbuilding-complex/view', { id: complexId })
    }

    const goToFlat = function (flatId) {
      Inertia.get('/flat/view', { id: flatId })
    }

    const furnishViewer = ref(false)

    const furnishViewerContent = ref({})

    const showFurnishViewer = (furnishId) => {
      furnishViewerContent.value = props.complex.furnishes.find(furnish => { return furnish.id == furnishId })
      furnishViewer.value = true
    }

    return {
      asCurrency,
      breadcrumbs,
      slide,
      focusOn,
      focusOff,
      goToComplex,
      goToFlat,
      furnishViewer,
      showFurnishViewer,
      furnishViewerContent
    }
  },
}
</script>

<style scoped>
  .row.justify-between::before, .row.justify-between::after {
    display: none;
  }
  .complex-logo {
    max-height: 200px;
  }
  .ocomplex-item-img {
    height: 90px;
  }
  .fitwindow {
    height: 100vh;
    max-height: 100vh;
    width: 100%;
  }
</style>
