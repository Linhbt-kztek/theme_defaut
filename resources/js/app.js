import './bootstrap';
import { createApp } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import MainPage from './components/MainPage.vue';

const app = createApp({});

app.component('main-page', MainPage);
app.mount('#app');

createApp(app).use(ZiggyVue);
