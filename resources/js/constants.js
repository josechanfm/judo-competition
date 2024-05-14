export const GENDER_COLOR_MAP = {
    M: 'blue',
    F: 'red',
    U: 'purple',
};

export const COMPETITION_STATUS = {
    created: 0,
    athletes_confirmed: 1,
    program_arranged: 2,

    // 抽籤完成
    seat_locked: 3,

    // 過磅完成
    program_started: 4,
    finished: 5,
};

export const PROGRAM_STATUS = {
    created: 0,
    drew: 1,
    drew_confirmed: 2,
    started: 3,
    finished: 4,
};

export const BOUT_RESULT = {
    cancelled: -1,
    white_win: 10,
    blue_win: 11,

    white_abstain: 20,
    blue_abstain: 21,

    white_medical: 30,
    blue_medical: 31,

    white_hansokumake: 40,
    blue_hansokumake: 41,
};
