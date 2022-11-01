<template>
    <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer :title="`Коммерческое предложение №${commercial.number}`">
        <template v-slot:content>

          <q-bar class="q-px-none bg-white">
            
            <q-btn unelevated label="Поделиться" icon="share">
              <q-menu auto-close>
                <q-list>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="link"/>
                    </q-item-section>
                    <q-item-section>
                      Ссылка
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="mail"/>
                    </q-item-section>
                    <q-item-section>
                      Электронная почта
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="picture_as_pdf"/>
                    </q-item-section>
                    <q-item-section @click="savePDF">
                      Скачать PDF
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="send"/>
                    </q-item-section>
                    <q-item-section>
                      <a target="_blank" class="undecorated" href="https://telegram.me/share/url?url=https://grch.ru&text=Коммерческое предложение">Telegram</a>
                    </q-item-section>
                  </q-item>
                  <q-item clickable v-ripple>
                    <q-item-section avatar>
                      <q-icon name="phone"/>
                    </q-item-section>
                    <q-item-section>
                      <a target="_blank" class="undecorated" href="https://api.whatsapp.com/send/?url=grch.ru&text=Коммерческое предложение: https://grch.ru">WhatsApp</a>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>

            <q-btn unelevated label="Настройки" icon="checklist">
              <q-menu>
                <q-list>

                  <q-item tag="label" v-if="flats.length > 1" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.compareTable" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Таблица сравнения</q-item-label>
                      <q-item-label caption>
                        Отображать таблицу сравнения объектов
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.initiator" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Сотрудник</q-item-label>
                      <q-item-label caption>
                        Отображать информацию о сотруднике
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.developer" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Застройщик</q-item-label>
                      <q-item-label caption>
                        Отображать информацию о застройщике
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.newbuildingComplex" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Жилой комплекс</q-item-label>
                      <q-item-label caption>
                        Отображать информацию о жилом комплексе
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <q-item tag="label" v-ripple>
                    <q-item-section side top>
                      <q-checkbox v-model="commercialSettings.finishing" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>Отделка</q-item-label>
                      <q-item-label caption>
                        Отображать информацию об отделке
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                </q-list>
                <q-btn
                  class="q-mt-sm"
                  label="Сохранить настройки"
                  icon="save"
                  @click="saveSettings"
                  :disable="JSON.stringify(commercialSettings) == commercial.settings"
                />
              </q-menu>
            </q-btn>
          </q-bar>
          <q-card flat bordered class="q-mt-sm">
            <q-card-section>
              <Loading v-if="PDFloading" size="md" text="Идёт подготовка PDF-файла" />
          
            <q-card class="hidden q-mb-md">
              <q-card-section>
                <div class="text-right"><q-btn flat icon="close" @click="closePDFLink" /></div>
                Если загрузка файла не началась автоматически, 
              <a
                class="d-hidden"
                ref="pdfLink"
                download
                :href="`/downloads/Коммерческое предложение - ${commercial.number}.pdf`"
              >
              воспользуйтесь ссылкой
              </a>
              </q-card-section>
            </q-card>

              <UserInfoBar v-if="commercialSettings.initiator" :user="commercial.initiator" />
            </q-card-section>
          </q-card>

          <template v-if="commercialSettings.compareTable && flats.length > 1">
            <div class="compare-table" v-for="(tablePage, i) of flatsForCompare" :key="i">
              <div class="compare-table-titlecolumn">
                <div class="compare-table-rowname layuot">Планировка</div>
                <div class="compare-table-rowname price">Цена</div>
                <div class="compare-table-rowname area">Площадь</div>
                <div class="compare-table-rowname type">Тип</div>
                <div class="compare-table-rowname floor">Этаж</div>
                <div class="compare-table-rowname deadline">Срок сдачи</div>
                <div class="compare-table-rowname developer">Застройщик</div>
                <div class="compare-table-rowname nbc">ЖК</div>
              </div>
              <div class="compare-table-column" v-for="flat in tablePage" :key="flat.id">
                <div class="compare-table-cell layout">
                  <img class="compare-table-layout" v-if="flat.layout" :src="`/uploads/${flat.layout}`" />
                </div>
                <div class="compare-table-cell price"><strong>{{ flat.price_cash }} ₽</strong></div>
                <div class="compare-table-cell area">{{ asArea(flat.area) }}</div>
                <div class="compare-table-cell type">
                  <span>{{ flat.rooms }}</span>
                  <span v-if="flat.rooms > 0 && flat.rooms < 2">-но</span>
                  <span v-else-if="flat.rooms >= 2 && flat.rooms < 5">-х</span>
                  <span v-else>-и</span>
                  <span> комнатная</span>
                  <span v-if="flat.is_studio"> студия</span>
                  <span v-else> квартира</span>
                </div>
                <div class="compare-table-cell floor">{{ flat.floor }}</div>
                <div class="compare-table-cell deadline">
                  <span v-if="flat.newbuilding.deadline">
                    <span v-if="new Date() > new Date(flat.newbuilding.deadline)">позиция сдана</span>
                    <span v-else>{{ asQuarterAndYearDate(flat.newbuilding.deadline) }}</span>
                  </span>
                  <span v-else>нет данных</span>
                </div>
                <div class="compare-table-cell developer">
                  {{ flat.developer.name }}
                </div>
                <div class="compare-table-cell nbc">{{ flat.newbuildingComplex.name }}</div>
              </div>
            </div>
          </template>

          <template v-for="flat in flats" :key="flat.id">
            <FlatCommercialItem class="q-mt-md q-ml-md" :flat="flat" />
            <AdvantagesBlock :advantages="flat.advantages" />
          </template>

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
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import FlatCommercialItem from '@/Components/Flat/FlatCommercialItem.vue'
import AdvantagesBlock from '@/Components/Elements/AdvantagesBlock.vue'
import Loading from '@/Components/Elements/Loading.vue'
import { asQuarterAndYearDate, asArea } from '@/helpers/formatter'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    UserInfoBar,
    FlatCommercialItem,
    AdvantagesBlock,
    Loading
  },
  props: {
    commercial: Array,
    commercialMode: String,
    flats: Array,
    operation: String,
    status: String
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
        label: 'Кабинет пользователя',
        icon: 'business_center',
        url: '/user/profile',
        data: false,
        options: false
      },
      {
        id: 3,
        label: 'Коммерческие предложения',
        icon: 'share',
        url: '/user/commercial/index',
        data: false,
        options: false
      },
      {
        id: 4,
        label: `№ ${props.commercial.number}`,
        icon: 'description',
        url: '/user/commercial/index',
        data: {id: props.commercial.id},
        options: false
      },
    ])

    const PDFloading = ref(false)
    // const PDFReady = ref(false)

    const formfields = ref({})
    const clearFormFields = function () {
      formfields.value = {}
    }

    const flatsForCompare = computed(() => {
      const chunkedArray = []
      const chunkSize = 5
      for (let i = 0; i < props.flats.length; i += chunkSize) {
        const chunk = props.flats.slice(i, i + chunkSize);
        chunkedArray.push(chunk)
      }
      return chunkedArray
    })

    const commercialSettings = props.commercial.settings ? ref(JSON.parse(props.commercial.settings)) : ref({ compareTable:  true, initiator: true, developer: false, newbuildingComplex: false, finishing: false })

    const pdfContent = ref(null)
    const pdfLink = ref(null)

    const savePDF = function () {
      formfields.value.operation = 'pdf'
      PDFloading.value = true
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        PDFloading.value = false
        clearFormFields()
        //PDFReady.value = true
        pdfLink.value.click()
      })
    }

    /*const closePDFLink = function () {
      PDFReady.value = false
    }*/

    const saveSettings = function () {
      formfields.value.operation = 'settings'
      formfields.value.settings = commercialSettings.value
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
    }

    return { 
      breadcrumbs,
      asQuarterAndYearDate,
      asArea,
      flatsForCompare,
      commercialSettings,
      PDFloading,
      pdfLink,
      pdfContent,
      savePDF,
      //PDFReady,
      //closePDFLink,
      saveSettings
    }
  },
}
</script>

<style scoped>
.compare-table {
  display: flex;
}

.compare-table-titlecolumn, .compare-table-column {
  display: flex;
  flex-direction: column;
}

.compare-table-rowname, .compare-table-cell {
  display: flex;
  align-items: center;
  border: solid thin #555;
  padding: 5px 15px;
}

.compare-table-cell {
  justify-content: center;
  flex-wrap: wrap;
}

.compare-table-rowname.layuot, .compare-table-cell.layout {
  height: 160px;
  max-height: 160px;
}

.compare-table-rowname.price,
.compare-table-cell.price,
.compare-table-rowname.area,
.compare-table-cell.area {
  height: 35px;
  max-height: 35px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.compare-table-rowname.type,
.compare-table-cell.type,
.compare-table-rowname.deadline,
.compare-table-cell.deadline,
.compare-table-rowname.nbc,
.compare-table-cell.nbc {
  height: 54px;
  max-height: 54px;
}

.compare-table-rowname {
  font-weight: 600;
}

.compare-table-layout {
  height: 150px;
  max-height: 150px;
  max-width: 100%;
}

a.undecorated {
  color: inherit;
  text-decoration: none;
}
</style>