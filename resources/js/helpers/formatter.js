function asArea (area) {
  const formattedArea = new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2 }).format(area);
	return area > 0 ? `${formattedArea} м²` : 'площадь не указана'
}

function asCurrency (amount, fractionDigits = false) {
  const fractionOption = fractionDigits ?  { minimumFractionDigits: 2 } : { maximumFractionDigits: 0 }
  const formattedAmount = new Intl.NumberFormat('ru-RU', fractionOption).format(amount);
	return amount > 0 ? `${formattedAmount} ₽` : 'стоимость не указана'
}

function asDateTime (rawDate) {
  const date = new Date(rawDate)
  const day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate()
  const month = (date.getMonth() + 1) < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1
  const hour = date.getHours() < 10 ? '0' + date.getHours() : date.getHours()
  const minute = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()
  return `${day}.${month}.${date.getFullYear()}, ${hour}:${minute}`
}

function asFloor (floor, totalFloors) {
  return totalFloors > 0 ? `${floor}-й этаж (из ${totalFloors})` : `${floor}-й этаж`
}

function asNumberString (rooms) {
  let roomPrefix = ''
  switch (rooms) {
    case 1:
      roomPrefix = 'одно'
      break
    case 2:
      roomPrefix = 'двух'
      break
    case 3:
      roomPrefix = 'трёх'
      break
    case 4:
      roomPrefix = 'четырёх'
      break
    case 5:
      roomPrefix = 'пяти'
      break
    default:
      roomPrefix = 'много'
  }
  return roomPrefix
}

function asQuarterAndYearDate (dateValue, isQuarterString = true) {
  
  const date = new Date(dateValue)
  
  const quarter = {
    1: 'I',
    2: 'II',
    3: 'III',
    4: 'IV',
  }
  const quarterString = isQuarterString ? ' квартал ' : ' '
  return `${quarter[ Math.ceil(date.getMonth() / 3) ]}${quarterString}${date.getFullYear()}`
}

function asPricePerArea (amount) {
  const formattedAmount = new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 0 }).format(amount);
	return amount > 0 ? `${formattedAmount}  ₽/м²` : ''
}

export { asArea, asCurrency, asDateTime, asFloor, asNumberString, asQuarterAndYearDate, asPricePerArea }