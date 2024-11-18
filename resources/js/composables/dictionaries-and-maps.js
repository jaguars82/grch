/** Return array of aliases for one of the object's carachteristics */
// Rooms (return array of objects with aliases for room amount)
const roomAliases = () => {
  const commonAlliases = ['комнат']

  const postfixesVar1 = ['-но', 'но', '-на', 'на', '-нушка', 'нушка',  '-шка', 'шка', '-ка', 'ка']
  const postfixesVar2 = ['-х', 'х', '-ух', 'ух', '-ушка', 'ушка', '-шка', 'шка', '-ка', 'ка']
  const postfixesVar3_4 = ['-х', 'х', '-ех', 'ех', '-ёх', 'ёх', '-шка', 'шка', '-ка', 'ка']
  const postfixesVar5Plus = ['-ти', 'ти', '-и', 'и', '-ка', 'ка']
  
  const roomAmountParts = [
    {
      roomAmount: 1,
      postfixes: postfixesVar1,
      wordAliases: ['однушка', 'однокомнатная', 'одна', 'малосемейка'],
      label: '1 комната'
    },
    {
      roomAmount: 2,
      postfixes: postfixesVar2,
      wordAliases: ['двушка', 'двухкомнатная', 'два'],
      label: '2 комнаты'
    },
    {
      roomAmount: 3,
      postfixes: postfixesVar3_4,
      wordAliases: ['трешка', 'трёшка', 'трехкомнатная', 'трёхкомнатная', 'три'],
      label: '3 комнаты'
    },
    {
      roomAmount: 4,
      postfixes: postfixesVar3_4,
      wordAliases: ['четверка', 'четвёрка', 'четырехкомнатная', 'четырёхкомнатная', 'четыре'],
      label: '4 комнаты'
    },
    {
      roomAmount: 5,
      postfixes: postfixesVar5Plus,
      wordAliases: ['пятерка', 'пятёрка', 'пятикомнатная', 'пятикомнатная', 'пять'],
      label: '5 комнат'
    },
  ]

  const aliases = []

  roomAmountParts.forEach(item => {
    const aliasesByAmount = [`${item.roomAmount}`, `${item.roomAmount}к`, `${item.roomAmount}-к`, `${item.roomAmount}ком`, `${item.roomAmount}-ком`, `${item.roomAmount}комнатная`, `${item.roomAmount}-комнатная`, `${item.roomAmount} комнатная`, `${item.roomAmount} комнат`, `${item.roomAmount} комната`]
    const aliasesByPostfix = []
    item.postfixes.forEach(postfix => {
      aliasesByPostfix.push(`${item.roomAmount}${postfix}`)
    })

    aliases.push({
      value: item.roomAmount,
      aliases: [...aliasesByAmount, ...aliasesByPostfix, ...commonAlliases, ...item.wordAliases],
      label: item.label
    })
  })

  return aliases
}

