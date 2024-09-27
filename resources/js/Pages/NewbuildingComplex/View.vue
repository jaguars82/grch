<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: $q.platform.is.mobile ? false : true } }">

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
              <p class="q-mb-sm" :class="{ 'text-h1': $q.screen.gt.sm, 'text-h2': $q.screen.sm, 'text-h3': $q.screen.xs }">{{ complex.name }}</p>
              <p v-if="complex.address"
                class="q-mt-xs text-grey"
                :class="{ 'text-h4': $q.screen.gt.sm, 'text-h5': $q.screen.sm, 'text-h6': $q.screen.xs }"
              >
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
          <!-- Complex description and images -->
          <div v-if="complex.images.length || complex.detail" class="row q-mt-md q-col-gutter-y-md">
            <div v-if="complex.images.length" class="col-12 col-lg-7">
              <q-carousel
                :height="$q.screen.xs ? '300px' : $q.screen.gt.md ? '400px' : '450px'"
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

      <!-- Map -->
      <RegularContentContainer v-if="complex.latitude && complex.longitude" class="q-mt-md q-mx-md">
        <template v-slot:content>
          <ObjectOnMap
            :address="complex.address"
            :latitude="complex.latitude"
            :longitude="complex.longitude"
            :markers="[{ latitude: complex.latitude, longitude: complex.longitude }]"
            :reverseCoords="true"
            :reverseMarkerCoords="true"
          />
        </template>
      </RegularContentContainer>

      <!-- Chesses section -->
      <RegularContentContainer v-if="complex.newbuildings.length" class="q-mx-md q-mt-md" title="Шахматки/позиции">
        <template v-slot:content>
          <template v-for="building of complex.newbuildings" :key="building.id">
            <div v-if="building.active">
              <q-expansion-item
                class="q-my-sm"
                header-class="rounded-borders position-item bg-primary text-white"
                expand-icon-class="text-white"
              >
                <template v-slot:header>
                  <div class="row items-center full-width">
                    <!-- Building info header for xs screens -->
                    <div class="col-12 xs">
                      <span class="text-bold">{{ building.name }}</span>
                      <span v-if="building.aviableFlats > 0 || building.reservedFlats > 0" class="text-bold">.  </span>
                      <span v-if="building.aviableFlats > 0">Доступно {{ building.aviableFlats }}</span>
                      <span v-if="building.aviableFlats > 0 && building.reservedFlats > 0">.  </span>
                      <span v-if="building.reservedFlats > 0">Бронь {{ building.reservedFlats }}</span>
                      <span>, {{ building.deadlineString }}</span>
                      <span>, {{ building.totalFloorString }}</span>
                    </div>
                    <!-- Building info header for sm+ screens -->
                    <div class="col-2 text-bold gt-xs">
                      <div class="flex justify-between">
                        <div>{{ building.name }}</div>
                        <q-separator class="gt-sm" color="white" vertical />
                      </div>
                    </div>
                    <div class="col-3 gt-xs">
                      <div class="flex justify-between">
                        <div class="q-px-sm">
                          <span v-if="building.aviableFlats > 0">Доступно {{ building.aviableFlats }}</span>
                          <span v-if="building.aviableFlats > 0 && building.reservedFlats > 0">.  </span>
                          <span v-if="building.reservedFlats > 0">Бронь {{ building.reservedFlats }}</span>
                        </div>
                        <q-separator class="gt-sm" color="white" vertical />
                      </div>
                    </div>
                    <div class="col-3 text-bold gt-xs">
                      <div class="flex justify-between">
                        <div class="q-px-sm">
                          {{ building.deadlineString }}
                        </div>
                        <q-separator class="gt-sm" color="white" vertical />
                      </div>
                    </div>
                    <div class="col-3 gt-xs">
                      <div class="q-px-sm">{{ building.totalFloorString }}</div>
                    </div>
                  </div>
                </template>

                <template v-if="building.entrances.length">
                  <q-expansion-item
                    class="q-my-sm"
                    v-for="entrance of building.entrances"
                    dense
                    dense-toggle
                  >
                    <template v-slot:header>
                      <div class="row items-center full-width">
                        <!-- Entrance info header for xs & md screens -->
                        <div class="col-12 lt-md">
                          <span class="text-bold">{{ entrance.name }}</span>
                          <span v-if="entrance.aviableFlats > 0 || entrance.reservedFlats > 0" class="text-bold">, </span>
                          <span v-if="entrance.aviableFlats > 0" class="text-grey">доступно - <span class="text-bold">{{ entrance.aviableFlats }}</span>, </span>
                          <span v-if="entrance.reservedFlats > 0" class="text-grey">бронь - <span class="text-bold">{{ entrance.reservedFlats }}</span>, </span>
                          <span v-if="entrance.aviableFlats < 1 && entrance.reservedFlats < 1" class="text-bold">, </span>
                          <span class="text-grey">{{ entrance.deadlineString }}</span>
                          <span v-if="entrance.floors" class="text-grey">, {{ entrance.floors }} этажей</span>
                          <span v-if="entrance.material" class="text-grey text-lowercase">, {{ entrance.material }}</span>
                        </div>
                        <!-- Entrance info header for md+ screens -->
                        <div class="col-2 gt-sm text-bold">{{ entrance.name }}</div>
                        <div class="col gt-sm text-grey">
                          <span v-if="entrance.aviableFlats > 0">доступно - <span class="text-bold">{{ entrance.aviableFlats }}</span>, </span>
                          <span v-if="entrance.reservedFlats > 0">бронь - <span class="text-bold">{{ entrance.reservedFlats }}</span>, </span>
                          <span>{{ entrance.deadlineString }}</span>
                          <span v-if="entrance.floors">, {{ entrance.floors }} этажей</span>
                          <span v-if="entrance.material" class="text-lowercase">, {{ entrance.material }}</span>
                        </div>
                      </div>
                    </template>

                    <div class="bg-grey-3 q-pl-none q-py-sm q-pr-sm rounded-borders overflow-auto relative-position">
                      <ChessLegend :statusLabels="flatStatuses" :existingStatuses="entrance.flatStatuses" />
                      <div class="row q-pl-none relative-position no-wrap w-max-content" v-for="floor of Object.keys(entrance.flats).reverse()">
                        <div class="floor-cell q-pl-sm text-weight-bolder bg-grey-3 text-grey">{{ floor }}</div>
                        <div>
                          <div class="row no-wrap">
                            <FlatCell v-for="flatId of Object.keys(entrance.flats[floor])" :flat="entrance.flats[floor][flatId]" />
                          </div>
                        </div>
                      </div>
                    </div>
                    
                  </q-expansion-item>
                </template>
              </q-expansion-item>
            </div>
          </template>
        </template>
      </RegularContentContainer>

      <!-- Banks accreditation section -->
      <RegularContentContainer v-if="complex.banks.length" class="q-mx-md  q-mt-md" title="Аккредитация банков">
        <template v-slot:content>
          <div class="row q-mt-sm">
            <template v-for="bank of complex.banks">
              <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <q-card
                  class="no-shadow full-height cursor-pointer"
                  @mouseenter="focusOn"
                  @mouseleave="focusOff"
                  @click="goUrl(bank.url)"
                >
                  <!-- Menu prototype for multiple actions for a bank -->
                  <!--<q-menu
                    touch-position
                  >
                    <q-list dense style="min-width: 100px">
                      <q-item clickable v-close-popup>
                        <q-item-section>Сайт банка</q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>-->

                  <q-card-section>
                    <q-img
                      class="bank-item-img"
                      fit="scale-down"
                      :src="bank.logo ? `/uploads/${bank.logo}` : `/img/bank.png`"
                      :alt="bank.name"
                    />
                  </q-card-section>
                  <q-card-section>
                    <p
                      class="text-center"
                      :class="{ 'text-h6': $q.screen.xs, 'text-h5': $q.screen.gt.xs }"
                    >
                      {{ bank.name }}
                    </p>
                  </q-card-section>
                </q-card>
              </div>
            </template>
          </div>
        </template>
      </RegularContentContainer>

      <!-- Documents section -->
      <RegularContentContainer v-if="complex.documents.length" class="q-mx-md  q-mt-md" title="Документация">
        <template v-slot:content>
          <div class="row q-mt-sm">
            <template v-for="doc of complex.documents">
              <div class="col-12 col-md-6 col-lg-4">
                <FileDownloadable :file="doc" />
              </div>
            </template>
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
                    <q-img
                      class="ocomplex-item-img"
                      fit="scale-down"
                      :src="otherComplex.logo ? `/uploads/${otherComplex.logo}` : `/img/newbuilding-complex.png`"
                      :alt="otherComplex.name"
                    />
                  </q-card-section>
                  <q-card-section>
                    <p
                      class="text-center"
                      :class="{ 'text-h6': $q.screen.xs, 'text-h5': $q.screen.gt.xs }"
                    >
                      {{ otherComplex.name }}
                    </p>
                  </q-card-section>
                </q-card>
              </div>
            </template>
          </div>
        </template>
      </RegularContentContainer>
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

  <q-dialog
    v-model="imageViewer"
    persistent
    :maximized="true"
    transition-show="slide-up"
    transition-hide="slide-down"
  >
    <q-card>
      <q-bar>
        <q-space />
        <q-btn round dense flat icon="close" v-close-popup />
      </q-bar>
      <q-card-section class="q-pa-none full-height">
        <q-carousel
          class="full-height"
          v-if="complex.images.length"
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
            @click="imageViewer = false"
          />
        </q-carousel>
      </q-card-section>
    </q-card>
  </q-dialog>

