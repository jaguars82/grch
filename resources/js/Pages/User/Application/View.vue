<template>
  <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="`Заявка ${application.application_number}`" :subtitle="`от ${asDateTime(application.created_at)}`">
        <template v-slot:content>

          <!-- Author (application's creator) info section -->
          <h5 class="text-uppercase q-mb-xs">Автор</h5>
            <q-card class="no-shadow" bordered>
              <q-card-section>
                <span>{{ application.author.last_name }} {{ application.author.first_name }}, <span class="text-lowercase">{{ application.author.roleLabel }}</span> {{ application.author.agency_name }}</span>
                <template v-if="application.author.phone">
                  <br />
                  <q-icon name="phone_enabled" class="q-pr-sm" /><span>{{ application.author.phone }}</span>
                </template>
                <template v-if="application.author.email">
                  <br />
                  <q-icon name="mail" class="q-pr-sm" /><span>{{ application.author.email }}</span>
                </template>
            </q-card-section>
          </q-card>

          <!-- Client info section -->
          <template v-if="application.client_firstname || application.client_lastname || application.client_middlename || application.client_phone || application.client_email">
            <h5 class="text-uppercase q-mb-xs q-mt-lg">Информация о клиенте</h5>
            <q-card class="no-shadow" bordered>
              <q-card-section>
                <template v-if="application.client_firstname|| application.client_lastname || application.client_middlename">
                  <span v-if="application.client_firstname">{{ application.client_firstname }}&nbsp;</span>
                  <span v-if="application.client_middlename">{{ application.client_middlename }}&nbsp;</span>
                  <span v-if="application.client_lastname">{{ application.client_lastname }}</span>
                </template>
                <template v-if="application.client_phone">
                  <br />
                  <q-icon name="phone_enabled" class="q-pr-sm" /><span>{{ application.client_phone }}</span>
                </template>
                <template v-if="application.client_email">
                  <br />
                  <q-icon name="mail" class="q-pr-sm" /><span>{{ application.client_email }}</span>
                </template>
              </q-card-section>
            </q-card>
          </template>

          <!-- Comment section -->
          <template v-if="application.applicant_comment">
            <h5 class="text-uppercase q-mb-xs q-mt-lg">Комментарий к заявке</h5>
            <q-card class="no-shadow" bordered>
              <q-card-section>
                {{ application.applicant_comment }}
              </q-card-section>
            </q-card>
          </template>

          <!-- Developer representative info section -->
          <template v-if="application.manager_firstname || application.manager_lastname || application.manager_middlename || application.manager_phone || application.manager_email">
            <h5 class="text-uppercase q-mb-xs q-mt-lg">Представитель застройщика</h5>
            <q-card class="no-shadow" bordered>
              <q-card-section>
                <template v-if="application.manager_firstname|| application.manager_lastname || application.manager_middlename">
                  <span v-if="application.manager_firstname">{{ application.manager_firstname }}&nbsp;</span>
                  <span v-if="application.manager_middlename">{{ application.manager_middlename }}&nbsp;</span>
                  <span v-if="application.manager_lastname">{{ application.manager_lastname }}</span>
                </template>
                <template v-if="application.manager_phone">
                  <br />
                  <q-icon name="phone_enabled" class="q-pr-sm" /><span>{{ application.manager_phone }}</span>
                </template>
                <template v-if="application.manager_email">
                  <br />
                  <q-icon name="mail" class="q-pr-sm" /><span>{{ application.manager_email }}</span>
                </template>
              </q-card-section>
            </q-card>
          </template>

          <!-- Reservation conditions section -->
          <template v-if="application.reservation_conditions">
            <h5 class="text-uppercase q-mb-xs q-mt-lg">Условия бронирования</h5>
            <q-card class="no-shadow" bordered>
              <q-card-section>
                {{ application.reservation_conditions }}
              </q-card-section>
            </q-card>
          </template>
          <h5 class="text-uppercase q-mb-xs q-mt-lg">Текущий статус</h5>
          <q-card class="no-shadow" bordered>
            <q-card-section>
              {{ statusMap[application.status] }}
              <!--<span> (последнее обновление {{ asDateTime(application.updated_at) }})</span>-->
            </q-card-section>
          </q-card>

          <!--<p>
            Статус: <span class="text-lowercase">{{ statusMap[application.status] }}</span>
          </p>-->

            <!-- Next action (step) section -->
            <template v-if="statusChangesForm">
              <template v-if="
                user.role === 'manager' && application.applicant_id == user.id
                || user.role === 'agent' && application.applicant_id == user.id
                || user.role === 'developer_repres' && application.developer_id == user.developer_id
                || user.role === 'admin'
              ">
              <h5 class="text-uppercase q-mb-xs q-mt-lg">Требуемое действие:</h5>
              <q-card class="no-shadow" bordered>
                <q-card-section>
                  {{ statusChangesForm.operationLabel }}
                  <!-- Optional content for different cases and conditions -->
                  <!-- Notification to prepare receipt before take the application in work -->
                  <template v-if="(statusChangesForm.operation === 'take_in_work_by_agent' || statusChangesForm.operation === 'take_in_work_by_manager') && $application.is_toll === 1">
                    <q-banner rounded class="q-mx-sm q-mt-sm bg-orange text-white">
                      <template v-slot:avatar>
                        <q-icon name="report" color="white" />
                      </template>
                      <span class="text-h5"><span class="text-uppercase">Обратите внимание</span>: чтобы принять заявку в работу, нужно загрузить документ, подтверждающий оплату брони. Пожалуйста, подготовьте файл с документом.</span>
                    </q-banner>
                  </template>
                  <!-- Notification to prepare DDU file -->
                  <template v-if="(statusChangesForm.operation === 'upload_ddu_by_agent' || statusChangesForm.operation === 'upload_ddu_by_manager')">
                    <q-banner rounded class="q-mx-sm q-mt-sm bg-orange text-white">
                      <template v-slot:avatar>
                        <q-icon name="report" color="white" />
                      </template>
                      <span class="text-h5"><span class="text-uppercase">Обратите внимание</span>: Вам нужно будет загрузить файл(ы) Договора долевого участия. Пожалуйста, подготовьте его.</span>
                    </q-banner>
                  </template>
                  <!-- Report-Act template download -->
                  <template v-if="statusChangesForm.operation === 'issue_report_act'">
                    <p>Скачайте файл Отчёта-Акта, расположенный ниже по ссылки, заполните его и загрузите</p>
                    <FileDownloadable folder="/downloads/reservation" :file="{ name: 'Образец Отчета-Акта', file: 'OtchetActTemplate.docx' }" />
                  </template>
                </q-card-section>
                <q-card-actions align="right">
                  <q-btn v-if="application.status < 3 && user.role === 'admin'" class="q-mr-xs" unelevated label="Сменить объект" @click="objectChange"></q-btn>
                  <inertia-link :href="`update?id=${application.id}`">
                    <q-btn color="primary" unelevated :label="statusChangesForm.submitLabel" />
                  </inertia-link>
                </q-card-actions>
              </q-card>
              </template>
            </template>

            <!-- Documents section -->
            <template v-if="application.documents.amount > 0">
              <h5 class="text-uppercase q-mb-xs q-mt-lg">Документы</h5>
              <q-card class="no-shadow" bordered>
                <q-card-section>
                  <!-- Receipt file(s) for paid reservation -->
                  <q-expansion-item
                    class="q-mb-sm"
                    v-if="application.is_toll === 1 && application.documents.reciepts.length"
                    v-model="documentExpansions.reciept"
                    header-class="rounded-borders"
                    icon="receipt_long"
                    label="Оплата брони"
                    caption="Квитанция (платежный документ), подтверждающий оплату брони"
                  >
                    <q-card>
                      <q-card-section class="q-pa-none">
                        <div class="row q-mt-sm q-col-gutter-none">
                          <template v-for="doc of application.documents.reciepts">
                            <div class="col-12 col-md-6 col-lg-4">
                              <FileDownloadable :file="doc" />
                            </div>
                          </template>
                        </div>
                      </q-card-section>
                    </q-card>
                  </q-expansion-item>
                  <!-- DDU file's) -->
                  <q-expansion-item
                    class="q-mb-sm"
                    v-if="application.documents.ddus.length"
                    v-model="documentExpansions.ddu"
                    header-class="rounded-borders"
                    icon="assignment"
                    label="Договор долевого участия"
                  >
                    <q-card>
                      <q-card-section class="q-pa-none">
                        <div class="row q-mt-sm q-col-gutter-none">
                          <template v-for="doc of application.documents.ddus">
                            <div class="col-12 col-md-6 col-lg-4">
                              <FileDownloadable :file="doc" />
                            </div>
                          </template>
                        </div>
                      </q-card-section>
                    </q-card>
                  </q-expansion-item>
                </q-card-section>
              </q-card>
            </template>

            <!-- History section -->
            <h5 class="text-uppercase q-mb-xs q-mt-lg">История</h5>
            <q-table
              class="no-shadow"
              bordered
              :rows="rows"
              :columns="columns"
              row-key="id"
              :pagination="{ rowsPerPage: 15 }"
              hide-bottom
            >
            </q-table>
        </template>
      </RegularContentContainer>
      <FlatListItem class="q-ml-md q-mt-md" :flat="flat" />
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import FlatListItem from '@/Components/Flat/FlatListItem.vue'
import FileDownloadable from '@/Components/File/FileDownloadListItem.vue'
import { getApplicationFormParamsByStatus } from '@/composables/components-configurations'
import { asDateTime } from '@/helpers/formatter'
import { userInfo } from '@/composables/shared-data'

export default ({
components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    FlatListItem,
    FileDownloadable
  },
  props: {
    application: Array,
    applicationHistory: Array,
    statusMap: Array,
    flat: Object
  },
  setup(props) {
    const { user } = userInfo()

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
        label: `Заявка ${props.application.application_number}`,
        icon: 'article',
        url: `/user/application/view?id=${props.application.id}`,
        data: false,
        options: false
      },
    ])

    const columns = [
      { name: 'action', required: true, align: 'left', label: 'Статус', field: 'action', sortable: true },
      { name: 'made_at', required: true, align: 'center', label: 'Дата', field: 'made_at', sortable: true },
    ]

    const rows = computed(() => {
      const processedRows = []
      props.applicationHistory.forEach(row => {
        const processedItem = {
          id: row.id,
          action: props.statusMap[row.action],
          made_at: asDateTime(row.made_at),
        }
        processedRows.push(processedItem)
      });
      return processedRows
    })

    const documentExpansions = ref({
      reciept: true,
      ddu: true,
    })

    const statusChangesForm = getApplicationFormParamsByStatus(props.application.status, user.value.role)

    const objectChange = () => {
      Inertia.visit(`/user/application/update?id=${props.application.id}`, {
        method: 'post',
        data: {
          id: props.application.id,
          eOperation: 'change_object',
          developerId: props.flat.developer.id,
          complexId: props.flat.newbuildingComplex.id,
          buildingId: props.flat.newbuilding.id,
          entranceId: props.flat.entrance.id
        },
      })
    }

    return { user, breadcrumbs, asDateTime, documentExpansions, columns, rows, statusChangesForm, objectChange }
  },
})
</script>
