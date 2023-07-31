/** Lists of options (in format of array of {label, value} objects) */

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

/** list of agencies */
const agencyOptionList = (agencies) => {
  const options = []

  const agencyIdies = Object.keys(agencies)
  agencyIdies.forEach(agencyId => {
    options.push({ label: agencies[agencyId], value: agencyId })
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

export {
  secondaryCategoryOptionList,
  agencyOptionList,
  userOptionList
}