</template>
  
<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea } from '@/helpers/formatter'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from '@/Components/Elements/Loading.vue'
import ParamPair from '@/Components/Elements/ParamPair.vue'
import FileDownloadable from '@/Components/File/FileDownloadListItem.vue'
import ChessLegend from '@/Components/Chess/ChessLegend.vue'
import FlatCell from '@/Components/Chess/FlatCell.vue'
import FinishingCard from '@/Components/FinishingCard.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'
import ObjectOnMap from '@/Components/Map/ObjectOnMap.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'

export default {
  props: {
    flatStatuses: Object,
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
    MainLayout, Breadcrumbs, Loading, FileDownloadable, ParamPair, ChessLegend, FlatCell, FinishingCard, AdvantagesBlock, ObjectOnMap, RegularContentContainer
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

    const imageViewer = ref(false)

    const onImageClick = (image) => {
      imageViewer.value = true
    }

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

    const goUrl = (url) => {
      window.open(url, '_blank')
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
      imageViewer,
      onImageClick,
      focusOn,
      focusOff,
      goToComplex,
      goUrl,
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
  .ocomplex-item-img, .bank-item-img {
    height: 90px;
  }
  .fitwindow {
    height: 100vh;
    max-height: 100vh;
    width: 100%;
  }
  .w-max-content {
    width: max-content;
  }
  .floor-cell {
    width: 25px;
    min-width: 25px;
    position: sticky;
    top: 0;
    left: 0;
  }
</style>
