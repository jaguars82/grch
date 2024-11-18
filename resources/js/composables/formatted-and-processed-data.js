/** LISTS OF OPTIONS (in format of array of {label, value} objects) */

/** convert oject of { id: name, id2: name2, etc.. } to option list { label, value } */
const idNameObjToOptions = (idNameObject) => {
  const options = []
  const idies = Object.keys(idNameObject)
  idies.forEach(id => {
    options.push({ label: idNameObject[id], value: id })
  })
  return options
}

/** convert array of objects [ { id1, name1 }, { id2, name2 } ] to option list { label, value } */
const idNameArrayToOptions = (idNameArray) => {
  const options = []
  idNameArray.forEach(item => {
    options.push({ label: item.name, value: item.id })
  })
  return options
}

/** secondary categories */
const secondaryCategoryOptionList = (categoriges) => {
  const options = []
  const categoriesArray = []
  
  for (const id in categoriges) {
    categoriesArray.push(categoriges[id])
  }

  categoriesArray.forEach(category => {
    options.push({ label: category.name, disable: true })
    category.subcats.forEach(subcat => {
      options.push({ label: subcat.name, value: subcat.id })
    })
  })

  return options
}

/** list of users */
const userOptionList = (users) => {
  const options = []
  users.forEach(user => {
    options.push({ label: `${user.last_name} ${user.first_name} ${user.middle_name ? user.middle_name : ''}`.trimEnd(), value: user.id })
  })
  return options
}

/** SELECT ITEMS FROM LISTS OF OPTIONS */

/** select one item */
const selectOneFromOptionsList = (optionsList, targetValue) => {
  return typeof targetValue !== 'undefined' ? optionsList.find(option => { return option.value == targetValue }) : null
}

/** select multiple items */
const selectMultipleFromOptionsList = (optionsList, targetValues) => {
  return typeof targetValues !== 'undefined' ? optionsList.filter(option => { return targetValues.includes(option.value) }) : null
}

/** GET VALUE OF AN OPTION 
 * if the option may be represented
 * - as an object kind of { label, value } or as a value itself
 * - as an array ob objects kind of { label, value } or as an array of values
 */
const getValueOfAnOption = (option) => {
  if (option == null) return ""
  let value = ""
  if (option.constructor === Array) {
    value = []
    option.forEach(optionItem => {
      let itemValue = typeof optionItem === 'object' && optionItem != null ? optionItem.value : optionItem
      value.push(itemValue)
    })
    if (value.length < 1) value = ""
  } else {
    value = typeof option === 'object' ? option.value : option
  }
  return value
}


/** OPTIONS FOR SMART SEARCH-FIELD AND COMBINED SEARCH FIELD */
// method to fetch options to the set of variants for a given category
const fetchToVariants = (options, category) => {
  const categoryParams = {
    city: {
      category: 'city',
      name: 'Населённый пункт',
      icon: 'near_me',
      color: 'primary'
    },
    district: {
      category: 'district',
      name: 'Район города',
      icon: 'home_work',
      color: 'grey-8'
    },
    developer: {
      category: 'developer',
      name: 'Застройщик',
      icon: 'engineering',
      color: 'orange-9'
    },
    newbuildingComplex: {
      category: 'newbuildingComplex',
      name: 'ЖК',
      icon: 'location_city',
      color: 'positive'
    },
    rooms: {
      category: 'rooms',
      name: 'Количество комнат',
      icon: 'dashboard',
      color: 'accent'
    },
    floor: {
      category: 'floor',
      name: 'Этаж',
      //icon: 'horizontal_split',
      icon: 'table_rows',
      color: 'light-blue-9'
    },
    floorFrom: {
      category: 'floorFrom',
      name: 'Минимальный этаж',
      icon: 'table_rows',
      color: 'light-blue-9'
    },
    floorTo: {
      category: 'floorTo',
      name: 'Максимальный этаж',
      icon: 'table_rows',
      color: 'light-blue-9'
    },
  }

  const variants = []

  options.forEach(item => {
    const itemObject = {
      label: item.label,
      value: item.value,
      category: categoryParams[category].category, 
      name: categoryParams[category].name, 
      icon: categoryParams[category].icon, 
      color: categoryParams[category].color,
    }
    
    if (item.hasOwnProperty('aliases')) {
      itemObject.aliases = item.aliases
    }
    
    if (item.hasOwnProperty('compare')) {
      itemObject.compare = item.compare
    }

    variants.push(itemObject)
  })

  return variants
}


export {
  idNameObjToOptions,
  idNameArrayToOptions,
  secondaryCategoryOptionList,
  userOptionList,
  selectOneFromOptionsList,
  selectMultipleFromOptionsList,
  getValueOfAnOption,
  fetchToVariants
}