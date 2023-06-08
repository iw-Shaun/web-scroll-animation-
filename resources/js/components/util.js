import moment from 'moment';

const fieldsIsEmpty = {
  selectDay: '請選擇預約日期',
  activity_id: '請選擇預約時間',
  headcount: '請選擇參加人數',
  name: '請填寫姓名',
  email: '請填寫信箱',
  mobile: '請填寫手機',
  id_number: '請填寫身分證字號',
  inv_type: '請選擇發票選項',
  inv_taxId: '請填寫統編號碼',
  inv_title: '請填寫發票抬頭',
  inv_address: '請填寫收件人地址',
  inv_mobile: '請填寫收件人電話',
}

const Util = {
  getServerTime: async () => {
    const apiUrl = "https://worldtimeapi.org/api/timezone/Asia/Taipei";
    const response = await fetch(apiUrl);
    const data = await response.json();
    const time = new Date(data.utc_datetime).getTime();
    return time;
  },
  isActivitiesFull: (activities) => {
    const is_full = !activities.some(activity => !activity.is_full);
    const is_end = !activities.some(activity => !activity.is_end);
    return {is_full: is_full, is_end: is_end}
  },
  getDateObject: (activity) => {
    moment.updateLocale('zh-cn', {weekdays: ['日', '一', '二', '三', '四', '五', '六']});
    const start = moment(activity.start_at);
    const end = moment(activity.end_at);
    return {
      date: moment(start).format('MM/DD (dddd)'),
      time: start.format('HH:mm') + '~' + end.format('HH:mm')
    }
  },
  setActivitiesFormat: async (activities, type) => {
    const serverTime = await Util.getServerTime();
    const filteredActivities = activities.filter(activity => activity.type === type);
    const groupedActivities = filteredActivities.reduce((groups, activity) => {
      const { start_at, id, general_curr_people, general_max_people } = activity;
      const startDate = start_at.split(' ')[0];
      const { date, time } = Util.getDateObject(activity);
      const targetTime = new Date(start_at).getTime();
      (groups[startDate] = groups[startDate] || []).push({
        id,
        is_full: general_curr_people >= general_max_people,
        is_end: serverTime > targetTime,
        date,
        time
      });
      return groups;
    }, {});
  
    return groupedActivities;
  },
  validateFields: (formData, fieldsToValidate) => {
    const isEmpty = (str) => str === undefined || str === null || /^\s*$/.test(str);
    const validators = {
      email: { validate: (email) => /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email), errorMessage: '信箱格式錯誤' },
      mobile: { validate: (mobile) => /^09\d{8}$/.test(mobile), errorMessage: '手機格式錯誤' },
      inv_mobile: { validate: (mobile) => /^09\d{8}$/.test(mobile), errorMessage: '發票手機格式錯誤' },
      id_number: { validate: (id_number) => /^[A-Z][1-2]\d{8}$/.test(id_number), errorMessage: '身分證字號格式錯誤' },
    };
  
    const errors = {};
  
    fieldsToValidate.forEach((field) => {
      if (!isEmpty(formData[field])) {
        const validator = validators[field];
        if (validator && !validator.validate(formData[field])) {
          errors[field] = validator.errorMessage;
        }
      } else {
        errors[field] = fieldsIsEmpty[field]
      }
    });
  
    return errors;
  }
}

export default Util;
