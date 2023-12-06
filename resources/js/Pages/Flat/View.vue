<template>
  <MainLayout>

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>

      <pre>{{ flat }}</pre>

      <!-- Layouts viewer section -->
      <RegularContentContainer>
        <template v-slot:content>

          <div class="row">
            <div class="col-1">
              <q-btn round unelevated icon="zoom_out_map" @click="fullscreenLayoutsViewer = true" />
            </div>
            <div class="col-11">
              <q-tabs
                v-model="layoutViewerTab"
                outside-arrows
                mobile-arrows
                inline-label
                dense
                align="right"
                class="text-primary"
              >
                <q-tab name="apartment" icon="space_dashboard" label="Квартира" />
                <q-tab name="floor" icon="dashboard" label="Этаж" />
                <q-tab name="plan" icon="backup_table" label="Генплан" />
              </q-tabs>
            </div>
          </div>

          <div class="layouts-viewer-container" :class="{ 'layouts-viewer-container-large': $q.screen.gt.xs }">
            <q-tab-panels class="full-height" v-model="layoutViewerTab" @transition="onTabChange">
              <q-tab-panel name="apartment">
                <img class="fit" :src="`/uploads/${flat.layout}`" alt="flat layout" />
              </q-tab-panel>
              <q-tab-panel name="floor">
                <FloorLayoutForAFlat
                  :floorLayout="flat.floorBackground"
                  :selfCoords="flat.layout_coords"
                  :viewBox="flat.svgViewBox"
                  :neighboringFlats="flat.neighboringFlats"
                />
              </q-tab-panel>
              <q-tab-panel name="plan">
                <img class="fit" :src="`/uploads/${flat.masterPlan}`" alt="masterplan" />
              </q-tab-panel>
            </q-tab-panels>
          </div>
        </template>
      </RegularContentContainer>

      <!-- Layouts fullscreen popup window -->
      <q-dialog
        class="fitwindow"
        v-model="fullscreenLayoutsViewer"
        :maximized="true"
        transition-show="scale"
        transition-hide="slide-down"
      >

        <q-card>
          <q-card-section class="q-pa-none full-height">

            <div class="column full-height">
              <div class="col-1">

                <q-toolbar class="q-pt-sm q-pr-sm bg-white full-height">
                  <Compass
                    v-if="flat.entranceAzimuth && layoutViewerTab !== 'plan'"
                    ref="compass"
                    :azimuth="flat.entranceAzimuth"
                    class="full-height cursor-pointer"
                  />
                  <q-space />
                  <q-btn class="self-start" round dense flat icon="close" v-close-popup />
                </q-toolbar>

              </div>
              <div class="col-11">

                <div class="row full-height">
                  <div class="col-10 full-height">
                    <q-tab-panels class="full-height" v-model="layoutViewerTab">
                      <q-tab-panel class="no-scroll overflow-hidden" name="apartment">
                        <div class="row full-height justify-center">
                          <div class="col-12 col-md-11 col-lg-10 col-xl-10 full-height">
                            <img :id="`flat-${flat.id}-layout-fs`" ref="flatLayoutFS" class="fit" :src="`/uploads/${flat.layout}`" alt="flat layout" />
                          </div>
                        </div>
                      </q-tab-panel>
                      <q-tab-panel class="no-scroll overflow-hidden" name="floor">
                        <div class="row full-height justify-center">
                          <div class="col-12 col-md-11 col-lg-10 col-xl-10 full-height">
                            <FloorLayoutForAFlat
                              :id="`floor-${flat.id}-layout-fs`"
                              :floorLayout="flat.floorBackground"
                              :selfCoords="flat.layout_coords"
                              :viewBox="flat.svgViewBox"
                              :neighboringFlats="flat.neighboringFlats"
                            />
                          </div>
                        </div>
                      </q-tab-panel>
                      <q-tab-panel name="plan">
                        <img class="fit" :src="`/uploads/${flat.masterPlan}`" alt="masterplan" />
                      </q-tab-panel>
                    </q-tab-panels>
                  </div>
                  <div class="col-2">
                    <q-tabs
                      v-model="layoutViewerTab"
                      switch-indicator
                      vertical
                      class="text-primary"
                    >
                      <q-tab name="apartment" icon="space_dashboard" label="Квартира" />
                      <q-tab name="floor" icon="dashboard" label="Этаж" />
                      <q-tab name="plan" icon="backup_table" label="Генплан" />
                    </q-tabs>
                  </div>
                </div>

              </div>
            </div>


          </q-card-section>
        </q-card>
      </q-dialog>

      <!-- Chesses section -->
      <RegularContentContainer v-if="true" class="q-mt-md" title="Шахматки/позиции">
        <template v-slot:content>
        </template>
      </RegularContentContainer>

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
import Compass from '@/Components/Svg/Compass.vue'
import FloorLayoutForAFlat from '@/Components/Svg/FloorLayoutForAFlat.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    flat: {
      type: Object,
      derfault: {}
    },
    otherNC: {
      type: Array,
      derfault: []
    },
  },
  components: {
    MainLayout, Breadcrumbs, Loading, ParamPair, Compass, FloorLayoutForAFlat, RegularContentContainer
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
        label: props.flat.complex.name,
        icon: 'apartment',
        url: `/newbuilding-complex/view?id=${props.flat.complex.id}`,
        data: false,
        options: false
      },
      {
        id: 4,
        label: props.flat.number,
        icon: 'tag',
        url: `/flat/view?id=${props.flat.id}`,
        data: false,
        options: false
      },
    ])

    const layoutViewerTab = ref('apartment')

    const fullscreenLayoutsViewer = ref(false)

    const compass = ref(null)

    const emitter = useEmitter()

    emitter.on('compass-orient', (payload) => {
      const targetsToOrient = [
        `flat-${props.flat.id}-layout-fs`,
        `floor-${props.flat.id}-layout-fs`
      ]
      targetsToOrient.forEach(targetId => {
        const currentTarget = document.getElementById(targetId)
        if (currentTarget) {
          if (payload.orientedToNorth === true) {
            currentTarget.style.transition = 'transform 1s'
            currentTarget.style.transform = 'rotate(0deg) scale(1)'
          } else {
            currentTarget.style.transition = 'transform 1s'
            currentTarget.style.transform = 'rotate('+ -payload.angle +'deg) scale(0.75)'
          }
        }      
      })
    })

    const onTabChange = () => {
      if (compass.value) {
        compass.value.resetCompass()
      }
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

    return {
      asCurrency,
      breadcrumbs,
      layoutViewerTab,
      fullscreenLayoutsViewer,
      compass,
      onTabChange,
      focusOn,
      focusOff,
      goToComplex,
      goToFlat
    }
  },
}
</script>

<style scoped>
.row.justify-between::before, .row.justify-between::after {
  display: none;
}
.ocomplex-item-img {
  height: 90px;
}
.layouts-viewer-container-large {
  height: 650px;
}
.layouts-viewer-container {
  max-height: 650px;
  min-height: 100px;
}
.fitwindow {
  height: 100vh;
  max-height: 100vh;
  width: 100%;
}
.fitparent {
  height: 100%;
  max-height: 100%;
}
</style>
