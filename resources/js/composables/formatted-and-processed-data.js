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
    totalFloorFrom: {
      category: 'totalFloorFrom',
      name: 'Минимальная этажность здания',
      icon: 'table_rows',
      color: 'light-blue-9'
    },
    totalFloorTo: {
      category: 'totalFloorTo',
      name: 'Максимальная этажность здания',
      icon: 'table_rows',
      color: 'light-blue-9'
    },
    priceFrom: {
      category: 'priceFrom',
      name: 'Минимальная стоимость',
      icon: 'currency_ruble',
      color: 'teal-8'
    },
    priceTo: {
      category: 'priceTo',
      name: 'Максимальная стоимость',
      icon: 'currency_ruble',
      color: 'teal-8'
    },
    areaFrom: {
      category: 'areaFrom',
      name: 'Минимальная площадь',
      icon: 'crop_square',
      color: 'brown-8'
    },
    areaTo: {
      category: 'areaTo',
      name: 'Максимальная площадь',
      icon: 'crop_square',
      color: 'brown-8'
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

// Convert number to price options (in millions)
const numberToMillionsPriceOptions = (inputNumber, labelPrefix='from') => {
  
  const prefix = labelPrefix === 'from' ? 'от' : 'до'
  
  const results = []
  
  if (inputNumber >= 1 && inputNumber <= 9) {
    // For Single digit Numbers
    results.push({ 
      value: inputNumber * 1_000_000,
      label: `${prefix} ${inputNumber} млн. ₽`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
  } else if (inputNumber >= 10 && inputNumber <= 99) {
    // For Double digit Numbers
    results.push({ 
      value: inputNumber * 100_000,
      label: `${prefix} ${(inputNumber * 0.1).toFixed(1)} млн. ₽`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
    results.push({ 
      value: inputNumber * 1_000_000,
      label: `${prefix} ${inputNumber} млн. ₽`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
  } else if (inputNumber > 99) {
    // For Numbers greater then 99
    const firstThreeDigits = Math.floor(inputNumber / Math.pow(10, Math.floor(Math.log10(inputNumber)) - 2))
    results.push({ 
      value: firstThreeDigits * 10_000,
      label: `${prefix} ${(firstThreeDigits * 0.01).toFixed(2)} млн. ₽`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
    results.push({ 
      value: firstThreeDigits * 100_000,
      label: `${prefix} ${(firstThreeDigits * 0.1).toFixed(1)} млн. ₽`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
  }

  return results
}

// Convert number to area options (in square meters)
const numberToAreaOptions = (inputNumber, labelPrefix = 'from') => {

  const prefix = labelPrefix === 'from' ? 'от' : 'до'
  
  const results = []

  if (inputNumber >= 1 && inputNumber <= 9) {
    // For single-digit numbers
    results.push({
      value: inputNumber,
      label: `${prefix} ${inputNumber} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
    results.push({
      value: inputNumber * 10,
      label: `${prefix} ${inputNumber * 10} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
  } else if (inputNumber >= 10 && inputNumber <= 99) {
    // For double-digit numbers
    results.push({
      value: inputNumber,
      label: `${prefix} ${inputNumber} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
    results.push({
      value: inputNumber * 10,
      label: `${prefix} ${inputNumber * 10} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
  } else if (inputNumber > 99) {
    // For triple-digit and larger numbers (only first three digits are considered)
    const firstThreeDigits = Math.floor(inputNumber / Math.pow(10, Math.floor(Math.log10(inputNumber)) - 2))

    results.push({
      value: firstThreeDigits * 0.1,
      label: `${prefix} ${(firstThreeDigits * 0.1).toFixed(1)} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
    results.push({
      value: firstThreeDigits,
      label: `${prefix} ${firstThreeDigits} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
    results.push({
      value: firstThreeDigits * 10,
      label: `${prefix} ${firstThreeDigits * 10} м²`,
      aliases: [inputNumber],
      compare: 'fromStart',
    })
  }

  return results
}



export {
  idNameObjToOptions,
  idNameArrayToOptions,
  secondaryCategoryOptionList,
  userOptionList,
  selectOneFromOptionsList,
  selectMultipleFromOptionsList,
  getValueOfAnOption,
  fetchToVariants,
  numberToMillionsPriceOptions,
  numberToAreaOptions
}