<template>
    <ProfileLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <RegularContentContainer title="Коммерческое предложение">
        <template v-slot:content>

          <q-bar class="q-px-none bg-white">
            
            <q-btn unelevated label="Поделиться" icon="send">
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
                </q-list>
              </q-menu>
            </q-btn>

            <q-btn unelevated label="Настройки" icon="checklist">
              <q-menu>
                <q-list>

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
                      <div v-if="PDFloading">
            !!!!!!!!!!!!
            Генерируем PDF, пожалуйста, подождите...
          </div>
          <a
            ref="pdfLink"
            download
            href="/uploads/Коммерческое предложение - 10.pdf"
          >
          Загрузить...
          </a>

          <UserInfoBar :user="commercial.initiator" />
            </q-card-section>
          </q-card>
          <template v-for="flat in flats">
          <FlatCommercialItem class="q-mt-md q-ml-md" :flat="flat"></FlatCommercialItem>
          </template>

        </template>
      </RegularContentContainer>
    </template>
  </ProfileLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
//import { jsPDF } from 'jspdf'
//import html2pdf from 'html2pdf.js'
import ProfileLayout from '@/Layouts/ProfileLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import RegularContentContainer from '@/Components/Layout/RegularContentContainer.vue'
import UserInfoBar from '@/Components/Elements/UserInfoBar.vue'
import FlatCommercialItem from '../../../Components/Flat/FlatCommercialItem.vue'

export default {
  components: {
    ProfileLayout,
    Breadcrumbs,
    RegularContentContainer,
    UserInfoBar,
    FlatCommercialItem
  },
  props: {
    commercial: Array,
    commercialMode: String,
    flats: Array,
    operation: String,
    status: String
  },
  setup(props) {

    const PDFloading = ref(false)

    const formfields = ref({})
    const clearFormFields = function () {
      formfields.value = {}
    }

    /*const commercialSettings = ref({
      initiator: props.commercial.settings && props.commercial.settings.initiator ? props.commercial.settings.initiator : true,
      developer: props.commercial.settings && props.commercial.settings.developer ? props.commercial.settings.developer : false,
      newbuildingComplex: props.commercial.settings && props.commercial.settings.newbuildingComplex ? props.commercial.settings.newbuildingComplex : false,
      finishing: props.commercial.settings && props.commercial.settings.finishing ? props.commercial.settings.finishing : false,
    })*/

    const commercialSettings = props.commercial.settings ? ref(JSON.parse(props.commercial.settings)) : ref({ initiator: true, developer: false, newbuildingComplex: false, finishing: false })

    const pdfContent = ref(null)
    const pdfLink = ref(null)

    const savePDF = function () {
      formfields.value.operation = 'pdf'
      PDFloading.value = true
      //Inertia.post(`/user/commercial/download-pdf?flatId=82452`, formfields.value)
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
      Inertia.on('finish', (event) => {
        PDFloading.value = false
        clearFormFields()
        pdfLink.value.click()
      })
    }

    const saveSettings = function () {
      formfields.value.operation = 'settings'
      formfields.value.settings = commercialSettings.value
      Inertia.post(`/user/commercial/view?id=${props.commercial.id}`, formfields.value)
    }

    //console.log (pdfContent)

    return { 
      commercialSettings,
      PDFloading,
      pdfLink,
      pdfContent,
      savePDF,
      saveSettings
    }
  },
}
</script>