<template>
  <MainLayout :drawers="{ left: { is: false, opened: false }, right: { is: true, opened: $q.platform.is.mobile ? false : true } }">

    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>

    <template v-slot:main>

      <!-- Flat description section -->
      <RegularContentContainer class="q-mx-md">
        <template v-slot:content>
          <div class="row items-center justify-start">
            <div class="col-12">
              <p class="q-mt-xs q-mb-sm" :class="{ 'text-h1': $q.screen.gt.sm, 'text-h2': $q.screen.sm, 'text-h3': $q.screen.xs }">
                <template v-if="flat.rooms == 0">
                  <span>Квартира {{ asNumberString(flat.rooms) }}  № {{ flat.number }}</span>
                </template>
                <template v-else>
                  <span class="text-capitalize">{{ asNumberString(flat.rooms) }}</span>комнатная квартира № {{ flat.number }}
                </template>
              </p>
              <div>
                <q-chip v-if="flat.status === 0" color="positive" class="text-white">
                  продается
                </q-chip>
                <q-chip v-else-if="flat.status === 1" color="warning" class="text-white">
                  бронь
                </q-chip>
                <q-chip v-else-if="flat.status === 2" color="negative" class="text-white">
                  продана
                </q-chip>
              </div>
              <p
                class="q-mb-xs text-grey"
                :class="{ 'text-h4': $q.screen.gt.sm, 'text-h5': $q.screen.sm, 'text-h6': $q.screen.xs }"
              >
                {{ flat.developer.name }} > {{ flat.complex.name }} > {{ flat.building.name }} >
                <span v-if="flat.entrance.name"> {{ flat.entrance.name }}</span>
                <span v-else> Подъезд {{ flat.section }}</span>
              </p>
            </div>
            <!-- Flat price (single value or range) -->
            <div class="col-12">
              <p class="text-bold text-blue-8 q-mb-xs" :class="{ 'text-h1': $q.screen.gt.sm, 'text-h2': $q.screen.sm, 'text-h3': $q.screen.xs }">
                <span v-if="flat.hasDiscount && flat.priceRange && flat.status === 0">
                  {{ flat.priceRange }}
                </span>
                <span v-else>
                  {{ asCurrency(flat.price_cash) }}
                </span>
                <PriceChangeIndicator v-if="flat.status === 0 && flat.priceChange.length > 1" :data="flat.priceChange" :size="$q.screen.gt.sm ? 'xl' : 'md'" />
              </p>
              <!-- Prices with discount (if any) -->
              <q-expansion-item
                header-class="rounded-borders q-px-xs"
                v-if="flat.status === 0 && flat.allDiscounts.length"
                v-model="discountListExpanded"
                icon="price_change"
                label="Варианты стоимости"
                caption="на основании действующих скидок и акций"
              >
                <template v-slot:header>
                  <div class="row items-center full-width">
                    <div class="col">
                      <q-avatar size="md" icon="price_change" color="orange" text-color="white" />
                    </div>
                    <div :class="{ 'col-10': $q.screen.xs, 'col-11': $q.screen.gt.xs }">
                      Варианты стоимости
                    </div>
                  </div>
                </template>

                <div class="row items-center q-py-sm">
                  <div class="col-12 q-px-sm lt-md">
                    <span>{{ asCurrency(flat.price_cash) }}</span>
                    <q-badge class="q-mx-sm" color="orange">
                      базовая цена
                    </q-badge>
                  </div>
                  <div class="col-2 gt-sm q-px-sm text-strong">
                    {{ asCurrency(flat.price_cash) }}
                  </div>
                  <div class="col-2 gt-sm">
                    <q-badge color="orange">
                      базовая цена
                    </q-badge>
                  </div>
                </div>
                <div v-for="discount of flat.allDiscounts" class="row items-center q-py-sm">
                  <div class="col-12 q-px-sm lt-md">
                    <span>{{ asCurrency(discount.price) }}</span>
                    <q-badge class="q-mx-sm" color="orange">
                      <span v-if="discount.discount_type === 0">- {{ discount.discount }} %</span>
                      <span v-else-if="discount.discount_type === 1">- {{ asCurrency(discount.discount_amount) }}</span>
                      <span v-else-if="discount.discount_type === 2">цена по акции</span>
                    </q-badge>
                    <span class="text-grey" v-if="discount.resume">- {{ discount.resume }}</span>
                    <span v-else>- {{ discount.title }}</span>
                    <q-icon name="info" color="grey" size="sm" class="q-px-md cursor-pointer" @click="showDiscountInfoWindow(discount.id)">
                    </q-icon>
                  </div>
                  <div class="col-2 gt-sm q-px-sm text-strong">
                    {{ asCurrency(discount.price) }}
                  </div>
                  <div class="col-2 gt-sm">
                    <q-badge color="orange">
                      <span v-if="discount.discount_type === 0">- {{ discount.discount }} %</span>
                      <span v-else-if="discount.discount_type === 1">- {{ asCurrency(discount.discount_amount) }}</span>
                      <span v-else-if="discount.discount_type === 2">цена по акции</span>
                    </q-badge>
                  </div>
                  <div class="col-8 gt-sm text-grey">
                    <span v-if="discount.resume">- {{ discount.resume }}</span>
                    <span v-else>- {{ discount.title }}</span>
                    <q-icon name="info" size="sm" class="q-px-md cursor-pointer" @click="showDiscountInfoWindow(discount.id)">
                    </q-icon>
                  </div>
                </div>
              </q-expansion-item>
            </div>

            <!-- Action buttons (add to favorite, commercial, reserve) -->
            <div class="row q-mt-md full-width items-center justify-end">
              <FlatActionButtons
                v-if="flat.status === 0"
                :res="flat.is_reserved != 1 && flat.developer.hasRepresentative"
                :flat="{ id: flat.id, isFavorite: flat.isFavorite }"
              />
            </div>

          </div>
        </template>
      </RegularContentContainer>

      <!-- Newbuilding complex description section -->
      <RegularContentContainer class="q-mt-md q-mx-md">
        <template v-slot:content>
          <!-- Complex title (name and location) -->
          <div class="row items-center justify-between">
            <div class="col-10">
              <p class="q-mb-xs text-h3 text-grey-7">{{ flat.complex.name }}</p>
              <p class="q-mb-xs text-h5">{{ flat.complex.address }}</p>
            </div>
            <div class="col-2">
              <q-img class="complex-logo" fit="scale-down" :src="flat.complex.logo ? `/uploads/${flat.complex.logo}` : `/img/newbuilding-complex.png`" :alt="flat.complex.name" />
            </div>
          </div>
          <div class="row justify-between">
            <div class="col-12 col-sm-5">
              <ParamPair
                paramName="Застройщик"
              >
                <template v-slot:customValue>
                  <inertia-link :href="`/developer/view?id=${flat.developer.id}`">
                    {{ flat.developer.name }}
                  </inertia-link>
                </template>
              </ParamPair>
              <ParamPair
                v-if="flat.building.id"
                paramName="Позиция"
                :paramValue="flat.building.name"
              />
              <ParamPair
                v-if="flat.area"
                paramName="Площадь"
                :paramValue="asArea(flat.area)"
              />
              <ParamPair
                v-if="flat.entrance.number"
                paramName="Подъезд"
                :paramValue="flat.entrance.number"
              />
              <ParamPair
                v-if="flat.floor && flat.building.total_floor"
                paramName="Этаж"
                :paramValue="asFloor(flat.floor, flat.building.total_floor)"
              />
              <ParamPair
                v-if="flat.deadLine"
                paramName="Срок сдачи"
                :paramValue="flat.deadLine"
              />
            </div>
            <div class="col-12 col-sm-5">
              <ParamPair
                paramName="Жилой комплекс"
              >
                <template v-slot:customValue>
                  <inertia-link :href="`/newbuilding-complex/view?id=${flat.complex.id}`">
                    {{ flat.complex.name }}
                  </inertia-link>
                </template>
              </ParamPair>
              <ParamPair
                v-if="flat.deadLine"
                paramName="Материал"
                :paramValue="flat.building.material ? flat.building.material : 'не указано'"
              />
              <ParamPair
                v-if="flat.complex.furnishes.length"
                paramName="Отделка"
              >
                <template v-slot:customValue>
                  <template v-for="(furnish, index) of flat.complex.furnishes">
                    <span class="text-blue-9 cursor-pointer" @click="showFurnishViewer(furnish.id)">{{ furnish.name }}</span>
                    <span v-if="index < flat.complex.furnishes.length - 1">, </span>
                  </template>
                </template>
              </ParamPair>
              <ParamPair
                v-if="flat.complex.freeFlats"
                paramName="Свободно"
                :paramValue="`${flat.complex.freeFlats} квартир`"
              />
              <ParamPair
                v-if="flat.area && flat.area != 0"
                paramName="Цена за метр"
                :paramValue="asPricePerArea(Math.round(flat.price_cash / flat.area))"
              />
              <ParamPair
                v-if="flat.complex.minYearlyRate"
                paramName="Ставка от"
                :paramValue="flat.complex.minYearlyRate"
              />
            </div>
          </div>
        </template>
      </RegularContentContainer>

      <!-- Layouts viewer section -->
      <RegularContentContainer class="q-mt-md q-mx-md">
        <template v-slot:content>

          <div class="row">
            <div class="col">
              <q-btn round unelevated icon="zoom_out_map" @click="fullscreenLayoutsViewer = true" />
            </div>
            <div :class="{ 'col-10': $q.screen.xs, 'col-11': $q.screen.gt.xs }">
              <q-tabs
                v-model="layoutViewerTab"
                outside-arrows
                mobile-arrows
                inline-label
                dense
                align="right"
                class="text-primary"
              >
                <q-tab name="apartment" icon="space_dashboard" :label="$q.screen.gt.xs ? 'Квартира' : 'кв.'" />
                <q-tab name="floor" icon="dashboard" :label="$q.screen.gt.xs ? 'Этаж' : 'эт.'" />
                <q-tab name="plan" icon="backup_table" :label="$q.screen.gt.xs ? 'Генплан' : 'план'" />
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

      <!-- Price change chart section -->
      <RegularContentContainer v-if="flat.priceChange.length > 1" class="q-mt-md q-mx-md" title="Изменение цены">
        <template v-slot:content>
          <PriceChangeChart :data="flat.priceChange" />
        </template>
      </RegularContentContainer>

      <!-- Chesses section -->
      <RegularContentContainer v-if="true" class="q-mt-md q-mx-md" title="Шахматки/позиции">
        <template v-slot:content>

          <template v-for="building of flat.complex.newbuildings" :key="building.id">
            <div v-if="building.active">
              <q-expansion-item
                class="q-my-sm"
                header-class="rounded-borders position-item bg-primary text-white"
                expand-icon-class="text-white"
                :default-opened="building.id === flat.newbuilding_id"
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
                    :key="entrance.id"
                    :default-opened="entrance.id === flat.entrance_id"
                    dense
                    dense-toggle
                    @before-show="loadFlats(entrance.id)"
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

                    <div v-if="flatsData[entrance.id]" class="bg-grey-3 q-pl-none q-py-sm q-pr-sm rounded-borders overflow-auto relative-position">
                      <ChessLegend :statusLabels="flatStatuses" :existingStatuses="entrance.flatStatuses" />
                      <div class="row q-pl-none relative-position no-wrap w-max-content" v-for="floor of Object.keys(flatsData[entrance.id]).reverse()" :key="floor">
                        <div class="floor-cell q-pl-sm text-weight-bolder bg-grey-3 text-grey">{{ floor }}</div>
                        <div>
                          <div class="row no-wrap">
                            <FlatCell v-for="flatId of Object.keys(flatsData[entrance.id][floor])" :key="flatId" :flat="flatsData[entrance.id][floor][flatId]" :currentlyOpened="flatsData[entrance.id][floor][flatId].id == flat.id" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-else class="text-grey q-pa-md">Загрузка данных...</div>

                    <!--<div class="bg-grey-3 q-pl-none q-py-sm q-pr-sm rounded-borders overflow-auto">
                      <ChessLegend :statusLabels="flatStatuses" :existingStatuses="entrance.flatStatuses" />
                      <div class="row q-pl-none relative-position no-wrap w-max-content" v-for="floor of Object.keys(entrance.flats).reverse()">
                        <div class="floor-cell q-pl-sm text-weight-bolder bg-grey-3 text-grey">{{ floor }}</div>
                        <div>
                          <div class="row no-wrap">
                            <FlatCell v-for="flatId of Object.keys(entrance.flats[floor])" :flat="entrance.flats[floor][flatId]" :currentlyOpened="entrance.flats[floor][flatId].id == flat.id" />
                          </div>
                        </div>
                      </div>
                    </div>-->
                    
                  </q-expansion-item>
                </template>
              </q-expansion-item>
            </div>
          </template>

        </template>
      </RegularContentContainer>

      <!-- Map -->
      <RegularContentContainer v-if="flat.complex.latitude && flat.complex.longitude" class="q-mt-md q-mx-md">
        <template v-slot:content>
          <ObjectOnMap
            :address="flat.complex.address"
            :latitude="flat.complex.latitude"
            :longitude="flat.complex.longitude"
            :markers="[{ latitude: flat.complex.latitude, longitude: flat.complex.longitude }]"
            :reverseCoords="true"
            :reverseMarkerCoords="true"
          />
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
                      <q-tab name="apartment" icon="space_dashboard" :label="$q.screen.gt.xs ? 'Квартира' : 'кв-ра'" />
                      <q-tab name="floor" icon="dashboard" :label="$q.screen.gt.xs ? 'Этаж' : 'этаж'" />
                      <q-tab name="plan" icon="backup_table" :label="$q.screen.gt.xs ? 'Генплан' : 'план'" />
                    </q-tabs>
                  </div>
                </div>

              </div>
            </div>

          </q-card-section>
        </q-card>
      </q-dialog>

      <!-- Furnish view fullscreen dialog window -->
      <q-dialog
        v-if="flat.complex.furnishes.length"
        class="fitwindow"
        v-model="furnishViewer"
        :maximized="true"
        transition-show="scale"
        transition-hide="scale"
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

      <!-- Discount (action) info dialog -->
      <q-dialog v-if="flat.allDiscounts.length" v-model="discountInfoWindow">
        <q-card>
          <q-img v-if="discountInfoWindowContent.image" :src="`/uploads/${discountInfoWindowContent.image}`" />
          <q-card-section>
            
            <div class="row no-wrap items-center">
              <div class="col text-h6 ellipsis">
                {{ discountInfoWindowContent.title }}
              </div>
            </div>

          </q-card-section>

          <q-card-section class="q-pt-none">
            <div class="text-subtitle1" v-if="discountInfoWindowContent.resume">
              {{ discountInfoWindowContent.resume }}
            </div>
            <!--<div class="text-caption text-grey">
              TODO: Place here reduced discount detaul
            </div>-->
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
            <q-btn v-close-popup flat color="primary" label="Подробнее" icon="pageview" @click="goToNews(discountInfoWindowContent.news_id)" />
          </q-card-actions>
        </q-card>
      </q-dialog>

    </template>

    <!-- Right Drawer -->
    <template v-slot:right-drawer>
      <div class="q-pa-md">
        <p class="q-mb-xs text-h4 text-center">
          <template v-if="flat.rooms == 0">
            <span>Квартира {{ asNumberString(flat.rooms) }}  № {{ flat.number }}</span>
          </template>
          <template v-else>
            <span class="text-capitalize">{{ asNumberString(flat.rooms) }}</span>комнатная квартира № {{ flat.number }}
          </template>
        </p>
        <p class="q-mb-xs text-h5 text-center text-grey">Обновлено {{ asDateTime(flat.updated_at) }}</p>
        <p class="q-mb-xs text-h5 text-center text-grey">{{ flat.complex.address }}</p>
        <div class="q-py-md rounded-borders bg-grey-2 relative-position">
          <q-badge v-if="flat.hasDiscount && flat.status === 0" color="orange" floating>
            Есть скидка
          </q-badge>
          <p class="q-mb-xs text-h3 text-bold text-blue-8 text-center">
            <span v-if="flat.hasDiscount && flat.priceRange && flat.status === 0">{{ flat.priceRange }}</span>
            <span v-else>{{ asCurrency(flat.price_cash) }}</span>
            <PriceChangeIndicator v-if="flat.status === 0 && flat.priceChange.length > 1" :data="flat.priceChange" />
          </p>
        </div>

        <div class="row q-mt-md justify-center">
          <FlatActionButtons
            v-if="flat.status === 0"
            :fav="false"
            :res="flat.is_reserved != 1 && flat.developer.hasRepresentative"
            :flat="{ id: flat.id, isFavorite: flat.isFavorite }"
          />
        </div>

        <p class="q-mb-xs q-mt-md text-h4 text-center">Информация</p>
        <ParamPair
          paramName="Площадь"
          :paramValue="asArea(flat.area)"
        />
        <ParamPair
          v-if="flat.entrance.number || flat.section"
          paramName="Подъезд"
          :paramValue="flat.entrance.number ? flat.entrance.number : flat.section"
        />
        <ParamPair
          v-if="flat.floor && flat.building.total_floor"
          paramName="Этаж"
          :paramValue="asFloor(flat.floor, flat.building.total_floor)"
        />
        <ParamPair
          v-if="flat.building.material"
          paramName="Материал"
          :paramValue="flat.building.material"
        />
        <ParamPair
          v-if="flat.complex.furnishes.length"
          paramName="Отделка"
        >
          <template v-slot:customValue>
            <template v-for="(furnish, index) of flat.complex.furnishes">
              <span class="text-blue-9 cursor-pointer" @click="showFurnishViewer(furnish.id)">{{ furnish.name }}</span>
              <span v-if="index < flat.complex.furnishes.length - 1">, </span>
            </template>
          </template>
        </ParamPair>
        <ParamPair
          v-if="flat.complex.freeFlats"
          paramName="Свободно"
          :paramValue="`${flat.complex.freeFlats} квартир`"
        />
        <ParamPair
          v-if="flat.complex.minYearlyRate"
          paramName="Ставка от"
          :paramValue="flat.complex.minYearlyRate"
        />
        <ParamPair
          v-if="flat.deadLine"
          paramName="Срок сдачи"
          :paramValue="flat.deadLine"
        />

        <div class="q-mt-md">
          <AdvantagesBlock v-if="flat.complex.advantages.length" :advantages="flat.complex.advantages" :inColumn="true" />
        </div>

      </div>
    </template>

  </MainLayout>
