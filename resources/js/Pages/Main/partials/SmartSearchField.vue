<template>
  <div class="highlighted-input-container">
    
    <div class="highlighted-text">
      <span
        v-for="(part, index) in highlightedParts"
        :key="index"
        :class="{ highlight: isHighlighted(part) }"
      >
        {{ part }}
      </span>
    </div>
    
    <!-- Field to enter search request -->
    <textarea
      v-model="text"
      class="input-overlay"
      @input="handleInput"
      ref="textarea"
      placeholder="Введите запрос"
    ></textarea>

   <!-- A list of variants to select -->
    <q-menu
      target=".input-overlay"
      no-parent-event
      no-focus
      v-model="showVariantsPopup"
      transition-show="scale"
      transition-hide="scale"
    >
      <q-list v-if="matchingVariants">
        <q-item v-for="item of matchingVariants" clickable v-ripple @click="addVariantToSelection(item)">
          <q-item-section top avatar>
            <q-avatar :color="item.color" text-color="white" :icon="item.icon" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ item.label }}</q-item-label>
            <q-item-label caption lines="1">{{ item.name }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-menu>

    <!-- Current selections -->
    <!-- City -->
    <template v-if="selectedCity.length">
      <q-chip v-for="city of selectedCity" size="sm" class="q-mt-none cf-chip" outline color="primary" icon="near_me" removable @remove="removeItemFromCitySelection(city)">
        {{ city.label }}
      </q-chip>
    </template>
    <!-- District (of a city or a settlement) -->
    <template v-if="selectedDistrict.length">
      <q-chip v-for="district of selectedDistrict" size="sm" class="q-mt-none cf-chip" outline color="grey-8" icon="home_work" removable @remove="removeItemFromDistrictSelection(district)">
        {{ district.label }}
      </q-chip>
    </template>
    <!-- Developer -->
    <template v-if="selectedDeveloper.length">
      <q-chip v-for="developer of selectedDeveloper" size="sm" class="q-mt-none cf-chip" outline color="orange-9" icon="engineering" removable @remove="removeItemFromDeveloperSelection(developer)">
        {{ developer.label }}
      </q-chip>
    </template>
    <!-- Newbuilding Complex -->
    <template v-if="selectedNewbuildingComplex.length">
      <q-chip v-for="complex of selectedNewbuildingComplex" size="sm" class="q-mt-none cf-chip" outline :color="complex.color" :icon="complex.icon" removable @remove="removeItemFromNewbuildingComplexSelection(complex)">
        {{ complex.label }}
      </q-chip>
    </template>
    <!-- Rooms -->
    <template v-if="selectedRooms.length">
      <q-chip v-for="item of selectedRooms" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromRoomsSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- Floor -->
    <template v-if="selectedFloor.length">
      <q-chip v-for="item of selectedFloor" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromFloorSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- FloorFrom -->
    <template v-if="selectedFloorFrom.length">
      <q-chip v-for="item of selectedFloorFrom" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromFloorFromSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- FloorTo -->
    <template v-if="selectedFloorTo.length">
      <q-chip v-for="item of selectedFloorTo" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromFloorToSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- totalFloorFrom -->
    <template v-if="selectedTotalFloorFrom.length">
      <q-chip v-for="item of selectedTotalFloorFrom" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromTotalFloorFromSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- totalFloorTo -->
    <template v-if="selectedTotalFloorTo.length">
      <q-chip v-for="item of selectedTotalFloorTo" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromTotalFloorToSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- priceFrom -->
    <template v-if="selectedPriceFrom && selectedPriceFrom.length">
      <q-chip v-for="item of selectedPriceFrom" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromPriceFromSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- priceTo -->
    <template v-if="selectedPriceTo && selectedPriceTo.length">
      <q-chip v-for="item of selectedPriceTo" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromPriceToSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- areaFrom -->
    <template v-if="selectedAreaFrom && selectedAreaFrom.length">
      <q-chip v-for="item of selectedAreaFrom" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromAreaFromSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
    <!-- areaTo -->
    <template v-if="selectedAreaTo && selectedAreaTo.length">
      <q-chip v-for="item of selectedAreaTo" size="sm" class="q-mt-none cf-chip" outline :color="item.color" :icon="item.icon" removable @remove="removeItemFromAreaToSelection(item)">
        {{ item.label }}
      </q-chip>
    </template>
  </div>
  <!--<pre>price from vars {{ priceFromVariants }}</pre>-->
  <!--<pre>sel price from {{ selectedPriceFrom }}</pre>-->
  <!--<pre>match vars {{ matchingVariants }}</pre>-->
</template>

<script>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { idNameArrayToOptions, idNameObjToOptions, numberToMillionsPriceOptions, numberToAreaOptions, fetchToVariants } from '@/composables/formatted-and-processed-data'
import { roomAliases, floorAliases } from '@/composables/dictionaries-and-maps'
import useEmitter from '@/composables/use-emitter'
import axios from 'axios'

export default {
  name: 'SmartSearchField',
  props: {
    defaultSetsOfVariantForRegion: Object
  },
  setup(props) {
    const text = ref('') // Full text
    const previousText = ref('') // Previous state of full text
    const currentWord = ref('') // Currently editing word
    const textarea = ref(null)
    const textareaPopup = ref(false)

    const emitter = useEmitter()

    /** Highlighting text */
    // Patterns for highlighting
    const patterns = ['образец1', 'образец2']

    // Check if the word matches any pattern
    /*const isHighlighted = (word) => {
      // return patterns.some((pattern) => word.toLowerCase().includes(pattern))
      return patterns.some((pattern) => {
        const regex = new RegExp(`^${pattern}$`, 'i') // Full match ignoring the register
        return regex.test(word)
      })
    }*/
    const isHighlighted = (word) => {
      return patterns.some((pattern) => {
        const regex = new RegExp(`^${pattern}$`, 'i') // Exact match ignoring case
        return regex.test(word)
      })
    }

    // Separate the text for highlighting
    /*const highlightedParts = computed(() => {
      return text.value.split(/(\s+)/)
    })*/

    // Compute highlighted parts
    const highlightedParts = computed(() => {
      const parts = []
      let remainingText = text.value

      while (remainingText.length > 0) {
        let found = false

        for (const pattern of patterns) {
          const escapedPattern = pattern.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&') // Escape special characters
          const regex = new RegExp(`^(${escapedPattern})(\\s|$)`, 'i') // Match pattern at start of remaining text

          const match = remainingText.match(regex)
          if (match) {
            // Add the matched part
            parts.push(match[1]) // Highlighted part
            remainingText = remainingText.slice(match[1].length) // Remove matched text from remainingText
            found = true
            break
          }
        }

        if (!found) {
          // No pattern matched; add the next character and continue
          parts.push(remainingText[0])
          remainingText = remainingText.slice(1)
        }
      }

      return parts
    })

    // Handle input to the textarea
    const handleInput = () => {
      adjustHeight() // Adjusting height of the text field
      updateCurrentWord() // Finding currently editing word
      previousText.value = text.value // Saving current state of the text
    }

    /** Finding currently editing word */
    const updateCurrentWord = () => {
      const newWords = text.value.split(/\s+/) // Current words
      const oldWords = previousText.value.split(/\s+/) // Previous words

      // Track whether the last character is a space
      const endsWithSpace = text.value.endsWith(' ')

      for (let i = 0; i < newWords.length; i++) {
        if (newWords[i] !== oldWords[i]) {
          // If the word is complete (ends with a space), reset currentWord
          currentWord.value = endsWithSpace ? null : newWords[i] || ''
          return
        }
      }

      // If a new word is added at the end of the string
      if (newWords.length > oldWords.length) {
        currentWord.value = endsWithSpace ? null : newWords[newWords.length - 1]
      }
    }

    /** Tuning the height of textarea */
    const adjustHeight = () => {
      const el = textarea.value
      el.style.height = '40px' // reset height before calculation
      el.style.height = el.scrollHeight + 'px' // Set textarea height according to content height
    }

    /** Filter Logic */
    //const showVariantsPopup = ref(false)

    const regionSelect = ref(null)

    const citiesForRegion = ref(null)
    const citySelect = ref(null)
    const districtsForSelectedCity = ref(null)

    const districtSelect = ref(null)

    const developerSelect = ref(null)

    const developersForSelectedParams = ref(null)

    const newbuildingComplexesForDevelopers = ref(null)

    const newbuildingComplexSelect = ref(null)

    const roomsSelect = ref(null)

    const floorSelect = ref(null)
    const floorRangeSelect = ref({ min: null, max: null })
    const totalFloorRangeSelect = ref({ min: null, max: null })

    const priceRangeSelect = ref({ min: null, max: null })
    
    const areaRangeSelect = ref({ min: null, max: null })

    /** Sets of variants for each category (field) */
    // City
    const cityVariants = computed(() => { 
      if (citiesForRegion.value === null) return []
      return fetchToVariants(idNameArrayToOptions(citiesForRegion.value), 'city') 
    })

    const districtVariants = computed(() => {
      let variants = []

      if (districtsForSelectedCity.value !== null && districtsForSelectedCity.value !== undefined && districtsForSelectedCity.value.length) {
        variants = fetchToVariants(idNameArrayToOptions(districtsForSelectedCity.value), 'district')
      } else {
        variants = fetchToVariants(idNameArrayToOptions(props.defaultSetsOfVariantForRegion.districts), 'district')
      }

      return variants
    })

    const developerVariants = computed(() => {
      let variants = []

      if (developersForSelectedParams.value !== null && developersForSelectedParams.value !== undefined && developersForSelectedParams.value.length) {
        variants = fetchToVariants(idNameObjToOptions(developersForSelectedParams.value), 'developer')
      } else {
        variants = fetchToVariants(idNameObjToOptions(props.defaultSetsOfVariantForRegion.developers), 'developer')
      }

      return variants
    })

    const isLoadingDevelopers = ref(false)
    
    const fetchDevelopers = async (cityId = null, regionId = null, districtId = null) => {
      
      if (Array.isArray(districtId) && districtId.length === 0) {
        districtId = null
      }

      let action, argumentName, locationId;

      if (districtId) {
        action = 'get-developers-for-city-district'
        argumentName = 'district_id'
        locationId = districtId
      } else if (cityId) {
        action = 'get-developers-for-city'
        argumentName = 'city_id'
        locationId = cityId
      } else {
        action = 'get-developers-for-region'
        argumentName = 'region_id'
        locationId = regionId
      }

      isLoadingDevelopers.value = true // set the flag of loading developers

      try {
        const response = await axios.post(`/developer/${action}`, { [argumentName]: locationId })

        developersForSelectedParams.value = response.data
        
        await nextTick()

      } catch (error) {
        console.error("Error loading developers:", error)
      } finally {
        isLoadingDevelopers.value = false // reset the loading flag
      }
    }

    const newbuildingComplexVariants = computed(() => {
      let variants = []

      if (newbuildingComplexesForDevelopers.value !== null && newbuildingComplexesForDevelopers.value !== undefined && newbuildingComplexesForDevelopers.value.length) {
        variants = fetchToVariants(idNameArrayToOptions(newbuildingComplexesForDevelopers.value), 'newbuildingComplex')
      } else {
        variants = fetchToVariants(idNameArrayToOptions(props.defaultSetsOfVariantForRegion.newbuildingComplexes), 'newbuildingComplex')
      }

      return variants
    })

    const roomsVariants = computed(() => {
      let variants = []
      const options = roomAliases()
      variants = fetchToVariants(options, 'rooms')
      return variants
    })

    const floorVariants = computed(() => {
      let variants = []
      const options = floorAliases()
      variants = fetchToVariants(options, 'floor')
      return variants
    })

    const floorFromVariants = computed(() => {
      let variants = []
      const options = floorAliases('floorFrom')
      variants = fetchToVariants(options, 'floorFrom')
      return variants
    })

    const floorToVariants = computed(() => {
      let variants = []
      const options = floorAliases('floorTo')
      variants = fetchToVariants(options, 'floorTo')
      return variants
    })

    const totalFloorFromVariants = computed(() => {
      let variants = []
      const options = floorAliases('totalFloorFrom')
      variants = fetchToVariants(options, 'totalFloorFrom')
      return variants
    })

    const totalFloorToVariants = computed(() => {
      let variants = []
      const options = floorAliases('totalFloorTo')
      variants = fetchToVariants(options, 'totalFloorTo')
      return variants
    })

    const priceFromVariants = ref([])
    watch(currentWord, async (newVal) => {
      // Generate options based on the currentWord
      const options = numberToMillionsPriceOptions(currentWord.value)
      // Fetch new variants based on the options
      const variants = fetchToVariants(options, 'priceFrom')
      if (variants.length) {
        // Use the reusable function to update priceToVariants
        updateReactiveArray(priceFromVariants, variants)
      }
    })

    const priceToVariants = ref([])
    watch(currentWord, async (newVal) => {
      // Generate options based on the currentWord
      const options = numberToMillionsPriceOptions(currentWord.value, 'to')
      // Fetch new variants based on the options
      const variants = fetchToVariants(options, 'priceTo')
      if (variants.length) {
        // Use the reusable function to update priceToVariants
        updateReactiveArray(priceToVariants, variants)
      }
    })

    const areaFromVariants = ref([])
    watch(currentWord, async (newVal) => {
      // Generate options based on the currentWord
      const options = numberToAreaOptions(currentWord.value)
      // Fetch new variants based on the options
      const variants = fetchToVariants(options, 'areaFrom')
      if (variants.length) {
        // Use the reusable function to update priceToVariants
        updateReactiveArray(areaFromVariants, variants)
      }
    })

    const areaToVariants = ref([])
    watch(currentWord, async (newVal) => {
      // Generate options based on the currentWord
      const options = numberToAreaOptions(currentWord.value, 'to')
      // Fetch new variants based on the options
      const variants = fetchToVariants(options, 'areaTo')
      if (variants.length) {
        // Use the reusable function to update priceToVariants
        updateReactiveArray(areaToVariants, variants)
      }
    })

    // Method to update variants for dynamically generated options
    const updateReactiveArray = (reactiveArray, newVariants) => {
      // Update existing objects if value matches
      const updatedArray = reactiveArray.value.map((existing) => {
        const match = newVariants.find((el) => el.value === existing.value)
        return match ? match : existing
      })
      // Add new objects that are not yet in the array
      const newObjects = newVariants.filter((el) => {
        return !reactiveArray.value.some((f) => f.value === el.value)
      })
      // Merge updated and new objects
      reactiveArray.value = [...updatedArray, ...newObjects]
    }

    // Developers on citySelect/regionSelect/districtSelect change
    watch([citySelect, regionSelect, districtSelect], ([newCityId, newRegionId, newDistrictId]) => {

      //developerSelect.value = null
      //newbuildingComplexesSelect.value = null

      fetchDevelopers(newCityId, newRegionId, newDistrictId)
    }, { deep: true })

    // watch filter fields and refresh dependent fields
    watch (citySelect, (newVal) => {
      districtsForSelectedCity.value = null
      axios.post('/city/get-for-city?id=' + newVal)
      .then(function (response) {
        districtsForSelectedCity.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    })

    // Combine all the variants in one set
    const allVariants = computed(() => {
      const variants =  [
        ...cityVariants.value,
        ...districtVariants.value,
        ...developerVariants.value,
        ...newbuildingComplexVariants.value,
        ...roomsVariants.value,
        ...floorVariants.value,
        ...floorFromVariants.value,
        ...floorToVariants.value,
        ...totalFloorFromVariants.value,
        ...totalFloorToVariants.value,
        ...priceFromVariants.value,
        ...priceToVariants.value,
        ...areaFromVariants.value,
        ...areaToVariants.value,
      ]
      return variants
    })

    // All the variant that matches combined field value
    const matchingVariants = computed(() => {
      if (currentWord.value === null || !currentWord.value.length) return 
      const matchVariants = allVariants.value.filter(variant => {
        // if we have several aliases to compare with
        if (variant.hasOwnProperty('aliases')) {
          // if a mode of comparison is set
          if (variant.hasOwnProperty('compare')) {
            switch (variant.compare) {
              case 'fromStart': // strign must start with the pattern
                return variant.aliases.some(alias => alias.toLowerCase().startsWith(currentWord.value.toLowerCase()))
              default: // search any matches
                return variant.aliases.some(alias => alias.toLowerCase().includes(currentWord.value.toLowerCase()))
            }
          // if no comparison mode is set - search any matches
          } else {
            return variant.aliases.some(alias => alias.toLowerCase().includes(currentWord.value.toLowerCase()))
          }
        }  else {
          return variant.label.toLowerCase().includes(currentWord.value.toLowerCase())
        }
      })
      return matchVariants
    })

    /** Show variants on current word change */
    /*watch(currentWord, (newVal) => {
      if (
        matchingVariants.value === undefined
        || matchingVariants.value === null
        || !matchingVariants.value.length
        || newVal === undefined
        || newVal === null
        || !newVal.length
      ) {
        showVariantsPopup.value = false 
        return 
      }

      showVariantsPopup.value = true
    })*/

    const showVariantsPopup = computed(() => {
      if (
        matchingVariants.value === undefined
        || matchingVariants.value === null
        || !matchingVariants.value.length
        || currentWord.value === undefined
        || currentWord.value === null
        || !currentWord.value.length
      ) {
        return false
      } else { 
        return true
      }
    })

    /** Function to replace currently editing word with recognized value */
    const replaceCurrentWord = (newWord) => {
      const cursorPosition = textarea.value.selectionStart // Position of the caret
      const beforeCursor = text.value.slice(0, cursorPosition)
      const afterCursor = text.value.slice(cursorPosition)

      // The beginning of currently editing
      const wordStart = beforeCursor.lastIndexOf(' ') + 1
      const start = wordStart > 0 ? wordStart : 0

      // The ending of currently editing word
      const nextSpaceIndex = afterCursor.indexOf(' ')
      const end =
        nextSpaceIndex !== -1 ? cursorPosition + nextSpaceIndex : text.value.length

      // Replacing the editing word
      const updatedText =
        text.value.slice(0, start) +
        newWord +
        text.value.slice(end)

      // Renew the text
      text.value = updatedText
    }
    
    /** Add a variant to a selection (according to the category) */
    const addVariantToSelection = (item) => {
      switch (item.category) {
        case 'city':
          citySelect.value = item.value
          break
        case 'district':
          if (districtSelect.value === null) {
            districtSelect.value = [item.value]
          } else if (!districtSelect.value.includes(item.value)) {
            districtSelect.value.push(item.value)
          }
          break
        case 'developer':
          if (developerSelect.value === null) {
            developerSelect.value = [item.value]
          } else if (!developerSelect.value.includes(item.value)) {
            developerSelect.value.push(item.value)
          }
          break
        case 'newbuildingComplex':
          if (newbuildingComplexSelect.value === null) {
            newbuildingComplexSelect.value = [item.value]
          } else if (!newbuildingComplexSelect.value.includes(item.value)) {
            newbuildingComplexSelect.value.push(item.value)
          }
          break
        case 'rooms':
          if (roomsSelect.value === null) {
            roomsSelect.value = [item.value]
          } else if (!roomsSelect.value.includes(item.value)) {
            roomsSelect.value.push(item.value)
          }
          break
        case 'floor':
          floorSelect.value = item.value
          break
        case 'floorFrom':
          floorRangeSelect.value.min = item.value
          break
        case 'floorTo':
          floorRangeSelect.value.max = item.value
          break
        case 'totalFloorFrom':
          totalFloorRangeSelect.value.min = item.value
          break
        case 'totalFloorTo':
          totalFloorRangeSelect.value.max = item.value
          break
        case 'priceFrom':
          priceRangeSelect.value.min = item.value
          break
        case 'priceTo':
          priceRangeSelect.value.max = item.value
          break
        case 'areaFrom':
          areaRangeSelect.value.min = item.value
          break
        case 'areaTo':
          areaRangeSelect.value.max = item.value
          break
      }

      replaceCurrentWord(item.label)
      patterns.push(item.label)
      currentWord.value = null
      showVariantsPopup.value = false
    }

    /** Removing items from selections */
    // Function to remove item from query textarea field
    const RemoveItemFromQueryInput = (itemName) => {
      const regex = new RegExp(`(^|\\s)${itemName}(\\s|$)`, 'g') // Match the word with surrounding spaces or string boundaries
      text.value = text.value.replace(regex, ' ').trim() // Replace match with a space and trim
    }

    // Function to remove item from highlight patterns
    const RemoveItemFromHighlightPatterns = (itemName) => {
      // Find the index of the item in the array
      const index = patterns.indexOf(itemName)

      // If the item exists, remove it
      if (index !== -1) {
        patterns.splice(index, 1) // Remove one element at the found index
      }
    }

    // Remove City from selection
    const removeItemFromCitySelection = (item) => {
      citySelect.value = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove District from selection
    const removeItemFromDistrictSelection = (item) => {
      const itemIndex = districtSelect.value.indexOf(item.value)
      if (itemIndex !== -1) {
        districtSelect.value.splice(itemIndex, 1)
      }
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove Developer from selection
    const removeItemFromDeveloperSelection = (item) => {
      const itemIndex = developerSelect.value.indexOf(item.value)
      if (itemIndex !== -1) {
        developerSelect.value.splice(itemIndex, 1)
      }
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove Newbuilding Complex from selection
    const removeItemFromNewbuildingComplexSelection = (item) => {
      const itemIndex = newbuildingComplexSelect.value.indexOf(item.value)
      if (itemIndex !== -1) {
        newbuildingComplexSelect.value.splice(itemIndex, 1)
      }
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove Rooms Amount from selection
    const removeItemFromRoomsSelection = (item) => {
      const itemIndex = roomsSelect.value.indexOf(item.value)
      if (itemIndex !== -1) {
        roomsSelect.value.splice(itemIndex, 1)
      }
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove Floor from selection
    const removeItemFromFloorSelection = (item) => {
      floorSelect.value = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove FlooFrom from selection
    const removeItemFromFloorFromSelection = (item) => {
      floorRangeSelect.value.min = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove FlooTo from selection
    const removeItemFromFloorToSelection = (item) => {
      floorRangeSelect.value.max = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove totalFloorFrom from selection
    const removeItemFromTotalFloorFromSelection = (item) => {
      totalFloorRangeSelect.value.min = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove totalFloorTo from selection
    const removeItemFromTotalFloorToSelection = (item) => {
      totalFloorRangeSelect.value.max = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove priceFrom from selection
    const removeItemFromPriceFromSelection = (item) => {
      priceRangeSelect.value.min = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove priceTo from selection
    const removeItemFromPriceToSelection = (item) => {
      priceRangeSelect.value.max = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove areaFrom from selection
    const removeItemFromAreaFromSelection = (item) => {
      areaRangeSelect.value.min = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    // Remove areaTo from selection
    const removeItemFromAreaToSelection = (item) => {
      areaRangeSelect.value.max = null
      RemoveItemFromQueryInput(item.label)
      RemoveItemFromHighlightPatterns(item.label)
    }

    /** Selected values */
    // City
    const selectedCity = computed(() => {
      return cityVariants.value.filter((option) => {
        return option.value == citySelect.value
      })
    })

    // District(s)
    const selectedDistrict = computed(() => {
      let items = []

      if (districtSelect.value === null || !districtSelect.value.length) return items

      items =  districtVariants.value.filter((option) => {
        return districtSelect.value.includes(option.value)
      })

      return items
    })

    // Developer(s)
    const selectedDeveloper = computed(() => {
      let items = []

      if (developerSelect.value === null || !developerSelect.value.length) return items

      items =  developerVariants.value.filter((option) => {
        return developerSelect.value.includes(option.value)
      })

      return items
    })

    // Newbulding Complex(es)
    const selectedNewbuildingComplex = computed(() => {
      let items = []

      if (newbuildingComplexSelect.value === null || !newbuildingComplexSelect.value.length) return items

      items =  newbuildingComplexVariants.value.filter((option) => {
        return newbuildingComplexSelect.value.includes(option.value)
      })

      return items
    })

    // Rooms
    const selectedRooms = computed(() => {
      let items = []

      if (roomsSelect.value === null || !roomsSelect.value.length) return items

      items =  roomsVariants.value.filter((option) => {
        return roomsSelect.value.includes(option.value)
      })

      return items
    })

    // Floor
    const selectedFloor = computed(() => {
      return floorVariants.value.filter((option) => {
        return option.value == floorSelect.value
      })
    })

    // FloorFrom
    const selectedFloorFrom = computed(() => {
      return floorFromVariants.value.filter((option) => {
        return option.value == floorRangeSelect.value.min
      })
    })

    // FloorTo
    const selectedFloorTo = computed(() => {
      return floorToVariants.value.filter((option) => {
        return option.value == floorRangeSelect.value.max
      })
    })

    // totalFloorFrom
    const selectedTotalFloorFrom = computed(() => {
      return totalFloorFromVariants.value.filter((option) => {
        return option.value == totalFloorRangeSelect.value.min
      })
    })

    // tatalFloorTo
    const selectedTotalFloorTo = computed(() => {
      return totalFloorToVariants.value.filter((option) => {
        return option.value == totalFloorRangeSelect.value.max
      })
    })

    // priceFrom
    const selectedPriceFrom = computed(() => {
      if (priceFromVariants.value === null) return
      return priceFromVariants.value.filter((option) => {
        return option.value == priceRangeSelect.value.min
      })
    })

    // priceTo
    const selectedPriceTo = computed(() => {
      if (priceToVariants.value === null) return
      return priceToVariants.value.filter((option) => {
        return option.value == priceRangeSelect.value.max
      })
    })

    // areaFrom
    const selectedAreaFrom = computed(() => {
      if (areaFromVariants.value === null) return
      return areaFromVariants.value.filter((option) => {
        return option.value == areaRangeSelect.value.min
      })
    })

    // areaTo
    const selectedAreaTo = computed(() => {
      if (areaToVariants.value === null) return
      return areaToVariants.value.filter((option) => {
        return option.value == areaRangeSelect.value.max
      })
    })


    const renewCitiesRorRegion = (regionId) => {
      regionSelect.value = regionId
      axios.post('/city/get-for-region?id=' + regionId)
      .then(function (response) {
        citiesForRegion.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    onMounted(() => {
      adjustHeight() // Setting up textare height on mounting
      renewCitiesRorRegion(props.defaultSetsOfVariantForRegion.regionId)
    })

    emitter.on('region-changed', payload => renewCitiesRorRegion(payload))

    /*emitter.on('region-changed', (payload) => {
      regionSelect.value = payload
      axios.post('/city/get-for-region?id=' + payload)
      .then(function (response) {
        citiesForRegion.value = response.data
      })
      .catch(function (error) {
        console.log(error)
      })
    })*/

    // Collect Selected values of all filters
    const filterState = computed (() => {
      return {
        region_id: regionSelect.value,
        city_id: citySelect.value,
        district: districtSelect.value,
        developer: developerSelect.value,
        newbuilding_complex: newbuildingComplexSelect.value,
        //newbuilding_array: newbuildingSelect.value,
        //material: materialSelect.value,
        //deadlineYear: deadlineYearSelect.value,
        roomsCount: roomsSelect.value ? roomsSelect.value : [],
        //flatType: flatTypeSelect.value ? flatTypeSelect.value : '0',
        priceFrom: priceRangeSelect.value.min ? priceRangeSelect.value.min : "",
        priceTo: priceRangeSelect.value.max ? priceRangeSelect.value.max : "",
        //priceType: priceTypeSelect.value ? priceTypeSelect.value : '0',
        areaFrom: areaRangeSelect.value.min ? areaRangeSelect.value.min : "",
        areaTo: areaRangeSelect.value.max ? areaRangeSelect.value.max : "",
        floorFrom: floorSelect.value ? floorSelect.value : (floorRangeSelect.value.min ? floorRangeSelect.value.min : ""),
        floorTo: floorSelect.value ? floorSelect.value : (floorRangeSelect.value.max ? floorRangeSelect.value.max : ""),
        totalFloorFrom: totalFloorRangeSelect.value.min ? totalFloorRangeSelect.value.min : "",
        totalFloorTo: totalFloorRangeSelect.value.max ? totalFloorRangeSelect.value.max : "",
        //newbuilding_status: newbuildingStatusSelect.value ? 1 : ""
      }
    })

    /** watch filter change and emit an event */
    watch (filterState, (newVal) => {
      emitter.emit('smart-field-change', newVal)
    }, { deep: true })

    return {
      text,
      textarea,
      highlightedParts,
      isHighlighted,
      handleInput,
      //currentWord,
      //regionSelect,
      showVariantsPopup,
      matchingVariants,
      selectedCity,
      selectedDistrict,
      selectedDeveloper,
      selectedNewbuildingComplex,
      selectedRooms,
      selectedFloor,
      selectedFloorFrom,
      selectedFloorTo,
      selectedTotalFloorFrom,
      selectedTotalFloorTo,
      selectedPriceFrom,
      selectedPriceTo,
      selectedAreaFrom,
      selectedAreaTo,
      addVariantToSelection,
      removeItemFromCitySelection,
      removeItemFromDistrictSelection,
      removeItemFromDeveloperSelection,
      removeItemFromNewbuildingComplexSelection,
      removeItemFromRoomsSelection,
      removeItemFromFloorSelection,
      removeItemFromFloorFromSelection,
      removeItemFromFloorToSelection,
      removeItemFromTotalFloorFromSelection,
      removeItemFromTotalFloorToSelection,
      removeItemFromPriceFromSelection,
      removeItemFromPriceToSelection,
      removeItemFromAreaFromSelection,
      removeItemFromAreaToSelection,
    }
  },
}
</script>

<style scoped>
.highlighted-input-container {
  position: relative;
  width: 100%;
}

.highlighted-text {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  min-height: 40px;
  padding: 8px;
  border-radius: 20px;
  border: 1px transparent;
  background: rgba(255, 255, 255, 0.7);
  white-space: pre-wrap;
  word-wrap: break-word;
  overflow-wrap: break-word;
  pointer-events: none;
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
  color: black;
}

.input-overlay {
  position: relative;
  width: 100%;
  height: 40px;
  min-height: 40px;
  padding: 8px;
  color: transparent;
  caret-color: black;
  background: transparent;
  border: 1px solid #ddd;
  border-radius: 20px;
  outline: none;
  resize: none; /* Отключаем ручное изменение размера */
  overflow: hidden; /* Убираем полосы прокрутки */
  white-space: pre-wrap;
  word-wrap: break-word;
  overflow-wrap: break-word;
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
  z-index: 1;
}

.highlight {
  background-color: yellow; /* Цвет подсветки */
  color: black;
}

.cf-chip {
  background-color: rgba(255,255,255,.7) !important;
}
</style>
