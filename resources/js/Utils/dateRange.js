import moment from "moment";

/**
 * 把日期值正規化為「當天 00:00」的 moment。
 *
 * ant-design 的 DatePicker 是基於 rc-picker，它傳進 `disabledDate` 的日期
 * **會帶著當下時間**（例如 10:30），而不是 00:00。
 * 因此若直接拿它跟 `moment('YYYY-MM-DD')`（= 當天 00:00）的時間戳比較，
 * 「結束日期當天」會被判定成超出範圍而無法選取 —— 當開始日與結束日同一天時，
 * 整個日曆會沒有任何日期可以選。
 *
 * @param {any} value dayjs 物件、字串、時間戳或 Date
 * @returns {moment.Moment} 當天 00:00
 */
const toStartOfDay = (value) => {
  // 注意：不能用 moment(value)，因為 moment 無法正確解析 dayjs 物件（會得到錯誤日期）
  const normalized = value && typeof value.valueOf === "function" ? value.valueOf() : value;
  return moment(normalized).startOf("day");
};

/**
 * 判斷日期是否落在 [start, end] 之外（含頭含尾，以「日」為單位）。
 * 供 DatePicker 的 `disabledDate` 使用。
 *
 * @param {any} current rc-picker 傳入的日期
 * @param {string|undefined} start 允許範圍的開始日（YYYY-MM-DD）
 * @param {string|undefined} end 允許範圍的結束日（YYYY-MM-DD）
 * @returns {boolean} true = 停用
 */
export const isDateOutsideRange = (current, start, end) => {
  const day = toStartOfDay(current);
  if (start && day.isBefore(toStartOfDay(start))) {
    return true;
  }
  if (end && day.isAfter(toStartOfDay(end))) {
    return true;
  }
  return false;
};

/**
 * 判斷日期是否早於 start（以「日」為單位）。
 * 供「結束日期」DatePicker 的 `disabledDate` 使用。
 *
 * @param {any} current rc-picker 傳入的日期
 * @param {string|undefined} start 允許範圍的開始日（YYYY-MM-DD）
 * @returns {boolean} true = 停用
 */
export const isDateBefore = (current, start) => {
  if (!start) {
    return false;
  }
  return toStartOfDay(current).isBefore(toStartOfDay(start));
};