</template>
  
<script>
import { ref, computed, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
import { asArea, asCurrency, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea, asDateTime } from '@/helpers/formatter'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"
import FlatActionButtons from '@/Components/Flat/FlatActionButtons/FlatActionButtons.vue'
import ParamPair from '@/Components/Elements/ParamPair.vue'
import PriceChangeIndicator from '@/Components/Elements/Price/PriceChangeIndicator.vue'
import PriceChangeChart from "@/Pages/Flat/partials/PriceChangeChart.vue"
import Compass from '@/Components/Svg/Compass.vue'
import FloorLayoutForAFlat from '@/Components/Svg/FloorLayoutForAFlat.vue'
import ChessLegend from '@/Components/Chess/ChessLegend.vue'
import FlatCell from '@/Components/Chess/FlatCell.vue'
import FinishingCard from '@/Components/FinishingCard.vue'
import ObjectOnMap from '@/Components/Map/ObjectOnMap.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import useEmitter from '@/composables/use-emitter'

export default {
  props: {
    flatStatuses: Object,
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
    MainLayout, Breadcrumbs, Loading, ParamPair, PriceChangeIndicator, PriceChangeChart, FlatActionButtons, Compass, FloorLayoutForAFlat, ChessLegend, FlatCell, FinishingCard, ObjectOnMap, AdvantagesBlock, RegularContentContainer
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
        label: props.flat.complex.name,
        icon: 'apartment',
        url: `/newbuilding-complex/view?id=${props.flat.complex.id}`,
        data: false,
        options: false
      },
      {
        id: 4,
        label: String(props.flat.number),
        icon: 'tag',
        url: `/flat/view?id=${props.flat.id}`,
        data: false,
        options: false
      },
    ])

    const emitter = useEmitter()

    /** Layuot Viewer */
    const layoutViewerTab = ref('apartment')
    const fullscreenLayoutsViewer = ref(false)
    const compass = ref(null)

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

    /** Finishing Viewer */
    const furnishViewer = ref(false)
    const furnishViewerContent = ref({})

    const showFurnishViewer = (furnishId) => {
      furnishViewerContent.value = props.flat.complex.furnishes.find(furnish => { return furnish.id == furnishId })
      furnishViewer.value = true
    }

    /** Discount list & info window */
    const discountListExpanded = ref(false)
    const discountInfoWindow = ref(false)
    const discountInfoWindowContent = ref({})

    const showDiscountInfoWindow = (discountId) => {
      discountInfoWindowContent.value = props.flat.allDiscounts.find(discount => { return discount.id == discountId })
      discountInfoWindow.value = true
    }

    const goToNews = function (newsId) {
      Inertia.get('/news/view', { id: newsId })
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

    
    // Local storage for loaded flats
    const flatsData = ref({})

    // Loading flats by entrance id
    const loadFlats = async (entranceId) => {
      if (!flatsData.value[entranceId]) {
        try {
          const response = await axios.post(`/entrance/get-chess-flats-by-entrance?id=${ entranceId }`, { id: entranceId })
          flatsData.value[entranceId] = response.data
        } catch (error) {
          console.error("Ошибка загрузки квартир:", error)
        }
      }
    }

    onMounted (() => {
      loadFlats(props.flat.entrance_id)
    })

    return {
      asCurrency,
      asArea,
      asFloor,
      asQuarterAndYearDate,
      asPricePerArea,
      asNumberString,
      asDateTime,
      breadcrumbs,
      layoutViewerTab,
      fullscreenLayoutsViewer,
      compass,
      onTabChange,
      furnishViewer,
      furnishViewerContent,
      showFurnishViewer,
      discountListExpanded,
      discountInfoWindow,
      discountInfoWindowContent,
      showDiscountInfoWindow,
      goToNews,
      focusOn,
      focusOff,
      goToComplex,
      goToFlat,
      flatsData,
      loadFlats,
    }
  },
}
</script>

<style scoped>
.row.justify-between::before, .row.justify-between::after {
  display: none;
}
.complex-logo {
  max-height: 50px;
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
