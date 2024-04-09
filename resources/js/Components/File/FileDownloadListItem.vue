<template>
  <a :href="folder ? `${folder}/${file.file}` : `/uploads/${file.file}`" download>
    <div
      class="flex no-wrap items-center q-pa-sm cursor-pointer rounded-borders"
      @mouseenter="focusOn"
      @mouseleave="focusOff"
    >
      <q-icon :name="icon.name" :color="icon.color" size="md" />
      <div class="q-px-sm">{{ file.name }}</div>
    </div>
  </a>
</template>

<script>
import { computed } from 'vue'

export default {
  props: {
    file: Object,
    folder: String
  },
  setup (props) {
    const icon = computed(() => {
      let extension = /(?:\.([^.]+))?$/.exec(props.file.file)[1]
      let name = 'draft'
      let color = 'grey-5'
      switch (extension) {
        case 'doc':
        case 'docx':
        case 'odt':
          name = 'article'
          color = 'indigo-14'
         break
        case 'pdf':
          name = 'picture_as_pdf'
          color = 'red'
          break
        case 'jpg':
        case 'jpeg':
        case 'gif':
        case 'bmp':
        case 'png':
          name = 'image'
          color = 'blue'
          break  
      }
      return { name, color }
    })

    const focusOn = function (event) {
      event.target.classList.add('bg-grey-3')
    }

    const focusOff = function (event) {
      event.target.classList.remove('bg-grey-3')
    }

    return { icon, focusOn, focusOff }
  }
}
</script>