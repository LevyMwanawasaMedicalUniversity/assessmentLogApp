import axios from 'axios';
window.axios = axios;

axios.defaults.baseURL = process.env.MIX_APP_URL;


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';