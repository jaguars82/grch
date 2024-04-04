<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="eOperation === 'change_object'? 'Изменить объект для бронирования' : statusChangesForm.formCaption">
        <template v-slot:content>
          <Loading v-if="loading" />
          <div v-else>
            <div class="q-my-lg" v-if="eOperation === 'change_object'" >
              <p>Укажите новый объект</p>
            </div>
            <div class="q-my-lg" v-else v-html="statusChangesForm.formContent"></div>
            <q-form
              @submit="onSubmit"
            >
              <input type="hidden" v-model="formfields.operation" />

              <div v-if="eOperation === 'change_object' && application.status < 3" class="row q-col-gutter-none">

                <div class="col-12 col-sm-6 q-py-xs" :class="{'q-pr-none': $q.screen.xs }">
                  <q-select
                    outlined
                    v-model="optfields.developer_select"
                    :options="developerOptions"
                    label="Застройщик"
                    options-dense
                    @update:model-value="onDeveloperSelect"
                  >
                    <template v-slot:append>
                      <q-icon
                        v-if="optfields.developer_select !== null"
                        class="cursor-pointer"
                        name="clear"
                        @click.stop.prevent="() => { 
                          optfields.developer_select = null
                          optfields.buildingComplex_select = null
                          optfields.building_select = null
                          optfields.entrance_select = null
                          optfields.flat_select = null
                        }"
                      />
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-sm-6 q-py-xs q-pr-none">
                  <q-select
                    outlined
                    v-model="optfields.buildingComplex_select"
                    :options="buildingComplexOptions"
                    label="Жилой комплекс"
                    options-dense
                    :disable="!buildingComplexOptions.length"
                    @update:model-value="onBuildingComplexSelect"
                  >
                    <template v-slot:append>
                      <q-icon
                        v-if="optfields.buildingComplex_select !== null"
                        class="cursor-pointer"
                        name="clear"
                        @click.stop.prevent="() => { 
                          optfields.buildingComplex_select = null
                          optfields.building_select = null
                          optfields.entrance_select = null
                          optfields.flat_select = null
                        }"
                      />
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-sm-4 q-py-xs" :class="{'q-pr-none': $q.screen.xs }">
                  <q-select
                    outlined
                    v-model="optfields.building_select"
                    :options="buildingOptions"
                    label="Позиция"
                    options-dense
                    :disable="!buildingOptions.length"
                    @update:model-value="onBuildingSelect"
                  >
                    <template v-slot:append>
                      <q-icon
                        v-if="optfields.building_select !== null"
                        class="cursor-pointer"
                        name="clear"
                        @click.stop.prevent="() => { 
                          optfields.building_select = null
                          optfields.entrance_select = null
                          optfields.flat_select = null
                        }"
                      />
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-sm-4 q-py-xs" :class="{'q-pr-none': $q.screen.xs }">
                  <q-select
                    outlined
                    v-model="optfields.entrance_select"
                    :options="entranceOptions"
                    label="Подъезд"
                    options-dense
                    :disable="!entranceOptions.length"
                    @update:model-value="onEntranceSelect"
                  >
                    <template v-slot:append>
                      <q-icon
                        v-if="optfields.entrance_select !== null"
                        class="cursor-pointer"
                        name="clear"
                        @click.stop.prevent="() => { 
                          optfields.entrance_select = null
                          optfields.flat_select = null
                        }"
                      />
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-sm-4 q-py-xs q-pr-none">
                  <q-select
                    outlined
                    v-model="optfields.flat_select"
                    :options="flatOptions"
                    label="Квартира"
                    options-dense
                    :disable="!flatOptions.length"
                  >
                    <template v-slot:append>
                      <q-icon
                        v-if="optfields.flat_select !== null"
                        class="cursor-pointer"
                        name="clear"
                        @click.stop.prevent="optfields.flat_select = null"
                      />
                    </template>
                  </q-select>
                </div>

              </div>

              <template v-else>

                <!-- Approving reservation by developer or by manager -->
                <template
                  v-if="formfields.operation === 'approve_reservation_by_developer' || formfields.operation === 'approve_reservation_from_developer_by_admin' && application.status == 2"
                >
                  <div class="row q-col-gutter-none" :class="{'q-pb-sm': $q.screen.xs }">
                    <div class="col-12 col-sm-4" :class="{'q-py-xs': $q.screen.gt.xs, 'q-pr-none': $q.screen.xs }">
                      <q-input outlined v-model="formfields.manager_lastname" label="Фамилия менеджера" />
                    </div>
                    <div class="col-12 col-sm-4" :class="{'q-py-xs': $q.screen.gt.xs, 'q-pr-none': $q.screen.xs }">
                      <q-input outlined v-model="formfields.manager_firstname" label="Имя менеджера" />
                    </div>
                    <div class="col-12 col-sm-4 q-pr-none" :class="{'q-py-xs': $q.screen.gt.xs }">
                      <q-input outlined v-model="formfields.manager_middlename" label="Отчество менеджера" />
                    </div>
                  </div>

                  <div class="row q-col-gutter-none" :class="{'q-py-sm': $q.screen.xs }">
                    <div class="col-12 col-sm-6" :class="{'q-py-xs': $q.screen.gt.xs, 'q-pr-none': $q.screen.xs }">
                      <q-input outlined v-model="formfields.manager_phone" label="Телефон менеджера" mask="# (###) ###-##-##" unmasked-value />
                    </div>
                    <div class="col-12 col-sm-6 q-pr-none" :class="{'q-py-xs': $q.screen.gt.xs }">
                      <q-input outlined v-model="formfields.manager_email" label="Email менеджера" />
                    </div>
                  </div>

                  <div class="row q-col-gutter-none" :class="{'q-pt-sm': $q.screen.xs }">
                    <div class="col" :class="{'q-py-xs': $q.screen.gt.xs }">
                      <q-input outlined autogrow v-model="formfields.reservation_conditions" label="Условия бронирования" />
                    </div>
                  </div>
                  
                  <div class="row q-col-gutter-none" :class="{'q-pt-sm': $q.screen.xs }">
                    <q-toggle
                      v-model="formfields.is_toll"
                      label="Платная бронь"
                    />
                  </div>
                  
                  <div v-if="formfields.is_toll" class="row q-col-gutter-none" :class="{'q-pt-sm': $q.screen.xs }">
                    <q-banner inline-actions rounded class="q-mx-md q-mb-sm bg-orange text-white">
                      <template v-slot:avatar>
                        <q-icon name="report" color="white" />
                      </template>
                      <span class="text-h5"><span class="text-uppercase">Обратите внимание</span>: чтобы принять заявку в работу, агент должен будет предоставить квитанцию об оплате бронирования</span>
                    </q-banner>
                  </div>
                </template>

                <!-- Taking a paid application in work by an agent or a manager -->
                <template
                  v-if="application.is_toll === 1 && (formfields.operation === 'take_in_work_by_agent' || formfields.operation === 'take_in_work_by_manager')"
                >
                  <div class="row q-col-gutter-none" :class="{'q-pb-sm': $q.screen.xs }">
                    <div class="col-12">
                      <q-file
                        outlined
                        v-model="recieptFile"
                        label="Перетащите или загрузите документ (квитанцию) об оплате бронирования"
                        multiple 
                        use-chips
                        accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, .jpg, image/*"
                      >
                        <template v-slot:prepend>
                          <q-icon name="attach_file" />
                        </template>
                        <template v-slot:file="{ index, file }">
                          <q-chip
                            removable
                            @remove="onRemoveRecieptFile(index)"
                          >
                            <q-avatar>
                              <q-icon name="draft" />
                            </q-avatar>
                            <div class="ellipsis relative-position">
                              {{ file.name }}
                            </div>
                          </q-chip>
                        </template>
                      </q-file>
                    </div>
                  </div>
                </template>

                <!-- Reporting successful deal -->
                <template
                  v-if="formfields.operation === 'report_success_deal_by_agent' || formfields.operation === 'report_success_deal_by_manager'"
                >
                  <div class="row q-col-gutter-none" :class="{'q-pb-sm': $q.screen.xs }">
                    <q-file
                      v-model="formfields.deal_success_docs"
                      label="Прикрепите документы о подтверждении сделки"
                      outlined
                      multiple
                    />
                  </div>
                </template>

              </template>

              <div class="text-right">
                <q-btn unelevated :label="statusChangesForm.submitLabel" :disable="!canSubmit" type="submit" color="primary"/>
              </div>

            </q-form>
          </div>
        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import Loading from "@/Components/Elements/Loading.vue"
import { getApplicationFormParamsByStatus } from '@/composables/components-configurations'
import { userInfo } from '@/composables/shared-data'
 
export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    Loading
  },
  props: {
    application: Object,
    statusMap: Array,
    eOperation: {
      type: String,
      default: ''
    },
    developers: {
      type: Object,
      default: {}
    },
    buildingComplexes: {
      type: Array,
      default: []
    },
    buildings: {
      type: Array,
      default: []
    },
    entrances: {
      type: Array,
      default: []
    },
    flats: {
      type: Array,
      default: []
    },
    selectedParams: {
      type: Object,
      default: {
        developerId: '',
        complexId: '',
        buildingId: '',
        entranceId: '',
      }
    }
  },
  setup (props) {
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
        label: 'Кабинет пользователя',
        icon: 'business_center',
        url: '/user/profile',
        data: false,
        options: false
      },
      {
        id: 3,
        label: 'Заявки',
        icon: 'real_estate_agent',
        url: '/user/application/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `Изменение заявки ${props.application.application_number}`,
        icon: 'edit_square',
        url: `/user/application/update?id=${props.application.id}`,
        data: false,
        options: false
      },
    ])

    const { user } = userInfo()

    const loading = ref(false)

    const statusChangesForm = getApplicationFormParamsByStatus(props.application.status, user.value.role)

    // Reciept file(s) for paid reservation
    const recieptFile = ref([])

    const onRemoveRecieptFile = (ind) => {
      const halfBeforeTheUnwantedElement = recieptFile.value.slice(0, ind)
      const halfAfterTheUnwantedElement = recieptFile.value.slice(ind+1);
      recieptFile.value = halfBeforeTheUnwantedElement.concat(halfAfterTheUnwantedElement);
    }

    const formfields = ref(
      {
        operation: props.eOperation ? props.eOperation : statusChangesForm ? statusChangesForm.operation : '',
        manager_firstname: '',
        manager_lastname: '',
        manager_middlename: '',
        manager_phone: '',
        manager_email: '',
        reservation_conditions: '',
        is_toll: false,
        recieptFile: [],
        new_object_id: null,
        deal_success_docs: null
      }
    )

    const initialSelections = computed(() => {
      const developer_id = props.selectedParams.developerId ? props.selectedParams.developerId : props.application.flat.developer.id
      const complex_id = props.selectedParams.complexId ? props.selectedParams.complexId : props.application.flat.newbuildingComplex.id
      const complex = props.buildingComplexes.find(compl => compl.id == complex_id)
      const building_id = props.selectedParams.buildingId ? props.selectedParams.buildingId : props.application.flat.newbuilding.id
      const building = props.buildings.find(build => build.id == building_id)
      const entrance_id = props.selectedParams.entranceId ? props.selectedParams.entranceId : props.application.flat.entrance.id
      const entrance = props.entrances.find(entr => entr.id == entrance_id)

      return {
        developer: { label: props.developers[developer_id], value: developer_id },
        complex: complex ? { label: complex.name, value: complex.id } : '',
        building: building ? { label: building.name, value: building.id } : '',
        entrance: entrance ? { label: entrance.name, value: entrance.id } : '',
      }
    })

    const optfields = ref({
      developer_select: initialSelections.value.developer,
      buildingComplex_select: initialSelections.value.complex,
      building_select: initialSelections.value.building,
      entrance_select: initialSelections.value.entrance,
      flat_select: null
    })

    const developerOptions = computed(() => {
      const options = []
      Object.keys(props.developers).forEach(index => {
        options.push({ label: props.developers[index], value: index })
      })
      return options
    })

    const onDeveloperSelect = () => {
      optfields.value.buildingComplex_select = null
      optfields.value.building_select = null
      optfields.value.entrance_select = null
      optfields.value.flat_select = null
      Inertia.visit(`/user/application/update?id=${props.application.id}`, {
        method: 'post',
        data: {
          id: props.application.id,
          eOperation: 'change_object',
        },
        preserveState: true,
        preserveScroll: true,
        only: ['buildingComplexes']
      })
    }

    const buildingComplexOptions = computed(() => {
      const options = []
        if (optfields.value.developer_select !== null) {
        props.buildingComplexes.forEach(complex => {
          options.push({ label: complex.name, value: complex.id })
        })
      }
      return options
    })

    const onBuildingComplexSelect = () => {
      optfields.value.building_select = null
      optfields.value.entrance_select = null
      optfields.value.flat_select = null
      Inertia.visit(`/user/application/update?id=${props.application.id}`, {
        method: 'post',
        data: {
          id: props.application.id,
          eOperation: 'change_object',
          developerId: optfields.value.developer_select.value,
          complexId: optfields.value.buildingComplex_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['buildings']
      })
    }

    const buildingOptions = computed(() => {
      const options = []
        if (optfields.value.buildingComplex_select !== null) {
        props.buildings.forEach(building => {
          options.push({ label: building.name, value: building.id })
        })
      }
      return options
    })

    const onBuildingSelect = () => {
      optfields.value.entrance_select = null
      optfields.value.flat_select = null
      Inertia.visit(`/user/application/update?id=${props.application.id}`, {
        method: 'post',
        data: {
          id: props.application.id,
          eOperation: 'change_object',
          developerId: optfields.value.developer_select.value,
          complexId: optfields.value.buildingComplex_select.value,
          buildingId: optfields.value.building_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['entrances']
      })
    }

    const entranceOptions = computed(() => {
      const options = []
        if (optfields.value.building_select !== null) {
        props.entrances.forEach(entrance => {
          options.push({ label: entrance.name, value: entrance.id })
        })
      }
      return options
    })

    const onEntranceSelect = () => {
      optfields.value.flat_select = null
      Inertia.visit(`/user/application/update?id=${props.application.id}`, {
        method: 'post',
        data: {
          id: props.application.id,
          eOperation: 'change_object',
          developerId: optfields.value.developer_select.value,
          complexId: optfields.value.buildingComplex_select.value,
          buildingId: optfields.value.building_select.value,
          entranceId: optfields.value.entrance_select.value
        },
        preserveState: true,
        preserveScroll: true,
        only: ['flats']
      })
    }

    const flatOptions = computed(() => {
      const options = []
        if (optfields.value.entrance_select !== null) {
        props.flats.forEach(flat => {
          options.push({ label: `№ ${flat.number} (${flat.floor} этаж)`, value: flat.id })
        })
      }
      return options
    })

    const canSubmit = computed(() => {
      // if no reciept files provided
      if (props.application.is_toll === 1 && statusChangesForm && (statusChangesForm.operation === 'take_in_work_by_agent' || statusChangesForm.operation === 'take_in_work_by_manager' ) && recieptFile.value.length < 1) {
        return false
      }
      return true
    })
    
    function onSubmit() {
      loading.value = true

      if (props.eOperation === 'change_object') {
        formfields.value.new_object_id = optfields.value.flat_select.value
      }

      // Add reciept files to formfields
      if (props.application.is_toll === 1 && statusChangesForm && (statusChangesForm.operation === 'take_in_work_by_agent' || statusChangesForm.operation === 'take_in_work_by_manager' )) {
        formfields.value.recieptFile = recieptFile.value
        console.log(formfields.value)
      }

      Inertia.post(`/user/application/view?id=${props.application.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        loading.value = false
      })
    }

    return {
      breadcrumbs,
      loading,
      recieptFile,
      onRemoveRecieptFile,
      formfields,
      optfields,
      statusChangesForm,
      developerOptions,
      onDeveloperSelect,
      buildingComplexOptions,
      onBuildingComplexSelect,
      buildingOptions,
      onBuildingSelect,
      entranceOptions,
      onEntranceSelect,
      flatOptions,
      canSubmit,
      onSubmit
    }
  }
}

</script>