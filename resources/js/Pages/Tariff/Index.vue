<template>
  <MainLayout>
    <template v-slot:breadcrumbs>
      <Breadcrumbs :links="breadcrumbs"></Breadcrumbs>
    </template>
    <template v-slot:main>
      <q-card class="q-ma-md shadow-7">
        <q-card-section>
          <h3 class="text-center">Таблица тарифов</h3>
        </q-card-section>
        <q-card-section>
          <div v-for="developer of developers" :key="developer.id">
            <q-expansion-item
              v-if="developer.complexes.length"
              class="q-mb-md"
              default-opened
              header-class="text-h4 q-mt-md"
              :label="developer.name"
            >
              <table class="tarifftable full-width">
                <tr>
                  <th class="text-center zk-col">Жилой комплекс</th>
                  <th class="text-center amount-col">Размер вознаграждения</th>
                  <th class="text-center info-col">Условия</th>
                  <th class="text-center terms-col">Сроки выплаты вознаграждения</th>
                </tr>
                <tr v-for="complex of developer.complexes" :key="complex.id">
                  <td class="zk-col">{{ complex.name }}</td>
                  <td class="amount-col text-center">
                    <div v-for="(tariff, i) of complex.tariffs" :key="i">
                      <span v-if="tariff.tariffType === 'percent'">{{ tariff.amountPercent }}%</span>
                      <span v-else-if="tariff.tariffType === 'currency'">{{ tariff.amountCurrency }} ₽</span>
                      <span v-else-if="tariff.tariffType === 'custom'">{{ tariff.amountCustom }}</span>
                    </div>
                  </td>
                  <td class="info-col">
                    <div class="ellipsis" v-for="(tariff, i) of complex.tariffs" :key="i">
                      <span v-if="tariff.annotation">{{ tariff.annotation }}</span>
                      <span v-else>&nbsp;</span>
                    </div>
                  </td>
                  <td class="terms-col">{{ complex.termsOfPayment }}</td>
                </tr>
              </table>
            </q-expansion-item>
          </div>
        </q-card-section>
      </q-card>
    </template>
  </MainLayout>
</template>

<script>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import MainLayout from '@/Layouts/MainLayout.vue'
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue'
import Loading from "@/Components/Elements/Loading.vue"

export default {
  props: {
    model: {
      type: Array,
      derfault: []
    },
    developers: {
      type: Array,
      default: []
    }
  },
  components: {
    MainLayout, Breadcrumbs, Loading
  },
  setup() {
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
        label: 'Таблица тарифов',
        icon: 'toc',
        url: '/tariff',
        data: false,
        options: false
      },
    ])

    return { breadcrumbs }
  },
}
</script>

<style scoped>
.tarifftable {
  border-collapse: collapse;
}

.tarifftable .zk-col {
  width: 20%;
  max-width: 20%;
}

.tarifftable .amount-col {
  width: 10%;
  max-width: 10%;
}

.tarifftable .info-col {
  width: 40%;
  max-width: 300px;
}

.tarifftable .terms-col {
  width: 30%;
  max-width: 30%;
}

table td, table th {
  border: 1px solid #888;
  padding: 5px 10px;
}

.ellipsis {
  width: 100%;
  max-width: 100%;
  text-overflow: ellipsis;
}
</style>