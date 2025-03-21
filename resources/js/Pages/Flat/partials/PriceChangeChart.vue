<template>
  <div>
    <LineChart
      class="chart-container"
      v-if="formattedChartData.datasets.length > 0"
      :chart-data="formattedChartData"
      :chart-options="chartOptions"
    />
  </div>
</template>

<script>
import { defineComponent, ref, computed } from "vue";
import { LineChart } from "vue-chart-3";
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale, LineController } from "chart.js";

// Регистрируем компоненты Chart.js
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale, LineController);

export default defineComponent({
  components: { LineChart },
  props: {
    data: Array,
  },
  setup(props) {
    // Format date to "dd.mm.yyyy"
    const formatDateForAxis = (dateStr) => {
      const [datePart] = dateStr.split(" ");
      const [year, month, day] = datePart.split("-");
      return `${day}.${month}.${year}`;
    };

    // Format date to "d month yyyy"
    const formatDateForTooltip = (dateStr) => {
      const months = [
        "января", "февраля", "марта", "апреля", "мая", "июня",
        "июля", "августа", "сентября", "октября", "ноября", "декабря"
      ];
      const [datePart] = dateStr.split(" ");
      const [year, month, day] = datePart.split("-");
      return `${Number(day)} ${months[Number(month) - 1]} ${year}`;
    };

    const formattedChartData = computed(() => {
      if (!props.data || props.data.length === 0) {
        return { labels: [], datasets: [] };
      }

      return {
        labels: props.data.map((item) => formatDateForAxis(item.price_updated_at)), // Даты для оси X
        datasets: [
          {
            label: "Цена (₽)",
            data: props.data.map((item) => Number(item.price_cash)),
            borderColor: "#42A5F5",
            backgroundColor: "rgba(66, 165, 245, 0.2)",
            fill: true,
            tension: 0.3,
            pointRadius: 5,
          },
        ],
      };
    });

    // Graph options
    const chartOptions = ref({
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          type: "category",
          ticks: {
            autoSkip: true,
            callback: (value, index) => formattedChartData.value.labels[index], // Форматируем метки оси X
          },
        },
        y: {
          beginAtZero: false,
          title: {
            display: true,
            text: "Цена (₽)",
          },
        },
      },
      plugins: {
        tooltip: {
          callbacks: {
            title: (tooltipItems) => {
              const originalDate = props.data[tooltipItems[0].dataIndex]?.price_updated_at; // Берем исходную дату
              return originalDate ? formatDateForTooltip(originalDate) : "";
            },
          },
        },
      },
    });

    return { formattedChartData, chartOptions };
  },
});
</script>

<style scoped>
.chart-container {
  width: 100%;
  height: 300px;
}
</style>
