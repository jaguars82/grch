<template>
  <div class="row justify-between q-pa-md bg-black text-white footer-offset">
    <img src="/img/icons/logo-footer.svg" alt="logo" />
    <div class="text-right">
      <span>Гильдия риэлторов Черноземья</span>
      <br />
      <span>г.Воронеж</span>
    </div>
  </div>
</template>

<script>
export default ({
  name: 'Footer'
})
</script>

<style scoped>
.row.justify-between::before, .row.justify-between::after {
  display: none;
}
.footer-offset {
  margin-top: -58px;
}
</style>