// Floors (return array of objects with aliases for floor
const floorAliases = (type = 'floor') => {
  // Numerals in different forms
  const textNumerals = [
    { nominative: 'первый', genitive: 'первого', prepositional: 'первом' },
    { nominative: 'второй', genitive: 'второго', prepositional: 'втором' },
    { nominative: 'третий', genitive: 'третьего', prepositional: 'третьем' },
    { nominative: 'четвертый', genitive: 'четвертого', prepositional: 'четвертом' },
    { nominative: 'пятый', genitive: 'пятого', prepositional: 'пятом' },
    { nominative: 'шестой', genitive: 'шестого', prepositional: 'шестом' },
    { nominative: 'седьмой', genitive: 'седьмого', prepositional: 'седьмом' },
    { nominative: 'восьмой', genitive: 'восьмого', prepositional: 'восьмом' },
    { nominative: 'девятый', genitive: 'девятого', prepositional: 'девятом' },
    { nominative: 'десятый', genitive: 'десятого', prepositional: 'десятом' },
    { nominative: 'одиннадцатый', genitive: 'одиннадцатого', prepositional: 'одиннадцатом' },
    { nominative: 'двенадцатый', genitive: 'двенадцатого', prepositional: 'двенадцатом' },
    { nominative: 'тринадцатый', genitive: 'тринадцатого', prepositional: 'тринадцатом' },
    { nominative: 'четырнадцатый', genitive: 'четырнадцатого', prepositional: 'четырнадцатом' },
    { nominative: 'пятнадцатый', genitive: 'пятнадцатого', prepositional: 'пятнадцатом' },
    { nominative: 'шестнадцатый', genitive: 'шестнадцатого', prepositional: 'шестнадцатом' },
    { nominative: 'семнадцатый', genitive: 'семнадцатого', prepositional: 'семнадцатом' },
    { nominative: 'восемнадцатый', genitive: 'восемнадцатого', prepositional: 'восемнадцатом' },
    { nominative: 'девятнадцатый', genitive: 'девятнадцатого', prepositional: 'девятнадцатом' },
    { nominative: 'двадцатый', genitive: 'двадцатого', prepositional: 'двадцатом' },
    { nominative: 'двадцать первый', genitive: 'двадцать первого', prepositional: 'двадцать первом' },
    { nominative: 'двадцать второй', genitive: 'двадцать второго', prepositional: 'двадцать втором' },
    { nominative: 'двадцать третий', genitive: 'двадцать третьего', prepositional: 'двадцать третьем' },
    { nominative: 'двадцать четвертый', genitive: 'двадцать четвертого', prepositional: 'двадцать четвертом' },
    { nominative: 'двадцать пятый', genitive: 'двадцать пятого', prepositional: 'двадцать пятом' },
    { nominative: 'двадцать шестой', genitive: 'двадцать шестого', prepositional: 'двадцать шестом' },
    { nominative: 'двадцать седьмой', genitive: 'двадцать седьмого', prepositional: 'двадцать седьмом' },
    { nominative: 'двадцать восьмой', genitive: 'двадцать восьмого', prepositional: 'двадцать восьмом' },
    { nominative: 'двадцать девятый', genitive: 'двадцать девятого', prepositional: 'двадцать девятом' },
    { nominative: 'тридцатый', genitive: 'тридцатого', prepositional: 'тридцатом' },
    { nominative: 'тридцать первый', genitive: 'тридцать первого', prepositional: 'тридцать первом' },
    { nominative: 'тридцать второй', genitive: 'тридцать второго', prepositional: 'тридцать втором' },
    { nominative: 'тридцать третий', genitive: 'тридцать третьего', prepositional: 'тридцать третьем' },
    { nominative: 'тридцать четвертый', genitive: 'тридцать четвертого', prepositional: 'тридцать четвертом' },
    { nominative: 'тридцать пятый', genitive: 'тридцать пятого', prepositional: 'тридцать пятом' },
    { nominative: 'тридцать шестой', genitive: 'тридцать шестого', prepositional: 'тридцать шестом' },
    { nominative: 'тридцать седьмой', genitive: 'тридцать седьмого', prepositional: 'тридцать седьмом' },
    { nominative: 'тридцать восьмой', genitive: 'тридцать восьмого', prepositional: 'тридцать восьмом' },
    { nominative: 'тридцать девятый', genitive: 'тридцать девятого', prepositional: 'тридцать девятом' },
    { nominative: 'сороковой', genitive: 'сорокового', prepositional: 'сороковом' },
    { nominative: 'сорок первый', genitive: 'сорок первого', prepositional: 'сорок первом' },
    { nominative: 'сорок второй', genitive: 'сорок второго', prepositional: 'сорок втором' },
    { nominative: 'сорок третий', genitive: 'сорок третьего', prepositional: 'сорок третьем' },
    { nominative: 'сорок четвертый', genitive: 'сорок четвертого', prepositional: 'сорок четвертом' },
    { nominative: 'сорок пятый', genitive: 'сорок пятого', prepositional: 'сорок пятом' },
    { nominative: 'сорок шестой', genitive: 'сорок шестого', prepositional: 'сорок шестом' },
    { nominative: 'сорок седьмой', genitive: 'сорок седьмого', prepositional: 'сорок седьмом' },
    { nominative: 'сорок восьмой', genitive: 'сорок восьмого', prepositional: 'сорок восьмом' },
    { nominative: 'сорок девятый', genitive: 'сорок девятого', prepositional: 'сорок девятом' },
    { nominative: 'пятидесятый', genitive: 'пятидесятого', prepositional: 'пятидесятом' }
  ]
  

  const results = [];
  const generateAliases = (floor, textNumeral) => {
    const { nominative, genitive, prepositional } = textNumeral
    const commonAliases = [
      `${floor}`,
      `${floor}этаж`,
      `этаж${floor}`,
      `${prepositional}`,
      `${genitive}`,
      `${nominative}`,
      `${floor}-й`,
      `${floor}-м`,
      `${floor}-го`,
      `${floor}й`,
      `${floor}м`,
      `${floor}го`,
    ]

    switch (type) {
      case 'floor': {
        return [
          ...commonAliases
        ]
      }
      case 'floorFrom': {
        return [
          ...commonAliases,
          `нениже${floor}`,
          `выше${floor}`,
          `от${floor}`,
          `>${floor}`,
          `нениже${genitive}`,
          `выше${genitive}`,
          `от${genitive}`,
          `>${genitive}`,
        ]
      }
      case 'floorTo': {
        return [
          ...commonAliases,
          `невыше${floor}`,
          `ниже${floor}`,
          `до${floor}`,
          `<${floor}`,
          `невыше${genitive}`,
          `ниже${genitive}`,
          `до${genitive}`,
          `<${genitive}`,
        ]
      }
      case 'totalFloorsFrom': {
        return [
          ...commonAliases,
          `этажность${floor}`,
          `неменее${floor}`,
          `этажей${floor}`,
          `от${floor}`,
          `>${floor}`,
          `этажность${genitive}`,
          `неменее${genitive}`,
          `этажей${genitive}`,
          `от${genitive}`,
          `>${genitive}`,
        ]
      }
      case 'totalFloorsTo': {
        return [
          ...commonAliases,
          `этажность${floor}`,
          `неболее${floor}`,
          `до${floor}`,
          `этажей${floor}`,
          `<${floor}`,
          `этажность${genitive}`,
          `неболее${genitive}`,
          `до${genitive}`,
          `этажей${genitive}`,
          `<${genitive}`,
        ]
      }
      default:
        return []
    }
  }

  const generateLabel = (floor, type) => {
    switch (type) {
      case 'floor':
        return `${floor} этаж`
      case 'floorFrom':
        return `не ниже ${floor} этажа`
      case 'floorTo':
        return `не выше ${floor} этажа`
      case 'totalFloorsFrom':
        return `не менее ${floor} этажей`
      case 'totalFloorsTo':
        return `не более ${floor} этажей`
      default:
        return ''
    }
  }

  for (let floor = 1; floor <= 50; floor++) {
    const textNumeral = textNumerals[floor - 1]
    if (!textNumeral) continue

    results.push({
      value: floor,
      label: generateLabel(floor, type),
      aliases: generateAliases(floor, textNumeral),
      compare: 'fromStart'
    })
  }

  return results
}


export {
  roomAliases, floorAliases
}