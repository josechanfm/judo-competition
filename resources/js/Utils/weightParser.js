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


export function convertGender(weightCode) {
  if (!weightCode) return '未知';
  
  const firstChar = weightCode.charAt(0).toUpperCase();
  
  switch(firstChar) {
    case 'M': return '男子';
    case 'F': return '女子';
    default: return '未知';
  }
}

/**
 * 公斤級 (weight group) 代碼 → 目前語系的顯示名稱。
 *
 *   MW60-  → 男子60公斤以下      (en: Men -60kg)
 *   FW42+  → 女子42公斤以上      (en: Women +42kg)
 *   MW60   → 男子60公斤          (en: Men 60kg)
 *   MWULW  → 男子無限量級        (en: Men Unlimited)
 *
 * 解析不出來（或查不到翻譯）時，直接回傳原始代碼，不會顯示出 key 名稱。
 *
 * @param {string} weightCode 例如 MW60- / FW42+ / MWULW
 * @param {(key: string, replacements?: object) => string} t i18n 的 $t（由元件傳入）
 */
export function weightGroupLabel(weightCode, t) {
  if (!weightCode) {
    return '';
  }

  if (typeof t !== 'function') {
    return weightCode;
  }

  const tokens = /^(?:(M|F|MF)W)?(ULW|\d+)([+-])?$/i.exec(String(weightCode).trim());

  if (!tokens) {
    return weightCode;
  }

  const genderKey = `weight.gender.${(tokens[1] ?? '').toUpperCase()}`;
  const genderLabel = tokens[1] ? t(genderKey) : '';
  const gender = genderLabel === genderKey ? '' : genderLabel;
  const weight = tokens[2];
  const sign = tokens[3];

  const labelKey = weight.toUpperCase() === 'ULW'
    ? 'weight.label.unlimited'
    : sign === '-'
      ? 'weight.label.under'
      : sign === '+'
        ? 'weight.label.over'
        : 'weight.label.exact';

  const label = t(labelKey, { gender, weight });

  // 查不到翻譯時，laravel-vue-i18n 會回傳 key 本身
  if (!label || label === labelKey || label.includes(':')) {
    return weightCode;
  }

  // 沒有性別前綴時（例如 ULW）英文樣板會多出空白
  return label.trim().replace(/\s+/g, ' ');
}

export function convertWeight(weightCode) {
  if (!weightCode) return '';
  
  const weight = weightCode.toString();
  
  // 如果是 MWULW 或 FWULW，轉換為 無限量級
  if (['MWULW', 'FWULW', 'ULW'].includes(weight.toUpperCase())) {
    return '無限量級';
  }
  
  // 去除 MW 或 FW 前綴
  let cleanedWeight = weight.replace(/^(MW|FW)/i, '');
  
  // 處理帶有 +/- 符號的體重級別
  const plusMinusMatch = cleanedWeight.match(/^(\d+)([+-])$/);
  if (plusMinusMatch) {
    const sign = plusMinusMatch[2];
    const value = plusMinusMatch[1];
    
    if (sign === '-') {
      return `-${value}kg`;
    } else if (sign === '+') {
      return `+${value}kg`;
    }
  }
  
  // 如果是純數字，加上 kg
  if (/^\d+$/.test(cleanedWeight)) {
    return `${cleanedWeight}kg`;
  }
  
  // 其他情況返回原始值（去除前綴後的）
  return cleanedWeight;
}