<template>
  <div class="compare-table q-mt-md q-mb-lg q-mx-md" v-for="(tablePage, i) of flatsForCompare" :key="i">
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

<script>
import { computed } from 'vue'
import { asQuarterAndYearDate, asArea } from '@/helpers/formatter'

export default {
  props: {
    flats: Array
  },
  setup(props) {
    const flatsForCompare = computed(() => {
      const chunkedArray = []
      const chunkSize = 5
      for (let i = 0; i < props.flats.length; i += chunkSize) {
        const chunk = props.flats.slice(i, i + chunkSize);
        chunkedArray.push(chunk)
      }
      return chunkedArray
    })

    return { flatsForCompare, asQuarterAndYearDate, asArea }
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
</style>