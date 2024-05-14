const preg = /(M|F|MF)W((\d+)(\+|\-)|ULW|0)/i;

const genderMapZh = {
    M: '男子',
    F: '女子',
    MF: '混合'
}

const genderMapEn = {
    M: 'Men',
    F: 'Female',
    MF: 'Mixed'
}

const genderMapPt = {
    M: 'Masculino',
    F: 'Feminino',
    MF: 'Misto'
}

const colorMapper = {
    M: 'blue',
    F: 'red',
    MF: 'green'
}

const weightMap = {
    '+': '以上',
    '-': '以下',
    'ULW': '無限量級',
    'OPEN': '開放級',
    '0': '新人組'
}

export const weightParser = (weight) => {
    const tokens = preg.exec(weight).filter((token) => token !== undefined);

    if (tokens.length < 3) {
        throw new Error('Invalid weight format')
    }

    // console.debug(tokens)

    const nameZh =  () => {
            if (tokens.length === 5) {
                return genderMapZh[tokens[1]] + tokens[3] + '公斤' + weightMap[tokens[4]]
            } else {
                return genderMapZh[tokens[1]] + weightMap[tokens[2]]
            }
        }

    return {
        nameZh: nameZh,
        gender: tokens[1],
        symbol: tokens[4] ?? null,
        weight: tokens[3] ?? tokens[2],
        color: colorMapper[tokens[1]],
        code: weight,
        toObject: () => {
            return {
                'code': weight,
                'color': colorMapper[tokens[1]],
                'gender' : tokens[1],
                'weight' : tokens[2],
                'nameZh' : nameZh(),
            }
        }
    }
}
