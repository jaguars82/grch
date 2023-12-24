/** LISTS OF OPTIONS (in format of array of {label, value} objects) */

/** convert oject of { id: name } to option list */
const idNameObjToOptions = (idNameObject) => {
  const options = []
  const idies = Object.keys(idNameObject)
  idies.forEach(id => {
    options.push({ label: idNameObject[id], value: id })
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

export {
  idNameObjToOptions,
  secondaryCategoryOptionList,
  userOptionList,
  selectOneFromOptionsList,
  selectMultipleFromOptionsList,
  getValueOfAnOption
}